<?php

if (!defined('FW')) {
    die('Forbidden');
}

$options = array(
    'title' => array(
		'label' => esc_html__('Title', 'workreap'),
		'desc' => esc_html__('Add title or leave it empty to hide', 'workreap'),
		'type' => 'text',
		'value' => ''
	),
	'sub_title' => array(
		'label' => esc_html__('Title', 'workreap'),
		'desc' => esc_html__('Add sub title or leave it empty to hide', 'workreap'),
		'type' => 'text',
		'value' => ''
	),
	'description' => array(
		'label' => esc_html__('Description', 'workreap'),
		'desc' => esc_html__('Add description or leave it empty to hide', 'workreap'),
		'type' => 'textarea',
		'value' => ''
	),
	'services' => array(
		'label' 		=> esc_html__('Choose Category', 'workreap'),
		'type' 			=> 'multi-select',
		'population' 	=> 'taxonomy',
		'source' 		=> 'project_cat',
		'desc' 			=> esc_html__('', 'workreap'),
		'prepopulate' 	=> 100,
	),
	'btn_title' => array (
		'label' => esc_html__('Button title', 'workreap'),
		'desc' => esc_html__('Add button title or leave it empty to hide', 'workreap'),
		'type' => 'text',
		'value' => ''
	),
	'template_page' => array(
		'label' 		=> esc_html__('Choose Page', 'workreap'),
		'type' 			=> 'multi-select',
		'population' 	=> 'posts',
		'source' 		=> 'page',
		'desc' 			=> esc_html__('Choose template page.The page link will be the button URL', 'workreap'),
		'limit' 		=> 1,
		'prepopulate' 	=> 100,
	),
	'layout' => array(
		'type' 	=> 'select',
		'value' => 'DESC',
		'desc' 	=> esc_html__('Post Order', 'workreap'),
		'label' => esc_html__('Order By', 'workreap'),
		'choices' => array(
			'three' => esc_html__('3 Columns', 'workreap'),
			'four' => esc_html__('4 Columns', 'workreap'),
		),
	),
	'show_posts' => array(
		'type' => 'slider',
		'value' => 6,
		'properties' => array(
			'min' => 1,
			'max' => 100,
			'sep' => 1,
		),
		'label' => esc_html__('Show no of posts', 'workreap'),
	),
);
