<?php
/**
 * Email Helper To Send Email for New Notification
 * @since    1.0.0
 */
if (!class_exists('WorkreapSendDispute')) {

    class WorkreapSendNotification extends Workreap_Email_helper {

        public function __construct() {
			//do stuff here
        }

		/**
		 * @Send Generat Password Link
		 *
		 * @since 1.0.0
		 */
		public function send($params = '') {
			extract($params);

			$subject_default = esc_html__('New Notification', 'workreap_core');
			$contact_default = 'Hello %user_name%<br/>
								You have received a new notification.<br/>
								Message : %message%<br/><br/>
								%signature%,<br/>';

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
			
			$email_content = str_replace("%user_name%", $user_name, $email_content); 
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

	new WorkreapSendNotification();
}