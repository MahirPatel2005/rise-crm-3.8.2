<?php echo form_open(get_uri("sms/save_sms_template"), array("id" => "sms-template-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">

        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

        <div class="form-group">
            <div class="row">
                <label for="title" class=" col-md-3"><?php echo app_lang('event'); ?></label>
                <div class=" col-md-9">
                    <strong>
                        <?php
                        echo app_lang($model_info->template_name);
                        ?>
                    </strong>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="message" class=" col-md-3"><?php echo app_lang('message'); ?></label>
                <div class=" col-md-9">
                    <div class="notepad">
                        <?php
                        echo form_textarea(array(
                            "id" => "message",
                            "name" => "message",
                            "value" => $model_info->custom_message ? $model_info->custom_message : $model_info->default_message,
                            "class" => "form-control",
                            "placeholder" => app_lang('message'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                            "data-rich-text-editor" => true
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="avilable_variables" class="col-md-3"><?php echo app_lang("avilable_variables"); ?></label>
                <div class="col-md-9">
                    <?php
                    foreach ($variables as $variable) {
                        echo "{" . $variable . "}, ";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button id="restore_to_default" data-bs-toggle="popover" data-id="<?php echo $model_info->id; ?>" data-placement="top" type="button" class="btn btn-danger float-start me-auto"><span data-feather="refresh-cw" class="icon-16"></span> <?php echo app_lang('restore_to_default'); ?></button>

    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#sms-template-form").appForm({
            onSuccess: function (result) {
                $("#sms-templates-table").appTable({newData: result.data, dataId: result.id});
            }
        });

        $('#restore_to_default').on("click", function () {
            var $instance = $(this);
            $(this).appConfirmation({
                title: "<?php echo app_lang('are_you_sure'); ?>",
                btnConfirmLabel: "<?php echo app_lang('yes'); ?>",
                btnCancelLabel: "<?php echo app_lang('no'); ?>",
                onConfirm: function () {
                    $.ajax({
                        url: "<?php echo get_uri('sms/restore_to_default') ?>",
                        type: 'POST',
                        dataType: 'json',
                        data: {id: $instance.attr("data-id")},
                        success: function (result) {
                            if (result.success) {
                                $('#message').val(result.data);
                                appAlert.success(result.message, {duration: 10000});
                            } else {
                                appAlert.error(result.message);
                            }
                        }
                    });

                }
            });

            return false;
        });
    });
</script>    