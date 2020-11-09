<?php
/**
 *
 * Ajax request hooks
 *
 * @package   Workreap
 * @author    amentotech
 * @link      https://themeforest.net/user/amentotech/portfoliot
 * @since 1.0
 */


/**re
 * check dispute feeback
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_get_dispute_feedback')) {

    function workreap_get_dispute_feedback() {
        global $current_user;
        $json = array();     

        $user_input = !empty($_POST['dispute_id']) ? intval( $_POST['dispute_id'] ) : '';

        if ( empty( $user_input ) ) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('No tricks please!', 'workreap');
            wp_send_json($json);
        }
		
		if (function_exists('fw_get_db_post_option')) {
			$feedback 	= fw_get_db_post_option($user_input, 'feedback');
		}
		
		$user_input = !empty($feedback) ? $feedback : esc_html__('No feedback provided yet.', 'workreap');
		
		$json['type'] 	  = 'success';
		$json['feedback'] = $user_input;
		wp_send_json($json);
    }

    add_action('wp_ajax_workreap_get_dispute_feedback', 'workreap_get_dispute_feedback');
    add_action('wp_ajax_nopriv_workreap_get_dispute_feedback', 'workreap_get_dispute_feedback');
}

/**
 * Remove dispute
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_dispute_remove' ) ) {

	function workreap_dispute_remove() {
		global $current_user;
		$json 	 = array();
		$post_id = !empty($_POST['id']) ? intval( $_POST['id'] ) : '';

        if ( empty( $post_id ) ) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('No tricks please!', 'workreap');
            wp_send_json($json);
        }
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		wp_delete_post($post_id,true);
		
		$json['type'] 		= 'success';
		$json['message'] 	= esc_html__('Successfully!  removed this dispute.', 'workreap');	
		
		wp_send_json( $json );
	}

	add_action( 'wp_ajax_workreap_dispute_remove', 'workreap_dispute_remove' );
	add_action( 'wp_ajax_nopriv_workreap_dispute_remove', 'workreap_dispute_remove' );
}

/**
 * Create dispute
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_submit_dispute')) {

    function workreap_submit_dispute() {
        global $wpdb,$current_user,$post;
        $json = array();     
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$fields	= array(
			'project' 		=> esc_html__('No project/service is selected','workreap'),
			'reason' 		=> esc_html__('Please select the reason','workreap'),
			'description' 	=> esc_html__('Please add dispute description','workreap'),
		);
		
		foreach( $fields as $key => $item ){
			if( empty( $_POST['dispute'][$key] ) ){
				$json['type'] 	 = "error";
				$json['message'] = $item;
				wp_send_json( $json );
			}
		}
		
		//Create dispute
        $username   	= workreap_get_username( $current_user->ID );
		$linked_profile = workreap_get_linked_profile_id($current_user->ID);
        $project      	= sanitize_text_field( $_POST['dispute']['project'] );
		$title      	= sanitize_text_field( $_POST['dispute']['reason'] );
		$description    = !empty( $_POST['dispute']['description'] ) ? ( $_POST['dispute']['description'] ) : '';
		$list			= workreap_project_ratings('dispute_options');
		$dispute_title  = !empty( $list[$title] ) ? $list[$title] : rand(1,9999);
		
		$dispute_args = array('posts_per_page' => -1,
			'post_type' 		=> array( 'disputes'),
			'orderby' 			=> 'ID',
			'order' 			=> 'DESC',
			'post_status' 		=> array('pending','publish'),
			'author' 			=> $current_user->ID,
			'suppress_filters'  => false,
			'meta_query'		=> array(
				'relation' 		=> 'AND',
				 array( 'key' 			=> '_dispute_project',
					   'value' 			=> $project,
					   'compare' 		=> '='
					 )
			)
		);
		
		$dispute_is = get_posts($dispute_args); 
		if( !empty( $dispute_is ) ){
			$json['type'] = "error";
			$json['message'] = esc_html__("You have already submitted the dispute against this project.", 'workreap');
			wp_send_json( $json );
		}
		
		$project_id	= get_post_meta($project, '_project_id', true);

        $dispute_post  = array(
            'post_title'    => wp_strip_all_tags( $dispute_title ), //proposal title
            'post_status'   => 'pending',
            'post_content'  => $description,
            'post_author'   => $current_user->ID,
            'post_type'     => 'disputes',
        );

        $dispute_id    		= wp_insert_post( $dispute_post );
		update_post_meta( $dispute_id, '_send_by', $current_user->ID);
		update_post_meta( $dispute_id, '_dispute_key', $title);
		update_post_meta( $dispute_id, '_dispute_project', $project); //propsal ID
		update_post_meta( $dispute_id, '_project_id', $project_id);
		update_post_meta( $project, 'dispute', 'yes');

		$post_type_object = get_post_type_object( 'proposals' );
		$link = !empty( $post_type_object->_edit_link ) ? admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $project ) ) : '';

        //Send email to user
        if (class_exists('Workreap_Email_helper')) {
            if (class_exists('WorkreapSendDispute')) {
                $email_helper = new WorkreapSendDispute();
                $emailData = array();
                $emailData['project_link']  	= $link;
				$emailData['project_title']  	= get_the_title($project);
				$emailData['user_name']  		= $username;
                $emailData['user_link']     	= get_the_permalink($linked_profile);
                $emailData['message']      		= $description;
				$emailData['dispute_subject']   = $dispute_title;
                $email_helper->send($emailData);
            }
        }     

        $json['type'] = "success";
        $json['message'] = esc_html__("We have recived your dispute, soon we will get back to you.", 'workreap');
        wp_send_json( $json );
    }

    add_action('wp_ajax_workreap_submit_dispute', 'workreap_submit_dispute');
    add_action('wp_ajax_nopriv_workreap_submit_dispute', 'workreap_submit_dispute');
}

/**
 * Get Lost Password
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_ajax_lp')) {

    function workreap_ajax_lp() {
        global $wpdb;
        $json = array();     

        $user_input = !empty($_POST['email']) ? sanitize_email( $_POST['email'] ) : '';
		
        $captcha_settings = '';
		if (function_exists('fw_get_db_settings_option')) { 
			$captcha_settings = fw_get_db_settings_option('captcha_settings', $default_value = null);
		}
		
		//recaptcha check
        if (isset($captcha_settings) && $captcha_settings === 'enable') {
            if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
                $docReResult = workreap_get_recaptcha_response($_POST['g-recaptcha-response']);

                if ($docReResult == 1) {
                    $workdone = 1;
                } else if ($docReResult == 2) {
					$json['type'] = 'error';
                    $json['message'] = esc_html__('An error occurred, please try again later.', 'workreap');
                    wp_send_json($json);
                } else {
					$json['type'] = 'error';
                    $json['message'] = esc_html__('Wrong reCaptcha. Please verify first.', 'workreap');
                    wp_send_json($json);
                }
            } else {
                wp_send_json(array('type' => 'error', 'message' => esc_html__('Please enter reCaptcha!', 'workreap')));
            }
        }

        if (empty($user_input)) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Please add email address.', 'workreap');
            wp_send_json( $json );
        } else if (!is_email($user_input)) {
            $json['type'] = "error";
            $json['message'] = esc_html__("Please add a valid email address.", 'workreap');
            wp_send_json( $json );
        }      

        $user_data = get_user_by('email', $user_input);
        if (empty($user_data) ) {
            $json['type'] = "error";
            $json['message'] = esc_html__("This Email address is not exists.", 'workreap');
            wp_send_json( $json );
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
        }else{
			//generate reset key
            $key = wp_generate_password(20, false);
            $wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
		}

        $protocol = is_ssl() ? 'https' : 'http';
        $reset_link = esc_url(add_query_arg(array('action' => 'reset_pwd', 'key' => $key, 'login' => $user_login), home_url('/', $protocol)));

        //Send email to user
        if (class_exists('Workreap_Email_helper')) {
            if (class_exists('WorkreapGetPassword')) {
                $email_helper = new WorkreapGetPassword();
                $emailData = array();
                $emailData['username']  = $username;
                $emailData['email']     = $user_email;
                $emailData['link']      = $reset_link;
                $email_helper->send($emailData);
            }
        }     

        $json['type'] = "success";
        $json['message'] = esc_html__("A link has been sent, please check your email.", 'workreap');
        wp_send_json( $json );
    }

    add_action('wp_ajax_workreap_ajax_lp', 'workreap_ajax_lp');
    add_action('wp_ajax_nopriv_workreap_ajax_lp', 'workreap_ajax_lp');
}

/**
 * Reset Password
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_ajax_reset_password')) {

    function workreap_ajax_reset_password() {
        global $wpdb;
        $json = array();   
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
        //Security check
        if (!wp_verify_nonce($_POST['wt_change_pwd_nonce'], "wt_change_pwd_nonce")) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('No Kiddies please.', 'workreap');
            wp_send_json( $json );
        }

        //Form Validation
        if (isset($_POST['password'])) {
            if ($_POST['password'] != $_POST['verify_password']) {
                // Passwords don't match
                $json['type'] = "error";
                $json['message'] = esc_html__("Oops! password is not matched", 'workreap');
                wp_send_json( $json );
            }

            if (empty($_POST['password'])) {
                $json['type'] = "error";
                $json['message'] = esc_html__("Oops! password should not be empty", 'workreap');
                wp_send_json( $json );
            }
			
			if ( strlen( $_POST['password'] ) < 6 ) {
				$json['type'] 	 = 'error';
				$json['message'] = esc_html__('Password length should be minimum 6', 'workreap');
				wp_send_json( $json );
			}
        } else {
            $json['type'] = "error";
            $json['message'] = esc_html__("Oops! Invalid request", 'workreap');
            wp_send_json( $json );
        }     


        if (!empty($_POST['key']) &&
			( isset($_POST['reset_action']) && $_POST['reset_action'] == "reset_pwd" ) &&
			(!empty($_POST['login']) )
        ) {

            $reset_key  = sanitize_text_field($_POST['key']);
            $user_login = sanitize_text_field($_POST['login']);

            $user_data = $wpdb->get_row($wpdb->prepare("SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $reset_key, $user_login));

            $user_login = $user_data->user_login;
            $user_email = $user_data->user_email;

            if (!empty($reset_key) && !empty($user_data)) {
                $new_password = sanitize_text_field( $_POST['password'] );

                wp_set_password($new_password, $user_data->ID);

                $json['redirect_url'] = home_url('/');
                $json['type'] = "success";
                $json['message'] = esc_html__("Congratulation! your password has been changed.", 'workreap');
                wp_send_json( $json );
            } else {
                $json['type'] = "error";
                $json['message'] = esc_html__("Oops! Invalid request", 'workreap');
                wp_send_json( $json );
            }
        } else {
        	$json['type'] = 'error';
        	$json['message'] = esc_html__('No kiddies please', 'workreap');
        	wp_send_json( $json );
        }
    }

    add_action('wp_ajax_workreap_ajax_reset_password', 'workreap_ajax_reset_password');
    add_action('wp_ajax_nopriv_workreap_ajax_reset_password', 'workreap_ajax_reset_password');
}

/**
 * Temp Uploader
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_award_temp_file_uploader')) {

    function workreap_award_temp_file_uploader() {
		
        global $current_user, $wp_roles, $userdata, $post;
        $user_identity = $current_user->ID;
        $ajax_response  =  array();
        $upload = wp_upload_dir();

        $upload_dir = $upload['basedir'];
        $upload_dir = $upload_dir . '/workreap-temp/';
        
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
        //create directory if not exists
        if (! is_dir($upload_dir)) {
           wp_mkdir_p( $upload_dir );
        }

        $submitted_file = $_FILES['award_img'];
        $name = preg_replace("/[^A-Z0-9._-]/i", "_", $submitted_file["name"]);

        $i = 0;
        $parts = pathinfo($name);
        while (file_exists($upload_dir . $name)) {
            $i++;
            $name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
        }
        
        //move files
        $is_moved = move_uploaded_file($submitted_file["tmp_name"], $upload_dir . '/'.$name);                
        if( $is_moved ){
            $size       = $submitted_file['size'];
            $file_size  = size_format($size, 2);           
            $ajax_response['type']    = 'success';
            $ajax_response['message'] = esc_html__('File uploaded!', 'workreap');
            $url = $upload['baseurl'].'/workreap-temp/'.$name;
            $ajax_response['thumbnail'] = $upload['baseurl'].'/workreap-temp/'.$name;
            $ajax_response['name']    = $name;
            $ajax_response['size']    = $file_size;
        } else{
            $ajax_response['message'] = esc_html__('Some error occur, please try again later', 'workreap');
            $ajax_response['type']    = 'error';
        }
		
		wp_send_json( $ajax_response );
    }

    add_action('wp_ajax_workreap_award_temp_file_uploader', 'workreap_award_temp_file_uploader');
    add_action('wp_ajax_nopriv_workreap_award_temp_file_uploader', 'workreap_award_temp_file_uploader');
}

/**
 * Process proposal
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists( 'workreap_process_project_proposal' ) ){
    function workreap_process_project_proposal(){
		global $current_user;        

        $user_role 			= apply_filters('workreap_get_user_role', $current_user->ID);
        $linked_profile  	= workreap_get_linked_profile_id($current_user->ID);
	
		$project_id         = !empty($_POST['post_id']) ?  intval( $_POST['post_id'] ) :'';
		$proposed_amount    = !empty($_POST['proposed_amount']) ?   $_POST['proposed_amount']  : '';
		$proposal_edit_id		= !empty($_POST['proposal_id']) ?  intval( $_POST['proposal_id'] ) : '';

		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		if( apply_filters('workreap_feature_connects', $current_user->ID) === false && empty($proposal_edit_id) ){
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__('You’ve consumed all your credits to apply on a job. Please subscribe to a package to appy on this job','workreap');
			wp_send_json( $json );
		}
		
		do_action('workreap_check_post_author_status', $linked_profile); //check if user is not blocked or deactive
		
		if( get_post_status( $project_id ) === 'hired' ){
			$json['type'] = 'error';
            $json['message'] = esc_html__('This project has been assigned to one of the freelancer. You can\'t send proposals.', 'workreap');
            wp_send_json( $json );
		} else if( get_post_status( $project_id ) === 'completed' ){
			$json['type'] = 'error';
            $json['message'] = esc_html__('This project has been completed, so you can\'t send proposals', 'workreap');
            wp_send_json( $json );
		}else if( get_post_status( $project_id ) === 'completed' ){
			$json['type'] = 'error';
            $json['message'] = esc_html__('This project has been cancelled, when employer will re-open this project then you would be able to send proposal.', 'workreap');
            wp_send_json( $json );
		}else if( get_post_status( $project_id ) === 'pending' ){
			$json['type'] = 'error';
            $json['message'] = esc_html__('This project is under review. You can\'t send proposals.', 'workreap');
            wp_send_json( $json );
		}
		
		//Check user role
        if( $user_role !== 'freelancers' ){
            $json['type'] = 'error';
            $json['message'] = esc_html__('You are not allowed to send  the proposals', 'workreap');
            wp_send_json( $json );
        }

        if( empty( $_POST['post_id'] ) ){
            $json['type'] = 'error';
            $json['message'] = esc_html__('Some thing went wrong, try again', 'workreap');
            wp_send_json( $json );
        }
				
        //Check if user already submitted proposal
        $proposals_sent = intval(0);
        $args = array(
            'post_type' => 'proposals',
            'author'    =>  $current_user->ID,
            'meta_query' => array(
                array(
                    'key'     => '_project_id',
                    'value'   => intval( $_POST['post_id'] ),
                    'compare' => '=',
                ),
            ),
        );

        $query = new WP_Query( $args );
        if( !empty( $query ) ){
           $proposals_sent =  $query->found_posts;
        }

        if( $proposals_sent > 0 && empty($proposal_edit_id) ){
            $json['type'] = 'error';
            $json['message'] = esc_html__('You have already sent the proposal', 'workreap');
            wp_send_json( $json );
        }

        //Check Security
        $do_check = check_ajax_referer('workreap_submit_proposal_nounce', 'workreap_submit_proposal_nounce', false);
        if ( $do_check == false ) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('No Kiddies Please', 'workreap');
            wp_send_json( $json );
        }

        //Check if project is open
        $project_status = get_post_status( $project_id );
        if( $project_status === 'closed' ){
            $json['type'] = 'error';
            $json['message'] = esc_html__('You can not send proposal for a closed project', 'workreap');
            wp_send_json( $json );
        }        


        $proposed_time_enabled = 'no';
        if (function_exists('fw_get_db_post_option')) {
            $allow_proposal_amount_edit = fw_get_db_settings_option('allow_proposal_amount_edit');
        }

        //Validation
        $validations = array(            
            'proposed_amount'       => esc_html__('Amount field is required', 'workreap'),
            'proposed_content'      => esc_html__('Cover latter field is required', 'workreap'),            
        );
        if(!empty($allow_proposal_amount_edit) && $allow_proposal_amount_edit == 'no') {
            unset($validations['proposed_amount']);
        }

        $validations	= apply_filters('workreap_sort_proposal_validations',$validations);

        foreach ( $validations as $key => $value ) {
            if ( empty( $_POST[$key] ) ) {
                $json['type'] = 'error';
                $json['message'] = $value;
                wp_send_json( $json );
            }                    
        }           

		if (function_exists('fw_get_db_post_option')) {
			$db_project_type     = fw_get_db_post_option($project_id,'project_type');
			if( !empty( $db_project_type['gadget'] ) && $db_project_type['gadget'] === 'hourly' ){
				if( empty( $_POST['estimeted_time'])) {
					$json['type'] 		= 'error';
					$json['message'] 	= esc_html__('Estimated Hours are required','workreap');
					wp_send_json( $json );
				} else {
					$estimeted_time     = sanitize_text_field( $_POST['estimeted_time'] );
					$per_hour_amount	= $proposed_amount;
					$proposed_amount	= $proposed_amount * $estimeted_time;
				}
			} else if( !empty( $db_project_type['gadget'] ) && $db_project_type['gadget'] === 'fixed' ){
				if( empty( $_POST['proposed_time']) && $proposed_time_enabled == 'yes' ) {
					$json['type'] 		= 'error';
					$json['message'] 	= esc_html__('Proposal time is required','workreap');
					wp_send_json( $json );
				} else {
					$proposed_time      = sanitize_text_field( $_POST['proposed_time'] );
				}
			}
		}

        // get project price if editting the cost is disabled		
        if(!empty($allow_proposal_amount_edit) && $allow_proposal_amount_edit == 'no') {
            $project_price  = workreap_project_price($project_id);
            $proposed_amount = $project_price['max_val'];
        }

        //Get Form data
        $fw_options 		= array();
        $user_id            = $current_user->ID;
        
        $proposed_content   = sanitize_textarea_field( $_POST['proposed_content'] );
        $files              = !empty( $_POST['temp_items'] ) ? $_POST['temp_items'] : array();     
        $proposal_files     = array();
		
        //Calculate Service and Freelance Share
        $service_fee 		= 0;
        $service_fee		= worrketic_hiring_payment_setting();
		
        $service_fee 		= !empty( $service_fee['percentage'] ) ? $service_fee['percentage'] : 0;
		
		if( !empty( $service_fee ) ){
			$admin_amount       = $proposed_amount / 100 * $service_fee;
        	$freelancer_amount  = $proposed_amount - $admin_amount;
		} else{
			$admin_amount       = 0;
        	$freelancer_amount  = $proposed_amount - $admin_amount;
		}
                
		//Upload files from temp folder to uploads
	
        if( !empty( $files ) ) {
            foreach ( $files as $key => $value ) { 
				if( !empty($value['attachment_id']) ) {
					$proposal_files[$key] = $value;
				} else {
					$proposal_files[] = workreap_temp_upload_to_media($value, $project_id);
				}
            }                
        }
	
        //Create Proposal
        $username   = workreap_get_username( $current_user->ID );
        $title      = esc_html( get_the_title( $project_id ));
        $title      = $title .' ' . '(' . $username . ')';
		
		$proposal_post = array(
			'post_title'    => wp_strip_all_tags( $title ), //proposal title
			'post_status'   => 'publish',
			'post_content'  => $proposed_content,
			'post_author'   => $current_user->ID,
			'post_type'     => 'proposals',
		);

		if( empty($proposal_id) ){
			$proposal_id    = wp_insert_post( $proposal_post );

			//Prepare Params
			$params_array['user_identity'] = $current_user->ID;
			$params_array['project_id'] = (int) $project_id;
			$params_array['user_role'] = apply_filters('workreap_get_user_type', $current_user->ID );
			$params_array['type'] = 'proposal_made';

			//child theme : update extra settings
			do_action('wt_process_proposal_made', $params_array);
		} else {
			$proposal_post['ID']	= $proposal_edit_id;
			$proposal_id    		= $proposal_edit_id;
			wp_update_post( $proposal_post );
		}
		
        if( !is_wp_error( $proposal_id ) ) {
			
			
			if( !empty( $db_project_type['gadget'] ) && $db_project_type['gadget'] === 'fixed' ){
            	update_post_meta( $proposal_id, '_proposed_duration', $proposed_time );
				$fw_options['proposal_duration'] 	= $proposed_time;
			} else if( !empty( $db_project_type['gadget'] ) && $db_project_type['gadget'] === 'hourly' ){
				update_post_meta( $proposal_id, '_estimeted_time', $estimeted_time );
				update_post_meta( $proposal_id, '_per_hour_amount', $per_hour_amount );
				$fw_options['estimeted_time'] 	= $estimeted_time;
				$fw_options['per_hour_amount'] 	= $per_hour_amount;
			}
			
            //Update post meta
            update_post_meta( $proposal_id, '_send_by', $linked_profile);
            update_post_meta( $proposal_id, '_project_id', $project_id );
            update_post_meta( $proposal_id, '_amount', $proposed_amount);
            update_post_meta( $proposal_id, '_status', 'pending');
            update_post_meta( $proposal_id, '_admin_amount', $admin_amount);
            update_post_meta( $proposal_id, '_freelancer_amount', $freelancer_amount);
			
			//update connects
			if ( function_exists( 'fw_get_db_settings_option' ) ) {
				$proposal_connects 	= fw_get_db_settings_option( 'proposal_connects', $default_value = null );
				$proposal_connects	= !empty( $proposal_connects ) ? intval( $proposal_connects ) : 2;
			} 
			
			$remaning_connects		= workreap_get_subscription_metadata( 'wt_connects',intval($current_user->ID) );
			$remaning_connects  	= !empty( $remaning_connects ) ? intval($remaning_connects) : '';
			
			if( !empty( $remaning_connects) && $remaning_connects >= $proposal_connects && empty($proposal_edit_id) ) {
				$update_connects	= $remaning_connects - $proposal_connects ;
				$update_connects	= intval($update_connects);
				
				$wt_subscription 	= get_user_meta(intval($current_user->ID), 'wt_subscription', true);
				$wt_subscription	= !empty( $wt_subscription ) ?  $wt_subscription : array();
				$wt_subscription['wt_connects'] = $update_connects;
				update_user_meta( intval($current_user->ID), 'wt_subscription', $wt_subscription);
			}
			
			
            if( !empty( $proposal_files ) ){
                update_post_meta( $proposal_id, '_proposal_docs', $proposal_files);
                $fw_options['proposal_docs'] = $proposal_files;
            }
            
			//update meta
			$fw_options['project']				= array($project_id);
			$fw_options['proposed_amount'] 		= $proposed_amount;
			
			
			//Update User Profile
			fw_set_db_post_option($proposal_id, null, $fw_options);

			//update api key data
			if( apply_filters('workreap_filter_user_promotion', 'disable') === 'enable' ){	
				do_action('workreap_update_users_marketing_attributes', $current_user->ID, 'freelancer_bid');
				do_action('workreap_update_users_marketing_product_creation', $current_user->ID, $project_id, 'proposal_count_update');
			}
			
			//update more data hook 
			do_action('workreap_update_proposals_extra_data',$_POST,$proposal_id);
			
            //Send email to employer
            if (class_exists('Workreap_Email_helper')) {
                if (class_exists('WorkreapProposalSubmit')) {
					
					if(empty($proposal_edit_id)){
						$freelancer_link        = esc_url( get_the_permalink( $linked_profile ));
						$project_link           = esc_url( get_the_permalink( $project_id ));
						$project_title          = esc_html( get_the_title( $project_id ));
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
						$emailData['proposal_amount']            = workreap_price_format($proposed_amount,'return');;
						$emailData['proposal_duration']          = $project_duration_value;
						$emailData['proposal_message']           = $proposed_content;
						$emailData['employer_email']             = $email_to;
						$emailData['freelancer_email']           = $current_user->user_email;
						$email_helper->send_employer_proposal_submit($emailData);
						$email_helper->send_freelancer_proposal_submit($emailData);
					}
                }
            }

			$json['return']  = esc_url( get_the_permalink( $project_id ));
            $json['type']    = 'success';
            $json['message'] = esc_html__('Your proposal has sent Successfully', 'workreap');
            wp_send_json( $json );
			
        } else {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Some thing went wrong, try again', 'workreap');
            wp_send_json( $json );
        }

    }
    add_action('wp_ajax_workreap_process_project_proposal', 'workreap_process_project_proposal');
    add_action('wp_ajax_nopriv_workreap_process_project_proposal', 'workreap_process_project_proposal');
}

/**
 * File uploader
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_temp_file_uploader')) {

    function workreap_temp_file_uploader() {       
        global $current_user, $wp_roles, $userdata, $post;
        $user_identity 		= $current_user->ID;
        $ajax_response  	=  array();
        $upload 			= wp_upload_dir();
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
        $upload_dir = $upload['basedir'];
        $upload_dir = $upload_dir . '/workreap-temp/';
        
        //create directory if not exists
        if (! is_dir($upload_dir)) {
           wp_mkdir_p( $upload_dir );
        }
       
        $submitted_file = $_FILES['file_name'];
        $name = preg_replace("/[^A-Z0-9._-]/i", "_", $submitted_file["name"]);

        $i = 0;
        $parts = pathinfo($name);
        while (file_exists($upload_dir . $name)) {
            $i++;
            $name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
        }
        
        //move files
        $is_moved = move_uploaded_file($submitted_file["tmp_name"], $upload_dir . '/'.$name);                
        if( $is_moved ){
            $size       = $submitted_file['size'];
            $file_size  = size_format($size, 2);           
            $ajax_response['type']    = 'success';
            $ajax_response['message'] = esc_html__('File uploaded!', 'workreap');
            $url = $upload['baseurl'].'/workreap-temp/'.$name;
            $ajax_response['thumbnail'] = $upload['baseurl'].'/workreap-temp/'.$name;
            $ajax_response['name']    = $name;
            $ajax_response['size']    = $file_size;
        } else{
            $ajax_response['message'] = esc_html__('Some error occur, please try again later', 'workreap');
            $ajax_response['type']    = 'error';
        }
        wp_send_json($ajax_response);
    }

    add_action('wp_ajax_workreap_temp_file_uploader', 'workreap_temp_file_uploader');
    add_action('wp_ajax_nopriv_workreap_temp_file_uploader', 'workreap_temp_file_uploader');
}

/**
 * Generate QR code
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists( 'workreap_generate_qr_code' ) ) {
    function workreap_generate_qr_code(){        
        $user_id = !empty( $_POST['key'] ) ? $_POST['key'] : '';  
        $type    = !empty( $_POST['type'] ) ? $_POST['type'] : '';        
        if( file_exists( WP_PLUGIN_DIR. '/workreap_core/libraries/phpqrcode/phpqrcode.php' ) ){
            if( !empty( $user_id ) && !empty( $type ) ) {  
                require_once(WP_PLUGIN_DIR. '/workreap_core/libraries/phpqrcode/phpqrcode.php' );
                $user_link      = get_permalink( $user_id );
                $data_type 		= $type.'-';
				
                $tempDir        = wp_upload_dir();                  
                $codeContents   = esc_url($user_link);      
                $tempUrl    = trailingslashit($tempDir['baseurl']);
                $tempUrl    = $tempUrl.'/qr-code/'.$data_type.$user_id.'/';            
                $upload_dir = trailingslashit($tempDir['basedir']);
                $upload_dir = $upload_dir .'qr-code/';
				
                if (! is_dir($upload_dir)) {
                    wp_mkdir_p( $upload_dir);
                    //qr-code directory created
                    $upload_folder = $upload_dir.$data_type.$user_id.'/';                
                    if (! is_dir($upload_folder)) {
                        wp_mkdir_p( $upload_folder);
                        //Create image
                        $fileName = $user_id.'.png';      
                        $qrAbsoluteFilePath = $upload_folder.$fileName; 
                        $qrRelativeFilePath = $tempUrl.$fileName;     
                    } 
                } else {
                    //create user directory
                    $upload_folder = $upload_dir.$data_type.$user_id.'/';              
                    if (! is_dir($upload_folder)) {
                        wp_mkdir_p( $upload_folder );
                        //Create image
                        $fileName = $user_id.'.png';      
                        $qrAbsoluteFilePath = $upload_folder.$fileName; 
                        $qrRelativeFilePath = $tempUrl.$fileName;     
                    } else {
                        $fileName = $user_id.'.png';      
                        $qrAbsoluteFilePath = $upload_folder.$fileName; 
                        $qrRelativeFilePath = $tempUrl.$fileName;     
                    }
                }                
                //Delete if exists
                if (file_exists($qrAbsoluteFilePath)) { 
                    wp_delete_file( $qrAbsoluteFilePath );
                    QRcode::png($codeContents, $qrAbsoluteFilePath, QR_ECLEVEL_L, 3);                        
                } else {
                    QRcode::png($codeContents, $qrAbsoluteFilePath, QR_ECLEVEL_L, 3);            
                }           
                
                if( !empty( $qrRelativeFilePath ) ) {
                        $json['type'] = 'success';
                        $json['message'] = esc_html__('QR code genrated successfully', 'workreap');
                        $json['key'] = $qrRelativeFilePath;
                        wp_send_json( $json );
                }     
                $json['type'] = 'error';
                $json['message'] = esc_html__('Some thing went wrong.', 'workreap');
                wp_send_json( $json );  
            } else {
                $json['type'] = 'error';
                $json['message'] = esc_html__('Something went wrong.', 'workreap');
                wp_send_json( $json ); 
            }
        } else {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Please update/install required plugins', 'workreap');
            wp_send_json( $json );
        }
    }
    add_action('wp_ajax_workreap_generate_qr_code', 'workreap_generate_qr_code');
    add_action('wp_ajax_nopriv_workreap_generate_qr_code', 'workreap_generate_qr_code');
}

/**
 * add project to favourite
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_add_project_to_wishlist')) {

    function workreap_add_project_to_wishlist() {
        global $current_user;
        $json           = array();
        $saves_jobs     = array();
		
        $post_id        = workreap_get_linked_profile_id($current_user->ID);
        $saves_jobs     = get_post_meta($post_id, '_saved_projects', true);
        $saves_jobs     = !empty( $saves_jobs ) && is_array( $saves_jobs ) ? $saves_jobs : array();
        $project_id     = sanitize_text_field( $_POST['project_id'] );

        if (!empty($project_id)) {            
            $saves_jobs[] = $project_id;
            $saves_jobs   = array_unique( $saves_jobs );
            update_post_meta( $post_id, '_saved_projects', $saves_jobs );
           
            $json['type'] 		= 'success';
			$json['text'] 		= esc_html__('Saved', 'workreap');
            $json['message'] 	= esc_html__('Successfully! added to your saved jobs', 'workreap');
            wp_send_json( $json );
        }
        
        $json['type'] 		= 'error';
        $json['message'] 	= esc_html__('Oops! something is going wrong.', 'workreap');
        wp_send_json( $json );
    }

    add_action('wp_ajax_workreap_add_project_to_wishlist', 'workreap_add_project_to_wishlist');
    add_action('wp_ajax_nopriv_workreap_add_project_to_wishlist', 'workreap_add_project_to_wishlist');
}

/**
 * follow employer action
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_follow_employer' ) ) {

	function workreap_follow_employer() {
		global $current_user;
		$type 		= !empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
		$post_id 	= !empty( $_POST['id'] ) ? intval( $_POST['id'] ) : '';
		$json 		= array();

		if ( empty( $current_user->ID ) ) {
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__( 'You must login before add this employer to your following list.', 'workreap' );
			wp_send_json( $json );
		}
		
		
		$linked_profile   		= workreap_get_linked_profile_id($current_user->ID);
		
		//employer followers
		$emp_followers 			= get_post_meta($post_id, '_followers', true);
		$emp_followers   		= !empty( $emp_followers ) && is_array( $emp_followers ) ? $emp_followers : array();
		$emp_followers[] 		= $linked_profile;
		$emp_followers   		= array_unique( $emp_followers );
		update_post_meta($post_id, '_followers', $emp_followers);
		
		//update user followings
		$user_followings 		= get_post_meta($linked_profile, '_following_employers', true);
		$user_followings   		= !empty( $user_followings ) && is_array( $user_followings ) ? $user_followings : array();
		$user_followings[] 		= $post_id;
		$user_followings   		= array_unique( $user_followings );
		
		update_user_meta( $current_user->ID, '_following_employers', $user_followings );
		
		if( !empty( $linked_profile ) ){
			update_post_meta($linked_profile, '_following_employers', $user_followings);
		}
		
		$json['type'] = 'success';
		$json['message'] = esc_html__( 'Successfully added your following list', 'workreap' );
		wp_send_json( $json );
	}

	add_action( 'wp_ajax_workreap_follow_employer', 'workreap_follow_employer' );
	add_action( 'wp_ajax_nopriv_workreap_follow_employer', 'workreap_follow_employer' );
}

/**
 * Report employer, project or freelancer 
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists(  'workreap_report_user' ) ) {
	function workreap_report_user(){
		global $current_user;

		$json 			= array();
		$type 			= !empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
		$reported_id 	= !empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
		$description 	= !empty( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '';
		$reason 		= !empty( $_POST['reason'] ) ? sanitize_textarea_field( $_POST['reason'] ) : '';
		$json['loggin'] = 'true';

		if ( !isset( $_POST['report_nonce'] ) || ! wp_verify_nonce( $_POST['report_nonce'], 'workreap_report_nonce' ) ) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('No tricks please!', 'workreap');
            wp_send_json( $json );
        }
		
		if( empty( $reason ) || empty( $description ) ){
			$json['type'] = 'error';
			$json['message'] = esc_html__( 'All the fields are required', 'workreap' );
			wp_send_json( $json );
		}

		if ( empty( $current_user->ID ) ) {
			$json['type'] = 'error';
			$json['loggin'] = 'false';
			$json['message'] = esc_html__( 'You must login before report', 'workreap' );
			wp_send_json( $json );
		}
		
		$reasons	= workreap_get_report_reasons();
		
		if ( function_exists( 'fw_get_db_settings_option' ) ) {
			$remove_settings 	= fw_get_db_settings_option( 'report_'.$type, $default_value = null );
			$remove_report		= !empty( $remove_settings['gadget'] ) ? $remove_settings['gadget'] : 'no';
			$reasons			= !empty( $remove_settings['no']['report_options'] ) ? $remove_settings['no']['report_options'] : 'no';
			
			if( !empty( $reasons ) and is_array($reasons) ){
				$reasons = array_filter($reasons);
				$reasons = array_combine(array_map('sanitize_title', $reasons), $reasons);
			} else{
				$reasons	= workreap_get_report_reasons();
			}
		} 
		
		$linked_profile   	= workreap_get_linked_profile_id($current_user->ID);
		$title				= !empty( $reasons[$reason] ) ? $reasons[$reason] : rand(1,999999);
		
		//Create Post
		$user_post = array(
			'post_title'    => wp_strip_all_tags( $title ),
			'post_status'   => 'publish',
			'post_content'  => $description,
			'post_author'   => $current_user->ID,
			'post_type'     => 'reports',
		);

		$post_id    = wp_insert_post( $user_post );
		
		
		if( !is_wp_error( $post_id ) ) {
			update_post_meta($post_id, '_report_type', $type);
			update_post_meta($post_id, '_reported_id', $reported_id);
			update_post_meta($post_id, '_user_by', $linked_profile);
			
			//Send email to users
			if (class_exists('Workreap_Email_helper')) {
				if (class_exists('WorkreapReportUser')) {
					$email_helper = new WorkreapReportUser();
					$emailData = array();
					$emailData['name'] 				= esc_html( get_the_title($reported_id));
					$emailData['user_link'] 		= get_edit_post_link($linked_profile);
					$emailData['message'] 			= $description;
					$emailData['reported_by'] 		= workreap_get_username($current_user->ID);
					$emailData['reported_title'] 	= $title;
					
					if( !empty( $type ) && $type === 'employer' ){
						$emailData['employer_link'] 	= get_edit_post_link($reported_id);
						$email_helper->send_employer_report($emailData);
					} else if( !empty( $type ) && $type === 'project' ){
						$emailData['project_link'] 	= get_edit_post_link($reported_id);
						$email_helper->send_project_report($emailData);
					} else if( !empty( $type ) && $type === 'freelancer' ){
						$emailData['freelancer_link'] 	= get_edit_post_link($reported_id);
						$email_helper->send_freelancer_report($emailData);
					}else if( !empty( $type ) && $type === 'service' ){
						$emailData['service_link'] 	= get_edit_post_link($reported_id);
						$email_helper->send_service_report($emailData);
					}
				}
			}
			
			$json['type'] = 'success';
			$json['message'] = esc_html__('Your report has submitted', 'workreap');                
			wp_send_json($json);
		} else {
			$json['type'] = 'error';
			$json['message'] = esc_html__('Some error occurs, please try again later', 'workreap');                
			wp_send_json($json);
		}			
	}
	add_action( 'wp_ajax_workreap_report_user', 'workreap_report_user' );
	add_action( 'wp_ajax_nopriv_workreap_report_user', 'workreap_report_user' );
}

/**
 * follow freelqancer action
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_follow_freelancer' ) ) {

	function workreap_follow_freelancer() {
		global $current_user;
		$post_id = !empty( $_POST['id'] ) ? intval( $_POST['id'] ) : '';
		$json = array();

		if ( empty( $current_user->ID ) ) {
			$json['type'] = 'error';
			$json['message'] = esc_html__( 'You must login before add this freelancer to wishlist.', 'workreap' );
			wp_send_json( $json );
		}
		
		$linked_profile   	= workreap_get_linked_profile_id($current_user->ID);
		$saved_freelancers 	= get_post_meta($linked_profile, '_saved_freelancers', true);
		
		$json       = array();
        $wishlist   = array();
        $wishlist   = !empty( $saved_freelancers ) && is_array( $saved_freelancers ) ? $saved_freelancers : array();

        if (!empty($post_id)) {
            if( in_array($post_id, $wishlist ) ){                
                $json['type'] = 'error';
                $json['message'] = esc_html__('This freelancer is already to your wishlist', 'workreap');
                wp_send_json( $json );
            }

            $wishlist[] = $post_id;
            $wishlist   = array_unique( $wishlist );
            update_post_meta( $linked_profile, '_saved_freelancers', $wishlist );
           
            $json['type'] = 'success';
            $json['message'] = esc_html__('Successfully! added to your wishlist', 'workreap');
            wp_send_json( $json );
        }
        
        $json['type'] = 'error';
        $json['message'] = esc_html__('Oops! something is going wrong.', 'workreap');
        wp_send_json( $json );
	}

	add_action( 'wp_ajax_workreap_follow_freelancer', 'workreap_follow_freelancer' );
	add_action( 'wp_ajax_nopriv_workreap_follow_freelancer', 'workreap_follow_freelancer' );
}


/**
 * Upload Profile Photo
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_image_uploader')) {

    function workreap_image_uploader() {

        global $current_user, $wp_roles;
        $user_identity = $current_user->ID;
		$ajax_response	=  array();
		
        $nonce = sanitize_text_field( $_REQUEST['nonce'] );
        $type  = sanitize_text_field( $_REQUEST['type'] );
		
		do_action('workreap_do_check_package_limit',$type);

		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
        if (!wp_verify_nonce($nonce, 'sp_upload_nonce')) {
            $ajax_response = array(
                'type' => 'error',
				'message' => esc_html__('Security check failed!', 'workreap'),
            );
            wp_send_json($ajax_response);
        }

        $submitted_file = $_FILES['sp_image_uploader'];
        $uploaded_image = wp_handle_upload($submitted_file, array('test_form' => false));

        if (isset($uploaded_image['file'])) {
            $file_name = basename($submitted_file['name']);
            $file_type = wp_check_filetype($uploaded_image['file']);

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

            //Image Size
            $size_type = 'thumbnail';
            if (!empty($type) && $type === 'profile_photo') {
                $size_type = 'avatar';
            } elseif (!empty($type) && $type === 'profile_banner_photo') {
                $size_type = 'banner';
            } elseif (!empty($type) && $type === 'profile_award') {
                $size_type = 'award';
            }

            $attachment_json = workreap_get_profile_image_url($attach_data, $size_type, $file_name); //get image url
            $is_replace = 'no';

            if (!empty($type) && $type === 'profile_photo') {
                $profile_meta = get_user_meta($user_identity, 'profile_avatar', true);
                $data_array = array();
                if (!empty($profile_meta['image_data'])) {

                    $attach_array[$attach_id] = array(
                        'full' => $attachment_json['full'],
                        'thumb' => $attachment_json['thumbnail'],
                        'banner' => $attachment_json['banner'],
                        'image_id' => $attach_id
                    );
                    $is_replace = 'no';
                    $profile_meta['image_data'] = $profile_meta['image_data'] + $attach_array;
                    update_user_meta($user_identity, 'profile_avatar', $profile_meta);
                } else {
                    $data_array = array(
                        'image_type' => $type,
                        'default_image' => $attach_id,
                        'image_data' => array(
                            $attach_id => array(
                                'full' => $attachment_json['full'],
                                'thumb' => $attachment_json['thumbnail'],
                                'banner' => $attachment_json['banner'],
                                'image_id' => $attach_id
                            ),
                        )
                    );
                    $is_replace = 'yes';
                    update_user_meta($user_identity, 'profile_avatar', $data_array);
                }
				
				update_user_meta($user_identity, 'is_avatar_available', 1);
				
            } elseif (!empty($type) && $type === 'profile_banner_photo') {
                $profile_banner_meta = get_user_meta($user_identity, 'profile_banner_photos', true);
                $data_array = array();
                if (!empty($profile_banner_meta['image_data'])) {

                    $attach_array[$attach_id] = array(
                        'full' => $attachment_json['full'],
                        'thumb' => $attachment_json['thumbnail'],
                        'banner' => $attachment_json['banner'],
                        'image_id' => $attach_id
                    );
                    $is_replace = 'no';
                    $profile_banner_meta['image_data'] = $profile_banner_meta['image_data'] + $attach_array;
                    update_user_meta($user_identity, 'profile_banner_photos', $profile_banner_meta);
                } else {
                    $data_array = array(
                        'image_type' => $type,
                        'default_image' => $attach_id,
                        'image_data' => array(
                            $attach_id => array(
                                'full' => $attachment_json['full'],
                                'thumb' => $attachment_json['thumbnail'],
                                'banner' => $attachment_json['banner'],
                                'image_id' => $attach_id
                            ),
                        )
                    );
                    $is_replace = 'yes';
                    update_user_meta($user_identity, 'profile_banner_photos', $data_array);
                }
            } elseif (!empty($type) && $type === 'profile_gallery') {
                $profile_gallery_meta = get_user_meta($user_identity, 'profile_gallery_photos', true);
                $data_array = array();
                if (!empty($profile_gallery_meta['image_data'])) {

                    $attach_array[$attach_id] = array(
                        'full' => $attachment_json['full'],
                        'thumb' => $attachment_json['thumbnail'],
                        'banner' => $attachment_json['banner'],
                        'image_id' => $attach_id
                    );
                    $is_replace = 'no';
                    $profile_gallery_meta['image_data'] = $profile_gallery_meta['image_data'] + $attach_array;
                    update_user_meta($user_identity, 'profile_gallery_photos', $profile_gallery_meta);
                } else {
                    $data_array = array(
                        'image_type' => $type,
                        'default_image' => $attach_id,
                        'image_data' => array(
                            $attach_id => array(
                                'full' => $attachment_json['full'],
                                'thumb' => $attachment_json['thumbnail'],
                                'banner' => $attachment_json['banner'],
                                'image_id' => $attach_id
                            ),
                        )
                    );
                    $is_replace = 'yes';
                    update_user_meta($user_identity, 'profile_gallery_photos', $data_array);
                }
            }

            $ajax_response = array(
                'is_replace' 	=> $is_replace,
                'type' 			=> 'success',
				'message' 		=> esc_html__('Image uploaded!', 'workreap'),
                'thumbnail' 	=> $attachment_json['thumbnail'],
                'full' 			=> $attachment_json['full'],
                'banner' 		=> $attachment_json['banner'],
                'attachment_id' => $attach_id
            );

            wp_send_json( $ajax_response );
        } else {
			
			$ajax_response['message'] = esc_html__('Image upload failed!', 'workreap');
			$ajax_response['type'] 	  = 'error';

            wp_send_json( $ajax_response );
        }
    }

    add_action('wp_ajax_workreap_image_uploader', 'workreap_image_uploader');
    add_action('wp_ajax_nopriv_workreap_image_uploader', 'workreap_image_uploader');
}

/**
 * Update freelancer Profile
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists( 'workreap_update_freelancer_profile' ) ){
    function workreap_update_freelancer_profile(){       
        global $current_user, $post;               
        $json = array();
		$user_id		 = $current_user->ID;
		$post_id  		 = workreap_get_linked_profile_id($user_id);
		
		$hide_map 		= 'show';
		if (function_exists('fw_get_db_settings_option') ) {
			$hide_map	= fw_get_db_settings_option('hide_map');
		}
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		//Skills
        $skills = !empty( $_POST['settings']['skills'] ) ? $_POST['settings']['skills'] : array();
        $skill_keys 	= array();
        $skills_new 	= array();
		$skills_term 	= array();
		$counter = 0;
        if( !empty( $skills ) ){
            foreach ($skills as $key => $value) {
                if( !in_array($value['skill'], $skill_keys ) ){
                    $skill_keys[] = $value['skill'];
                    $skills_new[$counter]['skill'][0] = $value['skill'];
                    $skills_new[$counter]['value'] = $value['value'];
					$skills_term[] = $value['skill'];
                    $counter++;
                }
			} 
			
			//Prepare Params
			$params_array['post_obj'] = $_POST;
			$params_array['user_identity'] = $current_user->ID;
			$params_array['user_role'] = apply_filters('workreap_get_user_type', $current_user->ID );
			$params_array['type'] = 'skills';

			//child theme : update extra settings
			do_action('wt_process_profile_child', $params_array);

			if( !empty($skills_term) ){
				wp_set_post_terms( $post_id, $skills_term, 'skills' );
			}
        }
		
		// Featured skills
		$feature_skills		= workreap_is_feature_value( 'wt_no_skills', $user_id);
		
		if( isset( $hide_map ) && $hide_map === 'show' ){
			$basics = array(
				'address'   => esc_html__('Address is required', 'workreap'),
				'latitude'  => esc_html__('Latitude is required', 'workreap'),
				'longitude' => esc_html__('Longitude is required', 'workreap'),
				'country'   => esc_html__('Country is required', 'workreap'),
			);
		} else{
			$basics = array(
				'country'   => esc_html__('Country is required', 'workreap'),
			);
		}


		$basics = apply_filters('workreap_field_validation', $basics);

		if(!empty($basics)) {
			foreach ($basics as $key => $value) {
				if( empty( $_POST['basics'][$key] ) ){
				 $json['type'] 		= 'error';
				 $json['message'] 	= $value;        
				 wp_send_json($json);
				}
			 }
		}

		//update languages
		if( !empty( $_POST['settings']['languages'] ) ){
			$lang		= array();
			$lang_slugs	= array();
			foreach( $_POST['settings']['languages'] as $key => $item ){
				$lang[] = $item;
				
			}
			
			if( !empty( $lang ) ){
				wp_set_post_terms($post_id, $lang, 'languages');
			}
		}

		//update english level
		if( !empty( $_POST['settings']['english_level'] ) ){
			$english_level	= sanitize_text_field( $_POST['settings']['english_level']);
			update_post_meta($post_id, '_english_level', $english_level);
			
			//Update User Profile
			if( function_exists('fw_set_db_post_option') ){
				fw_set_db_post_option($post_id, 'english_level', $english_level);
			}
		}
		
		//update freelancer type
		$freelancer_type = '';
		if( !empty( $_POST['settings']['freelancer_type'] ) ){

			$freelancer_type	=  $_POST['settings']['freelancer_type'];
			update_post_meta($post_id, '_freelancer_type', $freelancer_type);
			
			$freelancer_type_array	= !empty($freelancer_type) && is_array($freelancer_type) ? $freelancer_type : array($freelancer_type);
			do_action('workreap_update_term_taxonomy_meta', $_POST);
			wp_set_object_terms($post_id, $freelancer_type_array, 'freelancer_type');
		}
		
        //Form data
        $first_name = !empty($_POST['basics']['first_name']) ? sanitize_text_field($_POST['basics']['first_name']) : '';
        $last_name  = !empty($_POST['basics']['last_name'] ) ? sanitize_text_field($_POST['basics']['last_name']) : '';
        $gender     = !empty($_POST['basics']['gender'] ) ? $_POST['basics']['gender'] : '';
		$tag_line   = !empty($_POST['basics']['tag_line'] ) ? sanitize_text_field( $_POST['basics']['tag_line'] ) : '';
		
        $content    = !empty($_POST['basics']['content'] ) ? wp_kses_post( $_POST['basics']['content'] ) : '';
        $per_hour   = !empty($_POST['basics']['per_hour_rate']) ? intval($_POST['basics']['per_hour_rate']) : 0;        
        $address    = !empty( $_POST['basics']['address'] ) ? $_POST['basics']['address'] : '';
        $country    = !empty( $_POST['basics']['country'] ) ? $_POST['basics']['country'] : '';
        $latitude   = !empty( $_POST['basics']['latitude'] ) ? $_POST['basics']['latitude'] : '';
        $longitude  = !empty( $_POST['basics']['longitude'] ) ? $_POST['basics']['longitude'] : '';
		$display_name  = !empty( $_POST['basics']['display_name'] ) ? $_POST['basics']['display_name'] : '';

		
        
        //Update user meta
        update_user_meta($user_id, 'first_name', $first_name);
        update_user_meta($user_id, 'last_name', $last_name);
		
		if( !empty( $display_name ) ) {
			$post_title	= $display_name;
			$user_info	= array( 'ID' => $user_id, 'display_name' => $display_name );
			wp_update_user( $user_info );
		} else {
			$post_title	= esc_html( get_the_title( $post_id ));
		}
		
        //Update Freelancer Post        
        $freelancer_user = array(
            'ID'           => $post_id,
            'post_title'   => $post_title,
            'post_content' => $content,
        );

        // Update the post into the database
        wp_update_post( $freelancer_user );
		
        update_post_meta($post_id, '_gender', $gender);
        update_post_meta($post_id, '_tag_line', $tag_line);
        update_post_meta($post_id, '_perhour_rate', $per_hour);
        update_post_meta($post_id, '_address', $address);
        update_post_meta($post_id, '_country', $country);
        update_post_meta($post_id, '_latitude', $latitude);
        update_post_meta($post_id, '_longitude', $longitude);

        //Profile avatar
        $profile_avatar = array();
        if( !empty( $_POST['basics']['avatar']['attachment_id'] ) ){
            $profile_avatar = $_POST['basics']['avatar'];
        } else {                                
            if( !empty( $_POST['basics']['avatar'] ) ){
                $profile_avatar = workreap_temp_upload_to_media($_POST['basics']['avatar'], $post_id);
            }
        }
		
		//Set country for unyson
        $locations = get_term_by( 'slug', $country, 'locations' );
        $location = array();
        if( !empty( $locations ) ){
            $location[0] = $locations->term_id;
			wp_set_post_terms( $post_id, $locations->term_id, 'locations' );
        }
		
		//delete prevoius attachment ID
		$pre_attachment_id = get_post_thumbnail_id($post_id);
		if ( !empty($pre_attachment_id) && !empty( $profile_avatar['attachment_id'] ) && intval($pre_attachment_id) != intval($profile_avatar['attachment_id'])) {
			wp_delete_attachment($pre_attachment_id, true);
		}
		
		//update thumbnail
		if (!empty($profile_avatar['attachment_id'])) {
			delete_post_thumbnail($post_id);
			set_post_thumbnail($post_id, $profile_avatar['attachment_id']);
		} else {
			wp_delete_attachment( $pre_attachment_id, true );
		}   

        //Profile avatar
        $profile_banner = array();
        if( !empty( $_POST['basics']['banner']['attachment_id'] ) ){
            $profile_banner = $_POST['basics']['banner'];
        } else {                                
            if( !empty( $_POST['basics']['banner'] ) ){
                $profile_banner = workreap_temp_upload_to_media($_POST['basics']['banner'], $post_id);
            }
		}
		
		//Resume
        $profile_resume = array();
        if( !empty( $_POST['basics']['resume']['attachment_id'] ) ){
            $profile_resume = $_POST['basics']['resume'];
        } else {                                
            if( !empty( $_POST['basics']['resume'] ) ){
                $profile_resume = workreap_temp_upload_to_media($_POST['basics']['resume'], $post_id);
            }
        }   

        //Set country for unyson
        $locations = get_term_by( 'slug', $country, 'locations' );
        $location = array();
        if( !empty( $locations ) ){
            $location[0] = $locations->term_id;
        }
        
        
        update_post_meta($post_id, '_skills', $skills_new);
        
        //Experience
        $experiences = array();      
        $experience  = !empty( $_POST['settings']['experience'] ) ? $_POST['settings']['experience'] : array();        
        if( !empty( $experience ) ){
            $counter = 0;
            foreach ($experience as $key => $value) {
                if( !empty( $value['title'] ) ){
                    $experiences[$counter]['title']       = sanitize_text_field($value['title']);
                    $experiences[$counter]['company']     = sanitize_text_field($value['job']);
                    $experiences[$counter]['startdate']   = apply_filters('workreap_picker_date_format',$value['startdate'] ); 
                    $experiences[$counter]['enddate']     = apply_filters('workreap_picker_date_format',$value['enddate'] ); 
                    $experiences[$counter]['description'] = $value['details'];
                    $counter++;
                }

            }
        }
		
        update_post_meta($post_id, '_experience', $experiences);

        //Education        
        $educations = array();      
        $education  = !empty( $_POST['settings']['education'] ) ? $_POST['settings']['education'] : array();  
        if( !empty( $education ) ){
            $counter = 0;
            foreach ($education as $key => $value) {
                if( !empty( $value['degree'] ) ){
                    $educations[$counter]['title']          = sanitize_text_field($value['degree']);
                    $educations[$counter]['institute']      = sanitize_text_field($value['university']);
                    $educations[$counter]['startdate']      = apply_filters('workreap_picker_date_format',$value['startdate'] ); 
                    $educations[$counter]['enddate']        = apply_filters('workreap_picker_date_format',$value['enddate'] ); 
                    $educations[$counter]['description']    = sanitize_textarea_field($value['details']);
                    $counter++;
                }

            }
        }
		
        update_post_meta($post_id, '_educations', $educations);

        //Awards
        $awards = array();
        $award = !empty( $_POST['settings']['awards'] ) ? $_POST['settings']['awards'] : array();
		
        if( !empty( $award ) ){
            $counter = 0;
            foreach ($award as $key => $value) {
                if( !empty( $value['title'] ) ){
                    $awards[$counter]['title']     = sanitize_text_field($value['title']);
                    $awards[$counter]['date']      = $value['date'];

                    if( !empty( $value['image']['attachment_id'] ) ){
                        $awards[$counter]['image'] = $value['image'];
                    } else {                                
                        if( !empty( $value['image'] ) ){
                            $awards[$counter]['image'] = workreap_temp_upload_to_media($value['image'], $post_id);
                        }
                    }
					
                    $counter++;
                }

            }
        }
		
        update_post_meta($post_id, '_awards', $awards);

        //Projects
        $projects = array();
        $project  = !empty( $_POST['settings']['project'] ) ? $_POST['settings']['project'] : array();
        if( !empty( $project ) ){
            $counter = 0;
            foreach ($project as $key => $value) {
                if( !empty( $value['title'] ) ){
                    $projects[$counter]['title']     = sanitize_text_field($value['title']);
                    $projects[$counter]['link']      = $value['link'];
                    if( !empty( $value['image']['attachment_id'] ) ){
                        $projects[$counter]['image'] = $value['image'];
                    } else {                                
                        if( !empty( $value['image'] ) ){
                            $projects[$counter]['image'] = workreap_temp_upload_to_media($value['image'], $post_id);
                        }
                    }
					
                    $counter++;
                }

            }
        }        
        update_post_meta($post_id, '_projects', $projects);
		
		$videos = !empty( $_POST['settings']['videos'] ) ? $_POST['settings']['videos'] : array();
		
        //Fw Options
		$fw_options = array();
		$max_price   = !empty($_POST['basics']['max_price'] ) ? sanitize_text_field( $_POST['basics']['max_price'] ) : '';
		if (function_exists('fw_get_db_settings_option')) {
			$freelancer_price_option = fw_get_db_settings_option('freelancer_price_option', $default_value = null);
		}

		if(!empty($freelancer_price_option) && $freelancer_price_option === 'enable' ){
			$fw_options['max_price']     = $max_price;
			update_post_meta($post_id, '_max_price', $max_price);
		}
		
		//Profile avatar
        $profile_gallery = array();
        if( !empty( $_POST['basics']['images_gallery'] ) ){
            $fw_options['images_gallery'] 	= $_POST['basics']['images_gallery'];
		} 
		
		if( !empty( $_POST['basics']['images_gallery_new'] ) ){
			$new_index	= !empty($fw_options['images_gallery']) ?  max(array_keys($fw_options['images_gallery'])) : 0;
			foreach( $_POST['basics']['images_gallery_new'] as $new_gallery ){
				$new_index ++;
				$profile_gallery 							= workreap_temp_upload_to_media($new_gallery, $post_id);
				$fw_options['images_gallery'][$new_index]	= $profile_gallery;
			}
		 }
		
		//specializations
        $specialization = !empty( $_POST['settings']['specialization'] ) ? $_POST['settings']['specialization'] : array();
        $spec_keys 	= array();
        $specialization_new 	= array();
		$specialization_term 	= array();

		$counter = 0;
        if( !empty( $specialization ) ){
            foreach ($specialization as $key => $value) {
                if( !in_array($value['spec'], $spec_keys ) ){
                    $spec_keys[] = $value['spec'];
                    $specialization_new[$counter]['spec'][0] = $value['spec'];
                    $specialization_new[$counter]['value'] = $value['value'];
					$specialization_term[] = $value['spec'];
                    $counter++;
                }
			}
		}

		wp_set_post_terms( $post_id, $specialization_term, 'wt-specialization' );
		$fw_options['specialization']             = $specialization_new;

		//specializations
        $industrial_experiences = !empty( $_POST['settings']['industrial_experiences'] ) ? $_POST['settings']['industrial_experiences'] : array();
        $exp_keys 	= array();
        $industrial_experiences_new 	= array();
		$industrial_experiences_term 	= array();
		
		$counter = 0;
        if( !empty( $industrial_experiences ) ){
            foreach ($industrial_experiences as $key => $value) {
                if( !in_array($value['exp'], $exp_keys ) ){
                    $exp_keys[] = $value['exp'];
                    $industrial_experiences_new[$counter]['exp'][0] = $value['exp'];
                    $industrial_experiences_new[$counter]['value'] = $value['value'];
					$industrial_experiences_term[] = $value['exp'];
                    $counter++;
                }
			} 

			if( !empty($industrial_experiences_term) ){
				wp_set_post_terms( $post_id, $industrial_experiences_term, 'wt-industrial-experience' );
			}
			$fw_options['industrial_experiences']             = $industrial_experiences_new;
		}
		
		$socialmediaurls	= array();
		if( function_exists('fw_get_db_settings_option')  ){
			$socialmediaurls	= fw_get_db_settings_option('freelancer_social_profile_settings', $default_value = null);
		}

		$socialmediaurl 		= !empty($socialmediaurls['gadget']) ? $socialmediaurls['gadget'] : '';
		if(!empty($socialmediaurl) && $socialmediaurl  ==='enable'){
			$social_settings    	= function_exists('workreap_get_social_media_icons_list') ? workreap_get_social_media_icons_list('yes') : array();
			if(!empty($social_settings)) {
				foreach($social_settings as $key => $val ) {
					$enable_value   = !empty($socialmediaurls['enable'][$key]['gadget']) ? $socialmediaurls['enable'][$key]['gadget'] : '';
					if( !empty($enable_value) && $enable_value === 'enable' ){
						$social_val	= !empty($_POST['basics'][$key]) ? esc_attr($_POST['basics'][$key]) : '';
						$fw_options[$key]           = $social_val;
					}
				}
			}
		}

        $fw_options['gender']             = $gender;
        $fw_options['tag_line']           = $tag_line;
        $fw_options['_perhour_rate']      = $per_hour;
        $fw_options['address']            = $address;
        $fw_options['longitude']          = $longitude;
        $fw_options['latitude']           = $latitude;
        $fw_options['country']            = $location;
        $fw_options['skills']             = $skills_new;
        $fw_options['projects']           = $projects;
        $fw_options['awards']             = $awards;
        $fw_options['experience']         = $experiences;
        $fw_options['education']          = $educations;
        $fw_options['banner_image']       = $profile_banner;
		$fw_options['resume']       	  = $profile_resume;
		$fw_options['videos']       	  = $videos;
		$fw_options['freelancer_type']    = $freelancer_type;

        //Update User Profile
        fw_set_db_post_option($post_id, null, $fw_options);
		
		//child theme : update extra settings
		do_action('workreap_update_freelancer_profile_settings', $_POST);

		//update api key data
		if( apply_filters('workreap_filter_user_promotion', 'disable') === 'enable' ){
			do_action('workreap_update_users_marketing_attributes',$user_id,'images_gallery');
			do_action('workreap_update_users_marketing_attributes',$user_id,'experience');
			do_action('workreap_update_users_marketing_attributes',$user_id,'url');
			
		}

        $json['type']    = 'success';
        $json['message'] = esc_html__('Settings Updated.', 'workreap');        
        wp_send_json($json);
    }
            
    add_action('wp_ajax_workreap_update_freelancer_profile', 'workreap_update_freelancer_profile');
    add_action('wp_ajax_nopriv_workreap_update_freelancer_profile', 'workreap_update_freelancer_profile');
}

/**
 * Update employer Profile
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists( 'workreap_update_employer_profile' ) ){
    function workreap_update_employer_profile(){       
        global $current_user, $post;               
        $json = array();
		$user_id		 = $current_user->ID;
		$post_id  		 = workreap_get_linked_profile_id($user_id);
		
		$hide_map 		= 'show';
		if (function_exists('fw_get_db_settings_option')) {
			$hide_map	= fw_get_db_settings_option('hide_map');
		}
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		if( isset( $hide_map ) && $hide_map === 'show' ){
			$basics = array(
				'address'   => esc_html__('Address is required', 'workreap'),
				'latitude'  => esc_html__('Latitude is required', 'workreap'),
				'longitude' => esc_html__('Longitude is required', 'workreap'),
				'country'   => esc_html__('Country is required', 'workreap'),
			);
		} else{
			$basics = array(
				'country'   => esc_html__('Location is required', 'workreap'),
			);
		}

        foreach ($basics as $key => $value) {
           if( empty( $_POST['basics'][$key] ) ){
            $json['type'] = 'error';
            $json['message'] = $value;        
            wp_send_json($json);
           }
		}
		
		$comapny_name	= '';
		if( function_exists('fw_get_db_settings_option')  ){
			$comapny_name	= fw_get_db_settings_option('comapny_name', $default_value = null);
		}

		$company_job_title	= '';
		if( function_exists('fw_get_db_settings_option')  ){
			$company_job_title	= fw_get_db_settings_option('company_job_title', $default_value = null);
		}

        //Form data
        $first_name = !empty($_POST['basics']['first_name']) ? sanitize_text_field($_POST['basics']['first_name']) : '';
        $last_name  = !empty($_POST['basics']['last_name'] ) ? sanitize_text_field($_POST['basics']['last_name']) : '';
        $tag_line   = !empty($_POST['basics']['tag_line'] ) ? sanitize_text_field( $_POST['basics']['tag_line'] ) : '';
		$content    = !empty($_POST['basics']['content'] ) ? wp_kses_post( $_POST['basics']['content'] ) : '';   
		    
        $address    = !empty( $_POST['basics']['address'] ) ? $_POST['basics']['address'] : '';
        $country    = !empty( $_POST['basics']['country'] ) ? $_POST['basics']['country'] : '';
        $latitude   = !empty( $_POST['basics']['latitude'] ) ? $_POST['basics']['latitude'] : '';
        $longitude  = !empty( $_POST['basics']['longitude'] ) ? $_POST['basics']['longitude'] : '';
		
		$employees  = !empty( $_POST['employees'] ) ? $_POST['employees'] : '';
		$department  = !empty( $_POST['department'] ) ? $_POST['department'] : '';
		
		$display_name  = !empty( $_POST['basics']['display_name'] ) ? $_POST['basics']['display_name'] : '';

		$brochures     = !empty( $_POST['basics']['brochures'] ) ? $_POST['basics']['brochures'] : array();
		
		//Update user meta
        update_user_meta($user_id, 'first_name', $first_name);
        update_user_meta($user_id, 'last_name', $last_name);
		
		if( !empty( $display_name ) ) {
			$post_title	= $display_name;
			$user_info	= array( 'ID' => $user_id, 'display_name' => $display_name );
			wp_update_user( $user_info );
		} else {
			$post_title	= esc_html( get_the_title( $post_id ));
		}
        
        

        //Update Freelancer Post        
        $freelancer_user = array(
            'ID'           => $post_id,
            'post_title'   => $post_title,
            'post_content' => $content,
        );

        // Update the post into the database
        wp_update_post( $freelancer_user );
		
        update_post_meta($post_id, '_tag_line', $tag_line);
        update_post_meta($post_id, '_address', $address);
        update_post_meta($post_id, '_country', $country);
        update_post_meta($post_id, '_latitude', $latitude);
        update_post_meta($post_id, '_longitude', $longitude);
		update_post_meta($post_id, '_employees', $employees);
		
		if( !empty( $department ) ){
			$department_term = get_term_by( 'term_id', $department, 'department' );
			if( !empty( $department_term ) ){
				wp_set_post_terms( $post_id, $department, 'department' );
				update_post_meta($post_id, '_department', $department_term->slug);
			}
		}
		
        //Profile avatar
        $profile_avatar = array();
        if( !empty( $_POST['basics']['avatar']['attachment_id'] ) ){
            $profile_avatar = $_POST['basics']['avatar'];
        } else {                                
            if( !empty( $_POST['basics']['avatar'] ) ){
                $profile_avatar = workreap_temp_upload_to_media($_POST['basics']['avatar'], $post_id);
            }
        }
		
		//delete prevoius attachment ID
		$pre_attachment_id = get_post_thumbnail_id($post_id);
		if ( !empty($pre_attachment_id) && !empty( $profile_avatar['attachment_id'] ) && intval($pre_attachment_id) != intval($profile_avatar['attachment_id'])) {
			wp_delete_attachment($pre_attachment_id, true);
		}
		
		//update thumbnail
		if (!empty($profile_avatar['attachment_id'])) {
			delete_post_thumbnail($post_id);
			set_post_thumbnail($post_id, $profile_avatar['attachment_id']);
		} else {
			wp_delete_attachment( $pre_attachment_id, true );
		}   

        //Profile avatar
        $profile_banner = array();
        if( !empty( $_POST['basics']['banner']['attachment_id'] ) ){
            $profile_banner = $_POST['basics']['banner'];
        } else {                                
            if( !empty( $_POST['basics']['banner'] ) ){
                $profile_banner = workreap_temp_upload_to_media($_POST['basics']['banner'], $post_id);
            }
        }        

        //Set country for unyson
        $locations = get_term_by( 'slug', $country, 'locations' );
        $location = array();
        if( !empty( $locations ) ){
            $location[0] = $locations->term_id;
			wp_set_post_terms( $post_id, $locations->term_id, 'locations' );
        }
        //Fw Options
		$fw_options = array();
		if(!empty($comapny_name) && $comapny_name === 'enable') { 
			$company_name  = !empty( $_POST['basics']['company_name'] ) ? $_POST['basics']['company_name'] : '';
			$fw_options['comapny_name']           = $company_name;
		}
		if(!empty($company_job_title) && $company_job_title === 'enable') { 
			$job_title  = !empty( $_POST['basics']['comapny_name_title'] ) ? $_POST['basics']['comapny_name_title'] : '';
			$fw_options['company_job_title']           = $job_title;
		}
		
		$socialmediaurls	= array();
		if( function_exists('fw_get_db_settings_option')  ){
			$socialmediaurls	= fw_get_db_settings_option('employer_social_profile_settings', $default_value = null);
		}
		
		$brochure_attachemnts	= array();
		if( !empty( $brochures ) ) {
			foreach ( $brochures as $key => $value ) {
				if( !empty( $value['attachment_id'] ) ){
					$brochure_attachemnts[] = $value;
				} else{
					$brochure_attachemnts[] = workreap_temp_upload_to_media($value, $post_id);
				} 	
			}                
		}

		$socialmediaurl 		= !empty($socialmediaurls['gadget']) ? $socialmediaurls['gadget'] : '';
		if(!empty($socialmediaurl) && $socialmediaurl  ==='enable'){
			$social_settings    	= function_exists('workreap_get_social_media_icons_list') ? workreap_get_social_media_icons_list('yes') : array();
			if(!empty($social_settings)) {
				foreach($social_settings as $key => $val ) {
					$enable_value   = !empty($socialmediaurls['enable'][$key]['gadget']) ? $socialmediaurls['enable'][$key]['gadget'] : '';
					if( !empty($enable_value) && $enable_value === 'enable' ){
						$social_val	= !empty($_POST['basics'][$key]) ? esc_attr($_POST['basics'][$key]) : '';
						$fw_options[$key]           = $social_val;
					}
				}
			}
		}
		
		
        $fw_options['tag_line']           = $tag_line;
        $fw_options['address']            = $address;
        $fw_options['longitude']          = $longitude;
        $fw_options['latitude']           = $latitude;
        $fw_options['country']            = $location;
		$fw_options['department']         = array( $department );
		$fw_options['no_of_employees']    = $employees;
        $fw_options['banner_image']       = $profile_banner;
        $fw_options['brochures']       	  = $brochure_attachemnts;
		
        //Update User Profile
        fw_set_db_post_option($post_id, null, $fw_options);
		
		//child theme : update extra settings
		do_action('workreap_update_employer_profile_settings',$_POST);
		
		//update api key data
		if( apply_filters('workreap_filter_user_promotion', 'disable') === 'enable' ){	
			do_action('workreap_update_users_marketing_attributes',$user_id,'profile_photo');
			do_action('workreap_update_users_marketing_attributes',$user_id,'url');
		}
		
        $json['type']    = 'success';
        $json['message'] = esc_html__('Settings Updated.', 'workreap');        
        wp_send_json($json);
    }
            
    add_action('wp_ajax_workreap_update_employer_profile', 'workreap_update_employer_profile');
    add_action('wp_ajax_nopriv_workreap_update_employer_profile', 'workreap_update_employer_profile');
}

/**
 * delete account
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_delete_account' ) ) {

	function workreap_delete_account() {
		global $current_user;
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent

		$post_id	= workreap_get_linked_profile_id($current_user->ID);
		$user 		= wp_get_current_user(); //trace($user);
		$json 		= array();

		$do_check = check_ajax_referer('wt_account_delete_nonce', 'account_delete', false);
        if ($do_check == false) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('No kiddies please!', 'workreap');
            wp_send_json( $json );
        }
		
		$required = array(
            'password'   	=> esc_html__('Password is required', 'workreap'),
            'retype'  		=> esc_html__('Retype your password', 'workreap'),
            'reason' 		=> esc_html__('Select reason to delete your account', 'workreap'),
        );

        foreach ($required as $key => $value) {
           if( empty( $_POST['delete'][$key] ) ){
            $json['type'] = 'error';
            $json['message'] = $value;        
            wp_send_json($json);
           }
        }
		
		if (empty($_POST['delete']['password']) || empty($_POST['delete']['retype'])) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Please add your password and retype password.', 'workreap');
            wp_send_json( $json );
        }
		
		$user_name 	 = workreap_get_username($user->data->ID);
		$user_email	 = $user->user_email;
        $is_password = wp_check_password($_POST['delete']['password'], $user->user_pass, $user->data->ID);
		
	
		if( $is_password ){
			wp_delete_user($user->data->ID);
			wp_delete_post($post_id,true);
			
			extract($_POST['delete']);
			$reason		 = workreap_get_account_delete_reasons($reason);
			
			//Send email to users
			if (class_exists('Workreap_Email_helper')) {
				if (class_exists('WorkreapDeleteAccount')) {
					$email_helper = new WorkreapDeleteAccount();
					$emailData = array();
					
					$emailData['username'] 			= esc_html( $user_name );
					$emailData['reason'] 			= sanitize_textarea_field( $reason );
					$emailData['email'] 			= esc_html( $user_email );
					$emailData['description'] 		= sanitize_textarea_field( $description );
					$email_helper->send($emailData);
				}
			}

			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('You account has been deleted.', 'workreap');
			$json['redirect'] 	= esc_url(home_url('/'));
			wp_send_json( $json );
		} else{
			$json['type'] = 'error';
			$json['message'] = esc_html__('Password doesn\'t match', 'workreap');
			wp_send_json( $json );
		}
	}

	add_action( 'wp_ajax_workreap_delete_account', 'workreap_delete_account' );
	add_action( 'wp_ajax_nopriv_workreap_delete_account', 'workreap_delete_account' );
}

/**
 * Update User Password
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_change_user_password')) {

    function workreap_change_user_password() {
        global $current_user;
        $user_identity = $current_user->ID;
        $json = array();
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
        $do_check = check_ajax_referer('wt_change_password_nonce', 'change_password', false);
		
		
        if ($do_check == false) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('No kiddies please!', 'workreap');
            wp_send_json( $json );
        }
		
		$password		= sanitize_text_field ( $_POST['password'] );
		$new_password	= sanitize_text_field ( $_POST['retype'] );
		
        $user = wp_get_current_user(); //trace($user);
        $is_password = wp_check_password($password, $user->user_pass, $user->data->ID);

        if ($is_password) {

            if (empty($new_password) ) {
                $json['type'] = 'error';
                $json['message'] = esc_html__('Please add your new password.', 'workreap');
                wp_send_json( $json );
            } else if ( strlen( $new_password ) < 6 ) {
				$json['type'] 	 = 'error';
				$json['message'] = esc_html__('Password length should be minimum 6', 'workreap');
				wp_send_json( $json );
			}

            wp_update_user(array('ID' => $user_identity, 'user_pass' => sanitize_text_field($new_password)));
			$json['type'] = 'success';
			$json['message'] = esc_html__('Password Updated.', 'workreap');
        } else {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Old Password doesn\'t matched with the existing password', 'workreap');
        }

       wp_send_json( $json );
    }

    add_action('wp_ajax_workreap_change_user_password', 'workreap_change_user_password');
    add_action('wp_ajax_nopriv_workreap_change_user_password', 'workreap_change_user_password');
}

/**
 * Save account settings
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_save_account_settings')) {

    function workreap_save_account_settings() {
        global $current_user;
        $user_identity   = $current_user->ID;
		$link_id		 = workreap_get_linked_profile_id( $user_identity );
		$user_type	 	 = apply_filters('workreap_get_user_type', $user_identity );
        $json = array();
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent

		//update settings
		$settings		 = workreap_get_account_settings($user_type);
		if( !empty( $settings ) ){
			foreach( $settings as $key => $value ){
				$save_val 	= !empty( $_POST['settings'][$key] ) ? $_POST['settings'][$key] : '';
				$db_val 	= !empty( $save_val ) ?  $save_val : 'off';
				update_post_meta($link_id, $key, $db_val);
			}
		}

        $json['type'] = 'success';
		$json['message'] = esc_html__('Settings Updated.', 'workreap');

        wp_send_json( $json );
    }

    add_action('wp_ajax_workreap_save_account_settings', 'workreap_save_account_settings');
    add_action('wp_ajax_nopriv_workreap_save_account_settings', 'workreap_save_account_settings');
}
/**
 * Freelancer request
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_milstone_request' ) ) {

	function workreap_milstone_request() {
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent

		$proposal_id		= !empty($_POST['id']) ? intval($_POST['id']) : '';
		$project_id			= get_post_meta($proposal_id, '_project_id', true);

		$proposed_amount  	= get_post_meta($proposal_id, '_amount', true);

		update_post_meta( $proposal_id, '_proposal_status', 'pending' );
		update_post_meta( $proposal_id, '_proposal_type', 'milestone' );
		
		$freelancer_id				= get_post_field('post_author', $proposal_id);
		$freelancer_linked_profile	= workreap_get_linked_profile_id($freelancer_id);
		$hired_freelancer_title 	= workreap_get_username('', $freelancer_linked_profile);
		$employer_id				= get_post_field('post_author', $project_id);
		$employer_linked_profile	= workreap_get_linked_profile_id($employer_id);
		$employer_name 				= workreap_get_username('', $employer_linked_profile);
		$employer_link 				= esc_url(get_the_permalink($employer_linked_profile));
		
		$project_title				= get_the_title($project_id);
		$project_link				= get_the_permalink($project_id);

		$proposed_duration  		= get_post_meta($proposal_id, '_proposed_duration', true);
		$duration_list				= worktic_job_duration_list();
		$duration					= !empty( $duration_list[$proposed_duration] ) ? $duration_list[$proposed_duration] : '';

		$profile_id		= workreap_get_linked_profile_id($freelancer_linked_profile, 'post');
		$user_email 	= !empty( $profile_id ) ? get_userdata( $profile_id )->user_email : '';

		//Send email to freelancer
		if (class_exists('Workreap_Email_helper')) {
			if (class_exists('WorkreapMilestoneRequest')) {
				$email_helper = new WorkreapMilestoneRequest();
				$emailData = array();
				
				$emailData['freelancer_name'] 	= esc_html( $hired_freelancer_title);
				$emailData['employer_name'] 	= esc_html( $employer_name);
				$emailData['employer_link'] 	= esc_html( $employer_link);
				$emailData['project_title'] 	= esc_html( $project_title);
				$emailData['project_link'] 		= esc_html( $project_link);
				$emailData['proposal_amount'] 	= workreap_price_format($proposed_amount, 'return');
				$emailData['proposal_duration'] = esc_html( $duration);
				$emailData['email_to'] 			= esc_html( $user_email);

				$email_helper->send_milestone_request_email($emailData);
			}
		}

        $json['type'] = 'success';
		$json['message'] = esc_html__('Request sent successfully to the freelancer.', 'workreap');

        wp_send_json( $json );

	}
	add_action('wp_ajax_workreap_milstone_request', 'workreap_milstone_request');
    add_action('wp_ajax_nopriv_workreap_milstone_request', 'workreap_milstone_request');
}

/**
 * Freelancer request
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_cancelled_milestone' ) ) {

	function workreap_cancelled_milestone() {
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		global $current_user;

		$proposal_id			= !empty($_POST['proposal_id']) ? intval($_POST['proposal_id']) : '';
		$project_id				= get_post_meta($proposal_id, '_project_id', true);
		$cancelled_reason		= !empty($_POST['cancelled_reason']) ? ($_POST['cancelled_reason']) : '';
		$json					= array();
		$update_post			= array();

		if(empty($proposal_id)){
			$json['type'] = 'error';
            $json['message'] = esc_html__('Proposal ID is required', 'workreap');
            wp_send_json( $json );
		}

		if(empty($cancelled_reason)){
			$json['type'] = 'error';
            $json['message'] = esc_html__('Cancelled reason is required', 'workreap');
            wp_send_json( $json );
		}

		if(!empty($proposal_id) && !empty($cancelled_reason)) {
			update_post_meta( $proposal_id, '_cancelled_reason', $cancelled_reason );
			update_post_meta( $proposal_id, '_proposal_status', 'cancelled' );
			update_post_meta( $proposal_id, '_cancelled_user_id', $current_user->ID );
			$update_post	= array(
								'ID'    		=>  $proposal_id,
								'post_status'   =>  'cancelled'
							);	
			wp_update_post($update_post);

			$freelancer_id				= get_post_field('post_author', $proposal_id);
			$freelancer_linked_profile	= workreap_get_linked_profile_id($freelancer_id);
			$hired_freelancer_title 	= workreap_get_username('', $freelancer_linked_profile );
			$freelancer_link 		    = esc_url(get_the_permalink($freelancer_linked_profile));


			$employer_id				= get_post_field('post_author', $project_id);
			$employer_linked_profile	= workreap_get_linked_profile_id($employer_id);
			$employer_name 				= workreap_get_username('', $employer_linked_profile );
			
			$project_title				= get_the_title($project_id);
			$project_link				= get_the_permalink($project_id);

			$profile_id					= workreap_get_linked_profile_id($employer_linked_profile, 'post');
			$user_email 				= !empty( $profile_id ) ? get_userdata( $profile_id )->user_email : '';

			//Send email to employer
			if (class_exists('Workreap_Email_helper')) {
				if (class_exists('WorkreapMilestoneRequest')) {
					$email_helper = new WorkreapMilestoneRequest();
					$emailData = array();
					
					$emailData['freelancer_name'] 	= esc_html( $hired_freelancer_title);
					$emailData['freelancer_link'] 	= esc_html( $freelancer_link);
					$emailData['employer_name'] 	= esc_html( $employer_name);
					$emailData['project_title'] 	= esc_html( $project_title);
					$emailData['project_link'] 		= esc_html( $project_link);
					$emailData['reason'] 			= esc_html( $cancelled_reason);

					$emailData['email_to'] 			= esc_html( $user_email);

					$email_helper->send_milestone_request_rejected_email($emailData);

				}
			}
			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('Settings Updated.', 'workreap');
			wp_send_json( $json );
		}
	}
	add_action('wp_ajax_workreap_cancelled_milestone', 'workreap_cancelled_milestone');
    add_action('wp_ajax_nopriv_workreap_cancelled_milestone', 'workreap_cancelled_milestone');
}

/**
 * Freelancer request
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_milstone_completed' ) ) {

	function workreap_milstone_completed() {

		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent

		$json 			= array();
		$current_date 	= current_time('mysql');
		$milestone_id	= !empty($_POST['id']) ? intval($_POST['id']) : '';
		$completed_date	= date('Y-m-d H:i:s', strtotime($current_date));
		
		$milestone_title 	= get_the_title($milestone_id);

		$project_id 		= get_post_meta($milestone_id, '_project_id', true);
		$freelancer_id 		= get_post_meta($project_id, '_freelancer_id', true);

		$freelancer_name 	= workreap_get_username('', $freelancer_id);

		$profile_id			= workreap_get_linked_profile_id($freelancer_id, 'post');	
		
		$user_email 		= !empty( $profile_id ) ? get_userdata( $profile_id )->user_email : '';
		
		$update		= array( 'status' 		=> 'completed' );
		$where		= array( 'milestone_id' => $milestone_id );
		workreap_update_earning( $where, $update, 'wt_earnings');

		// complete service
		$order_id			= get_post_meta($milestone_id,'_order_id',true);
		if ( class_exists('WooCommerce') && !empty( $order_id )) {
			$order = wc_get_order( intval($order_id ) );
			if( !empty( $order ) ) {
				$order->update_status( 'completed' );
			}
		}

		update_post_meta( $milestone_id, '_status', 'completed' );
		update_post_meta( $milestone_id, '_completed_date', $completed_date );
		
		$project_title		= get_the_title($project_id);
		$project_link		= get_the_permalink($project_id);
		
		

		//Send email to freelancer
		if (class_exists('Workreap_Email_helper')) {
			if (class_exists('WorkreapMilestoneRequest')) {
				$email_helper = new WorkreapMilestoneRequest();
				$emailData = array();
				
				$emailData['freelancer_name'] 	= esc_html( $freelancer_name);
				$emailData['milestone_title'] 	= esc_html( $milestone_title);
				$emailData['project_title'] 	= esc_html( $project_title);
				$emailData['project_link'] 		= esc_html( $project_link);
				$emailData['email_to'] 			= esc_html( $user_email);

				$email_helper->send_completed_milestone_to_freelancer_email($emailData);

			}
		}

		$json['type'] 		= 'success';
		$json['message'] 	= esc_html__('Milestone is completed successfully', 'workreap');
		wp_send_json($json);

	}
	add_action('wp_ajax_workreap_milstone_completed', 'workreap_milstone_completed');
    add_action('wp_ajax_nopriv_workreap_milstone_completed', 'workreap_milstone_completed');
}

/**
 * Freelancer request
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_milstone_checkout' ) ) {

	function workreap_milstone_checkout() {
		global $woocommerce,$current_user;
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$milestone_id	= !empty($_POST['id']) ? intval($_POST['id']) : '';
		$bk_settings	= worrketic_hiring_payment_setting();
		$price_symbol		= workreap_get_current_currency();
		
		if( isset( $bk_settings['type'] ) && ($bk_settings['type'] === 'woo' || $bk_settings['type'] === 'offline_woo') ) {
			$product_id	= workreap_get_hired_product_id();
			if( !empty( $product_id )) {
				if ( class_exists('WooCommerce') ) {

					$woocommerce->cart->empty_cart(); //empty cart before update cart
					$user_id			= $current_user->ID;
					$job_id				= get_post_meta($milestone_id ,'_project_id',true);
					$price				= get_post_meta($milestone_id ,'_price',true);
					$admin_shares 		= 0.0;
					$freelancer_shares 	= 0.0;
					
					if( !empty( $price ) ){
						if( isset( $bk_settings['percentage'] ) && $bk_settings['percentage'] > 0 ){
							$admin_shares 		= $price/100*$bk_settings['percentage'];
							$freelancer_shares 	= $price - $admin_shares;
							$admin_shares 		= number_format($admin_shares,2,'.', '');
							$freelancer_shares 	= number_format($freelancer_shares,2,'.', '');
						} else{
							$admin_shares 		= 0.0;
							$freelancer_shares 	= $price;
							$admin_shares 		= number_format($admin_shares,2,'.', '');
							$freelancer_shares 	= number_format($freelancer_shares,2,'.', '');
						}
					}
					
					$cart_meta['project_id']		= $job_id;
					$cart_meta['price']				= $price;
					$cart_meta['milestone_id']		= $milestone_id;
					
					$cart_data = array(
						'product_id' 		=> $product_id,
						'cart_data'     	=> $cart_meta,
						'price'				=> $price_symbol['symbol'].$price,
						'payment_type'     	=> 'milestone',
						'admin_shares'     	=> $admin_shares,
						'freelancer_shares' => $freelancer_shares,
					);
					
					$woocommerce->cart->empty_cart();
					$cart_item_data = $cart_data;
					WC()->cart->add_to_cart($product_id, 1, null, null, $cart_item_data);

					$json['type'] 			= 'checkout';
					$json['message'] 		= esc_html__('Please wait you are redirecting to the checkout page.', 'workreap');
					$json['checkout_url']	= wc_get_checkout_url();
					wp_send_json($json);
				} else {
					$json['type'] 		= 'error';
					$json['message'] 	= esc_html__('Please install WooCommerce plugin to process this order', 'workreap');
					wp_send_json($json);
				}
			} else{
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('Hiring settings is missing, please contact to administrator.', 'workreap');
				wp_send_json($json);
			}
		}  else{
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__('Some error occur, please try again later', 'workreap');
			wp_send_json($json);
		}

	}
	add_action('wp_ajax_workreap_milstone_checkout', 'workreap_milstone_checkout');
    add_action('wp_ajax_nopriv_workreap_milstone_checkout', 'workreap_milstone_checkout');
}

/**
 * Freelancer request approved
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_milstone_request_approved' ) ) {

	function workreap_milstone_request_approved() {
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent

		$json = array();
		$proposal_id		= !empty($_POST['id']) ? intval($_POST['id']) : '';
		$status				= !empty($_POST['status']) ? $_POST['status'] : '';
		
		if(!empty($status) && $status === 'approved' ){
			$args 			= array(
								'posts_per_page' 	=> -1,
								'post_type' 		=> 'wt-milestone',
								'suppress_filters' 	=> false
							);
			$meta_query_args[] = array(
								'key' 		=> '_propsal_id',
								'value' 	=> $proposal_id,
								'compare' 	=> '='
							);
			$query_relation 	= array('relation' => 'AND',);
			$args['meta_query'] = array_merge($query_relation, $meta_query_args);
			$query 				= new WP_Query($args);
			
			while ($query->have_posts()) : $query->the_post();
				global $post;
				update_post_meta( $post->ID, '_status', 'pay_now' );
			endwhile;
			
			wp_reset_postdata();

			$project_id	= get_post_meta( $proposal_id, '_project_id', true );
			if(!empty($proposal_id) && !empty($project_id)){
				workreap_hired_freelancer_after_payment($project_id, $proposal_id);
			}

			$freelancer_id				= get_post_field('post_author', $proposal_id);
			$freelancer_linked_profile	= workreap_get_linked_profile_id($freelancer_id);
			$hired_freelancer_title 	= workreap_get_username('', $freelancer_linked_profile);
			$freelancer_link 		    = esc_url(get_the_permalink($freelancer_linked_profile));


			$employer_id				= get_post_field('post_author', $project_id);
			$employer_linked_profile	= workreap_get_linked_profile_id($employer_id);
			$employer_name 				= workreap_get_username('', $employer_linked_profile );
			
			$project_title				= get_the_title($project_id);
			$project_link				= get_the_permalink($project_id);

			$profile_id					= workreap_get_linked_profile_id($employer_linked_profile, 'post');
			$user_email 				= !empty( $profile_id ) ? get_userdata( $profile_id )->user_email : '';
		}
		
		update_post_meta( $proposal_id, '_proposal_status', $status );

		//Send email to freelancer
		if (class_exists('Workreap_Email_helper')) {
			if (class_exists('WorkreapMilestoneRequest')) {
				$email_helper = new WorkreapMilestoneRequest();
				$emailData = array();
				
				$emailData['freelancer_name'] 	= esc_html($hired_freelancer_title);
				$emailData['freelancer_link'] 	= esc_html($freelancer_link);
				$emailData['employer_name'] 	= esc_html($employer_name);
				$emailData['project_title'] 	= esc_html($project_title);
				$emailData['project_link'] 		= esc_html($project_link);

				$emailData['email_to'] 			= esc_html( $user_email);

				$email_helper->send_milestone_request_approved_email($emailData);
			}
		}
		$json['type'] 		= 'success';
		$json['message'] 	= esc_html__('You have successfully update proposal request.', 'workreap');
		wp_send_json($json);

	}
	add_action('wp_ajax_workreap_milstone_request_approved', 'workreap_milstone_request_approved');
    add_action('wp_ajax_nopriv_workreap_milstone_request_approved', 'workreap_milstone_request_approved');
}
/**
 * Post a job
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_save_milstone' ) ) {

	function workreap_save_milstone() {
		global $current_user;
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$json	= array();
		$required = array(
			'id'   				=> esc_html__('Proposal is required', 'workreap'),
			'title'   			=> esc_html__('Milestone title is required', 'workreap'),
			'due_date'  		=> esc_html__('Due date is required', 'workreap'),
			'price'  			=> esc_html__('Price is required', 'workreap')
		);
		
		foreach ($required as $key => $value) {
			if( empty( $_POST[$key] ) ){
				$json['type'] 		= 'error';
				$json['message'] 	= $value;        
				wp_send_json($json);
			}
		}

		$proposal_id	= !empty($_POST['id']) ? intval($_POST['id']) : '';
		$milstone_id	= !empty($_POST['milestone_id']) ? intval($_POST['milestone_id']) : '';
		$project_id		= !empty($proposal_id) ? get_post_meta($proposal_id,'_project_id',true) : '';
		$price			= !empty($_POST['price']) ? $_POST['price'] : '';
		$due_date		= !empty($_POST['due_date']) ? $_POST['due_date'] : '';
		$title			= !empty($_POST['title']) ? $_POST['title'] : '';
		$description	= !empty($_POST['description']) ? $_POST['description'] : '';
		
		$proposal_price					= get_post_meta( $proposal_id, '_amount', true );
		$proposal_price					= !empty($proposal_price) ? $proposal_price : 0;
		$total_milestone_price			= workreap_get_milestone_statistics($proposal_id,array('pending','publish'));
		$total_milestone_price			= !empty($total_milestone_price) ? $total_milestone_price : 0;
		$remaning_price	= ($proposal_price) > ($total_milestone_price) ? $proposal_price - $total_milestone_price : 0;
		
		$remaning_price	= (string) $remaning_price;
		
		if( ( $price > $remaning_price) && empty($milstone_id) ){
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__('Price is greater then remaining price','workreap');       
			wp_send_json($json);
		} else if(!empty($milstone_id)){
			$old_price	= get_post_meta($milstone_id,'_price',true);
			$old_price	= !empty($old_price) ? $old_price : 0;
			$new_price	= $old_price+ $remaning_price;
			
			if( empty($remaning_price) && $price > $old_price ) {
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('Price is greater then remaining price','workreap');        
				wp_send_json($json);

			} else if($price > $new_price ){
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('Price is greater then remaining price','workreap');        
				wp_send_json($json);
			}
		}

		if(empty($milstone_id)) {
			$milestone_post = array(
				'post_title'    => wp_strip_all_tags( $title ),
				'post_status'   => 'pending',
				'post_content'  => $description,
				'post_author'   => $current_user->ID,
				'post_type'     => 'wt-milestone',
			);

			$milstone_id    		= wp_insert_post( $milestone_post );
			update_post_meta( $milstone_id, '_status', 'pending' );
		} else if( !empty($milstone_id) ) {
			$milestone_post = array(
				'ID'			=> $milstone_id,
				'post_title'    => wp_strip_all_tags( $title ),
				'post_content'  => $description,
				'post_type'     => 'wt-milestone',
			);
			
			wp_update_post( $milestone_post );
		}
		
		if(!empty($milstone_id )){
			$freelancer_id			= get_post_field('post_author', $proposal_id);
			
			$fw_options	= array();
			$fw_options['projects']	= $project_id;
			$fw_options['price']	= $price;
			$fw_options['due_date']	= $due_date;
			fw_set_db_post_option($milstone_id, null, $fw_options);

			update_post_meta($milstone_id,'_freelancer_id',$freelancer_id);
			update_post_meta($milstone_id,'_propsal_id',$proposal_id);
			update_post_meta($milstone_id,'_project_id',$project_id);
			update_post_meta($milstone_id,'_price',$price);
			update_post_meta($milstone_id,'_due_date',$due_date);

		}

		if(!empty($milstone_id)){
			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('You are successfully update/added Milestone.','workreap');
			wp_send_json( $json );
		} else {
			$json['type'] 		= 'error';
            $json['message'] 	= esc_html__('There are some errors, please try again later', 'workreap');
            wp_send_json( $json );
		}

	}
	add_action('wp_ajax_workreap_save_milstone', 'workreap_save_milstone');
    add_action('wp_ajax_nopriv_workreap_save_milstone', 'workreap_save_milstone');

}


/**
 * Post a job
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_check_post_author_status' ) ) {
	add_action('workreap_check_post_author_status', 'workreap_check_post_author_status', 10, 1);
	function workreap_check_post_author_status($postid) {
		$is_verified		= get_post_meta($postid, '_is_verified', true);
		$profile_blocked	= get_post_meta($postid, '_profile_blocked', true);
		
		if( empty( $is_verified ) || $is_verified === 'no' ){
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__('Your account is not verified, so you cannot post anything.','workreap');
			wp_send_json( $json );
		} else if( !empty( $profile_blocked ) && $profile_blocked === 'on' ){
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__('Your account is temporarily blocked, so you cannot post anything.','workreap');
			wp_send_json( $json );
		}
	}
}

/**
 * Post a job
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_post_job' ) ) {

	function workreap_post_job() {
		global $current_user;
		$hide_map 		= 'show';

		if (function_exists('fw_get_db_settings_option')) {
			$hide_map	= fw_get_db_settings_option('hide_map');
			// $job_status	= fw_get_db_settings_option('job_status');
		}
		
		// $job_status	=  !empty( $job_status ) ? $job_status : 'publish';
        $job_status = 'not_paid';

		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$user_id	= workreap_get_linked_profile_id($current_user->ID);
		do_action('workreap_check_post_author_status', $user_id); //check if user is not blocked or deactive
		
		$json = array();
		$current = !empty($_POST['id']) ? intval($_POST['id']) : '';

		if( apply_filters('workreap_is_job_posting_allowed','wt_jobs', $current_user->ID) === false && empty($current) ){
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__('You’ve consumed all you points to add new job.','workreap');
			wp_send_json( $json );
		}
		
		if( isset( $hide_map ) && $hide_map === 'show' ){
			$required = array(
				'title'   			=> esc_html__('Job title is required', 'workreap'),
				'description'       => esc_html__('Job Details are required', 'workreap'),
				// 'project_cost'      => esc_html__('Project cost is required', 'workreap'),
				'skills'            => esc_html__('Project skills are required', 'workreap'),
				// 'project_level'  	=> esc_html__('Project level is required', 'workreap'),
				// 'project_duration'  => esc_html__('Project duration is required', 'workreap'),
				// 'english_level'   	=> esc_html__('English level is required', 'workreap'),
				// 'project_type' 		=> esc_html__('Please select job type.', 'workreap'),
				'categories' 		=> esc_html__('Please select at-least one category', 'workreap'),
				// 'address'   => esc_html__('Address is required', 'workreap'),
				// 'latitude'  => esc_html__('Latitude is required', 'workreap'),
				// 'longitude' => esc_html__('Longitude is required', 'workreap'),
				// 'country'   => esc_html__('Country is required', 'workreap'),
			);
		} else{
			$required = array(
				'title'   			=> esc_html__('Job title is required', 'workreap'),
				'description'       => esc_html__('Job Details are required', 'workreap'),
				// 'project_cost'      => esc_html__('Project cost is required', 'workreap'),
				'skills'            => esc_html__('Project skills are required', 'workreap'),
				// 'project_level'  	=> esc_html__('Project level is required', 'workreap'),
				// 'project_duration'  => esc_html__('Project duration is required', 'workreap'),
				// 'english_level'   	=> esc_html__('English level is required', 'workreap'),
				// 'project_type' 		=> esc_html__('Please select job type.', 'workreap'),
				'categories' 		=> esc_html__('Please select at-least one category', 'workreap'),
				// 'country'   => esc_html__('Country is required', 'workreap'),
			);
		}
		
		$required	= apply_filters('workreap_filter_post_job_fields',$required);
		
		if (function_exists('fw_get_db_settings_option')) {
			$job_option_setting         = fw_get_db_settings_option('job_option', $default_value = null);
			$multiselect_freelancertype  = fw_get_db_settings_option('multiselect_freelancertype', $default_value = null);
			$job_experience_single  	= fw_get_db_settings_option('job_experience_option', $default_value = null);
			$job_price_option           = fw_get_db_settings_option('job_price_option', $default_value = null);
			$milestone         			= fw_get_db_settings_option('job_milestone_option', $default_value = null);
		}
		
		$multiselect_freelancertype = !empty($multiselect_freelancertype) ?  $multiselect_freelancertype: '';
		$job_price_option 			= !empty($job_price_option) ? $job_price_option : '';
		$job_option_setting 		= !empty($job_option_setting) ? $job_option_setting : '';
		$milestone					= !empty($milestone['gadget']) ? $milestone['gadget'] : '';

		if(!empty($job_option_setting) && $job_option_setting === 'enable' ){
			$required['job_option']	= esc_html__('Project location type is required', 'workreap');
		}
		
        foreach ($required as $key => $value) {
			if( empty( $_POST['job'][$key] ) ){
				$json['type'] = 'error';
				$json['message'] = $value;        
				wp_send_json($json);
			}
			
			if( $key === 'project_type' && $_POST['job']['project_type'] === 'hourly' && empty( $_POST['job']['hourly_rate'] )  ){
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('Per hour rate is required', 'workreap');        
				wp_send_json($json);
			} else if( $key === 'project_type' && $_POST['job']['project_type'] === 'hourly' && empty( $_POST['job']['estimated_hours'] )  ){
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('Estimated hours is required', 'workreap');        
				wp_send_json($json);
			} else if( $key == 'project_type' && $_POST['job']['project_type'] === 'hourly' && !empty( $_POST['job']['max_price'] ) && $_POST['job']['max_price'] < $_POST['job']['hourly_rate'] ){
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('Maximum project cost should not be less than minimum project cost', 'workreap');        
				wp_send_json($json);
			} else if( $key == 'project_type' && $_POST['job']['project_type'] === 'fixed' && !empty( $_POST['job']['max_price'] ) && $_POST['job']['max_price'] < $_POST['job']['project_cost'] ){
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('Maximum project cost should not be less than minimum project cost', 'workreap');        
				wp_send_json($json);
			// } else if( $key == 'project_type' && $_POST['job']['project_type'] === 'fixed' && empty( $_POST['job']['project_cost'] )  ){
			// 	$json['type'] 		= 'error';
			// 	$json['message'] 	= esc_html__('Project cost is required', 'workreap');        
			// 	wp_send_json($json);
			}

		}
	
		//extract the job variables
		extract($_POST['job']);
		$title				= !empty( $title ) ? $title : rand(1,999999);		
		
		if( isset( $_POST['submit_type'] ) && $_POST['submit_type'] === 'update' ){
			$current = !empty($_POST['id']) ? intval($_POST['id']) : '';
			
			$post_author = get_post_field('post_author', $current);
            $post_id 	 = $current;
            $status 	 = get_post_status($post_id);
			
			if( intval( $post_author ) === intval( $current_user->ID ) ){
				$article_post = array(
					'ID' => $current,
					'post_title' => $title,
					'post_content' => $description,
					'post_status' => $status,
				);

				wp_update_post($article_post);
			} else{
				$json['type'] = 'error';
				$json['message'] = esc_html__('Some error occur, please try again later', 'workreap');
				wp_send_json( $json );
			}
			
		} else{
			//Create Post
			$user_post = array(
				'post_title'    => wp_strip_all_tags( $title ),
				'post_status'   => $job_status,
				'post_content'  => $description,
				'post_author'   => $current_user->ID,
				'post_type'     => 'projects',
			);

			$post_id    		= wp_insert_post( $user_post );
			update_post_meta( $post_id, '_featured_job_string',0 );

			//update api key data
			if( apply_filters('workreap_filter_user_promotion', 'disable') === 'enable' ){	
				do_action('workreap_update_users_marketing_attributes',$user_id,'posted_projects');
			}

			//update jobs
			$remaning_jobs		= workreap_get_subscription_metadata( 'wt_jobs',intval($current_user->ID) );
			$remaning_jobs  	= !empty( $remaning_jobs ) ? intval($remaning_jobs) : 0;

			if( !empty( $remaning_jobs ) && $remaning_jobs >= 1 ) {
				$update_jobs	= intval( $remaning_jobs ) - 1 ;
				$update_jobs	= intval($update_jobs);

				$wt_subscription 	= get_user_meta(intval($current_user->ID), 'wt_subscription', true);
				$wt_subscription	= !empty( $wt_subscription ) ?  $wt_subscription : array();
				$wt_subscription['wt_jobs'] = $update_jobs;
				update_user_meta( intval($current_user->ID), 'wt_subscription', $wt_subscription);
			}

			$expiry_string		= workreap_get_subscription_metadata( 'subscription_featured_string',$current_user->ID );

			if( !empty($expiry_string) ) {
				update_post_meta($post_id, '_expiry_string', $expiry_string);
			}
		}
		
		
		if( $post_id ){
			//Upload files from temp folder to uploads
			$files              = !empty( $_POST['job']['project_documents'] ) ? $_POST['job']['project_documents'] : array();
			$job_files			= array();
			if( !empty( $files ) ) {
				foreach ( $files as $key => $value ) {
					if( !empty( $value['attachment_id'] ) ){
						$job_files[] = $value;
					} else{
						$job_files[] = workreap_temp_upload_to_media($value, $post_id);
					} 	
				}                
			}

			
			$languages               = !empty( $_POST['job']['languages'] ) ? $_POST['job']['languages'] : array();
			$categories              = !empty( $_POST['job']['categories'] ) ? $_POST['job']['categories'] : array();
			$skills              	 = !empty( $_POST['job']['skills'] ) ? $_POST['job']['skills'] : array();
			$expiry_date             = !empty( $_POST['job']['expiry_date'] ) ? $_POST['job']['expiry_date'] : '';
			$deadline             	 = !empty( $_POST['job']['deadline'] ) ? $_POST['job']['deadline'] : '';
			
			$is_featured              = !empty( $_POST['job']['is_featured'] ) ? $_POST['job']['is_featured'] : '';
			if( !empty($is_featured) ){
				if( $is_featured === 'on'){
					$is_featured_job	= get_post_meta($post_id,'_featured_job_string',true); 
					if(empty($is_featured_job)){
						$featured_jobs	= workreap_featured_job( $current_user->ID );
						if( $featured_jobs ) {
							update_post_meta($post_id, '_featured_job_string', 1);
							$remaning_featured_jobs		= workreap_get_subscription_metadata( 'wt_featured_jobs',intval($current_user->ID) );
							$remaning_featured_jobs  	= !empty( $remaning_featured_jobs ) ? intval($remaning_featured_jobs) : 0;

							if( !empty( $remaning_featured_jobs) && $remaning_featured_jobs >= 1 ) {
								$update_featured_jobs	= intval( $remaning_featured_jobs ) - 1 ;
								$update_featured_jobs	= intval( $update_featured_jobs );
								$wt_subscription 	= get_user_meta(intval($current_user->ID), 'wt_subscription', true);
								$wt_subscription	= !empty( $wt_subscription ) ?  $wt_subscription : array();
								$wt_subscription['wt_featured_jobs'] = $update_featured_jobs;

								update_user_meta( intval($current_user->ID), 'wt_subscription', $wt_subscription);
							}
						} else{
							update_post_meta( $post_id, '_featured_job_string',0 );
						}
					}
				} else {
					update_post_meta( $post_id, '_featured_job_string',0 );
				}
			} else{
				update_post_meta( $post_id, '_featured_job_string',0 );
			}

			//update langs
			// wp_set_post_terms( $post_id, $languages, 'languages' );
			
			//update cats
			wp_set_post_terms( $post_id, $categories, 'project_cat' );

			//update skills
			wp_set_post_terms( $post_id, $skills, 'skills' );

			// price range
			if(!empty($job_price_option) && $job_price_option === 'enable' ){
				update_post_meta($post_id, '_max_price', $max_price);
			}

			// update projec expriences
			if(!empty($job_experience_single['gadget']) && $job_experience_single['gadget'] === 'enable' ){
				$experiences		= !empty( $_POST['job']['experiences'] ) ? $_POST['job']['experiences'] : array();
				wp_set_post_terms( $post_id, $experiences, 'project_experience' );
			}
			
			//update
			// update_post_meta($post_id, '_expiry_date', $expiry_date);
			// update_post_meta($post_id, 'deadline', $deadline);
			// update_post_meta($post_id, '_project_type', $project_type);
			// update_post_meta($post_id, '_project_duration', $project_duration);
			// update_post_meta($post_id, '_english_level', $english_level); 

			// update_post_meta($post_id, '_estimated_hours', $estimated_hours);
			// update_post_meta($post_id, '_hourly_rate', $hourly_rate);
			// update_post_meta($post_id, '_project_cost', $project_cost);


			$project_data	= array(); 
			$project_data['gadget']	= !empty( $_POST['job']['project_type'] ) ? $_POST['job']['project_type'] : 'fixed';
			$project_data['hourly']['hourly_rate']		= !empty( $_POST['job']['hourly_rate'] ) ? $_POST['job']['hourly_rate'] : '';
			$project_data['hourly']['estimated_hours']	= !empty( $_POST['job']['estimated_hours'] ) ? $_POST['job']['estimated_hours'] : '';
			$project_data['fixed']['project_cost']		= !empty( $_POST['job']['project_cost'] ) ? $_POST['job']['project_cost'] : '';
			
			$project_data['hourly']['max_price']		= !empty( $_POST['job']['max_price'] ) ? $_POST['job']['max_price'] : '';
			$project_data['fixed']['max_price']			= !empty( $_POST['job']['max_price'] ) ? $_POST['job']['max_price'] : '';

			//update location
			// $address    = !empty( $_POST['job']['address'] ) ? $_POST['job']['address'] : '';
			// $country    = !empty( $_POST['job']['country'] ) ? $_POST['job']['country'] : '';
			// $latitude   = !empty( $_POST['job']['latitude'] ) ? $_POST['job']['latitude'] : '';
			// $longitude  = !empty( $_POST['job']['longitude'] ) ? $_POST['job']['longitude'] : '';
			
			// update_post_meta($post_id, '_address', $address);
			// update_post_meta($post_id, '_country', $country);
			// update_post_meta($post_id, '_latitude', $latitude);
			// update_post_meta($post_id, '_longitude', $longitude);
			

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
			
			if(!empty($job_price_option) && $job_price_option === 'enable' ){
				$fw_options['max_price']         	 = $max_price;
			}

			$freelancer_level	= !empty( $_POST['job']['freelancer_level'] ) ? $_POST['job']['freelancer_level']  : array();
			if(!empty($multiselect_freelancertype) && $multiselect_freelancertype === 'enable' ){
				$fw_options['freelancer_level']      = $freelancer_level;
			} else {
				$freelancer_level					= !empty($freelancer_level[0]) ? $freelancer_level[0] : '';
				$fw_options['freelancer_level'][0]  = $freelancer_level;
			}

			if( !empty($milestone) && $milestone ==='enable' && !empty($project_data['gadget']) && $project_data['gadget'] ==='fixed' ){
				$is_milestone    			= !empty( $_POST['job']['is_milestone'] ) ? $_POST['job']['is_milestone'] : 'off';
				$project_data['project_type']['fixed']['milestone']  	= $is_milestone;
				update_post_meta($post_id, '_milestone', $is_milestone);
			}

			// update post option
			if( !empty($job_option_setting) && $job_option_setting === 'enable' ){
				$job_option_text						= !empty( $_POST['job']['job_option'] ) ? $_POST['job']['job_option'] : '';
				$fw_options['job_option']    			= $job_option_text;
				update_post_meta($post_id, '_job_option', $job_option_text);
			}

			update_post_meta($post_id, '_freelancer_level', $freelancer_level);
			
			$fw_options['expiry_date']         	 = $expiry_date;
			$fw_options['deadline']         	 = $deadline;
			$fw_options['project_level']         = $project_level;
			$fw_options['project_type']          = $project_data;
			// $fw_options['project_duration']      = $project_duration;
			// $fw_options['english_level']         = $english_level;
			$fw_options['show_attachments']      = $show_attachments;
			$fw_options['project_documents']     = $job_files;
			$fw_options['address']            	 = $address;
			$fw_options['longitude']          	 = $longitude;
			$fw_options['latitude']           	 = $latitude;
			$fw_options['country']            	 = $location;


			//Update User Profile
			fw_set_db_post_option($post_id, null, $fw_options);
			
            // redirect to bundle selection page
            $job_bundles_tpl      = fw_get_db_settings_option('job_bundles_tpl');
            if(empty($job_bundles_tpl)) {
                $json['type']         = 'error';
                $json['message']      = esc_html__('Please specify the bundle selection page in dashboard.', 'workreap');
                wp_send_json($json);
            }

            $json['type']         = 'redirect';
            $json['message']      = esc_html__('Please wait while you are redirecting to bundle selection page.', 'workreap');
            $json['redirect_url'] = add_query_arg('project', $post_id, get_permalink((int) $job_bundles_tpl[0]));
            wp_send_json($json);

            // TODO to be moved to another function
			if( isset( $_POST['submit_type'] ) && $_POST['submit_type'] === 'update' ){
				$json['type'] 		= 'success';
				$json['url'] 		= Workreap_Profile_Menu::workreap_profile_menu_link('jobs', $current_user->ID, true,'posted');
				$json['message'] 	= esc_html__('Your job has been updated', 'workreap');
			} else{
				//Send email to users
				if (class_exists('Workreap_Email_helper')) {
					if (class_exists('WorkreapJobPost')) {
						$email_helper = new WorkreapJobPost();
						$emailData 	  = array();

						$employer_name 		= workreap_get_username($current_user->ID);
						$employer_email 	= get_userdata( $current_user->ID )->user_email;
						$employer_profile 	= get_permalink($user_id);
						$job_title 			= esc_html( get_the_title($post_id) );
						$job_link 			= get_permalink($post_id);
						

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
	
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Your job has been posted.', 'workreap');
			}
			
			$json['url'] 		= Workreap_Profile_Menu::workreap_profile_menu_link('jobs', $current_user->ID, true,'posted');
			
			//add custom data
			do_action('workreap_post_job_extra_data',$_POST,$post_id);

			//Prepare Params
			$params_array['user_identity'] = $current_user->ID;
			$params_array['user_role'] = apply_filters('workreap_get_user_type', $current_user->ID );
			$params_array['type'] = 'project_create';
			
			do_action('wt_process_job_child', $params_array);
			
			wp_send_json( $json );
		} else {
			$json['type'] = 'error';
			$json['message'] = esc_html__('Some error occur, please try again later', 'workreap');
			wp_send_json( $json );
		}

	}

	add_action( 'wp_ajax_workreap_post_job', 'workreap_post_job' );
	add_action( 'wp_ajax_nopriv_workreap_post_job', 'workreap_post_job' );
}

/**
 * Post a job
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_select_project_bundle' ) ) {

    function workreap_select_project_bundle() {

        global $current_user;

        $user_id    = $current_user->ID;
        $project_id = intval( $_POST['project_id'] );
        $bundle_id  = intval( $_POST['bundle_id'] );

        // check project id
        if( empty($project_id) || $project_id == 0 || empty($bundle_id) || $bundle_id == 0 ){
            $json['type'] = 'error';
            $json['message'] = esc_html__('Error proceeding with the bundle selection', 'workreap');
            wp_send_json($json);
        }
        $project = get_post($project_id);
        if($project->post_type != 'projects' || $project->post_status != 'not_paid' || $project->post_author != $user_id) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Error proceeding with the bundle selection', 'workreap');
            wp_send_json($json);
        }
        $bundle = get_post($bundle_id);
        if($bundle->post_type != 'bundles' || $bundle->post_status != 'publish') {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Error proceeding with the bundle selection', 'workreap');
            wp_send_json($json);
        }

        // set project cost
        $project_category = wp_get_post_terms($project_id, 'project_cat', array('fields' => 'ids'))[0];
        $price = intval(fw_get_db_post_option($bundle_id, 'price_cat_' . $project_category));
        update_post_meta($project_id, '_project_cost', $price);

        $db_project_type = fw_get_db_post_option($project_id, 'project_type');
        $db_project_type['fixed']['project_cost'] = $price;
        fw_set_db_post_option($project_id, 'project_type', $db_project_type);

        // prepare checkout process
        $bk_settings = worrketic_hiring_payment_setting();
        $product_id = workreap_get_posting_job_product_id();
        if( !empty( $product_id )) {
            if ( class_exists('WooCommerce') ) {
                global $woocommerce;

                $price_symbol       = workreap_get_current_currency();
                $admin_shares       = 0.0;
                $freelancer_shares  = 0.0;
                
                if( !empty( $price ) ){
                    if( isset( $bk_settings['percentage'] ) && $bk_settings['percentage'] > 0 ){
                        $admin_shares       = $price * $bk_settings['percentage'] / 100;
                        $freelancer_shares  = $price - $admin_shares;
                        $admin_shares       = number_format($admin_shares, 2, '.', '');
                        $freelancer_shares  = number_format($freelancer_shares, 2, '.', '');
                    } else{
                        $admin_shares       = 0.0;
                        $freelancer_shares  = $price;
                        $admin_shares       = number_format($admin_shares, 2, '.', '');
                        $freelancer_shares  = number_format($freelancer_shares, 2, '.', '');
                    }
                }
                
                $cart_meta['project_id'] = $project_id;
                $cart_meta['price']      = $price;
                
                $cart_data = array(
                    'product_id'        => $product_id,
                    'cart_data'         => $cart_meta,
                    'price'             => $price_symbol['symbol'].$price,
                    'payment_type'      => 'posting_job',
                    'admin_shares'      => $admin_shares,
                    'freelancer_shares' => $freelancer_shares,
                );

                $woocommerce->cart->empty_cart();
                $cart_item_data = $cart_data;
                WC()->cart->add_to_cart($product_id, 1, null, null, $cart_item_data);

                $json['type']           = 'checkout';
                $json['message']        = esc_html__('Please wait you are redirecting to the checkout page.', 'workreap');
                $json['checkout_url']   = wc_get_checkout_url();
                wp_send_json($json);
            } else {
                $json['type']       = 'error';
                $json['message']    = esc_html__('Please install WooCommerce plugin to process this order', 'workreap');
                wp_send_json($json);
            }
        } else {
            $json['type']       = 'error';
            $json['message']    = esc_html__('Please add posting job product', 'workreap');
            wp_send_json($json);
        }
    }

    add_action( 'wp_ajax_workreap_select_project_bundle', 'workreap_select_project_bundle' );
    add_action( 'wp_ajax_nopriv_workreap_select_project_bundle', 'workreap_select_project_bundle' );
}

/**
 * submit project comment
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists( 'workreap_submit_project_chat' ) ){
    function workreap_submit_project_chat(){
		global $current_user;
    	$user_id 		= $current_user->ID; 
    	$user_email 	= $current_user->user_email;  
    	$author 		= workreap_get_username($user_id);
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		if ( apply_filters('workreap_get_user_type', $user_id) === 'employer' ){
			$employer_post_id   		= get_user_meta($user_id, '_linked_profile', true);
    		$avatar = apply_filters(
		        'workreap_employer_avatar_fallback', workreap_get_employer_avatar(array('width' => 100, 'height' => 100), $employer_post_id), array('width' => 100, 'height' => 100) 
			);
    	} else {
			$freelancer_post_id   		= get_user_meta($user_id, '_linked_profile', true);
    		$avatar = apply_filters(
				'workreap_freelancer_avatar_fallback', workreap_get_freelancer_avatar(array('width' => 100, 'height' => 100), $freelancer_post_id), array('width' => 100, 'height' => 100) 
			);
    	}    	
    	
        $json = array();

        //Form Validation
       	if( empty( $_POST['id'] ) || empty( $_POST['chat_desc'] ) ){
       		$json['type'] = 'error';
       		$json['message'] = esc_html__('Message is required.', 'workreap');
       		wp_send_json($json);
       	}

       	$post_id 	= !empty( $_POST['id'] ) ? $_POST['id'] : '';     	
    	$temp_items = !empty( $_POST['temp_files']) ? ($_POST['temp_files']) : array();
    	$content 	= !empty( $_POST['chat_desc'] ) ? $_POST['chat_desc'] : ''; 
		
		$post_type	= get_post_type($post_id);

		//Upload files from temp folder to uploads
		$project_files = array();
        if( !empty( $temp_items ) ) {
            foreach ( $temp_items as $key => $value ) {
                $project_files[] = workreap_temp_upload_to_media($value, $post_id);
            }                
		}
		
		if( isset( $post_type ) && $post_type === 'services-orders' ){
			$project_id 				= get_post_meta( $post_id, '_service_id', true);
			$hired_freelance_id 		= get_post_field('post_author', $project_id);
			$freelancer_id				= workreap_get_linked_profile_id($hired_freelance_id);
			$employer_id				= get_post_field('post_author', $post_id);
		} else{
			$project_id 				= get_post_meta( $post_id, '_project_id', true);
			$freelancer_id 				= get_post_meta( $project_id, '_freelancer_id', true);
			$hired_freelance_id			= get_post_field('post_author', $post_id);
			$employer_id				= get_post_field('post_author', $project_id);
		}

		$time = current_time('mysql');
						
		$data = array(
		    'comment_post_ID' 		=> $post_id,
		    'comment_author' 		=> $author,
		    'comment_author_email' 	=> $user_email,
		    'comment_author_url' 	=> 'http://',
		    'comment_content' 		=> $content,
		    'comment_type' 			=> '',
		    'comment_parent' 		=> 0,
		    'user_id' 				=> $user_id,
		    'comment_date' 			=> $time,
		    'comment_approved' 		=> 1,
		);

		$comment_id = wp_insert_comment($data);
		
		if( !empty( $comment_id ) ) {	
			$is_files	= 'no';
			if( !empty( $project_files )) {
				$is_files	= 'yes';
				add_comment_meta($comment_id, 'message_files', $project_files);		
			}
			
			if( isset( $post_type ) && $post_type === 'services-orders' ){
				if($user_type === 'employer'){
					$receiver_id = $hired_freelance_id;
				} else{
					$receiver_id = $employer_id;
				}

				//Send email to users
				if (class_exists('Workreap_Email_helper')) {
					if (class_exists('WorkreapServiceMessage')) {
						$email_helper = new WorkreapServiceMessage();
						$emailData = array();

						$employer_name 		= workreap_get_username($employer_id);
						$employer_profile 	= get_permalink(workreap_get_linked_profile_id($employer_id));

						$job_title 			= esc_html( get_the_title($project_id) );
						$job_link 			= get_permalink($project_id);

						$freelancer_link 	= get_permalink($freelancer_id);
						$freelancer_title 	= esc_html( get_the_title($freelancer_id));

						$freelancer_email 	= get_userdata( $hired_freelance_id )->user_email;
						$employer_email 	= get_userdata( $employer_id )->user_email;


						$emailData['employer_name'] 		= esc_html( $employer_name );
						$emailData['employer_link'] 		= esc_url( $employer_profile );
						$emailData['employer_email'] 		= sanitize_email( $employer_email );

						$emailData['freelancer_link']       = esc_url( $freelancer_link );
						$emailData['freelancer_name']       = esc_html( $freelancer_title );
						$emailData['freelancer_email']      = sanitize_email( $freelancer_email );

						$emailData['service_title'] 		= esc_html( $job_title );
						$emailData['service_link'] 			= esc_url( $job_link );
						$emailData['service_msg']			= esc_textarea( $content );

						if ( apply_filters('workreap_get_user_type', $user_id) === 'employer' ){
							$email_helper->send_service_message_freelancer($emailData);
						} else{
							$email_helper->send_service_message_employer($emailData);
						}

					}
				}
				
			} else{
				if($user_type === 'employer'){
					$receiver_id = $hired_freelance_id;
				} else{
					$receiver_id = $employer_id;
				}
				
				//Send email to users
				if (class_exists('Workreap_Email_helper')) {
					if (class_exists('WorkreapProposalMessage')) {
						$email_helper = new WorkreapProposalMessage();
						$emailData = array();

						$employer_name 		= workreap_get_username($employer_id);
						$employer_profile 	= get_permalink(workreap_get_linked_profile_id($employer_id));

						$job_title 			= esc_html( get_the_title($project_id) );
						$job_link 			= get_permalink($project_id);

						$freelancer_link 	= get_permalink($freelancer_id);
						$freelancer_title 	= esc_html( get_the_title($freelancer_id));

						$freelancer_email 	= get_userdata( $hired_freelance_id )->user_email;
						$employer_email 	= get_userdata( $employer_id )->user_email;


						$emailData['employer_name'] 		= esc_html( $employer_name );
						$emailData['employer_link'] 		= esc_url( $employer_profile );
						$emailData['employer_email'] 		= sanitize_email( $employer_email );

						$emailData['freelancer_link']       = esc_url( $freelancer_link );
						$emailData['freelancer_name']       = esc_html( $freelancer_title );
						$emailData['freelancer_email']      = sanitize_email( $freelancer_email );

						$emailData['job_title'] 			= esc_html( $job_title );
						$emailData['job_link'] 				= esc_url( $job_link );
						$emailData['proposal_msg']			= $content;
						
						if ( apply_filters('workreap_get_user_type', $user_id) == 'employer' ){
							$email_helper->send_proposal_message_freelancer($emailData);
						} else{
							$email_helper->send_proposal_message_employer($emailData);
						}

					}
				}
			}
			
			$json['comment_id']			= $comment_id;
			$json['user_id']			= intval( $user_id );
			$json['receiver_id']		= intval( $receiver_id );
			$json['type'] 				= 'success';
			$json['message'] 			= esc_html__('Your message has sent.', 'workreap');
			$json['content_message'] 	= esc_html( wp_strip_all_tags( $content ) );
			$json['user_name'] 			= $author;
			$json['is_files'] 			= $is_files;
			$json['date'] 				= date(get_option('date_format'), strtotime($time));
			$json['img'] 				= $avatar;
			wp_send_json($json);
		}
    	
    	$json['type'] = 'error';
		$json['message'] = esc_html__('Something went wrong please try again', 'workreap');
		wp_send_json($json);
      
    }
    add_action('wp_ajax_workreap_submit_project_chat', 'workreap_submit_project_chat');
    add_action('wp_ajax_nopriv_workreap_submit_project_chat', 'workreap_submit_project_chat');
}

/**
 * Download attachment chat
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */

