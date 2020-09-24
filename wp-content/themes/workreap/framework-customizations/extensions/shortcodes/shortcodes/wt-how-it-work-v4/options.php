<?php

if (!defined('FW'))
    die('Forbidden');



$options = array(
	'title' => array(
		'label' => esc_html__('Title', 'workreap'),
        'desc' => esc_html__('Add title. leave it empty to hide.', 'workreap'),
        'type' => 'text',
	),
    'sub_title' => array(
        'label' => esc_html__('Sub Title', 'workreap'),
        'desc' => esc_html__('Add subtitle. leave it empty to hide.', 'workreap'),
        'type' => 'text',
    ),
    'description' => array(
        'type' 			=> 'wp-editor',
        'label' 		=> esc_html__('Description', 'workreap'),
        'desc' 			=> esc_html__('Add description. leave it empty to hide.', 'workreap')
    ),
	'image' => array(
        'type' 	=> 'upload',
        'label' => esc_html__('Add image', 'workreap'),
        'desc' 	=> esc_html__('Add image.Leave it empty to hide.', 'workreap'),
    ),
	'video_link' => array(
		'label' => esc_html__('Add Video url', 'workreap'),
        'desc' => esc_html__('Add youtube video url.', 'workreap'),
        'type' => 'text',
	),
	
);
