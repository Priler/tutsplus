<?php

return array(
    '#^/users/login/form/*$#i' => render_url('users/login_form'),
    '#^/users/signup/form/*$#i' => render_url('users/signup_form'),
    '#^/users/restore/form/*$#i' => render_url('users/restore_form'),

    '#^/@([A-z0-9_-]+)/*$#i' => 'profile',
    '#^/users/settings/*$#i' => 'settings',
    '#^/users/settings/general/*$#i' => 'settings',
    '#^/users/settings/profile/*$#i' => 'settings_profile',
    '#^/users/settings/password/*$#i' => 'settings_password',

    '#^/users/signup/*$#i' => 'signup',
    '#^/users/restore/*$#i' => 'restore',
    '#^/users/logout/*$#i' => 'logout',

    '#^/users/restore/process/(.+?)/*$#i' => 'restore_process',
);