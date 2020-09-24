<?php
if (!class_exists('AndroidAppGetJobsRoutes')) {

    class AndroidAppGetJobsRoutes extends WP_REST_Controller{

        /**
         * Register the routes for the objects of the controller.
         */
        public function register_routes() {
            $version 	= '1';
            $namespace 	= 'api/v' . $version;
            $base 		= 'listing';

            register_rest_route($namespace, '/' . $base . '/get_jobs',
                array(
                  array(
                        'methods' 	=> WP_REST_Server::READABLE,
                        'callback' 	=> array(&$this, 'get_listing'),
                        'args' 		=> array(),
                    ),
                )
            );
			register_rest_route($namespace, '/' . $base . '/add_jobs',
                array(
                  array(
                        'methods' 	=> WP_REST_Server::CREATABLE,
                        'callback' 	=> array(&$this, 'add_job'),
                        'args' 		=> array(),
                    ),
                )
            );
        }
		
		/**
         * Get Listings
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
		public function add_job($request) {
			$user_id			= !empty( $request['user_id'] ) ? intval( $request['user_id'] ) : '';
			
			$json				= array();
			$items				= array();
			$job_files			= array();
			$submitted_files	= array();
			
			//disabled
			if( empty( $user_id ) ) {
				if( apply_filters('workreap_is_feature_allowed', 'packages', $user_id) === false ){	
					if( apply_filters('workreap_is_feature_job','wt_jobs', $user_id) === false){
						$json['type'] 		= 'error';
						$json['message'] 	= esc_html__('You’ve consumed all you points to add new job.','workreap_api');
						$items[] 			= $json;
						return new WP_REST_Response($items, 203);
					}
				}
			}
			
			$required = array(
							'title'   			=> esc_html__('Job title is require', 'workreap_api'),
							'project_level'  	=> esc_html__('Project level is necessary', 'workreap_api'),
							'freelancer_level'  => esc_html__('Freelancer level is required', 'workreap_api'),
							'project_duration'  => esc_html__('Project duration is required', 'workreap_api'),
							'english_level'   	=> esc_html__('English level is requires', 'workreap_api'),
							'project_type' 		=> esc_html__('Please select job type.', 'workreap_api'),
							'address'   		=> esc_html__('Address is required', 'workreap_api'),
							'latitude'  		=> esc_html__('Latitude is required', 'workreap_api'),
							'longitude' 		=> esc_html__('Longitude is required', 'workreap_api'),
							'country'  			=> esc_html__('Country is required', 'workreap_api'),
						);

			foreach ($required as $key => $value) {
				if( empty( $request[$key] ) ){

					if( $key === 'project_type' && $request['project_type'] === 'hourly' && empty( $request['hourly_rate'] )  ){
						$json['type'] 		= 'error';
						$json['message'] 	= esc_html__('Per hour rate is required', 'workreap_api');        
						$items[] 			= $json;
						return new WP_REST_Response($items, 203);
					} else if( $key === 'project_type' && $request['project_type'] === 'hourly' && empty( $request['estimated_hours'] )  ){
						$json['type'] 		= 'error';
						$json['message'] 	= esc_html__('Estimated hours is required', 'workreap_api');        
						$items[] 			= $json;
						return new WP_REST_Response($items, 203);
					}else if( $key === 'project_type' && $request['project_type'] === 'fixed' && empty( $request['project_cost'] )  ){
						$json['type'] 		= 'error';
						$json['message'] 	= esc_html__('Please add project cost', 'workreap_api');        
						$items[] 			= $json;
						return new WP_REST_Response($items, 203);
					}

					$json['type'] 		= 'error';
					$json['message'] 	= $value;        
					$items[] 			= $json;
					return new WP_REST_Response($items, 203);
				} 
			}
			
			$title				= !empty( $request['title'] ) ? esc_attr( $request['title'] ) : '';
			$description		= !empty( $request['description'] ) ? $request['description'] : '';
			$project_level		= !empty( $request['project_level'] ) ? $request['project_level'] : '';
			$project_duration	= !empty( $request['project_duration'] ) ? $request['project_duration'] : '';
			$english_level		= !empty( $request['english_level'] ) ? $request['english_level'] : '';
			$project_type		= !empty( $request['project_type'] ) ? $request['project_type'] : '';
			$freelancer_level	= !empty( $request['freelancer_level'] ) ? $request['freelancer_level'] : '';
			$hourly_rate		= !empty( $request['hourly_rate'] ) ? $request['hourly_rate'] : '';
			$project_cost		= !empty( $request['project_cost'] ) ? $request['project_cost'] : '';
			$expiry_date        = !empty( $request['expiry_date'] ) ? $request['expiry_date'] : '';
			$total_attachments 	= !empty($request['size']) ? $request['size'] : 0;
			
			$user_post = array(
				'post_title'    => wp_strip_all_tags( $title ),
				'post_status'   => 'publish',
				'post_content'  => $description,
				'post_author'   => $user_id,
				'post_type'     => 'projects',
			);

			$post_id    		= wp_insert_post( $user_post );
			if( !empty( $post_id ) ) {
				$expiry_string		= workreap_get_subscription_metadata( 'subscription_featured_string',$user_id );
				if( !empty($expiry_string) ) {
					update_post_meta($post_id, '_expiry_string', $expiry_string);
				}
				$languages               = !empty( $request['languages'] ) ? $request['languages'] : array();
				$categories              = !empty( $request['categories'] ) ? $request['categories'] : array();
				$skills              	 = !empty( $request['skills'] ) ? $request['skills'] : array();

				$is_featured              = !empty( $request['is_featured'] ) ? $request['is_featured'] : '';
				if( !empty($is_featured) ){
					if( $is_featured === 'on'){
						$featured_jobs	= workreap_featured_job( $user_id );
						if( $featured_jobs ) {
							$featured_string	= workreap_is_feature_value( 'subscription_featured_string', $user_id );
							update_post_meta($post_id, '_featured_job_string', 1);
						}
					} else {
						update_post_meta( $post_id, '_featured_job_string',0 );
					}
				}

				if( !empty( $languages ) ){
					wp_set_post_terms( $post_id, $languages, 'languages' );
				}

				if( !empty( $categories ) ){
					wp_set_post_terms( $post_id, $categories, 'project_cat' );
				}

				if( !empty( $skills ) ){
					wp_set_post_terms( $post_id, $skills, 'skills' );
				}
				
				if( !empty( $_FILES ) && $total_attachments != 0 ){
					if ( ! function_exists( 'wp_handle_upload' ) ) {
						require_once( ABSPATH . 'wp-admin/includes/file.php' );
						require_once(ABSPATH . 'wp-admin/includes/image.php');
						require_once( ABSPATH . 'wp-includes/pluggable.php' );
					}
					$counter	= 0;
					for ($x = 0; $x < $total_attachments; $x++) {
						$submitted_files = $_FILES['proposal_files'.$x];
						$uploaded_image  = wp_handle_upload($submitted_files, array('test_form' => false));
						$file_name		 = basename($submitted_files['name']);
						$file_type 		 = wp_check_filetype($uploaded_image['file']);

						// Prepare an array of post data for the attachment.
						$attachment_details = array(
							'guid' => $uploaded_image['url'],
							'post_mime_type' => $file_type['type'],
							'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_name)),
							'post_content' => '',
							'post_status' => 'inherit'
						);

						$attach_id 		= wp_insert_attachment($attachment_details, $uploaded_image['file']);
						$attach_data 	= wp_generate_attachment_metadata($attach_id, $uploaded_image['file']);
						wp_update_attachment_metadata($attach_id, $attach_data);
						
						$attachments['attachment_id']	= $attach_id;
						$attachments['url']				= wp_get_attachment_url($attach_id);
			
						$job_files[]					= $attachments;
					}
				}
				
				//update
				update_post_meta($post_id, '_project_level', $project_level);
				update_post_meta($post_id, '_project_type', $project_type);
				update_post_meta($post_id, '_project_duration', $project_duration);
				update_post_meta($post_id, '_english_level', $english_level);
				update_post_meta($post_id, '_freelancer_level', $freelancer_level);
				
				$project_data							= array(); 
				$project_data['gadget']					= !empty( $request['project_type'] ) ? $request['project_type'] : 'fixed';
				
				if( !empty( $project_type ) && $project_type === 'fixed' ) {
					update_post_meta($post_id, '_project_cost', $project_cost);
					$project_data['fixed']['project_cost']		= !empty( $request['project_cost'] ) ? $request['project_cost'] : '';
				} elseif( !empty( $project_type ) && $project_type === 'hourly' ) {
					update_post_meta($post_id, '_hourly_rate', $hourly_rate);
					$project_data['hourly']['hourly_rate']		= !empty( $request['hourly_rate'] ) ? $request['hourly_rate'] : '';
					$project_data['hourly']['estimated_hours']	= !empty( $request['estimated_hours'] ) ? $request['estimated_hours'] : '';
					update_post_meta($post_id, '_estimated_hours', $project_data['hourly']['estimated_hours']);
				}

				//update location
				$address    = !empty( $request['address'] ) ? $request['address'] : '';
				$country    = !empty( $request['country'] ) ? $request['country'] : '';
				$latitude   = !empty( $request['latitude'] ) ? $request['latitude'] : '';
				$longitude  = !empty( $request['longitude'] ) ? $request['longitude'] : '';
				
				$show_attachments  = !empty( $request['show_attachments'] ) ? $request['show_attachments'] : 'off';

				update_post_meta($post_id, '_address', $address);
				update_post_meta($post_id, '_country', $country);
				update_post_meta($post_id, '_latitude', $latitude);
				update_post_meta($post_id, '_longitude', $longitude);


				//Set country for unyson
				$locations = get_term_by( 'slug', $country, 'locations' );
				$location = array();
				if( !empty( $locations ) ){
					$location[0] = $locations->term_id;

					if( !empty( $location ) ){
						wp_set_post_terms( $post_id, $location, 'locations' );
					}

				}

				//update unyson meta
				$fw_options = array();
				$fw_options['project_level']         = $project_level;
				$fw_options['project_type']          = $project_data;
				$fw_options['project_duration']      = $project_duration;
				$fw_options['english_level']         = $english_level;
				$fw_options['freelancer_level']      = $freelancer_level;
				$fw_options['show_attachments']      = $show_attachments;
				$fw_options['expiry_date']         	 = $expiry_date;
				$fw_options['project_documents']     = $job_files;
				$fw_options['address']            	 = $address;
				$fw_options['longitude']          	 = $longitude;
				$fw_options['latitude']           	 = $latitude;
				$fw_options['country']            	 = $location;


				//Update User Profile
				fw_set_db_post_option($post_id, null, $fw_options);
				
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Your job have been posted successfully.','workreap_api');
				$items[] 			= $json;
				return new WP_REST_Response($items, 200);
			}
			
		}
        /**
         * Get Listings
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function get_listing($request){
			
			$limit			= !empty( $request['show_users'] ) ? intval( $request['show_users'] ) : 10;
			$job_id			= !empty( $request['job_id'] ) ? intval( $request['job_id'] ) : '';
			$author_id		= !empty( $request['company_id'] ) ? intval( $request['company_id'] ) : '';
			$profile_id		= !empty( $request['profile_id'] ) ? intval( $request['profile_id'] ) : '';
			$page_number	= !empty( $request['page_number'] ) ? intval( $request['page_number'] ) : 1;
			$listing_type	= !empty( $request['listing_type'] ) ? esc_attr( $request['listing_type'] ) : '';
			
			
			$offset 		= ($page_number - 1) * $limit;
			
			$json			= array();
			$items			= array();
			$today 			= time();
			
			if( !empty($profile_id) ) {
				$saved_projects	= get_post_meta($profile_id,'_saved_projects',true);
			}else {
				$saved_projects	= array();
			}
			
			$defult			= get_template_directory_uri().'/images/featured.png';
			
			$json['type']		= 'error';
			$json['message']	= esc_html__('Some error occur, please try again later','workreap_api');
			if( $request['listing_type'] === 'single' ){
				
				$query_args = array(
					'posts_per_page' 	  	=> 1,
					'post_type' 	 	  	=> 'projects',
					'post__in' 		 	  	=> array($job_id),
					'post_status' 	 	  	=> 'publish',
					'ignore_sticky_posts' 	=> 1
				);
				$query 			= new WP_Query($query_args);
				$count_post 	= $query->found_posts;
			}else if( !empty($listing_type) && $listing_type === 'featured' ){
				$order		 = 'DESC';
				$query_args = array(
					'posts_per_page' 	  => $limit,
					'post_type' 	 	  => 'projects',
					'paged' 		 	  => $page_number,
					'post_status' 	 	  => 'publish',
					'ignore_sticky_posts' => 1
				);
				//order by pro member
				$query_args['meta_key'] = '_featured_job_string';
				$query_args['orderby']	 = array( 
					'ID'      		=> 'DESC',
					'meta_value' 	=> 'DESC', 
				); 

				//Meta Query
				if (!empty($meta_query_args)) {
					$query_relation = array('relation' => 'AND',);
					$meta_query_args = array_merge($query_relation, $meta_query_args);
					$query_args['meta_query'] = $meta_query_args;
				}
				$query 			= new WP_Query($query_args);
				$count_post 	= $query->found_posts;
				

			} elseif( !empty($listing_type) && $listing_type === 'single' ){
				$post_id		= !empty( $request['job_id'] ) ? $request['job_id'] : '';
				$query_args = array(
					'post_type' 	 	  	=> 'any',
					'p'						=> $post_id
				);
				$query 			= new WP_Query($query_args);
				$count_post 	= $query->found_posts;
				
			} elseif( !empty($listing_type) && !empty($author_id) && $listing_type === 'company'  ){
				$order		 	= 'DESC';
				$query_args 	= array(
									'posts_per_page' 	=> -1,
									'post_type' 	 	=> 'projects',
									'post_status' 	 	=> array('publish','pending'),
									'author' 			=> $author_id,
									'suppress_filters' 	=> false
								);
				$query 			= new WP_Query($query_args);
				$count_post 	= $query->found_posts;
			}elseif( !empty($listing_type) && $listing_type === 'latest' ){
				$order		 	= 'DESC';
				$query_args 	= array(
									'posts_per_page' 	  	=> $limit,
									'post_type' 	 	  	=> 'projects',
									'paged' 		 	  	=> $page_number,
									'post_status' 	 	  	=> 'publish',
									'order'					=> 'ID',
									'orderby'				=> $order,
								);
				$query 			= new WP_Query($query_args);
				$count_post 	= $query->found_posts;
				
			} elseif( !empty($listing_type) && $listing_type === 'favorite' ){
				$user_id			= !empty( $request['user_id'] ) ? intval( $request['user_id'] ) : '';
				$linked_profile   	= workreap_get_linked_profile_id($user_id);
				$wishlist 			= get_post_meta($linked_profile, '_saved_projects',true);
				$wishlist			= !empty($wishlist) ? $wishlist : array();
				if( !empty($wishlist) ) {
					$order		 = 'DESC';
					$query_args = array(
						'posts_per_page' 	  	=> $limit,
						'post_type' 	 	  	=> 'projects',
						'post__in'				=> $wishlist,
						'paged' 		 	  	=> $page_number,
						'post_status' 	 	  	=> 'publish',
						'order'					=> 'ID',
						'orderby'				=> $order,
						'ignore_sticky_posts' 	=> 1
					);
					$query 			= new WP_Query($query_args);
					$count_post 	= $query->found_posts;
				} else {
					$json['type']		= 'error';
					$json['message']	= esc_html__('You have no project in your favorite list.','workreap_api');
					$items[] 			= $json;
					return new WP_REST_Response($items, 203);
				}
				
			}elseif( !empty($listing_type) && $listing_type === 'search' ){
				//Search parameters
				$keyword 		= !empty( $request['keyword']) ? $request['keyword'] : '';
				$languages 		= !empty( $request['language']) ? array($request['language']) : array();
				$categories 	= !empty( $request['category']) ? array($request['category']) : array();
				$locations 	 	= !empty( $request['location']) ? array($request['location']) : array();
				$skills			= !empty( $request['skills']) ? array($request['skills']) : array();
				$duration 		= !empty( $request['duration'] ) ? $request['duration'] : '';
				$type 			= !empty( $request['type'] ) ? array($request['type']) : array();
				$project_type	= !empty( $request['project_type'] ) ? $request['project_type'] : '';
				$english_level  = !empty( $request['english_level'] ) ? array($request['english_level']) : array();

				$minprice 		= !empty($request['minprice']) ? intval($request['minprice'] ): 0;
				$maxprice 		= !empty($request['maxprice']) ? intval($request['maxprice']) : '';
				
				$tax_query_args  = array();
				$meta_query_args = array();

				//Languages
				if ( !empty($languages[0]) && is_array($languages) ) {   
					$query_relation = array('relation' => 'OR',);
					$lang_args  	= array();

					foreach( $languages as $key => $lang ){
						$lang_args[] = array(
								'taxonomy' => 'languages',
								'field'    => 'slug',
								'terms'    => $lang,
							);
					}

					$tax_query_args[] = array_merge($query_relation, $lang_args);   
				}
				
				if ( !empty($categories) ) {    
					$query_relation = array('relation' => 'OR',);
					$category_args 	= array(
						'taxonomy' => 'project_cat',
						'field'    => 'slug',
						'terms'    => $categories,
					);

					$tax_query_args[] = array_merge($query_relation, $category_args);
				}

				//Locations
				if ( !empty($locations[0]) && is_array($locations) ) {    
					$query_relation = array('relation' => 'OR',);
					$location_args  = array();

					foreach( $locations as $key => $loc ){
						$location_args[] = array(
								'taxonomy' => 'locations',
								'field'    => 'slug',
								'terms'    => $loc,
							);
					}

					$tax_query_args[] = array_merge($query_relation, $location_args);
				}

				//skills
				if ( !empty($skills[0]) && is_array($skills) ) {    
					$query_relation = array('relation' => 'OR',);
					$skills_args  = array();

					foreach( $skills as $key => $skill ){
						$skills_args[] = array(
								'taxonomy' => 'skills',
								'field'    => 'slug',
								'terms'    => $skill,
							);
					}
				}

				//Freelancer Skill Level
				if ( !empty( $type ) ) {    
					$meta_query_args[] = array(
						'key' 		=> '_freelancer_level',
						'value' 	=> $type,
						'compare' 	=> 'IN'
					);    
				}

				//Duration
				if ( !empty( $duration ) ) {    
					$duration_args[] = array(
						'key'		=> '_project_duration',
						'value' 	=> $duration,
						'compare' 	=> 'IN'
					);    

					$meta_query_args = array_merge($meta_query_args, $duration_args);
				}

				//Project Type
				if ( !empty( $project_type ) && $project_type === 'hourly' || $project_type === 'fixed' ) {    
					$project_args[] = array(
						'key' 			=> '_project_type',
						'value' 		=> $project_type,
						'compare' 		=> '='
					);  

					$meta_query_args = array_merge($meta_query_args, $project_args);
				}
				
				//Hourly Rate
				if( !empty( $project_type ) &&  $project_type === 'hourly' && !empty( $maxprice ) ) {
					$range_array 		= array($minprice, $maxprice);
					if( !empty( $range_array ) ) {
						$meta_query_args[] = array(
							'key'     => '_hourly_rate',
							'value'   => $range_array,
							'type'    => 'NUMERIC',
							'compare' => 'BETWEEN',
						);  
					}
				} else if( !empty( $project_type ) &&  $project_type === 'fixed' && !empty( $maxprice ) ) {
						$price_range 		= array($minprice, $maxprice);
						$meta_query_args[]  = array(
							'key' 			=> '_project_cost',
							'value' 		=> $price_range,
							'type'    		=> 'NUMERIC',
							'compare' 		=> 'BETWEEN'
						);
				} else if( empty( $project_type ) && !empty( $maxprice ) ) {
					$price_range 		= array($minprice, $maxprice);
					$query_relation = array('relation' => 'OR',);
					$price_args = array();
					$price_args[]  = array(
						'key' 			=> '_project_cost',
						'value' 		=> $price_range,
						'type'    		=> 'NUMERIC',
						'compare' 		=> 'BETWEEN'
					);

					$price_args[] = array(
							'key'     => '_hourly_rate',
							'value'   => $price_range,
							'type'    => 'NUMERIC',
							'compare' => 'BETWEEN',
						); 

					$meta_query_args[] = array_merge($query_relation, $price_args);
				}

				/*if( !empty( $project_type ) &&  $project_type === 'hourly' ) {
					$hourly_rate = !empty( $request['hourlyrate'] ) ? $request['hourlyrate'] : '';
					$range_array = !empty($hourly_rate) ? str_replace('$', '', explode('-', $hourly_rate)) : array();
					if( !empty( $range_array ) ) {
						$hourlyrate_args[] = array(
							'key'     => '_perhour_rate',
							'value'   => $range_array,
							'compare' => 'BETWEEN',
						);  

						$meta_query_args = array_merge($meta_query_args, $hourlyrate_args);
					}

				}*/

				//Main Query
				$query_args = array(
					'posts_per_page' 	  => $limit,
					'post_type' 	 	  => 'projects',
					'paged' 		 	  => $page_number,
					'post_status' 	 	  => array('publish'),
					'ignore_sticky_posts' => 1
				);

				//keyword search
				if( !empty($keyword) ){
					$query_args['s']	=  $keyword;
				}

				//order by pro member
				$query_args['meta_key'] = '_featured_job_string';
				$query_args['orderby']	 = array( 
					'ID'      		=> 'DESC',
					'meta_value' 	=> 'DESC', 
				); 

				//Taxonomy Query
				if ( !empty( $tax_query_args ) ) {
					$query_relation = array('relation' => 'AND',);
					$query_args['tax_query'] = array_merge($query_relation, $tax_query_args);    
				}

				//Meta Query
				if (!empty($meta_query_args)) {
					$query_relation 			= array('relation' => 'AND',);
					$meta_query_args 			= array_merge($query_relation, $meta_query_args);
					$query_args['meta_query'] 	= $meta_query_args;
				}
				//print_r($query_args);die();
				$query 			= new WP_Query($query_args); 
				$count_post 	= $query->found_posts;		
				
			} else {
				if(!empty($count_post) && $count_post ) {
					$json['type']		= 'error';
					$json['message']	= esc_html__('Please provide api type','workreap_api');
					return new WP_REST_Response($json, 203);
				} else {
					$json['type']		= 'error';
					$json['message']	= esc_html__('Please provide api type','workreap_api');
					return new WP_REST_Response($json, 203);
				}
			}
			
			//Start Query working.
			
			if ($query->have_posts()) {
				$duration_list 			= worktic_job_duration_list();
				if (function_exists('fw_get_db_settings_option')) {
					$featured_image		= fw_get_db_settings_option('featured_job_img');
					$featured_bg_color	= fw_get_db_settings_option('featured_job_bg');
					$tag		  		= !empty( $featured_image['url'] ) ? $featured_image['url'] : $defult;
					$color		  		= !empty( $featured_bg_color ) ? $featured_bg_color : '#f1c40f';
				} else {
					$color	= '';
					$tag	= '';
				}
				
				while ($query->have_posts()) { 
					$query->the_post();
					global $post;
					$project_id						= $post->ID;
					
					if( !empty($saved_projects)  &&  in_array($project_id,$saved_projects)) {
						$item['favorit']			= 'yes';
					} else {
						$item['favorit']			= '';
					}
					
					//Featured Jobs
					$featured_job	= get_post_meta($project_id,'_featured_job_string',true);
					if( !empty($featured_job) && !empty($color) && !empty($tag) ) {
						$item['featured_url']		= workreap_add_http($tag);
						$item['featured_color']		= $color;
					} else {
						$item['featured_url']		= '';
						$item['featured_color']		= '';
					}
					$item['location']		= workreap_get_location($project_id);
					$author_id				= get_the_author_meta( 'ID' );  
					$linked_profile			= workreap_get_linked_profile_id($author_id);
					
					$item['job_id']			= $project_id;
					$item['job_link']		= get_the_permalink($project_id);
					
					$is_verified			= get_post_meta($linked_profile,'_is_verified',true);
					$item['_is_verified'] 	= !empty($is_verified) ? $is_verified : '';
					
					$item['project_level']		= apply_filters('workreap_filter_project_level',$project_id);
					
					if (function_exists('fw_get_db_post_option')) {
						$project_type 		= fw_get_db_post_option($project_id, 'project_type', true);
						$project_duration   = fw_get_db_post_option($project_id, 'project_duration', true);
						$project_documents  = fw_get_db_post_option($project_id, 'project_documents', true);
						$db_project_type 	= fw_get_db_post_option($project_id, 'project_type', true);
						$expiry_date 		= fw_get_db_post_option($project_id, 'expiry_date', true);
						
						$project_cost 		= !empty( $db_project_type['fixed']['project_cost'] ) ? $db_project_type['fixed']['project_cost'] : '';
						$hourly_rate 		= !empty( $db_project_type['hourly']['hourly_rate'] ) ? $db_project_type['hourly']['hourly_rate'] : '';
						$estimated_hours	= !empty( $db_project_type['hourly']['estimated_hours'] ) ? $db_project_type['hourly']['estimated_hours'] : '';
						
					}
					
            		$item['project_type']   	= !empty( $project_type['gadget'] ) ? ucfirst($project_type['gadget']) : '';
            		$item['project_duration']	= !empty( $project_duration ) ? $duration_list[$project_duration] : '';
					$item['project_cost']		= !empty( $project_cost ) ? apply_filters('workreap_price_format',$project_cost,'return') : '';
					$item['hourly_rate']		= !empty( $hourly_rate ) ? apply_filters('workreap_price_format',$hourly_rate,'return') : '';
					$item['estimated_hours']	= !empty( $estimated_hours ) ? $estimated_hours : '';
					$item['expiry_date']		= !empty( $expiry_date ) ? $expiry_date : '';
					
					$docs						= array();
					if( !empty( $project_documents ) ){ 
						$docs_count	= 0;
						foreach ( $project_documents as $key => $value ) {
							$docs_count ++;
							$docs[$docs_count]['document_name']   	= !empty( get_the_title( $value['attachment_id'] ) ) ? get_the_title( $value['attachment_id'] ) : '';
							$docs[$docs_count]['file_size']			= !empty(filesize( get_attached_file( $value['attachment_id'] ) )) ? size_format(filesize( get_attached_file( $value['attachment_id']) ),2) : '';
							$docs[$docs_count]['filetype']        	= wp_check_filetype( $value['url'] );
							$docs[$docs_count]['extension']       	= !empty( $filetype['ext'] ) ? $filetype['ext'] : '';
							$docs[$docs_count]['url']				= workreap_add_http($value['url']);
						}
					}
					$item['attanchents']	= array_values($docs);
					
					$terms 					= wp_get_post_terms( $project_id, 'skills');
					$skills					= array();
					if( !empty( $terms ) ){
						$sk_count	= 0;
						foreach ( $terms as $key => $term ) {
							$sk_count ++;
							$term_link 							= get_term_link( $term->term_id, 'skills' );	
							$skills[$sk_count]['skill_link']	= $term_link;
							$skills[$sk_count]['skill_name']	= $term->name;
						}
					}
					$item['skills']				= array_values($skills);
					
					$item['employer_name']		= get_the_title( $linked_profile );
					$item['project_title']		= get_the_title( $project_id );
					$item['project_content']	= get_the_content( $project_id );
					$item['count_totals']       = !empty($count_post) ? intval($count_post) : 0;
					$items[]				    = maybe_unserialize($item);					
				}
				return new WP_REST_Response($items, 200);
				//end query
				
			}else{
				$json['type']		= 'error';
				$json['message']	= esc_html__('Some error occur, please try again later','workreap_api');
				$items[] = $json;
				return new WP_REST_Response($items, 203);
			} 
        }

    }
}

add_action('rest_api_init',
function () {
	$controller = new AndroidAppGetJobsRoutes;
	$controller->register_routes();
});
