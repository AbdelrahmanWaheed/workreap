<?php

$groupSync = get_option('bp_group_sync');

/**
* createCometChatBaseData
* Return object
* @param (type) no param
* @return (object)
*/
function createCometChatBaseData(){
	global $cc_clientid;
	global $cc_base;

	if(!empty($cc_base)) {
		wp_enqueue_script( 'cc_base', plugin_dir_url( __DIR__ ).'js/scripttag.js');
		wp_add_inline_script( 'cc_base', 'var cc_base = '.$cc_base.';' );
	}else{
		if(get_option('cc_auth_key')){
			wp_enqueue_script( 'cc_base', plugin_dir_url( __DIR__ ).'js/scripttag.js');
			wp_add_inline_script( 'cc_base', 'var chat_auth = "'.get_option('cc_auth_key').'";' );
		}
	}
}

/**
 * getCometChatCloudDockedLayoutCode
 * Return cloud docked layout html code
 * @param (type) no param
 * @return (string) footer code
*/
function getCometChatCloudDockedLayoutCode() {
	global $cc_clientid;
	global $cc_base;

	wp_enqueue_style("cc_corecss", "//fast.cometondemand.net/".$cc_clientid."x_x".substr(md5($cc_clientid),0,5).".css");
	wp_enqueue_script("cc_corejs", "//fast.cometondemand.net/".$cc_clientid."x_x".substr(md5($cc_clientid),0,5).".js");

}

/**
 * getCometChatShortCode
 * @param mixed $atts = width, height, layout, groupid, groupsonly
 * @return shortcode
 */
function getCometChatShortCode($atts){
	global $cc_clientid;
	global $cc_base;

    extract(shortcode_atts(
    	array(
		    'width' => 400,
		    'height' => 420,
		    'layout' => 'embedded',
		    'groupid' => 0,
		    'groupsonly' => 0
    	), $atts)
	);

    $site_url = get_site_url();

    if(!empty($groupsonly)){
        $groupsonly = 1;
    }

    if($layout == 'docked'){
    	wp_enqueue_style("cc_corecss", "//fast.cometondemand.net/".$cc_clientid."x_x".substr(md5($cc_clientid),0,5).".css");
		wp_enqueue_script("cc_corejs", "//fast.cometondemand.net/".$cc_clientid."x_x".substr(md5($cc_clientid),0,6).".js");
		/** Force enabled CometChat Docked Layout (6) in cc_corejs **/
    } else{

    	wp_enqueue_script( 'cc_shortcodejs', '//fast.cometondemand.net/'.$cc_clientid."x_x".substr(md5($cc_clientid),0,5).'x_xcorex_xembedcode.js' );

    	wp_enqueue_script( 'cc_shortcode', plugin_dir_url( __DIR__ ).'js/scripttag.js' );
   		wp_add_inline_script( 'cc_shortcode', 'var iframeObj = {};iframeObj.module="synergy";iframeObj.style="min-height:420px;min-width:350px;";iframeObj.width="'.$width.'px";iframeObj.height="'.$height.'px";iframeObj.src="//'.$cc_clientid.'.cometondemand.net/cometchat_embedded.php?crid='.$groupid.'&chatroomsonly='.$groupsonly.'";if(typeof(addEmbedIframe)=="function"){addEmbedIframe(iframeObj);}' );

        return '
	        <div id="cometchat_embed_synergy_container" style="width:'.$width.'px;height:'.$height.'px;max-width:100%;border:1px solid #CCCCCC;border-radius:5px;overflow:hidden;"></div>';
    }
}

