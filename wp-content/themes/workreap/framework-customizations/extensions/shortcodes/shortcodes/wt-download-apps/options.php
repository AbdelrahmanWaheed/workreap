<?php

if (!defined('FW'))
    die('Forbidden');



$options = array(
	'image' => array(
        'label' => esc_html__('Upload Image', 'workreap'),
        'desc' => esc_html__('Upload Image. leave it empty to hide.', 'workreap'),
		'type' => 'upload',
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
        'type' => 'wp-editor',
        'label' => esc_html__('Description', 'workreap'),
        'desc' => esc_html__('Add description. leave it empty to hide.', 'workreap'),
        'editor_height' => 200
    ),
	'logos' => array(
		'type' => 'addable-box',
		'label' => esc_html__('Add multiple logo', 'workreap'),
		'desc' => esc_html__('Add category logo', 'workreap'),
		'box-options' => array(
			'image' => array('type' => 'upload'),
			'link_url' => array('type' => 'text'),
			'link_target' => array(
				'type' => 'select',
				'value' => '_self',
				'label' => esc_html__('Link Target', 'workreap'),
				'choices' => array(
					'_blank' => esc_html__('New Tab', 'workreap'),
					'_self' => esc_html__('Current Tab', 'workreap'),
				),
				'no-validate' => false,
			),
		),
		'template' => '{{- link_url }}', // box title
		'box-controls' => array(// buttons next to (x) remove box button
			'control-id' => '<small class = "dashicons dashicons-smiley"></small>',
		),
		'limit' => 2, // limit the number of boxes that can be added
		'add-button-text' => esc_html__('Add Logo', 'workreap'),
		'sortable' => true,
	),
);
