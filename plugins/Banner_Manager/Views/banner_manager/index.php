<?php banner_manager_load_css(array("assets/css/banner_manager_styles.css")); ?>
<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1> <?php echo app_lang('banner_manager'); ?></h1>
            <div class="title-button-group">
                <?php
                echo modal_anchor(get_uri("banner_manager/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('banner_manager_add_banner'), array("class" => "btn btn-default", "title" => app_lang('banner_manager_add_banner')));
                ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="banner-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#banner-table").appTable({
            source: '<?php echo_uri("banner_manager/list_data") ?>',
            order: [[3, 'desc']],
            columns: [
                {title: '<?php echo app_lang("title"); ?>', "class": "w300"},
                {title: '<?php echo app_lang("created_by"); ?>', "class": "w300"},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("start_date") ?>', "iDataSort": 2},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("end_date") ?>', "iDataSort": 4},
                {title: "<?php echo app_lang("banner_manager_banner") ?>", "class": "w100"},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
            ]
        });
    });
</script>