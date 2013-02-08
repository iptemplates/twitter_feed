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
        $data = array_merge($data, array(
            'default_username' => 'ip_templates',
            'default_notweets' => 10
        ));

        $rendered = \Ip\View::create($this->managementDir.'/default.php', $data)->render();
        
        return $rendered;
    }

    public function previewHtml($instanceId, $data, $layout) {   
        $data = array(
            'username' => $data['username'],
            'tweets'   => $this->getTweets($data['username'], $data['notweets'])
        );

        $rendered = \Ip\View::create($this->previewDir.'/'.$layout.'.php', $data)->render();
        
        return $rendered;
    }

    private function getTweets($username, $number){
        require_once($this->widgetDir.'/twitter.class.php');

        $twitter = new Twitter($username);

        return $twitter->getTweets($number);
    }

    private function getTranslation($key){
        global $parametersMod;

        return $parametersMod->getValue('standard', 'twitter_feed', 'translations', $key);
    }

}