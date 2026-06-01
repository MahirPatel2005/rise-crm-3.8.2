<?php

namespace Flexiblebackup\Libraries;

use BackupManager\Compressors;
use BackupManager\Config\Config;
use BackupManager\Databases;
use BackupManager\Filesystems;
use BackupManager\Filesystems\Destination;
use BackupManager\Manager;
use Exception;

class ManageBackup
{
    /**
     * Perform a database backup and store it in the specified folder.
     *
     * @param string $folder the root folder to store the backup
     *
     * @return bool true if the backup was successful, false otherwise
     */
    public static function backup($folder = '')
    {
        // Define the filesystem configuration for the local storage.
        $configFileSystemProvider = new Config([
            'local' => [
                'type' => 'Local',
                'root' => $folder,
            ],
        ]);

        // Get the database connection and details.
        $db     = \Config\Database::connect();
        $dbname = $db->database;

        // Define the database configuration for production and development environments.
        $configDatabase = new Config([
            'production' => [
                'type'     => 'mysql',
                'host'     => $db->hostname,
                'port'     => $db->port,
                'user'     => $db->username,
                'pass'     => $db->password,
                'database' => $db->database,
            ],
            'development' => [
                'type'     => 'mysql',
                'host'     => $db->hostname,
                'port'     => $db->port,
                'user'     => $db->username,
                'pass'     => $db->password,
                'database' => $db->database,
            ],
        ]);

        // Initialize filesystem, database, and compressor providers.
        $filesystems = new Filesystems\FilesystemProvider($configFileSystemProvider);
        $filesystems->add(new Filesystems\LocalFilesystem());

        $databases = new Databases\DatabaseProvider($configDatabase);
        $databases->add(new Databases\MysqlDatabase());
        $databases->add(new Databases\PostgresqlDatabase());

        $compressors = new Compressors\CompressorProvider();
        $compressors->add(new Compressors\GzipCompressor());
        $compressors->add(new Compressors\NullCompressor());

        // Create a backup manager using the providers.
        $manager = new Manager($filesystems, $databases, $compressors);

        // Set the backup name (e.g., database.sql).
        $backup_name = 'database.sql';

        try {
            // Perform the database backup and store it locally.
            $manager->makeBackup()->run('development', [new Destination('local', $backup_name)], 'null');

            return true;
        } catch (Exception $e) {
            // An exception occurred during the backup process.
            return false;
        }
    }
}
