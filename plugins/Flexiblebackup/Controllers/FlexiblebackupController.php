<?php

namespace Flexiblebackup\Controllers;

use App\Controllers\Security_Controller;
use Flexiblebackup\Libraries\Backup;

/**
 * Class FlexiblebackupController.
 *
 * Controller for managing backups.
 */
class FlexiblebackupController extends Security_Controller
{
    /**
     * @var flexiblebackupModel
     *                          The Flexiblebackup model for interacting with backup data
     */
    protected $Flexiblebackup_model;

    /**
     * @var array
     *            Array for storing settings related to flexible backups
     */
    protected $settings;

    /**
     * @var backupLibrary
     *                    The backup library used for backup operations
     */
    protected $backupLib;

    /**
     * Constructor for the FlexiblebackupController.
     */
    public function __construct()
    {
        parent::__construct();

        // Load the 'flexiblebackup' helper for this controller.
        helper('flexiblebackup');

        // Initialize the Flexiblebackup model for backup data.
        $this->Flexiblebackup_model = model('Flexiblebackup\Models\FlexiblebackupModel');

        // Initialize the 'Settings_model' to manage settings related to flexible backups.
        $this->settings = new \App\Models\Settings_model();

        // Initialize the 'Flexiblebackup' library for backup operations.
        $this->backupLib = new Backup();
    }

    /**
     * Display and manage settings related to flexible backups.
     *
     * @param string $type The type of settings to manage (e.g., 'database', 'files').
     *
     * @return \CodeIgniter\HTTP\Response the response containing the view of flexible backup settings
     */
    public function settings($type)
    {
        // Prepare data for the view.
        $viewData['title']                 = $type;
        $viewData['next_scheduled_backup'] = [
            'database_backup_schedule' => $this->backupLib->calculateNextScheduledBackupTime('database') ?? '',
            'files_backup_schedule'    => $this->backupLib->calculateNextScheduledBackupTime('files') ?? '',
            'time_now'                 => $this->backupLib->getCurrentTimestamp(),
        ];

        // Render the view for flexible backup settings.
        return $this->template->rander('Flexiblebackup\Views\flexiblebackup_settings', $viewData);
    }

    /**
     * Load the view for the backup modal.
     *
     * @return \CodeIgniter\HTTP\Response the response containing the view of the backup modal
     */
    public function loadBackupView()
    {
        // Render the view for the backup modal.
        return $this->template->view('Flexiblebackup\Views\backup_modal');
    }

    /**
     * List and retrieve backup data in JSON format.
     *
     * @return \CodeIgniter\HTTP\Response the response containing JSON data of backups
     */
    public function listBackups()
    {
        // Get the list of backups from the Flexiblebackup model.
        $backups = $this->Flexiblebackup_model->getAllBackups();
        $result  = [];

        // Process each backup and create a JSON-compatible data structure.
        foreach ($backups as $backup) {
            $result[] = $this->makeBackupRow($backup);
        }

        // Encode the result in JSON format and send it as a response.
        echo json_encode(['data' => $result]);
    }

    /**
     * Perform a backup operation based on user-selected options.
     *
     * @return \CodeIgniter\HTTP\Response the response containing JSON data about the backup operation
     */
    public function performBackup()
    {
        // Prepare a default response.
        $response = [
            'success' => false,
            'message' => app_lang('please_select_backup_type'),
        ];

        if ($this->request->isAJAX() && $this->request->getPost()) {
            $postData        = $this->request->getPost();
            $includeDatabase = $postData['include_database_in_the_backup'] ?? 0;
            $includeFiles    = $postData['include_file_in_the_backup'] ?? 0;

            // Define backup options and file settings.
            $options = [
                'file'     => (bool) $includeFiles,
                'database' => (bool) $includeDatabase,
            ];

            $fileSettings = [
                'plugins'       => $postData['include_plugins'] ?? 0,
                'app'           => $postData['include_app'] ?? 0,
                'files'         => $postData['include_files'] ?? 0,
                'assets'        => $postData['include_assets'] ?? 0,
                'system'        => $postData['include_system'] ?? 0,
                'documentation' => $postData['include_documentation'] ?? 0,
                'writable'      => $postData['include_writable'] ?? 0,
                'others'        => $postData['include_other'] ?? 0,
            ];

            // Execute the backup operation and update the response.
            $backupResult = $this->backupLib->executeBackup($options, $fileSettings);

            $response = [
                'success' => $backupResult['status'],
                'message' => $backupResult['message'],
            ];
        }

        // Encode the response in JSON format and send it as a response.
        echo json_encode($response);
    }

