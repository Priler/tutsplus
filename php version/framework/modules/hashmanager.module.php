<?php

class HashManager {

    static public function createHash($str, $ancor='some secret words')
    {
      $hash = sha1($str . $ancor);
      return $hash;
    }

    static public function encodeInt($var,$ancor=12345){
        return base64_encode($var+($ancor*100500));
    }

    static public function decodeInt($var,$ancor=12345){
        return base64_decode($var)-($ancor*100500);
    }

}