if( !function_exists( 'workreap_download_chat_attachments' ) ){
	function workreap_download_chat_attachments(){
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$json = array();
		$attachment_id	=  !empty( $_POST['comments_id'] ) ? intval($_POST['comments_id']) : '';
		if( empty( $attachment_id ) ){
			$json['type'] = 'error';
			$json['message'] = esc_html__('No kiddies please', 'workreap');
			wp_send_json($json);
		} else {
			$project_files = get_comment_meta( $attachment_id, 'message_files', true);
			if( !empty( $project_files ) ){
				if( class_exists('ZipArchive') ){
					$zip = new ZipArchive();
					$uploadspath	= wp_upload_dir();
					$folderRalativePath = $uploadspath['baseurl']."/downloades";
					$folderAbsolutePath = $uploadspath['basedir']."/downloades";
					wp_mkdir_p($folderAbsolutePath);
					$filename	= round(microtime(true)).'.zip';
					$zip_name = $folderAbsolutePath.'/'.$filename; 
					$zip->open($zip_name,  ZipArchive::CREATE);
					$download_url	= $folderRalativePath.'/'.$filename;

					foreach($project_files as $key => $value) {	
						$file_url	= $value['url'];
						$response	= wp_remote_get( $file_url );
						$filedata   = wp_remote_retrieve_body( $response );
						$zip->addFromString(basename( $file_url ), $filedata);
					}
					$zip->close();
				}else{
					$json['type'] = 'error';
					$json['message'] = esc_html__('Zip library is not installed on the server, please contact to hosting provider', 'workreap');
					wp_send_json($json);
				}
			}
			
			$json['type'] = 'success';
			$json['attachment'] = $download_url;
			$json['message'] = esc_html__('Downloads successfully.', 'workreap');
			wp_send_json($json);
		}
	}
	add_action('wp_ajax_workreap_download_chat_attachments', 'workreap_download_chat_attachments');
    add_action('wp_ajax_nopriv_workreap_download_chat_attachments', 'workreap_download_chat_attachments');
}

