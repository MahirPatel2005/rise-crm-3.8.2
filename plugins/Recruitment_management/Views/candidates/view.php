<?php recruitment_load_css(array(PLUGIN_URL_PATH . "Recruitment_management/assets/css/recruitment-style.css")); ?>

<?php echo view("includes/cropbox"); ?>
<div id="page-content" class="clearfix">
    <div class="bg-white clearfix">
        <div class="container-fluid b-b">
            <div class="row">
                <div class="col-md-7">
                    <div class="row p20">

                        <div class="box ps-0" id="profile-image-section">
                            <div class="box-content w100 text-center">
                                <div class="custom-avatar-lg m-auto"><div class="text-profile-image"><?php echo $candidate_info->first_name[0] . $candidate_info->last_name[0] ?></div></div> 
                            </div> 

                            <div class="box-content ps-4">
                                <h2><?php echo $candidate_info->first_name . " " . $candidate_info->last_name; ?></h2>

                                <p class="text-off mb-0"><?php echo $candidate_info->circular_title; ?></p> 
                                <p class="text-off mb-0"><?php echo app_lang("recruitment_applied_at") . " " . $candidate_info->applied_at; ?></p> 
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-md-5 p20 text-center cover-widget">
                    <div class="clearfix mt-3">
                        <div class="float-end">
                            <?php echo modal_anchor(get_uri("recruitment_candidates/send_email_modal_form/" . $candidate_info->id), "<i data-feather='send' class='icon-16'></i> " . app_lang('recruitment_send_email'), array("class" => "btn btn-default ml5", "title" => app_lang('recruitment_send_email_to') . " " . $candidate_info->first_name . " " . $candidate_info->last_name)); ?>
                            <?php echo js_anchor($candidate_info->hiring_stage, array('title' => app_lang('recruitment_hiring_stage'), "data-bs-toggle" => "tooltip", "class" => "btn btn-default ml10", "data-id" => $candidate_info->id, "data-value" => $candidate_info->hiring_stage_id, "data-act" => "update-recruitment-candidate-status")); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ul id="candidate-view-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs scrollable-tabs rounded-0" role="tablist">
        <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_candidates/applicant_details_tab/" . $candidate_info->id); ?>" data-bs-target="#tab-applicant-details"> <?php echo app_lang('recruitment_applicant_details'); ?></a></li>
        <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_candidates/attachments/" . $candidate_info->id); ?>" data-bs-target="#tab-attachments"> <?php echo app_lang('recruitment_attachments'); ?></a></li>
        <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_candidates/events/" . $candidate_info->id); ?>" data-bs-target="#tab-events"> <?php echo app_lang('events'); ?></a></li>
        <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_candidates/notes/" . $candidate_info->id); ?>" data-bs-target="#tab-notes"> <?php echo app_lang('notes'); ?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" id="tab-applicant-details"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-attachments"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-events"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-notes"></div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>

<?php
echo view("Recruitment_management\Views\candidates\update_recruitment_candidate_script");
?>