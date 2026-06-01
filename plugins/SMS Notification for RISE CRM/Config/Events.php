<?php

namespace Config;

use CodeIgniter\Events\Events;

Events::on('post_controller_constructor', function () {
    helper('sms_general');
});
