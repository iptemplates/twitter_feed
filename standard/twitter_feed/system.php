<?php
/*
	Plugin name: Twitter feed
	Copyright: Copyright (C) 2013 JSC "Insightio"
	License: MIT, see README.md
*/

namespace Modules\standard\twitter_feed;     

if (!defined('CMS')) exit;

class System {

	function init() {
		global $site;

		$public_dir = BASE_URL.PLUGIN_DIR.'standard/twitter_feed/public/';
		$css_dir = $public_dir.'css/';
		$img_dir = $public_dir.'img/';
		$js_dir  = $public_dir.'js/';

		$site->addCss($css_dir.'twitter_feed.css');
	}

}
