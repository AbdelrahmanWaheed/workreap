<?php

if (!defined('FW')) {
    die('Forbidden');
}

$list               = worktic_job_duration_list();
$english_level      = worktic_english_level_list();
$freelancer_level   = worktic_freelancer_level_list();
$project_level   	= workreap_get_project_level();
$job_type 		 	= workreap_get_job_type();
$job_option		 	= function_exists('workreap_get_job_option') ? workreap_get_job_option() : array();

if (function_exists('fw_get_db_settings_option')) {
    $job_option_setting         = fw_get_db_settings_option('job_option', $default_value = null);
    $multiselect_freelancertype  = fw_get_db_settings_option('multiselect_freelancertype', $default_value = null);
    $job_price_option           = fw_get_db_settings_option('job_price_option', $default_value = null);
    $milestone         			= fw_get_db_settings_option('job_milestone_option', $default_value = null);
}

$multiselect_freelancertype  = !empty($multiselect_freelancertype) && $multiselect_freelancertype === 'multiselect' ?  'multi-select': 'select';
$job_price_option 			= !empty($job_price_option) ? $job_price_option : '';
$job_option_setting 		= !empty($job_option_setting) ? $job_option_setting : '';
$milestone					= !empty($milestone['gadget']) ? $milestone['gadget'] : '';

//Project location
$job_option_list	= array();
if(!empty($job_option_setting) && $job_option_setting === 'enable' ){
        $job_option_list['job_option'] = array(
									'label' => esc_html__('Project location type', 'workreap'),
									'desc'  => esc_html__('Select project location type', 'workreap'),
									'type'  => 'select',
									'value' => '',
									'choices' => $job_option
								);
}

$milestone_Array    = array();
if( !empty($milestone) && $milestone ==='enable' ){
    $milestone_Array['milestone']	= array(
                                        'type'  => 'select',
                                        'value' => '',
                                        'label' => esc_html__('Milestone', 'workreap'),
                                        'desc'  => esc_html__('Select milestone project', 'workreap'),
                                        'choices' => array(
                                            'off' => esc_html__('OFF', 'workreap'),
                                            'on'  => esc_html__('ON', 'workreap'),
                                        )
                                    );
}
//price type
$max_price	= array();
if(!empty($job_price_option) && $job_price_option === 'enable' ){
    $max_price['max_price']	= array(
						'type'  => 'text',
						'label' => esc_html__('Maximum price', 'workreap'),
						'desc'  => esc_html__('Add job maximum price (integers only)', 'workreap'),
						'value' => '',
					);
}

//Freelancer level
$freelancertype	= array();
if( $multiselect_freelancertype === 'enable' ){
   $freelancertype['freelancer_level'] =  array(
        'type'  => 'multi-select',
        'population' => 'array',
        'label' => esc_html__('Freelancer level', 'workreap'),
        'desc'  => esc_html__('Choose freelancer level required for the project', 'workreap'),                
        'choices' => $freelancer_level
    );
}else{
	$freelancertype['freelancer_level'] =  array(
        'type'  		=> 'multi-select',
        'prepopulate' 	=> 500,
		'limit' 		=> 1,
        'label' 		=> esc_html__('Freelancer level', 'workreap'),
        'desc'  		=> esc_html__('Choose freelancer level required for the project', 'workreap'),                
        'choices' 		=> $freelancer_level
    );
}

