<?php

class TimeBlocker {

    public function __construct() {
        //empty one
    }

    static public function block($ancor='', $type='', $period='1hou') {
        $block = R::dispense('timeblocks');
        $block->ancor = $ancor;
        $block->type = $type;
        $block->starttime = TimeManager::time();
        $block->endtime = TimeManager::timePlus($period);
        R::store($block);
    }

    static public function isBlocked($ancor='', $type='', $callCleanOldBlocks=true) {
        $block = R::findOne('timeblocks','ancor = ? AND type = ? AND endtime > ?',array($ancor,$type,TimeManager::time()));
        if ($callCleanOldBlocks) {
            TimeBlocker::cleanOldBlocks();
        }
        if (empty($block)) {
            return false;
        } else {
            return $block;
        }
    }

    static public function getDiff($ancor='', $type='') {
        if (is_object($ancor)) {
            $block = $ancor;
        } else {
            $block = R::findOne('timeblocks','ancor = ? AND type = ? AND endtime > ?',array($ancor,$type,TimeManager::time()));
        }
        if (empty($block)) {
            return 0;
        } else {
            if ($block->endtime > TimeManager::time()) {
                return $block->endtime - TimeManager::time();
            } else {
                return TimeManager::time() - $block->endtime;
            }
        }
    }

    static public function cleanOldBlocks($currentTime = 'now') {
        $fTime = TimeManager::timePlus($currentTime);
        R::exec("DELETE FROM `timeblocks` WHERE `endtime` < ".TimeManager::time());
    }

}