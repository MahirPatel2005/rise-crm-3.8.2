<div class="card no-border clearfix mb0">

    <?php echo form_open(get_uri("aws_s3_integration_settings/save_aws_s3_integration_settings"), array("id" => "aws_s3_integration-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>

    <div class="card-body">
        <div class="form-group">
            <div class="row">
                <label for="integrate_aws_s3" class=" col-md-2"><?php echo app_lang('aws_s3_integration_integrate') . " AWS S3"; ?></label>

                <div class="col-md-10">
                    <?php
                    echo form_checkbox("integrate_aws_s3", "1", get_aws_s3_integration_setting("integrate_aws_s3") ? true : false, "id='integrate_aws_s3' class='form-check-input'");
                    ?> 
                </div>
            </div>
        </div>

        <div class="clearfix integrate-with-aws_s3-details-section <?php echo get_aws_s3_integration_setting("integrate_aws_s3") ? "" : "hide" ?>">
            <div class="form-group">
                <div class="row">
                    <label class=" col-md-12">
                        <?php echo app_lang("aws_s3_integration_get_your_access_keys_from_here") . ": " . anchor("https://console.aws.amazon.com/iam/", "AWS IAM console", array("target" => "_blank")); ?>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="access_key_id" class=" col-md-2"><?php echo app_lang('aws_s3_integration_access_key_id'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "access_key_id",
                            "name" => "access_key_id",
                            "value" => get_aws_s3_integration_setting("access_key_id"),
                            "class" => "form-control",
                            "placeholder" => app_lang('aws_s3_integration_access_key_id'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="secret_access_key" class=" col-md-2"><?php echo app_lang('aws_s3_integration_secret_access_key'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "secret_access_key",
                            "name" => "secret_access_key",
                            "value" => get_aws_s3_integration_setting('secret_access_key'),
                            "class" => "form-control",
                            "placeholder" => app_lang('aws_s3_integration_secret_access_key'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="bucket_name" class=" col-md-2"><?php echo app_lang('aws_s3_integration_bucket_name'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "bucket_name",
                            "name" => "bucket_name",
                            "value" => get_aws_s3_integration_setting("bucket_name"),
                            "class" => "form-control",
                            "placeholder" => app_lang('aws_s3_integration_bucket_name'),
                            "minlength" => 3,
                            "maxlength" => 63,
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                        <span class="mt10 d-inline-block"><i data-feather='alert-triangle' class="icon-16 text-warning"></i> <span class="text-off"><?php echo app_lang("aws_s3_integration_bucket_naming_help_message") . " " . anchor("https://docs.aws.amazon.com/AmazonS3/latest/userguide/bucketnamingrules.html", app_lang("aws_s3_integration_see_rules_for_bucket_naming") . " <i data-feather='external-link' class='icon-16'></i>", array("target" => "_blank")); ?></span></span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="region" class=" col-md-2"><?php echo app_lang('aws_s3_integration_region'); ?></label>
                    <div class="col-md-10">
                        <?php
                        echo form_dropdown(
                                "region", $region_dropdown, get_aws_s3_integration_setting('region') ? get_aws_s3_integration_setting('region') : "english", "class='select2 mini'"
                        );
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="status" class=" col-md-2"><?php echo app_lang('status'); ?></label>
                    <div class=" col-md-10">
                        <?php if (get_aws_s3_integration_setting('aws_s3_authorized')) { ?>
                            <span class="badge bg-success"><?php echo app_lang("authorized"); ?></span>
                        <?php } else { ?>
                            <span class="badge" style="background:#F9A52D;"><?php echo app_lang("unauthorized"); ?></span>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="card-footer">
        <button id="save-button" type="submit" class="btn btn-primary <?php echo get_aws_s3_integration_setting("integrate_aws_s3") ? "hide" : "" ?>"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
        <button id="save-and-authorize-button" type="submit" class="btn btn-primary ml5 <?php echo get_aws_s3_integration_setting("integrate_aws_s3") ? "" : "hide" ?>"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save_and_authorize'); ?></button>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        var $saveAndAuthorizeBtn = $("#save-and-authorize-button"),
                $saveBtn = $("#save-button"),
                $detailsArea = $(".integrate-with-aws_s3-details-section");

        $("#aws_s3_integration-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message, {duration: 10000});

                    //if this is enabled, redirect to authorization system
                    if ($saveBtn.hasClass("hide")) {
                        location.reload();
                    }
                } else {
                    appAlert.error(result.message);
                }
            }
        });

        //show/hide calendar details area
        $("#integrate_aws_s3").on('click', function () {
            if ($(this).is(":checked")) {
                $saveAndAuthorizeBtn.removeClass("hide");
                $saveBtn.addClass("hide");
                $detailsArea.removeClass("hide");
            } else {
                $saveAndAuthorizeBtn.addClass("hide");
                $saveBtn.removeClass("hide");
                $detailsArea.addClass("hide");
            }
        });

        $("#aws_s3_integration-settings-form .select2").select2();

        var $bucketName = $('#bucket_name');

        //https://docs.aws.amazon.com/AmazonS3/latest/userguide/bucketnamingrules.html
        //only lowercase, numbers, dots (.) and hyphens (-) are allowed
        $bucketName.on('keyup', function () {
            var replacedValue = $bucketName.val().replace(/[^a-zA-Z0-9-.]/g, '').toLowerCase();
            $bucketName.val(replacedValue);
        });

    });
</script>