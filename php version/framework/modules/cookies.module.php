<?php

class CookieManager {

    public function __construct() {
        //empty one
    }

    static public function store($key, $value, $expire = false, $path = '/', $domain = 'current', $secure = 0) {
        if ($domain == 'current') {
            $domain = $_SERVER['HTTP_HOST'];
        }
        if (!$expire) {
            $expire = TimeManager::timePlus('24day');
        }
        setcookie($key, $value, $expire, $path, $domain, $secure);
        return true;
    }

    static public function read($key) {
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        } else {
            return false;
        }
    }

    static public function delete($key, $value = '', $expire = 1, $path = '/', $domain = 'current', $secure = 0) {
        if ($domain == 'current') {
            $domain = $_SERVER['HTTP_HOST'];
        }
        setcookie($key,$value,$expire,$path,$domain,$secure);
        return true;
    }

}