<?php

class SessionManager {
    
    public function __construct() {
        //empty one
    }

    static public function store($key, $value) {
        $_SESSION[$key]=$value;
        return true;
    }

    static public function stored($key) {
        if (isset($_SESSION[$key])) {
            return true;
        } else {
            return false;
        }
    }

    static public function read($key) {
        return $_SESSION[$key];
    }

}