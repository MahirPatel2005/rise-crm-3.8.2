<div class="card">
    <div class="table-responsive">
        <table id="recruitment-event-type-table" class="display" cellspacing="0" width="100%">            
        </table>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#recruitment-event-type-table").appTable({
            source: '<?php echo_uri("recruitment_settings/event_type_list_data") ?>',
            columns: [
                {title: '<?php echo app_lang("name"); ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });

    });
</script>
