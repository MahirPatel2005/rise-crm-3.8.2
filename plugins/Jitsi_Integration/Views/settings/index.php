<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "jitsi_integration";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("jitsi_integration_settings/save"), array("id" => "jitsi_integration-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
            <div class="card">
                <div class="page-title clearfix">
                    <h4> <?php echo app_lang('jitsi_integration'); ?></h4>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <label for="integrate_jitsi" class=" col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('jitsi_integration_enable_jitsi_meetings'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("integrate_jitsi", "1", get_jitsi_integration_setting("integrate_jitsi") ? true : false, "id='integrate_jitsi' class='form-check-input'");
                                ?> 
                            </div>
                        </div>
                    </div>

                    <div class="clearfix integrate-with-jitsi-details-section <?php echo get_jitsi_integration_setting("integrate_jitsi") ? "" : "hide" ?>">

                        <div class="form-group">
                            <div class="row">
                                <label for="jitsi_integration_users" class=" col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('jitsi_integration_who_can_manage_meetings'); ?> <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('jitsi_integration_users_help_message'); ?>"><i data-feather='help-circle' class="icon-16"></i></span></label>
                                <div class="col-md-10 col-xs-4 col-sm-8">
                                    <?php
                                    echo form_input(array(
                                        "id" => "jitsi_integration_users",
                                        "name" => "jitsi_integration_users",
                                        "value" => get_jitsi_integration_setting("jitsi_integration_users"),
                                        "class" => "form-control",
                                        "placeholder" => app_lang('team_members')
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="client_can_access_meetings" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('jitsi_integration_client_can_access_meetings'); ?></label>
                                <div class="col-md-10 col-xs-4 col-sm-8">
                                    <?php
                                    echo form_checkbox("client_can_access_meetings", "1", get_jitsi_integration_setting("client_can_access_meetings") ? true : false, "id='client_can_access_meetings' class='form-check-input'");
                                    ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><span data-feather='check-circle' class="icon-16"></span> <?php echo app_lang('save'); ?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#jitsi_integration-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });

        //show/hide details area
        var $detailsArea = $(".integrate-with-jitsi-details-section");
        $("#integrate_jitsi").on('click', function () {
            if ($(this).is(":checked")) {
                $detailsArea.removeClass("hide");
            } else {
                $detailsArea.addClass("hide");
            }
        });

        $("#jitsi_integration_users").select2({
            multiple: true,
            data: <?php echo ($members_dropdown); ?>
        });

        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>