/**
 * Download Downloadable files
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */

if( !function_exists( 'workreap_download_downloadable_files' ) ){
	function workreap_download_downloadable_files(){
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$json 		= array();
		$service_id	=  !empty( $_POST['id'] ) ? intval($_POST['id']) : '';
		if( empty( $service_id ) ){
			$json['type'] = 'error';
			$json['message'] = esc_html__('No kiddies please', 'workreap');
			wp_send_json($json);
		} else {
			$downloadable_files		= get_post_meta( $service_id, '_downloadable_files', true);
			$downloadable_files		= !empty( $downloadable_files ) ? $downloadable_files : array();
			if( !empty( $downloadable_files ) ){
				$zip = new ZipArchive();
				$uploadspath	= wp_upload_dir();
				$folderRalativePath = $uploadspath['baseurl']."/downloades";
				$folderAbsolutePath = $uploadspath['basedir']."/downloades";
				wp_mkdir_p($folderAbsolutePath);
				$filename	= round(microtime(true)).'.zip';
				$zip_name = $folderAbsolutePath.'/'.$filename; 
				$zip->open($zip_name,  ZipArchive::CREATE);
				$download_url	= $folderRalativePath.'/'.$filename;

				foreach($downloadable_files as $key => $value) {	
					$file_url	= $value['url'];
					$response	= wp_remote_get( $file_url );
					$filedata   = wp_remote_retrieve_body( $response );
					$zip->addFromString(basename( $file_url ), $filedata);
				}
				$zip->close();
			}
			
			$json['type'] 		= 'success';
			$json['attachment'] = $download_url;
			$json['message'] 	= esc_html__('Downloads successfully.', 'workreap');
			wp_send_json($json);
		}
	}
	add_action('wp_ajax_workreap_download_downloadable_files', 'workreap_download_downloadable_files');
    add_action('wp_ajax_nopriv_workreap_download_downloadable_files', 'workreap_download_downloadable_files');
}

