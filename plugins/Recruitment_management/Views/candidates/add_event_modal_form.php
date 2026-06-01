<?php echo form_open(get_uri("recruitment_candidates/save_event"), array("id" => "recruitment-candidate-event-form", "class" => "general-form", "role" => "form")); ?>
<div id="recruitment-candidates-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
            <input type="hidden" name="candidate_id" value="<?php echo $candidate_id; ?>" />
            <div class="form-group">
                <div class="row">
                    <label for="event_type_id" class="col-md-3"><?php echo app_lang('recruitment_event_type'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "event_type_id",
                            "name" => "event_type_id",
                            "value" => $model_info->event_type_id,
                            "class" => "form-control",
                            "placeholder" => app_lang('recruitment_event_type'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="clearfix">
                <div class="row">
                    <label for="start_date" class=" col-md-3 col-sm-3"><?php echo app_lang('start_date'); ?></label>
                    <div class="col-md-4 col-sm-4 form-group">
                        <?php
                        echo form_input(array(
                            "id" => "start_date",
                            "name" => "start_date",
                            "value" => $model_info->start_date,
                            "class" => "form-control",
                            "placeholder" => app_lang('start_date'),
                            "autocomplete" => "off",
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                    <label for="start_time" class=" col-md-2 col-sm-2"><?php echo app_lang('start_time'); ?></label>
                    <div class=" col-md-3 col-sm-3">
                        <?php
                        $start_time = is_date_exists($model_info->start_time) ? $model_info->start_time : "";

                        if ($time_format_24_hours) {
                            $start_time = $start_time ? date("H:i", strtotime($start_time)) : "";
                        } else {
                            $start_time = $start_time ? convert_time_to_12hours_format(date("H:i:s", strtotime($start_time))) : "";
                        }

                        echo form_input(array(
                            "id" => "start_time",
                            "name" => "start_time",
                            "value" => $start_time,
                            "class" => "form-control",
                            "placeholder" => app_lang('start_time'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>


            <div class="clearfix">
                <div class="row">
                    <label for="end_date" class=" col-md-3 col-sm-3"><?php echo app_lang('end_date'); ?></label>
                    <div class=" col-md-4 col-sm-4 form-group">
                        <?php
                        echo form_input(array(
                            "id" => "end_date",
                            "name" => "end_date",
                            "value" => $model_info->end_date,
                            "class" => "form-control",
                            "placeholder" => app_lang('end_date'),
                            "autocomplete" => "off",
                            "data-rule-greaterThanOrEqual" => "#start_date",
                            "data-msg-greaterThanOrEqual" => app_lang("end_date_must_be_equal_or_greater_than_start_date")
                        ));
                        ?>
                    </div>
                    <label for="end_time" class=" col-md-2 col-sm-2"><?php echo app_lang('end_time'); ?></label>
                    <div class=" col-md-3 col-sm-3">
                        <?php
                        $end_time = is_date_exists($model_info->end_time) ? $model_info->end_time : "";

                        if ($time_format_24_hours) {
                            $end_time = $end_time ? date("H:i", strtotime($end_time)) : "";
                        } else {
                            $end_time = $end_time ? convert_time_to_12hours_format(date("H:i:s", strtotime($end_time))) : "";
                        }

                        echo form_input(array(
                            "id" => "end_time",
                            "name" => "end_time",
                            "value" => $end_time,
                            "class" => "form-control",
                            "placeholder" => app_lang('end_time'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="location" class="col-md-3"><?php echo app_lang('location'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "location",
                            "name" => "location",
                            "value" => $model_info->location ? $model_info->location : "",
                            "class" => "form-control",
                            "placeholder" => app_lang('location'),
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
                            "class" => "form-control",
                            "placeholder" => app_lang('description'),
                            "value" => $model_info->description,
                            "data-rich-text-editor" => true
                        ));
                        ?>
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
        $("#recruitment-candidate-event-form").appForm({
            onSuccess: function (result) {
                $("#recruitment-candidate-events-table").appTable({newData: result.data, dataId: result.id});
            }
        });

        $("#recruitment-candidate-event-form .select2").select2();

        $('#event_type_id').select2({data: <?php echo json_encode($event_types_dropdown); ?>});

        setDatePicker("#start_date, #end_date");

        setTimePicker("#start_time, #end_time");

        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>