<?php

require dirname(__FILE__) . '/libs/libmail.class.php';

class Mailer {

    public function __construct() {
        //empty one
    }

    static public function send($from,$to,$title,$text) {
        $mail = new Mail;
        $mail->From($from);
        $mail->To($to);
        $mail->Subject($title);
        $mail->Body($text);
        $mail->Priority(3);
        return $mail->Send();
    }

}