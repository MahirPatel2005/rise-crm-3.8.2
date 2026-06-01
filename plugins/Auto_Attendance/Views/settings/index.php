<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "auto_attendance";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("auto_attendance_settings/save"), array("id" => "auto_attendance-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
            <div class="card">
                <div class="page-title clearfix">
                    <h4> <?php echo app_lang('auto_attendance'); ?></h4>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <label for="auto_clock_in_on_signin" class=" col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('auto_attendance_auto_clock_in_on_signin'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("auto_clock_in_on_signin", "1", get_auto_attendance_setting("auto_clock_in_on_signin") ? true : false, "id='auto_clock_in_on_signin' class='form-check-input'");
                                ?> 
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="auto_clock_out_on_signout" class=" col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('auto_attendance_auto_clock_out_on_signout'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_checkbox("auto_clock_out_on_signout", "1", get_auto_attendance_setting("auto_clock_out_on_signout") ? true : false, "id='auto_clock_out_on_signout' class='form-check-input'");
                                ?> 
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="auto_clock_out_after" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('auto_attendance_auto_clock_out_after'); ?>  <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('cron_job_required'); ?>"><i data-feather='help-circle' class="icon-16"></i></span></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <?php
                                echo form_input(array(
                                    "id" => "auto_clock_out_after",
                                    "name" => "auto_clock_out_after",
                                    "type" => "number",
                                    "value" => get_auto_attendance_setting("auto_clock_out_after"),
                                    "class" => "form-control mini float-start",
                                    "min" => 0
                                ));
                                ?>
                                <label class="mt5 ml10 float-start"><?php echo app_lang('hours'); ?></label>
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
        $("#auto_attendance-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });

        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>