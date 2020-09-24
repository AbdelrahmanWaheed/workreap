<?php
/**
 * Email Helper To Send Email for Getting Password
 * @since    1.0.0
 */
if (!class_exists('WorkreapSendDispute')) {

    class WorkreapSendDispute extends Workreap_Email_helper{

        public function __construct() {
			//do stuff here
        }

		/**
		 * @Send Generat Password Link
		 *
		 * @since 1.0.0
		 */
		public function send($params = '') {
			global $current_user;

			extract($params);

			$subject_default = esc_html__('You have received a new dispute', 'workreap_core');
			$contact_default = 'Hello Admin<br/>
								You have received a new dispute from the %user_name%, detail is given below<br/>
								Subject : %dispute_subject%<br/>
								Message : %message%<br/>
								<br/>
								%signature%,<br/>';

			if (function_exists('fw_get_db_settings_option')) {
				$subject 		= fw_get_db_settings_option('dispute_subject');
				$email_content 	= fw_get_db_settings_option('dispute_content');
				$email_to 		= fw_get_db_settings_option('dispute_email');
			}
			
			//set defalt admin email
			if( empty( $email_to ) ){
				$email_to = get_option('admin_email', 'somename@example.com');
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
			
			$email_content = str_replace("%project_link%", $project_link, $email_content); 
			$email_content = str_replace("%project_title%", $project_title, $email_content); 
			$email_content = str_replace("%dispute_subject%", $dispute_subject, $email_content); 
			$email_content = str_replace("%message%", $message, $email_content); 
			$email_content = str_replace("%user_name%", $user_name, $email_content); 
			$email_content = str_replace("%user_link%", $user_link, $email_content); 
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
		 * @Send Generat Password Link
		 *
		 * @since 1.0.0
		 */
		public function send_resolved_dispute_freelancer($params = '') {
			global $current_user;

			extract($params);

			$subject_default = esc_html__('Freelancer Dispute', 'workreap_core');
			$contact_default = 'Hi %freelancer_name%,<br/>
								We have reached out to you regarding a dispute that was raised by %dispute_raised_by%.<br/>
								%admin_message%<br/>
								Thanks <br/>
								%signature%,<br/>';

			if (function_exists('fw_get_db_settings_option')) {
				$subject 		= fw_get_db_settings_option('fr_dispute_subject');
				$email_content 	= fw_get_db_settings_option('fr_dispute_content');
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
			
			$email_content = str_replace("%freelancer_name%", $freelancer_name, $email_content); 
			$email_content = str_replace("%dispute_raised_by%", $dispute_raised_by, $email_content); 
			$email_content = str_replace("%admin_message%", $admin_message, $email_content); 
			$email_content = str_replace("%signature%", $sender_info, $email_content);

			$body = '';
			$body .= $this->prepare_email_headers();

			$body .= '<div style="width: 100%; float: left; padding: 0 0 60px; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;">';
			$body .= '<div style="width: 100%; float: left;">';
			$body .= '<p>' . $email_content . '</p>';
			$body .= '</div>';
			$body .= '</div>';
			$body .= $this->prepare_email_footers();	
			$email_to = $freelancer_email;																		          
			wp_mail($email_to, $subject, $body);
		}

		/**
		 * @Send Generat Password Link
		 *
		 * @since 1.0.0
		 */
		public function send_resolved_dispute_employer($params = '') {
			global $current_user;

			extract($params);

			$subject_default = esc_html__('Employer Dispute', 'workreap_core');
			$contact_default = 'Hi %employer_name%,<br/>
								We have reached out to you regarding a dispute that was raised by %dispute_raised_by%.<br/>
								%admin_message%<br/>
								Thanks <br/>
								%signature%,<br/>';

			if (function_exists('fw_get_db_settings_option')) {
				$subject 		= fw_get_db_settings_option('emp_dispute_subject');
				$email_content 	= fw_get_db_settings_option('emp_dispute_content');
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
			
			$email_content = str_replace("%employer_name%", $employer_name, $email_content); 
			$email_content = str_replace("%dispute_raised_by%", $dispute_raised_by, $email_content); 
			$email_content = str_replace("%admin_message%", $admin_message, $email_content); 
			$email_content = str_replace("%signature%", $sender_info, $email_content);

			$body = '';
			$body .= $this->prepare_email_headers();

			$body .= '<div style="width: 100%; float: left; padding: 0 0 60px; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;">';
			$body .= '<div style="width: 100%; float: left;">';
			$body .= '<p>' . $email_content . '</p>';
			$body .= '</div>';
			$body .= '</div>';
			$body .= $this->prepare_email_footers();
			$email_to = $employer_email;																			          
			wp_mail($email_to, $subject, $body);
		}
		
	}

	new WorkreapSendDispute();
}