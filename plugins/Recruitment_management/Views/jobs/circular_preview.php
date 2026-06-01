<div id="page-content" class="<?php echo isset($is_editor_preview) ? "bg-all-white" : "page-wrapper"; ?> clearfix">
    <?php
    load_css(array(
        "assets/css/invoice.css",
    ));

    load_js(array(
        "assets/js/signature/signature_pad.min.js",
    ));
    ?>

    <div class="invoice-preview proposal-preview">
        <?php
        if (!isset($is_editor_preview)) {

            $action_buttons = "<div class='clearfix float-end'>";

            if ($show_close_preview) {
                echo "<div class='text-center'>" . anchor("recruitment_management/view_circular/" . $circular_info->id, app_lang("close_preview"), array("class" => "btn btn-default round mb20 mr5")) . "</div>";
            }

            if ($login_user->user_type === "staff") {
                $action_buttons .= "<div class='float-start'>" . anchor(get_uri("recruitment_circulars/public_preview/" . $circular_info->id . "/" . $circular_info->public_key), "<i data-feather='external-link' class='icon-16'></i> " . app_lang('recruitment_circular') . " " . app_lang("url"), array("class" => "btn btn-default round mr5")) . "</div>";
            }

            $action_buttons .= "</div>";

            if ($login_user->user_type === "staff") {
                ?>

                <div class = "card  p15 no-border">

                    <div class="clearfix">
                        <div class="mr15 strong float-start">
                            <?php
                            if ($circular_info->status == "draft" || $circular_info->status == "inactive") {
                                echo ajax_anchor(get_uri("recruitment_management/update_circular_status/$circular_info->id/active"), "<i data-feather='check-circle' class='icon-16'></i> " . app_lang('recruitment_mark_as_active'), array("class" => "btn btn-success mr15", "title" => app_lang('recruitment_mark_as_active'), "data-reload-on-success" => "1"));
                            } else {
                                echo ajax_anchor(get_uri("recruitment_management/update_circular_status/$circular_info->id/inactive"), "<i data-feather='check-circle' class='icon-16'></i> " . app_lang('mark_as_inactive'), array("class" => "btn btn-danger mr15", "title" => app_lang('mark_as_inactive'), "data-reload-on-success" => "1"));
                            }
                            ?>
                        </div>

                        <?php echo $action_buttons; ?>
                    </div>
                </div>

                <?php
            }
        }
        ?>

        <div class="bg-white mt15">
            <?php
            echo $recruitment_circular_preview;
            ?>
        </div>

    </div>
</div>