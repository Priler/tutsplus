<?php

class Admin_Controller extends Controller {

  public function __construct()
  {
    // check if user have rights to access admin pages
    parent::__construct();
    if( user() && user('is_admin') == 1 )
    {} else
    {
        redirect('/');
    }

  }

  public function users($request)
  {
    load_private_module('xcrud');

    $xcrud = Xcrud::get_instance();
    $xcrud->table('users');

    if( isset($request['1']) )
    {
        if( $request['1'] == 'learners' )
        {
            $xcrud->table_name('Ученики');
        } else
        {
            $xcrud->table_name('Менторы');
        }
    } else
    {
        $xcrud->table_name('Пользователи');
    }

    $xcrud->language('ru');
    $xcrud->limit('30');
    $xcrud->limit_list('30, 50, 100');

    $xcrud->columns('name,profession,avatar,email,gender,age,money,account_type,join_date');
    $xcrud->fields('login,name,profession,avatar,email,gender,age,city,money,account_type,join_date');

    $xcrud->label('login', 'Логин');
    $xcrud->label('password', 'Пароль');
    $xcrud->label('name', 'Имя');
    $xcrud->label('avatar', 'Аватар');
    $xcrud->label('gender', 'Пол');
    $xcrud->label('age', 'Возраст');
    $xcrud->label('city', 'Город');
    $xcrud->label('email', 'Почта');
    $xcrud->label('money', 'Счёт');
    $xcrud->label('is_admin', 'Администратор?');
    $xcrud->label('account_type', 'Тип аккаунта');
    $xcrud->label('join_date', 'Дата регистрации');
    $xcrud->label('profession', 'Профессия');

    $xcrud->change_type('join_date', 'datetime');
    $xcrud->change_type('gender', 'radio', '', array('male' => 'Мужчина', 'female' => 'Женщина'));
    $xcrud->change_type('avatar', 'image', '', array(
        'width' => 300,
        'path'  => STATIC_DIR . '/uploads/avatars'));
    $xcrud->change_type('account_type', 'radio', '', array('0' => 'Ученик', '1' => 'Учитель'));
    $xcrud->change_type('money', 'price', array('suffix'=>' RUR'));

    if( isset($request['1']) )
    {
        if( $request['1'] == 'learners' )
        {
            $xcrud->where('account_type = 0');
        } else
        {
            $xcrud->where('account_type = 1');
        }
    }

    render('admin/xcrud', array('xcrud' => $xcrud->render()));
  }

  public function pages($request)
  {
    load_private_module('xcrud');

    $xcrud = Xcrud::get_instance();
    $xcrud->table('pages');
    $xcrud->table_name('Страницы');

    $xcrud->language('ru');
    $xcrud->limit('30');
    $xcrud->limit_list('30, 50, 100');

    $xcrud->label('title', 'Название');
    $xcrud->label('slug', 'URI');
    $xcrud->label('content', 'Контент');

    $xcrud->fields('title,slug,content');

    render('admin/xcrud', array('xcrud' => $xcrud->render()));
  }

}