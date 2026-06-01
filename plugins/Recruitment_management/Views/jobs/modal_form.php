<?php $today = get_today_date(); ?>
<?php echo form_open(get_uri("recruitment_management/save_job"), array("id" => "recruitment-job-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

        <div class="form-group">
            <div class="row">
                <label for="start_date" class=" col-md-3"><?php echo app_lang('start_date'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "start_date",
                        "name" => "start_date",
                        "value" => $model_info->start_date ? $model_info->start_date : get_my_local_time("Y-m-d"),
                        "class" => "form-control",
                        "placeholder" => app_lang('start_date'),
                        "autocomplete" => "off",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="deadline" class=" col-md-3"><?php echo app_lang('deadline'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "deadline",
                        "name" => "deadline",
                        "value" => is_date_exists($model_info->deadline) ? $model_info->deadline : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('deadline'),
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
                <label for="job_title" class="col-md-3"><?php echo app_lang('recruitment_job_title'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "job_title",
                        "name" => "job_title",
                        "value" => $model_info->job_title,
                        "class" => "form-control",
                        "autocomplete" => "off",
                        "placeholder" => app_lang('recruitment_job_title'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="status" class=" col-md-3"><?php echo app_lang('status'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_dropdown("status", array("draft" => app_lang("draft"), "active" => app_lang("active"), "inactive" => app_lang("inactive")), $model_info->status, "class='select2'");
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="job_type_id" class="col-md-3"><?php echo app_lang('recruitment_job_type'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "job_type_id",
                        "name" => "job_type_id",
                        "value" => $model_info->job_type_id,
                        "class" => "form-control",
                        "placeholder" => app_lang('recruitment_job_type'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="job_position_id" class="col-md-3"><?php echo app_lang('recruitment_job_position'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "job_position_id",
                        "name" => "job_position_id",
                        "value" => $model_info->job_position_id,
                        "class" => "form-control",
                        "placeholder" => app_lang('recruitment_job_position'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="department_id" class="col-md-3"><?php echo app_lang('recruitment_departments'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "department_id",
                        "name" => "department_id",
                        "value" => $model_info->department_id,
                        "class" => "form-control",
                        "placeholder" => app_lang('recruitment_departments'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="location_id" class="col-md-3"><?php echo app_lang('recruitment_location'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "location_id",
                        "name" => "location_id",
                        "value" => $model_info->location_id,
                        "class" => "form-control",
                        "placeholder" => app_lang('recruitment_location'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="description" class=" col-md-3"><?php echo app_lang('description'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "description",
                        "name" => "description",
                        "value" => "$model_info->description",
                        "class" => "form-control",
                        "placeholder" => app_lang('description'),
                        "data-rich-text-editor" => true
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="quantity_to_be_required" class="col-md-3"><?php echo app_lang('recruitment_quantity_to_be_required'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "quantity_to_be_required",
                        "name" => "quantity_to_be_required",
                        "type" => "number",
                        "value" => $model_info->quantity_to_be_required,
                        "class" => "form-control",
                        "placeholder" => app_lang('recruitment_quantity_to_be_required'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="salary" class="col-md-3"><?php echo app_lang('salary'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "salary",
                        "name" => "salary",
                        "value" => $model_info->salary,
                        "class" => "form-control",
                        "placeholder" => app_lang('salary')
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="recruiters" class=" col-md-3"><?php echo app_lang('recruitment_recruiters'); ?></label>
                <div class="col-md-9">
                    <input type="text" value="<?php echo $model_info->recruiters; ?>" name="recruiters" id="recruiters" class="w100p" placeholder="<?php echo app_lang('recruitment_recruiters'); ?>"  />    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('cancel'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#recruitment-job-form").appForm({
            onSuccess: function (result) {
                if (typeof RELOAD_CIRCULAR_VIEW_AFTER_UPDATE !== "undefined" && RELOAD_CIRCULAR_VIEW_AFTER_UPDATE) {
                    location.reload();
                }

                appAlert.success(result.message, {duration: 10000});
                $("#recruitment-job-table").appTable({newData: result.data, dataId: result.id});
            }
        });

        setDatePicker("#start_date, #deadline");

        $("#recruitment-job-form .select2").select2();

        $('#job_type_id').select2({data: <?php echo json_encode($job_types_dropdown); ?>});
        $('#job_position_id').select2({data: <?php echo json_encode($job_positions_dropdown); ?>});
        $('#department_id').select2({data: <?php echo json_encode($departments_dropdown); ?>});
        $('#location_id').select2({data: <?php echo json_encode($locations_dropdown); ?>});
        $('#recruiters').select2({multiple: true, data: <?php echo $recruiters_dropdown; ?>});

    });
</script>