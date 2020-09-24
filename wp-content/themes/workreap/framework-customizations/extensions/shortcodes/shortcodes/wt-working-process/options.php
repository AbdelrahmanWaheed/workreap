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
        'type' => 'wp-editor',
        'label' => esc_html__('Description', 'workreap'),
        'desc' => esc_html__('Add description. leave it empty to hide.', 'workreap'),
        'editor_height' => 200
    ),
    'text_align' => array(
        'type' => 'select',
        'value' => 'left',
        'label' => esc_html__('Text Alignment', 'workreap'),
        'choices' => array(
            'left' => esc_html__('Left', 'workreap'),
            'right' => esc_html__('Right', 'workreap'),
        ),
        'no-validate' => false,
    ),
    'image' => array(
        'label' => esc_html__('Upload Image', 'workreap'),
        'desc' => esc_html__('Upload image. leave it empty to hide.', 'workreap'),
        'type' => 'upload',
    ),
    'image_align' => array(
        'type' => 'select',
        'value' => 'right',
        'label' => esc_html__('Image Alignment', 'workreap'),
        'choices' => array(
            'left' => esc_html__('Left', 'workreap'),
            'right' => esc_html__('Right', 'workreap'),
        ),
        'no-validate' => false,
    ),
	'open' => array(
		'type' => 'select',
		'value' => 'no',
		'label' => esc_html__('Open First?', 'workreap'),
		'choices' => array(
			'yes' => esc_html__('Yes', 'workreap'),
			'no' => esc_html__('No', 'workreap'),
		),
		'no-validate' => false,
	),
	'faqs' => array(
        'type' => 'addable-popup',
        'label' => esc_html__('Add FAQs', 'workreap'),
        'desc' => esc_html__('', 'workreap'),
        'template' => '{{- title }}',
        'popup-title' => null,
        'size' => 'small', // small, medium, large
        'limit' => 0, // limit the number of popup`s that can be added
        'add-button-text' => esc_html__('Add FAQ', 'workreap'),
        'sortable' => true,
        'popup-options' => array(
           'title' => array(
                'label' => esc_html__('FAQ Title', 'workreap'),
                'type' => 'text',
                'value' => '',
                'desc' => esc_html__('Add FAQ title', 'workreap'),
            ),
            'question' => array(
                'label' => esc_html__('Question', 'workreap'),
                'type' => 'text',
                'value' => '',
                'desc' => esc_html__('Add question', 'workreap'),
            ),
            'answer' => array(
                'label' => esc_html__('Answer', 'workreap'),
                'type' => 'wp-editor',
                'value' => '',
                'desc' => esc_html__('Add answer to the question', 'workreap'),
                'editor-height' => '100'
            ),
        ),
    ),
);