/**
 * cometchatUserDetails
 * Return cc_base for user login
 * @param (type) no param
*/
function cometchatUserDetails() {
	global $cc_base;
	global $current_user;
	global $role,$user_info;

	$link = $avatar = $user_id = $user_name = $userRole = $friends = '';

	if(is_user_logged_in()) {
		$user_id = $current_user->ID;
		$user_name = $current_user->user_login;
		$display_name = $current_user->display_name;
		$role = reset($current_user->roles);
		if(!empty($display_name)){
			$user_name = $display_name;
		}
		$avatar = get_avatar_url($user_id);

		if(function_exists('bp_core_fetch_avatar')) {
			$avatar = bp_core_fetch_avatar(array(
					'item_id' 	=> $user_id,
					'type' 		=> 'thumb',
					'width' 	=> 32,
					'height'	=> 32,
					'class' 	=> 'friend-avatar',
					'html'		=> false
				)
			);
		}

		if(function_exists('bp_loggedin_user_domain')) {
			$link = bp_loggedin_user_domain();
		} else {

			$link_temp = get_userdata($user_id)->user_url;

			if (!empty($link_temp)) {
				$link = $link_temp;
			} else {
				$link = '';
			}
		}

		if(function_exists('bp_get_friend_ids')) {
			$friends = bp_get_friend_ids($user_id);
			if(empty($friends)){
				$friends = "";
			}
		}

		$user_info = array(
			"id"		=> $user_id,
			"n"			=> $user_name,
			"dn"		=> $display_name,
			"a"			=> $avatar,
			"l"			=> $link,
			"role"		=> $role,
			"friends"	=> $friends
		);
		if(function_exists('mycred_get_users_balance')){
			$user_info['balance'] = mycred_get_users_balance( $user_id );
		}
		if(!empty($current_user->user_email)) {
			$user_info['email'] = $current_user->user_email;
		}
		if(get_option('cc_auth_key')){
			$user_info['auth'] = get_option('cc_auth_key');
		}
		if(get_option('cc_api_key')){
			$api_key = get_option('cc_api_key');
			$user_info['signature'] = md5(implode(',', array($user_id,$user_name,$api_key)));
		}
		$cc_base = json_encode($user_info);
	}
}

/**
 * cc_buddypress_groups_sync
 * Return create group
 * @param (type) no param
*/
function cc_buddypress_groups_sync() {
	global $cc_clientid;
	global $current_user;
	$user_id = $current_user->ID;
	$buddypressgroupinfo = array();

	if(bp_is_active( 'groups' )) {
		$groups = BP_Groups_Group::get(array('type'=>'active','per_page'=>10));
		foreach ($groups['groups'] as $group) {
			if ($group->status != 'public') {
				$members = BP_Groups_Member::get_group_member_ids($group->id);
				$buddypressgroupinfo[$group->id] = array('groupid' => $group->id, 'groupname'=>$group->name,'clearExisting'=>true,'type'=> intval(4),'members'=>$members);
			}else{
				$members = BP_Groups_Member::get_group_member_ids($group->id);
				$buddypressgroupinfo[$group->id] = array('groupid' => $group->id, 'groupname'=>$group->name,'clearExisting'=>true,'type'=>0,'members'=>$members);
			}
		}
		curlRequestToCometChatAPI('sendgroupinfo',array('buddypressgroupinfo' =>$buddypressgroupinfo));
	}
}

/**
 * cc_activation
 * Return create schedular
 * @param (type) no param
*/
function cc_activation() {
	if (! wp_next_scheduled ( 'cc_buddypress_groups_sync_scheduler' )) {
		wp_schedule_event(time(), 'hourly', 'cc_buddypress_groups_sync_scheduler');
	}
}

/**
 * cc_deactivation
 * Return clear schedular
 * @param (type) no param
*/

function cc_deactivation() {
	wp_clear_scheduled_hook('cc_buddypress_groups_sync_scheduler');
	wp_clear_scheduled_hook('groups_group_create_complete');
}

add_action('wp_head', 'createCometChatBaseData',1);
add_action('wp_head', 'getCometChatCloudDockedLayoutCode');
add_action('init','cometchatUserDetails');
add_shortcode('cometchat', 'getCometChatShortCode');

if(function_exists('bp_is_active') && $groupSync) {
	add_action('cc_buddypress_groups_sync_scheduler', 'cc_buddypress_groups_sync');
	add_action( 'groups_group_create_complete',  'cc_buddypress_groups_sync' );
}

?>
