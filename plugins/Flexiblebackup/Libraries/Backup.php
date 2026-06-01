<?php

namespace Flexiblebackup\Libraries;

use App\Libraries\Google;
use CodeIgniter\Files\File;
use CodeIgniter\I18n\Time;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * Class Backup.
 *
 * Description: This class provides functionality related to flexible backups.
 */
class Backup
{
    /**
     * @var \Flexiblebackup\Models\FlexiblebackupModel model for flexible backups
     */
    protected $Flexiblebackup_model;

    /**
     * @var \App\Models\Settings_model model for application settings
     */
    protected $settings;

    /**
     * @var \CodeIgniter\Database\ConnectionInterface database connection
     */
    protected $db;

    /**
     * @var string path to the backup folder
     */
    private $backupFolder = '';

    /**
     * @var string name of the backup folder
     */
    private $backupFolderName = '';

    private $zipFile;


    /**
     * Flexiblebackup constructor.
     *
     * Description: Initializes class properties and sets up necessary models and database connection.
     */
    public function __construct()
    {
        // Initialize the FlexiblebackupModel.
        $this->Flexiblebackup_model = model('Flexiblebackup\Models\FlexiblebackupModel');

        // Initialize the Settings_model.
        $this->settings = new \App\Models\Settings_model();

        // Initialize the database connection.
        $this->db = db_connect('default');

        $this->zipFile = new \PhpZip\ZipFile();
    }

    /**
     * Calculates the timestamp for the next scheduled backup based on the backup type.
     *
     * @param string $backupType the type of backup ('file' or 'database')
     *
     * @return int|false the timestamp for the next scheduled backup or false if no backup is scheduled
     */
    public function calculateNextScheduledBackupTime($backupType)
    {
        // Get the timestamp of the last backup for the specified type
        $lastBackupTimestamp = get_setting('recent_backup_'.$backupType);

        // Get the backup interval based on the backup type
        $backupInterval = get_setting('database_backup_schedule');

        if ('file' == $backupType) {
            $backupInterval = get_setting('files_backup_schedule');
        }

        if (empty($backupInterval) || $backupInterval <= 1) {
            return false;
        }

        // Get the configured auto-backup time or use the default (22:00)
        $backupTimeConfig            = get_setting('auto_backup_time') ?: '22:00';
        [$backupHour, $backupMinute] = explode(':', $backupTimeConfig);

        // Get the current timestamp
        $currentTime = $this->getCurrentTimestamp();

        // If no last backup exists, schedule the backup for the next available time
        if (!$lastBackupTimestamp) {
            $today               = Time::today();
            $scheduledBackupTime = $today->setTime($backupHour, (int) $backupMinute, 0);

            // If the current time is later than the scheduled time for today, schedule it for tomorrow
            if ($currentTime > $scheduledBackupTime->getTimestamp()) {
                $scheduledBackupTime = $today->modify('+1 day');
            }

            return $scheduledBackupTime->getTimestamp();
        }

        // If a last backup exists, calculate the next backup time based on the interval
        $lastBackupTime = Time::createFromTimestamp($lastBackupTimestamp);

        $explodeString = explode('_', $backupInterval);

        if ('H' == $explodeString[1]) {
            return $lastBackupTime->addHours((int) $explodeString[0])->getTimestamp();
        }

        if ('D' == $explodeString[1]) {
            return $lastBackupTime->addDays((int) $explodeString[0])->setTime($backupHour, (int) $backupMinute, 0)->getTimestamp();
        }

        if ('M' == $explodeString[1]) {
            return $lastBackupTime->addMonth((int) $explodeString[0])->setTime($backupHour, $backupMinute, 0)->getTimestamp();
        }
    }

