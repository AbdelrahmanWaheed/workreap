<?php

if (!defined('FW'))
    die('Forbidden');

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
        'type' 	=> 'text',
        'label' => esc_html__('Heading', 'workreap'),
        'desc' 	=> esc_html__('Add section heading. Leave it empty to hide.', 'workreap'),
    ),
    'sub_title' => array(
        'type' 	=> 'text',
        'label' => esc_html__('Sub Heading', 'workreap'),
        'desc' 	=> esc_html__('Add section sub heading. Leave it empty to hide.', 'workreap'),
    ),
    'description' => array(
        'type' 	=> 'wp-editor',
        'label' => esc_html__('Button Title', 'workreap'),
        'desc' 	=> esc_html__('Add button title. Leave it empty to hide button.', 'workreap'),
    ),
    'google_image' => array(
        'type' 	=> 'upload',
        'label' => esc_html__('Google Play store image', 'workreap'),
        'desc' 	=> esc_html__('Add Google Play store image.', 'workreap'),
    ),
	'play_store_url' => array(
        'type' 	=> 'text',
        'label' => esc_html__('Google Play store link', 'workreap'),
        'desc' 	=> esc_html__('Add Google Play store link.', 'workreap'),
    ),
	'app_store_image' => array(
        'type' 	=> 'upload',
        'label' => esc_html__('App store image', 'workreap'),
        'desc' 	=> esc_html__('Add App store image.', 'workreap'),
    ),
	'app_store_url' => array(
        'type' 	=> 'text',
        'label' => esc_html__('App store link', 'workreap'),
        'desc' 	=> esc_html__('Add App store link.', 'workreap'),
    ),
	'image' => array(
        'type' 	=> 'upload',
        'label' => esc_html__('Add image', 'workreap'),
        'desc' 	=> esc_html__('Add image.', 'workreap'),
    ),
);
