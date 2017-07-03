<?php

if( ACTIVE_APP == 'Admin' ) {
  return array(
    'users' => array(
      'total'    => R::count('users'),
      'learners' => R::count('users', 'account_type = 0'),
      'mentors'  => R::count('users', 'account_type = 1')));
} else {
  return array();
}