    /**
     * Run scheduled backups for specified backup types.
     */
    public function runScheduledBackups()
    {
        // Define backup types with default 'file'
        $backupTypes = ['file'];

        // Include 'database' backup type if configured
        if (get_setting('include_in_database_backup')) {
            $backupTypes[] = 'database';
        }

        // Iterate through each backup type
        foreach ($backupTypes as $backupType) {
            // Get the timestamp of the last backup for the current type
            $lastBackupTimestamp = get_setting('recent_backup_'.$backupType);

            // Calculate the timestamp for the next scheduled backup
            $nextScheduledBackup = $this->calculateNextScheduledBackupTime($backupType);

            // Get the current timestamp
            $now = $this->getCurrentTimestamp();

            // Set a buffer window for backup scheduling
            $bufferWindow = 600;

            // Initialize flag to determine whether to run the backup
            $runBackup = false;

            // Check if a next scheduled backup is set
            if ($nextScheduledBackup) {
                // Determine whether to run the backup based on scheduled conditions
                $runBackup = (
                    $nextScheduledBackup == $now ||
                    ($now > $nextScheduledBackup) ||
                    (
                        $nextScheduledBackup > $now &&
                        ($nextScheduledBackup - $now) < $bufferWindow &&
                        $lastBackupTimestamp &&
                        $now - $lastBackupTimestamp > $bufferWindow
                    )
                );
            }

            // If conditions are met, execute the backup for the current type
            if ($runBackup) {
                $this->executeBackup(['file' => ('file' == $backupType), 'database' => ('database' == $backupType)], [], true);

                // Update the timestamp of the last backup for the current type
                $this->settings->save_setting('recent_backup_'.$backupType, Time::now()->getTimestamp(), 1);
            }
        }
    }

    /**
     * Execute the backup process based on specified options.
     *
     * @param array $backupOptions options for backup (['file' => true, 'database' => true])
     * @param array $fileSettings  additional settings for file backup
     * @param bool  $cronEnabled   flag indicating whether the backup is triggered by cron
     *
     * @return array backup status and message
     */
    public function executeBackup($backupOptions = ['file' => true, 'database' => true], $fileSettings = [], $cronEnabled = false)
    {
        // Check if at least one backup type is selected
        if (!$backupOptions['file'] && !$backupOptions['database']) {
            return ['status' => false, 'message' => app_lang('please_select_backup_type')];
        }

        // Set unlimited max execution time for backup process
        set_time_limit(0);

        // Monitor and handle memory limit errors during backup
        $this->monitorMemoryLimitShutdown();

        // Set up the backup directory
        $this->setupBackupDirectory();

        // Create an instance of the remote backup library
        $remoteLibrary = new BackupRemote();

        // Perform file backup if selected
        if ($backupOptions['file']) {
            // Prepare the backup folder for files
            $this->prepareBackupFolder();

            // Add file backup data to the database
            $filesSqlData = [
                'type' => 'file',
                'name' => $this->backupFolderName,
            ];
            $backupId = $this->Flexiblebackup_model->storeBackup($filesSqlData);

            // Perform the file backup and get the result
            $result = $this->performFilesBackup($backupId, $fileSettings);

            // Upload to remote storage if enabled and triggered by cron
            if ('yes' == get_setting('auto_backup_to_remote_enabled') && $cronEnabled && true == $result['status']) {
                $this->uploadBackupToRemoteStorage($backupId);
            }
        }

        // Perform database backup if selected
        if ($backupOptions['database']) {
            // Prepare the backup folder for the database
            $folder = $this->prepareBackupFolder('database');

            // Add database backup data to the database
            $dbSqlData = [
                'type' => 'database',
                'name' => $this->backupFolderName,
            ];
            $backupId = $this->Flexiblebackup_model->storeBackup($dbSqlData);

            // Perform the database backup and get the result
            $result = $this->performDatabaseBackup($backupId, $folder);

            // Upload to remote storage if enabled and triggered by cron
            if ('yes' == get_setting('auto_backup_to_remote_enabled') && $cronEnabled && true == $result['status']) {
                $this->uploadBackupToRemoteStorage($backupId);
            }
        }

        // Return the status and message of the backup process
        return ['status' => $result['status'], 'message' => $result['message']];
    }

