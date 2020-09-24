<?php
/**
 * Email Helper To Send Email
 * @since    1.0.0
 */
if (!class_exists('WorkreapJobPost')) {

    class WorkreapJobPost extends Workreap_Email_helper{

        public function __construct() {
			//do stuff here
        }

		/**
		 * @Send report user email
		 *
		 * @since 1.0.0
		 */
		public function send_admin_job_post($params = '') {
			extract($params);
			$status	= !empty( $status ) ? $status : '';
			$subject_default = esc_html__('Job Posted', 'workreap_core');
			$email_default = 'Hello,
						A new job has posted by the <a href="%employer_link%">%employer_name%</a>.

						<div style="width: 100%; float: left; padding: 15px; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;">
							<div style="width: 100%; float: left; padding: 15px; background: #f7f7f7; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;">
								<div style="width: 100%; float: left; padding: 30px 15px; border: 2px solid #fff; text-align: center; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;">
									<p>Consectetur adipisicing elit sed do eiusmod tempor incidi dunt ut labore et dolore magna aliqua enim adia minim</p>
									<a style="color: #fff; padding: 0 50px; margin: 0 0 15px; font-size: 20px; font-weight: 600; line-height: 60px; border-radius: 8px; background: #5dc560; vertical-align: top; display: inline-block; font-family: \'Work Sans\', Arial, Helvetica, sans-serif;  text-decoration: none;" href="%job_link%">%job_title%</a>
									<span style="width: 100%; float: left; font-size: 13px; line-height: 13px; color: #919191; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;">Click to view the job link</span>
								</div>
							</div>
						</div>

						%signature%';

			if (function_exists('fw_get_db_settings_option')) {
				$subject 		= fw_get_db_settings_option('admin_job_post_subject');
				$email_to 		= fw_get_db_settings_option('admin_job_post_email');
				$email_content  = fw_get_db_settings_option('admin_job_post_content');
			}

			//Set Default Subject
			if( empty( $subject ) ){
				$subject = $subject_default;
			}

			//set defalt contents
			if (empty($email_content)) {
				$email_content = $email_default;
			}                       
			
			//set defalt admin email
			if( empty( $email_to ) || !is_email( $email_to ) ){
				$email_to = get_option('admin_email', 'somename@example.com');
			}
			
			//Email Sender information
			$sender_info = $this->process_sender_information();
			
			$email_content = str_replace("%employer_name%", $employer_name, $email_content); 
			$email_content = str_replace("%employer_link%", $employer_link, $email_content); 
			$email_content = str_replace("%job_title%", $job_title, $email_content); 
			$email_content = str_replace("%job_link%", $job_link, $email_content); 
			$email_content = str_replace("%status%", $status, $email_content);
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
		 * @Send report user email
		 *
		 * @since 1.0.0
		 */
		public function send_employer_job_post($params = '') {
			extract($params);
			$status	= !empty( $status ) ? $status : '';
			
			$subject_default = esc_html__('Congratulations! Your Job Has Posted', 'workreap_core');
			$email_default = 'Hello %employer_name%,<br/>
								Congratulation! Your job has been posted.<br/>
								Click below link to view the job. <a href="%job_link%" target="_blank">%job_title%</a><br/>
								<br/>
								%signature%,<br/>';
			
			
			if (function_exists('fw_get_db_settings_option')) {
				$subject 		= fw_get_db_settings_option('emp_job_post_subject');
				$email_content  = fw_get_db_settings_option('emp_job_post_content');
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
			
			$email_content = str_replace("%employer_name%", $employer_name, $email_content); 
			$email_content = str_replace("%employer_link%", $employer_link, $email_content); 
			$email_content = str_replace("%job_title%", $job_title, $email_content); 
			$email_content = str_replace("%job_link%", $job_link, $email_content); 
			$email_content = str_replace("%status%", $status, $email_content);
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

	new WorkreapJobPost();
}