/**
 * Cancel Project
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */

if( !function_exists( 'workreap_cancel_project' ) ){
	function workreap_cancel_project(){
		global $current_user, $wpdb, $woocommerce;
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$json 				= array();
		$project_id			=  !empty( $_POST['project_id'] ) ? intval($_POST['project_id']) : '';
		$cancelled_reason	=  !empty( $_POST['cancelled_reason'] ) ? $_POST['cancelled_reason'] : '';
		
		if( empty( $project_id ) || empty( $cancelled_reason ) ){
			$json['type'] = 'error';
			$json['message'] = esc_html__('No kiddies please', 'workreap');
			wp_send_json($json);
		} else {
			$proposal_id 			= get_post_meta( $project_id, '_proposal_id', true);
			$freelancer_id 			= get_post_meta( $project_id, '_freelancer_id', true);
			$hired_freelance_id		= get_post_field('post_author', $proposal_id);
			
			delete_post_meta( $project_id, '_proposal_id', $proposal_id );
			delete_post_meta( $project_id, '_freelancer_id', $freelancer_id );
			add_post_meta( $proposal_id, '_cancelled_reason', $cancelled_reason );
			add_post_meta( $project_id, '_cancelled_proposal_id', $proposal_id );
			add_post_meta( $proposal_id, '_employer_user_id', $current_user->ID );
			
			$project_post_data 	= array(
				'ID'            => $project_id,
				'post_status'   => 'cancelled',
			);
  			wp_update_post( $project_post_data );
			$proposal_post_data 	= array(
				'ID'            => $proposal_id,
				'post_status'   => 'cancelled',
			);

			wp_update_post( $proposal_post_data );
			
			//update earnings
			
			$table_name = $wpdb->prefix . 'wt_earnings';
			$e_query		= $wpdb->prepare("SELECT * FROM `$table_name` where user_id = %d and project_id = %d",$hired_freelance_id,$project_id);
			$earnings		= $wpdb->get_results($e_query,OBJECT ); 
			
			if( !empty( $earnings ) ) {
				foreach($earnings as $earning ){
					$update		= array( 'status' => 'cancelled' );
					$where		= array( 'id' 	=> $earning->id );
					workreap_update_earning( $where, $update, 'wt_earnings');
					
					if ( class_exists('WooCommerce') ) {
						$order = wc_get_order( intval( $earning->order_id ) );
						if( !empty( $order ) ) {
							$order->update_status( 'cancelled' );
						}
					}
				}
					
			}
			
			//Send email to users
			if (class_exists('Workreap_Email_helper')) {
				if (class_exists('WorkreapCancelJob')) {
					$email_helper = new WorkreapCancelJob();
					$emailData = array();

					$employer_name 		= workreap_get_username($current_user->ID);
					$employer_profile 	= get_permalink(workreap_get_linked_profile_id($current_user->ID));
					$job_title 			= esc_html( get_the_title($project_id));
					$job_link 			= get_permalink($project_id);
					$freelancer_link 	= get_permalink(workreap_get_linked_profile_id($hired_freelance_id));
					$freelancer_title 	= esc_html( get_the_title(workreap_get_linked_profile_id($hired_freelance_id)) );
					$freelancer_email 	= get_userdata( $hired_freelance_id )->user_email;
					
					$emailData['employer_name'] 		= esc_html( $employer_name );
					$emailData['employer_link'] 		= esc_url( $employer_profile );
					$emailData['freelancer_link']       = esc_url( $freelancer_link );
					$emailData['freelancer_name']       = esc_html( $freelancer_title );
					$emailData['email_to']      		= sanitize_email( $freelancer_email );
					$emailData['job_title'] 			= esc_html( $job_title );
					$emailData['job_link'] 				= esc_url( $job_link );
					$emailData['cancel_msg'] 			= esc_textarea($cancelled_reason);

					$email_helper->send_job_cancel_email($emailData);
				}
			}
			
			$json['url'] 		= Workreap_Profile_Menu::workreap_profile_menu_link('jobs', $current_user->ID, true,'cancelled');
			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('Project cancelled successfully.', 'workreap');
			wp_send_json($json);
		}
	}
	add_action('wp_ajax_workreap_cancel_project', 'workreap_cancel_project');
    add_action('wp_ajax_nopriv_workreap_cancel_project', 'workreap_cancel_project');
}


