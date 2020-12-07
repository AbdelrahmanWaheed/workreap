<?php
/**
 * Packages options
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (class_exists('WooCommerce')) {
	if (!function_exists('workreap_packages_option')) {
		add_action( 'init', 'workreap_packages_option' );

		function workreap_packages_option(){
			
			$offline_package		= worrketic_hiring_payment_setting();
			$offline_package		= !empty($offline_package['type']) ? $offline_package['type'] : '';
			
			if( !empty($offline_package) && $offline_package === 'offline_woo' ){
				add_filter( 'woocommerce_cod_process_payment_order_status','workreap_code_status_callback', 10, 2 );
				if( is_admin() ){
					add_action( 'woocommerce_order_status_completed','workreap_payment_complete',10,1 );
					add_action( 'woocommerce_order_status_on-hold','workreap_payment_complete',10,1 );
				} else {
					add_action( 'woocommerce_order_status_completed','workreap_offline_onhold',10,1 );
					add_action( 'woocommerce_order_status_on-hold','workreap_offline_onhold',10,1 );
				}
				
			} else {
				add_filter('woocommerce_payment_gateways', 'workreap_unused_payment_gateways', 20, 1);
			}

		}
	}
}

/**
 * PayPal Order process
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */

if (!function_exists('workreap_paypal_payment_complete_order_status')) {
	add_filter('woocommerce_payment_complete_order_status', 'workreap_paypal_payment_complete_order_status', 10, 2 );
	function workreap_paypal_payment_complete_order_status( $order_status, $order_id ){
		$order = wc_get_order( $order_id );
		if( $order->get_payment_method() === 'paypal' ){
			$order_status = 'completed';
		}

		return $order_status;
	}
}
/**
 * cahnge status on cash on delivery 
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */

if (!function_exists('workreap_code_status_callback')) {

	function workreap_code_status_callback( $status,$order  ) {
		return 'on-hold';
	}
}

/**
 * offline packages after checkout
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */

if (!function_exists('workreap_offline_onhold')) {

	function workreap_offline_onhold( $order_id  ) {
		$order 		= wc_get_order($order_id);
        $user 		= $order->get_user();
		$items 		= $order->get_items();
		$hiring_id	= get_post_meta( $order_id, '_hiring_id',true);
		if( empty($hiring_id) ){
			foreach ($items as $key => $item) {
				$order_detail 					= wc_get_order_item_meta( $key, 'cus_woo_product_data', true );

				if ($user) {
					$payment_type = wc_get_order_item_meta( $key, 'payment_type', true );
					if (class_exists('Workreap_Email_helper')) {
						if (class_exists('WorkreapOfflinePackages')) {
							$email_helper 				= new WorkreapOfflinePackages();
							$emailData 					= array();
							$emailData['email_to']		= !empty( $user->user_email ) ? is_email($user->user_email) : '';
							$emailData['employer_name'] = !empty( $user->ID ) ? workreap_get_username( $user->ID ) : '';
							
							if( !empty( $payment_type ) && $payment_type == 'hiring' ) {
								$proposal_id		= !empty($order_detail['proposal_id']) ? intval($order_detail['proposal_id']) : 0;
								$project_id			= !empty( $order_detail['project_id'] ) ? intval($order_detail['project_id']) : 0;
								$emailData['order_name']		= !empty( $project_id ) ? get_the_title($project_id) : '';
								$emailData['order_link']		= !empty( $project_id ) ? get_the_permalink($project_id) : '';
								
								update_post_meta( $order_id, '_hiring_id', $proposal_id );
								update_post_meta( $project_id, '_order_id', $order_id );
								update_post_meta( $proposal_id, '_order_id', $order_id );
								$email_helper->recived_offline_order($emailData);
							} else if( !empty( $payment_type )  && $payment_type == 'hiring_service') {
								$service_id						= !empty( $order_detail['service_id'] ) ? intval($order_detail['service_id']) : 0;
								$emailData['order_name']		= !empty( $service_id ) ? get_the_title($service_id) : '';
								$emailData['order_link']		= !empty( $service_id ) ? get_the_permalink($service_id) : '';
								$email_helper->recived_offline_order($emailData);
							}  else if( !empty( $payment_type )  && $payment_type == 'milestone') {
								$milestone_id	= !empty( $order_detail['milestone_id'] ) ? $order_detail['milestone_id'] : '';
								$project_id		= !empty( $order_detail['project_id'] ) ? intval($order_detail['project_id']) : 0;
								update_post_meta( $order_id, '_hiring_id', $milestone_id );
								update_post_meta( $milestone_id, '_order_id', $order_id );
							}
							
						}
					}
				}
			}
		}
	}
}

