<?php

namespace Jitsi_Integration\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
    helper("jitsi_integration_general");
});