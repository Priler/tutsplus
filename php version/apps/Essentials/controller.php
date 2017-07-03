<?php

class Essentials_Controller extends Controller {

    public function main($request) {
      render('main', array());
    }

    public function ViewPage($request) {
        echo '<pre>';
        print_r($args);
    }

}