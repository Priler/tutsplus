<?php

// Require APP class
require( FRAMEWORK_DIR . '/core/app.class.php' );

// Require core files
require( FRAMEWORK_DIR . '/core/system/base_controller.class.php' );
require( FRAMEWORK_DIR . '/core/system/forms.class.php' );
require( FRAMEWORK_DIR . '/core/system/views.class.php' );
require( FRAMEWORK_DIR . '/core/system/models.class.php' );
require( FRAMEWORK_DIR . '/core/system/controller.class.php' );

// Require composer autoload
if( file_exists(FRAMEWORK_DIR . '/composer/vendor/autoload.php') )
{
  require( FRAMEWORK_DIR . '/composer/vendor/autoload.php' );
}

// Define more constants
define('APPS_DIR', BASE_DIR . '/apps');
define('TWIG_CACHE_DIR', FRAMEWORK_DIR . '/cache');

// Require all modules
$available_modules = glob( FRAMEWORK_DIR . '/modules/*.module.php' );
foreach( $available_modules as $module )
{
  require $module;
}

define('HTTP_HOST', Http::protocol() . $_SERVER['HTTP_HOST']);

// Start the fun
require( FRAMEWORK_DIR . '/core/bootstrap.php' );