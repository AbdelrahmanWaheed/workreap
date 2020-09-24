<?php

if (!defined('FW')) {
    die('Forbidden');
}

$options = array(
    'title' => array(
        'label' => esc_html__('Section Title', 'workreap'),
        'desc' => esc_html__('Add title or leave it empty to hide', 'workreap'),
        'type' => 'text',
        'value' => ''
    ),
    'sub_title' => array(
        'label' => esc_html__('Section Sub Title', 'workreap'),
        'desc' => esc_html__('Add sub title. Leave it empty to hide', 'workreap'),
        'type' => 'text',
        'value' => ''
    ),
    'team_members' => array(
        'label' => esc_html__('Add Team member', 'workreap'),
        'type' => 'addable-popup',
        'template' => '{{- name }}',
        'value' => array(),
        'desc' => esc_html__('Add Team Member', 'workreap'),
        'popup-options' => array(
            'avatar' => array(
                'label' => esc_html__('Team Member Image', 'workreap'),
                'desc' => esc_html__('Either upload a new, or choose an existing image from your media library', 'workreap'),
                'type' => 'upload'
            ),
            'name' => array(
                'label' => esc_html__('Team Member Name', 'workreap'),
                'desc' => esc_html__('Name of the person', 'workreap'),
                'type' => 'text',
                'value' => ''
            ),
            'designation' => array(
                'label' => esc_html__('Designation', 'workreap'),
                'desc' => esc_html__('', 'workreap'),
                'type' => 'text',
                'value' => ''
            ),
            'social_icons' => array(
	            'label' => esc_html__('Social Profiles', 'workreap'),
	            'type' => 'addable-popup',
	            'value' => array(),
	            'desc' => esc_html__('Add Social Icons as much as you want. Choose the icon, url and the title', 'workreap'),
	            'popup-options' => array(
	                'social_name' => array(
	                    'label' => esc_html__('Title', 'workreap'),
	                    'type' => 'text',
	                    'value' => 'Name',
	                    'desc' => esc_html__('The Title of the Link', 'workreap')
	                ),
	                'social_icons_list' => array(
	                    'type' => 'new-icon',
	                    'value' => 'fa-smile-o',
	                    'attr' => array(),
	                    'label' => esc_html__('Choose Icon', 'workreap'),
	                    'desc' => esc_html__('', 'workreap'),
	                    'help' => esc_html__('', 'workreap'),
	                ),
	                'social_url' => array(
	                    'label' => esc_html__('URL', 'workreap'),
	                    'type' => 'text',
	                    'value' => '#',
	                    'desc' => esc_html__('The link to the social profile.', 'workreap')
	                ),
	            ),
	            'template' => '{{- social_name }}',
	        ),        
        ),
    ),
    'loop' => array(
        'type' => 'switch',
        'label' => esc_html__('Loop', 'workreap'),
        'left-choice' => array(
            'value' => 'false',
            'label' => esc_html__('False', 'workreap'),
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
