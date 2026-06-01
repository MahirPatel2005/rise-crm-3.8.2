<?php

namespace Microsoft_Teams_Integration\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
    helper("microsoft_teams_integration_general");
});