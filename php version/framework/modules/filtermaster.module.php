<?php

class FilterMaster {

    public function __construct() {
        //empty one
    }

    static public function isRegEmpty($input) {
        if (preg_match('#^\s*$#',$input)) {
            return true;
        } else {
            return false;
        }
    }

    static public function isEmail($input) {
        if (preg_match('#^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6}$#',$input)) {
            return true;
        } else {
            return false;
        }
    }

    static public function isNumber($input) {
        if (preg_match('#^[0-9]+$#',$input)) {
            return true;
        } else {
            return false;
        }
    }

    static public function isFloat($input) {
        if (preg_match('#^[0-9]+\.[0-9]+$#',$input)) {
            return true;
        } else {
            return false;
        }
    }

    static public function validateString($input, $minLength = 0, $maxLength = 0, $symbolsRequired = true, $numbersRequired = true, $availableSpecs = '_') {
        $regExpr = '#^';
        if ($symbolsRequired || $numbersRequired || $availableSpecs != '') {
            $regExpr .= '[';
        }
        if ($symbolsRequired) {
            $regExpr .= 'A-z';
        }
        if ($numbersRequired) {
            $regExpr .= '0-9';
        }
        if ($symbolsRequired || $numbersRequired || $availableSpecs != '') {
            $regExpr .= $availableSpecs.']';
        }
        if ($minLength != 0 || $maxLength != 0) {
            if ($minLength > 0) {
                $regExpr .= '{'.$minLength.',';
            }
            if ($maxLength > 0 && $maxLength > $minLength) {
                $regExpr .= $maxLength;
            }
            $regExpr .= '}';
        }
        $regExpr .= '$#';
        if (preg_match($regExpr,$input)) {
            return true;
        } else {
            return false;
        }
    }

    static public function filterAll($inputStr) {
        $str = htmlspecialchars(strip_tags($inputStr));
        return $str;
    }

}