$options = array(
	'featured_job' => array(
        'title' => esc_html__('Featured Job?', 'workreap'),
        'type' => 'box',
        'options' => array(
			'featured_post' => array(
				'value' => false,
				'label' => esc_html__('Featured job?', 'workreap'),
                'desc' => esc_html__('Select to make this job as featured', 'workreap'),
                'type' => 'checkbox',
                'value' => '',
            ),
			// 'featured_expiry' => array(
			// 	'label' => esc_html__('Featured Expiry', 'workreap'),
			// 	'type' => 'datetime-picker',
			// 	'datetime-picker' => array(
			// 		'format'  => 'Y-m-d',
			// 		'maxDate' => false, 
			// 		'minDate' => date('Y-m-d'),
			// 		'timepicker' => false,
			// 		'datepicker' => true,
			// 		'defaultTime' => ''
			// 	),
			// 	'desc' => esc_html__('Add date here, Futured date is required to add user into featured listing', 'workreap')
			// ),
        ),
    ),
    'highlighted_job' => array(
        'title' => esc_html__('Highlighted Job?', 'workreap'),
        'type' => 'box',
        'options' => array(
            'highlighted_post' => array(
                'value' => false,
                'label' => esc_html__('Highlighted job?', 'workreap'),
                'desc' => esc_html__('Select to make this job as highlighted', 'workreap'),
                'type' => 'checkbox',
                'value' => '',
            ),
        ),
    ),
    'project_settings' => array(
        'title' => esc_html__('Project Settings', 'workreap'),
        'type' => 'box',
        'options' => array(
			'_featured_job_string' => array(
                'type' => 'hidden',
                'value' => 0,
            ),
            'project_level' => array(
                'type'  => 'select',
                'value' => '',
                'label' => esc_html__('Project level', 'workreap'),
                'desc'  => esc_html__('Required project level.', 'workreap'),                
                'choices' => $project_level
            ),
			$job_option_list,
            'project_type' => array(
                'type' => 'multi-picker',
                'label' => false,
                'desc' => false,
                'picker' => array(
                    'gadget' => array(
                        'label' => esc_html__('Project Type', 'workreap'),
                        'desc' => esc_html__('Select project type', 'workreap'),
                        'type' => 'select',
                        'value' => 'default',
                        'choices' => $job_type
                    )
                ),
                'choices' => array(                                       
                    'hourly' => array(
                        'hourly_rate' => array(
                            'type' => 'text',
                            'value' => '',
                            'label' => esc_html__('Minimum Price', 'workreap'),
                            'desc' => esc_html__('Add job minimum hourly rate (integers only)', 'workreap'),
                            'value' => '',
                        ),
						$max_price,
						'estimated_hours' => array(
                            'type' => 'text',
                            'value' => '',
                            'label' => esc_html__('Estimated Hours', 'workreap'),
                            'desc' => esc_html__('Add job estimated hours (integers only)', 'workreap'),
                            'value' => '',
                        ),
                    ),
					'fixed' => array(
                        'project_cost' => array(
                            'type' => 'text',
                            'value' => '',
                            'label' => esc_html__('Minimum Price', 'workreap'),
                            'desc' => esc_html__('Add job cost (integers only)', 'workreap'),
                            'value' => '',
                        ),
                        $max_price,
                        $milestone_Array
                    ),
                )
            ), 
			$freelancertype,
            'project_duration' => array(
                'type'  => 'select',
                'value' => '',
                'label' => esc_html__('Project Duration', 'workreap'),
                'desc'  => esc_html__('Select duration of the project', 'workreap'),                
                'choices' => $list,
            ),  
            'english_level' => array(
                'type'  => 'select',
                'value' => '',
                'label' => esc_html__('English level', 'workreap'),
                'desc'  => esc_html__('Select English level required for the project', 'workreap'),                
                'choices' => $english_level,
            ),
			'expiry_date' => array(
				'label' => esc_html__('Expiry Date', 'workreap'),
				'type' => 'datetime-picker',
				'datetime-picker' => array(
					'format'  => 'Y/m/d', // Format datetime.
					'maxDate' => false, // By default there is not maximum date , set a date in the datetime format.
					'minDate' => false, // By default minimum date will be current day, set a date in the datetime format.
					'timepicker' => false, // Show timepicker.
					'datepicker' => true, // Show datepicker.
					'defaultTime' => '' // If the input value is empty, timepicker will set time use defaultTime.
				),
				'desc' => esc_html__('Add date here', 'workreap')
			),
			'deadline' => array(
				'label' => esc_html__('Project deadline date', 'workreap'),
				'type' => 'datetime-picker',
				'datetime-picker' => array(
					'format'  => 'Y/m/d', // Format datetime.
					'maxDate' => false, // By default there is not maximum date , set a date in the datetime format.
					'minDate' => false, // By default minimum date will be current day, set a date in the datetime format.
					'timepicker' => false, // Show timepicker.
					'datepicker' => true, // Show datepicker.
					'defaultTime' => '' // If the input value is empty, timepicker will set time use defaultTime.
				),
				'desc' => esc_html__('Add date here', 'workreap')
			),
			'show_attachments' => array(
                'type'  => 'select',
                'value' => '',
                'label' => esc_html__('Show attachments', 'workreap'),
                'desc'  => esc_html__('Choose to show attachments on project detail page.', 'workreap'),                
                'choices' => array(
					'off' => esc_html__('OFF', 'workreap'),
					'on' => esc_html__('ON', 'workreap'),
				),
            ), 
            'project_documents' => array(
                'type'  => 'multi-upload',
                'value' => array(),
                'label' => esc_html__('Upload Documents', 'workreap'),
                'desc'  => esc_html__('Upload project documents', 'workreap'),         
                'images_only' => false,            
                'files_ext' => array( 'doc', 'docx', 'pdf','xls','xlsx','ppt','pptx','csv' ),  
            ),
        )
    ),
	'settings' => array(
        'title' => esc_html__('General Settings', 'workreap'),
        'type' => 'box',
        'options' => array(
            'address' => array(
				'label' => esc_html__('Address', 'workreap'),
                'desc' => esc_html__('Please add address', 'workreap'),
                'type' => 'text',
                'value' => '',
            ),
            'longitude' => array(
				'label' => esc_html__('Longitude', 'workreap'),
                'desc' => esc_html__('Please add Longitude', 'workreap'),
                'type' => 'text',
                'value' => '',
            ),
            'latitude' => array(
				'label' => esc_html__('Latitude', 'workreap'),
                'desc' => esc_html__('Please add Latitude', 'workreap'),
                'type' => 'text',
                'value' => '',
            ),
            'country' => array(
				'type' => 'multi-select',
				'label' => esc_html__('Select country', 'workreap'),
				'population' => 'taxonomy',
				'source' => 'locations',
				'prepopulate' => 500,
				'limit' => 1,
				'desc' => esc_html__('Select country to display.', 'workreap'),
			),
        )
    ),
);