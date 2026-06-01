<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="pt-4 px-4">
            <div class="alert alert-danger">
                <span class="fs-4 fw-bold"><?= app_lang('notice') ?> !</span> <br>
                <span><?= app_lang('clear_log_notice') ?></span>
            </div>
            <div class="filter-section-flex-row mt-4">
                <div class="filter-section-left">
                    <span class="fs-4 fw-bold"><?php echo app_lang('failed_login_attempts_logs') ?></span>
                </div>
                <div class="filter-section-right">
                    <a href="javascript:void(0)" class="btn btn-danger clear_log float-end"><?php echo app_lang('clear_log') ?></a>
                </div>
            </div>
        </div>
        <hr>
        <div class="table-responsive">
            <table id="riseguardlog-table" class="display" cellspacing="0" width="100%">
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";
    $(document).ready(function () {
        $("#riseguardlog-table").appTable({
            source: '<?php echo_uri('riseguard/riseguardlog_table'); ?>',
            columns: [
                {title: '<?php echo app_lang('#'); ?>'},
                {title: '<?php echo app_lang('email_address'); ?>'},
                {title: '<?php echo app_lang('last_failed_attempt'); ?>'},
                {title: '<?php echo app_lang('view_ip'); ?>'},
                {title: '<?php echo app_lang('failed_attempts_count'); ?>'},
                {title: '<?php echo app_lang('lockouts_count'); ?>'},
                {title: '<?php echo app_lang('country'); ?>'},
                {title: '<?php echo app_lang('country_code'); ?>'},
                {title: '<?php echo app_lang('isp'); ?>'},
                {title: '<?php echo app_lang('is_mobile'); ?>'}
            ],
            printColumns: [0, 1, 2, 3],
            xlsColumns: [0, 1, 2, 3]
        });
    });

    $('body').on('click', '.clear_log', function (event) {
        $.ajax({
            url: '<?php echo_uri('riseguard/riseguard_clear_log'); ?>',
            type: 'POST',
            dataType: 'json'
        }).done(function (res) {
            appAlert.success(res.message, {duration: 3000});
            $('#riseguardlog-table').DataTable().ajax.reload();
        });
    });
</script>