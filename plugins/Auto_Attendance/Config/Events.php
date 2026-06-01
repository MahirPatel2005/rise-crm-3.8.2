<?php

namespace Auto_Attendance\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
    helper("auto_attendance_general");
});