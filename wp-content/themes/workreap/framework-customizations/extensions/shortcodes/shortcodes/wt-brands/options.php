<?php

if (!defined('FW'))
    die('Forbidden');


$options = array(
	'brands' => array(
        'type' => 'addable-box',
        'label' => esc_html__('Add Brands', 'workreap'),
        'desc' => esc_html__('Add brands image and link', 'workreap'),
        'box-options' => array(
            'image' => array('type' => 'upload'),
            'link'  => array('type' => 'text'),
            'link_target' => array(
                'type' => 'select',
                'value' => '_self',
                'label' => esc_html__('Link Target', 'workreap'),
                'choices' => array(
                    '_blank' => esc_html__('New Tab', 'workreap'),
                    '_self' => esc_html__('Current Tab', 'workreap'),
                ),
                'no-validate' => false,
            ),
        ),
        'template' => '{{- link }}', // box title
        'box-controls' => array(// buttons next to (x) remove box button
            'control-id' => '<small class = "dashicons dashicons-smiley"></small>',
        ),
        'limit' => false, // limit the number of boxes that can be added
        'add-button-text' => esc_html__('Add Brands', 'workreap'),
        'sortable' => true,
    ),
    'loop' => array(
        'type' => 'switch',
        'label' => esc_html__('Loop', 'workreap'),
        'left-choice' => array(
            'value' => 'false',
            'label' => esc_html__('false', 'workreap'),
        ),
        'right-choice' => array(
            'value' => 'true',
            'label' => esc_html__('True', 'workreap'),
        ),
    ),
    'autoplay' => array(
        'type' => 'switch',
        'label' => esc_html__('Autoplay', 'workreap'),
        'left-choice' => array(
            'value' => 'false',
            'label' => esc_html__('False', 'workreap'),
        ),
        'right-choice' => array(
            'value' => 'true',
            'label' => esc_html__('True', 'workreap'),
        ),
    ),
);
