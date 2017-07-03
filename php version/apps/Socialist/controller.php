<?php

class Socialist_Controller extends Controller {

  public function vk($request)
  {
    // vk.com authorization
    $app_settings = App::settings();
    $api = $app_settings['socialist']['vk'];

    if(!empty($_GET['code']))
    {
      // we got the code
      $vk_grand_url = "https://api.vk.com/oauth/access_token?client_id=".$api['id']."&client_secret=".$api['secret']."&code=".$_GET['code']."&redirect_uri=".urlencode(SELF_URL);
      $resp = Http::request($vk_grand_url,true);
      $data = json_decode($resp, true);

      $vk_access_token = $data['access_token'];
      $vk_uid =  $data['user_id'];

      $res = Http::request("https://api.vk.com/method/users.get?uids=".$vk_uid."&access_token=".$vk_access_token."&fields=uid,first_name,last_name,screen_name,sex,bdate,city,photo_200_orig",true);
      $data = json_decode($res, true);
      $user_info = $data['response'][0];

      if( R::count('users', 'email = ?', array($user_info['uid'].'@vk.com')) )
      {
        // already exists, login
        $_POST['email'] = $user_info['uid'].'@vk.com';
        $_POST['password'] = base64_encode(($user_info['uid'] + 100500));

        $this->load('users/controller');
        $this->connections->controller['users']->login($request);
        exit();
      } else
      {
        // not exists, signup
        $newUser = R::dispense('users');
        $newUser->is_activated = 1;
        $newUser->is_admin = 0;
        $newUser->is_banned = 0;
        $newUser->login = $user_info['uid'].'@vk.com';
        $newUser->password = password_hash(base64_encode(($user_info['uid'] + 100500)), PASSWORD_DEFAULT);
        $newUser->name = $user_info['first_name'].' '.$user_info['last_name'];

        if ($user_info['sex'] == '1') {
            $gender = 'female';
        } else {
            $gender = 'male';
        }
        $newUser->gender = trim(FilterMaster::filterAll($gender));

        $age = 0;
        if (isset($user_info['bdate']) && $user_info['bdate'] != '') {
            $birth_date = strtotime($user_info['bdate']);
            $age = (int)date('Y',TimeManager::time()) - date('Y',$birth_date);
        }
        $newUser->age = $age;

        $city = '';
        if (isset($user_info['city']) && $user_info['city'] != '') {
            $cityInfo = json_decode(Http::request("https://api.vk.com/method/database.getCitiesById?city_ids=".$user_info['city'],true),true);
            $city = $cityInfo['response']['0']['name'];
        }
        $newUser->city = trim(FilterMaster::filterAll($city));

        $newUser->email = $user_info['uid'].'@vk.com';
        $newUser->money = 0.00;
        $newUser->socials = json_encode(array('vkontakte'=>$user_info['screen_name']));

        $file_info = pathinfo($user_info['photo_200_orig']);
        $avatars_media_dir = '/static/uploads/avatars/';
        $avatars_dir = BASE_DIR.$avatars_media_dir;
        $new_fname = FileSystem::getUniqueFilename($file_info['basename'],$avatars_dir);
        file_put_contents($avatars_dir.$new_fname, Http::request($user_info['photo_200_orig'], true));
        $image = new ImageResize();
        $image->load($avatars_dir.$new_fname);
        $image->resizeToWidth(207);
        $image->save($avatars_dir.$new_fname);
        $newUser->avatar = $new_fname;
        $newUser->profilesettings = json_encode(array());
        $newUser->account_type = 0;
        $id = R::store($newUser);

        // login now
        $_POST['email'] = $user_info['uid'].'@vk.com';
        $_POST['password'] = base64_encode(($user_info['uid'] + 100500));

        $this->load('users/controller');
        $this->connections->controller['users']->login($request);
        exit();
      }
    } else
    {
      redirect('/');
    }

  }