    /**
     * Delete a backup folder based on the specified type.
     *
     * @param int    $backupId ID of the backup to delete
     * @param string $type     type of backup to delete ('all', 'database', or 'file')
     *
     * @return bool whether the deletion was successful
     */
    public function deleteBackupFolder($backupId, $type='')
    {
        // Get the backup details from the database
        $backup = $this->Flexiblebackup_model->getBackupById($backupId);

        // Check if the backup exists
        if (empty($backup)) {
            return false;
        }

        // Delete the entire backup folder if the type is 'all'
        if ('all' == $type) {
            $path = BACKUP_FOLDER.$backup->backup_name;

            // Check if the folder exists
            if (is_dir($path)) {
                // Delete the backup data from the database
                $this->Flexiblebackup_model->deleteBackupFolder($backup->id);

                // Clean up the backup directory
                $this->flushDir($path);

                return true;
            }
        } else {
            // Determine the file extension based on the backup type
            $ext = ('database' == $type) ? 'sql' : 'zip';

            // Set the directory path and file path based on the backup type
            $dir  = BACKUP_FOLDER.$backup->backup_name.'/'.$type;
            $path = $dir.'.'.$ext;

            // Check if the file exists
            if (file_exists($path)) {
                // Remove the backup data for the specified type from the database
                $this->Flexiblebackup_model->removeBackupData($backup, $type);

                // Delete the file
                unlink($path);

                // Clean up the directory
                $this->flushDir($dir);

                return true;
            }
        }

        // Return false if the deletion was not successful
        return false;
    }

    /**
     * Get the path of the backup file based on the specified type.
     *
     * @param string $backupType   type of backup file ('database' or custom file type)
     * @param object $backupObject backup object containing information about the backup
     * @param bool   $extractFiles whether to unzip the file if it is a zip archive
     *
     * @return array|false the path to the backup file or false if not found
     */
    public function getBackupFile($backupType, $backupObject, $extractFiles = false)
    {
        // Get the backup folder name
        $backupFolderName = $backupObject->backup_name;
        $backupFolder     = BACKUP_FOLDER.$backupFolderName;

        // Database files
        if ('database' == $backupType) {
            $file = $backupFolder.'/database.sql';

            // Return false if the file does not exist
            return (!file_exists($file)) ? false : $file;
        }

        // Zipped files
        $file = $backupFolder.'/'.$backupType.'.zip';

        // Check if the file exists
        if (file_exists($file)) {
            // Unzip the file if specified
            $response = ($extractFiles) ? $this->extractZipFile($file, $backupFolder.'/'.$backupType) : $file;

            // Return the path to the file or the unzip response
            return $response;
        }

        // Return false if the file was not found
        return false;
    }

    /**
     * Uploads the backup files to remote storage.
     *
     * @param int $backupId the ID of the backup
     *
     * @return array the status and message of the upload operation
     */
    public function uploadBackupToRemoteStorage($backupId)
    {
        // Create an instance of the BackupRemote class
        $remoteLibrary = new BackupRemote();

        // Get the backup details
        $backup = $this->Flexiblebackup_model->getBackupById($backupId);

        // Initialize variables
        $fileCategories = [];
        $result         = ['status' => false, 'message' => ''];

        // Check if the backup exists
        if ($backup) {
            // Determine the file types to be uploaded
            if ('database' == $backup->backup_type) {
                $fileCategories[] = 'database';
            } else {
                $fileCategories = explode(',', $backup->backup_data);
            }

            // Initialize array to track files successfully uploaded to remote storage
            $uploadedToRemote = [];

            // Process each file type
            foreach ($fileCategories as $fileType) {
                // Get the file path for the current file type
                $file = $this->getBackupFile($fileType, $backup);

                try {
                    // Check if Google Drive API is enabled and authorized
                    if ('google_drive' == get_setting('remote_storage')) {
                        // Upload the file to Google Drive
                        $this->uploadBackupToGoogleDrive($file, $backup);
                    } else {
                        // Upload the file to remote storage
                        $remoteResponse = $remoteLibrary->transferBackupToRemote($file, $backup);

                        // Update result based on the response
                        if ($remoteResponse) {
                            array_push($uploadedToRemote, $fileType);
                        }
                        $result['status']   = ($remoteResponse) ? true : false;
                        $result['message']  = ($remoteResponse) ? app_lang('file_uploaded_succesfully') : ('email' == get_setting('remote_storage') && 'sftp' != get_setting('email_protocol') ? app_lang('please_verify_your_email_settings') : 'An error occurred '.$fileType.' to remote storage');
                    }
                } catch (Exception $e) {
                    // Capture and log any exceptions that occur during the upload
                    $result['message'] = $e->getMessage();
                }
            }

            // If all files are successfully uploaded, update the backup status
            if (count($uploadedToRemote) == count($fileCategories)) {
                $this->Flexiblebackup_model->markBackupAsUploadedToRemote($backupId);
            }
        } else {
            // Set an error message if the backup is not found
            $result['message'] = app_lang('backup_not_found');
        }

        return $result;
    }

