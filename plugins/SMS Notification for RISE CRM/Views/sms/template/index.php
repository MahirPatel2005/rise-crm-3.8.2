<div class="table-responsive">
    <table id="sms-templates-table" class="display" cellspacing="0" width="100%">            
    </table>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#sms-templates-table").appTable({
            source: '<?php echo_uri("sms/sms_template_list_data") ?>',
            columns: [
                {title: '<?php echo app_lang("sms_template_name"); ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w10p"}
            ],
            displayLength: 100
        });
    });
</script>