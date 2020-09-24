<?php
/**
 * APP API to manage users
 *
 * This file will include all global settings which will be used in all over the plugin,
 * It have gatter and setter methods
 *
 * @link              https://codecanyon.net/user/amentotech/portfolio
 * @since             1.0.0
 * @package           Workreap APP
 *
 */
if (!class_exists('AndroidApp_User_Route')) {

    class AndroidApp_User_Route extends WP_REST_Controller{

        /**
         * Register the routes for the user.
         */
        public function register_routes() {
            $version 	= '1';
            $namespace 	= 'api/v' . $version;
            $base 		= 'user';
			
			//user login
            register_rest_route($namespace, '/' . $base . '/do_login',
                array(
                    array(
                        'methods' => WP_REST_Server::READABLE,
                        'callback' => array(&$this, 'get_items'),
                        'args' => array(
                        ),
                    ),
                    array(
                        'methods' => WP_REST_Server::CREATABLE,
                        'callback' => array(&$this, 'user_login'),
                        'args' => array(),
                    ),
                )
            );
			
			// For signup
			register_rest_route($namespace, '/' . $base . '/signup',
                array(
                  array(
                        'methods' 	=> WP_REST_Server::CREATABLE,
                        'callback' 	=> array(&$this, 'signup'),
                        'args' 		=> array(),
                    ),
                )
            );
			
			// For signup
			register_rest_route($namespace, '/' . $base . '/resend_code',
                array(
                  array(
                        'methods' 	=> WP_REST_Server::CREATABLE,
                        'callback' 	=> array(&$this, 'resend_code'),
                        'args' 		=> array(),
                    ),
                )
            );
			
			// For verification code
			register_rest_route($namespace, '/' . $base . '/account_verification',
                array(
                  array(
                        'methods' 	=> WP_REST_Server::CREATABLE,
                        'callback' 	=> array(&$this, 'account_verification'),
                        'args' 		=> array(),
                    ),
                )
            );
			
			// For package info
			register_rest_route($namespace, '/' . $base . '/check_package',
                array(
                  array(
                        'methods' 	=> WP_REST_Server::READABLE,
                        'callback' 	=> array(&$this, 'check_package'),
                        'args' 		=> array(),
                    ),
                )
            );
			
			// For package info
			register_rest_route($namespace, '/' . $base . '/get_access',
                array(
                  array(
                        'methods' 	=> WP_REST_Server::READABLE,
                        'callback' 	=> array(&$this, 'get_access'),
                        'args' 		=> array(),
                    ),
                )
            );
			
			//user login
            register_rest_route($namespace, '/' . $base . '/do_logout',
                array(                 
                    array(
                        'methods' => WP_REST_Server::CREATABLE,
                        'callback' => array(&$this, 'do_logout'),
                        'args' => array(),
                    ),
                )
            );
			
			//favorite List
            register_rest_route($namespace, '/' . $base . '/favorite',
                array(                 
                    array(
                        'methods' => WP_REST_Server::CREATABLE,
                        'callback' => array(&$this, 'do_favorite'),
                        'args' => array(),
                    ),
                )
            );
			
			//forgot password
			register_rest_route($namespace, '/' . $base . '/forgot_password',
                array(
                    array(
                        'methods' => WP_REST_Server::READABLE,
                        'callback' => array(&$this, 'get_items'),
                        'args' => array(),
                    ),
                    array(
                        'methods' => WP_REST_Server::CREATABLE,
                        'callback' => array(&$this, 'get_forgot_password'),
                        'args' => array(),
                    ),
                )
            );
			
			//User Reporting
			register_rest_route($namespace, '/' . $base . '/reporting',
                array(
                    array(
                        'methods' => WP_REST_Server::READABLE,
                        'callback' => array(&$this, 'get_items'),
                        'args' => array(),
                    ),
                    array(
                        'methods' => WP_REST_Server::CREATABLE,
                        'callback' => array(&$this, 'reporting_user'),
                        'args' => array(),
                    ),
                )
            );
			
			register_rest_route($namespace, '/' . $base .'/create_checkout_page',
			array(
				array(
					'methods' 	=> WP_REST_Server::CREATABLE,
					'callback' 	=> array($this, 'create_checkout_page'),
					'args' 		=> array(),
				),
			));
        }
		
		/**
         * Resend Verification code
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Request
         */
        public function resend_code($request) {
			$user_id	= !empty( $request['user_id'] ) ? intval( $request['user_id'] ) : '';
			$json		= array();
			
			if( empty( $user_id ) ){
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('User ID is required.', 'workreap_api');
				return new WP_REST_Response($json, 203);
			} else {
				$user_info 	= get_userdata( $user_id );
				$key_hash 	= rand( 1000, 9999 );
				update_user_meta( $user_id, 'confirmation_key', $key_hash );
				$code 		= $key_hash;			
				$email 		= $user_info->user_email;		
				$name  		= workreap_get_username( $user_id );
				$blogname   = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);


				//Send verification code
				if (class_exists('Workreap_Email_helper')) {
					if (class_exists('WorkreapRegisterEmail')) {
						$email_helper = new WorkreapRegisterEmail();
						$emailData = array();
						$emailData['name'] 				= $name;
						$emailData['email']				= $email;
						$emailData['verification_code'] = $code;
						$emailData['site'] = $blogname;
						$email_helper->send_verification($emailData);
					}
				} 
				
				$json['type'] = 'success';
				$json['message'] = esc_html__('Verification code has sent on your email', 'workreap_api');        
				return new WP_REST_Response($json, 200);
			}
			
		}
		/**
         * Signup user for application
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Request
         */
        public function signup($request) {
			
			$json			= array();
			$validations 	= array(
								'gender' 		=> esc_html__('Gender field is required', 'workreap_api'),
								'username' 		=> esc_html__('User Name field is required', 'workreap_api'),
								'first_name' 	=> esc_html__('First Name is required', 'workreap_api'),
								'last_name' 	=> esc_html__('Last Name is required.', 'workreap_api'),
								'email'  		=> esc_html__('Email field is required.', 'workreap_api'),   
								'location' 			=> esc_html__('Location field is required', 'workreap_api'),
								'password' 			=> esc_html__('Password field is required', 'workreap_api'),
								'verify_password' 	=> esc_html__('Verify Password field is required.', 'workreap_api'),
								'user_type'  		=> esc_html__('User type field is required.', 'workreap_api'),            
								'termsconditions'  	=> esc_html__('You should agree to terms and conditions.', 'workreap_api'),
							);
			
			 foreach ( $validations as $key => $value ) {
				if ( empty( $request[$key] ) ) {
					$json['type'] 		= 'error';
					$json['message'] 	= $value;
					return new WP_REST_Response($json, 203);
				}

				//Validate email address
				if ( $key === 'email' ) {
					if ( !is_email( $request['email'] ) ) {
						$json['type'] 		= 'error';
						$json['message'] 	= esc_html__('Please add a valid email address.', 'workreap_api');
						return new WP_REST_Response($json, 203);
					} elseif( email_exists( $request['email']  ) ) {
						$json['type'] = 'error';
            			$json['message'] = esc_html__('This email is already registered', 'workreap_api');
						return new WP_REST_Response($json, 203);
					}
				}	
				 
				if ($key === 'password') {
					 if ( strlen( $request[$key] ) < 6 ) {
						$json['type'] 	 	= 'error';
						$json['message'] 	= esc_html__('Password length should be minimum 6', 'workreap_api');
						return new WP_REST_Response($json, 203);						 
					 }
				}
				if ($key === 'verify_password') {
					if ( $request['password'] != $request['verify_password']) {
						$json['type'] 		= 'error';
						$json['message'] 	= esc_html__('Password does not match.', 'workreap_api');
						return new WP_REST_Response($json, 203);
					}
				}
				if( $key == 'user_type'){
					if( $request['user_type'] == 'employer' ){
						$employees  = !empty( $request['employees'] ) ? esc_attr( $request['employees'] ) : '';
						$department = !empty( $request['department'] ) ? esc_attr( $request['department'] ) : '';
						if( empty( $employees ) || empty( $department ) ){
							$json['type'] 		= 'error';
							$json['message'] 	= esc_html__('Employee and department fields are required.', 'workreap_api');
							return new WP_REST_Response($json, 203);
						}
					}
				}
				
				 
			}
			
			$username	=  !empty( $request['username'] ) ? $request['username'] : '';
			
			if( !empty( $username ) && username_exists( $username ) ) {
				$json['type'] 		= 'error';
            	$json['message'] 	= esc_html__('Username already registered', 'workreap_api');
				return new WP_REST_Response($json, 203);
			}
			
			$username 	= !empty( $request['username'] ) ? esc_attr( $request['username'] ) : '';
			$first_name = !empty( $request['first_name'] ) ? esc_attr( $request['first_name'] ) : '';
			$last_name 	= !empty( $request['last_name'] ) ? esc_attr( $request['last_name'] ) : '';
			$gender 	= !empty( $request['gender'] ) ? esc_attr( $request['gender'] ) : '';
			$email 		= !empty( $request['email'] ) ? esc_attr( $request['email'] ) : '';
			$location   = !empty( $request['location'] ) ? esc_attr( $request['location'] ) : '';
			$password  	= !empty( $request['password'] ) ? esc_attr( $request['password'] ) : '';
			$user_type 	= !empty( $request['user_type'] ) ? esc_attr( $request['user_type'] ) : '';
			$department = !empty( $request['department'] ) ? esc_attr( $request['department'] ) : '';
			$employees  = !empty( $request['employees'] ) ? esc_attr( $request['employees'] ) : '';
			
			//Set User Role
			$db_user_role = 'employers';
			if( $user_type === 'freelancer' ){
				$db_user_role = 'freelancers';
			} else {
				$db_user_role = 'employers';
			}
			
			//User Registration
			$random_password = $password;
			$full_name 		 = $first_name.' '.$last_name;
			$user_nicename   = sanitize_title( $full_name );

			$userdata = array(
				'user_login'  		=>  $username,
				'user_pass'    		=>  $random_password,
				'user_email'   		=>  $email,  
				'user_nicename'   	=>  $user_nicename,  
				'display_name'   	=>  $full_name,  
			);
			//print_r($userdata);die();
			$user_identity 	 = wp_insert_user( $userdata );

			if ( is_wp_error( $user_identity ) ) {
				$json['type'] 		= "error";
				$json['message'] 	= esc_html__("User already exists. Please try another one.", 'workreap_api');
				return new WP_REST_Response($json, 203);
			} else {
				global $wpdb;
				wp_update_user( array('ID' => esc_sql( $user_identity ), 'role' => esc_sql( $db_user_role ), 'user_status' => 1 ) );

				$wpdb->update(
						$wpdb->prefix . 'users', array('user_status' => 1), array('ID' => esc_sql($user_identity))
				);

				update_user_meta( $user_identity, 'first_name', $first_name );
				update_user_meta( $user_identity, 'last_name', $last_name );
				update_user_meta( $user_identity, 'gender', esc_attr( $gender ) );              

				update_user_meta($user_identity, 'show_admin_bar_front', false);
				update_user_meta($user_identity, 'full_name', esc_attr($full_name));

				$key_hash = rand( 1000, 9999 );
				update_user_meta( $user_identity, '_is_verified', 'no' );
				update_user_meta( $user_identity, 'confirmation_key', $key_hash );

				$protocol = is_ssl() ? 'https' : 'http';

				$verify_link = esc_url(add_query_arg(array(
					'key' => $key_hash.'&verifyemail='.$email
								), home_url('/', $protocol)));

				//Create Post
				$user_post = array(
					'post_title'    => wp_strip_all_tags( $full_name ),
					'post_status'   => 'publish',
					'post_author'   => $user_identity,
					'post_type'     => $db_user_role,
				);

				$post_id    = wp_insert_post( $user_post );

				if( !is_wp_error( $post_id ) ) {

					$fw_options = array();

					//Update user linked profile
					update_user_meta( $user_identity, '_linked_profile', $post_id );
					wp_set_post_terms( $post_id, $location, 'locations' );
					update_post_meta( $post_id, '_is_verified', 'no' );

					if( $db_user_role == 'employers' ){

						update_post_meta($post_id, '_user_type', 'employer');
						update_post_meta($post_id, '_employees', $employees);            		
						update_post_meta($post_id, '_followers', '');

						//update department
						if( !empty( $department ) ){
							$department_term = get_term_by( 'term_id', $department, 'department' );
							if( !empty( $department_term ) ){
								wp_set_post_terms( $post_id, $department, 'department' );
							}
						}

						//Fw Options
						$fw_options['department']         = array( $department );
						$fw_options['no_of_employees']    = $employees;

					} elseif( $db_user_role == 'freelancers' ){
						update_post_meta($post_id, '_user_type', 'freelancer');
						update_post_meta($post_id, '_perhour_rate', '');
						update_post_meta($post_id, 'rating_filter', 0);
						update_post_meta($post_id, '_freelancer_type', 'rising_talent');         		           		
						update_post_meta($post_id, '_featured_timestamp', 0); 
						update_post_meta($post_id, '_english_level', 'basic');
						//extra data in freelancer
						update_post_meta($post_id, '_gender', $gender);
						$fw_options['_perhour_rate']    = '';
						$fw_options['gender']    		= $gender;
					}

					//Set country for unyson
					$locations = get_term_by( 'slug', $location, 'locations' );
					$location_data = array();
					if( !empty( $locations ) ){
						$location_data[0] = $locations->term_id;
						wp_set_post_terms( $post_id, $locations->term_id, 'locations' );
					}

					if ( function_exists('fw_get_db_post_option' )) {
						$dir_latitude 	= fw_get_db_settings_option('dir_latitude');
						$dir_longitude 	= fw_get_db_settings_option('dir_longitude');
						$verify_user 	= fw_get_db_settings_option('verify_user', $default_value = null);
						$verify_user	= !empty( $verify_user ) ? $verify_user : 'verified';
					} else {
						$dir_latitude	= '';
						$dir_longitude	= '';
						//$verify_user  	= 'verified';
					}
					
					$verify_user	= !empty( $verify_user ) ? $verify_user : 'verified';
					//add extra fields as a null
					$tagline	= esc_html__('Your tagline goes here','workreap_api');
					update_post_meta($post_id, '_tag_line', $tagline);
					update_post_meta($post_id, '_address', '');
					update_post_meta($post_id, '_latitude', $dir_latitude);
					update_post_meta($post_id, '_longitude', $dir_longitude);

					$fw_options['address']    	= '';
					$fw_options['longitude']    = $dir_longitude;
					$fw_options['latitude']    	= $dir_latitude;
					$fw_options['tag_line']     = $tagline;
					//end extra data

					//Update User Profile
					$fw_options['country']            = $location_data;
					fw_set_db_post_option($post_id, null, $fw_options);

					//update privacy settings
					$settings		 = workreap_get_account_settings($user_type);
					if( !empty( $settings ) ){
						foreach( $settings as $key => $value ){
							$val = $key === '_profile_blocked' ? 'off' : 'on';
							update_post_meta($post_id, $key, $val);
						}
					}


					update_post_meta($post_id, '_linked_profile', $user_identity);

					//Send email to users
					if (class_exists('Workreap_Email_helper')) {
						$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
						$emailData = array();
						$emailData['name'] 				= $first_name;
						$emailData['password'] 			= $random_password;
						$emailData['email'] 			= $email;
						$emailData['verification_code'] = $key_hash;
						$emailData['site'] 				= $blogname;

						//Welcome Email
						if( $db_user_role === 'employers' ){
							if (class_exists('WorkreapRegisterEmail')) {
								$email_helper = new WorkreapRegisterEmail();
								$email_helper->send_employer_email($emailData);
							}
						} else if( $db_user_role === 'freelancers' ){
							if (class_exists('WorkreapRegisterEmail')) {
								$email_helper = new WorkreapRegisterEmail();
								$email_helper->send_freelacner_email($emailData);
							}
						}

						//Send code
						if( isset( $verify_user ) && $verify_user === 'verified' ){
							$json['verify_user'] 			= 'verified';
							if (class_exists('WorkreapRegisterEmail')) {
								$email_helper = new WorkreapRegisterEmail();
								$email_helper->send_verification($emailData);
							}
						} else{
							$json['verify_user'] 			= 'none';
						}

						//Send admin email
						if (class_exists('WorkreapRegisterEmail')) {
							$email_helper = new WorkreapRegisterEmail();
							$email_helper->send_admin_email($emailData);
						}
					}		    

				} else {
					$json['type'] 		= 'error';
					$json['message'] 	= esc_html__('Some error occurs, please try again later', 'workreap_api');                
					return new WP_REST_Response($json, 203);
				}			

				if( isset( $verify_user ) && $verify_user === 'none' ){
					$json_message = esc_html__("Your account have been created. Please wait while your account is verified by the admin.", 'workreap_api');
				} else{
					$json_message = esc_html__("Your account have been created. Please verify your account through verification code, an email have been sent your email address.", 'workreap_api');
				}      	               

				$json['type'] 			= 'success';
				$json['user_id']		= $user_identity;
				$json['message'] 		= $json_message;
				return new WP_REST_Response($json, 200);
			}
			//print_r($request['fisrt_name']);die();
		}
		
		/**
         * Account verification
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
		public function account_verification($request){
			$user_id		= !empty( $request['user_id'] ) ? intval( $request['user_id'] ) : '';
			$code 			= !empty( $request['code'] ) ? esc_attr( $request['code'] ) : '';
			$confirmation_key = get_user_meta($user_id, 'confirmation_key', true);
			$confirmation_key = !empty( $confirmation_key ) ? $confirmation_key : '';
			if( empty( $user_id ) ) {
				$json['type'] 		= 'error';
            	$json['message'] 	= esc_html__('User ID is required field.', 'workreap_api');
				return new WP_REST_Response($json, 203);
			}
			
			if( empty( $code ) ) {
				$json['type'] 		= 'error';
            	$json['message'] 	= esc_html__('Verification code is required field.', 'workreap_api');
				return new WP_REST_Response($json, 203);
			}
			//print_r($confirmation_key);die();
			if( $code === $confirmation_key ){
				update_user_meta( $user_id, '_is_verified', 'yes' );

				//update post for users verification
				$linked_profile   	= workreap_get_linked_profile_id($user_id);
				update_post_meta($linked_profile, '_is_verified', 'yes');		

				$user_type						= workreap_get_user_type( $user_id );
				$freelancer_package_id			= workreap_get_package_type( 'package_type','trail_freelancer');
				$employer_package_id			= workreap_get_package_type( 'package_type','trail_employer');

				if( $user_type === 'employer' && !empty($employer_package_id) ) {
					workreap_update_pakage_data( $employer_package_id ,$user_id,'' );
				} else if( $user_type === 'freelancer' && !empty($freelancer_package_id) ) {
					workreap_update_pakage_data( $freelancer_package_id ,$user_id,'' );
				}
				
				$json['type']		= 'success';
				$json['signup']		= 'yes';
				$json['message'] 	= esc_html__('Your account has been verified successfully!', 'workreap_api');
				return new WP_REST_Response($json, 200);
			} else {
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('No kiddies please', 'workreap_api');	        
				return new WP_REST_Response($json, 203);
			}
		}
		/**
         * Create temp chekcout data
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
		public function create_checkout_page($request){
			global $wpdb;
			$params = $request->get_params();       
			if( !empty( $params['payment_data'] ) ){
			   
				$insert_data = "insert into `".MOBILE_APP_TEMP_CHECKOUT."` set `temp_data`='".stripslashes($params['payment_data'])."'";     
				$wpdb->query($insert_data);

				if(isset($wpdb->insert_id)){ 
					$data_id = $wpdb->insert_id; 
				} else{
					$data_id = $wpdb->print_error();
				}

				$json['type'] 		= "success";
				$json['message'] 	= esc_html__("You order has been placed, Please pay to make it complete", "workreap_api");

				 $pages = query_posts(array(
					 'post_type' =>'page',
					 'meta_key'  =>'_wp_page_template',
					 'meta_value'=> 'mobile-checkout.php'
				 ));

				$url = null;
				if(!empty($pages[0])) {
					 $url = get_page_link($pages[0]->ID).'?order_id='.$data_id.'&platform=mobile';
				}

				$json['url'] 		= esc_url($url);
				return new WP_REST_Response($json, 200);
			 } else {
				$json['type'] = "error";
				$json['message'] = esc_html__("Invalid Parem Data", 'workreap_api');
				return new WP_REST_Response($json, 203);
			}

		}
		
        /**
         * Get a collection of items
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function get_items($request) {
            $items['data'] = array();        
            return new WP_REST_Response($items, 200);
        }
		
		public function get_access() {
			$json			= array();
			$user_meta		= array();
			$user_meta['access_type']['service_access']	= '';
			$user_meta['access_type']['job_access']		= '';
			if( apply_filters('workreap_system_access','service_base') === true ){
				$user_meta['access_type']['service_access']	= 'yes';
			}
			if( apply_filters('workreap_system_access','job_base') === true ){
				$user_meta['access_type']['job_access']	= 'yes';
			}
			$json	= maybe_unserialize($user_meta);
			return new WP_REST_Response($json, 200);
		}
		/**
         * Set forgot password
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function do_favorite($request) {
            global $wpdb;
            $json			= array();
			$user_id 		= !empty($request['id']) ? $request['id'] : '';	
			$favorite_id 	= !empty($request['favorite_id']) ? $request['favorite_id'] : '';
			$type		 	= !empty($request['type']) ? $request['type'] : '';

			if ( !empty($user_id) && !empty($favorite_id) ) {
				$linked_profile   	= workreap_get_linked_profile_id($user_id);
				if( ! empty($linked_profile) ) {
					if( !empty($type)) {
						if( $type === '_saved_freelancers' ) {
							$wishlist 			= get_post_meta($linked_profile, '_saved_freelancers', true);
							$wishlist   		= !empty( $wishlist ) && is_array( $wishlist ) ? $wishlist : array();
							$favorite_id   		= workreap_get_linked_profile_id($favorite_id);
							$wishlist[] 		= $favorite_id;
							$wishlist   		= array_unique( $wishlist );
							update_post_meta( $linked_profile, '_saved_freelancers', $wishlist );
							$json['type'] 		= "success";
							$json['message'] 	= esc_html__("Freelancers is successfull added in your favorite list.", 'workreap_api');
							return new WP_REST_Response($json, 200);
						} elseif ( $type === '_saved_projects' ) {
							$wishlist 			= get_post_meta($linked_profile, '_saved_projects', true);
							$wishlist   		= !empty( $wishlist ) && is_array( $wishlist ) ? $wishlist : array();
							$wishlist[] 		= $favorite_id;
							$wishlist   		= array_unique( $wishlist );
							update_post_meta( $linked_profile, '_saved_projects', $wishlist );
							$json['type'] 		= "success";
							$json['message'] 	= esc_html__("Job is successfull added in your favorite list.", 'workreap_api');
							return new WP_REST_Response($json, 200);
						}elseif ( $type === '_following_employers' ) {
							$wishlist 			= get_post_meta($linked_profile, '_following_employers', true);
							$wishlist   		= !empty( $wishlist ) && is_array( $wishlist ) ? $wishlist : array();
							$favorite_id   		= workreap_get_linked_profile_id($favorite_id);
							$wishlist[] 		= $favorite_id;
							$wishlist   		= array_unique( $wishlist );
							update_post_meta( $linked_profile, '_following_employers', $wishlist );
							$json['type'] 		= "success";
							$json['message'] 	= esc_html__("Company is successfull added in your favorite list.", 'workreap_api');
							return new WP_REST_Response($json, 200);
						} elseif ( $type === '_saved_services' ) {
							$wishlist 			= get_post_meta($linked_profile, '_saved_services', true);
							$wishlist   		= !empty( $wishlist ) && is_array( $wishlist ) ? $wishlist : array();
							$favorite_id   		= $favorite_id;
							$wishlist[] 		= $favorite_id;
							$wishlist   		= array_unique( $wishlist );
							update_post_meta( $linked_profile, '_saved_services', $wishlist );
							$json['type'] 		= "success";
							$json['message'] 	= esc_html__("Service is successfull added in your favorite list.", 'workreap_api');
							return new WP_REST_Response($json, 200);
						} 
					} else {
						$json['type'] = "error";
						$json['message'] = esc_html__("Type field is required.", 'workreap_api');
						return new WP_REST_Response($json, 203);
					}
					
				} else {
					$json['type'] = "error";
					$json['message'] = esc_html__("Invalid User id.", 'workreap_api');
					return new WP_REST_Response($json, 203);
				}
				
			}
		}
		
		/**
         * Set Forgot Password
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function get_forgot_password($request) {
            global $wpdb;
            $json		= array();
			$user_input = !empty($request['email']) ? $request['email'] : '';	

			if (empty($user_input)) {
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('Please add email address.', 'workreap_api');
				return new WP_REST_Response($json, 203);
			} else if (!is_email($user_input)) {
				$json['type'] 		= "error";
				$json['message'] 	= esc_html__("Please add a valid email address.", 'workreap_api');
				return new WP_REST_Response($json, 203);
			}      

			$user_data = get_user_by($user_input);
			if (empty($user_data) || $user_data->caps['administrator'] == 1) {
				//the condition $user_data->caps[administrator] == 1 to prevent password change for admin users.
				//if you prefer to offer password change for admin users also, just delete that condition.
				$json['type'] = "error";
				$json['message'] = esc_html__("Invalid E-mail address!", 'workreap_api');
				return new WP_REST_Response($json, 203);
			}

			$user_id    = $user_data->ID;
			$user_login = $user_data->user_login;
			$user_email = $user_data->user_email;
			$username   = workreap_get_username( $user_id );

			$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));

			if (empty($key)) {
				//generate reset key
				$key = wp_generate_password(20, false);
				$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
			}

			$protocol 	= is_ssl() ? 'https' : 'http';
			$reset_link	= esc_url(add_query_arg(array('action' => 'reset_pwd', 'key' => $key, 'login' => $user_login), home_url('/', $protocol)));

			//Send email to user
			if (class_exists('Workreap_Email_helper')) {
				if (class_exists('WorkreapGetPasswordEmail')) {
					$email_helper = new WorkreapGetPasswordEmail();
					$emailData = array();
					$emailData['username']  = $username;
					$emailData['email']     = $user_email;
					$emailData['link']      = $reset_link;
					$email_helper->send($emailData);
				}
			}     

			$json['type'] 		= "success";
			$json['message'] 	= esc_html__("A link has been sent, please check your email.", 'workreap_api');
			$json				= maybe_unserialize($json);
			return new WP_REST_Response($json, 203);

        }

        /**
         * Login user for application
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Request
         */
        public function user_login($request) {
			$json 	=  array();
			$items 	=  array();
            if (!empty($request['username']) && !empty($request['password'])) {
                $creds = array(
                    'user_login' 			=> $request['username'],
                    'user_password' 		=> $request['password'],
                    'remember' 				=> true
                );
                
                $user = wp_signon($creds, false);
				
				if (is_wp_error($user)) {
                    $json['type']		= 'error';
                    $json['message']	= esc_html__('Some error occur, please try again later.','workreap_api');
					return new WP_REST_Response($json, 203);
                } else {
					
					unset($user->allcaps);
					unset($user->filter);
					
					$user_metadata	= array();
					$profile_data	= array();
					$shipping		= array();
					$billing		= array();
					
					$profile_id		= workreap_get_linked_profile_id($user->data->ID);
					
					if (function_exists('fw_get_db_post_option')) {
						$banner_image       = fw_get_db_post_option($profile_id, 'banner_image', true);	
					}else {
						$banner_image	= array();
					}
					
					//$user_pmetadata['featur_job_op']	= '';
					
					if( 'freelancer' === apply_filters('workreap_get_user_type', $user->data->ID ) ){
						
						$user_pmetadata['user_type']	= 'freelancer';
						
						$user_pmetadata['profile_img'] 	= apply_filters(
															'workreap_freelancer_avatar_fallback', workreap_get_freelancer_avatar( array( 'width' => 100, 'height' => 100 ), $profile_id ), array( 'width' => 100, 'height' => 100 )
														);
						$user_pmetadata['banner_img'] 	= apply_filters(
															'workreap_freelancer_banner_fallback', workreap_get_freelancer_banner( array( 'width' => 100, 'height' => 100 ), $profile_id ), array( 'width' => 100, 'height' => 100 )
														);
						
					} else if( 'employer' == apply_filters('workreap_get_user_type', $user->data->ID ) ){
						
						$user_pmetadata['user_type']	= 'employer';
						
						$user_pmetadata['profile_img'] 	=  apply_filters(
																'workreap_employer_avatar_fallback', workreap_get_employer_avatar(array('width' => 100, 'height' => 100), $profile_id), array('width' => 100, 'height' => 100) 
															);
						
						$user_pmetadata['banner_img'] 	=  apply_filters(
																'workreap_employer_banner_fallback', workreap_get_employer_banner(array('width' => 100, 'height' => 100), $profile_id), array('width' => 100, 'height' => 100) 
															);
						/*if( function_exists('workreap_featured_job') ) {
							$user_pmetadata['featur_job_op']	= workreap_featured_job($user->data->ID);
						}*/ 
						
					}
					
					$first_name	= get_user_meta($user->data->ID, 'first_name', true);
					$last_name	= get_user_meta($user->data->ID, 'last_name', true);
					$first_name	= !empty( $first_name ) ? $first_name : '';
					$last_name	= !empty( $last_name ) ? $last_name : '';

					$permission			= '';
					if(apply_filters('workreap_is_feature_allowed', 'wt_pr_chat', $user->data->ID) === true) {
						$permission		= 'allow';	
					} else {
						$permission		= 'notallow';	
					}
					
					$user_meta	= array(
						'profile_id'		=> $profile_id,
						'id' 				=> $user->data->ID,
						'user_login' 		=> $user->data->user_login,
						'user_pass' 		=> $user->data->user_pass,
						'first_name' 		=> $first_name,
						'last_name' 		=> $last_name,
						'user_email' 		=> $user->data->user_email,
						'chat_permission'	=> $permission
					);
					
					if ( function_exists( 'fw_get_db_settings_option' ) ) {
						$chat_settings    			= fw_get_db_settings_option('chat');
						$user_meta['chat_type']		= !empty( $chat_settings['gadget'] ) ? $chat_settings['gadget'] : 'inbox';
						$user_meta['host']			=  !empty( $chat_settings['chat']['host'] ) ?  $chat_settings['chat']['host'] : '';
						$user_meta['port']			=  !empty( $chat_settings['chat']['port'] ) ?  $chat_settings['chat']['port'] : '';
					} 
					$post_meta	= array(
						'_tag_line' 			=> '_tag_line',
						'_gender' 				=> '_gender',
						'_is_verified' 			=> '_is_verified',
						'_featured_timestamp' 	=> '_featured_timestamp'
					);
					
					foreach( $post_meta as $key => $usermeta ){
							$user_pmetadata[$key] = get_post_meta($profile_id,$key,true);		
					}
					
					$user_meta['service_access']	= '';
					$user_meta['job_access']		= '';
					
					if( apply_filters('workreap_system_access','service_base') === true ){
						$user_meta['service_access']	= 'yes';
					}
					
					if( apply_filters('workreap_system_access','job_base') === true ){
						$user_meta['job_access']	= 'yes';
					}
					
					if ( class_exists('WC_Customer') ) {
						$customer 	= new WC_Customer( $user->data->ID );
						$shipping	= $customer->get_shipping();
						$billing	= $customer->get_billing();
					}
					
					$json['profile']['shipping'] 		= maybe_unserialize($shipping);
					$json['profile']['billing'] 		= maybe_unserialize($billing);
					
					$user_pmetadata['full_name']	= get_the_title($profile_id);
					$json['profile']['pmeta'] 		= maybe_unserialize($user_pmetadata);
					$json['profile']['umeta'] 		= maybe_unserialize($user_meta);
					$json['type'] 					= 'success';
                    $json['message'] 				= esc_html__('You are logged in successfully', 'workreap_api');
					
                    $items							= maybe_unserialize($json);
					
					return new WP_REST_Response($items, 200);
                }                
            } else{
				$json['type']		= 'error';
				$json['message']	= esc_html__('user name and password are required fields.','workreap_api');
				$json				= maybe_unserialize($json);
				return new WP_REST_Response($json, 203);
			}
        }
		
		/**
		* Logout user from the application
		*
		* @param WP_REST_Request $request Full data about the request.
		* @return WP_Error|WP_REST_Request
		*/
		
        public function do_logout($request) {
			$json  = array();
			
        	if (!empty( $request['user_id'] ) ) {               						
        		$user_id 	= $request['user_id'];
				$sessions 	= WP_Session_Tokens::get_instance($user_id);
				
				// we have got the sessions, destroy them all!
				$sessions->destroy_all();

                $json['type'] 		= "success";
                $json['message'] 	= esc_html__('You are logged out successfully', 'workreap_api');               
                return new WP_REST_Response($json, 200);
            } else {
				$json['type'] 		= "error";
				$json['message'] 	= esc_html__('User ID required', 'workreap_api');               
				return new WP_REST_Response($json, 203); 
			}               
        }
		
		/**
         * Report user from the application
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Request
         */
        public function reporting_user($request) {
			
			$type 			= !empty( $request['type'] ) ? esc_attr( $request['type'] ) : '';
			$reported_id 	= !empty( $request['id'] ) ? intval( $request['id'] ) : '';
			$user_id 		= !empty( $request['user_id'] ) ? intval( $request['user_id'] ) : '';
			$description 	= !empty( $request['description'] ) ? esc_attr( $request['description'] ) : '';
			$reason 		= !empty( $request['reason'] ) ? esc_attr( $request['reason'] ) : '';
			$json 			= array();
			
			if( empty( $reason ) || empty( $description ) ){
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__( 'All the fields are required', 'workreap_api' );
				 return new WP_REST_Response($json, 203);
			}

			if ( empty( $user_id ) ) {
				$json['type']		= 'error';
				$json['message'] 	= esc_html__( 'You must login before report', 'workreap_api' );
				 return new WP_REST_Response($json, 203);
			}
			
			$reasons			= workreap_get_report_reasons();
			$linked_profile   	= workreap_get_linked_profile_id($user_id);
			$title				= !empty( $reasons[$reason] ) ? $reasons[$reason] : rand(1,999999);
			
			//Create Post
			$user_post = array(
				'post_title'    => wp_strip_all_tags( $title ),
				'post_status'   => 'publish',
				'post_content'  => $description,
				'post_author'   => $user_id,
				'post_type'     => 'reports',
			);

			$post_id    = wp_insert_post( $user_post );


			if( !is_wp_error( $post_id ) ) {
				update_post_meta($post_id, '_report_type', $type);
				update_post_meta($post_id, '_user_by', $linked_profile);
				//Send email to users
				if (class_exists('Workreap_Email_helper')) {
					if (class_exists('WorkreapReportUser')) {
						$email_helper = new WorkreapReportUser();
						$emailData = array();
						$emailData['name'] 				= get_the_title($reported_id);
						$emailData['user_link'] 		= get_edit_post_link($linked_profile);
						$emailData['message'] 			= $description;
						$emailData['reported_by'] 		= workreap_get_username($linked_profile);

						if( !empty( $type ) && $type === 'employer' ){
							$emailData['reported_employer'] 	= get_the_title($reported_id);
							$emailData['employer_link'] 		= get_edit_post_link($reported_id);
							$email_helper->send_employer_report($emailData);
						} else if( !empty( $type ) && $type === 'project' ){
							$emailData['reported_project'] 	= get_the_title($reported_id);
							$emailData['project_link'] 		= get_edit_post_link($reported_id);
							$email_helper->send_project_report($emailData);
						} else if( !empty( $type ) && $type === 'freelancer' ){
							$emailData['reported_freelancer'] 	= get_the_title($reported_id);
							$emailData['freelancer_link'] 		= get_edit_post_link($reported_id);
							$email_helper->send_freelancer_report($emailData);
						}else if( !empty( $type ) && $type === 'service' ){
							$emailData['reported_service'] 	= get_the_title($reported_id);
							$emailData['service_link'] 		= get_edit_post_link($reported_id);
							$email_helper->send_service_report($emailData);
						}
					}
				}

				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Your report has submitted', 'workreap_api');                
				return new WP_REST_Response($json, 200);
			}else {
				$json['type'] = 'error';
				$json['message'] = esc_html__('Some error occurs, please try again later', 'workreap_api');                
				return new WP_REST_Response($json, 203);
			}               
        }
		
		/**
		* Chcek Package
		*
		* @param WP_REST_Request $request Full data about the request.
		* @return WP_Error|WP_REST_Request
		*/
		
        public function check_package($request) {
			$type 			= !empty( $request['type'] ) ? esc_attr( $request['type'] ) : '';
			$user_id 		= !empty( $request['id'] ) ? intval( $request['id'] ) : '';
			$json  			= array();
			//disabled
			if( !empty( $user_id ) ) {
				if( apply_filters('workreap_is_feature_allowed', 'packages', $user_id) === false ){	
					if( $type === 'featured_service') {
						if( apply_filters('workreap_featured_service', $user_id) === false ){
							$json['type'] 		= 'error';
							$json['message'] 	= esc_html__('You’ve consumed all you points to add featured service.','workreap_api');
							$items[] 			= $json;
							return new WP_REST_Response($items, 203);
						} else {
							$json['type'] 		= 'success';
							$json['message'] 	= esc_html__('You have points to add new', 'workreap_api');                
							return new WP_REST_Response($json, 200);
						}
					}elseif( apply_filters('workreap_is_feature_job',$type, $user_id) === false){
						$json['type'] 		= 'error';
						$json['message'] 	= esc_html__('You’ve consumed all you points to add new.','workreap_api');
						$items[] 			= $json;
						return new WP_REST_Response($items, 203);
					} else{
						$json['type'] 		= 'success';
						$json['message'] 	= esc_html__('You have points to add new', 'workreap_api');                
						return new WP_REST_Response($json, 200);
					}
				}
			}
		}
		
    }
}

add_action('rest_api_init',
        function () {
    $controller = new AndroidApp_User_Route;
    $controller->register_routes();
});
