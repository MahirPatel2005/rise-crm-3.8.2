<?php

namespace Banner_Manager\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
    helper("banner_manager_general");
});