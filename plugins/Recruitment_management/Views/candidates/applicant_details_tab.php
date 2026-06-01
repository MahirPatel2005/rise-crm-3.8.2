<div class="tab-content">
    <div class="general-form dashed-row white"><div class="card">
            <div class=" card-header">
                <h4> <?php echo app_lang('recruitment_applicant_details'); ?></h4>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <div class="form-group">
                        <div class="row">
                            <label for="first_name" class="col-md-3"><?php echo app_lang('first_name'); ?></label>
                            <div class="col-md-9">
                                <div class="form-control"><?php echo $model_info->first_name; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="last_name" class="col-md-3"><?php echo app_lang('last_name'); ?></label>
                            <div class="col-md-9">
                                <div class="form-control"><?php echo $model_info->last_name; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="email" class="col-md-3"><?php echo app_lang('email'); ?></label>
                            <div class="col-md-9">
                                <div class="form-control"><?php echo $model_info->email; ?></div>
                            </div>
                        </div>
                    </div>   
                    <div class="form-group">
                        <div class="row">
                            <label for="skype" class="col-md-3"><?php echo app_lang("date_of_birth"); ?></label>
                            <div class="col-md-9">
                                <div class="form-control"><?php echo $model_info->dob ? $model_info->dob : ""; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="skype" class="col-md-3"><?php echo app_lang("gender"); ?></label>
                            <div class="col-md-9">
                                <div class="form-control"><?php echo app_lang($model_info->gender) ? app_lang($model_info->gender) : ""; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="phone" class="col-md-3"><?php echo app_lang('phone'); ?></label>
                            <div class="col-md-9">
                                <div class="form-control"><?php echo $model_info->phone ? $model_info->phone : ""; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="address" class="col-md-3"><?php echo app_lang('address'); ?></label>
                            <div class="col-md-9">
                                <div class="form-control"><?php echo $model_info->address ? $model_info->address : ""; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="education" class="col-md-3"><?php echo app_lang('recruitment_education'); ?></label>
                            <div class="col-md-9">
                                <div class="form-control"><?php echo $model_info->education ? $model_info->education : ""; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="work_experience" class="col-md-3"><?php echo app_lang('recruitment_work_experience'); ?></label>
                            <div class="col-md-9">
                                <div class="form-control"><?php echo $model_info->work_experience ? $model_info->work_experience : ""; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>