    /**
     * Gets the current timestamp.
     *
     * @return int the current timestamp
     */
    public function getCurrentTimestamp()
    {
        return Time::now()->getTimestamp();
    }

    /**
     * Restores backup files based on the given parameters.
     *
     * @param int   $backupId     the ID of the backup
     * @param array $restoreFiles an array of files to restore
     *
     * @return array the response indicating the status and message
     */
    public function restoreBackupFiles($backupId, $restoreFiles = [])
    {
        $backup = $this->Flexiblebackup_model->getBackupById($backupId);

        $backupFolder = BACKUP_FOLDER.$backup->backup_name;

        // Check if the backup exists
        if (!$backup || !is_dir($backupFolder)) {
            return ['message' => app_lang('backup_not_found'), 'status' => false];
        }

        // If 'database' is in the restoreFiles array, perform database restore
        if (\in_array('database', $restoreFiles)) {
            return $this->restoreBackupDatabase($backupFolder);
        }

        // Restore other specified files
        return $this->doRestore($backupFolder, $restoreFiles);
    }

    /**
     * Set up the backup directory if it doesn't exist.
     */
    private function setupBackupDirectory()
    {
        // Specify the path for the backup folder
        $backupFolderPath = BACKUP_FOLDER;

        // Check if the backup folder doesn't exist
        if (!is_dir($backupFolderPath)) {
            // Create the backup folder with the necessary permissions
            mkdir($backupFolderPath, 0755, true);

            // Create index.html file in the backup folder
            $indexFile = $backupFolderPath.'/index.html';
            file_put_contents($indexFile, '');

            // Create .htaccess file in the backup folder
            $htaccessFile = $backupFolderPath.'/.htaccess';
            file_put_contents($htaccessFile, 'Order Deny,Allow'.\PHP_EOL.'Deny from all');
        }
    }

    /**
     * Prepare the backup folder for the specified backup type.
     *
     * @param string $backupType the type of backup, default is "files"
     *
     * @return string|false the path to the created backup folder or false on failure
     */
    private function prepareBackupFolder($backupType = 'files')
    {
        try {
            // Determine the prefix for the backup name
            $prefix = $this->determineBackupNamePrefix();

            // Generate the folder name using the backup type, prefix, and timestamp
            $folderName   = $backupType.'_'.$prefix.'_'.date('Y-m-d_H-i-s');
            $backupFolder = BACKUP_FOLDER.$folderName;

            // Create the backup folder with the necessary permissions
            mkdir($backupFolder, 0755);

            // Set class properties for later use
            $this->backupFolder     = $backupFolder;
            $this->backupFolderName = $folderName;

            // Add a log entry indicating the creation of the backup folder
            $this->addToBackupLog('Backup folder created at '.date('Y-m-d H:i:s'));

            return $backupFolder;
        } catch (Exception $e) {
            // Add a log entry in case of an error during folder creation
            $this->addToBackupLog('Error occurred when creating backup folder: '.$e->getMessage());
        }

        return false;
    }

