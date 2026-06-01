<?php

defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: AWS S3 Integration
  Description: Upload all files to AWS S3.
  Version: 1.0.1
  Requires at least: 3.3
  Author: ClassicCompiler
  Author URL: https://codecanyon.net/user/classiccompiler/portfolio
 */

app_hooks()->add_filter('app_filter_integration_settings_tab', function ($hook_tabs) {
    $hook_tabs[] = array(
        "title" => "AWS S3",
        "url" => get_uri("aws_s3_integration_settings"),
        "target" => "aws_s3-integration"
    );

    return $hook_tabs;
});

//install dependencies
register_installation_hook("AWS_S3_Integration", function ($item_purchase_code) {
    include PLUGINPATH . "AWS_S3_Integration/install/do_install.php";
});

//add setting link to the plugin setting
app_hooks()->add_filter('app_filter_action_links_of_AWS_S3_Integration', function ($action_links_array) {
    $action_links_array = array(
        anchor(get_uri("settings/integration"), app_lang("settings"))
    );

    return $action_links_array;
});

//update plugin
use AWS_S3_Integration\Controllers\AWS_S3_Integration_Updates;

register_update_hook("AWS_S3_Integration", function () {
    $update = new AWS_S3_Integration_Updates();
    return $update->index();
});

//uninstallation: remove data from database
register_uninstallation_hook("AWS_S3_Integration", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "aws_s3_integration_settings`;";
    $db->query($sql_query);
});

use AWS_S3_Integration\Libraries\AWS_S3_Integration;

//upload file to temp
app_hooks()->add_action('app_hook_upload_file_to_temp', function ($data) {
    if (!(get_aws_s3_integration_setting("integrate_aws_s3") && get_aws_s3_integration_setting('aws_s3_authorized'))) {
        return false;
    }

    $temp_file = get_array_value($data, "temp_file");
    $file_name = get_array_value($data, "file_name");

    $AWS_S3_Integration = new AWS_S3_Integration();
    $AWS_S3_Integration->upload_file($temp_file, $file_name, "temp");
});

//move temp file
app_hooks()->add_filter('app_filter_move_temp_file', function ($data) {
    if (!(get_aws_s3_integration_setting("integrate_aws_s3") && get_aws_s3_integration_setting('aws_s3_authorized'))) {
        return $data;
    }

    $related_to = get_array_value($data, "related_to");
    $file_name = get_array_value($data, "file_name");
    $new_filename = get_array_value($data, "new_filename");
    $file_content = get_array_value($data, "file_content");
    $source_path = get_array_value($data, "source_path");
    $target_path = get_array_value($data, "target_path");

    $AWS_S3_Integration = new AWS_S3_Integration();

    if ($file_name == "avatar.png" || $file_name == "site-logo.png" || $file_name == "invoice-logo.png" || $file_name == "estimate-logo.png" || $file_name == "order-logo.png" || $file_name == "favicon.png" || $related_to == "imap_ticket" || $related_to == "pasted_image" || $file_content) {
        //directly upload to the main directory
        $files_data = $AWS_S3_Integration->upload_file($source_path, $new_filename, get_drive_folder_name($target_path), $file_content);
    } else {
        $files_data = $AWS_S3_Integration->move_temp_file($file_name, $new_filename, get_drive_folder_name($target_path));
    }

    return $files_data;
});

//get source url
app_hooks()->add_filter('app_filter_get_source_url_of_file', function ($data) {
    if (!get_aws_s3_integration_setting('aws_s3_authorized')) {
        return $data;
    }

    $file_info = get_array_value($data, "file_info");

    $AWS_S3_Integration = new AWS_S3_Integration();
    $source_url = $AWS_S3_Integration->get_source_url_of_file(get_array_value($file_info, "file_id"));
    return $source_url ? (string) $source_url : "";
});

//get file content for download
app_hooks()->add_filter('app_filter_get_file_content', function ($data) {
    if (!(get_aws_s3_integration_setting("integrate_aws_s3") && get_aws_s3_integration_setting('aws_s3_authorized'))) {
        return $data;
    }

    $file_info = get_array_value($data, "file_info");

    $AWS_S3_Integration = new AWS_S3_Integration();
    return $AWS_S3_Integration->get_file_content(get_array_value($file_info, "file_id"));
});

//delete file
app_hooks()->add_action('app_hook_delete_app_file', function ($file_info) {
    if (!(get_aws_s3_integration_setting("integrate_aws_s3") && get_aws_s3_integration_setting('aws_s3_authorized'))) {
        return false;
    }

    $aws_file_key = get_array_value($file_info, "file_id");

    $AWS_S3_Integration = new AWS_S3_Integration();
    $AWS_S3_Integration->delete_file($aws_file_key);
});
