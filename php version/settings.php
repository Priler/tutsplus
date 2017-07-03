<?php

return array(
    'project' => 'CodeStudio++',
    'production' => false,
    'debug' => false,

    'apps' => array(
        'Admin',
        'Essentials',
        'Users',
        'Socialist',
        '404'
    ),

    'database' => array(
      'use' => true,
      'connect' => 'mysql:host=127.0.0.1;dbname=infex',
      'host' => '127.0.0.1',
      'name' => 'infex',
      'username' => 'root',
      'password' => ''
    ),

    'socialist' => array(
      'vk' => array(
        'id' => '',
        'secret' => ''),
      'tw' => array(
        'key' => '',
        'secret' => ''),
      'fb' => array(
        'id' => '',
        'secret' => '')
    ),

    'mailer' => array(
      'system_from' => 'noreply@codestudio.ru',
    ),

    'twig' => array(
      'cache_enabled' => false
    )
);