<?php recruitment_load_css(array(PLUGIN_URL_PATH . "Recruitment_management/assets/css/recruitment-style.css")); ?>
<div class="circular-container">
    <div class="row">
        <?php foreach ($recruitment_circulars as $circular) { ?>
            <div class="col-12 col-md-6 col-lg-4">
                <a href="<?php echo get_uri("recruitment_circulars/public_preview/$circular->id/$circular->public_key"); ?>" target="_blank" class="text-default">
                    <div class="card card-bordered">
                        <div class="card-block">
                            <h5 class="circular-card-title mb-1"><?php echo $circular->job_title; ?></h5>
                            <div class="circular-company-title font-12"><?php echo $circular->job_type_title . " <i data-feather='more-vertical' class='icon-14'></i> " . $circular->job_position_title; ?></div>

                            <div class="d-flex flex-wrap justify-content-between">
                                <span class="fw-400 fs-14"><i data-feather="map-pin" class="icon-14"></i> <?php echo $circular->address; ?></span>
                                <span class="fw-400 fs-14"><i data-feather="dollar-sign" class="icon-14"></i> <?php echo $circular->salary; ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php } ?>  
    </div>
</div>