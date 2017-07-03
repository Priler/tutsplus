<?php

App::init();
$app_settings = App::settings();
if( $app_settings['database']['use'] ) {
  require dirname(__FILE__) . '/libs/rb.php';
  R::setup(
    $app_settings['database']['connect'],
    $app_settings['database']['username'],
    $app_settings['database']['password']
  );

  R::freeze($app_settings['production']);
}