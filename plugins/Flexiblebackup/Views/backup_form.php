<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_open(get_uri('flexiblebackup/perform_backup'), ['id' => 'backupForm', 'class' => 'general-form', 'role' => 'form']); ?>
        <div class="row">
            <div class="col-sm-4 col-lg-3"></div>
            <div class="col-sm-4 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4><?php echo $title; ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="checkbox mt-2">
                            <input type="checkbox" value="1" class="form-check-input" id="includeDatabase" name="include_database_in_the_backup">
                            <strong><?php echo app_lang('database_backup'); ?></strong>
                        </div>
                        <div class="checkbox mt-2">
                            <input type="checkbox" value="1" class="form-check-input" id="includeFile" name="include_file_in_the_backup">
                            <strong><?php echo app_lang('files_backup'); ?></strong>
                        </div>
                        <div class="ml20 backup-options hide">
                            <?php echo view('Flexiblebackup\Views\backup_files_options'); ?>
                        </div>
                    </div>
                    <div class="card-footer d-grid d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary" id="backupButton"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('backup'); ?></button>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg-3"></div>
        </div>
    <?php echo form_close(); ?>
</div>
<script>
    $(function() {
        $('#includeFile').on('change', function () {
            if ($(this).is(':checked')) {
                $('.backup-options').removeClass('hide');
            } else {
                $('.backup-options').addClass('hide');
            }
        });

        // Initialize the form
        window.backupForm = $("#backupForm").appForm({
            isModal: false,
            onSubmit: function () {
                $("#backupButton").attr('disabled', 'disabled');
            },
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 4000});
                $('#existingBackupTable').appTable({reload:true});
                $('input[type="checkbox"]').prop("checked", false);
            },
            onError: function(result) {
                appAlert.error(result.message, {duration: 4000});
            }
        });
    });
</script>
