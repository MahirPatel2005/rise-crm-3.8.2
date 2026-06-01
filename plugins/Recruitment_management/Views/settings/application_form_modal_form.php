<?php echo form_open(get_uri("recruitment_settings/application_form_save"), array("id" => "recruitment-application-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <div class="form-group mb-4 mt-4">
            <div class="row">
                <label for="required" class=" col-md-3"><?php echo app_lang('required'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_checkbox(
                            "required", "1", $model_info->required ? true : false, "id='required' class='form-check-input'"
                    );
                    ?>
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
        $("#recruitment-application-form").appForm({
            onSuccess: function (result) {
                $("#recruitment-application-form-table").appTable({newData: result.data, dataId: result.id});
            }
        });
    });
</script>