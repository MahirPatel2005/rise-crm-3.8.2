<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix rounded">
            <h1><?php echo app_lang('recruitment_circulars'); ?></h1>
            <div class="title-button-group">
                <?php
                if ($login_user->is_admin) {
                    echo modal_anchor(get_uri("recruitment_management/job_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('recruitment_add_new_job'), array("class" => "btn btn-default", "title" => app_lang('recruitment_add_new_job'), "data-xs-modal" => 1));
                }
                ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="recruitment-job-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#recruitment-job-table").appTable({
            source: '<?php echo_uri("recruitment_management/job_list_data") ?>',
            order: [[0, 'desc']],
            columns: [
                {title: '<?php echo app_lang("title"); ?>', "class": "w20p"},
                {title: '<?php echo app_lang("start_date"); ?>', "class": "w10p"},
                {title: '<?php echo app_lang("deadline"); ?>', "class": "w10p"},
                {title: '<?php echo app_lang("recruitment_job_type"); ?>', "class": "w10p"},
                {title: '<?php echo app_lang("recruitment_job_position"); ?>', "class": "w10p"},
                {title: '<?php echo app_lang("recruitment_department"); ?>', "class": "w150"},
                {title: '<?php echo app_lang("quantity"); ?>'},
                {title: '<?php echo app_lang("status"); ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w10p"}
            ],
            printColumns: [0, 2, 1, 3, 4],
            xlsColumns: [0, 2, 1, 3, 4]
        });
    });
</script>