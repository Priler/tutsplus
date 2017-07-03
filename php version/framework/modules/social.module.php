<?php

class Social {

    public function __construct() {
        //empty one
    }

    static public function getProfileLink($socialNetwork, $profileId) {
        switch($socialNetwork) {
            case'behance':
                return 'http://behance.com/'.$profileId;
                break;
            case'deviantart':
                return 'http://'.$profileId.'.deviantart.com';
                break;
            case'digg':
                return 'http://digg.com/users/'.$profileId;
                break;
            case'dribbble':
                return 'https://dribbble.com/'.$profileId;
                break;
            case'facebook':
                return 'https://www.facebook.com/'.$profileId;
                break;
            case'flickr':
                return 'https://www.flickr.com/photos/'.$profileId;
                break;
            case'google_plus':
                return 'https://plus.google.com/'.$profileId;
                break;
            case'lastfm':
                return 'http://www.lastfm.ru/user/'.$profileId;
                break;
            case'twitter':
                return 'http://twitter.com/'.$profileId;
                break;
            case'linkedin':
                if (!preg_match('#^https://www\.linkedin\.com/#i',$profileId)) {
                    return 'https://www.linkedin.com/'.$profileId;
                } else {
                    return $profileId;
                }
                break;
            case'myspace':
                return 'http://www.myspace.com/'.$profileId;
                break;
            case'reddit':
                return 'http://www.reddit.com/user/'.$profileId;
                break;
            case'soundcloud':
                return 'http://soundcloud.com/'.$profileId;
                break;
            case'tumblr':
                return 'http://'.$profileId.'.tumblr.com';
                break;
            case'tuts_plus':
                return 'http://tutsplus.com/authors/'.$profileId;
                break;
            case'vimeo':
                return 'http://vimeo.com/'.$profileId;
                break;
            case'youtube':
                return 'https://www.youtube.com/user/'.$profileId;
                break;
            case'instagram':
                return 'http://instagram.com/'.$profileId;
                break;
            case'vkontakte':
                return 'https://vk.com/'.$profileId;
                break;
            case'habrahabr':
                return 'http://habrahabr.ru/users/'.$profileId.'/';
                break;
            case'skype':
                return 'skype:'.$profileId.'?chat';
                break;
            case'medium':
                $username = $profileId['0'] == '@' ? $profileId:'@'.$profileId;
                return 'https://medium.com/'.$username;
                break;
            case'github':
                return 'https://github.com/'.$profileId;
                break;
        }
    }

}