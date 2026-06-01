<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "timecamp_integration";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>
        <div class="col-sm-9 col-lg-10">

            <div class="card">

                <?php echo form_open(get_uri("timecamp_integration/save_settings"), array("id" => "timecamp_integration-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>

                <div class="card-header">
                    <h4><?php echo app_lang("timecamp_integration"); ?></h4>
                </div>
                <div class="card-body general-form dashed-row">
                    <div class="form-group">
                        <div class="row">
                            <label for="enable_timecamp" class="col-md-3">
                                <?php echo app_lang('enable_timecamp'); ?>
                            </label>
                            <div class="col-md-9">
                                <?php
                                echo form_checkbox("enable_timecamp", "1", get_timecamp_integration_setting("enable_timecamp") ? true : false, "id='enable_timecamp' class='form-check-input'");
                                ?> 
                                <span id="timecamp_integration-show-hide-text" class="ml10 <?php echo get_timecamp_integration_setting("enable_timecamp") ? "" : "hide" ?>"><i data-feather='help-circle' class="icon-16"></i> <?php echo app_lang("timecamp_help_note"); ?></span>
                            </div>
                        </div>
                    </div>
                    <div id="timecamp-details" class="<?php echo get_timecamp_integration_setting("enable_timecamp") ? "" : "hide"; ?>">

                        <div class="form-group">
                            <div class="row">
                                <label class=" col-md-12">
                                    <?php echo app_lang("timecamp_get_your_api_token_from_here") . " " . anchor("https://app.timecamp.com/app#/settings/users/me", "Timecamp user settings", array("target" => "_blank")); ?>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="timecamp_api_token" class=" col-md-3"><?php echo app_lang('timecamp_api_token'); ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "timecamp_api_token",
                                        "name" => "timecamp_api_token",
                                        "value" => get_timecamp_integration_setting("timecamp_api_token"),
                                        "class" => "form-control",
                                        "placeholder" => app_lang('timecamp_api_token'),
                                        "data-rule-required" => true,
                                        "data-msg-required" => app_lang("field_required")
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-3"><?php echo app_lang('timecamp_sync'); ?></label>
                                <div class=" col-md-9">
                                    <div>
                                        <?php
                                        echo form_radio(array(
                                            "id" => "timecamp_sync",
                                            "name" => "timecamp_sync",
                                            "value" => "all",
                                            "class" => "toggle_specific form-check-input",
                                                ), get_timecamp_integration_setting("timecamp_sync"), (!get_timecamp_integration_setting("timecamp_sync") || get_timecamp_integration_setting("timecamp_sync") === "all") ? true : false);
                                        ?>
                                        <label for="timecamp_sync"><?php echo app_lang("all_projects"); ?></label>
                                    </div>
                                    <div class="form-group mb0">
                                        <?php
                                        echo form_radio(array(
                                            "id" => "timecamp_sync_specific_projects_button",
                                            "name" => "timecamp_sync",
                                            "value" => "specific",
                                            "class" => "toggle_specific form-check-input",
                                                ), get_timecamp_integration_setting("timecamp_sync"), (get_timecamp_integration_setting("timecamp_sync") && get_timecamp_integration_setting("timecamp_sync") != "all") ? true : false);
                                        ?>
                                        <label for="timecamp_sync_specific_projects_button"><?php echo app_lang("timecamp_specific_projects"); ?>:</label>
                                        <div class="specific_dropdown" style="display: none;">
                                            <input type="text" value="<?php echo (get_timecamp_integration_setting("timecamp_sync") && get_timecamp_integration_setting("timecamp_sync") != "all") ? get_timecamp_integration_setting("timecamp_sync") : ""; ?>" name="timecamp_sync_specific" id="timecamp_sync_specific" class="w100p validate-hidden"  data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>" placeholder="<?php echo app_lang('timecamp_specific_projects'); ?>"  />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group <?php echo get_timecamp_integration_setting("timecamp_sync") ? "" : "hide"; ?>">
                            <div class="row">
                                <label class="col-md-3"><?php echo app_lang('timecamp_sync_status'); ?></label>
                                <div class="col-md-9">
                                    <?php
                                    $partially_badge = "<span class='badge bg-warning'>" . app_lang("timecamp_partially") . "</span>";
                                    $fully_badge = "<span class='badge bg-success'>" . app_lang("timecamp_fully") . "</span>";

                                    echo app_lang("projects") . " " . strtolower(app_lang("timecamp_synced")) . ": <strong>" . $projects_count . "/" . $syncable_projects_count . "</strong> " . (($projects_count === $syncable_projects_count) ? $fully_badge : $partially_badge) . "<br />";
                                    echo app_lang("tasks") . " " . strtolower(app_lang("timecamp_synced")) . ": <strong>" . $tasks_count . "/" . $syncable_tasks_count . "</strong> " . (($tasks_count === $syncable_tasks_count) ? $fully_badge : $partially_badge);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary ml5"><span data-feather='check-circle' class="icon-16"></span> <?php echo app_lang('save'); ?></button>
                    <button id="sync-button" type="button" class="btn btn-success ml10 spinning-btn <?php echo get_timecamp_integration_setting("enable_timecamp") ? "" : "hide" ?>"><span data-feather='refresh-cw' class="icon-16"></span> <?php echo app_lang('timecamp_sync'); ?></button>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#timecamp_integration-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
                location.reload();
            }
        });

        //show/hide timecamp details area
        $("#enable_timecamp").click(function () {
            $("#sync-button").addClass("hide");

            if ($(this).is(":checked")) {
                $("#timecamp-details").removeClass("hide");
                $("#timecamp_integration-show-hide-text").removeClass("hide");
            } else {
                $("#timecamp-details").addClass("hide");
                $("#timecamp_integration-show-hide-text").addClass("hide");
            }
        });

        $(".toggle_specific").click(function () {
            $("#sync-button").addClass("hide");
            toggle_specific_dropdown();
        });

        toggle_specific_dropdown();

        function toggle_specific_dropdown() {
            $(".specific_dropdown").hide().find("input").removeClass("validate-hidden");

            var $element = $(".toggle_specific:checked");
            if ($element.val() === "specific") {
                var $dropdown = $element.closest("div").find("div.specific_dropdown");
                $dropdown.show().find("input").addClass("validate-hidden");
            }
        }

        $("#timecamp_sync_specific").select2({
            multiple: true,
            data: <?php echo json_encode($projects_dropdown); ?>
        }).on("change", function () {
            $("#sync-button").addClass("hide");
        });

        $("#sync-button").click(function () {
            $(this).addClass("spinning");
            $(this).attr("disabled", "disabled");

            $.ajax({
                url: "<?php echo get_uri('timecamp_integration/sync') ?>",
                type: 'POST',
                dataType: 'json',
                success: function (result) {
                    if (result.success) {
                        appAlert.warning(result.message, {duration: 10000});
                        location.reload();
                    } else {
                        appAlert.error(result.message);
                    }
                }
            });
        });
    });
</script>