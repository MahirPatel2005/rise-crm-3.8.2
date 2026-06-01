<div class="modal-body clearfix">
    <div class="container-fluid">
        <a href="<?php echo get_uri('flexiblebackup/download_backup_file/'.$backupId.'/'.$type); ?>" type="button" class="btn btn-primary"><?php echo app_lang('download_to_your_computer'); ?></a>
        <?php echo js_anchor(app_lang('delete_this_file'), ['title' => app_lang('delete_this_file'), 'class' => 'delete btn btn-danger', 'data-id' => $backupId, 'data-action-url' => get_uri('flexiblebackup/remove_backup_directory/'.$backupId.'/'.$type), 'data-action' => 'delete-confirmation', 'id' => 'deleteBackupFile']); ?>
        <?php if ('database' != $type) { ?>
            <a href="javascript:void(0)" data-id="<?php echo $backupId; ?>" data-type="<?php echo $type; ?>" type="button" id="browseContents" class="btn btn-secondary"><?php echo app_lang('browse_contents'); ?></a>
            <div class="hide row" id="folderTree" style="margin-top: 20px;">
                <hr>
                <div class="col-md-9">
                    <?php echo 'Browsing ZIP File : '.$type; ?> (<?php echo date('M dS, Y, g:i a', strtotime($backup->datecreated)); ?>)
                </div>
                <div style="text-align: right;" class="col-md-3">
                    <button type="button" class="btn-close" id="closeTree"></button>
                </div>
                <div id="fileTree">
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php echo app_lang('close'); ?></button>
</div>

<script type="text/javascript">

$(function () {
    var tree = new Tree(document.getElementById('fileTree'));
    tree.json(<?php echo json_encode($files); ?>);

    $('#folderTree').addClass('hide');
    $(document).on('click', '#browseContents', function(event) {
        $('#folderTree').removeClass('hide');

    });

    $(document).on('click', '#closeTree', function(event) {
        $('#folderTree').addClass('hide');
    });

    $(document).on('click', '#confirmDeleteButton', function(event) {
        location.reload();
    });
});
</script>
