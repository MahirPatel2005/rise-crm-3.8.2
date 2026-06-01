<div class="bg-white  p15 no-border m0 rounded-bottom">
    <span><?php echo app_lang("status") . ": " . $circular_status_label; ?></span>
    <?php if ($recruiters) { ?>
        <span class="ml10"><?php echo app_lang("recruitment_recruiters") . ": " . $recruiters; ?></span>
    <?php } ?>
</div>