<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

$lang['flexiblebackup']                          = 'Flexiblebackup';
$lang['existing_backups']                        = 'Stored Backups';
$lang['next_scheduled_backup']                   = 'Upcoming Backup';
$lang['backup_now']                              = 'Backup Now';
$lang['backup_pro']                              = 'Backup Pro';
$lang['backup_date']                             = 'Backup Date';
$lang['click_on_download']                       = 'Download Backup Copy';
$lang['uploaded_to_remote_storage']              = 'Uploaded to remote storage';
$lang['upload_to_remote']                        = 'Upload to remote';
$lang['restore'] 				                            = 'Restore';
$lang['database'] 					                          = 'Database';
$lang['nothing_currently_scheduled']             = 'Nothing currently scheduled';
$lang['time_now'] 					                          = 'Now O\'Clock';
$lang['include_database_in_the_backup']          = 'Include your database in the backup';
$lang['include_file_in_the_backup']              = 'Include your files in the backup';
$lang['app']                                     = 'App';
$lang['plugins']                                 = 'Plugins';
$lang['assets']                                  = 'Assets';
$lang['system']                                  = 'System';
$lang['install'] 						                          = 'Install';
$lang['documentation'] 					                     = 'Documentation';
$lang['writable'] 						                         = 'Writable';
$lang['other'] 							                           = 'Other';
$lang['files_backup_schedule'] 			               = 'Files backup schedule: (This setting is depended on cron job)';
$lang['database_backup_schedule'] 		             = 'Database backup schedule: (This setting is depended on cron job)';
$lang['backup_name_prefix'] 			                  = 'Backup FileName Prefix';
$lang['choose_your_remote_storage']              = 'Choose your remote storage (tap on an icon to select or unselect)';

// Settings options
$lang['type_manual'] 					                  = 'Manual';
$lang['type_every_two_hours'] 			           = 'Every two hours';
$lang['type_every_four_hours'] 			          = 'Every four hours';
$lang['type_every_eight_hours'] 		          = 'Every eight hours';
$lang['type_every_twelve_hours'] 		         = 'Every twelve hours';
$lang['type_daily'] 					                   = 'Daily';
$lang['type_weekly'] 					                  = 'Weekly';
$lang['type_fortnightly'] 				              = 'Fortnightly';
$lang['type_monthly'] 					                 = 'Monthly';

// Remote storage options
$lang['ftp_storage'] 					           = 'FTP';
$lang['s3_storage']  					           = 'Amazon S3';
$lang['sftp_storage'] 					          = 'SFTP/SCP';
$lang['webdav_storage'] 				         = 'WebDAV';
$lang['email'] 							               = 'Email';

$lang['s3_description'] 				         = 'Obtain your access key and secret key from your AWS console, and then choose a globally unique name, consisting of letters and numbers, for your Amazon S3 bucket.';
$lang['save_changes'] 					          = 'Save Changes';
$lang['email_notes']							          = 'Please be aware that mail servers often have size limits, typically around 10-20 MB. Backups exceeding these limits may not be delivered';

