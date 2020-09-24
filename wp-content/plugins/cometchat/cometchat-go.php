<?php

/**
 *
 *
 * @package cometchat
 */
	if ( ! defined( 'ABSPATH' ) ) exit;

	include_once(ABSPATH.'wp-admin'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'plugin.php');

	if ( !current_user_can( 'activate_plugins' ) ) {
		exit("You don't have permission to access this plugin.");
	}

	$adminpanelurl = $cometchatPluginPath = $cometchatLogo = '';

	$cometchatPluginPath = plugin_dir_url( __FILE__ );
	if(!defined('CC_PLUGIN_REFRRER')) define('CC_PLUGIN_REFRRER', $cometchatPluginPath);

	$cometchatLogo = esc_url($cometchatPluginPath.'images/cometchat_logo.png');
	$cometchatDockedLayout = esc_url($cometchatPluginPath.'images/docked_layout.svg');
	$cometchatAuthKey = esc_url($cometchatPluginPath.'images/cometchat_auth.png');

	if(!empty($cc_clientid) || !empty($_COOKIE['cc_cloud'])) {
		$cc_client_url = (!empty($_COOKIE['cc_cloud']) && $_COOKIE['cc_cloud'] != 1) ? $_COOKIE['cc_cloud'] : $cc_clientid;
		if($cc_client_url < 50000){
			$adminpanelurl = esc_url("//".$cc_client_url.".cometondemand.net/admin/");
		}else{
			$adminpanelurl = esc_url("https://secure.cometchat.com/licenses/access/".$cc_client_url);
		}
	}

	/** Initial access of CometChat Installation **/
	$isCometReady = get_option('ccintialaccess');

	/** Check if buddypress intalled or not **/
	if(empty($isCometReady) && is_plugin_active('buddypress/bp-loader.php')){
		update_option('show_friends', 'true');
		curlRequestToCometChatAPI('updateUserListSetting', array(
				'setting_key' => 'show_friends',
				'setting_value' => 'true'
			)
		);
	}

	if(!empty($cc_clientid) || !empty($_COOKIE['cc_cloud'])){
		if(empty($isCometReady)){
			include_once(plugin_dir_path(__FILE__).DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'comet-auth.php');
			add_option('ccintialaccess','1','','no');
		}elseif($isCometReady == '1'){
			include_once(plugin_dir_path(__FILE__).DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'comet-ready.php');
			update_option('ccintialaccess','2');
		}else {
			include_once(plugin_dir_path(__FILE__).DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'comet-admin.php');
		}
	}else{
		$dir = plugin_dir_path( __FILE__ ).'installer.php';
		require_once($dir);
	}
?>