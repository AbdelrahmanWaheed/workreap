<?php

if (!defined('FW'))
    die('Forbidden');



$options = array(
	'image' => array(
        'label' => esc_html__('Upload Background Image', 'workreap'),
        'desc' => esc_html__('Upload image. leave it empty to hide.', 'workreap'),
		'type' => 'upload',
    ),
	'title' => array(
		'label' => esc_html__('Title', 'workreap'),
        'desc' => esc_html__('Add newsletter title. leave it empty to hide.', 'workreap'),
        'type' => 'text',
	),
	'sub_title' => array(
		'label' => esc_html__('Sub title', 'workreap'),
        'desc' => esc_html__('Add newsletter sub title. leave it empty to hide.', 'workreap'),
        'type' => 'text',
	),
    'description' => array(
        'type' => 'wp-editor',
        'label' => esc_html__('Description', 'workreap'),
        'desc' => esc_html__('Add newsletter description. leave it empty to hide.', 'workreap'),
    ),
    'text_color' => array(
        'type' => 'color-picker',
        'value' => '',
        'label' => esc_html__( 'Text color', 'workreap' ),
        'desc' => esc_html__('Add text color. leave it empty to use default color', 'workreap'),
    ),
    'buttons' => array(
        'type' => 'addable-box',
        'label' => esc_html__('Add Button', 'workreap'),
        'desc' => esc_html__('Add buttons', 'workreap'),
        'box-options' => array(
            'button_text' => array('type' => 'text'),
            'button_link' => array('type' => 'text'),
            'btn_styles' => array(
                'type' => 'switch',
                'label' => esc_html__('Button Style', 'workreap'),
                'left-choice' => array(
                    'value' => 'de_active',
                    'label' => esc_html__('Style 1', 'workreap'),
                ),
                'right-choice' => array(
                    'value' => 'active',
                    'label' => esc_html__('Style 2', 'workreap'),
                ),
            ),
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
        'template' => '{{- button_text }}', // box title
        'box-controls' => array(// buttons next to (x) remove box button
            'control-id' => '<small class = "dashicons dashicons-smiley"></small>',
        ),
        'limit' => 2, // limit the number of boxes that can be added
        'add-button-text' => esc_html__('Add Buttons', 'workreap'),
        'sortable' => true,
    ),
);
