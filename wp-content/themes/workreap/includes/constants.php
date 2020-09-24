<?php

/**
 *  Contants
 */
if (!function_exists('workreap_prepare_constants')) {

    function workreap_prepare_constants() {
		global $current_user;
		$current_date 		= current_time('mysql');
		$is_loggedin 		= 'false';
		$user_type			= 'false';
		$calendar_locale    = 'en';
		$calendar_format	= 'Y-m-d';
		$startweekday		= get_option('start_of_week');
		$startweekday		=  !empty( $startweekday ) ?  $startweekday : 0;
		$fbsocial_connect	= 'no';
		$fbapp_id			= '';
        if (is_user_logged_in()) {
            $is_loggedin 	= 'true';
			$user_type		= workreap_get_user_type( $current_user->ID );
        }
		
		$currency_pos = get_option( 'woocommerce_currency_pos' );
		$currency = workreap_get_current_currency();
		
        //Service Fee
        $service_fee = 0;
        $dir_datasize = 5242880;
		$default_skills = 50;
		$gclient_id		= '';
        if ( function_exists('fw_get_db_post_option' )) {
			$service_fee		= worrketic_hiring_payment_setting();
			$default_skills		= fw_get_db_settings_option('default_skills');
			$dir_chat 			= fw_get_db_settings_option('chat');
			$dir_cluster_marker = fw_get_db_settings_option('dir_cluster_marker');
            $dir_map_marker 	= fw_get_db_settings_option('dir_map_marker');
            $dir_cluster_color 	= fw_get_db_settings_option('dir_cluster_color');
            $dir_map_type 		= fw_get_db_settings_option('dir_map_type');
            $dir_zoom 			= fw_get_db_settings_option('dir_zoom');
            $dir_longitude 		= fw_get_db_settings_option('dir_longitude');
            $dir_latitude 		= fw_get_db_settings_option('dir_latitude');
			$country_restrict   = fw_get_db_settings_option('country_restrict');
            $dir_datasize 		= fw_get_db_settings_option('dir_datasize');
            $dir_map_scroll 	= fw_get_db_settings_option('dir_map_scroll');
            $map_styles 		= fw_get_db_settings_option('map_styles');
			$header_type 		= fw_get_db_settings_option('header_type');
			$tip_content_bg     = fw_get_db_settings_option('tip_content_bg');
			$tip_content_color  = fw_get_db_settings_option('tip_content_color');
			$tip_title_bg    	= fw_get_db_settings_option('tip_title_bg');
			$tip_title_color    = fw_get_db_settings_option('tip_title_color');
			$chat_settings    	= fw_get_db_settings_option('chat');
			$calendar_format    = fw_get_db_settings_option('calendar_format');
			$calendar_locale    = fw_get_db_settings_option('calendar_locale');
			$fbsocial_connect	= fw_get_db_settings_option('facebook');
			$gclient_id			= fw_get_db_settings_option('client_id');
			$gosocial_connect	= fw_get_db_settings_option('google');
			$verify_user 		= fw_get_db_settings_option('verify_user', $default_value = null);
			$login_register		= fw_get_db_settings_option('enable_login_register');
			$captcha_settings = fw_get_db_settings_option('captcha_settings');
			$site_key = fw_get_db_settings_option('site_key');
			
			$fbapp_id    		= fw_get_db_settings_option('app_id');
			$calendar_format	= !empty( $calendar_format ) ?  $calendar_format : 'Y-m-d';
			
			$proposal_price_type    	= fw_get_db_settings_option('proposal_price_type');
			
			if (!empty($dir_cluster_marker)) {
                $dir_cluster_marker = $dir_cluster_marker['url'];
            } else {
                $dir_cluster_marker = get_template_directory_uri() . '/images/cluster.png';
            }

            if (empty($dir_map_marker)) {
                $dir_map_marker = get_template_directory_uri() . '/images/marker.png';
            }

            if (empty($dir_cluster_color)) {
                $dir_cluster_color = '#7dbb00';
            }

            if (empty($dir_map_type)) {
                $dir_map_type = 'ROADMAP';
            }

            if (empty($dir_zoom)) {
                $dir_zoom = '12';
            }

            if (empty($dir_longitude)) {
                $dir_longitude = '-0.1262362';
            }

            if (empty($dir_latitude)) {
                $dir_latitude = '51.5001524';
            }

            if (empty($dir_datasize)) {
                $dir_datasize = '5242880';
            }

            if (empty($dir_map_scroll)) {
                $dir_map_scroll = 'false';
            }
        } else{
			$dir_cluster_marker = get_template_directory_uri() . '/images/cluster.png';
            $dir_map_marker = get_template_directory_uri() . '/images/marker.png';
            $dir_cluster_color = '#7dbb00';
            $dir_map_type = 'ROADMAP';
            $dir_zoom = '12';
            $dir_longitude = '-0.1262362';
            $dir_latitude = '51.5001524';
            $dir_datasize = '5242880';
            $dir_map_scroll = 'false';
            $map_styles = 'none';
            $country_restrict = '';
            $dir_close_marker = get_template_directory_uri() . '/images/close.gif';
			$loading_duration		= 500;
			$header_type 			= '';
			$proposal_price_type 	= 'any';
			$captcha_settings = '';
			$site_key = '';
		}
		
		if (!empty($country_restrict['gadget']) && $country_restrict['gadget'] === 'enable' && !empty($country_restrict['enable']['country_code'])) {
			$country_restrict = $country_restrict['enable']['country_code'];
		} else {
			$country_restrict = '';
		}
		
		$tip_content_bg		=  !empty( $tip_content_bg ) ?  $tip_content_bg : '';
		$tip_content_color	=  !empty( $tip_content_color ) ?  $tip_content_color : '';
		$tip_title_bg		=  !empty( $tip_title_bg ) ?  $tip_title_bg : '';
		$tip_title_color	=  !empty( $tip_title_color ) ?  $tip_title_color : '';
		$chat_host			=  !empty( $chat_settings['chat']['host'] ) ?  $chat_settings['chat']['host'] : 'http://localhost';
		$chat_port			=  !empty( $chat_settings['chat']['port'] ) ?  $chat_settings['chat']['port'] : '81';
		
		$detail_page_chat = 'disable';
		if( apply_filters('workreap_chat_window_floating', 'disable') === 'enable' ){
			$detail_page_chat = 'enable';
		}

		if( !empty( $header_type['gadget'] ) && $header_type['gadget'] === 'header_v2' ){
			$sticky   = !empty($header_type['header_v2']['sticky']) ? $header_type['header_v2']['sticky'] : 'disable';
		} else if( !empty( $header_type['gadget'] ) && $header_type['gadget'] === 'header_v3' ){
			$sticky   = !empty($header_type['header_v3']['sticky']) ? $header_type['header_v3']['sticky'] : 'disable';
		} else if( !empty( $header_type['gadget'] ) && $header_type['gadget'] === 'header_v4' ){
			$sticky   = !empty($header_type['header_v4']['sticky']) ? $header_type['header_v4']['sticky'] : 'disable';
		} else if( !empty( $header_type['gadget'] ) && $header_type['gadget'] === 'header_v5' ){
			$sticky   = !empty($header_type['header_v5']['sticky']) ? $header_type['header_v5']['sticky'] : 'disable';
		} else if( !empty( $header_type['gadget'] ) && $header_type['gadget'] === 'header_v6' ){
			$sticky   = !empty($header_type['header_v6']['sticky']) ? $header_type['header_v6']['sticky'] : 'disable';
		} else{
			$sticky   = !empty($header_type['header_v1']['sticky']) ? $header_type['header_v1']['sticky'] : 'disable';
		}

		
		$dir_close_marker 	= get_template_directory_uri() . '/images/close.gif';
		
        $service_fee = !empty( $service_fee['percentage'] ) ? $service_fee['percentage'] : 0;
		$login_register = !empty( $login_register['enable']['login_signup_type'] ) ? $login_register['enable']['login_signup_type'] : 'page';
		
		if( $dir_datasize >= 1024 ){
			 $dir_datasize		= trim($dir_datasize);
			 $data_size_in_kb 	= $dir_datasize / 1024;
		} else{
			$data_size_in_kb 	= 5242880;
		}
		
		$current_user_id	= !empty( $current_user->ID ) ? $current_user->ID : '';

		if(is_user_logged_in()) {
			if(apply_filters('workreap_is_listing_free',false, $current_user_id) === true ){
				$feature_skills		= $default_skills;
			} else{
				$expiry_string		= workreap_get_subscription_metadata( 'subscription_featured_string',$current_user_id );
				
				if (!empty($expiry_string) && $expiry_string > strtotime($current_date)) {
					if ( function_exists( 'workreap_is_feature_value' )) {
						$feature_skills		= workreap_is_feature_value( 'wt_no_skills', $current_user_id);
					} else {
						$feature_skills		= $default_skills;
					}
				}else {
					$feature_skills		= $default_skills;
				}
			}
		} else {
			$feature_skills		= $default_skills;
		}

		$verify_user	= !empty( $verify_user ) ? $verify_user : 'none';
		
		//chat settings
		$chat_feature	= 'inbox';	
		$chat_page		= 'no';	
		
		if( apply_filters('workreap_chat_window_floating', 'disable') === 'enable' ){
			$chat_feature	= 'chat';	
		}
		
		if( !empty( $fbsocial_connect['gadget'] ) && $fbsocial_connect['gadget'] === 'enable' ){
			$fbsocial_connect	= 'yes';	
		}
		
		if( !empty( $gosocial_connect['gadget'] ) && $gosocial_connect['gadget'] === 'enable' ){
			$gosocial_connect	= 'yes';
			
		}
		
		if ( is_page_template('directory/dashboard.php') && isset($_GET['ref']) && $_GET['ref'] === 'chat' ) {
			$chat_page		= 'yes';	
		}
		
		if($detail_page_chat == 'enable' && (is_singular('freelancers') || is_singular('micro-services') )){
			$chat_page		= 'yes';
		} 

		if(is_page_template('directory/dashboard.php') && isset($_GET['mode']) && $_GET['mode'] === 'history' ) {
			$chat_page		= 'yes';
		}
		
		if(is_user_logged_in()) {
			$expiry_string		= workreap_get_subscription_metadata( 'subscription_featured_string',$current_user_id );	
			if (!empty($expiry_string) && $expiry_string > strtotime($current_date)) {
				$feature_connects	= apply_filters('workreap_feature_connects',$current_user_id );
			}else{
				$feature_connects	= 'false';
			}
		} else{
			$feature_connects	= 'false';
		}
		
		$dir_spinner 	= get_template_directory_uri() . '/images/spinner.gif';
		$default_pdf 	= get_template_directory_uri() . '/images/pdf.jpg';
		$chatloader 	= get_template_directory_uri() . '/images/chatloader.gif';
		$is_rtl			= workreap_rtl_check();

		//Redirect URL		
		$signup_page_slug = workreap_get_signup_page_url('step', '2');

		$site_url = site_url();
		
        wp_localize_script('workreap-callbacks', 'scripts_vars', array(
            'ajaxurl'           => admin_url('admin-ajax.php'),           
            'valid_email'       => esc_html__('Please add a valid email address.','workreap'),          
            'forgot_password'   => esc_html__('Reset Password','workreap'),          
            'login'             => esc_html__('Login Now','workreap'), 
            'is_loggedin'       => $is_loggedin,
			'user_type'       	=> $user_type,
			'captcha_settings'  => $captcha_settings,
			'site_key'  		=> $site_key,
            'wishlist_message'  => esc_html__('Login to save this job', 'workreap'),
            'service_fee'       => $service_fee,
            'proposal_message'  => esc_html__('Login to send your proposal', 'workreap'),
            'proposal_amount'   => esc_html__('Only numeric values allowed', 'workreap'),
            'proposal_error'    => esc_html__('Please login to submit your proposal', 'workreap'),
			'proposal_max_val'  => esc_html__('Proposal amount is greater then project cost.', 'workreap'),
            'message_error'     => esc_html__('No kiddies please', 'workreap'),
            'skill_error'       => esc_html__('Both fields are required', 'workreap'),
			'skill_value_error' => esc_html__('Enter a valid percentage value', 'workreap'),
			
			'specification_value_error'       => esc_html__('Specification and value required', 'workreap'),
            'specification_alert_value_error' => esc_html__('Enter a valid value', 'workreap'),
			
			'already_skill_value_error' => esc_html__('This skill is already selected', 'workreap'),
			
			'award_image'       => esc_html__('Image title', 'workreap'),
			'emptyexperience'   => esc_html__('Industrial experience value is required', 'workreap'),
            'data_size_in_kb'   => $data_size_in_kb . 'kb',
            'award_image_title' => esc_html__('Your image title', 'workreap'),
            'award_image_size'  => esc_html__('File size', 'workreap'),
            'document_title'    => esc_html__('Document Title', 'workreap'),
			'emptyCancelReason' => esc_html__('Cancelled reason is required', 'workreap'),
			'featured_skills'	=> intval( $feature_skills ),
			'package_update'	=> esc_html__('Please update your package to access this service.', 'workreap'),
			'feature_connects'	=> $feature_connects,
			'connects_pkg'		=> esc_html__('You’ve consumed all you points to apply new job.', 'workreap'),
			'verify_user'		=> $verify_user,
			'jobs_message'		=> esc_html__('You’ve consumed all you points to add new job.', 'workreap'),
			'services_message'	=> esc_html__('You’ve consumed all you points to add new service.', 'workreap'),
			'loggedin_message'	=> esc_html__('Login to purchase this service.', 'workreap'),
			'service_access'	=> esc_html__('This access is only for the Employer/Company users.', 'workreap'),
			
			'spinner'   	=> '<img class="sp-spin" src="'.esc_url($dir_spinner).'">',
			'chatloader'   	=> '<img class="sp-chatspin" src="'.esc_url($chatloader).'">',
			'chatloader_single'   	=> '<div class="loader-single-chat"><img class="sp-chatspin" src="'.esc_url($chatloader).'"></div>',
			'defult_pdf'	=> $default_pdf,
			'nothing' 		=> esc_html__('Oops, nothing found!','workreap'),
			'days' 			=> esc_html__('Days','workreap'),
			'hours' 		=> esc_html__('Hours','workreap'),
			'minutes' 		=> esc_html__('Minutes','workreap'),
			'expired' 		=> esc_html__('EXPIRED','workreap'),
			'min_and' 		=> esc_html__('Minutes and','workreap'),
			'seconds' 		=> esc_html__('Seconds','workreap'),
			'yes' 			=> esc_html__('Yes','workreap'),
			'no' 			=> esc_html__('No','workreap'),
			'order' 		=> esc_html__('Add to cart','workreap'),
			'order_message' => esc_html__('Are you sure you want to buy this package?','workreap'),
			
			'delete_service'    		=> esc_html__('Delete service', 'workreap'),
			'delete_service_message'    => esc_html__('Are you sure you want to delete your service?', 'workreap'),

			'delete_portfolio'    		=> esc_html__('Delete portfolio', 'workreap'),
			'delete_portfolio_message'    => esc_html__('Are you sure you want to delete your portfolio?', 'workreap'),

			'milestone_request'    			=> esc_html__('Milestone Request', 'workreap'),
			'milestone_request_message'     => esc_html__('Milestones will be sent to freelancer, if they approve then project will be start', 'workreap'),
			
			'milestone_completed'    		=> esc_html__('Complete Milestone', 'workreap'),
			'milestone_completed_message'   => esc_html__('Are you sure you want to complete this milestone?', 'workreap'),

			'milestone_checkout'    		=> esc_html__('Milestone Payment', 'workreap'),
			'milestone_checkout_message'    => esc_html__('Are you sure you want to pay for this milestone?', 'workreap'),

			'milestone_request_approved'    		=> esc_html__('Milestone Request', 'workreap'),
			'milestone_request_approved_message'    => esc_html__('On approval, you will be hired for this project.', 'workreap'),

			'cache_title' 				=> esc_html__('Confirm?','workreap'),
			'cache_message' 			=> esc_html__('Never show this message again','workreap'),
			'delete_account'    		=> esc_html__('Delete Account', 'workreap'),
			'delete_account_message'    => esc_html__('Are you sure you want to delete your account?', 'workreap'),
			'remove_itme' 				=> esc_html__('Remove from Saved', 'workreap'),
			'remove_itme_message' 		=> esc_html__('Are you sure you want to remove this?', 'workreap'),
			'job_reopen_title' 			=> esc_html__('Job Reopen', 'workreap'),
			'job_reopen_message'    	=> esc_html__('Are you sure you want to reopen this job?', 'workreap'),
			'job_attachments' 			=> esc_html__('Job attachments','workreap'),
			'portfolio_attachments' 	=> esc_html__('Portfolio attachments','workreap'),
			'hire_freelancer'    		=> esc_html__('Hire freelancer', 'workreap'),
			'hire_freelancer_message'   => esc_html__('Are you sure you want to hire this freelancer?', 'workreap'),
			'hire_service'    			=> esc_html__('Buy this service?', 'workreap'),
			'hire_service_message'    	=> esc_html__('Are you sure you want buy this service?', 'workreap'),
			'required_field' 			=> esc_html__('field is required','workreap'),
			'delete_dispute'    		=> esc_html__('Delete this dispute?', 'workreap'),
			'delete_dispute_message'    => esc_html__('Are you sure you want delete this dispute?', 'workreap'),
			'cancel_job'    			=> esc_html__('Cancel Project?', 'workreap'),
			'cancel_job_message'    	=> esc_html__('Are you sure you want cancel this project?', 'workreap'),

			'freelancer_action' 		=> esc_html__('Only freelancer can perform this action!', 'workreap'),
			'employer_action' 			=> esc_html__('Only employer can perform this action!', 'workreap'),
			
			'portfolio_required'    			=> esc_html__('At-least one portfolio image is required', 'workreap'),
			'login_first'		=> esc_html__('Please login your account before buy these packages', 'workreap'),
			'dir_close_marker' 	=> $dir_close_marker,
            'dir_cluster_marker'=> $dir_cluster_marker,
            'dir_map_marker' 	=> $dir_map_marker,
            'dir_cluster_color' => $dir_cluster_color,
            'dir_map_type' 		=> $dir_map_type,
            'dir_zoom' 			=> $dir_zoom,
            'dir_longitude' 	=> $dir_longitude,
            'dir_latitude' 		=> $dir_latitude,
            'map_styles' 		=> $map_styles,
            'country_restrict'  => $country_restrict,
            'dir_map_scroll' 	=> $dir_map_scroll,
			'dir_datasize' 		=> $dir_datasize,
			'chat_settings'		=> $chat_feature,
			'chat_page'			=> $chat_page,
			'sticky_header'		=> $sticky,
			'tip_content_bg' 	=> $tip_content_bg,
			'tip_content_color' => $tip_content_color,
			'tip_title_bg' 		=> $tip_title_bg,
			'tip_title_color' 	=> $tip_title_color,
			'chat_host' 		=> $chat_host,
			'chat_port' 		=> $chat_port,
			'is_rtl' 			=> $is_rtl,
			'calendar_format' 	=> $calendar_format,
			'calendar_locale' 	=> $calendar_locale,
			'startweekday' 		=> $startweekday,
			'country_restrict'  => $country_restrict,
			'fbsocial_connect'  => $fbsocial_connect,
			'fbapp_id'  		=> $fbapp_id,
			'gclient_id'		=> $gclient_id,
			'proposal_price_type' 		=> $proposal_price_type,
			'currency_pos'				=> $currency_pos,
			'login_register_type'		=> $login_register,
			'authentication_url'		=> $signup_page_slug,
			'site_url'		=> $site_url,
			'current_user'		=> $current_user->ID,
			'currency_code'		=> $currency['code'],
			'currency_symbol'		=> $currency['symbol']
        ));
    }

    add_action('wp_enqueue_scripts', 'workreap_prepare_constants', 90);
}