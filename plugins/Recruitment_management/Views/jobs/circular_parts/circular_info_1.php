<h3 class="mt-0"><?php echo $circular_info->job_title; ?></h3>

<div class="mb10">
    <span class="mr10"><i data-feather="clock" class="icon-14"></i> <?php echo $circular_info->job_type_title; ?></span>
    <span class="mr10"><i data-feather="home" class="icon-14"></i> <?php echo $circular_info->job_position_title; ?></span>
    <span><i data-feather="award" class="icon-14"></i> <?php echo $circular_info->department_title; ?></span>
</div>
<div class="mb10">
    <span class="mr10"><i data-feather="dollar-sign" class="icon-14"></i> <?php echo $circular_info->salary; ?></span>
    <span class="mr10"><i data-feather="map-pin" class="icon-14"></i> <?php echo $circular_info->address; ?></span>
</div>
<div>
    <span><i data-feather="users" class="icon-14"></i> <?php echo app_lang("recruitment_quantity_to_be_required") . ": " . $circular_info->quantity_to_be_required; ?></span>
</div>