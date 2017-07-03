<?php

function user($att = false)
{

  /**
   * This function will be called on every single page.
   */
  if( !isset($GLOBALS['logged_user']) )
  {
    if( $lu = CookieManager::read('logged_user') )
    {
      $hashRecovered = HashManager::createHash( HashManager::decodeInt($lu).$_SERVER['HTTP_USER_AGENT'] );
      $GLOBALS['logged_user'] = R::findOne('users', 'id = ? AND hash = ?', array(HashManager::decodeInt($lu), $hashRecovered) )->export();
      $GLOBALS['logged_user'] = user_purify($GLOBALS['logged_user']);
    } else
    {
      $GLOBALS['logged_user'] = false;
    }
  }

  // update user online status
  if( !SessionManager::stored('user_online_status_last_update') || SessionManager::read('user_online_status_last_update') > TimeManager::time())
  {
    // update status every 5 minutes
    R::exec("UPDATE `users` SET `last_visit` = NOW()");
    SessionManager::store('user_online_status_last_update', TimeManager::timePlus('5min'));
  }

  if( $GLOBALS['logged_user'] )
  {
    if( $att )
    {
      return $GLOBALS['logged_user'][$att];
    } else
    {
      return $GLOBALS['logged_user'];
    }
  } else
  {
    return false;
  }
}

function user_purify($user)
{
  if( !empty($user['name']) )
  {
    $user['title'] = $user['name'];
  } else
  {
    $user['title'] = $user['login'];
  }

  if( $user['account_type'] == 0 )
  {
    $user['account_type_title'] = 'Ученик';
  } else
  {
    $user['account_type_title'] = 'Ментор';
  }

  if( empty($user['profession']) )
  {
    $user['profession'] = $user['account_type_title'];
  }

  if( strtotime($user['last_visit']) > TimeManager::timeMinus('10min') )
  {
    $user['online_status'] = 'online';
  } else
  {
    $user['online_status'] = 'offline';
  }

  if( empty($user['avatar']) )
  {
    $user['avatar'] = '/media/images/design-time/no-avatar.jpg';
  } else
  {
    $user['avatar'] = $user['avatar'];
  }

  $user['profile_url'] = '/@' . $user['id'];
  return $user;
}

function user_force($with) {
  $GLOBALS['logged_user'] = user_purify($with);
}