<?php foreach (getBackupFileOptions() as $key => $value) { ?>
    <div class="checkbox mt-1">
        <?php get_setting('include_'.$value) ?? 1; ?>
        <input type="checkbox" class="form-check-input" name="include_<?php echo $value; ?>" id="include_<?php echo $value; ?>" value="1" <?php echo (1 == get_setting('include_'.$value)) ? 'checked' : ''; ?> />
        <strong for="include_<?php echo $value; ?>"><?php echo app_lang($value); ?></strong>
    </div>
<?php } ?>