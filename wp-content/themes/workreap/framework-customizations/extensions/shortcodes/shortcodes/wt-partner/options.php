<?php

if (!defined('FW'))
    die('Forbidden');


$options = array(
	'slider' => array(
        'label' 	=> esc_html__('Add slide images', 'workreap'),
        'type' 		=> 'addable-popup',
        'template' 	=> '{{- title }}',
        'value' 	=> array(),
        'desc' 		=> esc_html__('Add slide images', 'workreap'),
        'popup-options' => array(
			'image' => array(
				'label' => esc_html__('Upload slide image', 'workreap'),
				'desc' 	=> esc_html__('Upload image.', 'workreap'),
				'type' 	=> 'upload',
			),
			'title' => array(
				'label' => esc_html__('Add title', 'workreap'),
				'desc' 	=> esc_html__('Title of partner', 'workreap'),
				'type' 	=> 'text',
			),
			'url' => array(
				'label' => esc_html__('Add partner url', 'workreap'),
				'desc' 	=> esc_html__('Add partner link.', 'workreap'),
				'type' 	=> 'text',
			),
		)
	),
);
