<?php
/**
 * Email Helper To Send Email
 * @since    1.0.0
 */
if (!class_exists('WorkreapSendOffer')) {

    class WorkreapJobInvitationCancellation extends Workreap_Email_helper{

        public function __construct() {
			//do stuff here
        }
		
		/**
		 * @Send offer
		 *
		 * @since 1.0.0
		 */
		public function send_notification($params = '') {
			extract($params);
			$subject_default = esc_html__('Job Invitation Cancellation', 'workreap_core');
			$email_default   = 'Hello %employer_name%<br/>
								The freelancer <a href="%freelancer_link%">%freelancer_name%</a> is not interested in your job invitation of the project 
								<a href="%project_link%">%project_title%</a><br/>
								Message: %message%<br/><br/>
								%signature%,';
			
			if (function_exists('fw_get_db_settings_option')) {
				$subject 		= fw_get_db_settings_option('job_invitation_cancel_subject');
				$email_content  = fw_get_db_settings_option('job_invitation_cancel_content');
			}

			//Set Default Subject
			if( empty( $subject ) ){
				$subject = $subject_default;
			}

			//set defalt contents
			if (empty($email_content)) {
				$email_content = $email_default;
            }
            
			//Email Sender information
			$sender_info = $this->process_sender_information();
			
			//Email Sender information
			$email_content = str_replace("%freelancer_link%", $freelancer_link, $email_content); 
			$email_content = str_replace("%freelancer_name%", $freelancer_name, $email_content); 
			$email_content = str_replace("%project_link%", $project_link, $email_content); 
			$email_content = str_replace("%project_title%", $project_title, $email_content); 
			$email_content = str_replace("%employer_link%", $employer_link, $email_content); 
			$email_content = str_replace("%employer_name%", $employer_name, $email_content); 
			$email_content = str_replace("%message%", $message, $email_content); 
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
		
	}

	new WorkreapJobInvitationCancellation();
}