/**
 * Cancel Project from posted projects
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */

if( !function_exists( 'workreap_cancel_job' ) ){
	function workreap_cancel_job(){
		global $current_user, $wpdb, $woocommerce;
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$json 				= array();
		$project_id			= !empty( $_POST['project_id'] ) ? intval($_POST['project_id']) : '';
		
		if( empty( $project_id ) ){
			$json['type'] = 'error';
			$json['message'] = esc_html__('No kiddies please', 'workreap');
			wp_send_json($json);
		} else {
			
			$project_post_data 	= array(
				'ID'            => $project_id,
				'post_status'   => 'cancelled',
			);
			
  			wp_update_post( $project_post_data );
			
			$json['url'] 		= Workreap_Profile_Menu::workreap_profile_menu_link('jobs', $current_user->ID, true,'cancelled');
			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('Project has been cancelled.', 'workreap');
			wp_send_json($json);
		}
	}
	add_action('wp_ajax_workreap_cancel_job', 'workreap_cancel_job');
    add_action('wp_ajax_nopriv_workreap_cancel_job', 'workreap_cancel_job');
}

/**
 * Complete Project with reviews
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists( 'workreap_complete_project' ) ){
	function workreap_complete_project(){
		global $current_user,$wpdb;
		$json 					= array();
		$where					= array();
		$update					= array();
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent

		$rating_headings		= workreap_project_ratings();
		$project_id				= !empty( $_POST['project_id'] ) ? intval($_POST['project_id']) : '';
		$contents 				= !empty( $_POST['feedback_description'] ) ? sanitize_textarea_field($_POST['feedback_description']) : '';
		$reviews 				= !empty( $_POST['feedback'] ) ? $_POST['feedback'] : array();
		
		if( empty( $contents ) || empty( $project_id ) ){
			$json['type'] 		= 'error';
			
			if( empty( $contents ) ) {
				$json['message'] 	= esc_html__('Feedback detail is required field', 'workreap');	
			} 
			
			wp_send_json($json);
			
		} else {
			$employer_id		= get_post_field('post_author',$project_id);
			$proposal_id		= get_post_meta( $project_id, '_proposal_id', true);
			$freelance_id		= get_post_field('post_author',$proposal_id);
			$review_title		= esc_html( get_the_title($proposal_id) );

			$user_reviews = array(
				'posts_per_page' 	=> 1,
				'post_type' 		=> 'reviews',
				'post_status' 		=> 'any',
				'author' 			=> $freelance_id,
				'meta_key' 			=> '_project_id',
				'meta_value' 		=> $project_id,
				'meta_compare' 		=> "=",
				'orderby' 			=> 'meta_value',
				'order' 			=> 'ASC',
			);

			$reviews_query = new WP_Query($user_reviews);
			$reviews_count = $reviews_query->post_count;
			
			if (isset($reviews_count) && $reviews_count > 0) {
				$json['type'] = 'error';
				$json['message'] = esc_html__('You have already submit a review.', 'workreap');
				wp_send_json($json);
			}

			$review_post = array(
                'post_title' 		=> $review_title,
                'post_status' 		=> 'publish',
                'post_content' 		=> $contents,
                'post_author' 		=> $freelance_id,
                'post_type' 		=> 'reviews',
                'post_date' 		=> current_time('Y-m-d H:i:s')
            );

            $post_id = wp_insert_post($review_post);
			
			/* Get the rating headings */
            $rating_evaluation 			= workreap_project_ratings();
            $rating_evaluation_count 	= !empty($rating_evaluation) ? workreap_count_items($rating_evaluation) : 0;
			
			$review_extra_meta = array();
			$rating 		= 0;
			$user_rating 	= 0;
			
            if (!empty($rating_evaluation)) {
                foreach ($rating_evaluation as $slug => $label) {
                    if (isset($reviews[$slug])) {
                        $review_extra_meta[$slug] = esc_attr($reviews[$slug]);
                        update_post_meta($post_id, $slug, esc_attr($reviews[$slug]));
                        $rating += (int) $reviews[$slug];
                    }
                }
            }
			
			update_post_meta($post_id, '_project_id', $project_id);
			update_post_meta($post_id, '_proposal_id', $proposal_id);
			if( !empty( $rating ) ){
				$user_rating = $rating / $rating_evaluation_count;
			}
			
			$employer_profile_id 	= workreap_get_linked_profile_id( $employer_id );
			$freelance_profile_id 	= workreap_get_linked_profile_id( $freelance_id );
			
            $user_rating 			= number_format((float) $user_rating, 2, '.', '');
			$review_meta 			= array(
                'user_rating' 		=> $user_rating,
                'user_from' 		=> $employer_profile_id,
                'user_to' 			=> $freelance_profile_id,
                'review_date' 		=> current_time('Y-m-d H:i:s'),
            );
			
			$review_meta = array_merge($review_meta, $review_extra_meta);

            //Update post meta
            foreach ($review_meta as $key => $value) {
                update_post_meta($post_id, $key, $value);
            }
			
			$review_meta['user_from'] 	= array($employer_profile_id);
            $review_meta['user_to'] 	= array($freelance_profile_id);

            $new_values = $review_meta;
            if (isset($post_id) && !empty($post_id)) {
                fw_set_db_post_option($post_id, null, $new_values);
            }

            /* Update avarage rating in user table */

			$table_review = $wpdb->prefix . "posts";
			$table_meta   = $wpdb->prefix . "postmeta";

			$db_rating_query = $wpdb->get_row( "
				SELECT  p.ID,
				SUM( pm2.meta_value ) AS db_rating,
				count( p.ID ) AS db_total
				FROM   ".$table_review." p 
				LEFT JOIN ".$table_meta." pm1 ON (pm1.post_id = p.ID  AND pm1.meta_key = 'user_to') 
				LEFT JOIN ".$table_meta." pm2 ON (pm2.post_id = p.ID  AND pm2.meta_key = 'user_rating')
				WHERE post_status = 'publish'
				AND pm1.meta_value    = ".$freelance_profile_id."
				AND p.post_type = 'reviews'
			",ARRAY_A);
			
			$user_rating 	= '0';
			
			if( empty( $db_rating_query ) ){
				$user_db_reviews['wt_average_rating'] 			= 0;
				$user_db_reviews['wt_total_rating'] 			= 0;
				$user_db_reviews['wt_total_percentage'] 		= 0;
				$user_db_reviews['wt_rating_count'] 			= 0;
			} else{
				
				$rating			= !empty( $db_rating_query['db_rating'] ) ? $db_rating_query['db_rating']/$db_rating_query['db_total'] : 0;
				$user_rating 	= number_format((float) $rating, 2, '.', '');
				
				$user_db_reviews['wt_average_rating'] 			= $user_rating;
				$user_db_reviews['wt_total_rating'] 			= !empty( $db_rating_query['db_total'] ) ? $db_rating_query['db_total'] : '';
				$user_db_reviews['wt_total_percentage'] 		= $user_rating * 20;
				$user_db_reviews['wt_rating_count'] 			= !empty( $db_rating_query['db_rating'] ) ? $db_rating_query['db_rating'] : '';
			}

			update_post_meta($freelance_profile_id, 'review_data', $user_db_reviews);
			update_post_meta($freelance_profile_id, 'rating_filter', $user_rating);

			$project_post_data 	= array(
				'ID'            => $project_id,
				'post_status'   => 'completed',
			);
			
			wp_update_post( $project_post_data );
			
			$order_id			= get_post_meta($proposal_id,'_order_id',true);
			if ( class_exists('WooCommerce') && !empty( $order_id )) {
				$order = wc_get_order( intval($order_id ) );
				if( !empty( $order ) ) {
					$order->update_status( 'completed' );
				}
			}
			
			//update api key data
			if( apply_filters('workreap_filter_user_promotion', 'disable') === 'enable' ){	
				do_action('workreap_update_users_marketing_attributes',$current_user->ID,'last_completed_date');
			}

			$proposal_id 	= get_post_meta( $project_id, '_proposal_id', true);
			update_post_meta($proposal_id, '_employer_user_id', $current_user->ID);
			
			//update earning
			$where		= array('project_id' => $project_id, 'user_id' => $freelance_id);
			$update		= array('status' => 'completed');
			
			workreap_update_earning( $where, $update, 'wt_earnings');
			
			//Send email to users
			if (class_exists('Workreap_Email_helper')) {
				if (class_exists('WorkreapJobCompleted')) {
					$email_helper = new WorkreapJobCompleted();
					$emailData 	  = array();

					$job_title 			= esc_html( get_the_title($project_id) );
					$job_link 			= get_permalink($project_id);
					$employer_name 		= workreap_get_username($current_user->ID);
					$employer_profile 	= get_permalink(workreap_get_linked_profile_id($current_user->ID));
					$freelancer_link 	= get_permalink($freelance_profile_id );
					$freelancer_title 	= esc_html( get_the_title($freelance_profile_id ) );
					$freelancer_email 	= get_userdata( $freelance_id )->user_email;

						
					$emailData['employer_name'] 		= esc_html( $employer_name );
					$emailData['employer_link'] 		= esc_url( $employer_profile );
					$emailData['freelancer_name']       = esc_html( $freelancer_title );
					$emailData['freelancer_link']       = esc_url( $freelancer_link );
					$emailData['freelancer_email']      = sanitize_email( $freelancer_email );
					$emailData['project_title'] 		= esc_html( $job_title );
					$emailData['ratings'] 				= esc_html( $user_rating );
					$emailData['project_link'] 			= esc_url( $job_link );
					$emailData['message'] 				= sanitize_textarea_field( $contents );

					$email_helper->send_job_completed_email_admin($emailData);
					$email_helper->send_job_completed_email_freelancer($emailData);
				}
			}

			$json['url'] 		= Workreap_Profile_Menu::workreap_profile_menu_link('jobs', $current_user->ID, true,'completed');
			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('Project completed successfully.', 'workreap');
			wp_send_json($json);
			
		}
	}
	add_action('wp_ajax_workreap_complete_project', 'workreap_complete_project');
    add_action('wp_ajax_nopriv_workreap_complete_project', 'workreap_complete_project');
}

