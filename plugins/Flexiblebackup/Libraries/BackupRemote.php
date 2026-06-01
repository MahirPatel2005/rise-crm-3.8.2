<?php

namespace Flexiblebackup\Libraries;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;

class BackupRemote
{
    public function __construct()
    {
        // Constructor: You can put initialization logic here if needed.
    }

    /**
     * Transfer backup file to a remote storage based on the remote storage configuration.
     *
     * @param string $backupFile the backup file path
     * @param mixed  $backupData the backup data or parameters
     *
     * @throws Exception if an error occurs during the transfer
     *
     * @return bool true if transfer is successful, false otherwise
     */
    public function transferBackupToRemote($backupFile, $backupData)
    {
        try {
            return $this->transfer($backupFile, $backupData);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Transfer the backup file to a remote storage based on the remote storage type.
     *
     * @param string $backupFile the backup file path
     * @param mixed  $backupData the backup data or parameters
     *
     * @throws Exception if an error occurs during the transfer
     *
     * @return bool true if transfer is successful, false otherwise
     */
    private function transfer($backupFile, $backupData)
    {
        $remoteStorage = get_setting('remote_storage');

        try {
            if ('ftp' == $remoteStorage) {
                return $this->ftpTransfer($backupFile);
            } elseif ('s3' == $remoteStorage) {
                return $this->s3Transfer($backupFile);
            } elseif ('sftp' == $remoteStorage) {
                return $this->sftpTransfer($backupFile);
            } elseif ('email' == $remoteStorage) {
                return $this->emailTransfer($backupFile, $backupData);
            }

            return false;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Transfer the backup file using FTP protocol.
     *
     * @param string $backupFile the backup file path
     *
     * @throws Exception if an error occurs during the transfer
     *
     * @return bool true if transfer is successful, false otherwise
     */
    private function ftpTransfer($backupFile)
    {
        $adapter = new \League\Flysystem\Ftp\FtpAdapter(
            \League\Flysystem\Ftp\FtpConnectionOptions::fromArray([
                'host'                            => get_setting('ftp_server'),
                'root'                            => get_setting('ftp_path'),
                'username'                        => get_setting('ftp_user'),
                'password'                        => get_setting('ftp_password'),
                'port'                            => (int) get_setting('ftp_port') ?? 21,
                'ssl'                             => false,
                'timeout'                         => 90,
                'utf8'                            => false,
                'passive'                         => true,
                'transferMode'                    => FTP_BINARY,
                'systemType'                      => null,
                'ignorePassiveAddress'            => null,
                'timestampsOnUnixListingsEnabled' => false,
                'recurseManually'                 => true,
            ])
        );

        $filesystem = new \League\Flysystem\Filesystem($adapter);

        try {
            return $this->transferFileToRemote($filesystem, $backupFile);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Transfer the backup file to an S3 bucket.
     *
     * @param string $backupFile the backup file path
     *
     * @throws Exception if an error occurs during the transfer
     *
     * @return bool true if transfer is successful, false otherwise
     */
    private function s3Transfer($backupFile)
    {
        $s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => get_setting('s3_region'),
            'credentials' => [
                'key'    => get_setting('s3_access_key'),
                'secret' => get_setting('s3_secret_key'),
            ],
        ]);

        // The internal adapter
        $adapter = new AwsS3V3Adapter(
            $s3Client,
            // Bucket name
            get_setting('s3_location')
        );

        $filesystem = new \League\Flysystem\Filesystem($adapter);

        try {
            return $this->transferFileToRemote($filesystem, $backupFile);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Transfer the backup file using SFTP protocol.
     *
     * @param string $backupFile the backup file path
     *
     * @throws Exception if an error occurs during the transfer
     *
     * @return bool true if transfer is successful, false otherwise
     */
    private function sftpTransfer($backupFile)
    {
        $sftpAdapter = new SftpAdapter(
            new SftpConnectionProvider(
                get_setting('sftp_server'),
                get_setting('sftp_user'),
                get_setting('sftp_password'),
                null,
                null,
                (int) get_setting('sftp_port') ?? 22
            ),
            get_setting('sftp_path')
        );

        $filesystem = new Filesystem($sftpAdapter);

        try {
            return $this->transferFileToRemote($filesystem, $backupFile);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Transfer the backup file using email (SMTP) protocol.
     *
     * @param string $filePath the file path to be attached in the email
     * @param mixed  $backup   the backup data or parameters
     *
     * @throws Exception if an error occurs during the email transfer
     *
     * @return bool true if the email transfer is successful, false otherwise
     */
    private function emailTransfer($filePath, $backup)
    {
        if (get_setting('email_protocol') && 'smtp' == get_setting('email_protocol')) {
            $staffEmail = get_setting('email_address');

            if (!$staffEmail) {
                throw new Exception('Email address is not provided');
            }

            $fileName = pathinfo($filePath, \PATHINFO_FILENAME);
            $file     = ['attachments' => [['file_path' => $filePath], ['file_name' => $fileName]]];

            $templateMessage = app_lang('email_message_1').$backup->backup_type.app_lang('email_message_2').$backup->datecreated.app_lang('email_message_3');
            $subject         = app_lang('new_backup_available').$backup->backup_name;

            if (send_app_mail($staffEmail, $subject, $templateMessage, $file)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Write the backup file to the remote storage.
     *
     * @param Filesystem $filesystem the Flysystem filesystem
     * @param string     $file       the backup file path
     *
     * @throws Exception if an error occurs during the write operation
     *
     * @return bool true if the write is successful, false otherwise
     */
    private function transferFileToRemote($filesystem, $file)
    {
        try {
            $fileName       = basename($file);
            $parts          = explode('/', $file);
            $directory      = $parts[\count($parts) - 2];
            $remoteLocation = $directory.'/'.$fileName;

            if (false !== strpos($file, '.sql')) {
                $filesystem->write($remoteLocation, file_get_contents($file));
            } else {
                $filesystem->writeStream($remoteLocation, fopen($file, 'r+'));
            }

            return true;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
