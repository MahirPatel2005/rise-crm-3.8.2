<div class="tab-content">
    <div class="card rounded-0">
        <div class="tab-title clearfix">
            <h4><?php echo app_lang('events'); ?></h4>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("recruitment_candidates/add_event_modal_form"), "<i data-feather='calendar' class='icon-16'></i> " . app_lang('add_event'), array("class" => "btn btn-default", "title" => app_lang('add_event'), "data-post-candidate_id" => $model_info->id)); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="recruitment-candidate-events-table" class="display" width="100%">            
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#recruitment-candidate-events-table").appTable({
            source: '<?php echo_uri("recruitment_candidates/event_list_data/" . $model_info->id) ?>',
            order: [[0, "dasc"]],
            columns: [
                {visible: false, searchable: false},
                {title: "<?php echo app_lang("date") ?>", "class": "text-center w100", "iDataSort": 0},
                {title: "<?php echo app_lang("event") ?>"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [1, 2, 3, 4]
        });
    });
</script>