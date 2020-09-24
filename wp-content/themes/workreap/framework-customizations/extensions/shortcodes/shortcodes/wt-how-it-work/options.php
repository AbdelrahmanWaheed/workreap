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
        'desc' 			=> esc_html__('Add description. leave it empty to hide.', 'workreap'),
        'editor_height' => 200
    ),
	'work_process' => array(
        'type' 			=> 'addable-popup',
        'label' 		=> esc_html__('Add work process', 'workreap'),
        'desc' 			=> esc_html__('', 'workreap'),
        'template' 		=> '{{- title }}',
        'popup-title' 	=> null,
        'size' 			=> 'small', // small, medium, large
        'limit' 		=> 0, // limit the number of popup`s that can be added
        'add-button-text' => esc_html__('Add work process.', 'workreap'),
        'sortable' 		=> true,
        'popup-options' => array(
           'title' => array(
                'label' => esc_html__('Title', 'workreap'),
                'type' 	=> 'text',
                'value' => '',
                'desc' => esc_html__('Add work process title.', 'workreap'),
            ),
            'sub_title' => array(
                'label' => esc_html__('Sub work title', 'workreap'),
                'type' => 'text',
                'value' => '',
                'desc' => esc_html__('Add work process sub title.', 'workreap'),
            ),
            'image' => array(
               'label' => esc_html__('Image', 'workreap'),
                'type' => 'upload',
                'desc' => esc_html__('Add work process image.', 'workreap'),
            ),
        ),
    ),
);
