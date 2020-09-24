<?php

if (!defined('FW'))
    die('Forbidden');



$options = array(
    'section_heading' => array(
        'type' => 'text',
        'label' => esc_html__('Heading', 'workreap'),
        'desc' => esc_html__('Add section heading. Leave it empty to hide.', 'workreap'),
    ),
	'preloader' => array(
		'type' => 'multi-picker',
		'label' => false,
		'desc' => false,
		'picker' => array(
			'gadget' => array(
				'label' => esc_html__('Enable View All', 'workreap'),
				'type' => 'switch',
				'value' => 'enable',
				'left-choice' => array(
					'value' => 'enable',
					'label' => esc_html__('Enable', 'workreap'),
				),
				'right-choice' => array(
					'value' => 'disable',
					'label' => esc_html__('Disable', 'workreap'),
				),
			)
		),
		'choices' => array(
			'enable' => array(
				'btn_title' => array(
					'type' => 'text',
					'label' => esc_html__('Button Title', 'workreap'),
					'desc' => esc_html__('Add button title. Leave it empty to hide button.', 'workreap'),
				),
				'btn_link' => array(
					'type' => 'text',
					'label' => esc_html__('Button Link', 'workreap'),
					'desc' => esc_html__('Add button link. Leave it empty to hide.', 'workreap'),
				),
			),
		)
	),
    'skills' => array(
        'type' 			=> 'multi-select',
        'label' 		=> esc_html__('Select skills', 'workreap'),
        'population' 	=> 'taxonomy',
        'source' 		=> 'skills',
        'prepopulate' 	=> 500,
        'desc'			=> esc_html__('Select skills to display.', 'workreap'),
    ),
	'link_target' => array(
		'type' => 'select',
		'value' => 'jobs',
		'label' => esc_html__('Link Target', 'workreap'),
		'desc'			=> esc_html__('Do you want to search freelancers or jobs?', 'workreap'),
		'choices' => array(
			'jobs' => esc_html__('Jobs', 'workreap'),
			'freelancer' => esc_html__('Freelancer', 'workreap'),
		),
	),
	
);
