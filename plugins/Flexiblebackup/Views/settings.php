<div class="card-body">
    <div class="row">
        <div class="col-md-12">
            <?php echo form_open(get_uri('flexiblebackup/save_settings'), ['id' => 'settings_form', 'class' => 'general-form', 'role' => 'form']); ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="files_backup_schedule" class="control-label"><?php echo app_lang('files_backup_schedule'); ?></label>
                                <?php
                                    echo form_dropdown('files_backup_schedule', getBackupSchedulesTypes(), get_setting('files_backup_schedule') ?? 1, "class='select2 validate-hidden form-control' id='backup_schedule_options' data-rule-required='true', data-msg-required='".app_lang('field_required')."'");
                                ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="database_backup_schedule" class="control-label"><?php echo app_lang('database_backup_schedule'); ?></label>
                                <?php
                                    echo form_dropdown('database_backup_schedule', getBackupSchedulesTypes(), get_setting('database_backup_schedule') ?? 1, "class='select2 validate-hidden form-control' id='database_backup_schedule_options' data-rule-required='true', data-msg-required='".app_lang('field_required')."'");
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="backup_name_prefix" class="control-label"><?php echo app_lang('backup_name_prefix'); ?></label>
                                <div class="">
                                    <?php
                                        echo form_input([
                                            'id'                 => 'backup_name_prefix',
                                            'name'               => 'backup_name_prefix',
                                            'value'              => '' == get_setting('backup_name_prefix') ? 'backup' : get_setting('backup_name_prefix'),
                                            'class'              => 'form-control',
                                            'data-rule-required' => true,
                                            'data-msg-required'  => app_lang('field_required'),
                                        ]);
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="auto_backup_time" class="control-label"><?php echo app_lang('set_time_for_scheduled_backup'); ?></label>
                                <div class="">
                                    <?php
                                    $time_format_24_hours = '24_hours' == get_setting('time_format') ? true : false;
                                    $auto_backup_time     = is_date_exists(get_setting('auto_backup_time')) ? get_setting('auto_backup_time') : '';

                                    if ($time_format_24_hours) {
                                        $auto_backup_time = $auto_backup_time ? date('H:i', strtotime($auto_backup_time)) : '';
                                    } else {
                                        $auto_backup_time = $auto_backup_time ? convert_time_to_12hours_format(date('H:i:s', strtotime($auto_backup_time))) : '';
                                    }

                                    echo form_input([
                                        'id'    => 'auto_backup_time',
                                        'name'  => 'auto_backup_time',
                                        'value' => $auto_backup_time,
                                        'class' => 'form-control',
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php $default_remote_storage = get_setting('remote_storage'); ?>
                        <div class="form-group">
                            <label for="remote_storage" class="control-label"><?php echo app_lang('choose_your_remote_storage'); ?></label>
                            <select name="remote_storage" class="form-control" id="remote_storage">
                                <option value=""></option>
                                <?php foreach (fetchStorageOptions() as $option) { ?>
                                    <option value="<?php echo $option['key']; ?>" <?php echo $default_remote_storage == $option['key'] ? 'selected' : ''; ?>><?php echo app_lang($option['label']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group form-access-key-pairs">
                            <div class="email-container <?php echo ('email' == $default_remote_storage) ? '' : 'hide'; ?>" id="email">
                                <p class="text-primary"><?php echo app_lang('email_notes'); ?></p>
                                <div class="form-group">
                                    <span class="text-danger">*</span>
                                    <label for="email_address" class="control-label"><?php echo app_lang('email_address'); ?></label>
                                    <?php echo form_input(['id' => 'email_address', 'name' => 'email_address', 'value' => get_setting('email_address'),
                                        'class'                 => 'form-control',  'data-rule-required' => true, 'data-msg-required' => app_lang('field_required'), ]); ?>
                                </div>
                            </div>
                            <div class="ftp-container <?php echo ('ftp' == $default_remote_storage) ? '' : 'hide'; ?>" id="ftp">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <span class="text-danger">*</span>
                                        <label for="ftp_server" class="control-label"><?php echo app_lang('ftp_server'); ?></label>
                                        <?php echo form_input(['id' => 'ftp_server', 'name' => 'ftp_server', 'value' => get_setting('ftp_server'),
                                                'class'             => 'form-control', 'data-rule-required' => true, 'data-msg-required' => app_lang('field_required'), ]); ?>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <span class="text-danger">*</span>
                                        <label for="ftp_path" class="control-label"><?php echo app_lang('ftp_path'); ?></label>
                                        <?php echo form_input(['id' => 'ftp_path', 'name' => 'ftp_path', 'value' => get_setting('ftp_path'),
                                                'class'             => 'form-control', 'data-rule-required' => true, 'data-msg-required' => app_lang('field_required'), ]); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <span class="text-danger">*</span>
                                        <label for="ftp_user" class="control-label"><?php echo app_lang('ftp_user'); ?></label>
                                        <?php echo form_input(['id' => 'ftp_user', 'name' => 'ftp_user', 'value' => get_setting('ftp_user'),
                                        'class'                     => 'form-control', 'data-rule-required' => true, 'data-msg-required' => app_lang('field_required'), ]); ?>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <span class="text-danger">*</span>
                                        <label for="ftp_password" class="control-label"><?php echo app_lang('ftp_password'); ?></label>
                                        <?php echo form_input(['id' => 'ftp_password', 'name' => 'ftp_password', 'value' => get_setting('ftp_password'), 'class' => 'form-control', 'type' => 'password', 'data-rule-required' => true, 'data-msg-required' => app_lang('field_required')]); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <span class="text-danger">*</span>
                                        <label for="ftp_port" class="control-label"><?php echo app_lang('ftp_port'); ?></label>
                                        <?php echo form_input(['id' => 'ftp_port', 'name' => 'ftp_port', 'value' => get_setting('ftp_port'),
                                        'class'                     => 'form-control', 'data-rule-required' => true, 'data-msg-required' => app_lang('field_required'), 'type' => 'number', ]); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="sftp-container <?php echo ('sftp' == $default_remote_storage) ? '' : 'hide'; ?>" id="sftp">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <span class="text-danger">*</span>
                                        <label for="sftp_server" class="control-label"><?php echo app_lang('sftp_server'); ?></label>
                                        <?php echo form_input(['id' => 'sftp_server', 'name' => 'sftp_server', 'value' => get_setting('sftp_server'),
                                            'class'                 => 'form-control', 'data-rule-required' => true, 'data-msg-required' => app_lang('field_required'), ]); ?>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <span class="text-danger">*</span>
                                        <label for="sftp_path" class="control-label"><?php echo app_lang('sftp_path'); ?></label>
                                        <?php echo form_input(['id' => 'sftp_path', 'name' => 'sftp_path', 'value' => get_setting('sftp_path'),
                                            'class'                 => 'form-control', 'data-rule-required' => true, 'data-msg-required' => app_lang('field_required'), ]); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <span class="text-danger">*</span>
                                        <label for="sftp_user" class="control-label"><?php echo app_lang('sftp_user'); ?></label>
                                        <?php echo form_input(['id' => 'sftp_user', 'name' => 'sftp_user', 'value' => get_setting('sftp_user'),
                                            'class'                 => 'form-control', 'data-rule-required' => true, 'data-msg-required' => app_lang('field_required'), ]); ?>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <span class="text-danger">*</span>
                                        <label for="sftp_password" class="control-label"><?php echo app_lang('sftp_password'); ?></label>
                                        <?php echo form_input(['id' => 'sftp_password', 'name' => 'sftp_password', 'value' => get_setting('sftp_password'), 'class' => 'form-control', 'type' => 'password', 'data-rule-required' => true, 'data-msg-required' => app_lang('field_required')]); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <span class="text-danger">*</span>
                                        <label for="sftp_port" class="control-label"><?php echo app_lang('sftp_port'); ?></label>
                                        <?php echo form_input(['id' => 'sftp_port', 'name' => 'sftp_port', 'value' => get_setting('sftp_port'),
                                        'class'                     => 'form-control', 'data-rule-required' => true, 'data-msg-required' => app_lang('field_required'), 'type' => 'number', ]); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="s3-container <?php echo ('s3' == $default_remote_storage) ? '' : 'hide'; ?>" id="s3">
                                <p class="description text-primary mt-2"><?php echo app_lang('s3_description'); ?></p>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <span class="text-danger">*</span>
                                        <label for="s3_access_key" class="control-label"><?php echo app_lang('s3_access_key'); ?></label>
                                        <?php echo form_input(['id' => 's3_access_key', 'name' => 's3_access_key', 'value' => get_setting('s3_access_key'), 'class' => 'form-control', 'data-rule-required' => true, 'data-msg-required' => app_lang('field_required')]); ?>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <span class="text-danger">*</span>
                                        <label for="s3_secret_key" class="control-label"><?php echo app_lang('s3_secret_key'); ?></label>
                                        <?php echo form_input(['id' => 's3_secret_key', 'name' => 's3_secret_key', 'value' => get_setting('s3_secret_key'), 'class' => 'form-control', 'data-rule-required' => true, 'data-msg-required' => app_lang('field_required')]); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <span class="text-danger">*</span>
                                        <label for="s3_location" class="control-label"><?php echo app_lang('s3_location'); ?></label>
                                        <?php echo form_input(['id' => 's3_location', 'name' => 's3_location', 'value' => get_setting('s3_location'),
                                            'class'                 => 'form-control', 'data-rule-required' => true, 'data-msg-required' => app_lang('field_required'), ]); ?>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <span class="text-danger">*</span>
                                        <label for="s3_region" class="control-label"><?php echo app_lang('s3_region'); ?></label>
                                        <?php echo form_input(['id' => 's3_region', 'name' => 's3_region', 'value' => get_setting('s3_region'),
                                            'class'                 => 'form-control', 'data-rule-required' => true, 'data-msg-required' => app_lang('field_required'), ]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <div class="p-2 ml-4">
                                <div class="checkbox">
                                    <?php $include_in_database_backup = get_setting('include_in_database_backup') ?? 1; ?>
                                    <input type="checkbox" class="form-check-input" name="include_in_database_backup" id="include_in_database_backup" value="1" <?php echo (1 == $include_in_database_backup) ? 'checked' : ''; ?> />
                                    <label for="include_in_database_backup"><?php echo app_lang('include_in_database_backup'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="choose_your_remote_storage" class="control-label"><?php echo app_lang('include_in_files_backup'); ?></label>
                            <div class="p-2 ml-4">
                                <?php echo view('Flexiblebackup\Views\backup_files_options'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label for="type" class="control-label"><?php echo app_lang('auto_backup_to_remote_enabled'); ?></label>
                                <div class="col-md-12">
                                    <?php
                                    echo form_radio([
                                        'id'                => 'yes',
                                        'name'              => 'auto_backup_to_remote_enabled',
                                        'class'             => 'form-check-input auto_backup_to_remote_enabled',
                                        'data-msg-required' => app_lang('field_required'),
                                            ], 'yes', ('yes' === get_setting('auto_backup_to_remote_enabled')) ? true : false);
                                    ?>
                                    <label for="yes" class="mr15"><?php echo app_lang('yes'); ?></label>
                                    <?php
                                    echo form_radio([
                                        'id'                => 'no',
                                        'name'              => 'auto_backup_to_remote_enabled',
                                        'class'             => 'form-check-input auto_backup_to_remote_enabled',
                                        'data-msg-required' => app_lang('field_required'),
                                            ], 'no', ('no' === get_setting('auto_backup_to_remote_enabled')) ? true : (('yes' !== get_setting('auto_backup_to_remote_enabled')) ? true : false));
                                    ?>
                                    <label for="no" class=""><?php echo app_lang('no'); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div style="text-align: right;">
                    <button type="submit" class="btn btn-primary" >
                        <?php echo app_lang('save_changes'); ?>
                    </button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("#backup_schedule_options").select2();
    $("#database_backup_schedule_options").select2();

    setTimePicker("#auto_backup_time");

    $('#remote_storage').select2().on('change', function(event) {
        key = $(this) .val();
        $('.form-access-key-pairs > div').addClass('hide');
        const obj = $(`.${key}-container`);
        if($(obj).length){
            $('.form-access-key-pairs').removeClass('hide');
            $(obj).removeClass('hide');
        }
    });

    $('#settings_form').appForm({
        isModal:false,
        onSuccess : function() {
            appAlert.success('<?php echo app_lang('settings_updated_successfully'); ?>', {duration:5000});
        }
    });
</script>