/**
 * hire freelancer for job reopen
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_job_reopen' ) ) {

	function workreap_job_reopen() {
		$json				= array();
		$project_id			= !empty( $_POST['project_id'] ) ? intval( $_POST['project_id'] ) : '';
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		if( !empty($project_id) ){
			$project_post_data = array(
				'ID'            => $project_id,
				'post_status'   => 'publish'
			);
  			wp_update_post( $project_post_data );
			$json['type'] = 'success';
            $json['message'] = esc_html__('Job reopened successfully.', 'workreap');
            wp_send_json($json);
		} else{
			$json['type'] = 'error';
            $json['message'] = esc_html__('Some error occur, please try again later', 'workreap');
            wp_send_json($json);
		}		
	}

	add_action( 'wp_ajax_workreap_job_reopen', 'workreap_job_reopen' );
	add_action( 'wp_ajax_nopriv_workreap_job_reopen', 'workreap_job_reopen' );
}

/**
 * hire freelancer for job post
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_hire_freelancer' ) ) {

    function workreap_hire_freelancer() {
        global $current_user, $woocommerce;
        $json           = array();
        $job_id         = !empty( $_POST['job_post_id'] ) ? intval( $_POST['job_post_id'] ) : '';
        $proposal_id    = !empty( $_POST['proposal_id'] ) ? intval( $_POST['proposal_id'] ) : '';

        if( function_exists('workreap_is_demo_site') ) { 
            workreap_is_demo_site() ;
        }; //if demo site then prevent
        
        if( !empty($job_id) && !empty($proposal_id) ) {
            // prepare the hiring data
            $order_id       = get_post_meta( $job_id, '_order_id', true );
            $order          = wc_get_order( $order_id );
            $items          = $order->get_items();
            $item_id        = array_key_first( $items );
            $order_detail   = wc_get_order_item_meta( $item_id, 'cus_woo_product_data', true );
            $order_detail['proposal_id'] = $proposal_id;
            wc_update_order_item_meta( $item_id, 'cus_woo_product_data', $order_detail );

            workreap_update_hiring_data($order_id);
            
            // hire the freelancer
            workreap_hired_freelancer_after_payment($job_id, $proposal_id); 

            // update api key data
            if( apply_filters('workreap_filter_user_promotion', 'disable') === 'enable' ){  
                do_action('workreap_update_users_marketing_product_creation', $current_user->ID, $job_id, 'product_status_update');
            }          
            $json['type']       = 'success';
            $json['message']    = esc_html__('Freelancer has hired successfully.', 'workreap');
            wp_send_json($json);
        } else {
            $json['type']       = 'error';
            $json['message']    = esc_html__('Some error occur, please try again later', 'workreap');
            wp_send_json($json);
        }
    }

    add_action( 'wp_ajax_workreap_hire_freelancer', 'workreap_hire_freelancer' );
    add_action( 'wp_ajax_nopriv_workreap_hire_freelancer', 'workreap_hire_freelancer' );
}

/**
 * send feedback to freelancer job proposal
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_send_proposal_feedback' ) ) {

	function workreap_send_proposal_feedback() {
		global $current_user, $woocommerce;
		$json			  = array();
		$proposal_id	  = !empty( $_POST['proposal_id'] ) ? intval( $_POST['proposal_id'] ) : '';
        $feedback_message = !empty( $_POST['feedback_msg'] ) ? esc_attr( $_POST['feedback_msg'] ) : '';
        $feedback_rating  = !empty( $_POST['feedback_rating'] ) ? intval( $_POST['feedback_rating'] ) : '';

		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site();
		}; //if demo site then prevent

        if( empty($feedback_message) ) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Feedback message is missing', 'workreap');
            wp_send_json($json);
        } elseif ( empty($feedback_rating) ) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Feedback rating is missing', 'workreap');
            wp_send_json($json);
        } elseif ( empty($proposal_id) ) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Some error occur, please try again later', 'workreap');
            wp_send_json($json);
        }

        update_post_meta( $proposal_id, '_feedback', $feedback_message );
        update_post_meta( $proposal_id, '_feedback_rating', $feedback_rating );
        $json['type'] = 'success';
        $json['message'] = esc_html__('Feedback sent successfully.', 'workreap');
        wp_send_json($json);
	}

	add_action( 'wp_ajax_workreap_send_proposal_feedback', 'workreap_send_proposal_feedback' );
	add_action( 'wp_ajax_nopriv_workreap_send_proposal_feedback', 'workreap_send_proposal_feedback' );
}

/**
 * download job attachments
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_download_attachments' ) ) {

	function workreap_download_attachments() {
		$json			=  array();
		$job_id			= !empty( $_POST['job_post_id'] ) ? intval( $_POST['job_post_id'] ) : '';
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
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
				} else {
					$json['type'] 		= 'error';
					$json['message'] 	= esc_html__('No file attached.', 'workreap');
					wp_send_json($json);
				}
			}

			$json['type'] 		= 'success';
			$json['attachment'] = $download_url;
            $json['message'] 	= esc_html__('Downloaded successfully.', 'workreap');
            wp_send_json($json);
		} else{
			$json['type'] 		= 'error';
            $json['message'] 	= esc_html__('Some error occur, please try again later', 'workreap');
            wp_send_json($json);
		}		
	}

	add_action( 'wp_ajax_workreap_download_attachments', 'workreap_download_attachments' );
	add_action( 'wp_ajax_nopriv_workreap_download_attachments', 'workreap_download_attachments' );
}

/**
 * hire Remove single Save item
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_remove_save_item' ) ) {

	function workreap_remove_save_item() {
		$json			= array();
		$post_id		= !empty( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';
		$item_id		= !empty( $_POST['item_id'] ) ? array(intval( $_POST['item_id'] )) : array();
		$item_type		= !empty( $_POST['item_type'] ) ? sanitize_text_field( $_POST['item_type'] ) : '';
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		if( !empty($post_id) && !empty($item_type) && !empty(item_id) ){
			$save_projects_ids	= get_post_meta( $post_id, $item_type, true);
			$updated_values 	= array_diff(  $save_projects_ids , $item_id);
			update_post_meta( $post_id, $item_type, $updated_values );
			
			$json['type'] 		= 'success';
            $json['message'] 	= esc_html__('Remove save item successfully.', 'workreap');
            wp_send_json($json);
		} else{
			$json['type'] 		= 'error';
            $json['message'] 	= esc_html__('Some error occur, please try again later', 'workreap');
            wp_send_json($json);
		}		
	}

	add_action( 'wp_ajax_workreap_remove_save_item', 'workreap_remove_save_item' );
	add_action( 'wp_ajax_nopriv_workreap_remove_save_item', 'workreap_remove_save_item' );
}

/**
 * hire Remove Multiple Save item
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_remove_save_multipuleitems' ) ) {

	function workreap_remove_save_multipuleitems() {
		$json			=  array();
		$post_id		= !empty( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';
		$item_type		= !empty( $_POST['item_type'] ) ? sanitize_text_field( $_POST['item_type'] ) : '';
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		if( !empty($post_id) && !empty($item_type) && !empty(item_id) ){
			$save_projects_ids	= get_post_meta( $post_id, $item_type, true);
			update_post_meta( $post_id, $item_type, '' );
			
			$json['type'] 		= 'success';
            $json['message'] 	= esc_html__('Remove save items successfully.', 'workreap');
            wp_send_json($json);
		} else{
			$json['type'] 		= 'error';
            $json['message'] 	= esc_html__('Some error occur, please try again later', 'workreap');
            wp_send_json($json);
		}		
	}

	add_action( 'wp_ajax_workreap_remove_save_multipuleitems', 'workreap_remove_save_multipuleitems' );
	add_action( 'wp_ajax_nopriv_workreap_remove_save_multipuleitems', 'workreap_remove_save_multipuleitems' );
}

/**
 * get cover letter
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_get_coverletter' ) ) {

	function workreap_get_coverletter() {
		$json				=  array();
		$proposal_id		= !empty( $_POST['proposal_id'] ) ? intval( $_POST['proposal_id'] ) : '';
		if( empty( $proposal_id )) {
			$json['type'] 		= 'error';
            $json['message'] 	= esc_html__('Some error occur, please try again later', 'workreap');
            wp_send_json($json);
		}
		
		if( !empty($proposal_id) ){
			$contents			= nl2br( stripslashes( get_the_content('',true,$proposal_id) ) );
			
			$json['contents'] 	= $contents;
			$json['type'] 		= 'success';
            $json['message'] 	= esc_html__('Proposal coverletter', 'workreap');
            wp_send_json($json);
		} else{
			$json['type'] 		= 'error';
            $json['message'] 	= esc_html__('Some error occur, please try again later', 'workreap');
            wp_send_json($json);
		}		
	}

	add_action( 'wp_ajax_workreap_get_coverletter', 'workreap_get_coverletter' );
	add_action( 'wp_ajax_nopriv_workreap_get_coverletter', 'workreap_get_coverletter' );
}


/**
 * Add to Cart
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_update_cart' ) ) {

	function workreap_update_cart() {
		$json				=  array();
		$product_id		= !empty( $_POST['id'] ) ? intval( $_POST['id'] ) : '';
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		if( !empty( $product_id )) {
			if ( class_exists('WooCommerce') ) {
			
				global $current_user, $woocommerce;
				$woocommerce->cart->empty_cart(); //empty cart before update cart
				$user_id			= $current_user->ID;
				$is_cart_matched	= workreap_matched_cart_items($product_id);
				if ( isset( $is_cart_matched ) && $is_cart_matched > 0) {
					$json = array();
					$json['type'] 			= 'success';
					$json['message'] 		= esc_html__('You have already in cart, We are redirecting to checkout', 'workreap');
					$json['checkout_url'] 	= wc_get_checkout_url();
					wp_send_json($json);
				}
				
				$cart_meta					= array();
				$user_type					= workreap_get_user_type( $user_id );
				$pakeges_features			= workreap_get_pakages_features();

				if ( !empty ( $pakeges_features )) {
					foreach( $pakeges_features as $key => $vals ) {
						if( $vals['user_type'] === $user_type || $vals['user_type'] === 'common' ) {
							$item			= get_post_meta($product_id,$key,true);
							$text			=  !empty( $vals['text'] ) ? ' '.esc_html($vals['text']) : '';
							if( $key === 'wt_duration_type' ) {
								$feature 	= workreap_get_duration_types($item,'value');
							} else if( $key === 'wt_badget' ) {
								$feature 	= !empty( $item ) ? $item : 0;
							} else {
								$feature 	= $item;
							}
							
							$cart_meta[$key]	= $feature.$text;
						}
					}
				}
				
				$cart_data = array(
					'product_id' 		=> $product_id,
					'cart_data'     	=> $cart_meta,
					'payment_type'     	=> 'subscription',
				);

				$woocommerce->cart->empty_cart();
				$cart_item_data = $cart_data;
				WC()->cart->add_to_cart($product_id, 1, null, null, $cart_item_data);

				$json = array();
				$json['type'] 			= 'success';
				$json['message'] 		= esc_html__('Please wait you are redirecting to checkout page.', 'workreap');
				$json['checkout_url']	= wc_get_checkout_url();
				wp_send_json($json);
			} else {
				$json = array();
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('Please install WooCommerce plugin to process this order', 'workreap');
			}
		} else{
			$json['type'] 		= 'error';
            $json['message'] 	= esc_html__('Some error occur, please try again later', 'workreap');
            wp_send_json($json);
		}		
	}

	add_action( 'wp_ajax_workreap_update_cart', 'workreap_update_cart' );
	add_action( 'wp_ajax_nopriv_workreap_update_cart', 'workreap_update_cart' );
}


/**
 * @check Select features job values for employer
  *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_support_type')) {

    function workreap_support_type(  ) {
		if ( function_exists( 'fw_get_db_settings_option' ) ) {
			$help 		= fw_get_db_settings_option('help_support');		
			$contact_subject		= !empty( $help['enable']['contact_subject'] ) ? $help['enable']['contact_subject'] : '';
			
			if( !empty( $contact_subject ) and is_array($contact_subject) ){
				$contact_subject = array_filter($contact_subject);
				$contact_subject = array_combine(array_map('sanitize_title', $contact_subject), $contact_subject);
				return $contact_subject;
			} else{
				$support	= array ( 
					'query'			=> esc_html__("Query",'workreap'),
					'query_type'	=> esc_html__("Query Type",'workreap')
					);
			}
			
		} else {
			$support	= array ( 
					'query'			=> esc_html__("Query",'workreap'),
					'query_type'	=> esc_html__("Query Type",'workreap')
					);
		}

		return $support;
	}
}

/**
 * FAQ support
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_support_faq' ) ) {

	function workreap_support_faq() {
		global $current_user;
		$json			=  array();
		$query_type		= !empty( $_POST['query_type'] ) ? $_POST['query_type'] : '';
		$details		= !empty( $_POST['details'] ) ? $_POST['details'] : '';
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		if( empty(details) ) {
			$json['type'] 		= 'error';
            $json['message'] 	= esc_html__('Message is required.', 'workreap');
            wp_send_json($json);
		} else if( empty($query_type) ) {
			$json['type'] 		= 'error';
            $json['message'] 	= esc_html__('Query type is required.', 'workreap');
            wp_send_json($json);
		}else if( !empty(details) && !empty($query_type) ){
			if (class_exists('Workreap_Email_helper')) {
				if (class_exists('WorkreapHelp')) {
					$email_helper = new WorkreapHelp();
					$emailData 	  = array();
					$user_name 			= workreap_get_username($current_user->ID);
					$profile 			= workreap_get_linked_profile_id($current_user->ID);
					$user_profile 		= get_the_permalink($profile);

					$emailData['user_name'] 		= esc_attr( $user_name );
					$emailData['user_email'] 		= esc_attr( $user_email );
					$emailData['user_link'] 		= esc_url ( $user_profile );
					$emailData['query_type'] 		= esc_attr( $query_type );
					$emailData['message'] 			= esc_html( $details );

					$email_helper->send_admin_help($emailData);
				}
			}
			
			$json['type'] 		= 'success';
            $json['message'] 	= esc_html__('Message has sent', 'workreap');
            wp_send_json($json);
		} else{
			$json['type'] 		= 'error';
            $json['message'] 	= esc_html__('Some error occur, please try again later', 'workreap');
            wp_send_json($json);
		}		
	}

	add_action( 'wp_ajax_workreap_support_faq', 'workreap_support_faq' );
	add_action( 'wp_ajax_nopriv_workreap_support_faq', 'workreap_support_faq' );
}

/**
 * load more reviews
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_get_more_reviews' ) ) {

	function workreap_get_more_reviews() {
		$json			= array();
		$page			= !empty( $_POST['page'] ) ? intval( $_POST['page'] ) : '';
		$author_id		= !empty( $_POST['author_id'] ) ? intval( $_POST['author_id'] ) : '';
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$show_posts		= 3;
		$order 			= 'DESC';
		$sorting 		= 'ID';
		
		if(!empty($author_id) && !empty($page)) {
			$args2 		= array(
							'posts_per_page' 	=> $show_posts,
							'post_type' 		=> 'reviews',
							'orderby' 			=> $sorting,
							'order' 			=> $order,
							'author' 			=> $author_id,
							'paged' 			=> $page,
							'suppress_filters' 	=> false
						);
			$query2 			= new WP_Query($args2);

			if( $query2->have_posts() ){
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Reviews found', 'workreap');
				
				ob_start();
				$counter	= 0;
				
				while ($query2->have_posts()) : $query2->the_post();
					global $post;
				
					$counter ++;
					$project_id			= get_post_meta($post->ID, '_project_id', true);
					$project_rating		= get_post_meta($post->ID, 'user_rating', true);
					$employer_id		= get_post_field('post_author',$project_id);
					$company_profile 	= workreap_get_linked_profile_id($employer_id);
					$employer_title 	= esc_html( get_the_title( $company_profile ));
					$project_title		= esc_html( get_the_title($project_id));

					$company_avatar 	= apply_filters(
													'workreap_employer_avatar_fallback', workreap_get_employer_avatar( array( 'width' => 100, 'height' => 100 ), $company_profile ), array( 'width' => 225, 'height' => 225 )
												);
					$bg_class			= !empty($counter) && intval($counter)%2 === 0 ? '' : 'wt-bgcolor';
					?>
					<div class="wt-userlistinghold wt-userlistingsingle <?php echo esc_attr($bg_class);?> class-<?php echo esc_attr($post->ID);?>">	
						<figure class="wt-userlistingimg">
							<img src="<?php echo esc_url( $company_avatar );?>" alt="<?php esc_attr_e('Company','workreap');?>" >
						</figure>
						<div class="wt-userlistingcontent">
							<div class="wt-contenthead">
								<div class="wt-title">
									<?php do_action( 'workreap_get_verification_check', $company_profile, $employer_title ); ?>
									<h3><?php echo esc_html($project_title);?></h3>
								</div>
								<ul class="wt-userlisting-breadcrumb">
									<?php do_action('workreap_project_print_project_level', $project_id); ?>
									<?php do_action('workreap_print_location', $project_id); ?>
									<?php do_action('workreap_post_date', $post->ID); ?>
									<?php do_action('workreap_freelancer_get_project_rating', $project_rating,$post->ID); ?>
								</ul>
							</div>
						</div>
						<div class="wt-description">
							<p><?php echo get_the_content();?></p>
						</div>
					</div>
					<?php
					
				endwhile;
				wp_reset_postdata();
				
				$review				= ob_get_clean();
				$json['reviews'] 	= $review;
			} else{
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('No more review', 'workreap');
				$json['reviews'] 	= 'null';
			}
		}
		
		wp_send_json($json);			
	}

	add_action( 'wp_ajax_workreap_get_more_reviews', 'workreap_get_more_reviews' );
	add_action( 'wp_ajax_nopriv_workreap_get_more_reviews', 'workreap_get_more_reviews' );
}

/**
 * Update Payrols
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_payrols_settings')) {

    function workreap_payrols_settings() {
        global $current_user;
        $user_identity 	= $current_user->ID;
        $json 			= array();
		$data 			= array();
		$payrols		= workreap_get_payouts_lists();
		
		$fields		= !empty( $payrols[$_POST['payout_settings']['type']]['fields'] ) ? $payrols[$_POST['payout_settings']['type']]['fields'] : array();
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}

		if( !empty($fields) ) {
			foreach( $fields as $key => $field ){
				if( $field['required'] === true && empty( $_POST['payout_settings'][$key] ) ){
					$json['type'] = 'error';
					$json['message'] = $field['message'];
					wp_send_json( $json );
				}
				
			}
		}
		
		update_user_meta($user_identity,'payrols',$_POST['payout_settings']);
		$json['type'] 	 = 'success';
		$json['message'] = esc_html__('Payout settings have been updated.', 'workreap');

       wp_send_json( $json );
    }

    add_action('wp_ajax_workreap_payrols_settings', 'workreap_payrols_settings');
    add_action('wp_ajax_nopriv_workreap_payrols_settings', 'workreap_payrols_settings');
}

/**
 * hire freelancer for service post
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_hire_service' ) ) {

	function workreap_hire_service() {
		global $current_user, $woocommerce;
		$json			= array();
		$service_id		= !empty( $_POST['service_id'] ) ? intval( $_POST['service_id'] ) : '';
		$addons			= !empty( $_POST['addons'] ) ? explode( ',',$_POST['addons'] ) : array();
		$cart_meta		= array();
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		if ( is_user_logged_in() ) {
			$user_type	= apply_filters('workreap_get_user_type', $current_user->ID);
			if( $user_type === 'freelancer' ) {
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('You have not permission to buy this service.', 'workreap');
				wp_send_json($json);
			}
			
		} else {
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__('Please login to buy this service.', 'workreap');
			wp_send_json($json);
		}
		
		$bk_settings	= worrketic_hiring_payment_setting();
		
		if( isset( $bk_settings['type'] ) && ($bk_settings['type'] === 'woo' || $bk_settings['type'] === 'offline_woo' ) ) {
			$product_id	= workreap_get_hired_product_id();
			if( !empty( $product_id )) {
				if ( class_exists('WooCommerce') ) {

					$woocommerce->cart->empty_cart(); //empty cart before update cart
					$user_id			= $current_user->ID;
					$price				= get_post_meta($service_id ,'_price',true);
					$single_service_price	= $price;
					
					if( !empty( $addons ) ){
						foreach( $addons as $addon_id ){
							$addons_price		= get_post_meta($addon_id ,'_price',true);
							$addons_price		= !empty( $addons_price ) ? $addons_price : 0 ;
							$price				= $price + $addons_price;
							
						}
					}
					
					$delivery_time		= wp_get_post_terms($service_id, 'delivery');
					$delivery_time 		= !empty( $delivery_time[0] ) ? $delivery_time[0]->term_id : '';
					$admin_shares 		= 0.0;
					$freelancer_shares 	= 0.0;
					
					if( !empty( $price ) ){
						if( isset( $bk_settings['percentage'] ) && $bk_settings['percentage'] > 0 ){
							$admin_shares 		= $price/100*$bk_settings['percentage'];
							$freelancer_shares 	= $price - $admin_shares;
							$admin_shares 		= number_format($admin_shares,2,'.', '');
							$freelancer_shares 	= number_format($freelancer_shares,2,'.', '');
						} else{
							$admin_shares 		= 0.0;
							$freelancer_shares 	= $price;
							$admin_shares 		= number_format($admin_shares,2,'.', '');
							$freelancer_shares 	= number_format($freelancer_shares,2,'.', '');
						}
					}
					
					$cart_meta['service_id']		= $service_id;
					$cart_meta['delivery_time']		= $delivery_time;
					$cart_meta['price']				= $price;
					$cart_meta['service_price']		= $single_service_price;
					$cart_meta['addons']			= $addons;
					
					$cart_data = array(
						'product_id' 		=> $product_id,
						'cart_data'     	=> $cart_meta,
						'price'				=> workreap_price_format($price,'return'),
						'payment_type'     	=> 'hiring_service',
						'admin_shares'     	=> $admin_shares,
						'freelancer_shares' => $freelancer_shares,
					);

					$woocommerce->cart->empty_cart();
					$cart_item_data = $cart_data;
					WC()->cart->add_to_cart($product_id, 1, null, null, $cart_item_data);

					$json['type'] 			= 'checkout';
					$json['message'] 		= esc_html__('Please wait you are redirecting to the checkout page.', 'workreap');
					$json['checkout_url']	= wc_get_checkout_url();
					wp_send_json($json);
				} else {
					$json['type'] 		= 'error';
					$json['message'] 	= esc_html__('Please install WooCommerce plugin to process this order', 'workreap');
					wp_send_json($json);
				}
			} else{
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('Hiring settings is missing, please contact to administrator.', 'workreap');
				wp_send_json($json);
			}
		} else {
			if( !empty($service_id) ){          
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Please enable the payments', 'workreap');
				wp_send_json($json);
			} else{
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('Some error occur, please try again later', 'workreap');
				wp_send_json($json);
			}
		}			
	}

	add_action( 'wp_ajax_workreap_hire_service', 'workreap_hire_service' );
	add_action( 'wp_ajax_nopriv_workreap_hire_service', 'workreap_hire_service' );
}

/**
 * load more services
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_get_more_services' ) ) {

	function workreap_get_more_services() {
		$json			= array();
		$page			= !empty( $_POST['page'] ) ? intval( $_POST['page'] ) : '';
		$author_id		= !empty( $_POST['author_id'] ) ? intval( $_POST['author_id'] ) : '';
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$show_posts		= 3;
		$order 			= 'DESC';
		$sorting 		= 'ID';
		
		$post_id				= workreap_get_linked_profile_id( $author_id );
		$freelancer_title 		= get_the_title( $post_id );
		
		if(!empty($author_id) && !empty($page)) {
			$service_args = array(
							'posts_per_page' 	=> $show_posts,
							'post_type' 		=> 'micro-services',
							'orderby' 			=> $sorting,
							'order' 			=> $order,
							'author' 			=> $author_id,
							'paged' 			=> $page,
							'suppress_filters' 	=> false
						);
			
			$services_query		= new WP_Query($service_args);
			if( $services_query->have_posts() ){
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Services found', 'workreap');
				ob_start();
				$counter	= 0;
				while ($services_query->have_posts()) : $services_query->the_post();
					global $post;
				
					$counter ++;
					$service_url		= get_the_permalink();
					
					$db_docs			= array();
					$db_price			= '';
					$delivery_time		= '';
					$order_details		= '';

					if (function_exists('fw_get_db_post_option')) {
						$db_docs   			= fw_get_db_post_option($post->ID,'docs');
						$delivery_time		= fw_get_db_post_option($post->ID,'delivery_time');
						$order_details   	= fw_get_db_post_option($post->ID,'order_details');
						$db_price   		= fw_get_db_post_option($post->ID,'price');
					}
					$freelancer_avatar = apply_filters(
						'workreap_freelancer_avatar_fallback', workreap_get_freelancer_avatar(array('width' => 65, 'height' => 65), $post_id), array('width' => 65, 'height' => 65) 
					);

				?>
					<div class="col-12 col-sm-12 col-md-6 col-lg-4 float-left">
						<div class="wt-freelancers-info">
							<?php if( !empty( $db_docs ) ) {?>
								<div class="wt-freelancers wt-freelancers-services owl-carousel">
									<?php
										foreach( $db_docs as $key => $doc ){
											$attachment_id	= !empty( $doc['attachment_id'] ) ? $doc['attachment_id'] : '';
											$img_url		= wp_get_attachment_image_url($attachment_id,'medium');
											?>
											<figure class="item">
												<a href="<?php echo esc_url( $service_url );?>">
													<img src="<?php echo esc_url($img_url);?>" alt="<?php esc_attr_e('Service ','workreap');?>" class="item">
												</a>
											</figure>
									<?php } ?>
								</div>
							<?php } ?>
							<?php do_action('workreap_service_print_featured', $post->ID); ?>
							<div class="wt-freelancers-details">
								<?php if( !empty( $freelancer_avatar ) ){?>
									<figure class="wt-freelancers-img">
										<img src="<?php echo esc_url($freelancer_avatar); ?>" alt="<?php esc_attr_e('Service ','workreap');?>">
									</figure>
								<?php }?>
								<div class="wt-freelancers-content">
									<div class="dc-title">
										<?php do_action( 'workreap_get_verification_check', $post_id, $freelancer_title ); ?>
										<h3><?php echo esc_html( $post->post_title);?></h3>
										<?php if( !empty( $db_price ) ){?>
											<span><?php esc_html_e('Starting From','workreap');?>&nbsp;<strong><?php echo workreap_price_format($db_price);?></strong>
											</span>
										<?php }?>
									</div>
								</div>
								<div class="wt-freelancers-rating">
									<ul>
										<?php do_action('workreap_service_get_reviews',$post_id,'v1'); ?>
									</ul>
								</div>
							</div>
						</div>
					</div>	
				<?php
					
				endwhile;
				wp_reset_postdata();
				
				$review				= ob_get_clean();
				$json['services'] 	= $review;
			} else{
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('No more service', 'workreap');
				$json['services'] 	= 'null';
			}
		}
		wp_send_json($json);			
	}

	add_action( 'wp_ajax_workreap_get_more_services', 'workreap_get_more_services' );
	add_action( 'wp_ajax_nopriv_workreap_get_more_services', 'workreap_get_more_services' );
}

/**
 * Post a Addons Service
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_post_service' ) ) {

	function workreap_post_service() {
		global $current_user;

		$hide_map 		= 'show';
		$system_access	= '';
		if (function_exists('fw_get_db_post_option') ) {
			$hide_map		= fw_get_db_settings_option('hide_map');
			$job_status		= fw_get_db_settings_option('job_status');
			$system_access	= fw_get_db_settings_option('system_access');
		}

		$job_status	=  !empty( $job_status ) ? $job_status : 'publish';
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent

		$user_id	= workreap_get_linked_profile_id($current_user->ID);
		do_action('workreap_check_post_author_status',$user_id); //check if user is not blocked or deactive
		
		$json 		= array();
		$current 	= !empty($_POST['id']) ? esc_attr($_POST['id']) : '';
		
		if( apply_filters('workreap_is_service_posting_allowed','wt_services', $current_user->ID) === false && empty($current) ){
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__('You’ve consumed all you points or your package has get expired. Please upgrade your package','workreap');
			wp_send_json( $json );
		}
		
		$is_featured              = !empty( $_POST['service']['is_featured'] ) ? $_POST['service']['is_featured'] : '';
		
		$required = array(
            'title'   			=> esc_html__('Service title is required', 'workreap'),
            'delivery_time'  	=> esc_html__('Delivery time is required', 'workreap'),
			'price'  			=> esc_html__('Service price is required', 'workreap'),
			'english_level'  	=> esc_html__('English level is required', 'workreap'),
			'categories'   		=> esc_html__('Category is required', 'workreap')
        );
		
		$required	= apply_filters('workreap_filter_service_required_fields', $required);
		
        foreach ($required as $key => $value) {
			if( empty( $_POST['service'][$key] ) ){
				$json['type'] = 'error';
				$json['message'] = $value;        
				wp_send_json($json);
			}
        }
		
		//Addon check
		if( !empty( $_POST['addons_service'] ) ){
			$required = array(
				'title'   			=> esc_html__('Addons Service title is required', 'workreap'),
				'price'  			=> esc_html__('Addons Service price is required', 'workreap'),
			);
			
			foreach( $_POST['addons_service'] as $key => $item ) {
				foreach( $required as $inner_key => $item_check ) {
					if( empty( $_POST['addons_service'][$key][$inner_key] ) ){
						$json['type'] = 'error';
						$json['message'] =  $item_check;      
						wp_send_json($json);
					}
				}
			}	
		}

		//extract the job variables
		extract($_POST['service']);
		$title				= !empty( $title ) ? $title : rand(1,999999);
		$description		= !empty( $description ) ?  $description : '';
		
		if( isset( $_POST['submit_type'] ) && $_POST['submit_type'] === 'update' ){
			$current = !empty($_POST['id']) ? esc_attr($_POST['id']) : '';
			
			$post_author = get_post_field('post_author', $current);
            $post_id 	 = $current;
            $status 	 = get_post_status($post_id);
			
			if( intval( $post_author ) === intval( $current_user->ID ) ){
				$article_post = array(
					'ID' 			=> $current,
					'post_title' 	=> $title,
					'post_content' 	=> $description,
					'post_status' 	=> $status,
				);

				wp_update_post($article_post);
			} else{
				$json['type'] = 'error';
				$json['message'] = esc_html__('Some error occur, please try again later', 'workreap');
				wp_send_json( $json );
			}
			
		} else{
			//Create Post
			$user_post = array(
				'post_title'    => wp_strip_all_tags( $title ),
				'post_status'   => $job_status,
				'post_content'  => $description,
				'post_author'   => $current_user->ID,
				'post_type'     => 'micro-services',
			);

			$post_id    		= wp_insert_post( $user_post );
			
			//featured string
			update_post_meta( $post_id, '_featured_service_string', 0 );
			
			$remaning_services		= workreap_get_subscription_metadata( 'wt_services',intval($current_user->ID) );
			$remaning_services  	= !empty( $remaning_services ) ? intval($remaning_services) : 0;

			if( !empty( $remaning_services) && $remaning_services >= 1 ) {
				$update_services	= intval( $remaning_services ) - 1 ;
				$update_services	= intval($update_services);

				$wt_subscription 	= get_user_meta(intval($current_user->ID), 'wt_subscription', true);
				$wt_subscription	= !empty( $wt_subscription ) ?  $wt_subscription : array();

				$wt_subscription['wt_services'] = $update_services;

				update_user_meta( intval($current_user->ID), 'wt_subscription', $wt_subscription);
			}
			
			$expiry_string		= workreap_get_subscription_metadata( 'subscription_featured_string',$current_user->ID );
			if( !empty($expiry_string) ) {
				update_post_meta($post_id, '_expiry_string', $expiry_string);
			}
		}
				
		if( $post_id ){
			//Upload files from temp folder to uploads
			$files              = !empty( $_POST['service']['service_documents'] ) ? $_POST['service']['service_documents'] : array();
			$service_files		= array();
			if( !empty( $files ) ) {
				foreach ( $files as $key => $value ) {
					if( !empty( $value['attachment_id'] ) ){
						$service_files[] = $value;
					} else{
						$service_files[] = workreap_temp_upload_to_media($value, $post_id);
					} 	
				}                
			}
			
			if( !empty( $service_files [0]['attachment_id'] ) ){
				set_post_thumbnail( $post_id, $service_files [0]['attachment_id']);
			}
			
			$downloadable_files		= !empty( $_POST['service']['downloadable_files'] ) ? $_POST['service']['downloadable_files'] : array();
			$downloadables			= array();
			
			if( !empty( $downloadable_files ) ) {
				foreach ( $downloadable_files as $key => $value ) {
					if( !empty( $value['attachment_id'] ) ){
						$downloadables[] = $value;
					} else{
						$downloadables[] = workreap_temp_upload_to_media($value['url'], $post_id);
					} 	
				}                
			}
			
			$is_downloable	= !empty( $_POST['service']['downloadable'] ) ? $_POST['service']['downloadable'] : '';
			
			if( !empty( $is_downloable ) && $is_downloable === 'yes' && !empty( $downloadables ) ){
				update_post_meta( $post_id, '_downloadable_files', $downloadables );
			}
			
			update_post_meta( $post_id, '_downloadable', $is_downloable );
			
			//Set terms ( cat , language)
			$categories		= !empty( $_POST['service']['categories'] ) ? $_POST['service']['categories'] : array();
			$languages		= !empty( $_POST['service']['languages'] ) ? $_POST['service']['languages'] : array();
			
			$price	        = !empty( $_POST['service']['price'] ) ? $_POST['service']['price'] : '';
			$delivery_time  = !empty( $_POST['service']['delivery_time'] ) ? array($_POST['service']['delivery_time']) : array();
			$response_time  = !empty( $_POST['service']['response_time'] ) ? array($_POST['service']['response_time']) : array();
			$english_level	= !empty( $_POST['service']['english_level'] ) ? $_POST['service']['english_level'] : '';
			
			$addons	        = !empty( $_POST['service']['addons'] ) ? $_POST['service']['addons'] : array();
			
			if( !empty( $_POST['addons_service'] ) ){
				foreach( $_POST['addons_service'] as $key => $item ) {

					$user_post = array(
						'post_title'    => wp_strip_all_tags( $item['title'] ),
						'post_excerpt'  => $item['description'],
						'post_author'   => $current_user->ID,
						'post_type'     => 'addons-services',
						'post_status'	=> 'publish'
					);

					$addon_post_id    		= wp_insert_post( $user_post );
					
					$addons[]	= $addon_post_id;
					
					$addon_price	        = !empty( $item['price'] ) ? $item['price'] : '';

					//update
					update_post_meta($addon_post_id, '_price', $addon_price);

					//update unyson meta
					$fw_options = array();
					$fw_options['price']         	= $addon_price;
					
					//Update User Profile
					fw_set_db_post_option($addon_post_id, null, $fw_options);
				}	
			}

			update_post_meta( $post_id, '_addons', $addons );
			
			if( !empty($is_featured) && $is_featured === 'on' ){
				$is_featured_service	= get_post_meta($post_id,'_featured_service_string',true);
				
				if(empty( $is_featured_service )){
					$featured_services	= workreap_featured_service( $current_user->ID );
					if( $featured_services || $system_access == 'both' ) {
						$featured_string	= workreap_is_feature_value( 'subscription_featured_string', $current_user->ID );
						update_post_meta($post_id, '_featured_service_string', 1);
					}

					$remaning_featured_services		= workreap_get_subscription_metadata( 'wt_featured_services',intval($current_user->ID) );
					$remaning_featured_services  	= !empty( $remaning_featured_services ) ? intval($remaning_featured_services) : 0;

					if( !empty( $remaning_featured_services) && $remaning_featured_services >= 1 ) {
						$update_featured_services	= intval( $remaning_featured_services ) - 1 ;
						$update_featured_services	= intval( $update_featured_services );

						$wt_subscription 	= get_user_meta(intval($current_user->ID), 'wt_subscription', true);
						$wt_subscription	= !empty( $wt_subscription ) ?  $wt_subscription : array();

						$wt_subscription['wt_featured_services'] = $update_featured_services;
						update_user_meta( intval($current_user->ID), 'wt_subscription', $wt_subscription);
					}
				}
			} else {
				update_post_meta( $post_id, '_featured_service_string', 0 );
			}

			if( !empty( $categories ) ){
				wp_set_post_terms( $post_id, $categories, 'project_cat' );
			}
			
			if( !empty( $languages ) ){
				wp_set_post_terms( $post_id, $languages, 'languages' );
			}
			
			if( !empty( $delivery_time ) ){
				wp_set_post_terms( $post_id, $delivery_time, 'delivery' );
			}
			
			if( !empty( $response_time ) ){
				wp_set_post_terms( $post_id, $response_time, 'response_time' );
			}
									
			//update location
			$address    = !empty( $_POST['service']['address'] ) ? esc_attr( $_POST['service']['address'] ) : '';
			$country    = !empty( $_POST['service']['country'] ) ? $_POST['service']['country'] : '';
			$latitude   = !empty( $_POST['service']['latitude'] ) ? esc_attr( $_POST['service']['latitude'] ): '';
			$longitude  = !empty( $_POST['service']['longitude'] ) ? esc_attr( $_POST['service']['longitude'] ): '';
			$videos 	= !empty( $_POST['service']['videos'] ) ? $_POST['service']['videos'] : array();

			update_post_meta($post_id, '_country', $country);
			
			//Set country for unyson
			$locations = get_term_by( 'slug', $country, 'locations' );
			
			$location = array();
			if( !empty( $locations ) ){
				$location[0] = $locations->term_id;

				if( !empty( $location ) ){
					wp_set_post_terms( $post_id, $location, 'locations' );
				}

			}
			
			//update
			update_post_meta($post_id, '_price', $price);
			update_post_meta($post_id, '_english_level', $english_level);

			//update unyson meta
			$fw_options = array();
			$fw_options['price']         	= $price;
			$fw_options['downloadable']     = $is_downloable;
			$fw_options['english_level']    = $english_level;
			$fw_options['docs']    			= $service_files;
			
			$fw_options['address']            	 = $address;
			$fw_options['longitude']          	 = $longitude;
			$fw_options['latitude']           	 = $latitude;
			$fw_options['country']            	 = $location;
			$fw_options['videos']            	 = $videos;

			//Update User Profile
			fw_set_db_post_option($post_id, null, $fw_options);
			
			if( isset( $_POST['submit_type'] ) && $_POST['submit_type'] === 'update' ){
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Your service has been updated', 'workreap');
			} else{
				//Send email to users
				if (class_exists('Workreap_Email_helper')) {
					if (class_exists('WorkreapServicePost')) {
						$email_helper = new WorkreapServicePost();
						$emailData 	  = array();

						$freelancer_name 		= workreap_get_username($current_user->ID);
						$freelancer_email 		= get_userdata( $current_user->ID )->user_email;
						
						$freelancer_profile 	= get_permalink($user_id);
						$service_title 			= get_the_title($post_id);
						$service_link 			= get_permalink($post_id);
						

						$emailData['freelancer_name'] 	= esc_html( $freelancer_name );
						$emailData['freelancer_email'] 	= esc_html( $freelancer_email );
						$emailData['freelancer_link'] 	= esc_url( $freelancer_profile );
						$emailData['status'] 			= esc_html( $job_status );
						$emailData['service_title'] 	= esc_html( $service_title );
						$emailData['service_link'] 		= esc_url( $service_link );

						$email_helper->send_admin_service_post($emailData);
						$email_helper->send_freelancer_service_post($emailData);
					}
				}
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Your service has been posted.', 'workreap');
			}
			
			$json['url'] 		= Workreap_Profile_Menu::workreap_profile_menu_link('services', $current_user->ID, true,'posted');
			wp_send_json( $json );
		} else{
			$json['type'] = 'error';
			$json['message'] = esc_html__('Some error occur, please try again later', 'workreap');
			wp_send_json( $json );
		}

	}

	add_action( 'wp_ajax_workreap_post_service', 'workreap_post_service' );
	add_action( 'wp_ajax_nopriv_workreap_post_service', 'workreap_post_service' );
}

/**
 * Post a portfolio
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_add_portfolio' ) ) {

	function workreap_add_portfolio() {
		global $current_user;

		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent

		$json 		= array();
		$current 	= !empty($_POST['id']) ? esc_attr($_POST['id']) : '';

		$ppt_option			= '';
		if( function_exists('fw_get_db_settings_option') ){
			$ppt_option		= fw_get_db_settings_option('ppt_template');
		}

		$required = array(
            'title'   			=> esc_html__('Portfolio title is required', 'workreap'),
			'gallery_imgs'   	=> esc_html__('At-least one portfolio image is required', 'workreap'),
        );
		
		$required	= apply_filters('workreap_filter_portfolio_required_fields', $required);
		
        foreach ($required as $key => $value) {
			if( empty( $_POST['portfolio'][$key] ) ){
				$json['type'] = 'error';
				$json['message'] = $value;        
				wp_send_json($json);
			}
        }

		//extract the portfolio variables
		extract($_POST['portfolio']);
		
		$title				= !empty( $title ) ? $title : '';
		$description		= !empty( $description ) ?  $description : '';
		
		if( isset( $_POST['submit_type'] ) && $_POST['submit_type'] === 'update' ){
			$current = !empty($_POST['id']) ? esc_attr($_POST['id']) : '';
			
			$post_author = get_post_field('post_author', $current);
            $post_id 	 = $current;
            $status 	 = get_post_status($post_id);
			
			if( intval( $post_author ) === intval( $current_user->ID ) ){
				$portfolio_post = array(
					'ID' 			=> $current,
					'post_title' 	=> $title,
					'post_content' 	=> $description,
					'post_status' 	=> $status,
				);

				wp_update_post($portfolio_post);

				if( !empty( $categories ) ){
					wp_set_post_terms( $post_id, $categories, 'portfolio_categories' );
				}
	
				if( !empty( $_POST['tags'] ) ) {
					wp_set_post_terms( $post_id, $_POST['tags'], 'portfolio_tags' );
				}
			} else{
				$json['type'] = 'error';
				$json['message'] = esc_html__('Some error occur, please try again later', 'workreap');
				wp_send_json( $json );
			}
			
		} else{
			//Create Post
			$user_post = array(
				'post_title'    => wp_strip_all_tags( $title ),
				'post_status'   => 'publish',
				'post_content'  => $description,
				'post_author'   => $current_user->ID,
				'post_type'     => 'wt_portfolio',
			);

			$post_id    		= wp_insert_post( $user_post );

			//Prepare Params
			$params_array['user_identity'] = $current_user->ID;
			$params_array['user_role'] = apply_filters('workreap_get_user_type', $current_user->ID );
			$params_array['type'] = 'portfolio_upload';

			//child theme : update extra settings
			do_action('wt_process_portfolio_upload', $params_array);
		}
				
		if( $post_id ){
			//Upload files from temp folder to uploads
			$files              = !empty( $_POST['portfolio']['gallery_imgs'] ) ? $_POST['portfolio']['gallery_imgs'] : array();
			$documents          = !empty( $_POST['portfolio']['documents'] ) ? $_POST['portfolio']['documents'] : array();
			$zip_files          = !empty( $_POST['portfolio']['zip_attachments'] ) ? $_POST['portfolio']['zip_attachments'] : array();
			$videos				= !empty( $_POST['portfolio']['videos'] ) ? $_POST['portfolio']['videos'] : array();

			if( !empty($ppt_option) && $ppt_option === 'enable' ){
				$ppt_template		= !empty($_POST['ppt_template']) ? $_POST['ppt_template'] : '';
				update_post_meta( $post_id, 'ppt_template', $ppt_template );
			}

			$gallery_imgs		= array();
			$doc_attachemnts	= array();
			$zip_attachements	= array();

			if( !empty( $files ) ) {
				foreach ( $files as $key => $value ) {
					if( !empty( $value['attachment_id'] ) ){
						$gallery_imgs[] = $value;
					} else{
						$gallery_imgs[] = workreap_temp_upload_to_media($value, $post_id);
					} 	
				}                
			}

			if( !empty( $documents ) ) {
				foreach ( $documents as $key => $value ) {
					if( !empty( $value['attachment_id'] ) ){
						$doc_attachemnts[] = $value;
					} else{
						$doc_attachemnts[] = workreap_temp_upload_to_media($value, $post_id);
					} 	
				}                
			}

			if( !empty( $zip_files ) ) {
				foreach ( $zip_files as $key => $value ) {
					if( !empty( $value['attachment_id'] ) ){
						$zip_attachements[] = $value;
					} else{
						$zip_attachements[] = workreap_temp_upload_to_media($value, $post_id);
					} 	
				}                
			}
			
			if( !empty( $gallery_imgs[0]['attachment_id'] ) ){
				set_post_thumbnail( $post_id, $gallery_imgs[0]['attachment_id']);
			}
			
			$custom_link	= !empty( $_POST['portfolio']['custom_link'] ) ? $_POST['portfolio']['custom_link'] : '';

			if( !empty( $categories ) ){
				wp_set_post_terms( $post_id, $categories, 'portfolio_categories' );
			}

			if( !empty( $_POST['tags'] ) ) {
				wp_set_post_terms( $post_id, $_POST['tags'], 'portfolio_tags' );
			}

			//update unyson meta
			$fw_options = array();
			$fw_options['custom_link']  	= $custom_link;
			$fw_options['gallery_imgs']    	= $gallery_imgs;
			$fw_options['documents']    	= $doc_attachemnts;
			$fw_options['zip_attachments']  = $zip_attachments;
			$fw_options['videos']    		= $videos;

			fw_set_db_post_option($post_id, null, $fw_options);
			
			if( apply_filters('workreap_filter_user_promotion', 'disable') === 'enable' ){
				do_action('workreap_update_users_marketing_attributes',$current_user->ID,'portfolios');
			}

			if( isset( $_POST['submit_type'] ) && $_POST['submit_type'] === 'update' ){
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Your portfolio has been updated', 'workreap');
			} else{
				$json['url'] 		= Workreap_Profile_Menu::workreap_profile_menu_link('portfolios', $current_user->ID, true, 'posted');
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Your portfolio has been added.', 'workreap');
			}

			wp_send_json( $json );
		} else{
			$json['type'] = 'error';
			$json['message'] = esc_html__('Some error occur, please try again later', 'workreap');
			wp_send_json( $json );
		}

	}

	add_action( 'wp_ajax_workreap_add_portfolio', 'workreap_add_portfolio' );
	add_action( 'wp_ajax_nopriv_workreap_add_portfolio', 'workreap_add_portfolio' );
}


/**
 * Post a Service
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_post_addons_service' ) ) {

	function workreap_post_addons_service() {
		global $current_user;
				
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$user_id	= workreap_get_linked_profile_id($current_user->ID);
		$json 		= array();
		$current 	= !empty($_POST['id']) ? esc_attr($_POST['id']) : '';
		
		$required = array(
            'title'   			=> esc_html__('Addons Service title is required', 'workreap'),
			'price'  			=> esc_html__('Addons Service price is required', 'workreap'),
        );
		
        foreach ($required as $key => $value) {
			if( empty( $_POST['addons_service'][$key] ) ){
				$json['type'] = 'error';
				$json['message'] = $value;        
				wp_send_json($json);
			}
        }
		
		//extract the job variables
		extract($_POST['addons_service']);
		$title				= !empty( $title ) ? $title : rand(1,999999);
		$description		= !empty( $description ) ?  $description : '';
		
		if( isset( $_POST['submit_type'] ) && $_POST['submit_type'] === 'update' ){
			$current = !empty($_POST['id']) ? esc_attr($_POST['id']) : '';
			
			$post_author = get_post_field('post_author', $current);
            $post_id 	 = $current;
			
			if( intval( $post_author ) === intval( $current_user->ID ) ){
				$article_post = array(
					'ID' 			=> $current,
					'post_title' 	=> $title,
					'post_excerpt' 	=> $description,
				);

				wp_update_post($article_post);
			} else{
				$json['type'] = 'error';
				$json['message'] = esc_html__('Some error occur, please try again later', 'workreap');
				wp_send_json( $json );
			}
			
		} else{
			//Create Post
			$user_post = array(
				'post_title'    => wp_strip_all_tags( $title ),
				'post_excerpt'  => $description,
				'post_author'   => $current_user->ID,
				'post_type'     => 'addons-services',
				'post_status'	=> 'publish'
			);

			$post_id    		= wp_insert_post( $user_post );
			
		}
				
		if( $post_id ){
			//Upload files from temp folder to uploads
			$price	        = !empty( $_POST['addons_service']['price'] ) ? $_POST['addons_service']['price'] : '';
					
			//update
			update_post_meta($post_id, '_price', $price);

			//update unyson meta
			$fw_options = array();
			$fw_options['price']         	= $price;
			//Update User Profile
			fw_set_db_post_option($post_id, null, $fw_options);
			
			if( isset( $_POST['submit_type'] ) && $_POST['submit_type'] === 'update' ){
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Your addons service has been updated', 'workreap');
			} else{
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Your addons service has been added', 'workreap');
				
				$json['url'] 		= Workreap_Profile_Menu::workreap_profile_menu_link('addons_service', $current_user->ID, true,'listing',$post_id);
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Your service has been posted.', 'workreap');
			}

			wp_send_json( $json );
		} else{
			$json['type'] = 'error';
			$json['message'] = esc_html__('Some error occur, please try again later', 'workreap');
			wp_send_json( $json );
		}

	}

	add_action( 'wp_ajax_workreap_post_addons_service', 'workreap_post_addons_service' );
	add_action( 'wp_ajax_nopriv_workreap_post_addons_service', 'workreap_post_addons_service' );
}

/**
 * Update service price
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_service_price_update' ) ) {

	function workreap_service_price_update() {
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$service_id = !empty( $_POST['service_id'] ) ? intval( $_POST['service_id'] ) : '';
		$addons_ids = !empty( $_POST['addons_ids'] ) ?  $_POST['addons_ids']  : array();
		$json		= array();
		$service_price	= 0 ;
		
		if( !empty( $service_id ) ){
			$service_price	= get_post_meta($service_id,'_price',true);
		}
		
		$addons_price	= 0 ;
		if( !empty( $addons_ids ) ){
			foreach( $addons_ids as $post_id ) {
				$addon_price	= get_post_meta($post_id,'_price',true);
				$addon_price	= !empty( $addon_price ) ? $addon_price : 0 ;
				$addons_price	= $addons_price + $addon_price ;
			}
		}
		
		$total_service_price	= $addons_price + $service_price;
		$json['price'] 			= workreap_price_format($total_service_price,'return');
		$json['type'] 		= 'success';
        $json['message'] 	= esc_html__('Service price updated.', 'workreap');
        wp_send_json( $json );
	}
	
	add_action( 'wp_ajax_workreap_service_price_update', 'workreap_service_price_update' );
	add_action( 'wp_ajax_nopriv_workreap_service_price_update', 'workreap_service_price_update' );
}
/**
 * follow service action
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_follow_service' ) ) {

	function workreap_follow_service() {
		global $current_user;
		$post_id = !empty( $_POST['id'] ) ? esc_attr( $_POST['id'] ) : '';
		$json = array();
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		if ( empty( $current_user->ID ) ) {
			$json['type'] = 'error';
			$json['message'] = esc_html__( 'You must login before add this service to wishlist.', 'workreap' );
			wp_send_json( $json );
		}
		
		$linked_profile   	= workreap_get_linked_profile_id($current_user->ID);
		$saved_services 	= get_post_meta($linked_profile, '_saved_services', true);
		
		$json       = array();
        $wishlist   = array();
        $wishlist   = !empty( $saved_services ) && is_array( $saved_services ) ? $saved_services : array();

        if (!empty($post_id)) {
            if( in_array($post_id, $wishlist ) ){                
                $json['type'] = 'error';
                $json['message'] = esc_html__('This service is already to your wishlist', 'workreap');
                wp_send_json( $json );
            }

            $wishlist[] = $post_id;
            $wishlist   = array_unique( $wishlist );
            update_post_meta( $linked_profile, '_saved_services', $wishlist );
           
            $json['type'] 		= 'success';
            $json['message'] 	= esc_html__('Successfully! added to your wishlist', 'workreap');
            wp_send_json( $json );
        }
        
        $json['type'] = 'error';
        $json['message'] = esc_html__('Oops! something is going wrong.', 'workreap');
        wp_send_json( $json );
	}

	add_action( 'wp_ajax_workreap_follow_service', 'workreap_follow_service' );
	add_action( 'wp_ajax_nopriv_workreap_follow_service', 'workreap_follow_service' );
}

/**
 * change service status
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_service_status' ) ) {

	function workreap_service_status() {
		global $current_user;
		
		$json = array();

		if ( empty( $current_user->ID ) ) {
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__( 'You must login before changing this service status.', 'workreap' );
			wp_send_json( $json );
		}
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$required = array(
            'id'   			=> esc_html__('Service ID is required', 'workreap'),
            'status'  		=> esc_html__('Service status is required', 'workreap')
        );
		
        foreach ($required as $key => $value) {
			if( empty( $_POST[$key] ) ){
				$json['type'] 		= 'error';
				$json['message'] 	= $value;        
				wp_send_json($json);
			}
        }
		
		$service_id 			= !empty( $_POST['id'] ) ? esc_attr( $_POST['id'] ) : '';
		$status					= !empty( $_POST['status'] ) ? esc_attr( $_POST['status'] ) : '';
		
		$update_post			= array();
		$update					= workreap_save_service_status($service_id,$status);
		if( $update ) {
			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('Successfully! update service status', 'workreap');
			wp_send_json( $json );
		} else {
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__( 'Service status is not updated.', 'workreap' );
			wp_send_json( $json );
		}
		
	}

	add_action( 'wp_ajax_workreap_service_status', 'workreap_service_status' );
	add_action( 'wp_ajax_nopriv_workreap_service_status', 'workreap_service_status' );
}

/**
 * Remove service
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_service_remove' ) ) {

	function workreap_service_remove() {
		global $current_user;
		$json = array();
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		if ( empty( $current_user->ID ) ) {
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__( 'You must login before remove this service status.', 'workreap' );
			wp_send_json( $json );
		}
		
		$required = array(
            'id'   			=> esc_html__('Service ID is required', 'workreap')
        );
		
        foreach ($required as $key => $value) {
			if( empty( $_POST[$key] ) ){
				$json['type'] 		= 'error';
				$json['message'] 	= $value;        
				wp_send_json($json);
			}
        }
		
		$service_id 		= !empty( $_POST['id'] ) ? esc_attr( $_POST['id'] ) : '';
		$queu_services		= workreap_get_services_count('services-orders',array('hired'), $service_id);
		if( $queu_services === 0 ){
			$update		= workreap_save_service_status($service_id, 'deleted');
			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('Successfully!  removed this service.', 'workreap');	
		} else {
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__('You can\'t your service because you have orders in queue.', 'workreap');
		}
		
		wp_send_json( $json );
	}

	add_action( 'wp_ajax_workreap_service_remove', 'workreap_service_remove' );
	add_action( 'wp_ajax_nopriv_workreap_service_remove', 'workreap_service_remove' );
}

/**
 * Remove portfolio
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_portfolio_remove' ) ) {

	function workreap_portfolio_remove() {
		global $current_user;
		$json = array();
		
		$portfolio_id = !empty($_POST['id']) ? $_POST['id'] : '';
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		if ( empty( $current_user->ID ) ) {
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__( 'You must login before removing this portfolio.', 'workreap' );
			wp_send_json( $json );
		} else {
			wp_delete_post($portfolio_id);
			
			if( apply_filters('workreap_filter_user_promotion', 'disable') === 'enable' ){
				do_action('workreap_update_users_marketing_attributes',$current_user->ID,'portfolios');
			}

			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('Portfolio removed successfully.', 'workreap');
			wp_send_json( $json );	
		}	
	}

	add_action( 'wp_ajax_workreap_portfolio_remove', 'workreap_portfolio_remove' );
	add_action( 'wp_ajax_nopriv_workreap_portfolio_remove', 'workreap_portfolio_remove' );
}

/**
 * Remove service
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_addons_service_remove' ) ) {

	function workreap_addons_service_remove() {
		global $current_user;
		$json = array();
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		if ( empty( $current_user->ID ) ) {
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__( 'You must login before remove this service status.', 'workreap' );
			wp_send_json( $json );
		}
		
		$required = array(
            'id'   			=> esc_html__('Addons Service ID is required', 'workreap')
        );
		
        foreach ($required as $key => $value) {
			if( empty( $_POST[$key] ) ){
				$json['type'] 		= 'error';
				$json['message'] 	= $value;        
				wp_send_json($json);
			}
        }
		
		$service_id 		= !empty( $_POST['id'] ) ? esc_attr( $_POST['id'] ) : '';
		if( !empty( $service_id ) ){
			wp_delete_post($service_id);
			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('Successfully!  removed this addons service.', 'workreap');	
			wp_send_json( $json );
		} 
	}

	add_action( 'wp_ajax_workreap_addons_service_remove', 'workreap_addons_service_remove' );
	add_action( 'wp_ajax_nopriv_workreap_addons_service_remove', 'workreap_addons_service_remove' );
}


/**
 * Complete Service with reviews
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists( 'workreap_complete_service_project' ) ){
	function workreap_complete_service_project(){
		global $current_user;
		$json 					= array();
		$where					= array();
		$update					= array();
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent

		$service_order_id		= !empty( $_POST['service_order_id'] ) ? intval($_POST['service_order_id']) : '';
		$contents 				= !empty( $_POST['feedback_description'] ) ? esc_attr($_POST['feedback_description']) : '';
		$reviews 				= !empty( $_POST['feedback'] ) ? ($_POST['feedback']) : array();
	
		if( empty( $contents ) || empty( $service_order_id ) ){
			$json['type'] 		= 'error';
			
			if( empty( $contents ) ) {
				$json['message'] 	= esc_html__('Feedback detail is required field', 'workreap');	
			} 
			
			wp_send_json($json);
			
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
					
					$employer_name 		= workreap_get_username($current_user->ID);
					$employer_profile 	= get_permalink(workreap_get_linked_profile_id($current_user->ID));
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
			
			$json['url'] 		= Workreap_Profile_Menu::workreap_profile_menu_link('services', $current_user->ID, true,'completed');
			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('Service completed successfully.', 'workreap');
			wp_send_json($json);
			
		}
	}
	add_action('wp_ajax_workreap_complete_service_project', 'workreap_complete_service_project');
    add_action('wp_ajax_nopriv_workreap_complete_service_project', 'workreap_complete_service_project');
}

/**
 * Cancel service
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */

