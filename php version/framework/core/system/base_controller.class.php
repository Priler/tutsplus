<?php

class Base_Controller {

  public
    $data;

  public function __construct()
  {
    $this->data = array();
    $this->data['connections'] = array();
  }

  public function load($what, $as = false)
  {
    $what = ucfirst(strtolower($what));
    $what_explode = explode('/', $what);
    $what_name = array_shift($what_explode);
    $what_type = array_pop($what_explode);
    require_once(APPS_DIR . '/' . $what . '.php');
    $class_name = $what_name . '_' . ucfirst($what_type);

    if( $as )
    {
      $this->data['connections'][$what_type][$as] = new $class_name();
    } else
    {
      $this->data['connections'][$what_type][strtolower($what_name)] = new $class_name();
    }   
  }

  public function __get($key)
  {
    if( array_key_exists($key, $this->data))
    {
      $instance =& $this->data['connections'];
      return (object)$instance;
    }
  }

}