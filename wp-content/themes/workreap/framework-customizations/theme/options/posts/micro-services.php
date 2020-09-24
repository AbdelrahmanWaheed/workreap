<?php

if (!defined('FW')) {
    die('Forbidden');
}

$english_level  	= worktic_english_level_list();

$options = array(    
	'featured_service' => array(
        'title' => esc_html__('Feature Service', 'workreap'),
        'type' => 'box',
        'options' => array(
			'featured_post' => array(
				'value' => false,
				'label' => esc_html__('Featured service?', 'workreap'),
                'desc' => esc_html__('Select to make this service as featured', 'workreap'),
                'type' => 'checkbox',
                'value' => '',
            ),
			'featured_expiry' => array(
				'label' => esc_html__('Featured Expiry', 'workreap'),
				'type' => 'datetime-picker',
				'datetime-picker' => array(
					'format'  => 'Y-m-d',
					'maxDate' => false, 
					'minDate' => date('Y-m-d'),
					'timepicker' => false,
					'datepicker' => true,
					'defaultTime' => ''
				),
				'desc' => esc_html__('Add date here', 'workreap')
			),
        ),
    ),
    'micro_services_settings' => array(
        'title' => esc_html__('Service Settings', 'workreap'),
        'type' => 'box',
        'options' => array(
			'_featured_service_string' => array(
                'type' => 'hidden',
                'value' => 0,
            ),
			'price' => array(
				'label' => esc_html__('Micro Service Amount', 'workreap'),
                'desc' 	=> esc_html__('Micro Service amount', 'workreap'),
                'type' 	=> 'text',
                'value' => '',
            ),  
			'english_level' => array(
                'type'  => 'select',
                'value' => '',
                'label' => esc_html__('English level', 'workreap'),
                'desc'  => esc_html__('Select English level required for the service', 'workreap'),                
                'choices' => $english_level,
            ),
            'docs' => array(
                'type'  		=> 'multi-upload',
                'value' 		=> array(),
                'label' 		=> esc_html__('Upload gallery', 'workreap'),
                'desc'  		=> esc_html__('Upload micro service gallery images', 'workreap'),         
                'images_only' 	=> false,            
                'files_ext' 	=> array( 'jpg','jpeg','gif','png' ),  
            ),
			'address' => array(
				'label' => esc_html__('Address', 'workreap'),
                'desc' 	=> esc_html__('Please add address', 'workreap'),
                'type' 	=> 'text',
                'value' => '',
            ),
            'longitude' => array(
				'label' => esc_html__('Longitude', 'workreap'),
                'desc' 	=> esc_html__('Please add Longitude', 'workreap'),
                'type' 	=> 'text',
                'value' => '',
            ),
            'latitude' => array(
				'label' => esc_html__('Latitude', 'workreap'),
                'desc' 	=> esc_html__('Please add Latitude', 'workreap'),
                'type' 	=> 'text',
                'value' => '',
            ),
            'country' => array(
				'type' 			=> 'multi-select',
				'label' 		=> esc_html__('Select location', 'workreap'),
				'population' 	=> 'taxonomy',
				'source' 		=> 'locations',
				'prepopulate' 	=> 500,
				'limit' 		=> 1,
				'desc' 			=> esc_html__('Select location to display.', 'workreap'),
			),
			'videos' => array(
				'type' => 'addable-option',
				'value' => array(),
				'label' => esc_html__('Video URL', 'workreap'),
				'desc' => esc_html__('Add video URL here', 'workreap'),
				'option' => array('type' => 'text'),
				'add-button-text' => esc_html__('Add', 'workreap'),
				'sortable' => true,
			),
			'downloadable' => array(
                'type'  => 'select',
                'value' => '',
                'label' => esc_html__('Downloadable', 'workreap'),
                'desc'  => esc_html__('Select Yes or no for downloable service', 'workreap'),                
                'choices' => array(
								''		=> esc_html__('Select Downloadable', 'workreap'),
								'yes'	=> esc_html__('Yes','workreap'),
								'no'	=> esc_html__('No','workreap'),
								),
            ),
        )
    ),
);

