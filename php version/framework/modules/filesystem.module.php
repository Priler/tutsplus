<?php

class FileSystem {

    public function __construct() {
        //empty one
    }

    static public function copy($from, $to) {
        if (!file_exists($from)) {
            return false;
        }
        return rename($from, $to);
    }

    static public function move($from, $to) {
        if (!file_exists($from)) {
            return false;
        }
        return rename($from, $to);
    }

    static public function remove($filename) {
        if(!file_exists($filename)) {
            return true;
        }
        unlink($filename);
        return true;
    }

    static public function tranformBytesTo($bytes, $size_type) {
        switch($size_type) {
            case'kb':
                $result = $bytes / 1024;
                break;
            case'mb':
                $result = ($bytes / 1024) / 1024;
                break;
            case'gb':
                $result = (($bytes / 1024) / 1024) / 1024;
                break;
            case'tb':
                $result = ((($bytes / 1024) / 1024) / 1024) / 1024;
                break;
            default:
                return $bytes;
                break;
        }
        return number_format($result,2);
    }

    static public function getUniqueFilename($source_filename, $in_directory, $return_with_path = false) {
        $ext = FileSystem::getExtension($source_filename, true);
        if (!$ext) {
            $ext = '.unknown';
        }
        $fname = FileSystem::getFilename($source_filename);
        if (!$fname) {
            $fname = sha1(TimeManager::time());
        }
        if (substr($in_directory,-1) != '/') {
            $in_directory = $in_directory.'/';
        }
        $new_filename = md5($fname).$ext;
        $i = 0;
        while(file_exists($in_directory.$new_filename)) {
            $new_filename = md5($fname).++$i.$ext;
        }
        if ($return_with_path) {
            return $in_directory.$new_filename;
        } else {
            return $new_filename;
        }
    }

    static public function getFilename($filename) {
        return mb_substr($filename, 0, -mb_strlen(FileSystem::getExtension($filename, true),'utf-8'), 'utf-8');
    }

    static public function getExtension($filename, $with_dot = false) {
        if ($with_dot) {
            $ext = '.'.end(explode('.', $filename));
        } else {
            $ext = end(explode('.', $filename));
        }
        return $ext;
    }

}