  public function fb($request)
  {
    // facebook.com authorization
    $app_settings = App::settings();
    $api = $app_settings['socialist']['fb'];

    if(!empty($_GET['code']))
    {
      $params = array(
        'client_id'     => $api['id'],
        'redirect_uri'  => SELF_URL,
        'client_secret' => $api['secret'],
        'code'          => $_GET['code']
      );

      $url = 'https://graph.facebook.com/oauth/access_token';
      $tokenInfo = null;
      $rqwst = Http::request($url . '?' . http_build_query($params),true);
      parse_str($rqwst, $tokenInfo);
      if (count($tokenInfo) > 0 && isset($tokenInfo['access_token'])) {
          $params = array('access_token' => $tokenInfo['access_token']);
          $userInfo = json_decode(Http::request('https://graph.facebook.com/me' . '?fields=location,id,birthday,email,first_name,gender,last_name,link,name,age_range&' . urldecode(http_build_query($params)),true), true);
          if (isset($userInfo['id'])) {
              $userInfo = $userInfo;
              $result = true;
          }
      }
      $user_info = $userInfo;
      $user_picture = 'https://graph.facebook.com/'.$user_info['id'].'/picture?type=large&ext=.jpg';

      if (R::count('users', 'email = ?', array($user_info['id'].'@facebook.com'))) {
          // already exists, login
          $_POST['email'] = $user_info['id'].'@facebook.com';
          $_POST['password'] = base64_encode(($user_info['id'] + 100500));

          $this->load('users/controller');
          $this->connections->controller['users']->login($request);
          exit();
      } else {
          // not found, signup
          $newUser = R::dispense('users');
          $newUser->is_activated = 1;
          $newUser->is_admin = 0;
          $newUser->is_banned = 0;
          $newUser->login = $user_info['id'].'@facebook.com';

          $newUser->password = password_hash(base64_encode(($user_info['id'] + 100500)), PASSWORD_DEFAULT);
          $newUser->name = $user_info['first_name'].' '.$user_info['last_name'];
          $gender = $user_info['gender'];
          $newUser->gender = trim(FilterMaster::filterAll($gender));

          $age = 0;
          if (isset($user_info['birthday']) && $user_info['birthday'] != '') {
              $birth_date = strtotime($user_info['birthday']);
              $age = (int)date('Y',TimeManager::time()) - date('Y',$birth_date);
          }
          $newUser->age = $age;

          $city = '';
          if(isset($user_info['location']) && isset($user_info['location']['name']) && $user_info['location']['name'] != '') {
              $city = $user_info['location']['name'];
          }
          $newUser->city = trim(FilterMaster::filterAll($city));

          $file_info = pathinfo($user_picture);
          $avatars_media_dir = '/static/uploads/avatars/';
          $avatars_dir = BASE_DIR.$avatars_media_dir;
          $new_fname = FileSystem::getUniqueFilename($file_info['basename'],$avatars_dir);
          file_put_contents($avatars_dir.$new_fname,Http::request($user_picture,true));
          $image = new ImageResize();
          $image->load($avatars_dir.$new_fname);
          $image->resizeToWidth(207);
          $image->save($avatars_dir.$new_fname);
          $newUser->avatar = $new_fname;

          $newUser->email = $user_info['id'].'@facebook.com';
          $newUser->money = 0.00;
          $newUser->socials = json_encode(array('facebook'=>$user_info['id']));
          $newUser->profilesettings = json_encode(array());
          $newUser->account_type = 0;
          $id = R::store($newUser);

          // login now
          $_POST['email'] = $user_info['id'].'@facebook.com';
          $_POST['password'] = base64_encode(($user_info['id'] + 100500));

          $this->load('users/controller');
          $this->connections->controller['users']->login($request);
          exit();
      }
    } else
    {
      redirect('/');
    }
  }

