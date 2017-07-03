<?php

error_reporting(E_ERROR | E_PARSE);
define('BASE_DIR', getcwd());
define('FRAMEWORK_DIR', BASE_DIR . '/framework');
define('TEMPLATE_DIR', BASE_DIR . '/templates');
define('STATIC_DIR', BASE_DIR . '/static');

// Start the fun
require FRAMEWORK_DIR . '/starter.php';

App::init();
$app = new PrilerEngine( App::settings() );