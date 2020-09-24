<?php

if (!defined('FW')) {
    die('Forbidden');
}

$options = array(
    'services' => array(
        'label' => esc_html__('Add services', 'workreap'),
        'type' => 'addable-popup',
        'template' => '{{- title }}',
        'value' => array(),
        'desc' => esc_html__('Add services you are providing', 'workreap'),
        'popup-options' => array(
            'title' => array(
                'label' => esc_html__('Title', 'workreap'),
                'desc' => esc_html__('Add title or leave it empty to hide', 'workreap'),
                'type' => 'text',
                'value' => ''
            ),
            'description' => array(
                'label' => esc_html__('Description', 'workreap'),
                'desc' => esc_html__('Add description or leave it empty to hide', 'workreap'),
                'type' => 'wp-editor',
                'value' => ''
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
        ),
    ),
);