  public function tw($request)
  {
    // twitter.com authorization
    $app_settings = App::settings();
    $api = $app_settings['socialist']['tw'];

    define('CONSUMER_KEY', $api['key']);
    define('CONSUMER_SECRET', $api['secret']);
    define('REQUEST_TOKEN_URL', 'https://api.twitter.com/oauth/request_token');
    define('AUTHORIZE_URL', 'https://api.twitter.com/oauth/authorize');
    define('ACCESS_TOKEN_URL', 'https://api.twitter.com/oauth/access_token');
    define('ACCOUNT_DATA_URL', 'https://api.twitter.com/1.1/users/show.json');
    define('CALLBACK_URL', SELF_URL);
    define('URL_SEPARATOR', '&');

    if( isset($_GET['auth_redirect']) )
    {
      $this->tw_authorize();
    } else
    {
      if( !empty($_GET['oauth_token']) && !empty($_GET['oauth_verifier']) )
      {
        $oauth_nonce = md5(uniqid(rand(), true));
        $oauth_timestamp = time();
        $oauth_token = $_GET['oauth_token'];
        $oauth_verifier = $_GET['oauth_verifier'];

        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode(ACCESS_TOKEN_URL)."&";

        $params = array(
            'oauth_consumer_key=' . CONSUMER_KEY . URL_SEPARATOR,
            'oauth_nonce=' . $oauth_nonce . URL_SEPARATOR,
            'oauth_signature_method=HMAC-SHA1' . URL_SEPARATOR,
            'oauth_token=' . $oauth_token . URL_SEPARATOR,
            'oauth_timestamp=' . $oauth_timestamp . URL_SEPARATOR,
            'oauth_verifier=' . $oauth_verifier . URL_SEPARATOR,
            'oauth_version=1.0'
        );

        $key = CONSUMER_SECRET . URL_SEPARATOR;
        $oauth_base_text = 'GET' . URL_SEPARATOR . urlencode(ACCESS_TOKEN_URL) . URL_SEPARATOR . implode('', array_map('urlencode', $params));
        $oauth_signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

        $params = array(
            'oauth_nonce=' . $oauth_nonce,
            'oauth_signature_method=HMAC-SHA1',
            'oauth_timestamp=' . $oauth_timestamp,
            'oauth_consumer_key=' . CONSUMER_KEY,
            'oauth_token=' . urlencode($oauth_token),
            'oauth_verifier=' . urlencode($oauth_verifier),
            'oauth_signature=' . urlencode($oauth_signature),
            'oauth_version=1.0'
        );
        $url = ACCESS_TOKEN_URL . '?' . implode('&', $params);
        $response = Http::request($url,true);
        parse_str($response, $response);

        $oauth_nonce = md5(uniqid(rand(), true));
        $oauth_timestamp = time();

        $oauth_token = $response['oauth_token'];
        $oauth_token_secret = $response['oauth_token_secret'];
        $screen_name = $response['screen_name'];

        $params = array(
            'oauth_consumer_key=' . CONSUMER_KEY . URL_SEPARATOR,
            'oauth_nonce=' . $oauth_nonce . URL_SEPARATOR,
            'oauth_signature_method=HMAC-SHA1' . URL_SEPARATOR,
            'oauth_timestamp=' . $oauth_timestamp . URL_SEPARATOR,
            'oauth_token=' . $oauth_token . URL_SEPARATOR,
            'oauth_version=1.0' . URL_SEPARATOR,
            'screen_name=' . $screen_name
        );
        $oauth_base_text = 'GET' . URL_SEPARATOR . urlencode(ACCOUNT_DATA_URL) . URL_SEPARATOR . implode('', array_map('urlencode', $params));

        $key = CONSUMER_SECRET . '&' . $oauth_token_secret;
        $signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

        $params = array(
            'oauth_consumer_key=' . CONSUMER_KEY,
            'oauth_nonce=' . $oauth_nonce,
            'oauth_signature=' . urlencode($signature),
            'oauth_signature_method=HMAC-SHA1',
            'oauth_timestamp=' . $oauth_timestamp,
            'oauth_token=' . urlencode($oauth_token),
            'oauth_version=1.0',
            'screen_name=' . $screen_name
        );

        $url = ACCOUNT_DATA_URL . '?' . implode(URL_SEPARATOR, $params);

        $response = Http::request($url,true);

        // fuh... now into json
        $user_data = json_decode($response,true);
        $user_info = $user_data;

        if( R::count('users','email = ?',array($user_info['id_str'].'@twitter.com')) )
        {
          // account isset, login
          $_POST['email'] = $user_info['id_str'].'@twitter.com';
          $_POST['password'] = base64_encode(($user_info['id_str'] + 100500));

          $this->load('users/controller');
          $this->connections->controller['users']->login($request);
        } else
        {
          // new user, signup
          // http://pbs.twimg.com/profile_images/id_str/Rtg92MO0_normal.png
          $newUser = R::dispense('users');
          $newUser->is_activated = 1;
          $newUser->is_admin = 0;
          $newUser->is_banned = 0;
          $newUser->login = $user_info['id_str'].'@twitter.com';
          $newUser->password = password_hash(base64_encode(($user_info['id_str'] + 100500)), PASSWORD_DEFAULT);
          $newUser->name = $user_info['name'];
          $gender = 'male';
          $newUser->gender = trim(FilterMaster::filterAll($gender));
          $newUser->age = (int)trim(FilterMaster::filterAll(20));
          $newUser->city = trim(FilterMaster::filterAll($user_info['location']));
          $newUser->email = $user_info['id_str'].'@twitter.com';
          $newUser->money = 0.00;

          $user_picture = str_replace('_normal','',$user_info['profile_image_url']);
          $file_info = pathinfo($user_picture);
          $avatars_media_dir = '/static/uploads/avatars/';
          $avatars_dir = BASE_DIR.$avatars_media_dir;
          $new_fname = FileSystem::getUniqueFilename($file_info['basename'],$avatars_dir);
          file_put_contents($avatars_dir.$new_fname,Http::request($user_picture,true));
          $image = new ImageResize();
          $image->load($avatars_dir.$new_fname);
          $image->resizeToWidth(207);
          $image->save($avatars_dir.$new_fname);
          $newUser->avatar = $new_fname;

          $newUser->socials = json_encode(array('twitter'=>$user_info['screen_name']));
          $newUser->profilesettings = json_encode(array());
          $newUser->account_type = 0;
          $id = R::store($newUser);

          // login
          $_POST['email'] = $user_info['id_str'].'@twitter.com';
          $_POST['password'] = base64_encode(($user_info['id_str'] + 100500));

          $this->load('users/controller');
          $this->connections->controller['users']->login($request);
        }
      } else
      {
        redirect('/');
      }
    }
  }