/**
 * Complete order
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_payment_complete')) {
    add_action('woocommerce_payment_complete', 'workreap_payment_complete');
	// add_action( 'woocommerce_order_status_completed','workreap_payment_complete' );
    function workreap_payment_complete($order_id) {
		global $current_user, $wpdb;
		
        $order 		= wc_get_order($order_id);
        $user 		= $order->get_user();
        $items 		= $order->get_items();
        $offset 	= get_option('gmt_offset') * intval(60) * intval(60);
		
		//Update order status
		$order->update_status( 'completed' );
		$order->save();
				
		$invoice_id = esc_html__('Order #','workreap') . '&nbsp;' . $order_id;
        foreach ($items as $key => $item) {
            $product_id 	= !empty($item['product_id']) ? intval($item['product_id']) : '';
            $product_qty 	= !empty($item['qty']) ? $item['qty'] : 1;

            if ($user) {
				$payment_type = wc_get_order_item_meta( $key, 'payment_type', true );
				if( !empty( $payment_type ) && $payment_type == 'hiring' ) {
					workreap_update_hiring_data( $order_id );
					//update api key data
					//Get Project ID From Order ID
					$proposal_id = get_post_meta($order_id, '_hiring_id', true);
					$project_id = '';
					if(!empty($proposal_id)) {
						$project_id = get_post_meta($proposal_id, '_project_id', true);
					}
					if(!empty($project_id)) {
						if( apply_filters('workreap_filter_user_promotion', 'disable') === 'enable' ){	
							do_action('workreap_update_users_marketing_product_creation', $current_user->ID, $project_id, 'product_status_update');
						}
					}
				} else if( !empty( $payment_type )  && $payment_type == 'hiring_service') {
					workreap_update_hiring_service_data( $order_id,$user->ID );
				} else if( !empty( $payment_type )  && $payment_type == 'milestone') {
					workreap_update_hiring_milestone_data( $order_id,$user->ID );
				} else if( !empty( $payment_type ) && $payment_type == 'subscription' ) {
					workreap_update_pakage_data( $product_id ,$user->ID,$order_id );
				} else if( !empty( $payment_type ) && $payment_type == 'posting_job' ) {
					$order_detail 	= wc_get_order_item_meta( $key, 'cus_woo_product_data', true );
					$project_id 	= intval($order_detail['project_id']);
					$type 			= get_post_meta($project_id, 'type', true);
					$freelancers  	= get_post_meta($project_id, 'suggested_freelancers', false);

					if (function_exists('fw_get_db_settings_option')) {
						$job_public_status 				= fw_get_db_settings_option('job_status');
						$job_private_status = 'private';
					}
					$job_status = $type == 'one-to-one' ? $job_private_status : $job_public_status;
					$job_status	= !empty( $job_status ) ? $job_status : 'publish';

					if(!empty($project_id)) {
						$project_post_data 	= array(
							'ID'            => $project_id,
							'post_status'   => $job_status,
						);
						wp_update_post( $project_post_data );
						update_post_meta( $project_id, '_order_id', $order_id );

						// apply bundle features
						$bundle_id 		= get_post_meta($project_id, '_bundle_id', true);
						$featured 		= fw_get_db_post_option($bundle_id, 'featured');
						$highlighted 	= fw_get_db_post_option($bundle_id, 'highlighted');
						if( $featured === 'enabled' ) {
							update_post_meta( $project_id, '_featured_job_string', 1 );
						}
						if( $highlighted === 'enabled' ) {
							update_post_meta( $project_id, '_highlighted_job_string', 1 );
						}

						// Send email to users that the job is posted
						if (class_exists('Workreap_Email_helper')) {
							if (class_exists('WorkreapJobPost')) {
								$email_helper = new WorkreapJobPost();
								$emailData 	  = array();

								$employer_name 		= workreap_get_username($current_user->ID);
								$employer_email 	= get_userdata( $current_user->ID )->user_email;
								$employer_profile 	= get_permalink( workreap_get_linked_profile_id($current_user->ID) );
								$job_title 			= esc_html( get_the_title($project_id) );
								$job_link 			= get_permalink($project_id);

								$emailData['employer_name'] 	= esc_html( $employer_name );
								$emailData['employer_email'] 	= sanitize_email( $employer_email );
								$emailData['employer_link'] 	= esc_url( $employer_profile );
								$emailData['status'] 			= esc_html( $job_status );
								$emailData['job_link'] 			= esc_url( $job_link );
								$emailData['job_title'] 		= esc_html( $job_title );

								$email_helper->send_admin_job_post($emailData);
								$email_helper->send_employer_job_post($emailData);
							}
						}

						// send messages to freelancers in case of one to one job
						if($type == 'one-to-one' && !empty($freelancers)) {
							foreach ($freelancers as $freelancer) {
								$message = apply_filters( 'workreap_job_invitation_message', $project_id, $freelancer );
								$insert_data = array(
									'sender_id' 		=> $current_user->ID,
									'receiver_id' 		=> $freelancer,
									'chat_message' 		=> $message,
									'status' 			=> 1,
									'timestamp' 		=> time(),
									'time_gmt' 			=> get_gmt_from_date(current_time('mysql')),
								);
								ChatSystem::instance()->getUsersThreadListData($current_user->ID, $freelancer, 'insert_msg', $insert_data);

								if (class_exists('Workreap_Email_helper')) {
									if (class_exists('WorkreapSendOffer')) {
										$email_helper = new WorkreapSendOffer();
										$emailData 	  = array();
										
										$employer_id	= workreap_get_linked_profile_id($current_user->ID);
										$freelancer_id	= workreap_get_linked_profile_id($freelancer);
										//update invitation
										$invitation_count 	= get_user_meta(intval($freelancer_id), '_invitation_count', true);
										$invitation_count	= !empty($invitation_count) ? $invitation_count + 1 : 1;
										update_post_meta( $freelancer_id, '_invitation_count', $invitation_count);

										$emailData['freelancer_link'] 		= get_the_permalink( $freelancer_id );
										$emailData['freelancer_name'] 		= get_the_title($freelancer_id);
										$emailData['employer_link']       	= get_the_permalink( $employer_id );
										$emailData['employer_name'] 		= get_the_title($employer_id);
										$emailData['project_link']        	= !empty( $project_id ) ?  get_the_permalink( $project_id ) : '';
										$emailData['project_title']      	= !empty( $project_id ) ?  get_the_title( $project_id ) : '';
										$emailData['project_id']      		= $project_id;
										$emailData['employer_id']      		= $employer_id;
										$emailData['freelancer_id']      	= $freelancer_id;
										$emailData['message']      			= $message;
										$emailData['email_to']      		= get_userdata( $freelancer )->user_email;

										$email_helper->send_offer($emailData);
									}
								}
							}
						}
					}
				}
            }
        }
    }
}

/**
 * Update User Hiring Milestone payment
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_update_hiring_milestone_data')) {
    function workreap_update_hiring_milestone_data( $order_id ) {
        global $product,$woocommerce,$wpdb,$current_user;
		$current_date 	= current_time('mysql');
		$gmt_time		= current_time( 'mysql', 1 );
		
		$order 		= new WC_Order( $order_id );
		$items 		= $order->get_items();
		$earning	= array();
		
		if( !empty( $items ) ) {
			$counter	= 0;
			
			foreach( $items as $key => $order_item ){
				$counter++;
				$order_detail 					= wc_get_order_item_meta( $key, 'cus_woo_product_data', true );
				$earning['freelancer_amount']	= wc_get_order_item_meta( $key, 'freelancer_shares', true );
				$earning['admin_amount'] 		= wc_get_order_item_meta( $key, 'admin_shares', true );
				
				$earning['user_id']			= get_post_meta($order_detail['milestone_id'], '_freelancer_id', true);
				$earning['amount']			= !empty( $order_detail['price'] ) ? $order_detail['price'] : '';
				$earning['project_id']		= !empty( $order_detail['project_id'] ) ? $order_detail['project_id'] : '';
				$earning['milestone_id']	= !empty( $order_detail['milestone_id'] ) ? $order_detail['milestone_id'] : '';
			}
			
			$earning['order_id']		= $order_id;
			$earning['process_date'] 	= date('Y-m-d H:i:s', strtotime($current_date));
			$earning['date_gmt'] 		= date('Y-m-d H:i:s', strtotime($gmt_time));
			$earning['year'] 			= date('Y', strtotime($current_date));
			$earning['month'] 			= date('m', strtotime($current_date));
			$earning['timestamp'] 		= strtotime($current_date);
			$earning['status'] 			= 'hired';
			$earning['project_type'] 	= 'milestone';
			
			if( function_exists('workreap_get_current_currency') ) {
				$currency					= workreap_get_current_currency();
				$earning['currency_symbol']	= $currency['symbol'];
			} else {
				$earning['currency_symbol']	= '$';
			}
			
			if( !empty($earning['milestone_id']) && !empty($order_detail['project_id']) ) {
				workreap_hired_milestone_after_payment( $earning['milestone_id'] );
				$table_name = $wpdb->prefix . "wt_earnings";
				if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
					$tablename = $wpdb->prefix.'wt_earnings';
					$wpdb->insert( $tablename, $earning);
				}
			
				$milestone_title    = !empty( $earning['milestone_id'] ) ? get_the_title($earning['milestone_id']) :'';
				$project_title		= !empty( $earning['project_id'] ) ? get_the_title($earning['project_id']) : '';
				$project_link		= !empty( $earning['project_id'] ) ? get_the_permalink($earning['project_id']) : '';

				$hired_freelancer_title 	= workreap_get_username( $earning['user_id'] );

				$user_email 	= !empty( $earning['user_id'] ) ? get_userdata( $earning['user_id'] )->user_email : '';

				update_post_meta( $order_id, '_hiring_id', $earning['milestone_id'] );
				update_post_meta( $earning['milestone_id'], '_order_id', $order_id );
				
				//Send email to freelancer
				if (class_exists('Workreap_Email_helper')) {
					if (class_exists('WorkreapMilestoneRequest')) {
						$email_helper = new WorkreapMilestoneRequest();
						$emailData = array();
						
						$emailData['freelancer_name'] 	= esc_html( $hired_freelancer_title);
						$emailData['project_title'] 	= esc_html( $project_title);
						$emailData['project_link'] 		= esc_html( $project_link);
						$emailData['milestone_title'] 	= esc_html( $milestone_title);

						$emailData['email_to'] 			= esc_html( $user_email);

						$email_helper->send_hired_against_milestone_to_freelancer_email($emailData);
					}
				}	

			}
		}
    }
}

/**
 * Update User Hiring payment
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_update_hiring_data')) {
    function workreap_update_hiring_data( $order_id ) {
        global $product,$woocommerce,$wpdb,$current_user;
		$current_date 	= current_time('mysql');
		$gmt_time		= current_time( 'mysql', 1 );
		
		$order 		= new WC_Order( $order_id );
		$items 		= $order->get_items();
		$earning	= array();
		
		if( !empty( $items ) ) {
			$counter	= 0;
			foreach( $items as $key => $order_item ){
				$counter++;
				$order_detail 					= wc_get_order_item_meta( $key, 'cus_woo_product_data', true );
				$earning['freelancer_amount']	= wc_get_order_item_meta( $key, 'freelancer_shares', true );
				$earning['admin_amount'] 		= wc_get_order_item_meta( $key, 'admin_shares', true );
				
				$earning['user_id']		= get_post_field('post_author',$order_detail['proposal_id']);
				$earning['amount']		= !empty( $order_detail['price'] ) ? $order_detail['price'] : '';
				$earning['project_id']	= !empty( $order_detail['project_id'] ) ? $order_detail['project_id'] : '';
			}
			
			$earning['order_id']		= $order_id;
			$earning['process_date'] 	= date('Y-m-d H:i:s', strtotime($current_date));
			$earning['date_gmt'] 		= date('Y-m-d H:i:s', strtotime($gmt_time));
			$earning['year'] 			= date('Y', strtotime($current_date));
			$earning['month'] 			= date('m', strtotime($current_date));
			$earning['timestamp'] 		= strtotime($current_date);
			$earning['status'] 			= 'hired';
			
			if( function_exists('workreap_get_current_currency') ) {
				$currency					= workreap_get_current_currency();
				$earning['currency_symbol']	= $currency['symbol'];
			} else {
				$earning['currency_symbol']	= '$';
			}
			
			if( !empty($earning['project_id']) && !empty($order_detail['proposal_id']) ) {
				workreap_hired_freelancer_after_payment( $earning['project_id'],$order_detail['proposal_id']);
				$table_name = $wpdb->prefix . "wt_earnings";
				if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
					$tablename = $wpdb->prefix.'wt_earnings';
					$wpdb->insert( $tablename,$earning);
				}
				
				$project_id				= !empty( $earning['project_id'] ) ? $earning['project_id'] : '';
				$employer_id_user		= get_post_field ('post_author', $project_id);
				$freelancer_user_id		= get_post_field ('post_author', $order_detail['proposal_id']);
				
				$project_title			= esc_html( get_the_title( $project_id ) );
				$project_link			= esc_url( get_the_permalink( $project_id ));
				$message				= esc_html__('You are hiring for','workreap').' '.$project_title.' '.$project_link;
				 $insert_data = array(
					'sender_id' 		=> $employer_id_user,
					'receiver_id' 		=> $freelancer_user_id,
					'chat_message' 		=> $message,
					'status' 			=> 'unread',
					'timestamp' 		=> time(),
					'time_gmt' 			=> $gmt_time,
				);
				
				//plugin core active
				if (class_exists('ChatSystem')) {
					$msg_id = ChatSystem::getUsersThreadListData($employer_id_user, $freelancer_user_id, 'insert_msg', $insert_data, '');
				}
				
				update_post_meta( $order_id, '_hiring_id', $order_detail['proposal_id'] );
				update_post_meta( $project_id, '_order_id', $order_id );
				update_post_meta( $order_detail['proposal_id'], '_order_id', $order_id );
				
				//Send email to users
				if (class_exists('Workreap_Email_helper')) {
					if (class_exists('WorkreapHireFreelancer')) {
						$email_helper 	= new WorkreapHireFreelancer();
						$emailData 	  	= array();
						$employer_id	= workreap_get_linked_profile_id($employer_id_user);
						$freelancer_id	= get_post_meta($order_detail['proposal_id'],'_send_by',true);
						$profile_id		= workreap_get_linked_profile_id($freelancer_id,'post');
						$user_email 	= !empty( get_userdata( $profile_id )->user_email ) ? get_userdata( $profile_id )->user_email : '';

						$emailData['freelancer_link'] 		= esc_url( get_the_permalink( $freelancer_id ));
						$emailData['freelancer_name'] 		= esc_html( get_the_title($freelancer_id));
						$emailData['employer_link']       	= esc_url( get_the_permalink( $employer_id ) );
						$emailData['employer_name'] 		= esc_html( get_the_title($employer_id));
						$emailData['project_link']        	= $project_link;
						$emailData['project_title']      	= $project_title;
						$emailData['email_to']      		= $user_email;
						$emailData['project_id']      		= $project_id;
						$emailData['employer_id']      		= $employer_id;
						$emailData['freelancer_id']      	= $freelancer_id;
						$email_helper->send_hire_freelancer_email($emailData);
						
						$email_helper->send_rejected_freelancers_email($emailData);
					}
				}
			}
		}
    }
}
/**
 * Update User Hiring payment
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_update_hiring_service_data')) {
    function workreap_update_hiring_service_data( $order_id,$user_id ) {
        global $product,$woocommerce,$wpdb,$current_user;
		$current_date 	= current_time('mysql');
		$gmt_time		= current_time( 'mysql', 1 );

		$order 		= new WC_Order( $order_id );
		$items 		= $order->get_items();
		$earning	= array();
		
		if( !empty( $items ) ) {
			$counter	= 0;
			foreach( $items as $key => $order_item ){
				$counter++;
				$order_detail 					= wc_get_order_item_meta( $key, 'cus_woo_product_data', true );
				$earning['freelancer_amount']	= wc_get_order_item_meta( $key, 'freelancer_shares', true );
				$earning['admin_amount'] 		= wc_get_order_item_meta( $key, 'admin_shares', true );
				$earning['amount']				= $order_detail['price'];
				
			}
			
			$earning['order_id']		= $order_id;
			$earning['project_type']	= 'service';
			$earning['process_date'] 	= date('Y-m-d H:i:s', strtotime($current_date));
			$earning['date_gmt'] 		= date('Y-m-d H:i:s', strtotime($gmt_time));
			$earning['year'] 			= date('Y', strtotime($current_date));
			$earning['month'] 			= date('m', strtotime($current_date));
			$earning['timestamp'] 		= strtotime($current_date);
			$earning['status'] 			= 'hired';
			
			if( function_exists('workreap_get_current_currency') ) {
				$currency					= workreap_get_current_currency();
				$earning['currency_symbol']	= $currency['symbol'];
			} else {
				$earning['currency_symbol']	= '$';
			}
			
			if( !empty($order_detail['service_id']) ) {
				$addons				= !empty( $order_detail['addons'] ) ? $order_detail['addons'] : array();
				$receiver_id		= get_post_field('post_author',$order_detail['service_id'] );
				$service_title		= get_the_title( $order_detail['service_id'] );
				$service_link		= get_the_permalink( $order_detail['service_id'] );
				
				$order_post = array(
					'post_title'    => wp_strip_all_tags( $service_title ).' #'.$order_id,
					'post_status'   => 'hired',
					'post_author'   => $user_id,
					'post_type'     => 'services-orders',
				);

				$order_post    = wp_insert_post( $order_post );
				
				if( !empty( $order_post ) ) {
					update_post_meta($order_post,'_service_id',$order_detail['service_id']);
					update_post_meta($order_post,'_service_title',esc_attr( $service_title ));
					update_post_meta($order_post,'_service_author',$receiver_id);
					update_post_meta($order_post,'_order_id',$order_id);
					update_post_meta($order_post,'_addons',$addons);
					update_post_meta( $order_id, '_hiring_id', $order_post );
				}
				
				$earning['user_id']		= $receiver_id;
				$earning['project_id']	= $order_post;
				
				$table_name = $wpdb->prefix . "wt_earnings";
				if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
					$tablename = $wpdb->prefix.'wt_earnings';
					$wpdb->insert( $tablename,$earning);
				}
				
				$message				= esc_html__('Congratulations! You are hired for','workreap').' '.$service_title.' '.$service_link;
				
				$insert_data = array(
					'sender_id' 		=> $user_id,
					'receiver_id' 		=> $receiver_id,
					'chat_message' 		=> $message,
					'status' 			=> 'unread',
					'timestamp' 		=> time(),
					'time_gmt' 			=> $gmt_time,
				);
				
				if (class_exists('ChatSystem')) {
					$msg_id 	= ChatSystem::getUsersThreadListData($receiver_id, $freelancer_user_id, 'insert_msg', $insert_data, '');
				}
				
				$service_id	=  $order_detail['service_id'];
				
				//Send email to users
				if (class_exists('Workreap_Email_helper')) {
					if (class_exists('WorkreapHireFreelancer')) {
						$email_helper = new WorkreapHireFreelancer();
						$emailData 	  = array();
						$freelancer_id	= workreap_get_linked_profile_id( $receiver_id );
						$employer_id	= workreap_get_linked_profile_id( $user_id );
						$user_email 	= !empty( $receiver_id ) ? get_userdata( $receiver_id )->user_email : '';
						
						$emailData['freelancer_link'] 		= get_the_permalink( $freelancer_id );
						$emailData['freelancer_name'] 		= get_the_title($freelancer_id);
						$emailData['employer_link']       	= get_the_permalink( $employer_id );
						$emailData['employer_name'] 		= get_the_title($employer_id);
						$emailData['service_link']        	= $service_link;
						$emailData['service_title']      	= $service_title;
						$emailData['email_to']      		= $user_email;

						$email_helper->send_hire_freelancer_email_service($emailData);
					}
				}
			}
		}
    }
}

/**
 * Update User Pakage
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_update_pakage_data')) {
    function workreap_update_pakage_data( $product_id, $user_id,$order_id ) {
        $user_type					= workreap_get_user_type( $user_id );
		$package_data				= array();
		$pakeges_features			= workreap_get_pakages_features();
		$profile_id					= workreap_get_linked_profile_id($user_id);

		$current_date = current_time('mysql');
		$wt_featured	= '';
		$wt_isbadge	    = 'no';
		
		if ( !empty ( $pakeges_features )) {
			foreach( $pakeges_features as $key => $vals ) {
				if( !empty( $vals['user_type'] ) &&  ( $vals['user_type'] === $user_type || $vals['user_type'] === 'common' ) ) {
					$item				= get_post_meta($product_id,$key,true);
					
					if( $key === 'wt_duration_type' ) {
						$wt_featured 		= workreap_get_duration_types($item,'value');
						$feature			= $item;
					} elseif( $key === 'wt_badget' ) {
						$feature 		= !empty( $item ) ? $item : 0;
						if( !empty($feature) ){
							$wt_isbadge	    = 'yes';
						}
						
					} else {
						$feature 	= $item;
					}
					
					$package_data[$key]	= $feature;
				}
			}
		}
		
		
		$duration 		= $wt_featured; //no of days for a featured listings
		$featured_date  = date('Y-m-d H:i:s');
		
		if ( !empty( $duration ) && $duration > 0 ) {
			$featured_date = strtotime("+" . $duration . " days", strtotime($current_date));
			$featured_date = date('Y-m-d H:i:s', $featured_date);
		}
		
		$featured_string	= !empty( $featured_date ) ?  strtotime( $featured_date ) : 0;
		
		$package_data['subscription_id'] 				= $product_id;
		$package_data['subscription_featured_expiry'] 	= $featured_date;
		$package_data['subscription_featured_string'] 	= $featured_string;
		
		if ( !empty( $duration ) && $duration > 0 && $wt_isbadge === 'yes' ) {
			update_post_meta($profile_id, '_featured_timestamp', 1);
			update_post_meta($profile_id, '_expiry_string', $featured_string);
		} else{
			update_post_meta($profile_id, '_featured_timestamp', 0);
		}
		
		update_user_meta( $user_id, 'wt_subscription', $package_data);
		
		if( !empty( $order_id ) ) {
			//Send email to users
			if (class_exists('Workreap_Email_helper')) {
				if (class_exists('WorkreapSubscribePackage')) {
					$email_helper = new WorkreapSubscribePackage();
					$emailData 	= array();
					$user_type		= apply_filters('workreap_get_user_type', $user_id );

					$order 			= wc_get_order($order_id);

					$product 		= wc_get_product($product_id);
					$invoice_id 	= esc_html__('Order #','workreap').$order_id;
					$package_name   = $product->get_title();
					$amount 		= $product->get_price();
					$status 		= $order->get_status();
					$method 		= $order->payment_method;
					$name 			= $order->billing_first_name . '&nbsp;' . $order->billing_last_name;
					$user_email 	= get_userdata( $user_id )->user_email;

					$amount 		= wc_price( $amount );

					if( empty( $name ) ){
						$name 		= workreap_get_username($user_id);
					}

					$emailData['invoice'] 		= esc_html( $invoice_id );
					$emailData['package_name'] 	= esc_html( $package_name );
					$emailData['amount'] 		= wp_strip_all_tags( $amount );
					$emailData['status']        = esc_html( $status );
					$emailData['method']        = esc_html( $method );
					$emailData['date']      	= date( get_option('date_format'),strtotime( $current_date ) );
					$emailData['expiry'] 		= date( get_option('date_format'),strtotime( $featured_date ) );
					$emailData['name'] 			= esc_html( $name );
					$emailData['email_to'] 		= sanitize_email( $user_email );
					$emailData['link'] 			= esc_url( get_the_permalink( $profile_id ) );

					if ( $user_type === 'employer' ) {
						$email_helper->send_subscription_email_to_employer($emailData);
					} else if ( $user_type === 'freelancer' ) {
						$email_helper->send_subscription_email_to_freelancer($emailData);
					}
				}
			}
		}
    }
}

/**
 * Remove payment gateway
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_unused_payment_gateways')) {
    function workreap_unused_payment_gateways($load_gateways) {

        $remove_gateways = array(
            'WC_Gateway_BACS',
            'WC_Gateway_Cheque',
            'WC_Gateway_COD',
        );
		
        foreach ($load_gateways as $key => $value) {
            if (in_array($value, $remove_gateways)) {
                unset($load_gateways[$key]);
            }
        }
		
        return $load_gateways;
    }

}

/**
 * Get packages features
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_get_package_features')) {

    function workreap_get_package_features($key='') {
		$features	= workreap_get_pakages_features();
		if( !empty( $features[$key] ) ){
			return $features[$key]['title'];
		} else{
			return '';
		}
    }
}

/**
 * Get Hiring freelancer title
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_get_hiring_payment_title')) {

    function workreap_get_hiring_payment_title($key) {
		$hirings	= workreap_get_hiring_payment();
		
		if( !empty( $hirings[$key] ) ){
			return $hirings[$key]['title'];
		} else{
			return '';
		}
	}
}

/**
 * Get Hiring freelancer array
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_get_hiring_payment')) {

    function workreap_get_hiring_payment() {
		$hiring	= array(
				'project_id' 	=> array('title' => esc_html__('Project title','workreap')),
				'price'  		=> array('title' => esc_html__('Amount','workreap')),
				'proposal_id'   => array('title' => esc_html__('Freelancer','workreap')),
			);
		
		return $hiring;
	}
}

/**
 * Get Hiring milestone freelancer array
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_get_hiring_milestone_payment')) {

    function workreap_get_hiring_milestone_payment($key) {
		$hiring	= array(
				'project_id' 		=> esc_html__('Project title','workreap'),
				'price'  			=> esc_html__('Amount','workreap'),
				'milestone_id'   	=> esc_html__('Milestone','workreap'),
			);
		
		return $hiring[$key];
	}
}
/**
 * Get Hiring milestone meta
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_get_hiring_milestone_value')) {

    function workreap_get_hiring_milestone_value($val='',$key='') {
		
		if( !empty($key) && ($key === 'project_id' || $key === 'milestone_id') ) {
			$val 			= esc_html( get_the_title( $val ) );
		}  else if( !empty($key) && $key === 'price' ) {
			$price_symbol	= workreap_get_current_currency();
			$val			= $price_symbol['symbol'].floatval($val);
		}
		
		return $val;
	}
}

/**
 * Get Hiring meta
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_get_hiring_value')) {

    function workreap_get_hiring_value($val='',$key='') {
		
		if( !empty($key) && $key === 'project_id' ) {
			$val 			= esc_html( get_the_title( $val ) );
		} else if( !empty($key) && $key === 'proposal_id' ) {
			$freelancer_id	= get_post_field('post_author',$val);
			$profile_id		= workreap_get_linked_profile_id( $freelancer_id );
			
			$title			= esc_html( get_the_title( intval($profile_id) ) );
			$permalink		= esc_url( get_the_permalink( $profile_id ));
			$val			= '<a href="'.esc_url($permalink).'" title="'.esc_attr($title).'" >'.esc_html($title).'</a>';
		} else if( !empty($key) && $key === 'price' ) {
			$price_symbol	= workreap_get_current_currency();
			$val			= $price_symbol['symbol'].floatval($val);
		}
		
		return $val;
	}
}

/**
 * Get package Feature values
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_get_package_feature_value')) {

    function workreap_get_package_feature_value($val='',$key='') {
		if( !empty($key) && $key == 'wt_badget' ) {
			if(!empty($val) ){
				$badges		= get_term( intval($val) );
				if(!empty($badges->name)) {
					$return	= $badges->name;
				} else {
					$return	= '<i class="fa fa-times-circle sp-pk-not-allowed"></i>';
				}
			} else {
				$return	= '<i class="fa fa-times-circle sp-pk-not-allowed"></i>';
			}
		}elseif( isset( $val ) && $val === 'yes' ){
			$return	= '<i class="fa fa-check-circle sp-pk-allowed"></i>';
		} elseif( isset( $val ) && $val === 'no' ){
			$return	= '<i class="fa fa-times-circle sp-pk-not-allowed"></i>';
		} else{
			$return	= $val;
		}
		
		return $return;
	}
}

/**
 * Get Service attributes
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_get_service_attribute')) {

    function workreap_get_service_attribute($key='',$val) {
		$services		= array();
		$delviery		= array();
		if( function_exists( 'worktic_service_cart_attributes' ) ) {
			$services	= worktic_service_cart_attributes();
		}
		
		$return	= array();
		if( !empty( $services[$key] ) ) {
			if( $key === 'service_id' ) {
				$return['title']	= $services[$key];
				$return['value']	= get_the_title($val);
			} else if( $key === 'delivery_time' ) {
				$return['title']	= $services[$key];
				$return['value']	= workreap_get_term_name($val,'delivery');
			} else {
				$return['title']	= $services[$key];
				$return['value']	= $val;
			}
			
		} else if( $key === 'addons') {
			if( !empty( $val ) ) {
				$title	= '';
				foreach( $val as $addon_id ){
					$price	= get_post_meta($addon_id,'_price',true);
					$title	.= '<p>'.get_the_title($addon_id).' ('.workreap_price_format( $price ,'return').') </p>';
				}
				$return['title']	= esc_html__('Addons','workreap');
				$return['value']	= $title;
			}
		} else if( $key === 'service_price') {
			if( !empty( $val ) ) {
				
				$return['title']	= esc_html__('Service Price','workreap');
				$return['value']	= workreap_price_format( $val ,'return');
			}
		} 
		return $return;
	}
}

/**
 * Add data in checkout
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_add_new_fields_checkout')) {
	add_filter( 'woocommerce_checkout_after_customer_details', 'workreap_add_new_fields_checkout', 10, 1 );
	function workreap_add_new_fields_checkout() {
		global $product,$woocommerce;
		$cart_data = WC()->session->get( 'cart', null );
		if( !empty( $cart_data ) ) {
			foreach( $cart_data as $key => $cart_items ){
				if( !empty( $cart_items['payment_type'] )  && $cart_items['payment_type'] == 'hiring' ) {
					$title		= esc_html( get_the_title($cart_items['cart_data']['project_id']) );
					$quantity	= !empty( $cart_items['quantity'] ) ?  $cart_items['quantity'] : 1;

					if( !empty( $cart_items['cart_data'] ) ){?>
					<div class="wt-haslayout">
						<div class="cart-data-wrap">
						  <h3><?php echo esc_html($title);?>( <span class="cus-quantity">×<?php echo esc_html( $quantity );?></span> )</h3>
						  <div class="selection-wrap">
							<?php 
								$counter	= 0;
								foreach( $cart_items['cart_data'] as $key => $value ){
									$counter++;
								?>
								<div class="cart-style"> 
									<span class="style-lable"><?php echo workreap_get_hiring_payment_title( $key );?></span> 
									<span class="style-name"><?php echo workreap_get_hiring_value( $value,$key );?></span> 
								</div>
							<?php }?>
						  </div>
						</div>
					 </div>	
					<?php
					}
				} elseif( !empty( $cart_items['payment_type'] )  && $cart_items['payment_type'] == 'hiring_service' ) {
					$title		= esc_attr( get_the_title($cart_items['cart_data']['service_id']) );
					$quantity	= !empty( $cart_items['quantity'] ) ?  $cart_items['quantity'] : 1;
					
					
					if( !empty( $cart_items['cart_data'] ) ){?>
					<div class="wt-haslayout">
						<div class="cart-data-wrap">
						  <h3><?php echo esc_attr($title);?>( <span class="cus-quantity">×<?php echo esc_attr( $quantity );?></span> )</h3>
						  <div class="selection-wrap">
							<?php 
								$counter	= 0;
								foreach( $cart_items['cart_data'] as $key => $value ){
									$counter++;
									
									$attributes	= workreap_get_service_attribute($key,$value);
									if( !empty( $attributes ) ){
								?>
									<div class="cart-style"> 
										<span class="style-lable"><?php echo esc_attr($attributes['title']);;?></span> 
										<span class="style-name"><?php echo do_shortcode($attributes['value']);?></span> 
									</div>
								<?php }?>
							<?php }?>
						  </div>
						</div>
					 </div>	
					<?php
					}
				} elseif( !empty( $cart_items['payment_type'] )  && $cart_items['payment_type'] == 'milestone' ) {
					$title		= esc_attr( get_the_title($cart_items['cart_data']['milestone_id']) );
					$quantity	= !empty( $cart_items['quantity'] ) ?  $cart_items['quantity'] : 1;
					
					
					if( !empty( $cart_items['cart_data'] ) ){?>
						<div class="wt-haslayout">
							<div class="cart-data-wrap">
							<h3><?php echo esc_html($title);?>( <span class="cus-quantity">×<?php echo esc_html( $quantity );?></span> )</h3>
							<div class="selection-wrap">
								<?php 
									$counter	= 0;
									foreach( $cart_items['cart_data'] as $key => $value ){
										$counter++;
									?>
									<div class="cart-style"> 
										<span class="style-lable"><?php echo workreap_get_hiring_milestone_payment( $key );?></span> 
										<span class="style-name"><?php echo workreap_get_hiring_milestone_value( $value,$key );?></span> 
									</div>
								<?php }?>
							</div>
							</div>
						</div>
					 <?php
					}
				} elseif( !empty( $cart_items['payment_type'] ) && $cart_items['payment_type'] === 'subscription') {
					$title		= esc_html(get_the_title($cart_items['product_id']));
					$quantity	= !empty( $cart_items['quantity'] ) ?  $cart_items['quantity'] : 1;

					if( !empty( $cart_items['cart_data'] ) ){
					?>
					<div class="wt-haslayout">
						<div class="cart-data-wrap">
						  <h3><?php echo esc_html($title);?>( <span class="cus-quantity">×<?php echo esc_html( $quantity );?></span> )</h3>
						  <div class="selection-wrap">
							<?php 
								$counter	= 0;
								foreach( $cart_items['cart_data'] as $key => $value ){
									$counter++;
								?>
								<div class="cart-style"> 
									<span class="style-lable"><?php echo workreap_get_package_features( $key );?></span> 
									<span class="style-name" data-v="<?php echo esc_attr( $value );?>"  data-k="<?php echo esc_attr( $key );?>"><?php echo workreap_get_package_feature_value( $value,$key );?></span> 
								</div>
							<?php }?>
						  </div>
						</div>
					 </div>	
					<?php
					}
				}
				
			}
		}
	}
}

/**
 * Add meta on order item
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_woo_convert_item_session_to_order_meta')) {
	add_action( 'woocommerce_add_order_item_meta', 'workreap_woo_convert_item_session_to_order_meta', 10, 3 ); //Save cart data
	function workreap_woo_convert_item_session_to_order_meta( $item_id, $values, $cart_item_key ) {
		$cart_key				= 'cart_data';
		$cart_type_key			= 'payment_type';
		
		$admin_shares			= 'admin_shares';
		$freelancer_shares		= 'freelancer_shares';
		
		$cart_item_data = workreap_woo_get_item_data( $cart_item_key,$cart_key );
		$cart_item_type = workreap_woo_get_item_data( $cart_item_key,$cart_type_key );
		
		$admin_shares 		= workreap_woo_get_item_data( $cart_item_key,$admin_shares );
		$freelancer_shares  = workreap_woo_get_item_data( $cart_item_key,$freelancer_shares );
		
		
		if ( !empty( $cart_item_data ) ) {
			wc_add_order_item_meta( $item_id, 'cus_woo_product_data', $cart_item_data );
		}
		
		if ( !empty( $cart_item_type ) ) {
			wc_add_order_item_meta( $item_id, 'payment_type', $cart_item_type );
		}
		
		if( !empty( $admin_shares ) ){
			wc_add_order_item_meta( $item_id, 'admin_shares', $admin_shares );
		}
		
		if( !empty( $freelancer_shares ) ){
			wc_add_order_item_meta( $item_id, 'freelancer_shares', $freelancer_shares );
		}

	}
}

/**
 * Get woocommerce session data
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_woo_get_item_data')) {
	function workreap_woo_get_item_data( $cart_item_key, $key = null, $default = null ) {
		global $woocommerce;

		$data = (array)WC()->session->get( 'cart',$cart_item_key );
		if ( empty( $data[$cart_item_key] ) ) {
			$data[$cart_item_key] = array();
		}

		// If no key specified, return an array of all results.
		if ( $key == null ) {
			return $data[$cart_item_key] ? $data[$cart_item_key] : $default;
		}else{
			return empty( $data[$cart_item_key][$key] ) ? $default : $data[$cart_item_key][$key];
		}
	}
}

/**
 * Display order detail
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_display_order_data')) {
	add_action( 'woocommerce_thankyou', 'workreap_display_order_data', 20 ); 
	add_action( 'woocommerce_view_order', 'workreap_display_order_data', 20 );
	function workreap_display_order_data( $order_id ) {
		global $product,$woocommerce,$wpdb,$current_user;
		
		$order = new WC_Order( $order_id );
		$items = $order->get_items();
		if( !empty( $items ) ) {
			$counter	= 0;
			foreach( $items as $key => $order_item ){
				$counter++;
				$payment_type 	= wc_get_order_item_meta( $key, 'payment_type', true );
				$order_detail 	= wc_get_order_item_meta( $key, 'cus_woo_product_data', true );
				$item_id    	= $order_item['product_id'];
				
				if( !empty($payment_type)  && $payment_type === 'hiring' ) {
					$order_item['name'] 	= workreap_get_hiring_value($order_detail['project_id'],'project_id');
				}
				
				$name		= !empty( $order_item['name'] ) ?  $order_item['name'] : '';
				$quantity	= !empty( $order_item['qty'] ) ?  $order_item['qty'] : 5;
				if( !empty( $order_detail ) ) {?>
					<div class="row">
						<div class="col-md-12">
							<div class="cart-data-wrap">
							  <h3><?php echo esc_html($name);?>( <span class="cus-quantity">×<?php echo esc_html( $quantity );?></span> )</h3>
							  <div class="selection-wrap">
								<?php 
									$counter	= 0;
									foreach( $order_detail as $key => $value ){
										$counter++;
										if(!empty($payment_type)  && $payment_type ==='milestone' ) { ?>
											<div class="cart-style"> 
												<span class="style-lable"><?php echo workreap_get_hiring_milestone_payment( $key );?></span> 
												<span class="style-name"><?php echo workreap_get_hiring_milestone_value( $value,$key );?></span> 
											</div>
										<?php }else if( !empty($payment_type)  && $payment_type ==='hiring' ) {?>
											<div class="cart-style"> 
												<span class="style-lable"><?php echo workreap_get_hiring_payment_title( $key );?></span> 
												<span class="style-name"><?php echo workreap_get_hiring_value( $value,$key );?></span> 
											</div>
										<?php }else if( !empty($payment_type)  && $payment_type ==='hiring_service' ) {
											$attributes	= workreap_get_service_attribute($key,$value);
											if( !empty( $attributes ) ){
												?>
											<div class="cart-style"> 
												<span class="style-lable"><?php echo esc_attr($attributes['title']);?></span> 
												<span class="style-name"><?php echo do_shortcode($attributes['value']);?></span> 
											</div>
											<?php }?>
										<?php } else if( !empty( $payment_type ) && $payment_type === 'subscription' ) { ?>
											<div class="cart-style"> 
												<span class="style-lable"><?php echo workreap_get_package_features($key);?></span> 
												<span class="style-name"><?php echo workreap_get_package_feature_value( $value,$key );?></span> 
											</div>
										<?php } ?>
									<?php }?>
							  </div>
							</div>
						 </div>
						 <?php if( !empty( $current_user->ID ) ){?>
							 <div class="col-md-12">
								<a class="wt-btn" href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('insights', $current_user->ID); ?>"><?php esc_html_e('Return to dashboard','workreap');?></a>
							 </div>
						 <?php }?>	
					</div>
				<?php
				}
			}
		}
	}
}

/**
 * Print order meta at back-end in order detail page
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_woo_order_meta')) {
	add_filter( 'woocommerce_after_order_itemmeta', 'workreap_woo_order_meta', 10, 3 );
	function workreap_woo_order_meta( $item_id, $item, $_product ) {
		global $product,$woocommerce,$wpdb;
		$order_detail = wc_get_order_item_meta( $item_id, 'cus_woo_product_data', true );
		
		$order_item 		= new WC_Order_Item_Product($item_id);
		$order				= $order_item->get_order();
		$order_status		= $order->get_status();
  		$customer_user 		= get_post_meta( $order->get_id(), '_customer_user', true );
		$payment_type 		= wc_get_order_item_meta( $item_id, 'payment_type', true );

		if( !empty( $order_detail ) ) {?>
			<div class="order-edit-wrap">
				<div class="view-order-detail">
					<a href="javascript:;" data-target="#cus-order-modal-<?php echo esc_attr( $item_id );?>" class="cus-open-modal cus-btn cus-btn-sm"><?php esc_html_e('View order detail?','workreap');?></a>
				</div>
				<div class="cus-modal" id="cus-order-modal-<?php echo esc_attr( $item_id );?>">
					<div class="cus-modal-dialog">
						<div class="cus-modal-content">
							<div class="cus-modal-header">
								<a href="javascript:;" data-target="#cus-order-modal-<?php echo esc_attr( $item_id );?>" class="cus-close-modal">×</a>
								<h4 class="cus-modal-title"><?php esc_html_e('Order Detail','workreap');?></h4>
							</div>
							<div class="cus-modal-body">
								<div class="sp-order-status">
									<p><?php echo ucwords( $order_status );?></p>
								</div>
								<div class="cus-form cus-form-change-settings">
									<div class="edit-type-wrap">
										<?php 
										$counter	= 0;
										foreach( $order_detail as $key => $value ){
											$counter++;
											
											if( !empty($payment_type) && $payment_type === 'milestone') {?>
												<div class="cus-options-data">
													<label><span><?php echo workreap_get_hiring_milestone_payment($key);?></span></label>
													<div class="step-value">
														<span><?php echo workreap_get_hiring_milestone_value( $value, $key );?></span>
													</div>
												</div>
											<?php } else if( !empty($payment_type) && ($payment_type === 'hiring' || $payment_type === 'posting_job')) {?>
												<div class="cus-options-data">
													<label><span><?php echo workreap_get_hiring_payment_title($key);?></span></label>
													<div class="step-value">
														<span><?php echo workreap_get_hiring_value( $value, $key );?></span>
													</div>
												</div>
											<?php } elseif( !empty($payment_type) && $payment_type === 'hiring_service') {
													$attributes	= workreap_get_service_attribute($key,$value);
													if( !empty( $attributes ) ){
													?>
													<div class="cus-options-data">
														<label><span><?php echo esc_attr($attributes['title']);?></span></label>
														<div class="step-value">
															<span><?php echo do_shortcode($attributes['value']);?></span>
														</div>
													</div>
												<?php }?>
											<?php } else if( !empty($payment_type) && $payment_type === 'subscription' ) { ?>
												<div class="cus-options-data">
													<label><span><?php echo workreap_get_package_features($key);?></span></label>
													<div class="step-value">
														<span><?php echo workreap_get_package_feature_value( $value, $key );?></span>
													</div>
												</div>
											<?php }
											}
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php						
		}
	}
}

/**
 * Filter woocommerce display itme meta
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_filter_woocommerce_display_item_meta')) {
	function workreap_filter_woocommerce_display_item_meta( $html, $item, $args ) {
		// make filter magic happen here... 
		return ''; 
	}; 

	// add the filter 
	add_filter( 'woocommerce_display_item_meta', 'workreap_filter_woocommerce_display_item_meta', 10, 3 ); 
}

/**
 * Woocommerce account menu
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_my_account_menu_items')) {
	add_filter( 'woocommerce_account_menu_items', 'workreap_my_account_menu_items' );
	function workreap_my_account_menu_items( $items ) {
		unset($items['dashboard']);
		unset($items['downloads']);
		unset($items['edit-address']);
		unset($items['payment-methods']);
		unset($items['edit-account']);
		unset($items['orders']);
		unset($items['customer-logout']);
		return $items;
	}
}

/**
 * Hired product ID
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_get_hired_product_id')) {

    function workreap_get_hired_product_id() {
		$meta_query_args = array();
		$args = array(
			'post_type' 			=> 'product',
			'posts_per_page' 		=> -1,
			'order' 				=> 'DESC',
			'orderby' 				=> 'ID',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts' 	=> 1
		);


		$meta_query_args[] = array(
			'key' 			=> '_workreap_hiring',
			'value' 		=> 'yes',
			'compare' 		=> '=',
		);
		
		$query_relation 		= array('relation' => 'AND',);
		$meta_query_args 		= array_merge($query_relation, $meta_query_args);
		$args['meta_query'] 	= $meta_query_args;
		
		$hired_product = get_posts($args);
		
		if (!empty($hired_product)) {
            return (int) $hired_product[0]->ID;
        } else{
			 return 0;
		}
		
	}
}

/**
 * Posting job product ID
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_get_posting_job_product_id')) {

    function workreap_get_posting_job_product_id() {
		$meta_query_args = array();
		$args = array(
			'post_type' 			=> 'product',
			'posts_per_page' 		=> -1,
			'order' 				=> 'DESC',
			'orderby' 				=> 'ID',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts' 	=> 1
		);


		$meta_query_args[] = array(
			'key' 			=> '_workreap_posting_job',
			'value' 		=> 'yes',
			'compare' 		=> '=',
		);
		
		$query_relation 		= array('relation' => 'AND',);
		$meta_query_args 		= array_merge($query_relation, $meta_query_args);
		$args['meta_query'] 	= $meta_query_args;
		
		$hired_product = get_posts($args);
		
		if (!empty($hired_product)) {
            return (int) $hired_product[0]->ID;
        } else{
			 return 0;
		}
		
	}
}

/**
 * Price override
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_apply_custom_price_to_cart_item')) {
	
	add_action( 'woocommerce_before_calculate_totals', 'workreap_apply_custom_price_to_cart_item', 99 );
	function workreap_apply_custom_price_to_cart_item( $cart_object ) {  
		$hiring_settings	= worrketic_hiring_payment_setting();
			
		if( !empty($hiring_settings['is_enable']) && $hiring_settings['is_enable'] === 'yes'){
			if( !WC()->session->__isset( "reload_checkout" )) {
				foreach ( $cart_object->cart_contents as $key => $value ) {
					if( !empty( $value['payment_type'] ) && $value['payment_type'] == 'hiring' ){
						if( isset( $value['cart_data']['price'] ) ){
							$bk_price = floatval( $value['cart_data']['price'] );
							$value['data']->set_price($bk_price);
						}
					} else if( !empty( $value['payment_type'] ) && $value['payment_type'] == 'hiring_service' ){
						if( isset( $value['cart_data']['price'] ) ){
							$bk_price = floatval( $value['cart_data']['price'] );
							$value['data']->set_price($bk_price);
						}
					} else if( !empty( $value['payment_type'] ) && $value['payment_type'] == 'posting_job' ){
						if( isset( $value['cart_data']['price'] ) ){
							$bk_price = floatval( $value['cart_data']['price'] );
							$value['data']->set_price($bk_price);
						}
					} else if( !empty( $value['payment_type'] ) && $value['payment_type'] == 'milestone' ){
						if( isset( $value['cart_data']['price'] ) ){
							$bk_price = floatval( $value['cart_data']['price'] );
							$value['data']->set_price($bk_price);
						}
					}
				}   
			}
		}
	}
}

/**
 * Add product type options
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_product_type_options')) {
	add_filter('product_type_options', 'workreap_product_type_options', 10, 1);
	function workreap_product_type_options( $options ) {
		if(current_user_can('administrator')) {
			$bk_settings	= worrketic_hiring_payment_setting();

			if( isset( $bk_settings['is_enable'] ) && $bk_settings['is_enable'] === 'yes' ){
				$options['workreap_hiring'] = array(
					'id' 			=> '_workreap_hiring',
					'wrapper_class' => 'show_if_simple show_if_variable',
					'label' 		=> esc_html__('Hire Freelancer', 'workreap'),
					'description' 	=> esc_html__('Hire freelancer product will be used to make the payment for the project/job', 'workreap'),
					'default' => 'no'
				);
			}

			$options['workreap_posting_job'] = array(
				'id' 			=> '_workreap_posting_job',
				'wrapper_class' => 'show_if_simple show_if_variable',
				'label' 		=> esc_html__('Post Job', 'workreap'),
				'description' 	=> esc_html__('Post job product will be used to make the payment for posting project/job', 'workreap'),
				'default' => 'no'
			);
		}
		
		return $options;
	}
}

/**
 * Save products meta
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_woocommerce_process_product_meta')) {
	add_action('woocommerce_process_product_meta_variable', 'workreap_woocommerce_process_product_meta', 10, 1);
	add_action('woocommerce_process_product_meta_simple', 'workreap_woocommerce_process_product_meta', 10, 1);
	function workreap_woocommerce_process_product_meta( $post_id ) {
		$bk_settings	= worrketic_hiring_payment_setting();
		if( isset( $bk_settings['is_enable'] ) && $bk_settings['is_enable'] === 'yes' && !empty($_POST['_workreap_hiring']) ){
			workreap_update_hiring_product(); //update default booking product

			$is_workreap_hiring	= isset($_POST['_workreap_hiring']) ? 'yes' : 'no';
			update_post_meta($post_id, '_workreap_hiring', $is_workreap_hiring);
		}
	}
}

/**
 * Update hiring product
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_update_hiring_product')) {

    function workreap_update_hiring_product() {
		$meta_query_args = array();
		$args = array(
			'post_type' 		=> 'product',
			'posts_per_page' 	=> -1,
			'order' 			=> 'DESC',
			'orderby'			=> 'ID',
			'post_status' 		=> 'publish',
			'ignore_sticky_posts' => 1
		);


		$meta_query_args[] = array(
			'key' 			=> '_workreap_hiring',
			'value' 		=> 'yes',
			'compare' 		=> '=',
		);
		
		$query_relation 		= array('relation' => 'AND',);
		$meta_query_args 		= array_merge($query_relation, $meta_query_args);
		$args['meta_query'] 	= $meta_query_args;
		
		$booking_product = get_posts($args);
		
		if (!empty($booking_product)) {
            $counter = 0;
            foreach ($booking_product as $key => $product) {
                update_post_meta($product->ID, '_workreap_hiring', 'no');
            }
        }
		
	}
}

/**
 * Save products meta
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_woocommerce_process_posting_job_product_meta')) {
	add_action('woocommerce_process_product_meta_variable', 'workreap_woocommerce_process_posting_job_product_meta', 10, 1);
	add_action('woocommerce_process_product_meta_simple', 'workreap_woocommerce_process_posting_job_product_meta', 10, 1);
	function workreap_woocommerce_process_posting_job_product_meta( $post_id ) {
		workreap_update_hiring_product(); //update default booking product

		$is_workreap_posting_job	= isset($_POST['_workreap_posting_job']) ? 'yes' : 'no';
		update_post_meta($post_id, '_workreap_posting_job', $is_workreap_posting_job);
	}
}

/**
 * Update posting job product
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_update_posting_job_product')) {

    function workreap_update_posting_job_product() {
		$meta_query_args = array();
		$args = array(
			'post_type' 		=> 'product',
			'posts_per_page' 	=> -1,
			'order' 			=> 'DESC',
			'orderby'			=> 'ID',
			'post_status' 		=> 'publish',
			'ignore_sticky_posts' => 1
		);


		$meta_query_args[] = array(
			'key' 			=> '_workreap_posting_job',
			'value' 		=> 'yes',
			'compare' 		=> '=',
		);
		
		$query_relation 		= array('relation' => 'AND',);
		$meta_query_args 		= array_merge($query_relation, $meta_query_args);
		$args['meta_query'] 	= $meta_query_args;
		
		$booking_product = get_posts($args);
		
		if (!empty($booking_product)) {
            $counter = 0;
            foreach ($booking_product as $key => $product) {
                update_post_meta($product->ID, '_workreap_posting_job', 'no');
            }
        }
		
	}
}

/**
 * Remove Product link in checkout
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
add_filter( 'woocommerce_order_item_permalink', '__return_false' );