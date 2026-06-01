<?php
$status_dropdown = array();
foreach ($stages as $status) {
    $status_dropdown[] = array("id" => $status->id, "text" => $status->title);
}
?>


<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $('body').on('click', '[data-act=update-recruitment-candidate-status]', function () {
            $(this).appModifier({
                value: $(this).attr('data-value'),
                actionUrl: '<?php echo_uri("recruitment_candidates/save_candidate_status") ?>/' + $(this).attr('data-id'),
                select2Option: {data: <?php echo json_encode($status_dropdown) ?>},
                onSuccess: function (response, newValue) {
                    if (response.success) {
                        $("#recruitment-candidates-table").appTable({newData: response.data, dataId: response.id});
                    }
                }
            });

            return false;
        });
    });
</script>