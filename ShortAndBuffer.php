<?php
/*
Plugin Name: ShortAndBuffer
Plugin URI: https://github.com/twostairs/ShortAndBuffer
Description: A WordPress plugin that shortens a post's URL on publish through CloudApp and sends an update containing the post's title + short-url to buffer. This plugin was built on-top of Francis Yaconiello's great WordPress Plugin Template (https://github.com/fyaconiello/wp_plugin_template). This plugin uses CloudApp Wrapper by Matthias Plappert (https://github.com/matthiasplappert/CloudApp-API-PHP-wrapper). This plugin uses buffer class by Kevin Khandjian (https://github.com/thewebguy/bufferapp-php). This plugin does not have super-cow powers.
Version: 1.1
Author: Marius M.
Author URI: http://twostairs.com
License: GPL2
*/
/*
Copyright 2012  Marius M.  (email : marius@twostairs.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once(sprintf("%s/lib/SaB.php", dirname(__FILE__)));

if(!class_exists('ShortAndBuffer')) {
  class ShortAndBuffer {
  	public function __construct()	{
      require_once(sprintf("%s/functions.php", dirname(__FILE__)));
      require_once(sprintf("%s/settings.php", dirname(__FILE__)));
      $ShortAndBuffer_Settings = new ShortAndBuffer_Settings();
  	}
    
  	public static function activate()	{
  	}
    
  	public static function deactivate()	{
  	}
  }
}

if(class_exists('ShortAndBuffer')) {
	register_activation_hook(__FILE__, array('ShortAndBuffer', 'activate'));
	register_deactivation_hook(__FILE__, array('ShortAndBuffer', 'deactivate'));

	$shortandbuffer = new ShortAndBuffer();
  if(isset($shortandbuffer)) {
    function shortandbuffer_settings_link($links) { 
        $settings_link = '<a href="options-general.php?page=shortandbuffer">Settings</a>'; 
        array_unshift($links, $settings_link); 
        return $links; 
    }
        
    function shortandbuffer_publish_post($postId) {
      $sab = new SaB();
      $sab->postId($postId);
      if(!$sab->isPost()) {
        return;
      }
      
      $postShortLink = $sab->shortenUrl($sab->postLink(), $sab->postTitle());
      // $postShortLink = "http://example.com";
      
      $sab->bufferUpdate();

      // success!
      return;
    }
        
    $plugin = plugin_basename(__FILE__); 
    add_filter("plugin_action_links_$plugin", 'shortandbuffer_settings_link');
    // add_filter("publish_post", 'shortandbuffer_publish_post');
    add_action('draft_to_publish', 'shortandbuffer_publish_post');
    add_action('new_to_publish', 'shortandbuffer_publish_post');
    add_action('pending_to_publish', 'shortandbuffer_publish_post');
  }
}