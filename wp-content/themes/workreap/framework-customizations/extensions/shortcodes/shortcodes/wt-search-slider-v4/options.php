<?php
if (!defined('FW'))
    die('Forbidden');
$list_names	= worktic_get_search_list('yes');
$options = array(
	'title' => array(
		'label' => esc_html__('Title', 'workreap'),
        'desc' 	=> esc_html__('Add title. leave it empty to hide.', 'workreap'),
        'type' 	=> 'text',
	),
	'sub_title' => array(
		'label' => esc_html__('Sub title', 'workreap'),
        'desc' 	=> esc_html__('Add sub title. leave it empty to hide.', 'workreap'),
        'type' 	=> 'text',
	),
    'description' => array(
        'type' 	=> 'wp-editor',
        'label' => esc_html__('Description', 'workreap'),
        'desc' 	=> esc_html__('Add description. leave it empty to hide.', 'workreap'),
    ),
	'video_title' => array(
		'label' => esc_html__('Video title', 'workreap'),
        'desc' 	=> esc_html__('Add video title. leave it empty to hide.', 'workreap'),
        'type' 	=> 'textarea',
	),
	'video_url' => array(
		'label' => esc_html__('Video Url', 'workreap'),
        'desc' 	=> esc_html__('Add video url. leave it empty to hide.', 'workreap'),
        'type' 	=> 'text',
	),
	'search' => array(
		'type'  => 'checkboxes',
		'value' => array(
			'job' 			=> true,
			'freelancer' 	=> true,
		),
		'label' => esc_html__('Search options', 'workreap'),
		'desc'  => esc_html__('Atleast one item should be selected.', 'workreap'),
		'choices' => $list_names,
		'inline' => false,
	),
	'slider' => array(
        'label' 	=> esc_html__('Add slide images', 'workreap'),
        'type' 		=> 'addable-popup',
        'template' 	=> '',
        'value' 	=> array(),
        'desc' 		=> esc_html__('Add slide images', 'workreap'),
        'popup-options' => array(
			'image' => array(
				'label' => esc_html__('Upload slide image', 'workreap'),
				'desc' 	=> esc_html__('Upload image.', 'workreap'),
				'type' 	=> 'upload',
			),
		)
	),
	'animated_image' => array(
        'label' 	=> esc_html__('Add animated images', 'workreap'),
        'type' 		=> 'addable-popup',
        'template' 	=> '',
        'value' 	=> array(),
        'desc' 		=> esc_html__('Add animated image', 'workreap'),
        'popup-options' => array(
			'image' => array(
				'label' => esc_html__('Upload Image', 'workreap'),
				'desc' 	=> esc_html__('Upload image.', 'workreap'),
				'type' 	=> 'upload',
			),
		)
	)
);
