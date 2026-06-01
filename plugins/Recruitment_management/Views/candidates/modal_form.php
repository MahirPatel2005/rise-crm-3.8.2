<?php
$required_gender = "";
$required_dob = "";
$required_phone = "";
$required_address = "";
$required_education = "";
$required_work_experience = "";
$required_resume = "";

if (get_recruitment_application_form_setting('gender')) {
    $required_gender = "<span class='text-danger'>*</span>";
}
if (get_recruitment_application_form_setting('date_of_birth')) {
    $required_dob = "<span class='text-danger'>*</span>";
}
if (get_recruitment_application_form_setting('phone')) {
    $required_phone = "<span class='text-danger'>*</span>";
}
if (get_recruitment_application_form_setting('address')) {
    $required_address = "<span class='text-danger'>*</span>";
}
if (get_recruitment_application_form_setting('education')) {
    $required_education = "<span class='text-danger'>*</span>";
}
if (get_recruitment_application_form_setting('work_experience')) {
    $required_work_experience = "<span class='text-danger'>*</span>";
}
if (get_recruitment_application_form_setting('resume')) {
    $required_resume = "<span class='text-danger'>*</span>";
}
?>

<?php echo form_open(get_uri("recruitment_circulars/save_circular"), array("id" => "recruitment-candidate-form", "class" => "general-form", "recruitment_circular_template" => "form")); ?>
<div id="recruitment-candidates-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
            <input type="hidden" name="circular_id" value="<?php echo $model_info->circular_id; ?>" />

            <div class="form-group">
                <div class="row">
                    <label for="first_name" class="col-md-3"><?php echo app_lang('first_name'); ?><span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "first_name",
                            "name" => "first_name",
                            "class" => "form-control",
                            "value" => $model_info->first_name,
                            "placeholder" => app_lang('first_name'),
                            "autofocus" => true,
                            "data-rule-required" => false,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="last_name" class="col-md-3"><?php echo app_lang('last_name'); ?><span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "last_name",
                            "name" => "last_name",
                            "class" => "form-control",
                            "value" => $model_info->last_name,
                            "placeholder" => app_lang('last_name'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="email" class="col-md-3"><?php echo app_lang('email'); ?><span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "email",
                            "name" => "email",
                            "class" => "form-control",
                            "placeholder" => app_lang('email'),
                            "value" => $model_info->email,
                            "autocomplete" => "off",
                            "data-rule-email" => true,
                            "data-msg-email" => app_lang("enter_valid_email"),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="gender" class="col-md-3"><?php echo app_lang('gender'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_radio(array(
                            "id" => "gender_male",
                            "name" => "gender",
                            "class" => "form-check-input",
                            "data-rule-required" => get_recruitment_application_form_setting('gender') ? true : "false",
                            "data-msg-required" => app_lang("field_required"),
                                ), "male", ($model_info->gender == "male") ? true : false);
                        ?>
                        <label for="gender_male" class="mr15 p0"><?php echo app_lang('male'); ?></label> 
                        <?php
                        echo form_radio(array(
                            "id" => "gender_female",
                            "name" => "gender",
                            "class" => "form-check-input",
                            "data-rule-required" => get_recruitment_application_form_setting('gender') ? true : "false",
                            "data-msg-required" => app_lang("field_required"),
                                ), "female", ($model_info->gender == "female") ? true : false);
                        ?>
                        <label for="gender_female" class="p0 mr15"><?php echo app_lang('female'); ?></label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="date_of_birth" class="col-md-3"><?php echo app_lang('date_of_birth'); ?><?php echo $required_dob; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "date_of_birth",
                            "name" => "date_of_birth",
                            "class" => "form-control",
                            "value" => $model_info->dob,
                            "placeholder" => app_lang('date_of_birth'),
                            "data-rule-required" => get_recruitment_application_form_setting('date_of_birth') ? true : "false",
                            "data-msg-required" => app_lang("field_required"),
                            "autocomplete" => "off"
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="phone" class="col-md-3"><?php echo app_lang('phone'); ?><?php echo $required_phone; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "phone",
                            "name" => "phone",
                            "class" => "form-control",
                            "value" => $model_info->phone,
                            "placeholder" => app_lang('phone'),
                            "data-rule-required" => get_recruitment_application_form_setting('phone') ? true : "false",
                            "data-msg-required" => app_lang("field_required")
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="address" class="col-md-3"><?php echo app_lang('address'); ?><?php echo $required_address; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_textarea(array(
                            "id" => "address",
                            "name" => "address",
                            "class" => "form-control",
                            "value" => $model_info->address,
                            "placeholder" => app_lang('address'),
                            "data-rule-required" => get_recruitment_application_form_setting('address') ? true : "false",
                            "data-msg-required" => app_lang("field_required")
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="education" class="col-md-3"><?php echo app_lang('recruitment_education'); ?><?php echo $required_education; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "education",
                            "name" => "education",
                            "class" => "form-control",
                            "value" => $model_info->education,
                            "placeholder" => app_lang('recruitment_education'),
                            "data-rule-required" => get_recruitment_application_form_setting('education') ? true : "false",
                            "data-msg-required" => app_lang("field_required")
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="work_experience" class="col-md-3"><?php echo app_lang('recruitment_work_experience'); ?><?php echo $required_work_experience; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "work_experience",
                            "name" => "work_experience",
                            "value" => $model_info->work_experience,
                            "class" => "form-control",
                            "placeholder" => app_lang('recruitment_work_experience'),
                            "data-rule-required" => get_recruitment_application_form_setting('work_experience') ? true : "false",
                            "data-msg-required" => app_lang("field_required")
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
        $("#recruitment-candidate-form").appForm({
            onSuccess: function (result) {
                location.reload();
            }
        });

        setDatePicker("#date_of_birth");

        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>