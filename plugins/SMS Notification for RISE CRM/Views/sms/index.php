<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "sms_notifications";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <div class="card">
                <ul data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
                    <li><a  role="presentation"  href="javascript:;" data-bs-target="#twilio-settings-tab"> <?php echo app_lang('twilio'); ?></a></li>
                    <li><a role="presentation" href="<?php echo_uri("sms/sms_notification_settings"); ?>" data-bs-target="#sms-notification-settings-tab"><?php echo app_lang('sms_notification_settings'); ?></a></li>
                    <li><a role="presentation" href="<?php echo_uri("sms/sms_notification_template"); ?>" data-bs-target="#sms-notification-template-tab"><?php echo app_lang('sms_templates'); ?></a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade" id="twilio-settings-tab">
                        <?php echo form_open(get_uri("sms/save_twilio_sms_settings"), array("id" => "twilio-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    <label for="enable_sms_notification" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('sms_enable_sms'); ?></label>
                                    <div class="col-md-10 col-xs-4 col-sm-8">
                                        <?php
                                        echo form_checkbox("enable_sms", "1", get_sms_setting("enable_sms") ? true : false, "id='enable_sms_notification' class='form-check-input ml15'");
                                        ?>
                                        <span class="ms-2"><i data-feather="alert-triangle" class="icon-16 text-danger"></i> <?php echo app_lang("sms_info_message") ?></span>
                                    </div>
                                </div>
                            </div>

                            <div id="twilio-details-area" class="<?php echo get_sms_setting("enable_sms") ? "" : "hide" ?>">
                                <div class="form-group">
                                    <div class="row">
                                        <label for="" class=" col-md-12">
                                            <?php echo app_lang("get_your_app_credentials_from_here") . " " . anchor("https://www.twilio.com", "Twilio", array("target" => "_blank")); ?>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label for="twilio_account_sid" class=" col-md-2"><?php echo app_lang('sms_twilio_account_sid'); ?></label>
                                        <div class=" col-md-10">
                                            <?php
                                            echo form_input(array(
                                                "id" => "twilio_account_sid",
                                                "name" => "twilio_account_sid",
                                                "value" => get_sms_setting("twilio_account_sid"),
                                                "class" => "form-control",
                                                "placeholder" => app_lang('sms_twilio_account_sid'),
                                                "data-rule-required" => true,
                                                "data-msg-required" => app_lang("field_required")
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label for="twilio_auth_token" class=" col-md-2"><?php echo app_lang('sms_twilio_auth_token'); ?></label>
                                        <div class=" col-md-10">
                                            <?php
                                            echo form_input(array(
                                                "id" => "twilio_auth_token",
                                                "name" => "twilio_auth_token",
                                                "value" => get_sms_setting("twilio_auth_token"),
                                                "class" => "form-control",
                                                "placeholder" => app_lang('sms_twilio_auth_token'),
                                                "data-rule-required" => true,
                                                "data-msg-required" => app_lang("field_required")
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label for="twilio_phone_number" class=" col-md-2"><?php echo app_lang('sms_twilio_phone_number'); ?></label>
                                        <div class=" col-md-10">
                                            <?php
                                            echo form_input(array(
                                                "id" => "twilio_phone_number",
                                                "name" => "twilio_phone_number",
                                                "value" => get_sms_setting("twilio_phone_number"),
                                                "class" => "form-control",
                                                "placeholder" => app_lang('sms_twilio_phone_number'),
                                                "data-rule-required" => true,
                                                "data-msg-required" => app_lang("field_required")
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <i data-feather="alert-triangle" class="icon-16 text-warning"></i>
                                            <span><?php echo sprintf(app_lang("sms_twilio_help_message"), anchor("https://www.twilio.com/docs/glossary/what-e164", "E.164"), anchor("https://support.twilio.com/hc/en-us/articles/223183008-Formatting-International-Phone-Numbers", "here")); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <i data-feather="alert-triangle" class="icon-16 text-danger"></i>
                                            <span><?php echo sprintf(app_lang("sms_twilio_user_phone_no_help_message"), anchor("https://www.twilio.com/docs/glossary/what-e164", "E.164")); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>

                            <?php if (get_sms_setting("enable_sms") && get_sms_setting("twilio_account_sid") && get_sms_setting("twilio_auth_token") && get_sms_setting("twilio_phone_number")) { ?>
                                <?php echo modal_anchor(get_uri("sms/send_test_sms_modal_form"), "<i data-feather='message-square' class='icon-16'></i> " . app_lang('sms_send_test_sms'), array("id" => "send-test-sms-btn", "class" => "btn btn-info text-white ml15", "title" => app_lang('sms_send_test_sms'))); ?>
                            <?php } ?>
                        </div>
                        <?php echo form_close(); ?>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="sms-notification-settings-tab"></div>
                    <div role="tabpanel" class="tab-pane fade" id="sms-notification-template-tab"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#twilio-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                if (result.success) {
                    if ($("#enable_sms_notification").is(":checked")) {
                        window.location.href = "<?php echo_uri("sms"); ?>";
                    } else {
                        appAlert.success(result.message, {duration: 10000});
                    }
                }
            }
        });

        //show/hide twilio SMS integration details area
        $("#enable_sms_notification").on("click", function () {
            $("#send-test-sms-btn").addClass("hide");
            if ($(this).is(":checked")) {
                $("#twilio-details-area").removeClass("hide");
            } else {
                $("#twilio-details-area").addClass("hide");
            }
        });

    });
</script>