    /**
     * Perform files backup for the specified backup ID and file settings.
     *
     * @param int   $backupId     the ID of the backup
     * @param array $fileSettings settings for files backup
     *
     * @return array the status and message of the backup operation
     */
    private function performFilesBackup($backupId, $fileSettings = [])
    {
        try {
            // Get the backup folder path
            $backupFolder = $this->backupFolder;

            // Get the selected folders for backup
            $selectedFolders = $this->getBackupFolders($fileSettings);

            // Check if any folders are selected
            if (empty($selectedFolders)) {
                // Delete the backup folder and return an error message
                $this->deleteBackupFolder($backupId);

                return ['status' => false, 'message' => app_lang('please_select_files')];
            }

            // Process each selected folder
            foreach ($selectedFolders as $selectedFolder) {
                // Log the start of files backup for the folder
                $this->addToBackupLog($selectedFolder.' Files Backup started at '.date('Y-m-d H:i:s'));

                if ('others' == $selectedFolder) {
                    // Copy files from "others" folder
                    $this->copyOtherFilesToDestination($backupFolder.'/'.$selectedFolder);
                } else {
                    // Copy files and directories from the selected folder
                    $sourceFolder      = FCPATH.$selectedFolder;
                    $destinationFolder = $backupFolder.'/'.$selectedFolder;

                    $this->copyFilesAndDirectories($sourceFolder, $destinationFolder);
                }

                $this->Flexiblebackup_model->updateBackupData(['key' => $selectedFolder], $backupId);
                $this->zipDirectory($backupFolder.'/'.$selectedFolder);
                $this->flushDir($backupFolder.'/'.$selectedFolder);

                // Log the completion of files backup for the folder
                $this->addToBackupLog($selectedFolder.' Files Backup completed at '.date('Y-m-d H:i:s'));
            }
        } catch (Exception $e) {
            // Log an error if an exception occurs during files backup
            $this->addToBackupLog('Error occurred when backing up files: '.$e->getMessage());
        }

        return ['status' => true, 'message' => app_lang('backup_successfully')];
    }

    /**
     * Uploads the backup files to Google Drive.
     *
     * @param string $file   the file path to be uploaded
     * @param object $backup the backup object
     */
    private function uploadBackupToGoogleDrive($file, $backup)
    {
        // Create an instance of the Google class
        $google = new Google();
        // Upload the file to Google Drive
        $google->upload_file($file, $backup);
    }

    /**
     * Performs the database backup.
     *
     * @param int    $backupId     the ID of the backup
     * @param string $backupFolder the folder where the backup is stored
     *
     * @return array the status and message of the backup operation
     */
    private function performDatabaseBackup($backupId, $backupFolder = '')
    {
        // Create an instance of the ManageBackup class
        $manageBackup = new ManageBackup();

        try {
            // Add to backup log
            $this->addToBackupLog('Database backup started at '.date('Y-m-d H:i:s'));

            // Perform the database backup
            $backupResult = $manageBackup->backup($backupFolder);

            // Add to backup log
            $this->addToBackupLog('Database backup completed at '.date('Y-m-d H:i:s'));
        } catch (Exception $e) {
            // Add to backup log in case of an error
            $this->addToBackupLog('Error occurred when backing up the database: '.$e->getMessage());
        }

        return ['status' => true, 'message' => app_lang('backup_successfully')];
    }

    /**
     * Monitors the PHP memory limit and triggers actions on shutdown.
     */
    private function monitorMemoryLimitShutdown()
    {
        // Register shutdown function
        register_shutdown_function(function () {
            $lastError = error_get_last();

            // Check if a fatal error related to memory limit occurred
            if ($lastError && false !== strpos($lastError['message'], 'Allowed memory size of')) {
                // Display an error message
                echo '<h2>A fatal error occurred during backup due to PHP memory limit.</h2>';
                echo '<div style="font-size:18px;">';
                echo '<p>Your current PHP memory limit is '.ini_get('memory_limit').', which is insufficient for the backup process.</p>';
                echo '<p>Suggestion: Increase the PHP memory limit and retry the backup.</p>';
                echo '</div>';
            } elseif ($lastError) {
                // Display other fatal errors
                trigger_error('Fatal error: '.$lastError['message'].' in '.$lastError['file'].' on line '.$lastError['line'], \E_USER_ERROR);
            }
        });
    }

