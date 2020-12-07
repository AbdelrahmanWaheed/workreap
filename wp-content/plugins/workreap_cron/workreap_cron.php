<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://themeforest.net/user/amentotech/portfolio
 * @since             1.0
 * @package           Workreap Cron
 *
 * @wordpress-plugin
 * Plugin Name:       Workreap Cron
 * Plugin URI:        https://themeforest.net/user/amentotech/portfolio
 * Description:       This plugin is used for creating cron jobs for Workreap WordPress Theme
 * Version:           1.4.0
 * Author:            Amentotech
 * Author URI:        https://themeforest.net/user/amentotech
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       workreap_cron
 * Domain Path:       /languages
 */

/**
 * Active plugin
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists('workreap_cron_activation') ) {
	register_activation_hook (__FILE__, 'workreap_cron_activation');
	add_action('wp', 'workreap_cron_activation');
	function workreap_cron_activation() {	
		
		if( !wp_next_scheduled( 'workreap_payout_listing' ) ) { 
			if (function_exists('fw_get_db_settings_option')) {
				$cron_interval  = fw_get_db_settings_option('cron_interval');
				$interval		= !empty( $cron_interval )  ? $cron_interval : 'monthly';
			} else {
				$interval	= 'monthly';
			}
			
			if( !empty ( $interval ) ) {
				wp_schedule_event( time(), $interval, 'workreap_payout_listing' );  
			}
		}
		
		if( ! wp_next_scheduled( 'workreap_post_job_notification' ) ) { 
			if (function_exists('fw_get_db_settings_option')) {
				$cron_job_interval  = fw_get_db_settings_option('cron_job_interval');
				$interval		= !empty( $cron_job_interval )  ? $cron_job_interval : 'daily';
			} else {
				$interval	= 'daily';
			}
			
			wp_schedule_event( time(), $interval , 'workreap_post_job_notification');
		}
		
		if( ! wp_next_scheduled( 'workreap_job_invitation_auto_cancel' ) ) { 
			wp_schedule_event( time(), 'hourly', 'workreap_job_invitation_auto_cancel' );
		}

		if ( ! wp_next_scheduled( 'workreap_update_featured_expiry_listing' ) ) {
		  wp_schedule_event( time(), 'hourly', 'workreap_update_featured_expiry_listing' );
		}
	}
}

/**
 * Update expiry
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists('workreap_update_featured_expiry_listing') ) {
	function workreap_update_featured_expiry_listing() {
		//Projects
		$query_args = array(
			'posts_per_page' 	  => -1,
			'post_type' 	 	  => array( 'projects' ),
			'post_status' 	 	  => array( 'publish' ),
			'ignore_sticky_posts' => 1,
			'meta_query' 			=> array(
											array(
												'key'   => '_featured_job_string',
												'value' => 1,
											)
										)
		);
		
		$all_posts 		= get_posts( $query_args );

		foreach( $all_posts as $key => $item ){
			$current_time   = strtotime( current_time( 'mysql' ) );
			$get_expiry	= get_post_meta($item->ID,'_expiry_string',true);
			$get_expiry	= !empty($get_expiry) ? $get_expiry : 0;
			
			if( empty( $get_expiry ) || $get_expiry < $current_time  ){
				update_post_meta( $item->ID, '_featured_job_string', 0 );
			}
		}
		
		//Services expiry
		$query_args = array(
			'posts_per_page' 	  => -1,
			'post_type' 	 	  => array( 'micro-services' ),
			'post_status' 	 	  => array( 'publish' ),
			'ignore_sticky_posts' => 1,
			'meta_query' 			=> array(
											array(
												'key'   => '_featured_service_string',
												'value' => 1,
											)
										)
		);
		
		$all_posts 		= get_posts( $query_args );

		foreach( $all_posts as $key => $item ){
			$current_time   = strtotime( current_time( 'mysql' ) );
			$get_expiry	= get_post_meta($item->ID,'_expiry_string',true);
			$get_expiry	= !empty($get_expiry) ? $get_expiry : 0;
			
			if( empty( $get_expiry ) || $get_expiry < $current_time  ){
				update_post_meta( $item->ID, '_featured_service_string', 0 );
			}
		}
		
		//Freelancers expiry
		$query_args = array(
			'posts_per_page' 	  => -1,
			'post_type' 	 	  => array( 'freelancers' ),
			'post_status' 	 	  => array( 'publish' ),
			'ignore_sticky_posts' => 1,
			'meta_query' 			=> array(
											array(
												'key'   => '_featured_timestamp',
												'value' => 1,
											)
										)
		);
		
		$all_posts 		= get_posts( $query_args );

		foreach( $all_posts as $key => $item ){
			$current_time   = strtotime( current_time( 'mysql' ) );
			$get_expiry	= get_post_meta($item->ID,'_expiry_string',true);
			$get_expiry	= !empty($get_expiry) ? $get_expiry : 0;
			
			if( empty( $get_expiry ) || $get_expiry < $current_time  ){
				update_post_meta( $item->ID, '_featured_timestamp', 0 );
			}
		}
		
	}
	add_action( 'workreap_update_featured_expiry_listing', 'workreap_update_featured_expiry_listing' );
}

/**
 * Cron schedule weekly and monthly
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists('workreap_cron_schedule') ) {
	function workreap_cron_schedule( $schedules = array() ) {
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display' => esc_html__('Once a weekly','workreap_cron')
		);
		$schedules['monthly'] = array(
			'interval' => 2635200,
			'display' => esc_html__('Once a month','workreap_cron')
		);
		$schedules['half_month'] = array(
			'interval' => 1296000,
			'display' => esc_html__('Twice a month','workreap_cron')
		);
		$schedules['daily'] = array(
			'interval' => 86400,
			'display' => esc_html__('Once a day','workreap_cron')
		);
		$schedules['mints5daily'] = array(
			'interval' => 300,
			'display' => esc_html__('Every 5 mints','workreap_cron')
		);
		return $schedules; 
	}
	add_filter( 'cron_schedules', 'workreap_cron_schedule' );
}

/**
 * Payouts
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists('workreap_payouts_function') ) {
	function workreap_payouts_function() {
		global $wpdb;
		
		if (function_exists('fw_get_db_settings_option')) {
			$min_amount = fw_get_db_settings_option('min_amount');
		} else {
			$min_amount	= 0;
		}
		
		$min_amount	= !empty($min_amount) ? floatval( $min_amount ) : 0;
		
		$table_name 	= $wpdb->prefix . "wt_earnings";
		$payouts_table 	= $wpdb->prefix . "wt_payouts_history";
		$insert_payouts	= array();

		$current_date 						= current_time('mysql');
		$gmt_time							= current_time( 'mysql', 1 );
		$insert_payouts['processed_date'] 	= date('Y-m-d H:i:s', strtotime($current_date));
		$insert_payouts['processed_date'] 	= date('Y-m-d H:i:s', strtotime($current_date));
		$insert_payouts['year'] 			= date('Y', strtotime($current_date));
		$insert_payouts['month'] 			= date('m', strtotime($current_date));
		$insert_payouts['timestamp'] 		= strtotime($current_date);
		$insert_payouts['status']			= 'inprogress';
		
		if( function_exists('workreap_get_current_currency') ) {
			$currency			= workreap_get_current_currency();
		} else {
			$currency['symbol']	= '$';
		}
		
		$insert_payouts['currency_symbol']	= $currency['symbol'];
		
		//wp_mail('youremail@gmail.com', 'Payouts: Site Test CRON - Shareplant', 'Funtion being called');
		
		if (function_exists('workreap_sum_earning_freelancer_payouts')) {
			$payouts	= workreap_sum_earning_freelancer_payouts('completed','freelancer_amount');
			$payout_count	= !empty($payouts) ? count( $payouts ) : 0;
			if( !empty( $payouts ) && $payout_count > 0 ) {
				foreach( $payouts as $payout ) {
					if( empty( $payout->user_id ) ){
						continue;
					}
					
					$freelance_amount	= !empty($payout->total_amount) ? floatval($payout->total_amount) :0;
					if( $freelance_amount > $min_amount ) {
						$contents	= get_user_meta($payout->user_id,'payrols',true);
						
						if( !empty( $contents['payrol'] ) && $contents['payrol'] === 'paypal' ){
							$payrol		= !empty($contents['payrol']) ? $contents['payrol'] : "";
						} else{
							$payrol		= !empty($contents['type']) ? $contents['type'] : "";
						}
						
						if( $payrol === 'paypal' ){
							if( !empty( $contents['payrol'] ) && $contents['payrol'] === 'paypal' ){
								//only for migration from release 1.0.7
								$email		= !empty($contents['email']) ? $contents['email'] : "";
							} else{
								$email		= !empty($contents['paypal_email']) ? $contents['paypal_email'] : "";
							}
							
							$insert_payouts['paypal_email']		= $email;
							
							//check if email is valid
							if( empty( $email ) || !is_email( $email ) ){
								continue;
							}
							
						} else if( $payrol === 'bacs' ){
							$bank_details	= array();
							$bank_details['bank_account_name']		= !empty($contents['bank_account_name']) ? $contents['bank_account_name'] : "";
							$bank_details['bank_account_number']	= !empty($contents['bank_account_number']) ? $contents['bank_account_number'] : "";
							$bank_details['bank_name']				= !empty($contents['bank_name']) ? $contents['bank_name'] : "";
							$bank_details['bank_routing_number']	= !empty($contents['bank_routing_number']) ? $contents['bank_routing_number'] : "";
							$bank_details['bank_iban']				= !empty($contents['bank_iban']) ? $contents['bank_iban'] : "";
							$bank_details['bank_bic_swift']			= !empty($contents['bank_bic_swift']) ? $contents['bank_bic_swift'] : "";
							$insert_payouts['payment_details']		= serialize( $bank_details );
							
							if( empty( $contents['bank_account_name'] ) || empty( $contents['bank_account_number'] ) || empty( $contents['bank_name'] ) ){
								continue;
							}
						}
						
						if( !empty( $payrol ) ) {

							$insert_payouts['user_id']			= !empty($payout->user_id) ? intval($payout->user_id) : '';
							$insert_payouts['amount']			= $payout->total_amount;
							$insert_payouts['payment_method']	= $payrol;

							if( function_exists('workreap_update_earning') ) {
								$wpdb->insert($payouts_table,$insert_payouts);
								$where		= array( 
												'user_id' => !empty($payout->user_id) ? intval($payout->user_id) : '',
												'status'  => 'completed'
											);

								$update		= array('status' => 'processed');
								workreap_update_earning($where, $update, 'wt_earnings');

								if(class_exists('Workreap_Email_helper')) {
									if (class_exists('WorkreapSendEarningNotification')) {
										$linked_profile 	= workreap_get_linked_profile_id($payout->user_id);
										$email_helper 		= new WorkreapSendEarningNotification();
										$emailData 			= array();
										$emailData['total_amount']  	= workreap_price_format($payout->total_amount, 'return');
										$emailData['freelancer_name']  	= get_the_title($linked_profile);
										$emailData['freelancer_email']  = get_userdata($payout->user_id)->user_email;
										$email_helper->send_notification_to_freelancer($emailData);
									}
								}
							}
						}
					}
				}
			}
		}
	}
	
	add_action ('workreap_payout_listing', 'workreap_payouts_function');
}

/**
 * Job Notifications
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists('workreap_post_job_notification') ) {
	function workreap_post_job_notification() {
		global $wpdb;
		$search_job_tpl	= '';
		if (function_exists('fw_get_db_settings_option')) {
			$job_notification  	= fw_get_db_settings_option('job_notification');
			$search_job_tpl  	= fw_get_db_settings_option('search_job_tpl');
		}
		
		$job_search_url	= !empty( $search_job_tpl[0] ) ? get_page_link( $search_job_tpl[0] ) : get_site_url() ;
		
		//wp_mail('youremail@gmail.com', 'Job: Site Test CRON - Shareplant', 'Funtion being called');
		
		if (class_exists('WorkreapJobNotification') && class_exists('Workreap_Email_helper') ) {
			$email_helper 		= new WorkreapJobNotification();
			$job_notification	= !empty( $job_notification['gadget']) ? $job_notification['gadget'] : '';
			
			if( !empty( $job_notification ) && $job_notification === 'enable' ) {
				$today = getdate();
				
				$query = new WP_Query( 'post_type=projects&year=' . $today['year'] . '&monthnum=' . $today['mon'] . '&day=' . $today['mday'] );
				$latest_jobs	= '';

				$skills	= array();
				if( $query->have_posts() ) {
					$latest_jobs	= '<ul style="float: left;width: 100%; margin-bottom:20px; border: 1px solid #eee;border-radius: 4px;overflow: hidden; padding: 0; margin-top: 0;box-sizing: border-box;list-style: none;">';
					$latest_jobs	.= '<li style="list-style-type:none; margin:0px;float:left; width:100%; background-color: #fafafa;padding:15px 30px;box-sizing: border-box;">
												<span style="color: #222;font-size: 12px;line-height: 20px;float:left; text-transform: uppercase;font-weight: 500;width: 60%;">'.esc_html__('Job Title','workreap_cron').'</span>
												<span style="text-align:right;width: 40%;float:right;color: #222;font-size: 12px;line-height: 20px;display: inline-block;vertical-align: middle;text-transform: uppercase;font-weight: 500;text-decoration: none;">'.esc_html__('Budget','workreap_cron').'</span>
											</li>';
					
					$skills_data = array(); 
					while ($query->have_posts()) { 
						$query->the_post();
						global $post;
						$skill_data 	= wp_get_post_terms( $post->ID, 'skills' );	
						$skills_items	= !empty( $skill_data ) ? wp_list_pluck($skill_data, 'term_id') : array();
						$skills_data[]	= $skills_items;
						
						if (function_exists('fw_get_db_post_option')) {
							$db_project_type      = fw_get_db_post_option($post->ID,'project_type');
						}
						
						$project_price	= workreap_project_price($post->ID);
						
						$proposal_page = array();
						if (function_exists('fw_get_db_post_option')) {
							$proposal_page = fw_get_db_settings_option('dir_proposal_page');
						}

						$proposal_page_id = !empty( $proposal_page[0] ) ? $proposal_page[0] : '';
						$submit_proposal  = !empty( $proposal_page_id ) ? get_the_permalink( $proposal_page_id ) : '';		
						$submit_proposal  = !empty( $submit_proposal ) ? add_query_arg( 'project_id', $post->ID, $submit_proposal ) : '';
						
						$short_description	= wp_trim_words( get_the_excerpt( $post->ID ), 12 );
						$short_description	= !empty( $short_description ) ? $short_description : '';
						
						$permalink			= get_the_permalink( $post->ID );
						$title			 	= get_the_title( $post->ID );
						
						$latest_jobs		.= '<li style="float: left;margin:0px;width: 100%;border-top: 1px solid #eee;list-style: none;padding: 20px 30px;box-sizing: border-box;">';
						$latest_jobs		.= '<div style="float:left;width: 60%">';
						$latest_jobs	 	.= '<h3 style="font-weight: 400;margin: 0 0 7px;font-size: 14px;line-height: 16px;"><a href="'.esc_url( $permalink ).'" style="text-decoration: none;color: #767676;">'.esc_html($title).'</a></h3>';
						$latest_jobs		.= '<p style="font-size: 12px;line-height: 18px;margin: 0 0 10px;">'.do_shortcode($short_description).'</p>';
						$latest_jobs		.= '</div>';
						$latest_jobs		.= '<div style="float: right;width: 40%; text-align: right;">';
						$latest_jobs		.= '<strong style="float: left; width: 100%; margin-bottom: 10px; font-size: 12px; line-height: 20px;">'.do_shortcode($project_price['cost']).'</strong>';
						$latest_jobs		.= '<a href="'.esc_url($submit_proposal).'" style="color: #fff; padding: 0 10px; background: #ff5851; position: relative; text-align: center; border-radius: 5px; display: inline-block; vertical-align: middle; font-size: 11px;line-height: 30px;font-weight: 700;text-decoration: none;">'.esc_html__('Send Proposal','workreap_cron').'</a>';
						$latest_jobs		.= '</div>';
						$latest_jobs		.= '</li>';
					}
					
					$latest_jobs	.= '</ul>';
					
					//Send emails
					$meta_query_args	= array();
					$tax_query_args  	= array();
					
					$meta_query_args[] = array(
						'key' 			=> '_profile_blocked',
						'value' 		=> 'off',
						'compare' 		=> '='
					); 

					$meta_query_args[] = array(
						'key' 			=> '_is_verified',
						'value' 		=> 'yes',
						'compare' 		=> '='
					);
					
					$skills_data	= !empty($skills_data) ? array_unique($skills_data) : array();

					if ( !empty($skills_data[0]) && is_array($skills_data) ) {    
						$query_relation = array('relation' => 'OR',);
						$skills_args    = array();

						foreach( $skills_data as $key => $skill ){
							$skills_args[] = array(
									'taxonomy' => 'skills',
									'field'    => 'term_id',
									'terms'    => $skill,
								);
						}

						$tax_query_args[] = array_merge($query_relation, $skills_args);
					}
					
					$query_args = array(
						'posts_per_page' 	  => -1,
						'post_type' 	 	  => 'freelancers',
						'post_status' 	 	  => 'publish',
						'ignore_sticky_posts' => 1
					);
	
					//Taxonomy Query
					if ( !empty( $tax_query_args ) ) {
						$query_relation = array('relation' => 'AND',);
						$query_args['tax_query'] = $tax_query_args;    
					}

					//Meta Query
					if (!empty($meta_query_args)) {
						$query_relation = array('relation' => 'AND',);
						$meta_query_args = array_merge($query_relation, $meta_query_args);
						$query_args['meta_query'] = $meta_query_args;
					}

					$freelancer_data = new WP_Query($query_args); 

					if ($freelancer_data->have_posts()) {
						while ($freelancer_data->have_posts()) { 
							$freelancer_data->the_post();
							global $post;
							$linked_profile 		= $post->ID;
							$author_id 				= workreap_get_linked_profile_id($linked_profile, 'post');
							$freelancer_title 		= esc_html( get_the_title( $linked_profile ));
							
							if( !empty( $author_id ) ) {
								$table_review = $wpdb->prefix . "users";
								$db_email_query = $wpdb->get_row( "SELECT user_email from $table_review WHERE ID = $author_id",ARRAY_A);
								
								if( !empty( $db_email_query['user_email'] ) ) {
									$emailData 				= array();
									$emailData['email']					= $db_email_query['user_email'];
									$emailData['freelancer_name']		= $freelancer_title;
									$emailData['jobs_listings']			= $latest_jobs;
									$emailData['search_job_link']		= esc_url( $job_search_url );
									$email_helper->send_freelancers_job_notification($emailData);
								}
							}
						}
					}
				}
			}
		}
	}
	add_action ('workreap_post_job_notification', 'workreap_post_job_notification');
}

/**
 * Job Invitation Automatic Cancellation
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists('workreap_job_invitation_auto_cancel') ) {
	function workreap_job_invitation_auto_cancel() {

		// get projects whose invitation period has passed the allowed period for accepting the project by the freelancer
        $notice_priod = fw_get_db_settings_option('job_invitation_cancellation_priod_hours');
        $notice_priod = intval( $notice_priod );
		$args = array(
			'post_type' 		=> 'projects',
			'posts_per_page' 	=> -1,
			'post_status' 		=> 'private',
			'meta_query' 		=> array(
				'relation' 		=> 'AND',
				array(
					'key' 		=> 'invitation_time',
					'value' 	=> strtotime( sprintf("-%d hour", $notice_priod), current_time('timestamp') ),
					'compare' 	=> '<=',
					'type' 		=> 'NUMERIC',
				),
				array(
					'key' 		=> 'suggested_freelancers',
					'compare' 	=> 'EXISTS',
				),
			),
		);
		$projects = get_posts( $args );
		if( !empty( $projects ) ) {
			foreach ($projects as $project) {
				$freelancer = get_post_meta( $project->ID, 'suggested_freelancers', true );
				workreap_delete_freelancer_from_project_invitations( $project->ID, $freelancer );

				// send notification to the freelancer in case of automatic cancellation to the project invitation
		        if( class_exists('NotificationSystem') ) {
		            $message = apply_filters( 'workreap_job_invitation_auto_cancellation_message', $project->ID );
		            $subject = esc_html__('Job Invitation Automatic Cancellation', 'workreap');
		            NotificationSystem::sendNotificationWithEmail( $freelancer, $message, null, $subject );
		        }
			}
		}
	}
	add_action ('workreap_job_invitation_auto_cancel', 'workreap_job_invitation_auto_cancel');
}

/**
 * Deactive plugin
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists('workreap_cron_deactivate') ) {
	function workreap_cron_deactivate() {	
		$timestamp = wp_next_scheduled ('workreap_payout_listing');
		wp_unschedule_event ($timestamp, 'workreap_payout_listing');
		
		$next_job_scheduled	= wp_next_scheduled( 'workreap_job_notification_cron' );
		wp_unschedule_event ($next_job_scheduled, 'workreap_job_notification_cron');

		$next_job_invit_auto_cancel_scheduled = wp_next_scheduled( 'workreap_job_invitation_auto_cancel' );
		wp_unschedule_event ($next_job_invit_auto_cancel_scheduled, 'workreap_job_invitation_auto_cancel');
	} 
	register_deactivation_hook (__FILE__, 'workreap_cron_deactivate');
}

/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
add_action( 'init', 'workreap_cron_load_textdomain' );
function workreap_cron_load_textdomain() {
  load_plugin_textdomain( 'workreap_cron', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}