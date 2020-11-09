<?php
if (!class_exists('AndroidAppGetEmployersDashbord')) {

    class AndroidAppGetEmployersDashbord extends WP_REST_Controller{

        /**
         * Register the routes for the objects of the controller.
         */
        public function register_routes() {
            $version 	= '1';
            $namespace 	= 'api/v' . $version;
            $base 		= 'dashboard';

			//manage employers
			register_rest_route($namespace, '/' . $base . '/manage_employer_jobs',
                array(
                  array(
                        'methods' => WP_REST_Server::READABLE,
                        'callback' => array(&$this, 'manage_employer_jobs'),
                        'args' => array(),
						'permission_callback' => '__return_true',
                    ),
                )
			);

			//manage proposals
			register_rest_route($namespace, '/' . $base . '/manage_job_proposals',
                array(
                  array(
                        'methods' => WP_REST_Server::READABLE,
                        'callback' => array(&$this, 'manage_job_proposals'),
                        'args' => array(),
						'permission_callback' => '__return_true',
                    ),
                )
				);

			//manage ongoing jobs
			register_rest_route($namespace, '/' . $base . '/manage_employer_ongoing_jobs',
                array(
                  array(
                        'methods' => WP_REST_Server::READABLE,
                        'callback' => array(&$this, 'manage_employer_ongoing_jobs'),
                        'args' => array(),
						'permission_callback' => '__return_true',
                    ),
                )
			);
			

			register_rest_route($namespace, '/' . $base . '/get_employer_services',
				array(
				array(
						'methods' 	=> WP_REST_Server::READABLE,
						'callback' 	=> array(&$this, 'get_employer_services'),
						'args' 		=> array(),
						'permission_callback' => '__return_true',
					),
				)
			);
			
			register_rest_route($namespace, '/' . $base . '/get_services_feedbacks',
				array(
				array(
						'methods' 	=> WP_REST_Server::READABLE,
						'callback' 	=> array(&$this, 'get_services_feedbacks'),
						'args' 		=> array(),
						'permission_callback' => '__return_true',
					),
				)
			);
			register_rest_route($namespace, '/' . $base . '/complete_services',
                array(
                  array(
                        'methods' 	=> WP_REST_Server::CREATABLE,
                        'callback' 	=> array(&$this, 'complete_services'),
                        'args' 		=> array(),
						'permission_callback' => '__return_true',
                    ),
                )
			);
			register_rest_route($namespace, '/' . $base . '/cancelled_services',
                array(
                  array(
                        'methods' 	=> WP_REST_Server::CREATABLE,
                        'callback' 	=> array(&$this, 'cancelled_services'),
                        'args' 		=> array(),
						'permission_callback' => '__return_true',
                    ),
                )
            );
        }

		/**
         * Cancelled Service
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function cancelled_services($request){
			global $wpdb;
			$json 				= array();
			$service_order_id	=  !empty( $request['service_order_id '] ) ? intval($request['service_order_id ']) : '';
			$cancelled_reason	=  !empty( $request['cancelled_reason'] ) ? $request['cancelled_reason'] : '';
			$user_id 			= !empty( $request['user_id'] ) ? ($request['user_id']) : '';
			if( empty( $service_order_id ) || empty( $cancelled_reason ) ){
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('No kiddies please', 'workreap_api');
				return new WP_REST_Response($json, 203);
			} else {
				$freelancer_id		= get_post_meta( $service_order_id, '_service_author', true);
				$service_id			= get_post_meta( $service_order_id, '_service_id', true);
				
				$service_cancelled	= workreap_save_service_status($service_order_id, 'cancelled');
				
				if( $service_cancelled ) {
					// update earnings
					if( function_exists( 'fw_set_db_post_option' ) ) {
						fw_set_db_post_option($service_order_id, 'feedback', $cancelled_reason);
					}
					
					$table_name 	= $wpdb->prefix . 'wt_earnings';
					$e_query		= $wpdb->prepare("SELECT * FROM $table_name where project_id = %d", $service_order_id);
					$earning		= $wpdb->get_row($e_query, OBJECT ); 
					if( !empty( $earning ) ) {
						$update		= array( 'status' 	=> 'cancelled' );
						$where		= array( 'id' 		=> $earning->id );
						workreap_update_earning( $where, $update, 'wt_earnings');

						if ( class_exists('WooCommerce') ) {
							$order = wc_get_order( intval( $earning->order_id ) );
							if( !empty( $order ) ) {
								$order->update_status( 'cancelled' );
							}
						}	
					}
					
					//Send email to users
					if (class_exists('Workreap_Email_helper')) {
						if (class_exists('WorkreapCancelService')) {
							$email_helper = new WorkreapCancelService();
							$emailData 	  = array();

							$service_title 			= get_the_title($service_id);
							$service_link 			= get_permalink($service_id);
							$freelance_profile_id	= workreap_get_linked_profile_id( $freelancer_id );

							$employer_name 		= workreap_get_username($user_id);
							$employer_profile 	= get_permalink(workreap_get_linked_profile_id($user_id));
							$freelancer_link 	= get_permalink($freelance_profile_id );
							$freelancer_title 	= get_the_title($freelance_profile_id );
							$freelancer_email 	= get_userdata( $freelancer_id )->user_email;


							$emailData['employer_name'] 		= esc_attr( $employer_name );
							$emailData['employer_link'] 		= esc_url( $employer_profile );
							$emailData['freelancer_name']       = esc_attr( $freelancer_title );
							$emailData['freelancer_link']       = esc_url( $freelancer_link );
							$emailData['freelancer_email']      = esc_attr( $freelancer_email );
							$emailData['service_title'] 		= esc_attr( $service_title );
							$emailData['service_link'] 			= esc_url( $service_link );
							$emailData['message'] 				= esc_html( $cancelled_reason );

							$email_helper->send_service_cancel_email($emailData);
						}
					}
					
					$json['type'] 		= 'success';
					$json['message'] 	= esc_html__('Your order have been cancelled.', 'workreap_api');
					return new WP_REST_Response($json, 200);
				} else {
					$json['type'] 		= 'error';
					$json['message'] 	= esc_html__('No kiddies please', 'workreap_api');
					return new WP_REST_Response($json, 203);
				}
			}
		}
		/**
         * Complete Service
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function complete_services($request){
			$json				= array();
			$service_order_id		= !empty( $request['service_order_id'] ) ? intval($request['service_order_id']) : '';
			$contents 				= !empty( $request['feedback_description'] ) ? esc_attr($request['feedback_description']) : '';
			$reviews 				= !empty( $request['feedback'] ) ? ($request['feedback']) : array();
			$user_id 				= !empty( $request['user_id'] ) ? ($request['user_id']) : '';
		
			if( empty( $contents ) || empty( $service_order_id ) ){
				$json['type'] 		= 'error';
				
				if( empty( $contents ) ) {
					$json['message'] 	= esc_html__('Feedback detail is required field', 'workreap');	
				} 
				
				$items[] 			= $json;
				return new WP_REST_Response($items, 203);
				
			} else {
				workreap_save_service_rating($service_order_id,$reviews,'add');
				$freelancer_id	= get_post_meta( $service_order_id, '_service_author', true);
				$service_id		= get_post_meta( $service_order_id, '_service_id', true);
				
				if( function_exists( 'fw_set_db_post_option' ) ) {
					fw_set_db_post_option($service_order_id, 'feedback', $contents);
				}
				
				workreap_save_service_status( $service_order_id,'completed' );
				
				//update earning
				$where		= array('project_id' => $service_order_id, 'user_id' => $freelancer_id);
				$update		= array('status' 	=> 'completed');
				
				workreap_update_earning( $where, $update, 'wt_earnings');

				// complete service
				$order_id			= get_post_meta($service_order_id,'_order_id',true);
				if ( class_exists('WooCommerce') && !empty( $order_id )) {
					$order = wc_get_order( intval($order_id ) );
					if( !empty( $order ) ) {
						$order->update_status( 'completed' );
					}
				}
				
				$user_ratings	= get_post_meta( $service_order_id ,'_hired_service_rating', true );
				$user_ratings	= !empty( $user_ratings ) ? $user_ratings : 0;
				
				if( function_exists( 'fw_get_db_post_option' ) ) {
					$contents	= fw_get_db_post_option($service_order_id, 'feedback');
				}
				
				$contents		= !empty( $contents ) ? $contents : '';
				
				//Send email to users
				if (class_exists('Workreap_Email_helper')) {
					if (class_exists('WorkreapServiceCompleted')) {
						$email_helper = new WorkreapServiceCompleted();
						$emailData 	  = array();
						
						$freelance_profile_id	= workreap_get_linked_profile_id( $freelancer_id );
						$service_title 			= get_the_title($service_id);
						$service_link 			= get_permalink($service_id);
						
						$employer_name 		= workreap_get_username($user_id);
						$employer_profile 	= get_permalink(workreap_get_linked_profile_id($user_id));
						$freelancer_link 	= get_permalink($freelance_profile_id );
						$freelancer_title 	= get_the_title($freelance_profile_id );
						$freelancer_email 	= get_userdata( $freelancer_id )->user_email;	

							
						$emailData['employer_name'] 		= esc_attr( $employer_name );
						$emailData['employer_link'] 		= esc_url( $employer_profile );
						$emailData['freelancer_name']       = esc_attr( $freelancer_title );
						$emailData['freelancer_link']       = esc_url( $freelancer_link );
						$emailData['freelancer_email']      = esc_attr( $freelancer_email );
						$emailData['service_title'] 		= esc_attr( $service_title );
						$emailData['ratings'] 				= esc_attr( $user_ratings );
						$emailData['service_link'] 			= esc_url( $service_link );
						$emailData['message'] 				= esc_textarea( $contents );

						$email_helper->send_service_completed_email_admin($emailData);
						$email_helper->send_service_completed_email_freelancer($emailData);
					}
				}
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Your service have been completed successfully.','workreap_api');
				$items[] 			= $json;
				return new WP_REST_Response($items, 200);
			}
		}
		/**
         * Get feedbacks
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function get_services_feedbacks($request){
			$rating_titles 	= workreap_project_ratings('services_ratings');
			$items	= array();
			if( !empty( $rating_titles ) ) {
				$item	= array();
				foreach( $rating_titles as $slug => $label ) {
					$item['slug']	= $slug;
					$item['label']	= $label;
					$items[]		= $item;
				}
			}
			$items			    = maybe_unserialize($items);
			
			return new WP_REST_Response($items, 200);
		}

		/**
         * Get Listings
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function get_employer_services($request){
			$limit			= !empty( $request['show_users'] ) ? intval( $request['show_users'] ) : 6;
			$page_number	= !empty( $request['page_number'] ) ? intval( $request['page_number'] ) : 1;
			$user_id		= !empty( $request['user_id'] ) ? intval( $request['user_id'] ) : '';
			$type			= !empty( $request['type'] ) ? ( $request['type'] ) : '';

			if( $type === 'completed' ) {
				$post_status	= array('completed');
			} else if( $type === 'hired' ){
				$post_status	= array('hired');
			} else if( $type === 'cancelled' ){
				$post_status	= array('cancelled');
			}

			$order 		= 'DESC';
			$sorting 	= 'ID';

			$args 			= array(
				'posts_per_page' 	=> $limit,
				'post_type' 		=> 'services-orders',
				'orderby' 			=> $sorting,
				'order' 			=> $order,
				'post_status' 		=> $post_status,
				'paged' 			=> $page_number,
				'author' 			=> $user_id,
				'suppress_filters' 	=> false
			);
			$query 				= new WP_Query($args);
			$items	= array();
			if( $query->have_posts() ){
				$service_data	= array();
				while ($query->have_posts()) : $query->the_post();
					global $post;
					$service_id			= get_post_meta($post->ID,'_service_id',true);
					$freelance_id		= get_post_meta ( $post->ID,'_service_author',true);
					$service_addons		= get_post_meta( $service_id, '_addons', true);
					$addon_array		= array();
					if( !empty( $service_addons ) ){
						$service_addons_array	= array();
						foreach( $service_addons as $key => $addon ) { 
							$db_price			= 0;

							if (function_exists('fw_get_db_post_option')) {
								$db_price   = fw_get_db_post_option($addon,'price');
							}
							$service_addons_array['title']	= get_the_title($addon);
							$service_addons_array['detail']	= get_the_excerpt($addon);
							$service_addons_array['price']	= workreap_price_format($db_price,'return');
							$addon_array[]					= $service_addons_array;
						}
					}

					$service_data['addons']				= $addon_array;
					$service_data['order_id']			= $post->ID;
					$profile_id							= workreap_get_linked_profile_id($freelance_id);
					$service_data['freelancer_title']		= get_the_title($profile_id);
					$service_data['freelancertagline']	= workreap_get_tagline($profile_id);
					$service_data['freelancer_avatar'] 	= apply_filters(
											'workreap_freelancer_avatar_fallback', workreap_get_freelancer_avatar(array('width' => 65, 'height' => 65), $profile_id), array('width' => 65, 'height' => 65) 
										);
					$service_data['freelancer_verified'] 	= get_post_meta($profile_id, '_is_verified', true);
					
				
					$service_data['ID']					= $service_id;
					if (function_exists('fw_get_db_post_option')) {
						$db_docs   	= fw_get_db_post_option($service_id,'docs');
					}
					$service_data['service_title']	= get_the_title($service_id);
					$service_data['featured_img']	= get_the_post_thumbnail_url($service_id,'workreap_service_thumnail');
					$is_featured					= apply_filters('workreap_service_print_featured',$service_id,'yes');
					if(!empty($is_featured) && $is_featured === 'wt-featured'){
						$is_featured = 'yes';
					} else {
						$is_featured = 'no';
					}
					$db_price		= '';

					if (function_exists('fw_get_db_post_option')) {
						$db_price   = fw_get_db_post_option($service_id,'price');
					}

					$service_data['is_featured']		= $is_featured;

					if( $type === 'completed' ) {
						$feedback	 		= '';
						if (function_exists('fw_get_db_post_option')) {
							$feedback   = fw_get_db_post_option($post->ID, 'feedback');
						}

						$service_data['feedback']	= $feedback;

						$service_ratings	= get_post_meta($post->ID,'_hired_service_rating',true);
						$service_ratings	= !empty( $service_ratings ) ? $service_ratings : 0;
						$service_data['service_ratings']	= $service_ratings;
						$rating_headings 	= workreap_project_ratings('services_ratings');
						$avg_rationg		= array();
						if( !empty($rating_headings) ){
							$rating_array		= array();
							foreach ( $rating_headings  as $key => $item ) {
								$saved_projects     = get_post_meta($post->ID, $key, true);
								if( !empty( $saved_projects ) ) {
									$percentage				= $saved_projects;
									$rating_array['title']	= $item;
									$rating_array['score']	= $percentage;
									$avg_rationg[]			= $rating_array;
								}
							}
						}
					} else if( $type === 'cancelled' ) {
						$feedback	 				= fw_get_db_post_option($post->ID, 'feedback');
						$service_data['feedback']	= !empty($feedback) ? $feedback : '';
					}
					$service_data['rating_data']		= $avg_rationg;
					$service_data['price']				= workreap_price_format($db_price,'return');
					$items[]	= $service_data;
				endwhile;
				wp_reset_postdata();

			}
			$items			    = maybe_unserialize($items);
			
			return new WP_REST_Response($items, 200);
		}
		/**
         * Manage proposals
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function manage_employer_jobs($request){
			$limit			= !empty( $request['limit'] ) ? intval( $request['limit'] ) : 10;
			$page_number	= !empty( $request['page_number'] ) ? intval( $request['page_number'] ) : 1;
			$user_id		= !empty( $request['user_id'] ) ? intval( $request['user_id'] ) : '';
			$type			= !empty( $request['type'] ) ? ( $request['type'] ) : '';
			$offset 		= ($page_number - 1) * $limit;
			
			$json		= array();
			$item		= array();
			$items		= array();
			$proposals	= array();
			
			$order 			= 'DESC';
			$sorting 		= 'ID';

			if( $type === 'cancelled' ){
				$job_status	= array('cancelled');
			} elseif( $type === 'completed' ){
				$job_status	= array('completed');
			} elseif( $type === 'hired' ){
				$job_status	= array('hired');
			} else {
				$job_status	= array('publish','pending');
			}

			
			$args 			= array(
				'posts_per_page' 	=> $limit,
				'post_type' 		=> 'projects',
				'orderby' 			=> $sorting,
				'order' 			=> $order,
				'post_status' 		=> $job_status,
				'paged' 			=> $page_number,
				'author' 			=> $user_id,
				'suppress_filters' 	=> false
			);
			$query 				= new WP_Query($args);
			if( $query->have_posts() ){
				while ($query->have_posts()) {
					$query->the_post();
					global $post;

					$item		= array();
					$author_id 		= $user_id;  
					$linked_profile = workreap_get_linked_profile_id($author_id);
					$employer_title = esc_html( get_the_title( $linked_profile ));
					$milestone_option	= 'off';

					if( !empty($milestone) && $milestone ==='enable' ){
						$milestone_option	= get_post_meta( $post->ID, '_milestone', true );
					}
					
					if( $type === 'hired' ){
						$proposal_id					= get_post_meta( $post->ID, '_proposal_id', true );
						$item['proposal_id']	    	= $proposal_id;
					} 
					
					
					$item['ID']	    	= $post->ID;
					$item['title']		= get_the_title($post->ID);
					$item['milestone_option']	   = $milestone_option;
					

					$is_verified 	= get_post_meta($linked_profile, '_is_verified', true);
					$title			= $employer_title;
					if( function_exists('workreap_get_username') ){
						$title	= workreap_get_username('',$linked_profile);
					}
				
					$item['employer_avatar'] 				= apply_filters(
						'workreap_freelancer_avatar_fallback', workreap_get_freelancer_avatar( array( 'width' => 100, 'height' => 100 ), $linked_profile ), array( 'width' => 100, 'height' => 100 )
					);
				

					$item['employer_verified']		= 'no';
					if( !empty( $is_verified ) && $is_verified === 'yes' ){
						$item['employer_verified']		= 'yes';
					}

					$item['employer_name']		= $title;

					//project level
					$project_level = '';
					if (function_exists('fw_get_db_post_option')) {
						$project_level          = fw_get_db_post_option($post->ID, 'project_level', true);                
					}

					$item['project_level']		= workreap_get_project_level($project_level);

					//Location
					$item['location_name']		= '';
					$item['location_flag']		= '';
					if( !empty( $post->ID ) ){ 
						$args = array();
						if( taxonomy_exists('locations') ) {
							$terms = wp_get_post_terms( $post->ID, 'locations', $args );
							if( !empty( $terms ) ){
								foreach ( $terms as $key => $term ) {    
									$country = fw_get_db_term_option($term->term_id, 'locations', 'image');
									$item['location_name']		= !empty($term->name) ? $term->name : '';;
									$item['location_flag']		= !empty($country['url']) ? workreap_add_http( $country['url'] ) : '';;
								}
							}
						}

					}
				
					$items[]	= $item;
					
				}
			}
				
			$items		    = maybe_unserialize($items);
			
			return new WP_REST_Response($items, 200);
			
		}

		/**
         * Manage proposals
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function manage_job_proposals($request){
			$limit			= !empty( $request['limit'] ) ? intval( $request['limit'] ) : -1;
			$page_number	= !empty( $request['page_number'] ) ? intval( $request['page_number'] ) : 1;
			$job_id			= !empty( $request['job_id'] ) ? intval( $request['job_id'] ) : '';
			$type			= !empty( $request['type'] ) ? ( $request['type'] ) : '';
			$offset 		= ($page_number - 1) * $limit;
			
			$json		= array();
			$item		= array();
			$items		= array();

			
			$query_args = array('posts_per_page' => $limit,
				'post_type' 		=> 'proposals',
				'paged' 		 	  => $page_number,
				'suppress_filters' 	=> false,
			);

			$meta_query_args[] = array(
				'key' 			=> '_project_id',
				'value' 		=> $job_id,
				'compare' 		=> '='
			);
			$query_relation = array('relation' => 'AND',);
			$query_args['meta_query'] = array_merge($query_relation, $meta_query_args);    


			$query = new WP_Query($query_args);
			if( $query->have_posts() ){
				while ($query->have_posts()) {
					$query->the_post();
					global $post;
					$proposals	= array();
					$author_id 			= get_the_author_meta( 'ID' );  
					$linked_profile 	= workreap_get_linked_profile_id($author_id);
					$proposals['freelancer_title'] 	= esc_html(get_the_title( $linked_profile ));
					$proposals['is_verified'] 		= get_post_meta($linked_profile, '_is_verified', true);

					if (function_exists('fw_get_db_post_option')) {
						$proposal_docs 	= fw_get_db_post_option($post->ID, 'proposal_docs', true);
					} else {
						$proposal_docs	= '';
					}

					$proposals['proposal_docs'] = !empty( $proposal_docs ) && is_array( $proposal_docs ) ?  count( $proposal_docs ) : 0;
					$proposals['freelancer_avatar'] = apply_filters(
							'workreap_freelancer_avatar_fallback', workreap_get_freelancer_avatar( array( 'width' => 225, 'height' => 225 ), $linked_profile ), array( 'width' => 225, 'height' => 225 )
						);

					$reviews_data 	= get_post_meta( $post->ID , 'review_data');
					$proposals['freelancer_reviews_rate']	= !empty( $reviews_data[0]['wt_average_rating'] ) ? floatval( $reviews_data[0]['wt_average_rating'] ) : 0 ;
					$proposals['freelancer_total_rating']	= !empty( $reviews_data[0]['wt_total_rating'] ) ? intval( $reviews_data[0]['wt_total_rating'] ) : 0 ;
					
					$proposals['content']					= nl2br( stripslashes( get_the_content('',true,$post->ID) ) );
					$order_id	= get_post_meta( $post->ID, '_order_id', true );
					$order_id	= !empty($order_id) ? intval($order_id) : 0;
					$order_url	= '';
				
					$items[]	= $proposals;
					
				}
			}
				
			$items		    = maybe_unserialize($items);
			
			return new WP_REST_Response($items, 200);
			
		}
		
		/**
         * Get proposal attachment
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function get_attachments($request){
			$job_id			= !empty( $request['id'] ) ? intval( $request['id'] ) : 10;

			$item		= array();
			$items		= array();
			$item['attachment'] = '';
			
			if( !empty($job_id) ){
				if (function_exists('fw_get_db_post_option')) {
					$proposal_docs 			= fw_get_db_post_option($job_id, 'proposal_docs');
					if( !empty($proposal_docs) ) {
						$zip = new ZipArchive();
						$uploadspath			= wp_upload_dir();
						$folderRalativePath 	= $uploadspath['baseurl']."/downloades";
						$folderAbsolutePath 	= $uploadspath['basedir']."/downloades";
						wp_mkdir_p($folderAbsolutePath);
						
						$filename				= round(microtime(true)).'.zip';
						$zip_name 				= $folderAbsolutePath.'/'.$filename; 
						$zip->open($zip_name,  ZipArchive::CREATE);
						$download_url			= $folderRalativePath.'/'.$filename; 

						foreach($proposal_docs as $file) {
							$response			= wp_remote_get($file['url']);
							$filedata   		= wp_remote_retrieve_body( $response );
							$zip->addFromString(basename($file['url']), $filedata);
						}
						$zip->close();
						
						
						$item['attachment'] = $download_url;
					}
				}
			}
			
			$items[]			    = maybe_unserialize($item);
			
			return new WP_REST_Response($items, 200);
			
		}
    }
}

add_action('rest_api_init',
function () {
	$controller = new AndroidAppGetEmployersDashbord;
	$controller->register_routes();
});
