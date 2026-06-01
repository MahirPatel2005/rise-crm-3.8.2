<div class="tab-content">
    <div class="general-form dashed-row white">
        <div class="card">
            <div class=" card-header">
                <h4> <?php echo app_lang('recruitment_attachments'); ?></h4>
            </div>
            <div class="card-body">
                <div class="form-control">
                    <?php
                    $files = @unserialize($model_info->resume);
                    if ($files && is_array($files) && count($files)) {
                        ?>
                        <div class="clearfix">
                            <div class="col-md-12">
                                <div class="float-start"><?php
                                    foreach ($files as $key => $value) {
                                        $file_name = get_array_value($value, "file_name");
                                        echo "<div>";
                                        echo js_anchor(remove_file_prefix($file_name), array("data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("recruitment_candidates/file_preview/" . $model_info->id . "/" . $key . "/attachment_tab")));
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                                <div class="float-end"><?php echo anchor(get_uri("recruitment_candidates/download_files/" . $model_info->id), "<i data-feather='download' class='icon-16'></i>", array("title" => app_lang("download"))); ?></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div> 
    </div>
</div>