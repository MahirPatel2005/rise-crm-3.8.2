<?php echo form_open(get_uri('flexiblebackup/download_backup_file/'.$backupId.'/log'), ['id' => 'logForm']); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <div class="alert alert-info">
            <?php
                $logFile = file_exists($logFilePath) || die("Unable to open file!");
                $fileContentWithLineBreaks = nl2br(file_get_contents($logFilePath));
                echo $fileContentWithLineBreaks;
            ?>
        </div>
        <input type="hidden" id="flexiLogUrl" value="<?php echo get_uri('flexiblebackup/download_backup_file/'.$backupId.'/log'); ?>" />
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal">
        <span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?>
    </button>
    <button type="submit" class="btn btn-primary">
        <?php echo app_lang('download_log_file'); ?>
    </button>
</div>
<?php echo form_close(); ?>
