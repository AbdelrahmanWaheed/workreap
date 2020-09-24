<?php
/**
 * Email Helper To Send Email
 * @since    1.0.0
 */
if (!class_exists('WorkreapRegisterEmail')) {

    class WorkreapRegisterEmail extends Workreap_Email_helper{

        public function __construct() {
			//do stuff here
        }

		/**
		 * @Send welcome freelancer email
		 *
		 * @since 1.0.0
		 */
		public function send_freelacner_email($params = '') {
			extract($params);
			$subject_default = esc_html__('Thank you for registering', 'workreap_core');
			$contact_default = 'Hello %name%!<br/>
										
								Thanks for registering at %site%. You can now login to manage your account using the following credentials:<br/>
								Email: %email%<br/>
								Password: %password%<br/><br/>
								%signature%';

			if (function_exists('fw_get_db_settings_option')) {
				$subject = fw_get_db_settings_option('freelancers_subject');
				$email_content = fw_get_db_settings_option('freelancers_content');
			}

			//Set Default Subject
			if( empty( $subject ) ){
				$subject = $subject_default;
			}

			//set defalt contents
			if (empty($email_content)) {
				$email_content = $contact_default;
			}                       

			//Email Sender information
			$sender_info = $this->process_sender_information();
			
			$email_content = str_replace("%name%", $name, $email_content); 
			$email_content = str_replace("%password%", $password, $email_content); 
			$email_content = str_replace("%email%", $email, $email_content); 
			$email_content = str_replace("%verification_code%", $verification_code, $email_content);
			$email_content = str_replace("%site%", $site, $email_content); 
			$email_content = str_replace("%signature%", $sender_info, $email_content);

			$body = '';
			$body .= $this->prepare_email_headers();

			$body .= '<div style="width: 100%; float: left; padding: 0 0 60px; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;">';
			$body .= '<div style="width: 100%; float: left;">';
			$body .= '<p>' . $email_content . '</p>';
			$body .= '</div>';
			$body .= '</div>';

			$body .= $this->prepare_email_footers();											           
			wp_mail($email, $subject, $body);
		}
		
		/**
		 * @Send welcome employer email
		 *
		 * @since 1.0.0
		 */
		public function send_employer_email($params = '') {
			extract($params);
			$subject_default = esc_html__('Thank you for registering', 'workreap_core');
			$contact_default = 'Hello %name%!<br/>
										
								Thanks for registering at %site%. You can now login to manage your account using the following credentials:<br/>
								Email: %email%<br/>
								Password: %password%<br/><br/>
								%signature%';

			if (function_exists('fw_get_db_settings_option')) {
				$subject = fw_get_db_settings_option('employer_subject');
				$email_content = fw_get_db_settings_option('employer_content');
			}

			//Set Default Subject
			if( empty( $subject ) ){
				$subject = $subject_default;
			}

			//set defalt contents
			if (empty($email_content)) {
				$email_content = $contact_default;
			}                       

			//Email Sender information
			$sender_info = $this->process_sender_information();
			
			$email_content = str_replace("%name%", $name, $email_content); 
			$email_content = str_replace("%password%", $password, $email_content); 
			$email_content = str_replace("%email%", $email, $email_content); 
			$email_content = str_replace("%verification_code%", $verification_code, $email_content);
			$email_content = str_replace("%site%", $site, $email_content); 
			$email_content = str_replace("%signature%", $sender_info, $email_content);

			$body = '';
			$body .= $this->prepare_email_headers();

			$body .= '<div style="width: 100%; float: left; padding: 0 0 60px; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;">';
			$body .= '<div style="width: 100%; float: left;">';
			$body .= '<p>' . $email_content . '</p>';
			$body .= '</div>';
			$body .= '</div>';

			$body .= $this->prepare_email_footers();											           
			wp_mail($email, $subject, $body);
		}
		
		/**
		 * @Send welcome admin email
		 *
		 * @since 1.0.0
		 */
		public function send_admin_email($params = '') {
			extract($params);
			$subject_default = esc_html__('Thank you for registering', 'workreap_core');
			$contact_default = 'Hello!<br/>
								A new user "%name%" with email address "%email%" has registered on your website. Please login to check user detail.
								<br/>
								%signature%';

			if (function_exists('fw_get_db_settings_option')) {
				$email_to 		= fw_get_db_settings_option('admin_email');
				$subject 		= fw_get_db_settings_option('admin_register_subject');
				$email_content  = fw_get_db_settings_option('admin_register_content');
			}

			//Set Default Subject
			if( empty( $subject ) ){
				$subject = $subject_default;
			}

			//set defalt contents
			if (empty($email_content)) {
				$email_content = $contact_default;
			} 
			
			//set defalt contents
			if (empty($email_to)) {
				$email_to = get_option('admin_email', 'info@example.com');
			} 

			//Email Sender information
			$sender_info = $this->process_sender_information();
			
			$email_content = str_replace("%name%", $name, $email_content); 
			$email_content = str_replace("%password%", $password, $email_content); 
			$email_content = str_replace("%email%", $email, $email_content); 
			$email_content = str_replace("%verification_code%", $verification_code, $email_content);
			$email_content = str_replace("%site%", $site, $email_content); 
			$email_content = str_replace("%signature%", $sender_info, $email_content);

			$body = '';
			$body .= $this->prepare_email_headers();

			$body .= '<div style="width: 100%; float: left; padding: 0 0 60px; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;">';
			$body .= '<div style="width: 100%; float: left;">';
			$body .= '<p>' . $email_content . '</p>';
			$body .= '</div>';
			$body .= '</div>';

			$body .= $this->prepare_email_footers();											           
			wp_mail($email_to, $subject, $body);
		}

		/**
		 * @Send verification email
		 *
		 * @since 1.0.0
		 */
		public function send_verification($params = '') {
			global $current_user;
			extract($params);
			$subject_default = esc_html__('Email Verification Code', 'workreap_core');
			$contact_default = 'Hello %name%!<br/>

								Your account has created on %site%. Verification is required, To verify your account please use below code:<br> 
								Verification Code: %verification_code%<br/>

								%signature%';

			if (function_exists('fw_get_db_settings_option')) {
				$subject = fw_get_db_settings_option('verify_subject');
				$email_content = fw_get_db_settings_option('verify_content');
			}

			//Set Default Subject
			if( empty( $subject ) ){
				$subject = $subject_default;
			}

			//set defalt contents
			if (empty($email_content)) {
				$email_content = $contact_default;
			}                       
			
			//Email Sender information
			$sender_info = $this->process_sender_information();
			
			$email_content = str_replace("%name%", $name, $email_content); 
			$email_content = str_replace("%email%", $email, $email_content); 
			$email_content = str_replace("%verification_code%", $verification_code, $email_content);
			$email_content = str_replace("%site%", $site, $email_content); 
			$email_content = str_replace("%signature%", $sender_info, $email_content);

			$body = '';
			$body .= $this->prepare_email_headers();

			$body .= '<div style="width: 100%; float: left; padding: 0 0 60px; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;">';
			$body .= '<div style="width: 100%; float: left;">';
			$body .= '<p>' . $email_content . '</p>';
			$body .= '</div>';
			$body .= '</div>';
			$body .= $this->prepare_email_footers();													           
			wp_mail($email, $subject, $body);
		}		
		
	}

	new WorkreapRegisterEmail();
}