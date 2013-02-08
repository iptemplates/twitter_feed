<?php
/*
    Plugin name: Twitter feed
    Copyright: Copyright (C) 2013 JSC "Insightio"
    License: MIT, see README.md
*/

namespace Modules\standard\twitter_feed\widget;

/*
    Twitter class for interacting with Twitter API
*/
class Twitter
{
    private $_username;

    public function __construct($username)
    {
        $this->_username = $username;
    }

    /*
        Get tweets
    */
    public function getTweets($limit = 10, $formatLinks = true)
    {
        $data = $this->_getUrlCached('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=' . $this->_username . '&count=' . $limit);
        $tweets = json_decode($data);

        if(isset($tweets->errors) || isset($tweets->error) || $tweets == null){
            return array();
        } else {
            return $this->_formatLinks($tweets); 
        }
    }

    /*
        Get twitter url from cache, if it is not cached - retrieve it from the twitter.com
    */
    private function _getUrlCached($url){
        $cache_time = 60 * 60;
        $cache_file = __DIR__ . '/cache/twitter_' . md5($url) . '.cache';
        $cache_file_created = ((file_exists($cache_file))) ? filemtime($cache_file) : 0;

        if (time() - $cache_time < $cache_file_created) {
            return file_get_contents($cache_file);
        } else {
            $data = $this->_getUrl($url);

            file_put_contents($cache_file, $data);

            return $data;
        }
    }

    /*
        Return web page with php curl
    */
    private function _getUrl($url) {
        $agent = 'Mozilla/5.0 (X11; U; Linux i686; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.204 Safari/534.16';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_URL, $url);

        $response = $this->_curl_exec_follow($ch, 3);
        
        @curl_close($ch);

        return $response;
    }

    /*
        Handle redirects when safe mode if enabled
        See - http://de3.php.net/manual/de/function.curl-setopt.php#102121
    */
    private function _curl_exec_follow($ch, $maxredirect = null){ 
        $mr = $maxredirect === null ? 5 : intval($maxredirect); 
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) { 
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0); 
            curl_setopt($ch, CURLOPT_MAXREDIRS, $mr); 
        } else { 
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); 
            if ($mr > 0) { 
                $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); 

                $rch = curl_copy_handle($ch); 
                curl_setopt($rch, CURLOPT_HEADER, true); 
                curl_setopt($rch, CURLOPT_NOBODY, true); 
                curl_setopt($rch, CURLOPT_FORBID_REUSE, false); 
                curl_setopt($rch, CURLOPT_RETURNTRANSFER, true); 
                do { 
                    curl_setopt($rch, CURLOPT_URL, $newurl); 
                    $header = curl_exec($rch); 
                    if (curl_errno($rch)) { 
                        $code = 0; 
                    } else { 
                        $code = curl_getinfo($rch, CURLINFO_HTTP_CODE); 
                        if ($code == 301 || $code == 302) { 
                            preg_match('/Location:(.*?)\n/', $header, $matches); 
                            $newurl = trim(array_pop($matches)); 
                        } else { 
                            $code = 0; 
                        } 
                    } 
                } while ($code && --$mr); 
                curl_close($rch); 
                if (!$mr) { 
                    if ($maxredirect === null) { 
                        trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING); 
                    } else { 
                        $maxredirect = 0; 
                    } 
                    return false; 
                } 
                curl_setopt($ch, CURLOPT_URL, $newurl); 
            } 
        } 
        return curl_exec($ch); 
    } 

    /*
        Format twitter messages:
            Create active anchors from links
            Create links for twitter @usernames
            Create links for #hashtags
    */
    private function _formatLinks($tweets)
    {
        foreach($tweets as $tweet) {
            $tweet->text = preg_replace('/((http|ftp|https|ftps|irc):\/\/[^()<>\s]+)/i', '<a href="$1" target="_blank">$1</a>', $tweet->text);

            // Format links to user profiles (for @ messages)
            $tweet->text = preg_replace('/@([0-9a-zA-Z-+_]+)/i', '<a href="http://www.twitter.com/$1" target="_blank">@$1</a>', $tweet->text);

            // Format hashtag links
            $tweet->text = preg_replace('/#([0-9a-zA-Z-+_]+)/i', '<a href="http://www.twitter.com/search?q=%23$1" target="_blank">#$1</a>', $tweet->text);
        
            // twitter style time
            $current_time = time();
            $tweet_time = strtotime($tweet->created_at);
            $time_diff = abs($current_time - $tweet_time);
            switch ($time_diff) 
            {
                case ($time_diff < 60):
                    $display_time = $time_diff.' seconds ago';                  
                    break;      
                case ($time_diff >= 60 && $time_diff < 3600):
                    $min = floor($time_diff/60);
                    $display_time = $min.' minutes ago';                  
                    break;      
                case ($time_diff >= 3600 && $time_diff < 86400):
                    $hour = floor($time_diff/3600);
                    $display_time = 'about '.$hour.' hour';
                    if ($hour > 1){ $display_time .= 's'; }
                    $display_time .= ' ago';
                    break;          
                default:
                    $display_time = date('g:i A M jS', $tweet_time);
                    break;
            }

            $tweet->time_ago = $display_time;
            $tweet->tweet_url = 'http://www.twitter.com/'.$tweet->user->screen_name.'/status/'.$tweet->id_str;
        }

        return $tweets;
    }
}