    /**
     * Determines the backup name prefix.
     *
     * @return string the backup name prefix
     */
    private function determineBackupNamePrefix()
    {
        // Get the backup name prefix from settings
        $backupNamePrefix = get_setting('backup_name_prefix');
        // Use a default value if the prefix is empty
        return ('' == $backupNamePrefix) ? 'backup' : $backupNamePrefix;
    }

    /**
     * Adds a message to the backup log.
     *
     * @param string $message the message to be added
     */
    private function addToBackupLog($message = '')
    {
        // Get the backup directory and log file path
        $backupDirectory = $this->backupFolder;
        $logFilePath     = $backupDirectory.'/log.txt';

        // Open the log file for appending
        $filePointer = @fopen($logFilePath, 'a+');

        // Modify the message and write it to the log file
        $message = $message.PHP_EOL.PHP_EOL;
        $message = str_replace(FCPATH, '', $message);
        fwrite($filePointer, $message);

        // Close the file
        fclose($filePointer);
    }

    /**
     * Extracts files from a ZIP archive.
     *
     * @param string $zipFilePath the path to the ZIP file
     * @param string $extractPath the path where files should be extracted
     *
     * @return array|false the data structure representing the extracted files
     */
    private function extractZipFile($zipFilePath, $extractPath)
    {
        // Check if the folder exists; if it does, return the folder
        if (is_dir($extractPath)) {
            if ($this->checkIfDirectoryIsEmptyOrNot($extractPath)) {
                return $this->prepareFilesAndFoldersList($extractPath);
            }
        }

        if (!is_dir($extractPath)) {
            @mkdir($extractPath, 0777, true);   
        }

        try {
            $this->zipFile
                ->openFile($zipFilePath)
                ->extractTo($extractPath);
            return $this->prepareFilesAndFoldersList($extractPath);
        } catch (\PhpZip\Exception\ZipException $e) {
            return false;
        } finally {
            $this->zipFile->close();
        }
    }

    /**
     * Checks if a directory is empty.
     *
     * @param string $directory the path to the directory
     *
     * @return bool true if the directory is empty, false otherwise
     */
    private function checkIfDirectoryIsEmptyOrNot($directory)
    {
        // Open the directory handle
        $handle = opendir($directory);

        // Check each entry in the directory
        while (false !== ($entry = readdir($handle))) {
            // If a non-dot entry is found, the directory is not empty
            if ('.' != $entry && '..' != $entry) {
                // Close the directory handle and return false
                closedir($handle);

                return false;
            }
        }

        // Close the directory handle and return true if no non-dot entry is found
        closedir($handle);

        return true;
    }

    /**
     * Prepare a structure representing the contents of a directory.
     *
     * @param string $directory the path to the directory
     *
     * @return array the data structure
     */
    private function prepareFilesAndFoldersList($directory)
    {
        $data  = [];
        $files = scandir($directory);

        foreach ($files as $file) {
            if ('.' != $file && '..' != $file) {
                $filePath = $directory.'/'.$file;

                if (is_dir($filePath)) {
                    $data[] = [
                        'parent' => $file,
                        'child'  => $this->prepareFilesAndFoldersList($filePath),
                    ];
                }

                if (!is_dir($filePath)) {
                    $data[] = ['parent' => $file];
                }
            }
        }

        return $data;
    }

