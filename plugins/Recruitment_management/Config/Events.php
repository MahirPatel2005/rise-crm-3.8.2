<?php

namespace Recruitment_management\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
    helper('recruitment_management_general');
});
