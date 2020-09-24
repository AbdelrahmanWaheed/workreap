<?php
/**
 * APP API to set profile settings
 *
 * This file will include all global settings which will be used in all over the plugin,
 * It have gatter and setter methods
 *
 * @link              https://themeforest.net/user/amentotech/portfolio
 * @since             1.0.0
 * @package           Workreap APP
 *
 */
if (!class_exists('AndroidAppProfileSettingRoutes')) {

    class AndroidAppProfileSettingRoutes extends WP_REST_Controller{

        /**
         * Register the routes for the objects of the controller.
         */
        public function register_routes() {
            $version 	= '1';
            $namespace 	= 'api/v' . $version;
            $base 		= 'profile';

            register_rest_route($namespace, '/' . $base . '/setting',
                array(
					array(
                        'methods' 	=> WP_REST_Server::READABLE,
                        'callback' 	=> array(&$this, 'get_profile'),
                        'args' 		=> array(),
                    ),
                )
            );
			register_rest_route($namespace, '/' . $base . '/update_profile',
                array(
                    array(
                        'methods' 	=> WP_REST_Server::CREATABLE,
                        'callback' 	=> array(&$this, 'update_profile_setting'),
                        'args' 		=> array(),
                    ),
                )
            );
        }

		/**
         * Update Profile Data
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function update_profile_setting($request)
        {
			$user_id	= !empty( $request['id'] ) ? intval( $request['id'] ) : '';
			$type		= !empty( $request['user_type'] ) ? esc_attr( $request['user_type'] ) : '';
			$json 		= array();
			$locations	= array();
			$location 	= array();
			
			$common_fileds_meta	= array(
									'longitude'			=> '_longitude',
									'latitude'			=> '_latitude',
									'country'			=> '_country',
									'address'			=> '_address',
									'tag_line'			=> '_tag_line'
								);
			$employers_fields	= array(
									'department'			=> '_department',
									'no_of_employees'		=> 'no_of_employees',
									);
			
			$freelancer_fields	= array(
										'_perhour_rate'			=> '_perhour_rate',
										'gender'				=> '_gender'
										);
			
			if( !empty($user_id) && !empty($type) ) {
				$profile_id 		= workreap_get_linked_profile_id($user_id);
				foreach( $common_fileds_meta as $key => $val ){
					$value		= !empty( $request[$key] ) ? $request[$key] : '';
					if( !empty($value) ) {		
						if( $key === 'country' ) {
							
							$locations = get_term_by('id', $value, 'locations' );
							update_post_meta($profile_id, '_country', $locations->slug);
							wp_set_post_terms( $profile_id, $locations->term_id, 'locations' );
							$location[0]	= $locations->term_id;
							fw_set_db_post_option($profile_id, 'country', $location );
							
						} else {
							fw_set_db_post_option($profile_id, $key,  $value  );
							update_post_meta($profile_id, $val, $value);
						}

					}
				}

				//for employers
				if( $type === 'employer' ){
					foreach( $employers_fields as $key => $val ){
						$value		= !empty( $request[$key] ) ?  $request[$key] : '';
						if( !empty($value) ) {		
							if( $val === 'department' ) {
								update_post_meta($profile_id, $val, $value);
								fw_set_db_post_option($profile_id, $key, array( '0'	=> $value ) );
							} else {
								fw_set_db_post_option($profile_id, $key,  $value  );
								update_post_meta($profile_id, $val, $value);
							}

						}
					}
				} else if( $type === 'freelancer' ){
					foreach( $freelancer_fields as $key => $val ){
						$value		= !empty( $request[$key] ) ?  $request[$key] : '';
						if( !empty($value) ) {		
							fw_set_db_post_option($profile_id, $key,  $value  );
							update_post_meta($profile_id, $val, $value);

						}
					}
				}

				//update user name

				$first_name		= !empty( $request['first_name'] ) ? esc_attr( $request['first_name'] ) : '';
				$last_name		= !empty( $request['last_name'] ) ? esc_attr( $request['last_name'] ) : '';

				if( !empty($first_name) && !empty($last_name) ) {
					update_user_meta($user_id, 'first_name', $first_name);
					update_user_meta($user_id, 'last_name', $last_name);
					$update_post	= array (
										'ID'			=> $profile_id,
										'post_title'	=> $first_name.' '.$last_name
									);
					wp_update_post($update_post);
				}
				$json['type']   	= 'success';
				$json['message']    = esc_html__('Profile is updated successful','workreap_api'); 
				return new WP_REST_Response($json, 200);
				
			} else {
				
				$json['type']   	= 'error';
				$json['message']    = esc_html__('Required fields are missing','workreap_api'); 
				return new WP_REST_Response($json, 203);
			}

		}
		
        /**
         * Get Profile Data
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function get_profile($request)
        {
			$user_id			= !empty( $request['id'] ) ? intval( $request['id'] ) : '';
			//$profile_id			= !empty( $request['profile_id'] ) ? intval( $request['profile_id'] ) : '';
			$json				= array();
			$items				= array();
			$location			= array();
            if ( !empty($user_id) ) {
				$profile_id				= workreap_get_linked_profile_id( $user_id );
				if( !empty($profile_id) ) {
					$first_name				= get_user_meta($user_id, 'first_name', true);
					$item['first_name']		= !empty( $first_name ) ? esc_attr( $first_name ) : '';
					$last_name 				= get_user_meta($user_id, 'last_name', true);
					$item['last_name']		= !empty( $last_name ) ? esc_attr( $last_name ) : '';

					//$post_object 			= get_post( $profile_id );
					$tag_line 	 			= workreap_get_tagline($profile_id);
					$item['tag_line'] 		= !empty( $tag_line ) ? $tag_line : '';
					
					$user_type				= apply_filters('workreap_get_user_type', $user_id );
					if (function_exists('fw_get_db_post_option')) {	
						//common
						$address     	 			= fw_get_db_post_option($profile_id, 'address', true);	
						$item['address']			= !empty( $address ) ? esc_attr( $address ) : '';
						$latitude		     	 	= fw_get_db_post_option($profile_id, 'latitude', true);	
						$item['latitude']			= !empty( $latitude ) ? esc_attr( $latitude ) : '';
						$longitude		     	 	= fw_get_db_post_option($profile_id, 'longitude', true);	
						$item['longitude']			= !empty( $longitude ) ? esc_attr( $longitude ) : '';
						
						$countries		 			= fw_get_db_post_option($profile_id, 'country', true);
						$countries					= !empty( $countries[0] ) ? intval( $countries[0] ) : '';
						if( !empty($countries) ) {
							$locations 					= get_term_by('id', $countries, 'locations');
							if(!empty($locations) ) {
								$item['location']			= $locations->name;
							} else {
								$item['location']			= '';
							}
						} else {
							$item['location']			= '';
						}
						
						//for employer only
						if( $user_type === 'employer') {
							$department     	= fw_get_db_post_option($profile_id, 'department', true);
							$no_of_employees    = fw_get_db_post_option($profile_id, 'no_of_employees', true);
							if( !empty($no_of_employees) ){
								$item['no_of_employees']	= $no_of_employees;
							} else {
								$item['no_of_employees']	= '';
							}

							if( !empty($department[0])) {
								$department 	= get_term_by('id', $department[0], 'department');

								if(!empty($department)) {
									$item['department']	= $department->name;
								} else {
									$item['department']	= '';
								}
							}else {
								$item['department']	= '';
							}
							$item['per_hour_rate']	= '';
							$item['gender']     	= '';

						} elseif( $user_type === 'freelancer' ) {
							$item['per_hour_rate']     	= fw_get_db_post_option($profile_id, '_perhour_rate', true);	
							$item['gender']     	 	= fw_get_db_post_option($profile_id, 'gender', true);
							$item['department']			= '';
							$item['no_of_employees']	= '';
						}
					} else {
						$item['address']     	 	= '';	
						$item['latitude']     	 	= '';	
						$item['longitude']     	 	= '';	
						$item['location'] 			= array_values($location);	
						//for employer only
						if( $user_type === 'freelancer' ) {
							$item['per_hour_rate']     	= '';	
							$item['gender']     		= '';
						}
					}

					$item['type']		= 'success';
					$item['message']	= esc_html__('profile Settings.','workreap_api');
					$items				= maybe_unserialize($item);
					return new WP_REST_Response($items, 200); 
				}else {
					$json['type']   	= 'error';
					$json['message']    = esc_html__('Invalid user ID.','workreap_api'); 
					return new WP_REST_Response($json, 203);
				}
            } else {
                $json['type']   	= 'error';
                $json['message']    = esc_html__('User id is required','workreap_api'); 
				return new WP_REST_Response($json, 203);
            }
        }
    }
}

add_action('rest_api_init',
function () {
    $controller = new AndroidAppProfileSettingRoutes;
    $controller->register_routes();
});
