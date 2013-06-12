<?php
/*
    Plugin name: Twitter feed
    Copyright: Copyright (C) 2013 JSC "Insightio"
    License: MIT, see README.md
*/

namespace Modules\standard\twitter_feed\widget;

if (!defined('CMS')) exit;

class IptTwitterFeed extends \Modules\standard\content_management\Widget {

    public function __construct($name, $moduleGroup, $moduleName, $core = false) {
        parent::__construct($name, $moduleGroup, $moduleName, $core);

        $this->moduleDir     = BASE_DIR.PLUGIN_DIR.$this->moduleGroup.'/'.$this->moduleName;
        $this->widgetDir     = $this->moduleDir.'/widget/'.$this->name.'/';
        $this->managementDir = $this->widgetDir.self::MANAGEMENT_DIR;
        $this->previewDir    = $this->widgetDir.self::PREVIEW_DIR;
    }

    public function getTitle() {
        return $this->getTranslation('widget_title');
    }

    public function managementHtml($instanceId, $data, $layout) {    
        $data = array();

        $rendered = \Ip\View::create($this->managementDir.'/default.php', $data)->render();
        
        return $rendered;
    }

    public function previewHtml($instanceId, $data, $layout) {   
        $data = array(
            'tweets'   => $this->getTweets()
        );

        $rendered = \Ip\View::create($this->previewDir.'/'.$layout.'.php', $data)->render();
        
        return $rendered;
    }

    private function getTweets() {
        require_once $this->widgetDir.'/lib/twitter-text/Autolink.php';

        $username =  trim($this->getOption('username'));
        $count    =  trim($this->getOption('count'));

        $twitterData = $this->getData($username, $count);

        if(! isset($twitterData->errors)) {
            $tweets = array();

            foreach($twitterData as $tweet) {
                $tweetDetails = new \stdClass();
                $tweetDetails->text = \Twitter_Autolink::create($tweet->text)->setNoFollow(false)->addLinks();
                $tweetDetails->time = $this->timeAgo($tweet->created_at);
                $tweetDetails->id = $tweet->id_str;
                $tweetDetails->screenName = $tweet->user->screen_name;
                $tweetDetails->displayName = $tweet->user->name;
                $tweetDetails->profileImage = $tweet->user->profile_image_url_https;
                $tweetDetails->tweetUrl = 'http://www.twitter.com/'.$tweet->user->screen_name.'/status/'.$tweet->id_str;

                $tweets[] = $tweetDetails;
            }
            
            return $tweets;
        } else {
            return array();
        }
    }

    private function getData($username, $count) {
        require_once $this->widgetDir.'/lib/twitteroauth/twitteroauth.php';

        $cacheTime = 60 * 60;
        $cacheFile = __DIR__ . '/cache/twitter_'.md5($username.$count).'.cache';
        $cacheFileCreated = ((file_exists($cacheFile))) ? filemtime($cacheFile) : 0;

        if (time() - $cacheTime < $cacheFileCreated) {
            return json_decode(file_get_contents($cacheFile));
        } else {
            $OAuth = new \TwitterOAuth(
                trim($this->getOption('consumer_key')),    // Consumer Key
                trim($this->getOption('consumer_secret')), // Consumer secret
                trim($this->getOption('access_token')),    // Access token
                trim($this->getOption('access_secret'))    // Access token secret
            );

            $twitterData = $OAuth->get(
                'statuses/user_timeline',
                array(
                    'screen_name' => $username,
                    'count'       => $count
                )
            );

            if(! isset($twitterData->errors))
                file_put_contents($cacheFile, json_encode($twitterData));

            return $twitterData;
        }
    }

    private function timeAgo($date) {
        $currentTime = time();
        $tweetTime = strtotime($date);
        $timeDiff = abs($currentTime - $tweetTime);

        switch($timeDiff) {
            case ($timeDiff < 60):
                $displayTime = $timeDiff.' seconds ago';                  
                break;      
            case ($timeDiff >= 60 && $timeDiff < 3600):
                $min = floor($timeDiff / 60);
                $displayTime = $min.' minutes ago';                  
                break;      
            case ($timeDiff >= 3600 && $timeDiff < 86400):
                $hour = floor($timeDiff / 3600);
                $displayTime = 'about '.$hour.' hour';
                if($hour > 1){ 
                    $displayTime .= 's'; 
                }
                $displayTime .= ' ago';
                break;          
            default:
                $displayTime = date('g:i A M jS', $tweetTime);
                break;
        }

        return $displayTime;
    }

    private function getTranslation($key) {
        global $parametersMod;

        return $parametersMod->getValue('standard', 'twitter_feed', 'translations', $key);
    }

    private function getOption($key) {
        global $parametersMod;

        return $parametersMod->getValue('standard', 'twitter_feed', 'login_credentials', $key);
    }

}
