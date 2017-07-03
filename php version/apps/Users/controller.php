<?php

use Respect\Validation\Validator;
use Respect\Validation\Rules;

class Users_Controller extends Controller {

  public function profile($request)
  {
    $requested_user = R::findOne('users', "id = ? OR profile_link = ?", array($request['1'], $request['1']));

    if( ! $requested_user )
    {
      drop_404();
    }

    render('profile', array(
      'profile' => user_purify($requested_user->export())));
  }

  public function settings($request)
  {
    $data = $_POST;
    if( isset($data['process']) )
    {

      $errors = array();
      $user = R::load('users', user('id'));
      $changes = false;
      $error = '';
      $success = '';

      /**
       * Name.
       */
      if( $data['name'] != user('name') )
      {
        $user->name = $data['name'];
        $changes = true;
      }

      /**
       * Gender.
       */
      if( $data['gender'] != user('gender') )
      {
        if( in_array($data['gender'], array('male', 'female')) )
        {
          $user->gender = $data['gender'];
          $changes = true;
        }
      }

      /**
       * Age.
       */
      if( $data['age'] != user('age') )
      {
        if( (int)$data['age'] > 5 && (int)$data['age'] < 150 )
        {
          $user->age = (int)$data['age'];
          $changes = true;
        } else
        {
          $errors[] = 'Возраст указан не верно.';
        }
      }

      /**
       * City.
       */
      if( $data['city'] != user('city') )
      {
        $user->city = $data['city'];
        $changes = true;
      }

      /**
       * Profession.
       */
      if( $data['profession'] != user('profession') )
      {
        $user->profession = mb_substr($data['profession'], 0, 15, 'utf-8');
        $changes = true;
      }

      /**
       * Profile Public Url.
       */
      if( $data['profile_link'] != user('profile_link') )
      {
        if( R::count('users', "profile_link = ?", array($data['profile_link'])))
        {
          $errors[] = 'Такая ссылка на профиль уже занята.';
        } else
        {
          $user->profile_link = $data['profile_link'];
        }
        $changes = true;
      }

      user_force($user);
      $form_data = $user->export();
      if( $changes )
      {
        if( !empty($errors) )
        {
          $error = array_shift($errors);
        } else
        {
          R::store($user);
          $success = 'Настройка успешно изменены.';
        }
      }

    } else
    {
      $form_data = user();
    }

    render('settings', array(
      'profile' => user(),
      'form'    => $form_data,
      'error'   => $error,
      'success' => $success));
  }

  public function settings_profile($request)
  {
    $data = $_POST;
    if( isset($data['process']) )
    {

      $errors = array();
      $user = R::load('users', user('id'));
      $changes = false;
      $error = '';
      $success = '';

      /**
       * Avatar.
       */
      if( $_FILES['avatar'] )
      {
        $file_info = pathinfo($_FILES['avatar']['name']);

        if( in_array($file_info['extension'], array('jpg', 'jpeg', 'png', 'gif', 'bmp')) )
        {
          if( ($_FILES['avatar']['size'] / 1024) > (1024 * 10) )
          {
            $errors[] = 'Нельзя загружать изображения больше 10 МБ размером.';
          } else
          {
            $avatars_media_dir = '/static/uploads/avatars/';
            $avatars_dir = BASE_DIR . $avatars_media_dir;
            $new_fname = FileSystem::getUniqueFilename($file_info['basename'], $avatars_dir);
            move_uploaded_file($_FILES['avatar']['tmp_name'], $avatars_dir.$new_fname);

            $image = new ImageResize();
            $image->load($avatars_dir.$new_fname);
            $image->resizeToWidth(207);
            $image->save($avatars_dir.$new_fname);

            @unlink($avatars_dir . $user->avatar);
            $user->avatar = $new_fname;

            $changes = true;
          }
        } else
        {
          $errors[] = 'Загружать разрешено только изображения!';
        }
      }

      user_force($user);
      $form_data = $user->export();
      if( $changes )
      {
        if( !empty($errors) )
        {
          $error = array_shift($errors);
        } else
        {
          R::store($user);
          $success = 'Настройка успешно изменены.';
        }
      }

    } else
    {
      $form_data = user();
    }

    render('settings_profile', array(
      'profile' => user(),
      'form'    => $form_data,
      'error'   => $error,
      'success' => $success));
  }

  public function settings_password($request)
  {
    $data = $_POST;
    if( isset($data['process']) )
    {

      $errors = array();
      $user = R::load('users', user('id'));
      $changes = false;
      $error = '';
      $success = '';

      /**
       * Password.
       */
      if( $data['new_password'] )
      {
        if( !password_verify($data['new_password'], $user->password) )
        {
          $errors[] = 'Текущий пароль введён неверно.';
        } else
        {
          $user->password = password_hash($data['new_password'], PASSWORD_DEFAULT);
        }
        $changes = true;
      }

      user_force($user);
      $form_data = $user->export();
      if( $changes )
      {
        if( !empty($errors) )
        {
          $error = array_shift($errors);
        } else
        {
          R::store($user);
          $success = 'Настройка успешно изменены.';
        }
      }

    } else
    {
      $form_data = user();
    }

    render('settings_password', array(
      'profile' => user(),
      'form'    => $form_data,
      'error'   => $error,
      'success' => $success));
  }

