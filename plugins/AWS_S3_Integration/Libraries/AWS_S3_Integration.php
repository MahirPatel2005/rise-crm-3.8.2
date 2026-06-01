<?php

namespace AWS_S3_Integration\Libraries;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;
use Aws\Exception\MultipartUploadException;

class AWS_S3_Integration {

    private $AWS_S3_Integration_settings_model;

    public function __construct() {
        require_once(PLUGINPATH . "AWS_S3_Integration/ThirdParty/aws-sdk-php/vendor/autoload.php");
        $this->AWS_S3_Integration_settings_model = new \AWS_S3_Integration\Models\AWS_S3_Integration_settings_model();

        $this->access_key_id = get_aws_s3_integration_setting('access_key_id');
        $this->secret_access_key = get_aws_s3_integration_setting('secret_access_key');
        $this->region = get_aws_s3_integration_setting('region');
        $this->bucket_name = get_aws_s3_integration_setting('bucket_name');
    }

    private function get_s3_client() {
        $credentials = new Credentials($this->access_key_id, $this->secret_access_key);

        return new S3Client([
            'region' => $this->region,
            'version' => 'latest',
            'credentials' => $credentials
        ]);
    }

    //authorize connection
    public function authorize() {
        //create a bucket to check a valid credential
        try {
            $s3Client = $this->get_s3_client();
            $bucket_name = get_aws_s3_integration_setting("bucket_name");

            //first check if the bucket is existing
            $buckets = $s3Client->listBuckets();
            $buckets = $buckets['Buckets'];
            if (!in_array($bucket_name, array_column($buckets, 'Name'))) {
                //the bucket isn't exists, create new one
                $s3Client->createBucket([
                    'Bucket' => $bucket_name,
                ]);
            }

            $this->AWS_S3_Integration_settings_model->save_setting('bucket_name', $bucket_name);
            $this->AWS_S3_Integration_settings_model->save_setting('aws_s3_authorized', "1");

            return "authorized";
        } catch (AwsException $e) {
            return $e->getAwsErrorMessage();
        }
    }

    //upload file to temp folder
    public function upload_file($temp_file, $file_name, $folder_name = "", $file_content = "") {
        if ($file_content) { //file contents
            $content = $file_content;
            $finfo = new \finfo(FILEINFO_MIME);
            $mime_type = $finfo->buffer($file_content);
        } else { //file path
            $content = file_get_contents($temp_file);
            $mime_type = mime_content_type($temp_file);
        }

        try {
            $s3Client = $this->get_s3_client();
            $aws_file_key = $folder_name . "/" . $file_name;

            $result = $s3Client->putObject([
                'Bucket' => $this->bucket_name,
                'Key' => $aws_file_key,
                'Body' => $content,
                'ContentType' => $mime_type,
            ]);
        } catch (MultipartUploadException $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            exit();
        }

        if ($folder_name !== "temp") {
            return array("file_name" => $file_name, "file_id" => $aws_file_key, "service_type" => "aws");
        }
    }

    //to move temp files to permanent directory we've to copy the file first
    //then delete old one since move feature isn't available in aws api
    public function move_temp_file($file_name, $new_filename, $folder_name) {
        try {
            $s3Client = $this->get_s3_client();

            //copy file first
            $aws_file_key = $folder_name . "/" . $new_filename;
            $result = $s3Client->copyObject([
                'Bucket' => $this->bucket_name,
                'Key' => $aws_file_key,
                'CopySource' => "$this->bucket_name/temp/$file_name",
            ]);

            //delete old temp file
            $this->delete_file("temp/$file_name");
        } catch (AwsException $ex) {
            log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
            exit();
        }

        return array("file_name" => $new_filename, "file_id" => $aws_file_key, "service_type" => "aws");
    }

    //delete a file
    public function delete_file($aws_file_key = "") {
        if (!$aws_file_key) {
            return false;
        }

        try {
            $s3Client = $this->get_s3_client();
            $s3Client->deleteObject([
                'Bucket' => $this->bucket_name,
                'Key' => $aws_file_key,
            ]);

            return true;
        } catch (AwsException $ex) {
            log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
            return false;
        }
    }

    //get source url of a file
    public function get_source_url_of_file($aws_file_key = "") {
        if (!$aws_file_key) {
            return false;
        }

        try {
            $s3Client = $this->get_s3_client();
            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => $this->bucket_name,
                'Key' => $aws_file_key
            ]);

            $request = $s3Client->createPresignedRequest($cmd, get_aws_s3_integration_setting("signed_url_validity"));

            return $request->getUri();
        } catch (AwsException $ex) {
            log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
            return false;
        }
    }

    //get content of a file
    public function get_file_content($aws_file_key = "") {
        if (!$aws_file_key) {
            return false;
        }

        try {
            return file_get_contents($this->get_source_url_of_file($aws_file_key));
        } catch (\Exception $ex) {
            log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
            return false;
        }
    }

}