    /**
     * Save and update flexible backup settings based on user input.
     *
     * @return \CodeIgniter\HTTP\Response the response containing JSON data indicating the status of the settings update
     */
    public function saveSettings()
    {
        if ($this->request->getPost()) {
            $postData      = $this->request->getPost();
            $remoteStorage = $postData['remote_storage'] ?? '';

            $settingsToUpdate = [
                'include_in_database_backup',
                'files_backup_schedule',
                'database_backup_schedule',
                'remote_storage',
                'include_plugins',
                'include_app',
                'include_files',
                'include_assets',
                'include_system',
                'include_documentation',
                'include_writable',
                'include_other',
                'backup_name_prefix',
                'auto_backup_to_remote_enabled',
                'auto_backup_time',
            ];

            // Update the specified settings based on user input.
            foreach ($settingsToUpdate as $setting) {
                $value = $postData[$setting] ?? 0;
                $this->settings->save_setting($setting, $value);
            }

            // Define FTP settings based on the selected remote storage type.
            $ftpSettings = [];
            switch ($remoteStorage) {
                case 'ftp':
                    $ftpSettings = ['ftp_server', 'ftp_user', 'ftp_password', 'ftp_path', 'ftp_port'];
                    break;

                case 'sftp':
                    $ftpSettings = ['sftp_server', 'sftp_user', 'sftp_password', 'sftp_path', 'sftp_port'];
                    break;

                case 's3':
                    $ftpSettings = ['s3_access_key', 's3_secret_key', 's3_location', 's3_region'];
                    break;

                case 'email':
                    $ftpSettings = ['email_address'];
                    break;

                case 'google_drive':
                    $ftpSettings = ['google_drive_client_id_for_backup', 'google_drive_client_secret_for_backup'];
                    break;

                default:
                    $ftpSettings = [];
            }

            // Update FTP settings if applicable.
            foreach ($ftpSettings as $setting) {
                $this->validate_submitted_data([
                    $setting   => 'required',
                ]);
                $this->settings->save_setting($setting, $postData[$setting] ?? '');
            }

            // Send a JSON response indicating the status of the settings update.
            echo json_encode(['success' => (\count($settingsToUpdate) > 0), 'message' => (\count($settingsToUpdate) > 0) ? app_lang('settings_updated_successfully') : app_lang('something_went_wrong')]);
        }
    }

    /**
     * Remove a backup directory based on the provided backup ID.
     *
     * @param int   $backupId   the ID of the backup directory to remove
     * @param mixed $backupType
     *
     * @return \CodeIgniter\HTTP\Response the response containing JSON data indicating the status of the removal operation
     */
    public function removeBackupDirectory($backupId, $backupType)
    {
        // Attempt to delete the backup folder using the provided backup ID.
        $success = $this->backupLib->deleteBackupFolder($backupId, $backupType);

        // Send a JSON response indicating the status of the removal operation.
        echo json_encode([
            'success' => $success,
            'message' => ($success) ? app_lang('record_deleted') : app_lang('no_longer_exists'),
        ]);
    }