    /**
     * Gets the list of backup folders based on file settings or default folders.
     *
     * @param array $fileSettings the file settings array
     *
     * @return array the list of backup folders
     */
    private function getBackupFolders($fileSettings = []): array
    {
        $backupFolders = [
            'app',
            'assets',
            'documentation',
            'files',
            'plugins',
            'system',
            'writable',
            'others',
        ];

        // If file settings are provided, it's a manual backup
        if ($fileSettings) {
            // Filter settings array where value is 1
            $selectedFolders = array_filter($fileSettings, function ($value) {
                return 1 == $value;
            });

            return array_keys($selectedFolders);
        }

        // If no file settings, use default folders with exclusion based on settings
        foreach ($backupFolders as $folderName) {
            $optionName = 'Flexiblebackup_include_'.$folderName;

            if (0 == get_setting($optionName)) {
                $key = array_search($folderName, $backupFolders);

                if (false !== $key) {
                    unset($backupFolders[$key]);
                }
            }
        }

        return $backupFolders;
    }

    /**
     * Copies files and directories from source to destination.
     *
     * @param string $src  the source path
     * @param string $dest the destination path
     *
     * @return bool true if the copy is successful, false otherwise
     */
    private function copyFilesAndDirectories($src, $dest)
    {
        try {
            // If the source is a file, copy it to the destination
            if (is_file($src)) {
                $result = copy($src, $dest);

                return $result;
            }

            // If the source is a directory, recursively copy its contents to the destination
            if (is_dir($src)) {
                // If the destination directory doesn't exist, create it
                if (!is_dir($dest)) {
                    mkdir($dest, 0777, true);
                }

                // Open the source directory handle
                $dir = opendir($src);

                // Loop through entries in the source directory
                while (($entry = readdir($dir)) !== false) {
                    // Skip "." and ".."
                    if ('.' == $entry || '..' == $entry) {
                        continue;
                    }

                    // Create source and destination paths for the entry
                    $srcPath = $src.'/'.$entry;
                    $dstPath = $dest.'/'.$entry;

                    // Recursively copy the entry
                    $this->copyFilesAndDirectories($srcPath, $dstPath);
                }

                // Close the source directory handle
                closedir($dir);

                return true;
            }

            return false;
        } catch (Exception $e) {
            // Add to log file in case of an error
            $this->addToBackupLog('Error occurred when copying files from '.$src.'. '.$e->getMessage());
        }
    }

    /**
     * Copies other files to the destination.
     *
     * @param string $destination the destination path
     * @param string $source      the source path (default is FCPATH)
     *
     * @return bool true if the copy is successful, false otherwise
     */
    private function copyOtherFilesToDestination($destination, $source = FCPATH)
    {
        $files = scandir($source);

        // Create an "others" folder to put the files
        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        // Copy each file to the destination
        foreach ($files as $file) {
            if (is_file($source.$file)) {
                copy($source.$file, $destination.'/'.$file);
            }
        }

        return true;
    }

    /**
     * Checks if a string starts with a given prefix.
     *
     * @param string $string the string to check
     * @param string $prefix the prefix to check against
     *
     * @return bool true if the string starts with the prefix, false otherwise
     */
    private function isStringStartsWith($string, $prefix)
    {
        return substr($string, 0, \strlen($prefix)) === $prefix;
    }

    /**
     * Restores the database from the backup folder.
     *
     * @param string $backupFolder the path to the backup folder
     *
     * @return array the response indicating the status and message
     */
    private function restoreBackupDatabase($backupFolder)
    {
        $response = ['status' => false, 'message' => app_lang('backup_file_not_found')];
        $file     = $backupFolder.'/database.sql';

        // Check if the database backup file exists
        if (!file_exists($file)) {
            return ['status' => false, 'message' => app_lang('backup_file_not_found')];
        }

        // Begin database transaction
        $this->db->transBegin();

        try {
            $parser        = new SqlScriptParser();
            $sqlStatements = $parser->parse($file);

            // Execute each SQL statement in the database backup file
            foreach ($sqlStatements as $statement) {
                $distilled = $parser->removeComments($statement);
                if (!empty($distilled)) {
                    $this->db->query($distilled);
                }
            }

            // Commit the transaction on success
            $this->db->transCommit();

            return ['status' => true, 'message' => app_lang('backup_restored_successfully')];
        } catch (mysqli_sql_exception $exception) {
            // Rollback the transaction on failure
            $this->db->transRollback();
            throw $exception;

            return ['status' => false, 'message' => app_lang('something_went_wrong')];
        }
    }

