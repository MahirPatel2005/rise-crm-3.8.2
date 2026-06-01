<?php echo form_open(get_uri("banner_manager/save"), array("id" => "banner-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix post-dropzone">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <div class="form-group">
            <div class="row">
                <label for="title" class=" col-md-3"><?php echo app_lang('title'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "title",
                        "name" => "title",
                        "value" => $model_info->title,
                        "class" => "form-control",
                        "placeholder" => app_lang('title'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="clearfix">
            <div class="row">
                <label for="start_date" class="col-md-3"><?php echo app_lang('start_date'); ?></label>
                <div class="form-group col-md-3">
                    <?php
                    echo form_input(array(
                        "id" => "start_date",
                        "name" => "start_date",
                        "value" => $model_info->start_date,
                        "class" => "form-control",
                        "placeholder" => "YYYY-MM-DD",
                        "autocomplete" => "off",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required")
                    ));
                    ?>
                </div>

                <label for="end_date" class="col-md-2"><?php echo app_lang('end_date'); ?></label>
                <div class="form-group col-md-4">
                    <?php
                    echo form_input(array(
                        "id" => "end_date",
                        "name" => "end_date",
                        "value" => $model_info->end_date,
                        "class" => "form-control",
                        "placeholder" => "YYYY-MM-DD",
                        "autocomplete" => "off",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                        "data-rule-greaterThanOrEqual" => "#start_date",
                        "data-msg-greaterThanOrEqual" => app_lang("end_date_must_be_equal_or_greater_than_start_date")
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="share_with" class=" col-md-3"><?php echo app_lang('share_with'); ?></label>
                <div class=" col-md-9">
                    <!--Team members-->
                    <div class="share-with-section mb15">
                        <div>
                            <?php
                            echo form_checkbox(array(
                                "id" => "share_with_all_team_members",
                                "name" => "share_with_team_members",
                                "value" => "all",
                                "class" => "toggle_specific form-check-input",
                                    ), $model_info->share_with_team_members ? $model_info->share_with_team_members : "", ($model_info->share_with_team_members === "all") ? true : false);
                            ?>
                            <label for="share_with_all_team_members"><?php echo app_lang("all_team_members"); ?></label>
                        </div>

                        <div class="form-group mb0">
                            <?php
                            echo form_checkbox(array(
                                "id" => "share_with_specific_team_members_radio_button",
                                "name" => "share_with_team_members",
                                "value" => "specific",
                                "class" => "toggle_specific form-check-input",
                                    ), $model_info->share_with_team_members ? $model_info->share_with_team_members : "", ($model_info->share_with_team_members && $model_info->share_with_team_members != "all") ? true : false);
                            ?>
                            <label for="share_with_specific_team_members_radio_button"><?php echo app_lang("specific_members_and_teams"); ?>:</label>
                            <div class="specific_dropdown" style="display: none;">
                                <input type="text" value="<?php echo ($model_info->share_with_team_members && $model_info->share_with_team_members != "all" ) ? $model_info->share_with_team_members : ""; ?>" name="share_with_specific_team_members" id="share_with_specific_team_members" class="w100p validate-hidden"  data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>" placeholder="<?php echo app_lang('choose_members_and_or_teams'); ?>"  />
                            </div>
                        </div>
                    </div>

                    <!--Clients-->
                    <div class="share-with-section">
                        <div>
                            <?php
                            echo form_checkbox(array(
                                "id" => "share_with_all_client_contacts",
                                "name" => "share_with_client_contacts",
                                "value" => "all",
                                "class" => "toggle_specific form-check-input",
                                    ), $model_info->share_with_client_contacts ? $model_info->share_with_client_contacts : "", ($model_info->share_with_client_contacts === "all") ? true : false);
                            ?>
                            <label for="share_with_all_client_contacts"><?php echo app_lang("banner_manager_all_clients"); ?></label>
                        </div>

                        <div class="form-group mb0">
                            <?php
                            echo form_checkbox(array(
                                "id" => "share_with_specific_client_contacts_radio_button",
                                "name" => "share_with_client_contacts",
                                "value" => "specific",
                                "class" => "toggle_specific form-check-input",
                                    ), $model_info->share_with_client_contacts ? $model_info->share_with_client_contacts : "", ($model_info->share_with_client_contacts && $model_info->share_with_client_contacts != "all") ? true : false);
                            ?>
                            <label for="share_with_specific_client_contacts_radio_button"><?php echo app_lang("specific_client_contacts"); ?>:</label>
                            <div class="specific_dropdown" style="display: none;">
                                <input type="text" value="<?php echo ($model_info->share_with_client_contacts && $model_info->share_with_client_contacts != "all" ) ? $model_info->share_with_client_contacts : ""; ?>" name="share_with_specific_client_contacts" id="share_with_specific_client_contacts" class="w100p validate-hidden"  data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>" placeholder="<?php echo app_lang('banner_manager_choose_client_contacts'); ?>"  />
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label class=" col-md-3"><?php echo app_lang('banner_manager_banner'); ?></label>
                <div class=" col-md-9">
                    <div class="float-start mr15">
                        <?php
                        $banner_src = banner_manager_get_banner_source_url($model_info->banner);
                        if ($banner_src) {
                            ?>
                            <img id="banner-preview" class="banner-edit-preview" src="<?php echo banner_manager_get_banner_source_url($model_info->banner); ?>" alt="..." />
                        <?php } ?>
                    </div>
                    <div class="float-start mr15">
                        <?php echo view("includes/dropzone_preview"); ?>    
                    </div>
                    <div class="float-start upload-file-button btn btn-default btn-sm">
                        <span><i data-feather="upload" class="icon-14"></i> <?php echo app_lang("upload"); ?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    "use strict";
    
    $(document).ready(function () {
        $("#banner-form").appForm({
            onSuccess: function (result) {
                $("#banner-table").appTable({newData: result.data, dataId: result.id});
            }
        });

        var uploadUrl = "<?php echo get_uri("banner_manager/upload_file"); ?>";
        var validationUrl = "<?php echo get_uri("banner_manager/validate_banner_file"); ?>";

        var dropzone = attachDropzoneWithForm("#banner-form", uploadUrl, validationUrl, {maxFiles: 1});

        setDatePicker("#start_date");
        setDatePicker("#end_date");

        setTimeout(function () {
            $("#title").focus();
        }, 200);

        get_specific_dropdown($("#share_with_specific_team_members"), <?php echo ($members_and_teams_dropdown); ?>);
        $("#share_with_specific_client_contacts").select2({
            multiple: true,
            data: <?php echo ($clients_dropdown); ?>
        });

        function get_specific_dropdown(container, data) {
            setTimeout(function () {
                container.select2({
                    multiple: true,
                    formatResult: teamAndMemberSelect2Format,
                    formatSelection: teamAndMemberSelect2Format,
                    data: data
                }).on('select2-open change', function (e) {
                    feather.replace();
                });

                feather.replace();
            }, 100);
        }

        $(".toggle_specific").click(function () {
            //on sections, a single field should be selected
            if ($(this).is(":checked")) {
                $(this).closest(".share-with-section").find(".toggle_specific").removeAttr("checked").closest("div").addClass("hide");
                $(this).attr("checked", "checked").closest("div").removeClass("hide");
            } else {
                $(this).closest(".share-with-section").find(".toggle_specific").closest("div").removeClass("hide");
            }

            toggle_specific_dropdown();
        });

        toggle_specific_dropdown();

        function toggle_specific_dropdown() {
            $(".specific_dropdown").hide().find("input").removeClass("validate-hidden");

            $(".toggle_specific").each(function () {
                var $element = $(this);
                if ($element.is(":checked") && $element.val() === "specific") {
                    var $dropdown = $element.closest("div").find("div.specific_dropdown");
                    $dropdown.show().find("input").addClass("validate-hidden");
                }
            });
        }

    });
</script>