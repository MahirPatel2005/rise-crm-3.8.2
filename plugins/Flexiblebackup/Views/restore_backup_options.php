<?php echo form_open(get_uri('flexiblebackup/restore_backup_files'), ['id' => 'restore_backup_form', 'class' => 'general-form', 'role' => 'form']); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <label for="restore_files"><?php echo app_lang('choose_component_to_restore'); ?></label>
        <input type="hidden" name="backup_id" value="<?php echo $backup->id; ?>">
        <?php foreach ($file_types as $file_type) { ?>
            <div class="checkbox">
                <input type="checkbox" class="form-check-input" name="restore_files[]" id="restore_files_<?php echo $file_type; ?>" value="<?php echo $file_type; ?>">
                <label for="restore_files_<?php echo $file_type; ?>"><?php echo ucfirst($file_type); ?></label>
            </div>
        <?php } ?>
        <br/>
        <p class="text-info">
            <?php echo app_lang('restore_warning'); ?>
        </p>
        <?php if ('database' == $backup->backup_type) { ?>
        <p class="bold text-danger"><?php echo app_lang('restore_db_warning'); ?></p>
        <?php } ?>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary" id="restore_backup_button"><?php echo app_lang('restore'); ?></button>
</div>
<?php echo form_close(); ?>
<script>
    window.restoreBackupForm =$("#restore_backup_form").appForm({
        isModal: true,
        onSubmit: function () {
            $("#restore_backup_form").find('[type="submit"]').attr('disabled', 'disabled');
        },
        onSuccess: function (result) {
            appAlert.success(result.message, {duration: 5000});
            $('#existing_backup_table').appTable({reload:true});
            <?php if ('database' == $backup->backup_type) { ?>
            window.location.href = '<?php echo get_uri('signin/authenticate'); ?>';
            <?php } ?>
        },
        onError: function(result) {
            appAlert.error(result.message, {duration: 5000});
            window.restoreBackupForm.closeModal();
        }
    });
</script>
