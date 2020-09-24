<?php

if (!defined('FW'))
    die('Forbidden');

$options = array(
    'section_heading' => array(
        'type' => 'text',
        'label' => esc_html__('Heading', 'workreap'),
        'desc' => esc_html__('Add section heading. Leave it empty to hide.', 'workreap'),
    ),
    'categories' => array(
        'type' => 'multi-select',
        'label' => esc_html__('Select Categories', 'workreap'),
        'population' => 'taxonomy',
        'source' => 'project_cat',
        'prepopulate' => 500,
        'desc' => esc_html__('Select categories to display.', 'workreap'),
    ),
	'post_type' => array(
		'type' => 'select',
		'value' => 'jobs',
		'label' => esc_html__('Post Type', 'workreap'),
		'choices' => array(
			'jobs' => esc_html__('Jobs', 'workreap'),
			'services' => esc_html__('Services', 'workreap'),
		),
		'no-validate' => false,
	),
);
