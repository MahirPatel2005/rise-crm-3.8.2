<div class="card">
    <div class="table-responsive">
        <table id="recruitment-job-type-table" class="display" cellspacing="0" width="100%">            
        </table>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#recruitment-job-type-table").appTable({
            source: '<?php echo_uri("recruitment_settings/job_type_list_data") ?>',
            columns: [
                {title: '<?php echo app_lang("name"); ?>'},
                {title: '<?php echo app_lang("description"); ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });

    });
</script>
