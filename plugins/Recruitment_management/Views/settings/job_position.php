<div class="card">
    <div class="table-responsive">
        <table id="recruitment-job-position-table" class="display" cellspacing="0" width="100%">            
        </table>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#recruitment-job-position-table").appTable({
            source: '<?php echo_uri("recruitment_settings/job_position_list_data") ?>',
            columns: [
                {title: '<?php echo app_lang("name"); ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });

    });
</script>
