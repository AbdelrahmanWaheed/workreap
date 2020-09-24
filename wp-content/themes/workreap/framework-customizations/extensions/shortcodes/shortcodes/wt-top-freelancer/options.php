<?php

if (!defined('FW'))
    die('Forbidden');

$options = array(
	'title' => array(
		'label' => esc_html__('Title', 'workreap'),
        'desc' 	=> esc_html__('Add title. leave it empty to hide.', 'workreap'),
        'type' 	=> 'text',
	),
    'sub_title' => array(
        'label' => esc_html__('Sub Title', 'workreap'),
        'desc' 	=> esc_html__('Add subtitle. leave it empty to hide.', 'workreap'),
        'type' 	=> 'text',
    ),
    'description' => array(
        'type' 			=> 'wp-editor',
        'label' 		=> esc_html__('Description', 'workreap'),
        'desc' 			=> esc_html__('Add description. leave it empty to hide.', 'workreap'),
        'editor_height' => 200
    ),
	'freelancers' => array(
        'type' 			=> 'multi-select',
        'label' 		=> esc_html__('Select freelancers', 'workreap'),
        'population' 	=> 'posts',
        'source' 		=> 'freelancers',
        'desc' 			=> esc_html__('Select top freelancer\'s, leave it empty to show freelancers by below settings', 'workreap'),
    ),
	'listing_type' => array(
        'type' 			=> 'select',
        'label' 		=> esc_html__('Show freelancer by', 'workreap'),
        'desc' 			=> esc_html__('Select type to list freelancers by featured,verified,latest,', 'workreap'),
		'choices'		=> array(
							'' 			=> esc_html__('Select freelancer listing type', 'workreap'),
							'featured' 	=> esc_html__('Featured', 'workreap'),
							'DESC' 		=> esc_html__('Recents', 'workreap'),
							'ASC' 		=> esc_html__('Former', 'workreap'),
							),
    ),
	'listing_numbers' => array(
		'label' => esc_html__('Number of freelancers', 'workreap'),
        'desc' 	=> esc_html__('Add no of freelancer that show on listing.If empty then 4 freelancers are listing.', 'workreap'),
        'type' 	=> 'text',
	),
);
