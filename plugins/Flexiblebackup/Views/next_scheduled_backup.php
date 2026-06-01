<div class="card-body">
    <div class="row">
        <!-- Files Backup Section -->
        <div class="col-md-6">
            <div class="card shadow p-3 bg-body rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h5><?php echo app_lang('files'); ?></h5>
                        </div>
                        <div class="col-md-8">
                            <div class="d-grid d-md-flex justify-content-md-end">
                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <button type="button" class="btn btn-primary"><?php echo app_lang('time_now'); ?></button>
                                    <button type="button" class="btn btn-primary"><?php echo getCurrentDateTime(); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p>
                        <?php echo $files_backup_schedule ? "<span class='text-success'>".date('D, F j, Y H:i', $files_backup_schedule).'</span>' : app_lang('nothing_currently_scheduled'); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Database Backup Section -->
        <div class="col-md-6">
            <div class="card shadow p-3 bg-body rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h5><?php echo app_lang('database'); ?></h5>
                        </div>
                        <div class="col-md-8">
                            <div class="d-grid d-md-flex justify-content-md-end">
                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <button type="button" class="btn btn-primary"><?php echo app_lang('time_now'); ?></button>
                                    <button type="button" class="btn btn-primary"><?php echo getCurrentDateTime(); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p>
                        <?php echo $database_backup_schedule ? "<span class='text-success'>".getCurrentDateTime($database_backup_schedule).'</span>' : app_lang('nothing_currently_scheduled'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