    /**
     * Execute the upload of a backup to remote storage.
     *
     * @return \CodeIgniter\HTTP\Response the response containing JSON data indicating the status of the upload operation
     */
    public function executeRemoteBackupUpload()
    {
        $backupId             = $this->request->getPost('id');
        $remoteStorageEnabled = get_setting('remote_storage');

        $uploadResponse = ['status' => false, 'message' => '<a href='.get_uri('flexiblebackup/settings/settings').'><span class="bold link-light"> '.app_lang('verify_your_remote_settings').'</span></a>'];

        if ($remoteStorageEnabled) {
            // Attempt to upload the backup to remote storage.
            $uploadResponse = $this->backupLib->uploadBackupToRemoteStorage($backupId);
        }

        // Send a JSON response indicating the status of the upload operation.
        echo json_encode([
            'status'  => $uploadResponse['status'],
            'message' => (!empty($uploadResponse['message'])) ? $uploadResponse['message'] : app_lang('something_went_wrong'),
        ]);
    }

    /**
     * Restore selected backup files from a specific backup.
     *
     * @return \CodeIgniter\HTTP\Response the response containing JSON data indicating the status of the file restoration operation
     */
    public function restoreBackupFiles()
    {
        if ($this->request->getPost()) {
            $postData     = $this->request->getPost();
            $backupKey    = $postData['backup_id'];
            $fileCategory = $postData['restore_files'] ?? [];

            if ($fileCategory) {
                // Attempt to restore backup files for the selected backup and file types.
                $result = $this->backupLib->restoreBackupFiles($backupKey, $fileCategory);
            }

            $response = [
                'success' => ($fileCategory) ? $result['status'] : false,
                'message' => ($fileCategory) ? $result['message'] : app_lang('least_one_file_type_to_restore'),
            ];
        }

        // Send a JSON response indicating the status of the file restoration operation.
        echo json_encode($response);
    }

    /**
     * Handle various backup-related actions based on the provided action and parameters.
     *
     * @param int    $id         the ID associated with the backup
     * @param string $action     the action to be performed
     * @param string $backupType the type of backup
     *
     * @return string the view or content associated with the requested action
     */
    public function handleBackupActions($id, $action, $backupType = '')
    {
        if ('preview_file' == $action) {
            $backup        = $this->Flexiblebackup_model->getBackupById($id);
            $files         = $this->backupLib->getBackupFile($backupType, $backup, true);
            $fileStructure = prepareFileStructureForJS($files);

            $result = $this->template->view('Flexiblebackup\Views\file_action_modal', [
                'backupId' => $id,
                'type'     => $backupType,
                'backup'   => $backup,
                'files'    => ('database' == $backupType) ? [] : $fileStructure,
            ]);
        }

        if ('read_log_file' == $action) {
            $backup = $this->Flexiblebackup_model->getBackupById($id);
            $file   = BACKUP_FOLDER.$backup->backup_name.'/log.txt';
            $result = $this->template->view('Flexiblebackup\Views\view_log_modal', [
                'backupId'    => $id,
                'backup'      => $backup,
                'logFilePath' => $file,
            ]);
        }

        if ('recover_backup' == $action) {
            $backup     = $this->Flexiblebackup_model->getBackupById($id);
            $file_types = ('database' == $backup->backup_type) ? ['database'] : explode(',', $backup->backup_data);
            $result     = $this->template->view('Flexiblebackup\Views\restore_backup_options', [
                'backupId'   => $id,
                'backup'     => $backup,
                'file_types' => $file_types,
            ]);
        }

        return $result;
    }

    /**
     * Download a specific backup file.
     *
     * @param int    $id   the ID associated with the backup
     * @param string $type The type of the backup file (e.g., "database", "log").
     *
     * @return \CodeIgniter\HTTP\Response|null the file download response or a redirect response
     */
    public function downloadBackupFile($id, $type)
    {
        // Retrieve the backup data based on the provided ID.
        $backup = $this->Flexiblebackup_model->getBackupById($id);

        // Determine the file extension based on the provided type.
        $ext = ('database' == $type) ? 'sql' : (('log' == $type) ? 'txt' : 'zip');

        // Construct the full path to the backup file.
        $path = BACKUP_FOLDER.$backup->backup_name.'/'.$type.'.'.$ext;

        // Define the full name for the downloaded file.
        $fullname = $type.'.'.$ext;

        if (file_exists($path)) {
            // If the file exists, create a download response and set the file name.
            return $this->response->download($path, null)->setFileName($fullname);
        }

        // If the file does not exist, redirect to the existing backups page.
        app_redirect('flexiblebackup/settings/existing_backups');
    }

