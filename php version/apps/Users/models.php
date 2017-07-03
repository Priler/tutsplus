<?php

class Users_Models extends Models {

  protected
    $user_schema;
  public function __construct()
  {
    parent::__construct();
    $this->user_schema = array(
      'email' => 'Email|required',
      'password' => 'Пароль|required',
      'join_date' => 'Дата регистрации|timestamp',
      'type' => array(
        'label' => 'Тип пользователя',
        'options' => array(
          'site_admin' => 'Главный администратор',
          'company_admin' => 'Администратор компании',
        )),
      'hash' => 'hash string',
      'ban' => 'Забанен|checkbox'
    );
  }

  public function migrations()
  {
    return array('users' => $this->user_schema);
  }

}