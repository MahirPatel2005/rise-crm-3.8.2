<div class="table-responsive">
    <table id="recruitment-application-form-table" class="display no-thead b-t b-b-only no-hover" cellspacing="0" width="100%">            
    </table>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#recruitment-application-form-table").appTable({
            source: '<?php echo_uri("recruitment_settings/application_form_list_data") ?>',
            order: [[0, "asc"]],
            hideTools: true,
            displayLength: 100,
            columns: [
                {visible: false},
                {title: '<?php echo app_lang("title"); ?>'},
                {title: '<?php echo app_lang("required"); ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });
    });
</script>
