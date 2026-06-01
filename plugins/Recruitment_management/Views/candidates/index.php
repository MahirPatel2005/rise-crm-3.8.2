<?php recruitment_load_css(array(PLUGIN_URL_PATH . "Recruitment_management/assets/css/recruitment-style.css")); ?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('recruitment_candidates'); ?></h1>
        </div>
        <div class="table-responsive">
            <table id="recruitment-candidates-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#recruitment-candidates-table").appTable({
            source: '<?php echo_uri("recruitment_candidates/list_data/") ?>',
            order: [[1, "asc"]],
            filterDropdown: [{name: "hiring_stage", class: "w150", options: <?php echo $hiring_stage_dropdown; ?>}, {name: "circular_id", class: "w200", options: <?php echo $circular_dropdown; ?>}],
            columns: [
                {title: "<?php echo app_lang("name") ?>"},
                {title: "<?php echo app_lang("recruitment_applied_job") ?>", "class": "w20p"},
                {title: "<?php echo app_lang("recruitment_hiring_stage") ?>", "class": "w20p"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2],
            xlsColumns: [0, 1, 2]
        });
    });
</script>    
