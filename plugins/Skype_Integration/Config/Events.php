<?php

namespace Skype_Integration\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
    helper("skype_integration_general");
});