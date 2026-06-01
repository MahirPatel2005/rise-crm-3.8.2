<span style="font-size:20px; font-weight: bold;background-color: <?php echo $color; ?>; color: #fff;">&nbsp;<?php echo strtoupper(app_lang('recruitment_circular')) . " " . '#' . $circular_info->id; ?>&nbsp;</span>
<div style="line-height: 10px;"></div>
<span><?php echo app_lang("start_date") . ": " . format_to_date($circular_info->start_date, false); ?></span><br />
<span><?php echo app_lang("deadline") . ": " . format_to_date($circular_info->deadline, false); ?></span>