if( !function_exists( 'workreap_service_cancelled' ) ){
	function workreap_service_cancelled(){
		global $current_user, $wpdb, $woocommerce;
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$json 				= array();
		$service_order_id	=  !empty( $_POST['service_id'] ) ? intval($_POST['service_id']) : '';
		$cancelled_reason	=  !empty( $_POST['cancelled_reason'] ) ? $_POST['cancelled_reason'] : '';
		
		if( empty( $service_order_id ) || empty( $cancelled_reason ) ){
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__('No kiddies please', 'workreap');
			wp_send_json($json);
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

						$employer_name 		= workreap_get_username($current_user->ID);
						$employer_profile 	= get_permalink(workreap_get_linked_profile_id($current_user->ID));
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
				
				$json['url'] 		= Workreap_Profile_Menu::workreap_profile_menu_link('services', $current_user->ID, true,'cancelled');
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Your order have been cancelled.', 'workreap');
				wp_send_json($json);
			} else {
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('No kiddies please', 'workreap');
				wp_send_json($json);
			}
			
		}
	}
	add_action('wp_ajax_workreap_service_cancelled', 'workreap_service_cancelled');
    add_action('wp_ajax_nopriv_workreap_service_cancelled', 'workreap_service_cancelled');
}

/**
 * Service Cancelled Reason
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_service_reason' ) ) {

	function workreap_service_reason() {
		global $current_user;
		
		$json = array();
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		if ( empty( $current_user->ID ) ) {
			$json['type'] = 'error';
			$json['message'] = esc_html__( 'You must login before changing this service status.', 'workreap' );
			wp_send_json( $json );
		}
		
		$required = array(
            'service_id'   			=> esc_html__('Service ID is required', 'workreap')
        );
        foreach ($required as $key => $value) {
			if( empty( $_POST[$key] ) ){
				$json['type'] 		= 'error';
				$json['message'] 	= $value;        
				wp_send_json($json);
			}
        }
		
		$service_id 			= !empty( $_POST['service_id'] ) ? esc_attr( $_POST['service_id'] ) : '';
		$feedback	 			= fw_get_db_post_option($service_id, 'feedback');
		if( $feedback ) {
			$json['type'] 		= 'success';
			$json['feedback'] 	= $feedback;
			wp_send_json( $json );
		} else {
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__( 'Service status is not updated.', 'workreap' );
			wp_send_json( $json );
		}
		
	}

	add_action( 'wp_ajax_workreap_service_reason', 'workreap_service_reason' );
	add_action( 'wp_ajax_nopriv_workreap_service_reason', 'workreap_service_reason' );
}

/**
 * Service Complete Rating
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_service_complete_rating' ) ) {

	function workreap_service_complete_rating() {
		global $current_user;
		
		$json = array();
		
		if ( empty( $current_user->ID ) ) {
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__( 'You must login before changing this service status.', 'workreap' );
			wp_send_json( $json );
		}
		
		$required = array(
            'service_id'   			=> esc_html__('Service ID is required', 'workreap')
        );
        foreach ($required as $key => $value) {
			if( empty( $_POST[$key] ) ){
				$json['type'] 		= 'error';
				$json['message'] 	= $value;        
				wp_send_json($json);
			}
        }
		ob_start(); 
		$service_id 	= !empty( $_POST['service_id'] ) ? esc_attr( $_POST['service_id'] ) : '';
		$feedback	 	= fw_get_db_post_option($service_id, 'feedback');
		$rating_titles 	= workreap_project_ratings('services_ratings');
		?>
		
		<div class="wt-description">
			<p><?php echo esc_html( $feedback );?></p>
		</div>
		<form class="wt-formtheme wt-formfeedback">
			<fieldset>
				<?php 
					if( !empty( $rating_titles ) ) {
						foreach( $rating_titles as $slug => $label ) {
							$q_rating	 	= get_post_meta($service_id, $slug, true);
							if( !empty( $q_rating ) ){ ?>
								<div class="form-group wt-ratingholder">
									<div class="wt-ratepoints">
										<div class="counter wt-pointscounter"><?php echo esc_html( $q_rating );?></div>
										<div class="user-stars-v2">
											<?php do_action('workreap_freelancer_single_service_rating', $q_rating ); ?>
										</div>
									</div>
									<span class="wt-ratingdescription"><?php echo esc_html( $label );?></span>
								</div>
							<?php }?>
					<?php }?>
				<?php }?>
				<div class="form-group wt-btnarea">
					<a class="wt-btn" href="javascript:;" data-dismiss="modal" aria-label="Close"><?php esc_html_e('Okay','workreap');?></a>
				</div>
			</fieldset>
		</form>
		<?php
		if( $feedback ) {
			$json['type'] 		= 'success';
			$json['ratings'] 	= ob_get_clean();
			wp_send_json( $json );
		} else {
			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__( 'Service status is not updated.', 'workreap' );
			wp_send_json( $json );
		}
		
	}

	add_action( 'wp_ajax_workreap_service_complete_rating', 'workreap_service_complete_rating' );
	add_action( 'wp_ajax_nopriv_workreap_service_complete_rating', 'workreap_service_complete_rating' );
}

/*
**
 * load more service reviews
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_more_rating_service' ) ) {

	function workreap_more_rating_service() {
		$json			= array();
		$paged			= !empty( $_POST['page'] ) ? intval( $_POST['page'] ) : '';
		$service_id		= !empty( $_POST['service_id'] ) ? intval( $_POST['service_id'] ) : '';
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$show_posts		= 1;
		$order 			= 'DESC';
		$sorting 		= 'ID';
		
		if(!empty($service_id) && !empty($paged)) {
			$args2 			= array(
					'posts_per_page' 	=> $show_posts,
					'post_type' 		=> 'services-orders',
					'orderby' 			=> $sorting,
					'order' 			=> $order,
					'post_status' 		=> array('completed'),
					'paged' 			=> $paged,
					'suppress_filters' 	=> false
				);

			$meta_query_args2[] = array(
									'key' 		=> '_service_id',
									'value' 	=> $service_id,
									'compare' 	=> '='
								);
			$query_relation2 		= array('relation' => 'AND',);
			$args2['meta_query'] 	= array_merge($query_relation2, $meta_query_args2);
			
			$query2 			= new WP_Query($args2);
			$count_post 		= $query2->found_posts;

			if( $query2->have_posts() ){
				$json['type'] 		= 'success';
				$json['message'] 	= esc_html__('Review found', 'workreap');
				ob_start();
				$counter	= 0;
				while ($query2->have_posts()) : $query2->the_post();
					global $post;
					$author_id 		= get_the_author_meta( 'ID' );  
					$linked_profile = workreap_get_linked_profile_id($author_id);
					$tagline		= workreap_get_tagline($linked_profile);
					$employer_title = get_the_title( $linked_profile );
					$employer_avatar = apply_filters(
										'workreap_employer_avatar_fallback', workreap_get_employer_avatar(array('width' => 100, 'height' => 100), $linked_profile), array('width' => 100, 'height' => 100) 
									);
					$service_ratings	= get_post_meta($post->ID,'_hired_service_rating',true);
					if( function_exists('fw_get_db_post_option') ) {
						$feedback	 		= fw_get_db_post_option($post->ID, 'feedback');
					}
					?>
					<div class="wt-userlistinghold wt-userlistingsingle">	
						<?php if( !empty( $employer_avatar ) ){?>
							<figure class="wt-userlistingimg">
								<img src="<?php echo esc_url( $employer_avatar );?>" alt="<?php echo esc_attr($employer_title);?>">
							</figure>
						<?php } ?>
						<div class="wt-userlistingcontent">
							<div class="wt-contenthead">
								<div class="wt-title">
									<?php do_action( 'workreap_get_verification_check', $linked_profile, $employer_title ); ?>
									<?php if( !empty( $tagline ) ) {?>
										<h3><?php echo esc_html( $tagline );?></h3>
									<?php } ?>
								</div>
								<ul class="wt-userlisting-breadcrumb">
									<?php do_action('workreap_print_location', $linked_profile); ?>
									<li><?php do_action('workreap_freelancer_single_service_rating', $service_ratings ); ?></li>
								</ul>
							</div>
						</div>
						<?php if( !empty( $feedback ) ){?>
							<div class="wt-description">
								<p>“<?php echo esc_html( $feedback );?>”</p>
							</div>
						<?php  }?>
					</div>
					<?php
					
				endwhile;
				wp_reset_postdata();
				
				$review				= ob_get_clean();
				$json['reviews'] 	= $review;
			} else{
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('No more service reviews', 'workreap');
				$json['reviews'] 	= 'null';
			}
		}
		wp_send_json($json);			
	}

	add_action( 'wp_ajax_workreap_more_rating_service', 'workreap_more_rating_service' );
	add_action( 'wp_ajax_nopriv_workreap_more_rating_service', 'workreap_more_rating_service' );
}

/*
**
 * load more service
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( !function_exists( 'workreap_more_service' ) ) {

	function workreap_more_service() {
		$json			= array();
		$paged			= !empty( $_POST['page'] ) ? intval( $_POST['page'] ) : '';
		$user_id		= !empty( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : '';
		
		$post_id		= workreap_get_linked_profile_id( $user_id );
		$flag 			= rand(9999, 999999);
		
		$order 			= 'DESC';
		$sorting 		= 'ID';
		
		if(!empty($user_id) && !empty($paged)) {
			$show_posts		= 3;
			$order 			= 'DESC';
			$sorting 		= 'ID';
			$width			= 352;
			$height			= 200;
			$args_res 		= array(
									'posts_per_page' 	=> $show_posts,
									'post_type' 		=> 'micro-services',
									'orderby' 			=> $sorting,
									'order' 			=> $order,
									'author' 			=> $user_id,
									'paged' 			=> $paged,
									'suppress_filters' 	=> false
								);
			$query_res 		= new WP_Query($args_res);
			$count_post 	= $query_res->found_posts;

			if( $query_res->have_posts() ){
				$json['type'] 		= 'success';
				if( intval( $query_res->max_num_pages ) >= $paged ) {
					$json['show_btn']	= 'show';
				} else {
					$json['show_btn']	= 'hide';
				}
				
				$json['message'] 	= esc_html__('services found', 'workreap');
				ob_start();
				
				while ($query_res->have_posts()) : $query_res->the_post();
					global $post;
					$project_rating			= get_post_meta($post->ID, 'user_rating', true);
					$freelancer_title 		= get_the_title( $post_id );	
					$service_url			= get_the_permalink();

					$db_docs			= array();
					$db_price			= '';
					$delivery_time		= '';
					$order_details		= '';

					if (function_exists('fw_get_db_post_option')) {
						$db_docs   			= fw_get_db_post_option($post->ID,'docs');
						$delivery_time		= fw_get_db_post_option($post->ID,'delivery_time');
						$order_details   	= fw_get_db_post_option($post->ID,'order_details');
						$db_price   		= fw_get_db_post_option($post->ID,'price');
					}
					
					if( count( $db_docs )>1 ) {
						$class	= 'wt-freelancers-services-'.intval( $flag ).' owl-carousel';
					} else {
						$class	= '';
					}
					
					if( empty($db_docs) ) {
						$empty_image_class	= 'wt-empty-service-image';
						$is_featured		= workreap_service_print_featured( $post->ID, 'yes');
						$is_featured    	= !empty( $is_featured ) ? 'wt-featured-service' : '';
					} else {
						$empty_image_class	= '';
						$is_featured		= '';
					}

				?>
				<div class="col-12 col-sm-12 col-md-6 col-lg-4 float-left">
					<div class="wt-freelancers-info <?php echo esc_attr( $empty_image_class );?> <?php echo esc_attr( $is_featured );?>">
						<?php if( !empty( $db_docs ) ) {?>
							<div class="wt-freelancers <?php echo esc_attr( $class );?>">
								<?php
									foreach( $db_docs as $key => $doc ){
										$attachment_id	= !empty( $doc['attachment_id'] ) ? $doc['attachment_id'] : '';
										$thumbnail      = workreap_prepare_image_source($attachment_id, $width, $height);
										if ( strpos( $thumbnail,'media/default.png' ) === false ) { ?>
										<figure class="item">
											<a href="<?php echo esc_url( $service_url );?>">
												<img src="<?php echo esc_url($thumbnail);?>" alt="<?php esc_attr_e('Service ','workreap');?>" class="item">
											</a>
										</figure>
								<?php } } ?>
							</div>
						<?php } ?>
						<?php do_action('workreap_service_print_featured', $post->ID); ?>
						<?php do_action('workreap_service_shortdescription', $post->ID,$post_id); ?>
					</div>
				</div>
				<?php
					endwhile;
					wp_reset_postdata();
				
					$service			= ob_get_clean();
					$json['flag']		= $flag;
					$json['services'] 	= $service;
					wp_send_json($json);
			} else{
				$json['type'] 		= 'error';
				$json['message'] 	= esc_html__('No more services available.', 'workreap');
				$json['services'] 	= 'null';
				wp_send_json($json);
			}
		}			
	}
	
	add_action( 'wp_ajax_workreap_more_service', 'workreap_more_service' );
	add_action( 'wp_ajax_nopriv_workreap_more_service', 'workreap_more_service' );
}

//Articluate plugin compatibility
add_action('wp_ajax_workreap_articulate_upload_form_data', 'articulate_upload_form_data');
add_action('wp_ajax_nopriv_workreap_articulate_upload_form_data', 'articulate_upload_form_data');