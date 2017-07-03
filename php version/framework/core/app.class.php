<?php

class App {

  static public function init()
  {
    $GLOBALS['app_settings'] = require( BASE_DIR . '/settings.php' );
  }

  static public function &settings()
  {
    return $GLOBALS['app_settings'];
  }

}