  public function tw_authorize() {
      $oauth_nonce = md5(uniqid(rand(), true));
      $oauth_timestamp = time();

      $params = array(
          'oauth_callback=' . urlencode(CALLBACK_URL) . URL_SEPARATOR,
          'oauth_consumer_key=' . CONSUMER_KEY . URL_SEPARATOR,
          'oauth_nonce=' . $oauth_nonce . URL_SEPARATOR,
          'oauth_signature_method=HMAC-SHA1' . URL_SEPARATOR,
          'oauth_timestamp=' . $oauth_timestamp . URL_SEPARATOR,
          'oauth_version=1.0'
      );
      $oauth_base_text = implode('', array_map('urlencode', $params));
      $key = CONSUMER_SECRET . URL_SEPARATOR;
      $oauth_base_text = 'GET' . URL_SEPARATOR . urlencode(REQUEST_TOKEN_URL) . URL_SEPARATOR . $oauth_base_text;
      $oauth_signature = base64_encode(hash_hmac('sha1', $oauth_base_text, $key, true));

      //params
      $params = array(
          URL_SEPARATOR . 'oauth_consumer_key=' . CONSUMER_KEY,
          'oauth_nonce=' . $oauth_nonce,
          'oauth_signature=' . urlencode($oauth_signature),
          'oauth_signature_method=HMAC-SHA1',
          'oauth_timestamp=' . $oauth_timestamp,
          'oauth_version=1.0'
      );

      // склеиваем параметры для формирования url
      $url = REQUEST_TOKEN_URL . '?oauth_callback=' . urlencode(CALLBACK_URL) . implode('&', $params);

      // Отправляем GET запрос по сформированному url
      $response = Http::request($url,true);

      // Парсим ответ
      parse_str($response, $response);

      // записываем ответ в переменные
      $oauth_token = $response['oauth_token'];
      $oauth_token_secret = $response['oauth_token_secret'];

      //в итоге сама ссылка
      $link = AUTHORIZE_URL . '?oauth_token=' . $oauth_token;

      redirect( $link );
  }

}