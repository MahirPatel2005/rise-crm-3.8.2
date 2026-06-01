<div class="table-responsive">
    <table id="recruitment-circular-candidates-table" class="display" cellspacing="0" width="100%">   
    </table>
</div> 
<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#recruitment-circular-candidates-table").appTable({
            source: '<?php echo_uri("recruitment_candidates/list_data/" . $circular_id) ?>',
            order: [[1, "asc"]],
            columns: [
                {title: "<?php echo app_lang("name") ?>"},
                {title: "<?php echo app_lang("recruitment_applied_job") ?>", "class": "w20p"},
                {title: "<?php echo app_lang("recruitment_hiring_stage") ?>", "class": "w20p"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });
    });
</script>