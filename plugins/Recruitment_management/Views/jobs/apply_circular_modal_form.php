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

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo view('includes/head'); ?>
        <?php
        recruitment_load_css(
                array(PLUGIN_URL_PATH . "Recruitment_management/assets/css/recruitment-style.css")
        );
        ?>
    </head>
    <body>

        <style>
            .card {
                transition: all 0s !important;
            }
            .mt4{
                margin-top: 4px;
            }
        </style>

        <div id="apply-circular-container-scrollbar">
            <div class="circular-preview">
                <div class = "card p15 no-border">
                    <div class="clearfix">
                        <img class="dashboard-image float-start" src="<?php echo get_logo_url(); ?>" />
                    </div>
                </div>
                <div class="invoice-preview-container bg-white">
                    <div id="recruitment_circular_form_container">
                        <?php
                        echo form_open(get_uri("recruitment_circulars/save_circular"), array("id" => "recruitment-circular-form", "class" => "general-form", "role" => "form"));
                        echo "<input type='hidden' name='circular_id' value='$model_info->id' />";
                        ?>
                        <div class="text-center ps-5 pe-5 pb20">
                            <h1><?php echo $model_info->job_title; ?></h1>
                            <h4 class="w-75 m-auto"><?php echo app_lang("recruitment_sharing_your_basic_info"); ?></h4>
                        </div>

                        <div id="apply-circular-form-preview" class="card p15 mb-0 no-border clearfix post-dropzone" >
                            <div class="mb-3">
                                <input type="hidden" name="id" value="" />

                                <div class="form-group">
                                    <div class="row">
                                        <label for="first_name" class="col-md-3"><?php echo app_lang('first_name'); ?><span class="text-danger">*</span></label>
                                        <div class="col-md-9">
                                            <?php
                                            echo form_input(array(
                                                "id" => "first_name",
                                                "name" => "first_name",
                                                "class" => "form-control",
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
                                        <label for="gender" class="col-md-3"><?php echo app_lang('gender'); ?><?php echo $required_gender; ?></label>
                                        <div class="col-md-9">
                                            <?php
                                            echo form_radio(array(
                                                "id" => "gender_male",
                                                "name" => "gender",
                                                "class" => "form-check-input",
                                                "data-rule-required" => get_recruitment_application_form_setting('gender') ? true : "false",
                                                "data-msg-required" => app_lang("field_required"),
                                                    ), "male", "", "class='form-check-input'");
                                            ?>
                                            <label for="gender_male" class="mr15 p0"><?php echo app_lang('male'); ?></label> <?php
                                            echo form_radio(array(
                                                "id" => "gender_female",
                                                "name" => "gender",
                                                "class" => "form-check-input",
                                                "data-rule-required" => get_recruitment_application_form_setting('gender') ? true : "false",
                                                "data-msg-required" => app_lang("field_required"),
                                                    ), "female", "", "class='form-check-input'");
                                            ?>
                                            <label for="gender_female" class="p0"><?php echo app_lang('female'); ?></label>
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
                                                "class" => "form-control",
                                                "placeholder" => app_lang('recruitment_work_experience'),
                                                "data-rule-required" => get_recruitment_application_form_setting('work_experience') ? true : "false",
                                                "data-msg-required" => app_lang("field_required")
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label for="resume" class="col-md-3"><?php echo app_lang('recruitment_resume'); ?><?php echo $required_resume; ?> <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('recruitment_resume_upload_instruction'); ?>"><i data-feather='help-circle' class="icon-16"></i></span></label>
                                        <div class="col-md-9">
                                            <div class="recruitment-dropzone upload-file-button" id="resume" name="resume">
                                                <div class="dropzone-message">
                                                    <i data-feather='upload-cloud' class='recruitment-upload-icon'></i>
                                                    <h4 class="title text-muted text-center"><?php echo app_lang('recruitment_upload_your_resume'); ?></h4>
                                                </div>
                                                <?php echo view("includes/dropzone_preview"); ?>
                                            </div>
                                        </div>
                                        <?php if (get_recruitment_application_form_setting('resume')) { ?>
                                            <input id="recruitment_file" style="opacity: 0" name="file" value="" required="" />
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="submit-button" class="btn btn-primary"><i data-feather="arrow-right-circle" class="icon-16"></i> <?php echo app_lang('submit'); ?></button>
                            <div class="w-100 hide">
                                <button type="submit" id="submit-button" class="btn btn-primary w-25 float-start"><i data-feather="arrow-left-circle" class="icon-16"></i> <?php echo app_lang('previous'); ?></button>
                                <button type="submit" id="submit-button" class="btn btn-primary w-50 float-end"><i data-feather="send" class="icon-16"></i> <?php echo app_lang('submit'); ?></button>
                            </div>
                        </div>

                        <?php
                        echo form_close();
                        ?>
                    </div>
                </div>
            </div>
            <?php echo view('modal/index'); ?>
        </div>

        <script type="text/javascript">
            "use strict";

            $(document).ready(function () {
                $("#recruitment-circular-form").appForm({
                    isModal: false,
                    onSubmit: function () {
                        appLoader.show();
                        $("#recruitment-circular-form").find('[type="submit"]').attr('disabled', 'disabled');
                    },
                    onSuccess: function (result) {
                        appLoader.hide();
                        $("#recruitment_circular_form_container").html("");
                        appAlert.success(result.message, {container: "#recruitment_circular_form_container", animate: false});
                        $('.scrollable-page').scrollTop(0); //scroll to top
                    }
                });

                setDatePicker("#date_of_birth");

                var uploadUrl = "<?php echo get_uri("recruitment_circulars/upload_file"); ?>";
                var validationUrl = "<?php echo get_uri("recruitment_circulars/validate_file"); ?>";
                var dropzone = attachDropzoneWithForm("#apply-circular-form-preview", uploadUrl, validationUrl);

                $("#submit-button").on("click", function () {
                    var fileName = $(".post-file-previews").find("input[type=hidden]").val();
                    $("#recruitment_file").val(fileName);
                });

                $(".upload-file-button").on("click", function () {
                    $("#recruitment_file-error").remove();
                });

                $('[data-bs-toggle="tooltip"]').tooltip();

                initScrollbar('#apply-circular-container-scrollbar', {
                    setHeight: $(window).height()
                });
            });
        </script>
    </body>
</html>
