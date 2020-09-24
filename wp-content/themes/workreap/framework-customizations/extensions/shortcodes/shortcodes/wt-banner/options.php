<?php
if (!defined('FW'))
    die('Forbidden');
$list_names	= worktic_get_search_list('yes');
$options = array(
	'text_color' => array(
		'type' => 'color-picker',
		'value' => '',
		'attr' => array(),
		'label' => esc_html__('Text color', 'workreap'),
		'desc' => esc_html__('Add text color. leave it empty to use default color', 'workreap'),
		'help' => esc_html__('', 'workreap'),
		
	),
	'title' => array(
		'label' => esc_html__('Title', 'workreap'),
        'desc' => esc_html__('Add title. leave it empty to hide.', 'workreap'),
        'type' => 'text',
	),
	'sub_title' => array(
		'label' => esc_html__('Title', 'workreap'),
        'desc' => esc_html__('Add title. leave it empty to hide.', 'workreap'),
        'type' => 'text',
	),
    'description' => array(
        'type' => 'wp-editor',
        'label' => esc_html__('Description', 'workreap'),
        'desc' => esc_html__('Add description. leave it empty to hide.', 'workreap'),
    ),
    'video_title' => array(
		'label' => esc_html__('Video title', 'workreap'),
        'desc' => esc_html__('Add video title. leave it empty to hide.', 'workreap'),
        'type' => 'textarea',
	),
	'video_url' => array(
		'label' => esc_html__('Video Url', 'workreap'),
        'desc' => esc_html__('Add video url. leave it empty to hide.', 'workreap'),
        'type' => 'text',
	),
	'images' => array(
		'label' => esc_html__('Upload banner images', 'workreap'),
		'desc' => esc_html__('Upload images. leave it empty to hide.', 'workreap'),
		'type' => 'multi-upload',
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
	)
);
