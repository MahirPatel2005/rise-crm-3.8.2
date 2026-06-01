<?php

namespace Timecamp_Integration\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
    helper("timecamp_integration_general_helper");
});
