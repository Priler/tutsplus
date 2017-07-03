<?php

function filter_changed_field_value($str)
{
  $changes = array(
    'yes' => 'Да/Есть',
    'no' => 'Нет',
    'male' => 'Мужчина',
    'female' => 'Женщина'
  );
  return strtr($str, $changes);
}

function what_changed_history($value, $fieldname, $primary_key, $row, $xcrud)
{

  $worker_schema = array(
    'name' => 'Имя|required',
    'surname' => 'Фамилия|required',
    'patronymic' => 'Отчество|required',
    'birth_date' => 'Дата рождения|required',
    'gender' => 'Пол|required',
    'weight' => 'Вес',
    'height' => 'Рост',
    'photo' => 'Фотография',

    'family_status' => array(
      'label' => 'Семейное положение',
      'options' => 'холост,женат/замужем,гр. брак,вдовец,разведен'),

    'kids' => array(
      'label' => 'Дети',
      'options' => 'Да,Нет'),

    'place_any' => 'Любое место проживания|checkbox',
    'country' => 'Страна|required',
    'city' => 'Город|required',
    'district' => 'Район|required',

    'driver_license' => array(
      'label' => 'Водительские права',
      'options' => 'Есть,Не имею'),

    'private_car' => 'Наличие личного легкового авто|checkbox',
    'army_status' => 'Отношение к воинской обязанности|required|textarea',

    'criminal_status' => 'Привлекались ли к уголовной ответственности, когда, за что, каким органом, мера наказания|required|textarea',

    'additional_information' => 'Дополнительные сведения о себе (спортивные разряды, вид спорта, государственные награды, когда и чем награжденны, не страдаете ли эпилепсией и психическими расстройствами)|textarea|required',

    'contacting_time' => array(
      'label' => 'Связываться можно',
      'options' => 'с 9 до 23,с 8 до 24,в любое время'),

    'status' => array(
      'label' => 'Статус',
      'options' => array(
        'Активный для всех',
        'Не активный',
        'Активный для всех кроме'
      )
    ),

    'exclude_companies' => array(
      'label' => 'Скрыть работника для компаний',
      'options' => array()
    )

  );

  $result = '';
  $changed = json_decode($value, true, 512, JSON_UNESCAPED_UNICODE);
  foreach( $changed as $k => $v )
  {
    $field = schema_parser($worker_schema[$k]);
    $result .= '<div class="history_item"><div class="history_field">'.$field['label'].'</div>'
            . '<div class="history_before">Было: ' . filter_changed_field_value($v['before']) . '</div>'
            . '<div class="history_after">Стало: ' . filter_changed_field_value($v['after']) . '</div></div>';
  }
  return $result;
}

function log_workers_changes($postdata, $xcrud)
{

  $db = Xcrud_db::get_instance();

  $query = "SELECT * FROM `workers` WHERE id = " . (int)$xcrud;
  $db->query($query);
  $workerObj = $db->result();
  $workerObj = $workerObj['0'];

  unset($workerObj['id']);
  $before = $workerObj;
  $after = $postdata->to_array();

  $after_rebuilded = array();
  foreach( $after as $k => $v )
  {
    $after_rebuilded[str_replace('workers.', '', $k)] = $v;
  }
  $after = $after_rebuilded;

  if( $after['exclude_companies'] == '' ){$after['exclude_companies'] = null;}
  if( $after['private_car'] == '0' ){$after['private_car'] = null;}
  if( $after['army_status'] == '' ){$after['army_status'] = null;}
  if( $after['criminal_status'] == '' ){$after['criminal_status'] = null;}
  if( $after['additional_information'] == '' ){$after['additional_information'] = null;}
  $after['birth_date'] = date('Y-m-d', $after['birth_date']);

  // find difference
  $diff = false;
  $diff_array = array();
  foreach( $before as $k => $v )
  {
    if( $before[$k] != $after[$k] )
    {
      $diff = true;
      $diff_array[$k] = array('before' => $before[$k], 'after' => $after[$k]);
    }
  }

  if( $diff )
  {
    $before = json_encode($before, JSON_UNESCAPED_UNICODE);
    $after = json_encode($after, JSON_UNESCAPED_UNICODE);
    $diff_array = json_encode($diff_array, JSON_UNESCAPED_UNICODE);

    $query = "INSERT INTO `history`(`record_id`, `table_name`, `before`, `after`, `changes`, `pub_date`) VALUES (".$xcrud.", 'workers', '".$before."', '".$after."', '".$diff_array."', '".date('Y-m-d H:m:s', time())."')";
    $db->query($query);
  }
}

function before_test($postdata, $xcrud) {
  print_r($postdata);
  exit();
}

function render_time($value, $fieldname, $primary_key, $row, $xcrud) {
  return date('d.m.Y H:m', $value);
}

if( !function_exists('schema_parser') )
{

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

}