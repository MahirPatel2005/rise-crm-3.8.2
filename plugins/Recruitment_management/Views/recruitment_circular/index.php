<div class="no-border clearfix mb0">
    <div class="mt15">
        <div class="row">
            <div class="col-md-4">
                <div id="recruitment_circular_templates-list-box" class="card">
                    <div class="page-title clearfix">
                        <h4> <?php echo app_lang('recruitment_circular_templates'); ?></h4>
                        <div class="title-button-group">
                            <?php echo modal_anchor(get_uri("recruitment_circular_templates/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_template'), array("class" => "btn btn-default", "title" => app_lang('recruitment_add_job_circular_template'))); ?>
                        </div>
                    </div>
                    <div class="table-responsiv">
                        <table id="recruitment_circular_template-table" class="display clickable no-thead b-b-only" cellspacing="0" width="100%">            
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div id="recruitment_circular_templates-details-section"> 
                    <div id="empty-recruitment_circular_templates" class="text-center p15 box card " style="min-height: 150px;">
                        <div class="box-content" style="vertical-align: middle; height: 100%"> 
                            <div><?php echo app_lang("select_a_template"); ?></div>
                            <span data-feather="code" width="6rem" height="6rem" style="color:rgba(128, 128, 128, 0.1)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#recruitment_circular_template-table").appTable({
            source: '<?php echo_uri("recruitment_circular_templates/list_data") ?>',
            columns: [
                {title: '<?php echo app_lang("name"); ?>'},
                {title: '', class: 'text-center option w125'}
            ],
            hideTools: true,
            onInitComplete: function () {
                var $recruitment_circular_templates_list = $("#recruitment_circular_templates-list-box"),
                        $empty_recruitment_circular_templates = $("#empty-recruitment_circular_templates");
                if ($empty_recruitment_circular_templates.length && $recruitment_circular_templates_list.length) {
                    $empty_recruitment_circular_templates.height($recruitment_circular_templates_list.height() - 30);
                }
            },
            displayLength: 1000
        });

        /*load a message details*/
        $("body").on("click", "tr", function () {
            //don't load this message if already has selected.
            if (!$(this).hasClass("active")) {
                var recruitment_circular_templates_id = $(this).find(".recruitment_circular_template-row").attr("data-id");
                if (recruitment_circular_templates_id) {
                    appLoader.show();
                    $("tr.active").removeClass("active");
                    $(this).addClass("active");
                    $.ajax({
                        url: "<?php echo get_uri("recruitment_circular_templates/form"); ?>/" + recruitment_circular_templates_id,
                        success: function (result) {
                            appLoader.hide();
                            $("#recruitment_circular_templates-details-section").html(result);
                        }
                    });
                }
            }
        });
    });
</script>