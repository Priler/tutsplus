<?php

function load_private_module($name) {

  $requested_private_module = dirname(__FILE__) . '/private/' . $name . '.module.php';
  if( file_exists($requested_private_module) )
  {
    require $requested_private_module;
  } else
  {
    return false;
  }

}