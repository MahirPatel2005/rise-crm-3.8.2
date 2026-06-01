<?php

namespace Flexiblebackup\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
    helper(['flexiblebackup', 'general']);
});
