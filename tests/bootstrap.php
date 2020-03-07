<?php

use hiqdev\composer\config\Builder;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Don't do it in production, assembling takes it's time
Builder::rebuild();
