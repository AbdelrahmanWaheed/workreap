<?php

if (!defined('FW')) {
    die('Forbidden');
}

$cfg = array(
    'page_builder' => array(
        'title' => esc_html__('Categories slider', 'workreap'),
        'description' => esc_html__('Display categories on freelancer page.', 'workreap'),
        'tab' => esc_html__('Workreap', 'workreap'),
        'popup_size' => 'small' // can be large, medium or small
    )
);
