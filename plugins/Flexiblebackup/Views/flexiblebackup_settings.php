<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="card mtop20">
            <div class="card-header">
                <h4><?php echo app_lang($title); ?></h4>
            </div>

            <?php
            if ('next_scheduled_backup' === $title) {
                echo view('Flexiblebackup\Views\next_scheduled_backup', $next_scheduled_backup);
            } elseif ('settings' === $title) {
                echo view('Flexiblebackup\Views\settings');
            } else {
                if (!empty(get_setting('remote_storage'))) {
                    echo '
                    <div class="alert alert-info" role="alert">
                        '.app_lang('selected_remote_storage').': <b>'.app_lang(get_setting('remote_storage').'_storage').'</b>
                    </div>';
                }
                echo '
                <div class="table-responsive">
                    <table id="existing_backup_table" class="display dataTable no-footer" cellspacing="0" width="100%">
                    </table>
                </div>';
            }
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        // Initialize the data table
        $("#existing_backup_table").appTable({
            source: '<?php echo_uri('flexiblebackup/list_backups'); ?>',
            columns: [
                {title: '<?php echo app_lang('date'); ?>', order_by: "datecreated"},
                {title: '<?php echo app_lang('click_on_download'); ?>',  "width": "220px"},
                {title: '<?php echo app_lang('uploaded_to_remote_storage'); ?>'},
                {title: '<?php echo app_lang('options'); ?>'},
            ],
            order: [[0, "desc"]],
        });

        // Handle the upload to remote button click
        $('body').on('click', '#upload_to_remote_btn', function(event) {
            var id = $(this).data('id');
            $.ajax({
                url: '<?php echo_uri('flexiblebackup/execute_remote_backup_upload'); ?>',
                type: 'post',
                data: { id: id },
                dataType: 'json',
                success: function (res) {
                    if (res.status == true) {
                        appAlert.success(res.message, { duration: 5000 });
                        $('#existing_backup_table').appTable({ reload: true });
                    } else {
                        appAlert.error(res.message, { duration: 6000 });
                    }
                }
            });
        });
    });
</script>
