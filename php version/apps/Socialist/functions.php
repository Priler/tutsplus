<?php

namespace Socialist {

  function vk_login_url()
  {
    $app_settings = \App::settings();
    $api = $app_settings['socialist']['vk'];

    return 'https://oauth.vk.com/authorize?client_id='.$api['id'].'&scope=offline,photos,friends,email,wall&redirect_uri='.urlencode(HTTP_HOST . '/socialist/vk').'&response_type=code&v=5.2';
  }

  function fb_login_url()
  {
    $app_settings = \App::settings();
    $api = $app_settings['socialist']['fb'];

    return 'https://www.facebook.com/dialog/oauth?client_id='.$api['id'].'&redirect_uri='.urlencode(HTTP_HOST . '/socialist/fb').'&response_type=code&scope=user_friends,user_about_me,user_birthday,user_photos,email,user_hometown,user_tagged_places,user_location';
  }

  function tw_login_url()
  {
    return HTTP_HOST . '/socialist/tw?auth_redirect';
  }

}