    /**
     * Display the backup view for database and files backup.
     *
     * @return string the HTML content of the backup view
     */
    public function backupView()
    {
        // Initialize view data and set the title.
        $viewData          = [];
        $viewData['title'] = app_lang('database_and_files_backup');

        // Render and return the backup form view.
        return $this->template->rander('Flexiblebackup\Views\backup_form', $viewData);
    }

    /**
     * Create a row of backup data for display in the list view.
     *
     * @param array $backup the backup data to create the row for
     *
     * @return array an array containing data for a row in the backup list
     */
    private function makeBackupRow($backup)
    {
        $backup_date = date('M jS, Y, g:i a', strtotime($backup['datecreated']));

        $backup_button = '';
        if ('file' == $backup['backup_type']) {
            foreach (explode(',', $backup['backup_data']) as $item) {
                if (!$item) {
                    continue;
                }
                $backup_button .= modal_anchor(
                    get_uri('flexiblebackup/handle_backup_actions/'.$backup['id'].'/preview_file/'.$item),
                    "<span class='icon-16'></span>".ucfirst($item),
                    ['class' => 'btn btn-sm btn-danger me-1', 'title' => app_lang('download').' '.ucfirst($item).' '.$backup_date]
                );
            }
        } else {
            $backup_button .= modal_anchor(
                get_uri('flexiblebackup/handle_backup_actions/'.$backup['id'].'/preview_file/'.$backup['backup_type']),
                "<span class='icon-16'></span>".ucfirst($backup['backup_type']),
                ['class' => 'btn btn-sm btn-info', 'title' => app_lang('download').' '.ucfirst($backup['backup_type']).' '.$backup_date]
            );
        }

        $label              = (1 == $backup['uploaded_to_remote']) ? app_lang('yes') : app_lang('no');
        $uploaded_to_remote = '<span class="label label-info">'.ucfirst($label).'</span>';

        $options = '';
        $options .= '<div class="dropdown">
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <i data-feather="menu" class="icon-16"></i>
                        </button>
                      <ul class="dropdown-menu">';
        $options .= '<li>'.modal_anchor(get_uri('flexiblebackup/handle_backup_actions/'.$backup['id'].'/recover_backup'), "<span class='icon-16'></span>".app_lang('restore'), ['class' => 'dropdown-item text-dark', 'title' => app_lang('restore_files_from').' '.$backup_date]).'</li>';

        if (0 == $backup['uploaded_to_remote']) {
            $options .= '<li><a class="dropdown-item text-dark" data-id="'.$backup['id'].'" href="javascript:void(0)" id="upload_to_remote_btn">'.app_lang('upload_to_remote').'</a></li>';
        }

        $options .= '<li>'.modal_anchor(get_uri('flexiblebackup/handle_backup_actions/'.$backup['id'].'/read_log_file'), "<span class='icon-16'></span>".app_lang('view_log'), ['class' => 'dropdown-item text-dark', 'title' => app_lang('log_file')]).'</li>';
        $options .= '<li>'.js_anchor(app_lang('delete'), ['title' => app_lang('delete'), 'class' => 'delete dropdown-item text-dark', 'data-id' => $backup['id'], 'data-action-url' => get_uri('flexiblebackup/remove_backup_directory/'.$backup['id'].'/all'), 'data-action' => 'delete-confirmation']).'</li>';
        $options .= '</ul>
                    </div>';

        return [
            $backup_date,
            $backup_button,
            $uploaded_to_remote,
            $options,
        ];
    }
}