    /**
     * Restores specified files from the backup folder.
     *
     * @param string $backupFolder the path to the backup folder
     * @param array  $restoreFiles an array of files to restore
     *
     * @return array the response indicating the status and message
     */
    private function doRestore($backupFolder, $restoreFiles)
    {
        // Get all the backup files that exist
        $existingBackupFiles = array_filter($restoreFiles, function($restoreFile) use ($backupFolder) {
            return file_exists($backupFolder.'/'.$restoreFile.'.zip');
        });

        // Extract the zip files to temporary directories
        $temporaryDirectories = array_map(function($restoreFile) use ($backupFolder) {
            $backupTempDirectory = $backupFolder.'/'.$restoreFile;
            (!is_dir($backupTempDirectory)) ? $this->extractZipFile($backupFolder.'/'.$restoreFile.'.zip', $backupTempDirectory) : '';
            return $backupTempDirectory;
        }, $existingBackupFiles);

        // Copy the backup files and directories to the target directories
        $copyResults = array_map(function($backupTempDirectory, $targetDirectory) {
            if ('others' !== $backupTempDirectory) {
                $oldDir = $targetDirectory.'_backup';
                rename($targetDirectory, $oldDir);
                $copyDir = $this->copyFilesAndDirectories($backupTempDirectory, $targetDirectory);
                if ($copyDir) {
                    $this->flushDir($backupTempDirectory);
                    (!is_dir($targetDirectory)) ? rename($oldDir, $targetDirectory) : $this->flushDir($oldDir);
                }
                return $copyDir;
            }
            return $this->copyOtherFilesToDestination($targetDirectory, $backupTempDirectory.'/');
        }, $temporaryDirectories, array_map(function($restoreFile) {
            return ('others' == $restoreFile) ? FCPATH : FCPATH.$restoreFile;
        }, $existingBackupFiles));

        // Set the response based on the success of copying and renaming
        $response = [
            'status' => (array_product($copyResults)) ? true : false,
            'message' => (array_product($copyResults)) ? app_lang('backup_restored_successfully') : app_lang('something_went_wrong'),
        ];

        return $response;
    }

    /**
     * Zips the contents of a directory and creates a zip file.
     *
     * @param string $directory the path to the directory to be zipped
     *
     * @throws Exception if an error occurs during the zipping process
     */
    public function zipDirectory($directory)
    {
        try {
            // Add to log file
            $this->addToBackupLog('Zipping folder - '.$directory.' started at '.date('Y-m-d H:i:s'));

            $filename = $directory . '.zip';

            // Check if the given path is a directory
            if (is_dir($directory)) {
                $directoryIterator = new \RecursiveDirectoryIterator($directory);
                foreach ($directoryIterator as $fileinfo) {
                    $this->zipFile->addFilesFromIterator($directoryIterator, $directoryIterator->getFilename());
                }
                $this->zipFile->saveAsFile($filename);
                $this->zipFile->close();

                // Add a log entry indicating the completion of zipping the folder
                $this->addToBackupLog('Zipping folder - ' . $directory . ' completed at ' . date('Y-m-d H:i:s'));
            }
        } catch (Exception $e) {
            // Add to log file
            $this->addToBackupLog('Error occurred when zipping folder - '.$directory.'. '.$e->getMessage());
        }
    }

    /**
     * Recursively cleans up a directory by deleting its contents.
     *
     * @param string $path the path to the directory to be cleaned up
     *
     * @return bool true if the directory is successfully cleaned up, false otherwise
     */
    private function flushDir($path)
    {
        if (is_dir($path)) {
            $files = array_diff(scandir($path), ['.', '..']);

            // Iterate through each file and either delete it or clean up the subdirectory
            foreach ($files as $file) {
                $filePath = $path.'/'.$file;
                is_dir($filePath) ? $this->flushDir($filePath) : unlink($filePath);
            }

            // Remove the directory itself
            return rmdir($path);
        }

        return false;
    }
}