  /**
   * Login.
   */
  public function login($request)
  {
    $errors = array();
    $email = $_POST['email'];
    $password = $_POST['password'];
    $u = R::findOne('users', 'email = ?', array($email));
    if( !$u )
    {
      $errors[] = 'Не верно введён Email или пароль.';
    } else
    {
      if( password_verify($password, $u->password) )
      {
        $u->hash = HashManager::createHash($u->id . $_SERVER['HTTP_USER_AGENT']);
        R::store($u);
        CookieManager::store('logged_user', HashManager::encodeInt($u->id));
      } else
      {
        $errors[] = 'Не верно введён Email или пароль.';
      }
    }

    if( !empty($errors) )
    {
      render('login_form', array(
        'email' => $email,
        'password' => $password,
        'error' => array_shift($errors)));
    } else
    {
      redirect('/');
    }
  }

  /**
   * Signup.
   */
  public function signup($request)
  {

    $errors = array();
    $data = array(

      'email' => array(
        'value' => $_POST['email'],
        'validation' => new Rules\AllOf(
          new Rules\Email(),
          new Rules\Length(1, 100)),
        'empty_message' => 'Введите Email.',
        'error_message' => 'Неверно введён Email.'),

      'password' => array(
        'value' => $_POST['password'],
        'validation' => new Rules\AllOf(
          new Rules\Length(5, 1000)),
        'empty_message' => 'Введите пароль.',
        'error_message' => 'Пароль слишком простой, придумайте более сложный.')
    );

    foreach( $data as $field )
    {
      if( empty($field['value']) )
      {
        $errors[] = $field['empty_message'];
      } else if( !$field['validation']->validate($field['value']) )
      {
        $errors[] = $field['error_message'];
      }
    }

    $u = R::findOne('users', 'email = ?', array($data['email']));
    if( $u )
    {
      $errors[] = 'Пользователь с таким Email уже есть.';
    }

    if( !empty($errors) )
    {
      render('signup_form', array(
        'form' => $_POST,
        'error' => array_shift($errors)));
    } else
    {

      $data = $_POST;
      $newUser = R::dispense('users');
      $newUser->login = $data['email'];
      $newUser->password = password_hash($data['password'], PASSWORD_DEFAULT);
      $newUser->name = '';

      $newUser->gender = 'male';
      $newUser->age = 20;
      $newUser->city = '';

      $newUser->email = $data['email'];
      $newUser->money = 0.00;
      $newUser->socials = json_encode(array());
      $newUser->profilesettings = json_encode(array());
      $newUser->is_activated = 1;
      $newUser->is_admin = 0;
      $newUser->is_banned = 0;

      if( $data['account_type'] == 'learner' )
      {
        $account_type = 0;
      } else
      {
        $account_type = 1;
      }
      $newUser->account_type = $account_type ;
      $id = R::store($newUser);

      // login
      $_POST['email'] = $data['email'];
      $_POST['password'] = $data['password'];
      $this->login(array());
    }

  }

  public function restore($request)
  {
    $errors = array();
    $email = $_POST['email'];
    if( Validator::email()->validate($email) )
    {
      $u = R::findOne('users', 'email = ?', array($email));
      if( $u )
      {
        if( TimeBlocker::isBlocked('restore', 'block') )
        {
          $errors[] = 'Вы уже запрашивали восстановление на этот Email ранее.';
        } else
        {
          TimeBlocker::block('restore', 'block', '24hours');

          $time = TimeManager::time();
          $unique_hash = password_hash($u->id . $time, PASSWORD_DEFAULT);
          $recover = R::dispense('recovers');
          $recover->user_id = $u->id;
          $recover->unique_hash = $unique_hash;
          $recover->pub_date = $time;
          R::store($recover);

          $app_settings = App::settings();
          Mailer::send(
            $app_settings['mailer']['system_from'],
            $email,
            'Восстановление пароля на проекте ' . $app_settings['project'],
            "Проследуйте по этой ссылке, что-бы восстановить пароль.\r\nВнимание, если Вы не запрашивали восстановление пароля, проигнорируйте это письмо.\r\n\r\nСсылка: " . (get_protocol() . $_SERVER['HTTP_HOST'] . '/users/recover/process/' . $unique_hash));
        }
      } else
      {
        $errors[] = 'Пользователь с таким Email не найден.';
      }
    } else
    {
      $errors[] = 'Не верно указан Email.';
    }

    if( !empty($errors) )
    {
      render('restore_form', array(
        'email' => $email,
        'error' => array_shift($errors)));
    } else
    {
      render('restore_form', array(
        'email' => $email,
        'success' => 'На указанный Email отправлено письмо с дальнейшими инструкциями по восстановлению пароля.'));
    }
  }

  public function restore_process($request)
  {
    $restore = R::findOne('recovers', 'unique_hash = ?', array($request['1']));
    if( $restore )
    {
      $new_password = md5(uniqid());
      $u = R::load('users', $restore->user_id);
      $u->password = password_hash($new_password, PASSWORD_DEFAULT);
      R::store($u);
      R::trash($restore);
      render('restore_success', array('new_password' => $new_password));
    } else
    {
      render('restore_failed');
    }
  }

  public function logout($request)
  {
    CookieManager::delete('logged_user');
    redirect('/');
  }

  public function protect()
  {
    if( !user() )
    {
      redirect('/');
    }
  }

}