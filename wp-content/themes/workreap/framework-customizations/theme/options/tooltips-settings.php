<?php

if (!defined('FW')) {
    die('Forbidden');
}
$options = array(
    'tooltips' => array(
        'title' => esc_html__('Tooltips Settings', 'workreap'),
        'type' => 'tab',
        'options' => array( 
			'tip_content_bg' => array(
				'type' => 'color-picker',
				'value' => '#ff5851',
				'label' => esc_html__('Content background?', 'workreap'),
				'desc' => esc_html__('Select tooltip content background color.', 'workreap'),
			),
			'tip_content_color' => array(
				'type' => 'color-picker',
				'value' => '#FFF',
				'label' => esc_html__('Content color?', 'workreap'),
				'desc' => esc_html__('Select tooltip content text color.', 'workreap'),
			),
			'tip_title_bg' => array(
				'type' => 'color-picker',
				'value' => '#323232',
				'label' => esc_html__('Title background?', 'workreap'),
				'desc' => esc_html__('Select tooltip title background color.', 'workreap'),
			),
			'tip_title_color' => array(
				'type' => 'color-picker',
				'value' => '#FFF',
				'label' => esc_html__('Content color?', 'workreap'),
				'desc' => esc_html__('Select tooltip title text color.', 'workreap'),
			),
			'element-tip' => array(
				'type' => 'html',
				'html' => 'Elements Tooltip',
				'label' => esc_html__('', 'workreap'),
				'desc' => esc_html__('Please add elements tooltip, leave them empty to hide. Content is compulsory to show tooltip. Titles are optional.', 'workreap'),
			),
			
			'tip_first_name' => array(
				'type' => 'addable-box',
				'label' => esc_html__('First name', 'workreap'),
				'desc' => esc_html__('Add tooltip for first name in profile settings.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_last_name' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Last name', 'workreap'),
				'desc' => esc_html__('Add tooltip for last name in profile settings.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_min_price' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Min price', 'workreap'),
				'desc' => esc_html__('Add tooltip for min price in profile settings.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_max_price' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Max price', 'workreap'),
				'desc' => esc_html__('Add tooltip for max price in profile settings.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_perhour' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Per hour rate', 'workreap'),
				'desc' => esc_html__('Add tooltip for per hour rate in profile settings.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_tagline' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Tag line', 'workreap'),
				'desc' => esc_html__('Add tooltip for tagline in profile settings.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_display_name' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Display name', 'workreap'),
				'desc' => esc_html__('Add tooltip for display name in profile settings.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_longitude' => array(
				'type' => 'addable-box',
				'label' => esc_html__('longitude', 'workreap'),
				'desc' => esc_html__('Add tooltip for longitude in dashboard for locations.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_latitude' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Latitude', 'workreap'),
				'desc' => esc_html__('Add tooltip for latitude in dashboard for locations.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),

			'tip_paypal_email' => array(
				'type' => 'addable-box',
				'label' => esc_html__('PayPal Payouts', 'workreap'),
				'desc' => esc_html__('Add tooltip for PayPal payouts settings', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_bank_account_name' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Bank Account Name', 'workreap'),
				'desc' => esc_html__('Add tooltip for Bank account title payouts settings', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_bank_account_number' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Account no', 'workreap'),
				'desc' => esc_html__('Add tooltip for Bank account no payouts settings', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),

			'tip_bank_name' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Bank name', 'workreap'),
				'desc' => esc_html__('Add tooltip for Bank name payouts settings', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_bank_routing_number' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Bank routing number', 'workreap'),
				'desc' => esc_html__('Add tooltip for Bank routing number payouts settings', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_bank_iban' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Bank IBAN', 'workreap'),
				'desc' => esc_html__('Add tooltip for Bank IBAN payouts settings', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_bank_bic_swift' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Bank BIC Swift', 'workreap'),
				'desc' => esc_html__('Add tooltip for Bank BIC Swift payouts settings', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_total_budget' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Total Budget ', 'workreap'),
				'desc' => esc_html__('Add tooltip for Total Budget balance admin share on milestone listing page.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_in_escrow' => array(
				'type' => 'addable-box',
				'label' => esc_html__('In escrow admin share', 'workreap'),
				'desc' => esc_html__('Add tooltip for In escrow balance admin share in on milestone listing page.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_milestone_paid' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Paid milestone admin share', 'workreap'),
				'desc' => esc_html__('Add tooltip for paid milestone balance admin share on milestone listing page.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_remainings' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Remainings admin share', 'workreap'),
				'desc' => esc_html__('Add tooltip for remainings milestone balance admin share on milestone listing page.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'job-tip' => array(
				'type' => 'html',
				'html' => 'Project Tooltip',
				'label' => esc_html__('', 'workreap'),
				'desc' => esc_html__('Please add projects add/edit tooltip, leave them empty to hide. Content is compulsory to show tooltip. Titles are optional.', 'workreap'),
			),
			'tip_project_level' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Project level', 'workreap'),
				'desc' => esc_html__('Add tooltip for project level', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_project_duration' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Project duration', 'workreap'),
				'desc' => esc_html__('Add tooltip for project duration', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			
			'tip_freelancer_level' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Freelancer required level', 'workreap'),
				'desc' => esc_html__('Add tooltip for freelancer level', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_english_level' => array(
				'type' => 'addable-box',
				'label' => esc_html__('English level', 'workreap'),
				'desc' => esc_html__('Add tooltip for english level', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_job_option' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Job Location type', 'workreap'),
				'desc' => esc_html__('Add tooltip for job location type', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_experiences' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Job required experience', 'workreap'),
				'desc' => esc_html__('Add tooltip for job required experience', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_experiences' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Job required experience', 'workreap'),
				'desc' => esc_html__('Add tooltip for job required experience', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_expiry_date' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Job Expiry', 'workreap'),
				'desc' => esc_html__('Add tooltip for expiry date in job posting.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_deadline' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Project deadline', 'workreap'),
				'desc' => esc_html__('Add tooltip for deadline date in job posting.', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_specializations' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Specializations', 'workreap'),
				'desc' => esc_html__('Add tooltip for specializations', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_industrial_experience' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Industrial experience', 'workreap'),
				'desc' => esc_html__('Add tooltip for industrial experience', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
	
			'front-tip' => array(
				'type' => 'html',
				'html' => 'Front-end Tooltips',
				'label' => esc_html__('', 'workreap'),
				'desc' => esc_html__('Please add tooltip, leave them empty to hide. Content is compulsory to show tooltip. Titles are optional.', 'workreap'),
			),
			'tip_front_specializations' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Specializations', 'workreap'),
				'desc' => esc_html__('Add tooltip for specializations for freelancer details page', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_front_industrial' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Industrial experience', 'workreap'),
				'desc' => esc_html__('Add tooltip for industrial experience for freelancer details page', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			'tip_front_skills' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Skills', 'workreap'),
				'desc' => esc_html__('Add tooltip for skills for freelancer details page', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}', // box title
				'limit' => 1, // limit the number of boxes that can be added
			),
			
			'menu-tip' => array(
				'type' => 'html',
				'html' => 'Dashboard menu',
				'label' => esc_html__('', 'workreap'),
				'desc' => esc_html__('Please add dashboard menu tooltip.', 'workreap'),
			),
			'tip_insights' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Dashboard', 'workreap'),
				'desc' => esc_html__('Add tooltip for dashboard', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_chat' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Inbox', 'workreap'),
				'desc' => esc_html__('Add tooltip for chat', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_profile-settings' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Profile settings', 'workreap'),
				'desc' => esc_html__('Add tooltip for profile settings', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_manage-portfolios' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Manage Portfolios', 'workreap'),
				'desc' => esc_html__('Add tooltip for manage portfolios', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_account-settings' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Account Settings', 'workreap'),
				'desc' => esc_html__('Add tooltip for account settings', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
	
			'tip_payouts-settings' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Payouts settings', 'workreap'),
				'desc' => esc_html__('Add tooltip for payouts settings', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_manage-projects' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Manage projects', 'workreap'),
				'desc' => esc_html__('Add tooltip for manage projects for freelancers', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_manage-jobs' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Manage jobs', 'workreap'),
				'desc' => esc_html__('Add tooltip for manage jobs employers', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_manage-services' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Manage services', 'workreap'),
				'desc' => esc_html__('Add tooltip for manage services for freelancers', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_manage-service' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Manage services', 'workreap'),
				'desc' => esc_html__('Add tooltip for manage services for employers', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_saved' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Saved items', 'workreap'),
				'desc' => esc_html__('Add tooltip for saved items', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_invoices' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Invoices', 'workreap'),
				'desc' => esc_html__('Add tooltip for invoices', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_disputes' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Disputes', 'workreap'),
				'desc' => esc_html__('Add tooltip for disputes', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_switch-account' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Switch account', 'workreap'),
				'desc' => esc_html__('Add tooltip for switch account', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_help' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Help', 'workreap'),
				'desc' => esc_html__('Add tooltip for help', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_packages' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Packages', 'workreap'),
				'desc' => esc_html__('Add tooltip for packages', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
			'tip_logout' => array(
				'type' => 'addable-box',
				'label' => esc_html__('Logout', 'workreap'),
				'desc' => esc_html__('Add tooltip for logout', 'workreap'),
				'box-options' => array(
					'title' => array('type' => 'text'),
					'content' => array('type' => 'textarea'),
				),
				'template' => '{{- content }}',
				'limit' => 1,
			),
        )
    )
);
