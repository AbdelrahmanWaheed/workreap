<?php

if (!defined('FW'))
    die('Forbidden');

$options = array(
	'title' => array(
		'label' => esc_html__('Title', 'workreap'),
        'desc' 	=> esc_html__('Add title. leave it empty to hide.', 'workreap'),
        'type' 	=> 'text',
	),
    'sub_title' => array(
        'label' => esc_html__('Sub Title', 'workreap'),
        'desc' 	=> esc_html__('Add subtitle. leave it empty to hide.', 'workreap'),
        'type' 	=> 'text',
    ),
    'description' => array(
        'type' 			=> 'wp-editor',
        'label' 		=> esc_html__('Description', 'workreap'),
        'desc' 			=> esc_html__('Add description. leave it empty to hide.', 'workreap'),
        'editor_height' => 200
    ),
	'posts' => array(
        'type' 			=> 'multi-select',
        'label' 		=> esc_html__('Select latest posts', 'workreap'),
        'population' 	=> 'posts',
        'source' 		=> 'post',
        'prepopulate' 	=> 10,
        'desc' 			=> esc_html__('Select latest posts to display.', 'workreap'),
    ),
);
