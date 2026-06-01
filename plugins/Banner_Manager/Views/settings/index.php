<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "banner_manager";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <div class="card">

                <div class="card-header">
                    <h4><?php echo app_lang("banner_manager_settings"); ?></h4>
                </div>

                <?php echo form_open(get_uri("banner_manager_settings/save_banner_manager_settings"), array("id" => "banner_manager-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>

                <div class="card-body post-dropzone">
                    <div class="form-group">
                        <div class="row">
                            <label for="banner_manager_users" class=" col-md-2"><?php echo app_lang('banner_manager_who_can_manage_banners'); ?> <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('banner_manager_users_help_message'); ?>"><i data-feather='help-circle' class="icon-16"></i></span></label>
                            <div class=" col-md-9">
                                <?php
                                echo form_input(array(
                                    "id" => "banner_manager_users",
                                    "name" => "banner_manager_users",
                                    "value" => get_banner_manager_setting("banner_manager_users"),
                                    "class" => "form-control",
                                    "placeholder" => app_lang('team_members')
                                ));
                                ?>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#banner_manager-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });

        $("#banner_manager_users").select2({
            multiple: true,
            data: <?php echo ($members_dropdown); ?>
        });

        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>