$lang['ftp_server'] 					                        = 'FTP Server';
$lang['ftp_user'] 						                         = 'FTP User/Login';
$lang['ftp_password'] 				 	                     = 'FTP Password';
$lang['ftp_path'] 						                         = 'FTP Path (Needs to be exists and writable)';
$lang['sftp_server'] 					                       = 'SFTP Server';
$lang['sftp_user'] 						                        = 'SFTP User/Login';
$lang['sftp_password'] 					                     = 'SFTP Password';
$lang['sftp_path'] 						                        = 'SFTP Path (Needs to be exists and writable)';
$lang['s3_access_key'] 					                     = 'Amazon S3 Access Key';
$lang['s3_secret_key'] 					                     = 'Amazon S3 Secret Key';
$lang['s3_location'] 					                       = 'Amazon S3 Bucket Name';
$lang['s3_region'] 						                        = 'Amazon S3 Region e.g us-east-1';
$lang['webdav_base_uri'] 				                    = 'WebDAV Base URI';
$lang['webdav_username'] 				 		                 = 'WebDAV Username';
$lang['webdav_password'] 				 		                 = 'WebDAV Password';
$lang['email_address'] 					 		                  = 'Email Address';
$lang['include_in_files_backup'] 		 		           = 'Include in files backup:';
$lang['view_log'] 						 		                      = 'View Log';
$lang['restore_files_from'] 			 		               = 'Recover Files from';
$lang['choose_component_to_restore'] 	 		        = 'Choose the components to restore:';
$lang['restore_warning'] 				 		                 = 'Restoration will overwrite the contents of this site\'s App, Assets, Documentation, Files, Install, Plugins, System, Writable, Database, and any other directories based on the backup set and your selected options.';
$lang['restore_db_warning'] 			 		               = '<b>Backup the Current Database (Optional, but recommended): </b> Before proceeding with the database restore, it\'s a good practice to backup your current database in case you need to revert any changes.';
$lang['log_file'] 						 		                      = 'Log File';
$lang['download_log_file']			  	 		              = 'Download Log File';
$lang['backup_restored_successfully']    		      = 'Backup has been restored successfully';
$lang['backup_file_not_found']           		      = 'Backup file not found';
$lang['file_ready_actions']              		      = 'File Ready Actions : ';
$lang['download_to_your_computer']       		      = 'Download to your computer';
$lang['delete_from_your_webserver']      		      = 'Delete from your server';
$lang['browse_contents']				 		                  = 'Browse Contents';
$lang['database_restoration_failed']     		      = 'Database restoration failed';
$lang['database_has_been_restored_successfully'] = 'Database has been restored successfully';
$lang['backup_not_found']						                  = 'Backup not found';
$lang['backup_folder_not_found']				             = 'Backup folder not found';
$lang['email_message_1']						                   = 'Hi there! <br /><br /> Please find attached a copy of your ';
$lang['email_message_2']						                   = ' created on ';
$lang['email_message_3']						                   = '.  <br /><br /> Regards.';
$lang['new_backup_available']					               = 'New Backup Available ';
$lang['include_in_database_backup'] 			          = 'Include database backups in your auto backup process';
$lang['set_time_for_scheduled_backup']           = 'Set time for scheduled backup  (This setting is depended on cron job)';
$lang['auto_backup_to_remote_enabled']			        = 'Automatically upload Scheduled Backup to remote storage';
$lang['file_uploaded_succesfully']			            = 'File uploaded successfully';
$lang['please_select_files']					                = 'Please select which files you would like to add to the backup.';
$lang['please_select_backup_type'] 				          = 'Please select backup type';
$lang['backup_successfully']					                = 'Backup has been successfully done';
$lang['least_one_file_type_to_restore']	  		     = 'Please select at least one file type to restore.';
$lang['settings_updated_successfully']			        = 'Settings updated successfully';
$lang['verify_your_remote_settings']  			        = 'It seems your remote settings are incorrect, Kindly verify it <b>here</b>';
$lang['database_and_files_backup']				           = 'Database & Files Backup';
$lang['backup']									                         = 'Backup';
$lang['database_backup']						                   = 'Database Backup';
$lang['files_backup']							                     = 'Files Backup';
$lang['google_drive']							                     = 'Google Drive';
$lang['inherit_default_credentials']             = 'Inherit credentials from RISE CRM settings';
$lang['sftp_port']								                       = 'SFTP Port';
$lang['ftp_port']								                        = 'FTP Port';
$lang['delete_this_file']						                  = 'Delete This File';
$lang['please_verify_your_email_settings'] 		    = 'Verify your email settings';
$lang['no_longer_exists']                        ='The file is no longer exists';
$lang['email_storage'] 							                   = 'Email';
$lang['selected_remote_storage']				             = 'Your selected remote storage';

return $lang;
