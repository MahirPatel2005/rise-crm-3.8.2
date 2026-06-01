<?php recruitment_load_css(array(PLUGIN_URL_PATH . "Recruitment_management/assets/css/recruitment-style.css")); ?>

<div id="page-content" class="clearfix">
    <div style="max-width: 1000px; margin: auto;">
        <div class="page-title clearfix mt15">
            <h1><?php echo app_lang('recruitment_circular_info') . " " . '#' . $circular_info->id; ?></h1>
            <div class="title-button-group">
                <span class="dropdown inline-block mt15">
                    <button class="btn btn-info text-white dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true">
                        <i data-feather="tool" class="icon-16"></i> <?php echo app_lang('actions'); ?>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li role="presentation"><?php echo anchor(get_uri("recruitment_management/preview/" . $circular_info->id . "/1"), "<i data-feather='search' class='icon-16'></i> " . app_lang('recruitment_job_preview'), array("title" => app_lang('recruitment_job_preview'), "target" => "_blank", "class" => "dropdown-item")); ?> </li>
                        <li role="presentation"><?php echo anchor(get_uri("recruitment_circulars/public_preview/" . $circular_info->id . "/" . $circular_info->public_key), "<i data-feather='external-link' class='icon-16'></i> " . app_lang('recruitment_circular') . " " . app_lang("url"), array("target" => "_blank", "class" => "dropdown-item")); ?> </li>
                        <li role="presentation" class="dropdown-divider"></li>
                        <li role="presentation"><?php echo modal_anchor(get_uri("recruitment_management/job_modal_form"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('recruitment_edit_job'), array("title" => app_lang('recruitment_edit_job'), "data-post-id" => $circular_info->id, "role" => "menuitem", "tabindex" => "-1", "class" => "dropdown-item")); ?> </li>
                        <?php
                        if ($circular_info->status == "draft" || $circular_info->status == "inactive") {
                            ?>
                            <li role="presentation"><?php echo ajax_anchor(get_uri("recruitment_management/update_circular_status/$circular_info->id/active"), "<i data-feather='check-circle' class='icon-16'></i> " . app_lang('recruitment_mark_as_active'), array("class" => "dropdown-item", "title" => app_lang('recruitment_mark_as_active'), "data-reload-on-success" => "1")); ?> </li>
                            <?php
                        } else {
                            ?>
                            <li role="presentation"><?php echo ajax_anchor(get_uri("recruitment_management/update_circular_status/$circular_info->id/inactive"), "<i data-feather='check-circle' class='icon-16'></i> " . app_lang('mark_as_inactive'), array("class" => "dropdown-item", "title" => app_lang('mark_as_inactive'), "data-reload-on-success" => "1")); ?> </li>
                            <?php
                        }
                        ?>
                    </ul>
                </span>
            </div>
        </div>
        <div id="circular-status-bar">
            <?php echo view("Recruitment_management\Views\jobs\\circular_status_bar"); ?>
        </div>
        <div class="mt15">
            <div class="card no-border clearfix ">
                <ul data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
                    <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#job-info"><?php echo app_lang("recruitment_circular_info"); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_management/editor/" . $circular_info->id); ?>" data-bs-target="#job-preview-editor"><?php echo app_lang("recruitment_job_preview_editor"); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_management/preview/" . $circular_info->id . "/0/1"); ?>" data-bs-target="#job-preview" data-reload="true"><?php echo app_lang("preview"); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("recruitment_management/candidates/" . $circular_info->id); ?>" data-bs-target="#job-candidates"><?php echo app_lang("recruitment_candidates"); ?></a></li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade" id="job-info">
                        <div class="p15 b-t mb15">
                            <div class="clearfix p20">
                                <!-- small font size is required to generate the pdf, overwrite that for screen -->
                                <style type="text/css"> .invoice-meta {
                                        font-size: 100% !important;
                                    }</style>

                                <?php
                                $color = get_recruitment_setting("recruitment_job_circular_color");
                                if (!$color) {
                                    $color = get_setting("invoice_color");
                                }
                                $style = get_setting("invoice_style");
                                ?>
                                <?php
                                $data = array(
                                    "color" => $color,
                                    "circular_info" => $circular_info
                                );
                                ?>

                                <div class="row">
                                    <div class="col-md-8 mb15">
                                        <?php echo view('Recruitment_management\Views\jobs\\circular_parts\circular_info_1', $data); ?>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <?php echo view('Recruitment_management\Views\jobs\\circular_parts\circular_info_2', $data); ?>
                                    </div>
                                </div>
                                <div>
                                    <span><?php echo $circular_info->description; ?></span>
                                </div>
                            </div>

                            <div class="clearfix">
                                <div class="card">
                                    <div class="card-body">
                                        <?php
                                        $columns_data = array();
                                        foreach ($hiring_stage_info as $hiring_stage) {
                                            $columns_data[$hiring_stage->hiring_stage_id] = $hiring_stage->total;
                                        }
                                        ?>
                                        <?php foreach ($hiring_stage_columns as $column) { ?>
                                            <div class="recruitment-widget">
                                                <p class="font-20 m0"><?php echo get_array_value($columns_data, $column->id) ? get_array_value($columns_data, $column->id) : 0; ?></p>
                                                <p class="font-14 m0"><?php echo $column->title; ?></p>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="job-preview-editor"></div>
                    <div role="tabpanel" class="tab-pane fade" id="job-preview"></div>
                    <div role="tabpanel" class="tab-pane fade" id="job-candidates"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    RELOAD_CIRCULAR_VIEW_AFTER_UPDATE = true;

    $(document).ready(function () {
        $("body").on("click", "#circular-save-and-show-btn", function () {
            $(this).trigger("submit");

            setTimeout(function () {
                $("[data-bs-target='#job-preview']").trigger("click");
            }, 400);
        });

        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
