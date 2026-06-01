<div class="modal-body clearfix general-form">
    <div class="container-fluid">
        <div class="form-group">
            <div  class="col-md-12 notepad-title">
                <strong><?php echo $model_info->title; ?></strong>
            </div>
        </div>
        <div class="col-md-12 mb15 notepad">
            <?php
            echo nl2br(link_it($model_info->description));
            ?>
        </div>

        <div class="col-md-12 mt15">
            <?php
            if($model_info->files){
                $files = unserialize($model_info->files);
                $total_files = count($files);
                echo view("includes/timeline_preview", array("files" => $files));
            }
            ?>
        </div>

    </div>
</div>

<div class="modal-footer">
    <?php
    if ($model_info->created_by == $login_user->id || $login_user->is_admin) {
        echo modal_anchor(get_uri("recruitment_candidates/add_note_modal_form"), "<i data-feather='edit-2' class='icon-16'></i> " . app_lang('edit_note'), array("class" => "btn btn-default", "data-post-id" => $model_info->id, "title" => app_lang('edit_note')));
    }
    ?>
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
</div>