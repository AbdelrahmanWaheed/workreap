<?php

if (!defined('FW')) {
    die('Forbidden');
}

$cfg = array();

$cfg = array(
    'page_builder' => array(
        'title' => esc_html__('Explore Categories', 'workreap'),
        'description' => esc_html__('Display categories accoring to the post type.', 'workreap'),
        'tab' => esc_html__('Workreap', 'workreap'),
        'popup_size' => 'small' // can be large, medium or small
    )
);
