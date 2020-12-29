<?php

if (!defined('FW')) {
    die('Forbidden');
}

$options = array(
    'fees-settings' => array(
        'title' => esc_html__('Fees Settings', 'workreap'),
        'type' => 'tab',
        'options' => array(
            'private_project' => array(
                'type'  => 'group',
                'options' => array(
                    'private_project_enable' => array(
                        'label' => esc_html__('Private Job Enable', 'workreap'),
                        'desc'  => esc_html__('Enable / Disable private job option', 'workreap'),
                        'type'  => 'checkbox',
                        'value' => false,
                    ),
                    'private_project_cost' => array(
                        'label' => esc_html__('Private Job Cost', 'workreap'),
                        'desc'  => esc_html__('Set private job cost', 'workreap'),
                        'type'  => 'number',
                        'value' => '',
                    ),
                ),
            ),
            'faster_project' => array(
                'type'  => 'group',
                'options' => array(
                    'faster_project_enable' => array(
                        'label' => esc_html__('Faster Job Enable', 'workreap'),
                        'desc'  => esc_html__('Enable / Disable faster job option', 'workreap'),
                        'type'  => 'checkbox',
                        'value' => false,
                    ),
                    'faster_project_options' => array(
                        'type'  => 'addable-box',
                        'label' => esc_html__('Faster Job Options', 'workreap'),
                        'desc'  => esc_html__('Add job deadlines and fees', 'workreap'),
                        'box-options' => array(
                            'label'  => array(
                                'label' => esc_html__('Label', 'workreap'),
                                'type'  => 'text',
                            ),
                            'period' => array(
                                'label' => esc_html__('Period', 'workreap'),
                                'type'  => 'number', 
                                'desc'  => 'Set period in days',
                            ),
                            'fees' => array(
                                'label' => esc_html__('Fees', 'workreap'),
                                'type'  => 'number',
                            ),
                        ),
                        'template' => '{{- label }}', // box title
                        // 'box-controls' => array(// buttons next to (x) remove box button
                        //     'control-id' => '<small class = "dashicons dashicons-smiley"></small>',
                        // ),
                        'limit' => 0, // limit the number of boxes that can be added
                        'add-button-text' => esc_html__('Add Option', 'workreap'),
                        'sortable' => true,
                    ),
                ),
            ),
            'project_participation' => array(
                'type'  => 'group',
                'options' => array(
                    'project_participation_enable' => array(
                        'label' => esc_html__('Job Participation Enable', 'workreap'),
                        'desc'  => esc_html__('Enable / Disable job participation option', 'workreap'),
                        'type'  => 'checkbox',
                        'value' => false,
                    ),
                    'project_participation_options' => array(
                        'type'  => 'addable-box',
                        'label' => esc_html__('Job Participation Options', 'workreap'),
                        'desc'  => esc_html__('Add job participation options', 'workreap'),
                        'box-options' => array(
                            'label'  => array(
                                'label' => esc_html__('Label', 'workreap'),
                                'type'  => 'text',
                            ),
                            'fees' => array(
                                'label' => esc_html__('Fees', 'workreap'),
                                'type'  => 'number',
                            ),
                        ),
                        'template' => '{{- label }}', // box title
                        // 'box-controls' => array(// buttons next to (x) remove box button
                        //     'control-id' => '<small class = "dashicons dashicons-smiley"></small>',
                        // ),
                        'limit' => 0, // limit the number of boxes that can be added
                        'add-button-text' => esc_html__('Add Option', 'workreap'),
                        'sortable' => true,
                    ),
                ),
            ),
        )
    )
);
