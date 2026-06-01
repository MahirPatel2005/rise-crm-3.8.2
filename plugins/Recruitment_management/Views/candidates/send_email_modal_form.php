<?php echo form_open(get_uri("recruitment_candidates/send_email"), array("id" => "send-email-form", "class" => "general-form", "role" => "form")); ?>
<div id="send_invoice-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="candidate_id" value="<?php echo $candidate_info->id; ?>" />

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        echo form_input(array(
                            "id" => "subject",
                            "name" => "subject",
                            "class" => "form-control",
                            "placeholder" => app_lang("subject"),
                            "autofocus" => true,
                            "data-rule-required" => false,
                            "data-msg-required" => app_lang("field_required")
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class=" col-md-12">
                        <?php
                        echo form_textarea(array(
                            "id" => "message",
                            "name" => "message",
                            "class" => "form-control"
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="submit" class="btn btn-primary"><span data-feather="send" class="icon-16"></span> <?php echo app_lang('send'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#send-email-form").appForm({
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message, {duration: 10000});
                } else {
                    appAlert.error(result.message);
                }
            }
        });

        initWYSIWYGEditor("#message", {
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['hr', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview']]
            ],
            placeholder: "<?php echo app_lang('recruitment_add_a_message_here'); ?>"
        });

    });
</script>