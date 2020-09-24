<?php
/**
 * APP API to manage proposals
 *
 * This file will include all global settings which will be used in all over the plugin,
 * It have gatter and setter methods
 *
 * @link              https://codecanyon.net/user/amentotech/portfolio
 * @since             1.0.0
 * @package           Workreap APP
 *
 */
if (!class_exists('AndroidApp_Proposal_Route')) {

    class AndroidApp_Proposal_Route extends WP_REST_Controller{

        /**
         * Register the routes for the user.
         */
        public function register_routes() {
            $version 	= '1';
            $namespace 	= 'api/v' . $version;
            $base 		= 'proposal';
			
			//add Proposal
            register_rest_route($namespace, '/' . $base . '/add_proposal',
                array(
                    array(
                        'methods' => WP_REST_Server::CREATABLE,
                        'callback' => array(&$this, 'add_proposal'),
                        'args' => array(),
                    ),
                )
            );
        }

		 /**
         * add new proposal
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Request
         */
        public function add_proposal($request){
			
			$user_id			= !empty($request['user_id']) ? intval($request['user_id']) : '';
			$project_id			= !empty($request['project_id']) ? intval($request['project_id']) : '';

			$proposed_amount	= !empty($request['proposed_amount']) ? intval($request['proposed_amount']) : '';
			$proposed_time		= !empty($request['proposed_time']) ? esc_attr($request['proposed_time']) : '';
			$proposed_content	= !empty($request['proposed_content']) ? esc_attr($request['proposed_content']) : '';
			$total_attachments 	= !empty($request['size']) ? $request['size'] : 0;
			
			$submitted_file		= array();
			$json				= array();
			//Validation
			$validations = array(            
				'user_id'      			=> esc_html__('User ID field is required', 'workreap_api'),
				'project_id'      		=> esc_html__('Project ID field is required', 'workreap_api'),
				'proposed_amount'       => esc_html__('Amount field is required', 'workreap_api'),
				'proposed_time'         => esc_html__('Proposal time is required', 'workreap_api'),
				'proposed_content'      => esc_html__('Message field is required', 'workreap_api'),            
			);

			foreach ( $validations as $key => $value ) {
				if ( empty( $request[$key] ) ) {
					$json['type'] 		= 'error';
					$json['message'] 	= $value;
					return new WP_REST_Response($json, 203); 
				}                    
			}
			
			if( apply_filters('workreap_is_feature_allowed', 'packages', $user_id) === false ){	
				if( apply_filters('workreap_feature_connects', $user_id) === false ){
					$json['type'] 		= 'error';
					$json['message'] 	= esc_html__('You’ve consumed all you points to apply new job.','workreap_api');
					return new WP_REST_Response($json, 203); 
				}
			}

			$proposals_sent = intval(0);
			$args = array(
				'post_type' => 'proposals',
				'author'    =>  $user_id,
				'meta_query' => array(
					array(
						'key'     => '_project_id',
						'value'   => intval( $project_id ),
						'compare' => '=',
					),
				),
			);

			$query = new WP_Query( $args );
			if( !empty( $query ) ){
			   $proposals_sent =  $query->found_posts;
			}

			if( $proposals_sent > 0 ){
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('You have already sent the proposal', 'workreap_api');
				return new WP_REST_Response($json, 203); 
			}
			$user_role 			= apply_filters('workreap_get_user_role', $user_id);
			//Check user role
			if( $user_role != 'freelancers' ){
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('You are not allowed to send proposal', 'workreap_api');
				return new WP_REST_Response($json, 203); 
			}

			//Check if project is open
			$project_status = get_post_status( $project_id );
			if( $project_status === 'closed' ){
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('You can not send proposal for a closed project', 'workreap_api');
				return new WP_REST_Response($json, 203); 
			}  

			$linked_profile  	= workreap_get_linked_profile_id($user_id);
			//Calculate Service and Freelance Share
			$service_fee = '';
			if (function_exists('fw_get_db_post_option')) {
				$service_fee  = fw_get_db_settings_option('service_fee');
			}

			$service_fee = !empty( $service_fee ) ? $service_fee : 20;
			$admin_amount       = $proposed_amount / 100 * $service_fee;
			$freelancer_amount  = $proposed_amount - $admin_amount;

			//Create Proposal
			$username   = workreap_get_username( $user_id );
			$title      = get_the_title( $project_id );
			$title      = $title .' ' . '(' . $username . ')';

			$proposal_post = array(
				'post_title'    => wp_strip_all_tags( $title ), //proposal title
				'post_status'   => 'publish',
				'post_content'  => $proposed_content,
				'post_author'   => $user_id,
				'post_type'     => 'proposals',
			);

			$proposal_id    = wp_insert_post( $proposal_post );
			$fw_options 	= array();
			$attachments	= array();
			if( !is_wp_error( $proposal_id ) ) {
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

						$attach_id = wp_insert_attachment($attachment_details, $uploaded_image['file']);
						$attach_data = wp_generate_attachment_metadata($attach_id, $uploaded_image['file']);
						wp_update_attachment_metadata($attach_id, $attach_data);
						$attachments['attachment_id']	= $attach_id;
						$attachments['url']	= wp_get_attachment_url($attach_id);
			
						$proposal_files[]	= $attachments;
					}
				}
				update_post_meta( $proposal_id, '_proposal_docs', $proposal_files);
				//Update post meta
				update_post_meta( $proposal_id, '_send_by', $linked_profile);
				update_post_meta( $proposal_id, '_project_id', $project_id );
				update_post_meta( $proposal_id, '_proposed_duration', $proposed_time );
				update_post_meta( $proposal_id, '_amount', $proposed_amount);
				update_post_meta( $proposal_id, '_status', 'pending');
				update_post_meta( $proposal_id, '_admin_amount', $admin_amount);
				update_post_meta( $proposal_id, '_freelancer_amount', $freelancer_amount);

				//update connects
				if ( function_exists( 'fw_get_db_settings_option' ) ) {
					$proposal_connects 	= fw_get_db_settings_option( 'proposal_connects', $default_value = null );
					$proposal_connects	= !empty( $proposal_connects ) ? intval( $proposal_connects ) : '';
				} 

				$remaning_connects	= get_user_meta( $user_id, '_remaining_connects',true );
				$remaning_connects  = !empty( $remaning_connects ) ? intval($remaning_connects) : '';

				if( !empty( $remaning_connects) && $remaning_connects >= $proposal_connects ) {
					$update_connects	= $remaning_connects - $proposal_connects ;
					update_user_meta( $user_id, '_remaining_connects', $update_connects );
				}
				
				
				if( !empty( $proposal_files ) ){
					update_post_meta( $proposal_id, '_proposal_docs', $proposal_files);
					$fw_options['proposal_docs'] = $proposal_files;
					//Update User Profile
					fw_set_db_post_option($proposal_id, null, $fw_options);
				}
				
				//update meta
				$fw_options['project']				= array($project_id);
				$fw_options['proposed_amount'] 		= $proposed_amount;
				$fw_options['proposal_duration'] 	= $proposed_time;

				//Update User Profile
				fw_set_db_post_option($proposal_id, null, $fw_options);
					//Send email to employer
				if (class_exists('Workreap_Email_helper')) {
					if (class_exists('WorkreapProposalSubmit')) {
						$freelancer_link        = get_the_permalink( $linked_profile );
						$project_link           = get_the_permalink( $project_id );
						$project_title          = get_the_title( $project_id );
						$duration_list          = worktic_job_duration_list();
						$project_duration_value = !empty( $duration_list ) ? $duration_list[$proposed_time] : '';
						$post_author_id         = get_post_field( 'post_author', $project_id );
						$author_data            = get_userdata( $post_author_id );                    
						$email_to               = $author_data->data->user_email; 
						$employer_name          = workreap_get_username( $post_author_id );                 

						$email_helper           = new WorkreapProposalSubmit();

						$emailData = array();
						$emailData['employer_name']              = $employer_name;
						$emailData['freelancer_link']            = $freelancer_link;
						$emailData['freelancer_name']            = $username;
						$emailData['project_link']               = $project_link;
						$emailData['project_title']              = $project_title;
						$emailData['proposal_amount']            = $proposed_amount;
						$emailData['proposal_duration']          = $project_duration_value;
						$emailData['proposal_message']           = $proposed_content;
						$emailData['employer_email']             = $email_to;

						$email_helper->send_employer_proposal_submit($emailData);
						$email_helper->send_freelancer_proposal_submit($emailData);
					}
				}	
				$json['type']       = 'success';
				$json['message']    = esc_html__('Proposal sent Successfully ', 'workreap_api');
				return new WP_REST_Response($json, 200); 
			}else {
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('You can not send proposal for a closed project', 'workreap_api');
				return new WP_REST_Response($json, 203); 
			}
		}
    }
}

add_action('rest_api_init',
        function () {
    $controller = new AndroidApp_Proposal_Route;
    $controller->register_routes();
});
