<?php

function schema_parser($schema) {

  $result_schema = array();
  if( is_array($schema) )
  {

    // type
    if( !(isset($schema['type'])) )
    {
      if( isset($schema['options']) )
      {
        $schema['type'] = 'select';
      } else
      {
        $schema['type'] = 'input';
      }
    }

    // required
    if( !isset($schema['required']) )
    {
      $schema['required'] = false;
    }

    $result_schema = $schema;
  } else
  {

    $pieces = explode('|', $schema);
    $schema = array(
      'label' => array_shift($pieces)
    );

    // required
    if( $key = array_search('required', $pieces) )
    {
      unset($pieces[$key]);
      $schema['required'] = true;
    } else
    {
      $schema['required'] = false;
    }

    // type
    $field_types = array('input', 'textarea', 'checkbox', 'select', 'timestamp');
    foreach( $field_types as $ft )
    {
      if( in_array($ft, $pieces) )
      {
        $schema['type'] = $ft;
        unset($pieces[array_search($ft, $pieces)]);
      }
    }

    if( !isset($schema['type']) )
    {
      $schema['type'] = 'input';
    }

    $result_schema = $schema;

  }

  // sample value
  switch( $result_schema['type'] )
  {
    case 'input':
      $result_schema['sample_value'] = str_repeat('a', 255);
    break;

    case 'textarea':
      $result_schema['sample_value'] = str_repeat('Hello World! ', 100);
    break;

    case 'checkbox':
      $result_schema['sample_value'] = true;
    break;

    case 'select':
      $result_schema['sample_value'] = 'hello_world';
    break;

    case 'timestamp':
      $result_schema['sample_value'] = 99999999999;
    break;
  }

  // return
  return $result_schema;

}

function get_protocol()
{
  if( isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on' )
  {
    return 'https://';
  } else
  {
    return 'http://';
  }
}