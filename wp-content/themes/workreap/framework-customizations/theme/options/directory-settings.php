<?php

if (!defined('FW')) {
    die('Forbidden');
}

$schedules_list	= array();

if( function_exists('workreap_cron_schedule') ) {
	$schedules		= workreap_cron_schedule();
	
	if( !empty( $schedules ) ) {
		foreach ( $schedules as $key => $val ) {
			$schedules_list[$key]	= $schedules[$key]['display'];
		}
	}
}

$options = array(
    'directory' => array(
        'title' => esc_html__('Directory Settings', 'workreap'),
        'type' => 'tab',
        'options' => array( 
            'general-settings' => array(
                'title' => esc_html__('General Settings', 'workreap'),
                'type' => 'tab',
                'options' => array(
					'chat' => array(
						'type' => 'multi-picker',
						'label' => false,
						'desc' => '',
						'picker' => array(
							'gadget' => array(
								'label' => esc_html__('Real Time Chat?', 'workreap'),
								'type' => 'select',
								'value' => 'inbox',
								'desc' => esc_html__('Enable real time chat or use simple inbox system.', 'workreap'),
								'choices' => array(
									'inbox' => esc_html__('Inbox', 'workreap'),
									'chat' => esc_html__('Real Time Chat', 'workreap'),
									'cometchat' => esc_html__('Third Party CometChat', 'workreap'),
								)
							)
						),
						'choices' => array(
							'chat' => array(
								'instant' => array(
									'type' => 'html',
									'html' => esc_html__('Realtime Chat Settings', 'workreap'),
									'label' => esc_html__('', 'workreap'),
									'desc' => wp_kses( __( 'Please make sure Node.js has installed on your server.', 'workreap'),array(
														'a' => array(
															'href' => array(),
															'title' => array()
														),
														'br' => array(),
														'em' => array(),
														'strong' => array(),
													)),
									'help' => esc_html__('', 'workreap'),
								),
								'floating_chat' => array(
									'label' => esc_html__('Enable Chat window', 'workreap'),
									'type' => 'switch',
									'value' => 'disable',
									'desc' => esc_html__('Enable/Disable chat window on freelancer detail page and also on service details page.', 'workreap'),
									'left-choice' => array(
										'value' => 'enable',
										'label' => esc_html__('Enable', 'workreap'),
									),
									'right-choice' => array(
										'value' => 'disable',
										'label' => esc_html__('Disable', 'workreap'),
									),
								),
								'host' => array(
									'label' => esc_html__('Host?', 'workreap'),
									'value' => esc_html__('http://localhost', 'workreap'),
									'desc' 		=> wp_kses( __( 'Please add the host, default would be http://localhost<br/> 
												1) Host could be either http://localhost<br/>
												2) OR could be http://yourdomain.com<br/>
												', 'workreap' ),array(
																'a' => array(
																	'href' => array(),
																	'title' => array()
																),
																'br' => array(),
																'em' => array(),
																'strong' => array(),
															)),
									'type' => 'text',
								),
								'port' => array(
									'type' => 'text',
									'value' => '81',
									'label' => esc_html__('Port', 'workreap'),
									'desc' 		=> wp_kses( __( 'Please add the available port for chat, default would be 81<br/>
												1) Some server uses 80, 81, 8080 or 3000<br/>
												2) Please consult with your hosting provider<br/>
												3) No need to change the port if your server is using port 81, <br/>
												if you will change this port then you have to change it in server.js located in themes > workreap > js > server.js at line no 3<br/>
												Your node server should run server.js located in theme for real-time chat. Please ask your hosting provider, how you can run this file.
												', 'workreap' ),array(
																'a' => array(
																	'href' => array(),
																	'title' => array()
																),
																'br' => array(),
																'em' => array(),
																'strong' => array(),
															)),
									'help' => esc_html__('', 'workreap'),
								),
							),
							'cometchat' => array(
								'cometintro' => array(
									'type' => 'html',
									'value' => true,
									'html' => esc_html__('CometChat Configurations', 'workreap'),
									'label' => esc_html__('', 'workreap'),
									'desc' => wp_kses( __( 'Install the CometChat plugin first. <a href="https://www.cometchat.com/wordpress-chat" target="_blank"> Get CometChat Plugin </a><br />
													Set the api key and auth key in CometChat Plugin settings.
									', 'workreap'),array(
														'a' => array(
															'href' => array(),
															'title' => array()
														),
														'br' => array(),
														'em' => array(),
														'strong' => array(),
													)),
									'help' => esc_html__('', 'workreap'),
								),
							),

							'default' => array(),
						),
						
						'show_borders' => false,
					),
					'redirect_registration' => array(
						'label' => esc_html__('Registration redirect?', 'workreap'),
						'type' => 'select',
						'value' => 'settings',
						'desc' => esc_html__('You can select where to redirect after registration. Default would be dashboard', 'workreap'),
						'choices' => array(
							'package'   => esc_html__('Packages', 'workreap'),
							'settings'  => esc_html__('Profile Settings', 'workreap'),
							'insights' 		=> esc_html__('Dashboard', 'workreap'),
							'home' 		=> esc_html__('Home Page', 'workreap'),
						)
					),
					'redirect_login' => array(
						'label' => esc_html__('Login redirect?', 'workreap'),
						'type' => 'select',
						'value' => 'settings',
						'desc' => esc_html__('You can select where to redirect after login. Default would be dashboard', 'workreap'),
						'choices' => array(
							'package'   => esc_html__('Packages', 'workreap'),
							'settings'  => esc_html__('Profile Settings', 'workreap'),
							'insights' 		=> esc_html__('Dashboard', 'workreap'),
							'home' 		=> esc_html__('Home Page', 'workreap'),
						)
					),
					'portfolio' => array(
						'type' => 'multi-picker',
						'label' => false,
						'desc' => '',
						'picker' => array(
							'gadget' => array(
								'label' => esc_html__('Enable portfolio', 'workreap'),
								'type' => 'select',
								'value' => 'enable',
								'desc' => esc_html__('Enable portfolio for freelancers', 'workreap'),
								'choices' => array(
									'enable' => esc_html__('Enable', 'workreap'),
									'hide' => esc_html__('Hide it', 'workreap'),
								)
							)
						),
						'choices' => array(
							'enable' => array(
								'others' => array(
									'label'   		=> esc_html__( 'Hide default setttings', 'workreap' ),
									'desc'   		=> esc_html__( 'Hide videos, gallery images and crafted projects', 'workreap' ),
									'type'    		=> 'select',
									'value'    		=> 'no',
									'choices'	=> array(
										'no'   => esc_html__('No ( Show it )', 'workreap'),
										'yes'	=> esc_html__('Yes ( Hide it )', 'workreap')
									)
								),
							),
							'default' => array(),
						),
						'show_borders' => false,
					),
					'hide_freelancer_perhour' => array(
						'label'   		=> esc_html__( 'Per hour rate', 'workreap' ),
						'desc'   		=> esc_html__( 'Hide freelancer per hour rate all over the site', 'workreap' ),
						'type'    		=> 'select',
						'value'    		=> 'no',
						'choices'	=> array(
							'no'   => esc_html__('No ( Show it )', 'workreap'),
							'yes'	=> esc_html__('Yes ( Hide it )', 'workreap')
						)
					),
					'allow_proposal_edit' => array(
						'label'   		=> esc_html__( 'Allow proposal edit', 'workreap' ),
						'desc'   		=> esc_html__( 'Allow the freelancer to edit their proposals after submitting', 'workreap' ),
						'type'    		=> 'select',
						'value'    		=> 'yes',
						'choices'	=> array(
							'no'   => esc_html__('No', 'workreap'),
							'yes'	=> esc_html__('Yes', 'workreap')
						)
					),
					'allow_proposal_amount_edit' => array(
						'label'   		=> esc_html__( 'Allow proposal amount edit', 'workreap' ),
						'desc'   		=> esc_html__( 'Allow the freelancer to edit their proposals cost', 'workreap' ),
						'type'    		=> 'select',
						'value'    		=> 'no',
						'choices'	=> array(
							'no'   => esc_html__('No', 'workreap'),
							'yes'	=> esc_html__('Yes', 'workreap')
						)
					),
					'job_proposal_feedback_option' => array(
						'label'   		=> esc_html__( 'Allow proposal feedback', 'workreap' ),
						'desc'   		=> esc_html__( 'Allow the employer to send feedback about the proposal to the freelancer', 'workreap' ),
						'type'    		=> 'select',
						'value'    		=> 'yes',
						'choices'	=> array(
							'no'   => esc_html__('No', 'workreap'),
							'yes'	=> esc_html__('Yes', 'workreap')
						)
					),
					'cron_job_interval' => array(
						'label'   		=> esc_html__( 'Cron job interval', 'workreap' ),
						'desc'   		=> esc_html__( 'Select interval for job alerts.', 'workreap' ),
						'type'    		=> 'select',
						'value'    		=> 'basic',
						'choices' 		=> $schedules_list
					),
					'job_status'  => array(
						'label' => esc_html__( 'Review job/service', 'workreap' ),
						'type'  => 'select',
						'value' => 'publish',
						'desc' 		=> wp_kses( __( 'Review job and services before publish. Needs admin approval before going live..<br/> 
												1) In "Yes ( Pending )" job or service will be pending and admin will approve manually.<br/>
												2) In "No ( Published )" job or service will be published automatically.<br/>', 'workreap' ),array(
													'a' => array(
														'href' => array(),
														'title' => array()
													),
													'br' => array(),
													'em' => array(),
													'strong' => array(),
												)),
						'choices'	=> array(
							'pending'   => esc_html__('Yes ( Pending )', 'workreap'),
							'publish'	=> esc_html__('No ( Published )', 'workreap')
						)
					),
					'job_invitation_cancellation_priod_hours' => array(
						'label' => esc_html__( 'Job Invitation Cancellation Period (in hours)', 'workreap' ),
						'type'  => 'number',
						'value' => 24,
						'desc' 	=> 'Set the job invitation automatic cancellation period in hours.',
					),
					'upload_resume'  => array(
						'label' => esc_html__( 'Upload Resume', 'workreap' ),
						'type'  => 'select',
						'value' => 'no',
						'desc' => esc_html__('Enable the options to upload resume for freelancers', 'workreap'),
						'choices'	=> array(
							'yes'  => esc_html__('Yes', 'workreap'),
							'no'	=> esc_html__('No', 'workreap')
						)
					),
					'db_left_menu'  => array(
						'label' => esc_html__( 'Users Left menu', 'workreap' ),
						'type'  => 'select',
						'value' => 'no',
						'desc' => esc_html__('Hide users left menu?', 'workreap'),
						'choices'	=> array(
							'yes'  => esc_html__('Yes', 'workreap'),
							'no'	=> esc_html__('No', 'workreap')
						)
					),
					'payout_choices'  => array(
						'label' => esc_html__( 'Remove Payouts method', 'workreap' ),
						'type'  => 'select',
						'value' => 'both',
						'desc' => esc_html__('Select one of the method to remove from list', 'workreap'),
						'choices'	=> array(
							'both'  	=> esc_html__('Use both', 'workreap'),
							'paypal'	=> esc_html__('PayPal', 'workreap'),
							'bacs'		=> esc_html__('Bank Transfer', 'workreap')
						)
					),
					'services_layout'  => array(
						'label' => esc_html__( 'Services Layout', 'workreap' ),
						'type'  => 'select',
						'value' => 'two',
						'desc' => esc_html__('Select services layout on search result page.', 'workreap'),
						'choices'	=> array(
							'two'  => esc_html__('Two Column', 'workreap'),
							'three'	=> esc_html__('Three column', 'workreap'),
							'four'	=> esc_html__('Four column', 'workreap')
						)
					),
					'price_filter_start' => array(
						'type' => 'text',
						'value' => 0,
						'label' => esc_html__('Price Filter Start', 'workreap'),
						'desc' => esc_html__('Select price filter starting value', 'workreap'),
					),
					'price_filter_end' => array(
						'type' => 'text',
						'value' => 1000,
						'label' => esc_html__('Price Filter End', 'workreap'),
						'desc' => esc_html__('Select price filter ending value', 'workreap'),
					),
					'services_per_page' => array(
						'type' => 'slider',
						'value' => 12,
						'properties' => array(
							'min' => 1,
							'max' => 200,
							'sep' => 1,
						),
						'label' => esc_html__('Services per page', 'workreap'),
						'desc' => esc_html__('Select services per page to show', 'workreap'),
					),
					'projects_per_page' => array(
						'type' => 'slider',
						'value' => 12,
						'properties' => array(
							'min' => 1,
							'max' => 200,
							'sep' => 1,
						),
						'label' => esc_html__('Projects per page', 'workreap'),
						'desc' => esc_html__('Select projects per page to show', 'workreap'),
					),
					'freelancers_per_page' => array(
						'type' => 'slider',
						'value' => 12,
						'properties' => array(
							'min' => 1,
							'max' => 200,
							'sep' => 1,
						),
						'label' => esc_html__('Freelancers per page', 'workreap'),
						'desc' => esc_html__('Select freelancers per page to show', 'workreap'),
					),
					'employers_per_page' => array(
						'type' => 'slider',
						'value' => 12,
						'properties' => array(
							'min' => 1,
							'max' => 200,
							'sep' => 1,
						),
						'label' => esc_html__('Employers per page', 'workreap'),
						'desc' => esc_html__('Select employers per page to show', 'workreap'),
					),
					'portfolios_per_page' => array(
						'type' => 'slider',
						'value' => 12,
						'properties' => array(
							'min' => 1,
							'max' => 200,
							'sep' => 1,
						),
						'label' => esc_html__('Portfolios per page', 'workreap'),
						'desc' => esc_html__('Select portfolios per page to show', 'workreap'),
					),
					'default_skills' => array(
						'type' => 'slider',
						'value' => 50,
						'properties' => array(
							'min' => 1,
							'max' => 200,
							'sep' => 1,
						),
						'label' => esc_html__('Default skills', 'workreap'),
						'desc' => esc_html__('Default skills to per freelancer', 'workreap'),
					),
					'remove_qrcode'  => array(
						'label' => esc_html__( 'Remove QR CODE', 'workreap' ),
						'type'  => 'select',
						'value' => 'no',
						'desc' => esc_html__('Remove QR code all over the site.', 'workreap'),
						'choices'	=> array(
							'yes'  => esc_html__('Yes', 'workreap'),
							'no'	=> esc_html__('No', 'workreap')
						)
					),
					'hide_switch_account'  => array(
						'label' => esc_html__( 'Hide switch account?', 'workreap' ),
						'type'  => 'select',
						'value' => 'no',
						'desc' => esc_html__('Hide switch account from user menu?', 'workreap'),
						'choices'	=> array(
							'yes'  => esc_html__('Yes', 'workreap'),
							'no'	=> esc_html__('No', 'workreap')
						)
					),
					'report_freelancer' => array(
						'type' => 'multi-picker',
						'label' => false,
						'desc' => '',
						'picker' => array(
							'gadget' => array(
								'label' => esc_html__('Freelancer report form', 'workreap'),
								'type' => 'select',
								'value' => 'no',
								'desc' => esc_html__('', 'workreap'),
								'choices' => array(
									'yes' => esc_html__('Yes', 'workreap'),
									'no' => esc_html__('No', 'workreap'),
								)
							)
						),
						'choices' => array(
							'no' => array(
								'report_options' => array(
									'type' => 'addable-option',
									'value' => array(esc_html__('This is the fake', 'workreap'), 
													 esc_html__('Other', 'workreap')
												),
									'desc' => esc_html__('Report form options for freelancers', 'workreap'),
									'label' => esc_html__('Add report options', 'workreap'),
									'option' => array('type' => 'text'),
									'add-button-text' => esc_html__('Add', 'workreap'),
									'sortable' => true,
								),
							),
							'default' => array(),
						),
						'show_borders' => false,
					),
	
					'report_employer' => array(
						'type' => 'multi-picker',
						'label' => false,
						'desc' => '',
						'picker' => array(
							'gadget' => array(
								'label' => esc_html__('Employer report form', 'workreap'),
								'type' => 'select',
								'value' => 'no',
								'desc' => esc_html__('', 'workreap'),
								'choices' => array(
									'yes' => esc_html__('Yes', 'workreap'),
									'no' => esc_html__('No', 'workreap'),
								)
							)
						),
						'choices' => array(
							'no' => array(
								'report_options' => array(
									'type' => 'addable-option',
									'value' => array(esc_html__('This is the fake', 'workreap'), 
													 esc_html__('Other', 'workreap')
												),
									'desc' => esc_html__('Report form options for employers', 'workreap'),
									'label' => esc_html__('Add report options', 'workreap'),
									'option' => array('type' => 'text'),
									'add-button-text' => esc_html__('Add', 'workreap'),
									'sortable' => true,
								),
							),
							'default' => array(),
						),
						'show_borders' => false,
					),
					
					'report_project' => array(
						'type' => 'multi-picker',
						'label' => false,
						'desc' => '',
						'picker' => array(
							'gadget' => array(
								'label' => esc_html__('Project report form', 'workreap'),
								'type' => 'select',
								'value' => 'no',
								'desc' => esc_html__('', 'workreap'),
								'choices' => array(
									'yes' => esc_html__('Yes', 'workreap'),
									'no' => esc_html__('No', 'workreap'),
								)
							)
						),
						'choices' => array(
							'no' => array(
								'report_options' => array(
									'type' => 'addable-option',
									'value' => array(esc_html__('This is the fake', 'workreap'), 
													 esc_html__('Other', 'workreap')
												),
									'desc' => esc_html__('Report form options for projects', 'workreap'),
									'label' => esc_html__('Add report options', 'workreap'),
									'option' => array('type' => 'text'),
									'add-button-text' => esc_html__('Add', 'workreap'),
									'sortable' => true,
								),
							),
							'default' => array(),
						),
						'show_borders' => false,
					),
					'report_service' => array(
						'type' => 'multi-picker',
						'label' => false,
						'desc' => '',
						'picker' => array(
							'gadget' => array(
								'label' => esc_html__('Service report form', 'workreap'),
								'type' => 'select',
								'value' => 'no',
								'desc' => esc_html__('', 'workreap'),
								'choices' => array(
									'yes' => esc_html__('Yes', 'workreap'),
									'no' => esc_html__('No', 'workreap'),
								)
							)
						),
						'choices' => array(
							'no' => array(
								'report_options' => array(
									'type' => 'addable-option',
									'value' => array(esc_html__('This is the fake', 'workreap'), 
													 esc_html__('Other', 'workreap')
												),
									'desc' => esc_html__('Report form options for services', 'workreap'),
									'label' => esc_html__('Add report options', 'workreap'),
									'option' => array('type' => 'text'),
									'add-button-text' => esc_html__('Add', 'workreap'),
									'sortable' => true,
								),
							),
							'default' => array(),
						),
						'show_borders' => false,
					),
					'gender_settings' => array(
						'type' => 'multi-picker',
						'label' => false,
						'desc' => '',
						'picker' => array(
							'gadget' => array(
								'label' => esc_html__('Gender type setting', 'workreap'),
								'type' => 'select',
								'value' => 'yes',
								'desc' => esc_html__('These options will be used in user registration form and also in users dashboard', 'workreap'),
								'choices' => array(
									'yes' => esc_html__('Yes', 'workreap'),
									'no' => esc_html__('No', 'workreap'),
								)
							)
						),
						'choices' => array(
							'yes' => array(
								'gender_options' => array(
									'type' => 'addable-option',
									'value' => array(esc_html__('Mr', 'workreap'), 
													 esc_html__('Miss', 'workreap')
												),
									'desc' 		=> esc_html__('', 'workreap'),
									'label' 	=> esc_html__('Add options here', 'workreap'),
									'option' 	=> array('type' => 'text'),
									'add-button-text' => esc_html__('Add', 'workreap'),
									'sortable' => true,
								),
							),
							'default' => array(),
						),
						'show_borders' => false,
					),
					'verify_user'  => array(
						'label' => esc_html__( 'Verify User', 'workreap' ),
						'type'  => 'select',
						'value' => 'verified',
						'desc' => esc_html__('Verify users( freelancer and employers ) before publicly available. Note: If you select "Need to verify, after registration" then user will not be shown in search result until user will be verified by site owner. If you select "Verify by email" then users will get an email for verification. After clicking link user will be verified and available at the website.', 'workreap'),
						'choices'	=> array(
							'verified'  => esc_html__('Verify by email', 'workreap'),
							'none'	=> esc_html__('Need to verify, after registration', 'workreap')
						)
					),
					'system_access' => array(
						'type' 		=> 'select',
						'value' 	=> 'paid',
						'label' 	=> esc_html__('System Access type?', 'workreap'),
						'desc' 		=> wp_kses( __( 'Please select only one of the following options.<br/> 
												1) In "Paid Listings for both" means both employers and freelancers have to buy a package to access all the features of the site<br/>
												2) In "Free listings for employer" all features would be free for only employers not for freelancers. <br/>
												3) In "Free for both", In this settings all the site features would be free for both employers and freelancers', 'workreap' ),array(
																'a' => array(
																	'href' => array(),
																	'title' => array()
																),
																'br' => array(),
																'em' => array(),
																'strong' => array(),
															)),
						'choices' => array(
							'paid' => esc_html__('Paid Listings for both', 'workreap'),
							'employer_free' => esc_html__('Free listings for employer', 'workreap'),
							'both' => esc_html__('Free for both', 'workreap'),
						),
					),
					'remove_chat' => array(	
						'label' => esc_html__('Remove chat from packages?', 'workreap'),
						'type' 	=> 'select',
						'value' => 'no',
						'desc' 	=> esc_html__('Remove chat options from packages and make it free for all users', 'workreap'),
						'choices' => array(
							'yes' 		=> esc_html__('Yes, remove from packages and make it free for all users', 'workreap'),
							'no' 		=> esc_html__('No', 'workreap'),
						)
					),
					'hide_departments' => array(	
						'label' => esc_html__('Hide departments and employees?', 'workreap'),
						'type' 	=> 'select',
						'value' => 'no',
						'desc' 	=> esc_html__('Hide departments and employees from signup, employer dashboard and from search filters?', 'workreap'),
						'choices' => array(
							'signup' 	=> esc_html__('Remove only from signup form.', 'workreap'),
							'both' 		=> esc_html__('Remove from signup form and dashboard', 'workreap'),
							'site' 		=> esc_html__('Remove all over the site', 'workreap'),
							'no' 		=> esc_html__('Donot remove', 'workreap'),
						)
					),
					'hide_status' => array(	
						'label' => esc_html__('Hide status?', 'workreap'),
						'type' 	=> 'select',
						'value' => 'show',
						'desc' 	=> esc_html__('Hide online/offline status from employers and freelancers profiles', 'workreap'),
						'choices' => array(
							'show' 	=> esc_html__('Show', 'workreap'),
							'hide' 	=> esc_html__('Hide', 'workreap'),
						)
					),
					'hide_map' => array(	
						'label' => esc_html__('Hide Map?', 'workreap'),
						'type' 	=> 'select',
						'value' => 'show',
						'desc' 	=> esc_html__('Hide map from jobs, profile and services', 'workreap'),
						'choices' => array(
							'show' 	=> esc_html__('Show', 'workreap'),
							'hide' 	=> esc_html__('Hide', 'workreap'),
						)
					),
					'freelancer_stats' => array(	
						'label' => esc_html__('Hide freelancer stats', 'workreap'),
						'type' 	=> 'select',
						'value' => 'show',
						'desc' 	=> esc_html__('Hide freelancer stats on freelancer detail page', 'workreap'),
						'choices' => array(
							'show' 	=> esc_html__('Show', 'workreap'),
							'hide' 	=> esc_html__('Hide', 'workreap'),
						)
					),
					'application_access' => array(	
						'label' => esc_html__('Application Access', 'workreap'),
						'type' 	=> 'select',
						'value' => 'both',
						'desc' 	=> esc_html__('Enable Application Access?', 'workreap'),
						'choices' => array(
							'service_base' 	=> esc_html__('Service based application', 'workreap'),
							'job_base' 		=> esc_html__('Job based application', 'workreap'),
							'both' 			=> esc_html__('Both Service and Job based application', 'workreap'),
						)
					),
                    'search_freelancer_tpl' => array(
						'label' 		=> esc_html__('Choose Freelancer Search Page', 'workreap'),
						'type' 			=> 'multi-select',
						'population' 	=> 'posts',
						'source' 		=> 'page',
						'desc' 			=> esc_html__('Choose freelancer search template page.', 'workreap'),
						'limit' => 1,
						'prepopulate' => 100,
					), 
					'search_employer_tpl' => array(
						'label' => esc_html__('Choose Employer Search Page', 'workreap'),
						'type' => 'multi-select',
						'population' => 'posts',
						'source' => 'page',
						'desc' => esc_html__('Choose employer search template page.', 'workreap'),
						'limit' => 1,
						'prepopulate' => 100,
					), 
					'search_job_tpl' => array(
						'label' => esc_html__('Choose Job Search Page', 'workreap'),
						'type' => 'multi-select',
						'population' => 'posts',
						'source' => 'page',
						'desc' => esc_html__('Choose job search template page.', 'workreap'),
						'limit' => 1,
						'prepopulate' => 100,
					), 
					'job_bundles_tpl' => array(
						'label' => esc_html__('Choose Posting Job Bundle Selection page', 'workreap'),
						'type' => 'multi-select',
						'population' => 'posts',
						'source' => 'page',
						'desc' => esc_html__('Choose Posting Job Bundle Selection template page.', 'workreap'),
						'limit' => 1,
						'prepopulate' => 100,
					), 
					'job_addons_tpl' => array(
						'label' => esc_html__('Choose Posting Job Addons Selection page', 'workreap'),
						'type' => 'multi-select',
						'population' => 'posts',
						'source' => 'page',
						'desc' => esc_html__('Choose Posting Job Addons Selection template page.', 'workreap'),
						'limit' => 1,
						'prepopulate' => 100,
					), 
					'search_services_tpl' => array(
						'label' 		=> esc_html__('Choose Service Search Page', 'workreap'),
						'type' 			=> 'multi-select',
						'population' 	=> 'posts',
						'source' 		=> 'page',
						'desc' 			=> esc_html__('Choose Service search template page.', 'workreap'),
						'limit' 		=> 1,
						'prepopulate' 	=> 100,
					),
					'dashboard_tpl' => array(
						'label' => esc_html__('Choose Dashboard Page', 'workreap'),
						'type' => 'multi-select',
						'population' => 'posts',
						'source' => 'page',
						'desc' => esc_html__('Choose dashboard template page.', 'workreap'),
						'limit' => 1,
						'prepopulate' => 100,
					),
					'calendar_format'    => array(
						'label' => esc_html__( 'Calendar Date Format', 'workreap' ),
						'type'  => 'select',
						'value'  => 'Y-m-d',
						'desc' => esc_html__('Select your calendar date format.', 'workreap'),
						'choices'	=> array(
							'Y-m-d'	  => 'Y-m-d',
							'd-m-Y'	  => 'd-m-Y',
							'Y/m/d'	  => 'Y/m/d',
							'd/m/Y'	  => 'd/m/Y',
						)
					),
					'calendar_locale'    => array(
						'label' => esc_html__( 'Calendar Language', 'workreap' ),
						'type'  => 'text',
						'value'  => '',
						'desc' => wp_kses( __( 'Add 639-1 code. It will be two digit code like "en" for english. Leave it empty to use default. Click here to get code <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"> Get Code </a>', 'workreap' ),array(
																		'a' => array(
																			'href' => array(),
																			'title' => array()
																		),
																		'br' => array(),
																		'em' => array(),
																		'strong' => array(),
																	)),
					),
					'shortname_option' => array(
						'type' => 'switch',
						'value' => 'disable',
						'attr' => array(),
						'label' => esc_html__('Shortened names', 'workreap'),
						'desc' => esc_html__('Enable shortened names. If enabled then First name and last name Capital letter will show. For example ff first name is ABC and last name is XYZ then short name will be ABC X', 'workreap'),
						'left-choice' => array(
							'value' => 'disable',
							'label' => esc_html__('Disable', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'enable',
							'label' => esc_html__('Enable', 'workreap'),
						),
					),
					'ppt_template' => array(
						'type' => 'switch',
						'value' => 'disable',
						'attr' => array(),
						'label' => esc_html__('Articulate Content for Portofolios', 'workreap'),
						'desc' => wp_kses( __( 'Enable Articulate Content for portfolios. If enabled then you need to activate plugin "Insert or Embed Articulate Content into WordPress". <a href="https://wordpress.org/plugins/insert-or-embed-articulate-content-into-wordpress/" target="_blank"> Get Plugin </a>', 'workreap' ),array(
							'a' => array(
								'href' => array(),
								'title' => array()
							),
							'br' => array(),
							'em' => array(),
							'strong' => array(),
						)),
						'left-choice' => array(
							'value' => 'disable',
							'label' => esc_html__('Disable', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'enable',
							'label' => esc_html__('Enable', 'workreap'),
						),
					),
					'hide_hideshares'  => array(
						'label' => esc_html__( 'Hide sharing?', 'workreap' ),
						'type'  => 'select',
						'value' => 'no',
						'desc' => esc_html__('Hide employers and freelances profiles and jobs sharing', 'workreap'),
						'choices'	=> array(
							'yes' 	=> esc_html__('Yes', 'workreap'),
							'no'	=> esc_html__('No', 'workreap')
						)
					),
                ),
            ), 
            'images-settings' => array(
                'title' => esc_html__('Images Settings', 'workreap'),
                'type' => 'tab',
                'options' => array(
                    'default_freelancer_banner' => array(
						'label' => esc_html__('Upload freelancer banner', 'workreap'),
						'desc' => esc_html__('Upload default banner image for freelancer. leave it empty to hide use from theme directory\'ry. Upload minimum size 1920x450', 'workreap'),
						'type' => 'upload',
					),
					'default_employer_banner' => array(
						'label' => esc_html__('Upload employer banner', 'workreap'),
						'desc' => esc_html__('Upload default banner image for employer. leave it empty to hide use from theme directory. Upload minimum size 1110x300', 'workreap'),
						'type' => 'upload',
					),
					'default_freelancer_avatar' => array(
						'label' => esc_html__('Upload freelancer avatar', 'workreap'),
						'desc' => esc_html__('Upload default avatar image for freelancer. leave it empty to hide use from theme directory. Upload minimum size 225x225', 'workreap'),
						'type' => 'upload',
					),
					'default_employer_avatar' => array(
						'label' => esc_html__('Upload employer avatar', 'workreap'),
						'desc' => esc_html__('Upload default avatar image for employer. leave it empty to hide use from theme directory. Upload minimum size 100x100', 'workreap'),
						'type' => 'upload',
					),
					'dir_datasize' => array(
						'type' => 'text',
						'value' => '5242880',
						'attr' => array(),
						'label' => esc_html__('Add upload size', 'workreap'),
						'desc' => esc_html__('Maximum image upload size. Max 5MB, add in bytes. for example 5MB = 5242880 ( 1024x1024x5 )', 'workreap'),
						'help' => esc_html__('', 'workreap'),
					),
					'total_freelancers' => array(
						'label' => esc_html__('Dashboard  favorite freelancers statistic', 'workreap'),
						'desc' => esc_html__('Upload default favorites freelancer statistics image. leave it empty to hide use from theme directory\'ry. Upload minimum size 100x100', 'workreap'),
						'type' => 'upload',
					),
					'total_employers' => array(
						'label' => esc_html__('Dashboard favorite companies statistic', 'workreap'),
						'desc' => esc_html__('Upload default favorites companies statistics image. leave it empty to hide use from theme directory\'ry. Upload minimum size 100x100', 'workreap'),
						'type' => 'upload',
					),
					'total_jobs' => array(
						'label' => esc_html__('Dashboard favorites jobs statistic', 'workreap'),
						'desc' => esc_html__('Upload default favorites Jobs statistics image. leave it empty to hide use from theme directory\'ry. Upload minimum size 100x100', 'workreap'),
						'type' => 'upload',
					),
					'featured_job_img' => array(
						'label' => esc_html__('Dashboard featured jobs statistic', 'workreap'),
						'desc' => esc_html__('Upload dashboard featured jobs image. leave it empty to hide use default icon', 'workreap'),
						'type' => 'upload',
					),
					'new_messages' => array(
						'label' => esc_html__('Dashboard inbox statistic', 'workreap'),
						'desc' => esc_html__('Upload dashboard inbox statistic image. leave it empty to hide use default icon', 'workreap'),
						'type' => 'upload',
					),
					'latest_proposals' => array(
						'label' => esc_html__('Dashboard latest proposal statistic', 'workreap'),
						'desc' => esc_html__('Upload dashboard latest proposal statistic image. leave it empty to hide use default icon', 'workreap'),
						'type' => 'upload',
					),
					'package_expiry' => array(
						'label' => esc_html__('Dashboard package expiry statistic', 'workreap'),
						'desc' => esc_html__('Upload dashboard package expiry statistic image. leave it empty to hide use default icon', 'workreap'),
						'type' => 'upload',
					),
					'saved_items' => array(
						'label' => esc_html__('Dashboard saved items statistic', 'workreap'),
						'desc' => esc_html__('Upload dashboard saved items statistic image', 'workreap'),
						'type' => 'upload',
					),
					'total_ongoing_job' => array(
						'label' => esc_html__('Dashboard ongoing jobs statistic', 'workreap'),
						'desc' => esc_html__('Upload dashboard ongoing jobs statistic image. leave it empty to hide use default icon', 'workreap'),
						'type' => 'upload',
					),
					'total_completed_job' => array(
						'label' => esc_html__('Dashboard completed jobs statistic', 'workreap'),
						'desc' => esc_html__('Upload dashboard completed jobs statistic image. leave it empty to hide use default icon', 'workreap'),
						'type' => 'upload',
					),
					'total_cancelled_job' => array(
						'label' => esc_html__('Dashboard cancelled jobs statistic', 'workreap'),
						'desc' => esc_html__('Upload dashboard cancelled jobs statistic image. leave it empty to hide use default icon', 'workreap'),
						'type' => 'upload',
					),
					'total_posted_job' => array(
						'label' => esc_html__('Dashboard  statistic', 'workreap'),
						'desc' => esc_html__('Upload dashboard total posted jobs image. leave it empty to hide use default icon', 'workreap'),
						'type' => 'upload',
					),
					'avalible_balance_img' => array(
						'label' => esc_html__('Dashboard available balance statistic', 'workreap'),
						'desc' => esc_html__('Upload dashboard available balance image. leave it empty to hide use default icon', 'workreap'),
						'type' => 'upload',
					),
					'current_balance_img' => array(
						'label' => esc_html__('Dashboard current balance statistic', 'workreap'),
						'desc' => esc_html__('Upload dashboard current balance image. leave it empty to hide use default icon', 'workreap'),
						'type' => 'upload',
					),
					'total_completed_services' => array(
						'label' => esc_html__('Dashboard completed services statistic', 'workreap'),
						'desc' 	=> esc_html__('Upload default completed services statistics image. leave it empty to hide use default icon', 'workreap'),
						'type' 	=> 'upload',
					),
					'total_cancelled_services' => array(
						'label' => esc_html__('Dashboard cancelled services statistic', 'workreap'),
						'desc' 	=> esc_html__('Upload default cancelled services statistics image. leave it empty to hide use default icon', 'workreap'),
						'type' 	=> 'upload',
					),
					'total_ongoing_services' => array(
						'label' => esc_html__('Dashboard ongoing statistic', 'workreap'),
						'desc' 	=> esc_html__('Upload default ongoing services statistics image. leave it empty to hide use default icon', 'workreap'),
						'type' 	=> 'upload',
					),
					'total_sales_services' => array(
						'label' => esc_html__('Dashboard  statistic', 'workreap'),
						'desc' 	=> esc_html__('Upload default sold services statistics image. leave it empty to hide use default icon', 'workreap'),
						'type' 	=> 'upload',
					),
					'nrf_favorites' => array(
						'label' => esc_html__('Favorites listings', 'workreap'),
						'desc' 	=> esc_html__('This image will be used as background for favorites users listing. Size : 200x200', 'workreap'),
						'type' 	=> 'upload',
					),
					'nrf_messages' => array(
						'label' => esc_html__('Inbox', 'workreap'),
						'desc' 	=> esc_html__('This image will be used as background for inbox. Size : 200x200', 'workreap'),
						'type' 	=> 'upload',
					),
					'nrf_create' => array(
						'label' => esc_html__('Create record/articles/projects', 'workreap'),
						'desc' 	=> esc_html__('This image will be used as background for create listings. Size : 200x200', 'workreap'),
						'type' 	=> 'upload',
					),
					'nrf_found' => array(
						'label' => esc_html__('No record found', 'workreap'),
						'desc' 	=> esc_html__('This image will be used as background for no record found. Size : 200x200', 'workreap'),
						'type' 	=> 'upload',
					),
					'nrf_users' => array(
						'label' => esc_html__('Users', 'workreap'),
						'desc' 	=> esc_html__('This image will be used as background for users. Size : 200x200', 'workreap'),
						'type' 	=> 'upload',
					),
					'payout_bank' => array(
						'label' => esc_html__('Payouts Bank transfer', 'workreap'),
						'desc' 	=> esc_html__('Please upload payouts bank transfer image. Size : 100x30', 'workreap'),
						'type' 	=> 'upload',
					),
					'payout_paypal' => array(
						'label' => esc_html__('Payouts PayPal', 'workreap'),
						'desc' 	=> esc_html__('Please upload payouts PayPal image. Size : 100x30', 'workreap'),
						'type' 	=> 'upload',
					),
                ),
			),
			'jobs-settings' => array(
                'title' => esc_html__('Jobs Settings', 'workreap'),
                'type' => 'tab',
                'options' => array(
                    'job_option' => array(
						'type' => 'switch',
						'value' => 'disable',
						'attr' => array(),
						'label' => esc_html__('Jobs location', 'workreap'),
						'desc' => esc_html__('Enable/Disable jobs location. On enable job location will show Onsite, Partial Onsite and Remote', 'workreap'),
						'left-choice' => array(
							'value' => 'disable',
							'label' => esc_html__('Disable', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'enable',
							'label' => esc_html__('Enable', 'workreap'),
						),
					),
					'job_price_option' => array(
						'type' => 'switch',
						'value' => 'disable',
						'attr' => array(),
						'label' => esc_html__('Jobs price range', 'workreap'),
						'desc' => esc_html__('Enable/Disable jobs price range options', 'workreap'),
						'left-choice' => array(
							'value' => 'disable',
							'label' => esc_html__('Disable', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'enable',
							'label' => esc_html__('Enable', 'workreap'),
						),
					),
					'multiselect_freelancertype' => array(
						'type' => 'switch',
						'value' => 'disable',
						'attr' => array(),
						'label' => esc_html__('Freelancer type selection', 'workreap'),
						'desc' => esc_html__('Enable it to make freelancer type multiselect. Default would be single select', 'workreap'),
						'left-choice' => array(
							'value' => 'disable',
							'label' => esc_html__('Single select', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'enable',
							'label' => esc_html__('Multi-select', 'workreap'),
						),
					),
					'job_experience_option' => array(
						'type' => 'multi-picker',
						'label' => false,
						'desc' => '',
						'picker' => array(
							'gadget' => array(
								'type' => 'switch',
								'value' => 'disable',
								'attr' => array(),
								'label' => esc_html__('Experience', 'workreap'),
								'desc' => esc_html__('Enable or disable experience', 'workreap'),
								'left-choice' => array(
									'value' => 'disable',
									'label' => esc_html__('Disable', 'workreap'),
								),
								'right-choice' => array(
									'value' => 'enable',
									'label' => esc_html__('Enable', 'workreap'),
								),
							)
						),
						'choices' => array(
							'enable' => array( 
								'multiselect_experience' => array(
									'type' => 'switch',
									'value' => 'disable',
									'attr' => array(),
									'label' => esc_html__('Experience selection type', 'workreap'),
									'desc' => esc_html__('Enable it to make experience type multiselect. Default would be single select', 'workreap'),
									'left-choice' => array(
										'value' => 'single',
										'label' => esc_html__('Single select', 'workreap'),
									),
									'right-choice' => array(
										'value' => 'multiselect',
										'label' => esc_html__('Multi-select', 'workreap'),
									),
								),
							)
						)
					),
					'job_milestone_option' => array(
						'type' => 'multi-picker',
						'label' => false,
						'desc' => '',
						'picker' => array(
							'gadget' => array(
								'type' => 'switch',
								'value' => 'disable',
								'attr' => array(),
								'label' => esc_html__('Job milestone', 'workreap'),
								'desc' => esc_html__('Enable or disable Job milestone', 'workreap'),
								'left-choice' => array(
									'value' => 'disable',
									'label' => esc_html__('Disable', 'workreap'),
								),
								'right-choice' => array(
									'value' => 'enable',
									'label' => esc_html__('Enable', 'workreap'),
								),
							)
						),
						'choices' => array(
							'enable' => array( 
								'total_budget'  => array(
									'label' => esc_html__( 'Budget image', 'workreap' ),
									'type'  => 'upload',
									'desc' => esc_html__('Upload budget image', 'workreap')
								),
								'in_escrow'  => array(
									'label' => esc_html__( 'In escrow image', 'workreap' ),
									'type'  => 'upload',
									'desc' => esc_html__('Upload in escrow image', 'workreap')
								),
								
								'milestone_paid'  => array(
									'label' => esc_html__( 'Milestone paid image', 'workreap' ),
									'type'  => 'upload',
									'desc' => esc_html__('Upload milestone paid image', 'workreap')
								),
								
								'remainings'  => array(
									'label' => esc_html__( 'Remainings image', 'workreap' ),
									'type'  => 'upload',
									'desc' => esc_html__('Upload remianings image', 'workreap')
								),
								
							)
						)
					)
                ),
			), 
			'company-settings' => array(
                'title' => esc_html__('Company Settings', 'workreap'),
                'type' => 'tab',
                'options' => array(
					'comapny_name' => array(
						'type' => 'switch',
						'value' => 'disable',
						'attr' => array(),
						'label' => esc_html__('Company name', 'workreap'),
						'desc' => esc_html__('Enable/Disable company name in employers profile', 'workreap'),
						'left-choice' => array(
							'value' => 'disable',
							'label' => esc_html__('Disable', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'enable',
							'label' => esc_html__('Enable', 'workreap'),
						),
					),
					'company_job_title' => array(
						'type' => 'switch',
						'value' => 'disable',
						'attr' => array(),
						'label' => esc_html__('Job title', 'workreap'),
						'desc' => esc_html__('Enable/ the job title/field of work option', 'workreap'),
						'left-choice' => array(
							'value' => 'disable',
							'label' => esc_html__('Disable', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'enable',
							'label' => esc_html__('Enable', 'workreap'),
						),
					),
				),
			),
			'freelancer-settings' => array(
                'title' => esc_html__('Freelancer Settings', 'workreap'),
                'type' => 'tab',
                'options' => array(
					'freelancer_price_option' => array(
						'type' => 'switch',
						'value' => 'disable',
						'attr' => array(),
						'label' => esc_html__('Per hour rate range', 'workreap'),
						'desc' => esc_html__('Enable Per hour rate range fields', 'workreap'),
						'left-choice' => array(
							'value' => 'disable',
							'label' => esc_html__('Disable', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'enable',
							'label' => esc_html__('Enable', 'workreap'),
						),
					),
					'freelancer_gallery_option' => array(
						'type' => 'switch',
						'value' => 'disable',
						'attr' => array(),
						'label' => esc_html__('Gallery', 'workreap'),
						'desc' => esc_html__('Enable or disable gallery.', 'workreap'),
						'left-choice' => array(
							'value' => 'disable',
							'label' => esc_html__('Disable', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'enable',
							'label' => esc_html__('Enable', 'workreap'),
						),
					),
					'freelancertype_multiselect' => array(
						'type' => 'switch',
						'value' => 'disable',
						'attr' => array(),
						'label' => esc_html__('Freelancer type', 'workreap'),
						'desc' => esc_html__('Enable it to make freelancer type multiselect. Default would be single select', 'workreap'),
						'left-choice' => array(
							'value' => 'disable',
							'label' => esc_html__('Single', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'enable',
							'label' => esc_html__('Multiselect', 'workreap'),
						),
					),
					'freelancer_industrial_experience' => array(
						'type' => 'switch',
						'value' => 'disable',
						'attr' => array(),
						'label' => esc_html__('Freelancer industrial experience', 'workreap'),
						'desc' => esc_html__('Enable or disable freelancer industrial experience settings', 'workreap'),
						'left-choice' => array(
							'value' => 'disable',
							'label' => esc_html__('Disable', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'enable',
							'label' => esc_html__('Enable', 'workreap'),
						),
					),
					'freelancer_specialization' => array(
						'type' => 'switch',
						'value' => 'disable',
						'attr' => array(),
						'label' => esc_html__('Freelancer specialization', 'workreap'),
						'desc' => esc_html__('Enable or disable freelancer specialization.', 'workreap'),
						'left-choice' => array(
							'value' => 'disable',
							'label' => esc_html__('Disable', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'enable',
							'label' => esc_html__('Enable', 'workreap'),
						),
					),
					'display_type' => array(	
						'label' => esc_html__('Display type for skills, specialization and industrial experience', 'workreap'),
						'type' 	=> 'select',
						'value' => 'any',
						'desc' 	=> esc_html__('Select display type for skills, specialization and industrial experience', 'workreap'),
						'choices' => array(
							'number' 	=> esc_html__('Percentage', 'workreap'),
							'year' 		=> esc_html__('Years', 'workreap'),
						)
					),
                ),
			), 
			'proposal-settings' => array(
                'title' => esc_html__('Proposal Settings', 'workreap'),
                'type' => 'tab',
                'options' => array(
                    'dir_proposal_page' => array(
						'label' => esc_html__('Choose Submit Proposal Page', 'workreap'),
						'type' => 'multi-select',
						'population' => 'posts',
						'source' => 'page',
						'desc' => esc_html__('Choose page to show submit project proposal form.', 'workreap'),
						'limit' => 1,
						'prepopulate' => 100,
					),
					
					'hint_text' => array(
						'type' => 'textarea',
						'value' => '',
						'attr' => array(),
						'label' => esc_html__('Service Fee Hint', 'workreap'),
						'desc' => esc_html__('Add hint text for service field', 'workreap'),
					),
					'hint_text_two' => array(
						'type' => 'textarea',
						'value' => '',
						'attr' => array(),
						'label' => esc_html__('Service Fee Deduction Hint', 'workreap'),
						'desc' => esc_html__('Add hint text for deduction price field', 'workreap'),
					),  
					'proposal_connects' => array(
                                        'type' => 'text',
                                        'value' => '',                                       
                                        'label' => esc_html__('Proposal credits', 'workreap'),
                                        'desc' => esc_html__('No of credits per proposal', 'workreap'),                                        
                                    ),
					'proposal_price_type' => array(	
						'label' => esc_html__('Allow proposal price type', 'workreap'),
						'type' 	=> 'select',
						'value' => 'any',
						'desc' 	=> esc_html__('Allow the freelancers to add proposal price within the employer budget or any price', 'workreap'),
						'choices' => array(
							'budget' 	=> esc_html__('Within the budget', 'workreap'),
							'any' 		=> esc_html__('Any Price', 'workreap'),
						)
					),
					'proposal_message_option' => array(
						'label' => esc_html__('Chat option', 'workreap'),
						'type' => 'switch',
						'value' => 'disable',
						'desc' => esc_html__('Enable/Disable chat option for employer on project proposal listing page.', 'workreap'),
						'left-choice' => array(
							'value' => 'enable',
							'label' => esc_html__('Enable', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'disable',
							'label' => esc_html__('Disable', 'workreap'),
						),
					)
                ),
            ),                
			'review-settings' => array(
                'title' => esc_html__('Reviews Settings', 'workreap'),
                'type' => 'tab',
                'options' => array(
                    'project_ratings' => array(
						'type' => 'addable-option',
						'value' => array(esc_html__('How was my proffesional behaviour?', 'workreap'), 
										 esc_html__('How was my quality of work?', 'workreap'), 
										 esc_html__('Was I focused to deadline?', 'workreap'),
										 esc_html__('Was it worth it having my services?', 'workreap')
									),
						'label' => esc_html__('Rating Headings', 'workreap'),
						'desc' => esc_html__('Add leave your rating headings.', 'workreap'),
						'option' => array('type' => 'text'),
						'add-button-text' => esc_html__('Add', 'workreap'),
						'sortable' => true,
					),
					'cus_services_reviews' => array(
											'type' => 'html',
											'html' => esc_html__('Reviews for Services', 'workreap'),
											'label'=> esc_html__('', 'workreap'),
											'desc' => esc_html__('Add Question for services reviews.', 'workreap'),
											'help' => esc_html__('', 'workreap'),
											'images_only' => true,
										),
                    'services_ratings' => array(
						'type' => 'addable-option',
						'value' => array(esc_html__('How was my proffesional behaviour?', 'workreap'), 
										 esc_html__('How was my quality of work?', 'workreap'), 
										 esc_html__('Was I focused to deadline?', 'workreap'),
										 esc_html__('Was it worth it having my services?', 'workreap')
									),
						'label' 			=> esc_html__('Rating Headings', 'workreap'),
						'desc' 				=> esc_html__('Add leave your rating headings.', 'workreap'),
						'option' 			=> array('type' => 'text'),
						'add-button-text' 	=> esc_html__('Add', 'workreap'),
						'sortable' 			=> true,
					)
                ),
            ),
			'help-settings' => array(
                'title' => esc_html__('Help and Support Settings', 'workreap'),
                'type' => 'tab',
                'options' => array(
                    'general' => array(
                        'title' => esc_html__('Help and Support Settings.', 'workreap'),
                        'type' => 'tab',
                        'options' => array(
                            'help-group' => array(
                                'type' => 'group',
                                'options' => array(              
                                    'help_support' => array(
                                        'type' => 'multi-picker',
                                        'label' => false,
                                        'desc' => '',
                                        'picker' => array(
                                            'gadget' => array(
                                                'type' => 'switch',
                                                'value' => 'disable',
                                                'attr' => array(),
                                                'label' => esc_html__('Help and Support', 'workreap'),
                                                'desc' => esc_html__('Enable/Disable help and Support.', 'workreap'),
                                                'left-choice' => array(
                                                    'value' => 'disable',
                                                    'label' => esc_html__('Disable', 'workreap'),
                                                ),
                                                'right-choice' => array(
                                                    'value' => 'enable',
                                                    'label' => esc_html__('Enable', 'workreap'),
                                                ),
                                            )
                                        ),
                                        'choices' => array(
                                            'enable' => array( 
												'help_title' => array(
													'label' => esc_html__('Help and Support Heading?', 'workreap'),
													'type' => 'text',
													'value' => ''
												),
												'help_desc' => array(
													'type' => 'textarea',
													'value' => '',
													'label' => esc_html__('Help Description', 'workreap'),
												),
                                                'faq' => array(
													'label' => esc_html__('FAQ', 'workreap'),
													'type' => 'addable-popup',
													'value' => array(),
													'desc' => esc_html__('Add Question and answer for help and Support.', 'workreap'),
													'popup-options' => array(
														'faq_question' => array(
															'label' => esc_html__('Question', 'workreap'),
															'type' => 'text',
															'value' => '',
															'desc' => esc_html__('The Question for help and Support', 'workreap')
														),
														'faq_answer' => array(
															'label' => esc_html__('Answer', 'workreap'),
															'type' => 'wp-editor',
															'value' => '',
															'desc' => esc_html__('', 'workreap')
														),
													),
													'template' => '{{- faq_question }}',
												),
												'contact_subject' => array(
													'type' => 'addable-option',
													'value' => array(esc_html__('Query', 'workreap'), 
																	 esc_html__('Query Type', 'workreap'), 
																),
													'label' => esc_html__('Contact subjects', 'workreap'),
													'desc' => esc_html__('Add contact subjects.', 'workreap'),
													'option' => array('type' => 'text'),
													'add-button-text' => esc_html__('Add', 'workreap'),
													'sortable' => true,
												),
                                            ),
                                            'default' => array(),
                                        ),
                                        'show_borders' => false,
                                    ),                          
                                )
                            ),
                        )
                    ),
				)
			),
			'notifications_messages' => array(
                'title' => esc_html__('Notification Messages', 'workreap'),
                'type' => 'tab',
                'options' => array(
					'job_invitation_message' => array(
						'label' => esc_html__( 'Job Invitation Message', 'workreap' ),
						'type'  => 'textarea',
						'value' => "Hello [FREELANCER]\nFor your great performance, You have been invited to work in the following project\n[PROJECT_LINK]\nCome on, open the project and submit your proposal.",
						'desc' 	=> 'Describe the job invitation message that will be sent to the selected freelancers in one to one projects.<br/> 
							use the following keywords to express their value in the message:<br/>
							1) [FREELANCER]   : The freelancer name that will receive the invitation message.<br/>
							2) [PROJECT_LINK] : The link of the project in which the employer wants to invite the freelancer to work.<br/>',
					),
					'job_invitation_notice_priod_message' => array(
						'label' => esc_html__( 'Job Invitation Notice Period Message', 'workreap' ),
						'type'  => 'textarea',
						'value' => "Notice: You have only [INVITATION_CANCELLATION_PERIOD] to accept the invitation and submit your proposal.",
						'desc' 	=> 'Describe the job invitation message notice that will be prepended to the message sent to the freelancer to indicate the allowed time to submit the proposal to the project.<br/> 
							use the following keywords to express their value in the message:<br/>
							1) [INVITATION_CANCELLATION_PERIOD] : The invitation cancellation period (example: "8 hours").',
					),
					'job_invitation_cancellation_employer_message' => array(
						'label' => esc_html__( 'Job Invitation Cancellation Message (For Employer)', 'workreap' ),
						'type'  => 'textarea',
						'value' => "Your invitation of the project \"[PROJECT_NAME]\" to the freelancer \"[FREELANCER]\" has been ignored.\nPlease choose another freelancer and send him the invitation from the freelancers page.",
						'desc' 	=> 'Describe the job invitation cancellation message that will be sent to the employer if the selected freelancer ignored the invitation.<br/> 
							use the following keywords to express their value in the message:<br/>
							1) [PROJECT_NAME] : The name of the project.<br />
							2) [FREELANCER]   : The name of the freelancer who ignored the job invitation.<br/>
							3) [VIEW_FREELANCERS_LINK] : All freelancers view page link.<br/>',
					),
					'job_invitation_cancellation_freelancer_message' => array(
						'label' => esc_html__( 'Job Invitation Cancellation Message (For Freelancer)', 'workreap' ),
						'type'  => 'textarea',
						'value' => "The invitation of the project \"[PROJECT_NAME]\" sent to you has been ignored automatically by the system due to inactivity 
							for [NOTICE_PERIOD].",
						'desc' 	=> 'Describe the job invitation cancellation message that will be sent to the freelancer if the system cancelled the invitation
							due to not responding during all the notice period of the invitation.<br/> 
							use the following keywords to express their value in the message:<br/>
							1) [PROJECT_NAME]  : The name of the project.<br />
							2) [NOTICE_PERIOD] : The job invitation notice period (for example: 24 hours).<br/>',
					),
					'job_without_freelancer_message' => array(
						'label' => esc_html__( '"One To One" Job Without Freelancer Message', 'workreap' ),
						'type'  => 'textarea',
						'value' => "Your one to one project \"[PROJECT_NAME]\" is missing a freelancer to work on it.\nPlease choose a freelancer and send him the invitation from the freelancers page.",
						'desc' 	=> 'Describe the message that will be sent to the employer in case of creating a project without selecting any freelancer.<br/> 
							use the following keywords to express their value in the message:<br/>
							1) [PROJECT_NAME]  : The name of the project.<br />',
					),
					'job_hired_freelancer_message' => array(
						'label' => esc_html__( 'Job Hired For Freelancer', 'workreap' ),
						'type'  => 'textarea',
						'value' => "Congrats! You've been hired in the project \"[PROJECT_NAME]\".\nPlease visit your ongoing projects and continue your great work.\n",
						'desc' 	=> 'Describe the message that will be sent to the freelancer in case of hiring him in project.<br/> 
							use the following keywords to express their value in the message:<br/>
							1) [PROJECT_NAME]  : The name of the project.<br />',
					),
                ),
			),
        )
    )
);
