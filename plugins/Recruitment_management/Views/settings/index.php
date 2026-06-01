<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "recruitments";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <div class="no-border clearfix">

                <ul id="ticket-type-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
                    <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#recruitment-settings-tab"> <?php echo app_lang('recruitment_settings'); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_settings/application_form/"); ?>" data-bs-target="#recruitment-application-form-tab"><?php echo app_lang('recruitment_application_form'); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_settings/hiring_stage/"); ?>" data-bs-target="#recruitment-hiring-stage-tab"><?php echo app_lang('recruitment_hiring_stage'); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_settings/event_type/"); ?>" data-bs-target="#recruitment-event-type-tab"><?php echo app_lang('event_type'); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_settings/job_type/"); ?>" data-bs-target="#recruitment-job-type-tab"><?php echo app_lang('recruitment_job_type'); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_settings/job_position/"); ?>" data-bs-target="#recruitment-job-position-tab"><?php echo app_lang('recruitment_job_position'); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_settings/department/"); ?>" data-bs-target="#recruitment-department-tab"><?php echo app_lang('recruitment_department'); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_settings/location/"); ?>" data-bs-target="#recruitment-location-tab"><?php echo app_lang('location'); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_circular_templates"); ?>" data-bs-target="#recruitment-circular-templates"><?php echo app_lang("recruitment_circular_templates"); ?></a></li>
                    <div class="tab-title clearfix no-border">
                        <div class="title-button-group">
                            <?php echo modal_anchor(get_uri("recruitment_settings/hiring_stage_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('recruitment_add_hiring_stage'), array("class" => "btn btn-default", "title" => app_lang('recruitment_add_hiring_stage'), "id" => "recruitment-settings-button")); ?>
                        </div>
                    </div>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade" id="recruitment-settings-tab">
                        <div class="card no-border clearfix mb0">

                            <?php echo form_open(get_uri("recruitment_settings/save_recruitment_settings"), array("id" => "recruitment-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>

                            <div class="card-body">
                                <div class="form-group">
                                    <div class="row">
                                        <label for="recruitment_job_circular_color" class=" col-md-2"><?php echo app_lang('recruitment_job_circular_color'); ?></label>
                                        <div class=" col-md-10">
                                            <input type="color" id="recruitment_job_circular_color" name="recruitment_job_circular_color" value="<?php echo get_recruitment_setting("recruitment_job_circular_color"); ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <label for="default_recruitment_job_circular_template" class=" col-md-2"><?php echo app_lang('recruitment_default_job_circular_template'); ?></label>
                                        <div class=" col-md-10">
                                            <?php
                                            echo form_dropdown("default_recruitment_job_circular_template", $job_templates_dropdown, get_recruitment_setting("default_recruitment_job_circular_template"), "class='select2 mini' id='default_recruitment_job_circular_template'");
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><span data-feather='check-circle' class="icon-16"></span> <?php echo app_lang('save'); ?></button>
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade bg-white" id="recruitment-application-form-tab"></div>
                    <div role="tabpanel" class="tab-pane fade bg-white" id="recruitment-hiring-stage-tab"></div>
                    <div role="tabpanel" class="tab-pane fade bg-white" id="recruitment-event-type-tab"></div>
                    <div role="tabpanel" class="tab-pane fade bg-white" id="recruitment-job-type-tab"></div>
                    <div role="tabpanel" class="tab-pane fade bg-white" id="recruitment-job-position-tab"></div>
                    <div role="tabpanel" class="tab-pane fade bg-white" id="recruitment-department-tab"></div>
                    <div role="tabpanel" class="tab-pane fade bg-white" id="recruitment-location-tab"></div>
                    <div role="tabpanel" class="tab-pane fade" id="recruitment-circular-templates"></div>
                </div>

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#recruitment-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message, {duration: 10000});
                } else {
                    appAlert.error(result.message);
                }

                if (result.reload_page) {
                    location.reload();
                }
            }
        });

        $("#recruitment-settings-form .select2").select2();

        //change the add button attributes on changing tab panel
        var addButton = $("#recruitment-settings-button");
        $(".nav-tabs li").on("click", function () {
            var activeField = $(this).find("a").attr("data-bs-target");

            if (activeField === "#recruitment-event-type-tab") {
                addButton.attr("title", "<?php echo app_lang("recruitment_add_event_type"); ?>");
                addButton.attr("data-title", "<?php echo app_lang("recruitment_add_event_type"); ?>");
                addButton.attr("data-action-url", "<?php echo get_uri("recruitment_settings/event_type_modal_form"); ?>");

                addButton.html("<?php echo "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('recruitment_add_event_type'); ?>");
                feather.replace();
            } else if (activeField === "#recruitment-job-type-tab") {
                addButton.attr("title", "<?php echo app_lang("recruitment_add_job_type"); ?>");
                addButton.attr("data-title", "<?php echo app_lang("recruitment_add_job_type"); ?>");
                addButton.attr("data-action-url", "<?php echo get_uri("recruitment_settings/job_type_modal_form"); ?>");

                addButton.html("<?php echo "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('recruitment_add_job_type'); ?>");
                feather.replace();
            } else if (activeField === "#recruitment-job-position-tab") {
                addButton.attr("title", "<?php echo app_lang("recruitment_add_job_position"); ?>");
                addButton.attr("data-title", "<?php echo app_lang("recruitment_add_job_position"); ?>");
                addButton.attr("data-action-url", "<?php echo get_uri("recruitment_settings/job_position_modal_form"); ?>");

                addButton.html("<?php echo "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('recruitment_add_job_position'); ?>");
                feather.replace();
            } else if (activeField === "#recruitment-department-tab") {
                addButton.attr("title", "<?php echo app_lang("recruitment_add_department"); ?>");
                addButton.attr("data-title", "<?php echo app_lang("recruitment_add_department"); ?>");
                addButton.attr("data-action-url", "<?php echo get_uri("recruitment_settings/department_modal_form"); ?>");

                addButton.html("<?php echo "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('recruitment_add_department'); ?>");
                feather.replace();
            } else if (activeField === "#recruitment-location-tab") {
                addButton.attr("title", "<?php echo app_lang("recruitment_add_location"); ?>");
                addButton.attr("data-title", "<?php echo app_lang("recruitment_add_location"); ?>");
                addButton.attr("data-action-url", "<?php echo get_uri("recruitment_settings/location_modal_form"); ?>");

                addButton.html("<?php echo "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('recruitment_add_location'); ?>");
                feather.replace();
            } else if (activeField === "#recruitment-hiring-stage-tab") {
                addButton.attr("title", "<?php echo app_lang("recruitment_add_hiring_stage"); ?>");
                addButton.attr("data-title", "<?php echo app_lang("recruitment_add_hiring_stage"); ?>");
                addButton.attr("data-action-url", "<?php echo get_uri("recruitment_settings/hiring_stage_modal_form"); ?>");

                addButton.html("<?php echo "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('recruitment_add_hiring_stage'); ?>");
                feather.replace();
            }
        });

    });
</script>
