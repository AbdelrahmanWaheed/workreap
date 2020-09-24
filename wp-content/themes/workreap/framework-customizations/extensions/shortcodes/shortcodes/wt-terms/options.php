<?php

if (!defined('FW'))
    die('Forbidden');



$options = array(
	'title' => array(
		'label' => esc_html__('Title', 'workreap'),
        'desc' => esc_html__('Add title. leave it empty to hide.', 'workreap'),
        'type' => 'text',
	),
    
    'description' => array(
        'type' => 'wp-editor',
        'label' => esc_html__('Description', 'workreap'),
        'desc' => esc_html__('Add description. leave it empty to hide.', 'workreap')
    )
);
