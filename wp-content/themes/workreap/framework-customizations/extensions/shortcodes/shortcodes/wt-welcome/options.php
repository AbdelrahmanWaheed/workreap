<?php

if (!defined('FW'))
    die('Forbidden');



$options = array(
	'title' => array(
		'label' => esc_html__('Title', 'workreap'),
        'desc' => esc_html__('Add greetings title. leave it empty to hide.', 'workreap'),
        'type' => 'text',
	),
    'sub_title' => array(
        'label' => esc_html__('Sub Title', 'workreap'),
        'desc' => esc_html__('Add greetings subtitle. leave it empty to hide.', 'workreap'),
        'type' => 'text',
    ),
    'description' => array(
        'type' => 'wp-editor',
        'label' => esc_html__('Description', 'workreap'),
        'desc' => esc_html__('Add greetings description. leave it empty to hide.', 'workreap'),
        'editor_height' => 200
    ),
    'video_thumbnail' => array(
        'label' => esc_html__('Upload Image', 'workreap'),
        'desc' => esc_html__('Upload video thumbnail. leave it empty to hide.', 'workreap'),
        'type' => 'upload',
    ),
    'video_link' => array(
        'label' => esc_html__('Video Link', 'workreap'),
        'desc' => esc_html__('Add welcome greeting video link. leave it empty to hide.', 'workreap'),
        'type' => 'text',
    ),
	'counters' => array(
        'type' => 'addable-popup',
        'label' => esc_html__('Add Counter', 'workreap'),
        'desc' => esc_html__('', 'workreap'),
        'template' => '{{- counter_title }}',
        'popup-title' => null,
        'size' => 'small', // small, medium, large
        'limit' => 0, // limit the number of popup`s that can be added
        'add-button-text' => esc_html__('Counter', 'workreap'),
        'sortable' => true,
        'popup-options' => array(
            'counter_title' => array(
                'label' => esc_html__('Counter Title', 'workreap'),
                'type' => 'text',
                'value' => '',
                'desc' => esc_html__('Add counter title', 'workreap'),
            ),
            'counter_start' => array(
                'label' => esc_html__('Start Number', 'workreap'),
                'type' => 'text',
                'value' => '',
                'desc' => esc_html__('Add counter start', 'workreap'),
            ),
            'counter_end' => array(
                'label' => esc_html__('End Number', 'workreap'),
                'type' => 'text',
                'value' => '',
                'desc' => esc_html__('Add counter end', 'workreap'),
            ),
			'counter_symbol' => array(
                'label' => esc_html__('Counter symbol', 'workreap'),
                'type' => 'text',
                'value' => ''
            ),
            'counter_interval' => array(
                'type' => 'slider',
                'value' => 0,
                'properties' => array(
                    'min' => 1,
                    'max' => 50,
                    'sep' => 1,
                ),
                'attr' => array(),
                'label' => esc_html__('Interval', 'workreap'),
                'desc' => esc_html__('add interval', 'workreap'),
            ),
            'counter_speed' => array(
                'type' => 'slider',
                'value' => 0,
                'properties' => array(
                    'min' => 1000,
                    'max' => 10000,
                    'sep' => 1,
                ),
                'attr' => array(),
                'label' => esc_html__('Speed', 'workreap'),
                'desc' => esc_html__('add speed', 'workreap'),
            ),
        ),
    ),
);
