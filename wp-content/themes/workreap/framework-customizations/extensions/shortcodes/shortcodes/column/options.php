<?php

if (!defined('FW')) {
    die('Forbidden');
}

$options = array(
    'general_settings' => array(
        'type' => 'tab',
        'title' => esc_html__('General Settings', 'workreap'),
        'options' => array(
            'custom_classes' => array(
                'label' => esc_html__('Custom Classes', 'workreap'),
                'desc' => esc_html__('Add Custom Classes', 'workreap'),
                'type' => 'text',
            ),
        )
    ),
    'margin_settings' => array(
        'type' => 'tab',
        'title' => esc_html__('Margin', 'workreap'),
        'options' => array(
            'margin_top' => array(
                'type' => 'text',
                'value' => '',
                'label' => esc_html__('Margin Top', 'workreap'),
                'desc' => esc_html__('add margin, Leave it empty to hide, Note: add only integer value as : 10', 'workreap'),
            ),
            'margin_bottom' => array(
                'type' => 'text',
                'value' => '',
                'label' => esc_html__('Margin Bottom', 'workreap'),
                'desc' => esc_html__('add margin, Leave it empty to hide, Note: add only integer value as : 10', 'workreap'),
            ),
            'margin_left' => array(
                'type' => 'text',
                'value' => '',
                'label' => esc_html__('Margin Left', 'workreap'),
                'desc' => esc_html__('add margin, Leave it empty to hide, Note: add only integer value as : 10', 'workreap'),
            ),
            'margin_right' => array(
                'type' => 'text',
                'value' => '',
                'label' => esc_html__('Margin Right', 'workreap'),
                'desc' => esc_html__('add margin, Leave it empty to hide, Note: add only integer value as : 10', 'workreap'),
            ),
        ),
    ),
    'padding_settings' => array(
        'type' => 'tab',
        'title' => esc_html__('Padding', 'workreap'),
        'options' => array(
            'padding_top' => array(
                'type' => 'text',
                'value' => '',
                'label' => esc_html__('Padding Top', 'workreap'),
                'desc' => esc_html__('add padding, Leave it empty to hide, Note: add only integer value as : 10', 'workreap'),
            ),
            'padding_bottom' => array(
                'type' => 'text',
                'value' => '',
                'label' => esc_html__('Padding Bottom', 'workreap'),
                'desc' => esc_html__('add padding, Leave it empty to hide, Note: add only integer value as : 10', 'workreap'),
            ),
            'padding_left' => array(
                'type' => 'text',
                'value' => '',
                'label' => esc_html__('Padding Left', 'workreap'),
                'desc' => esc_html__('add padding, Leave it empty to hide, Note: add only integer value as : 10', 'workreap'),
            ),
            'padding_right' => array(
                'type' => 'text',
                'value' => '',
                'label' => esc_html__('Padding Right', 'workreap'),
                'desc' => esc_html__('add padding, Leave it empty to hide, Note: add only integer value as : 10', 'workreap'),
            ),
        )
    ),
    'responsive_settings' => array(
        'type' => 'tab',
        'title' => esc_html__('Responsive Settings', 'workreap'),
        'options' => array(
			'xs_settings' => array(
				'type' => 'tab',
				'title' => esc_html__('Extra small', 'workreap'),
				'options' => array(
					'extra_small_switch' => array(
						'label' => esc_html__('Extra small Screen', 'workreap'),
						'desc' => esc_html__('Show/hide Small Screen Settings', 'workreap'),
						'type' => 'switch',
						'value' => 'off',
						'left-choice' => array(
							'value' => 'on',
							'label' => esc_html__('ON', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'off',
							'label' => esc_html__('OFF', 'workreap'),
						),
					),
					'extra_small' => array(
						'type' => 'select',
						'value' => 'no-repeat',
						'attr' => array(),
						'label' => esc_html__('Small Screen Class', 'workreap'),
						'desc' => esc_html__('Choose Your Small Screen Class', 'workreap'),
						'help' => esc_html__('', 'workreap'),
						'choices' => array(
							'col-xs-1' => esc_html__('col-xs-1', 'workreap'),
							'col-sm-2' => esc_html__('col-xs-2', 'workreap'),
							'col-xs-3' => esc_html__('col-xs-3', 'workreap'),
							'col-xs-4' => esc_html__('col-xs-4', 'workreap'),
							'col-xs-5' => esc_html__('col-xs-5', 'workreap'),
							'col-xs-6' => esc_html__('col-xs-6', 'workreap'),
							'col-xs-7' => esc_html__('col-xs-7', 'workreap'),
							'col-xs-8' => esc_html__('col-xs-8', 'workreap'),
							'col-xs-9' => esc_html__('col-xs-9', 'workreap'),
							'col-xs-10' => esc_html__('col-xs-10', 'workreap'),
							'col-xs-11' => esc_html__('col-xs-11', 'workreap'),
							'col-xs-12' => esc_html__('col-xs-12', 'workreap'),
						),
					),
				),
			),
			'sm_settings' => array(
				'type' => 'tab',
				'title' => esc_html__('Small Screen', 'workreap'),
				'options' => array(
					'responsive_switch' => array(
						'label' => esc_html__('Responsive Settings', 'workreap'),
						'desc' => esc_html__('Show/hide Small Screen Settings', 'workreap'),
						'type' => 'switch',
						'value' => 'off',
						'left-choice' => array(
							'value' => 'on',
							'label' => esc_html__('ON', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'off',
							'label' => esc_html__('OFF', 'workreap'),
						),
					),
					'responsive_classes' => array(
						'type' => 'select',
						'value' => 'no-repeat',
						'attr' => array(),
						'label' => esc_html__('Small Screen Class', 'workreap'),
						'desc' => esc_html__('Choose Your Small Screen Class', 'workreap'),
						'help' => esc_html__('', 'workreap'),
						'choices' => array(
							'col-sm-1' => esc_html__('col-sm-1', 'workreap'),
							'col-sm-2' => esc_html__('col-sm-2', 'workreap'),
							'col-sm-3' => esc_html__('col-sm-3', 'workreap'),
							'col-sm-4' => esc_html__('col-sm-4', 'workreap'),
							'col-sm-5' => esc_html__('col-sm-5', 'workreap'),
							'col-sm-6' => esc_html__('col-sm-6', 'workreap'),
							'col-sm-7' => esc_html__('col-sm-7', 'workreap'),
							'col-sm-8' => esc_html__('col-sm-8', 'workreap'),
							'col-sm-9' => esc_html__('col-sm-9', 'workreap'),
							'col-sm-10' => esc_html__('col-sm-10', 'workreap'),
							'col-sm-11' => esc_html__('col-sm-11', 'workreap'),
							'col-sm-12' => esc_html__('col-sm-12', 'workreap'),
						),
					),
				),
			),
            'md_settings' => array(
				'type' => 'tab',
				'title' => esc_html__('Medium Screen', 'workreap'),
				'options' => array(
					'medium_switch' => array(
						'label' => esc_html__('Medium Screen Settings', 'workreap'),
						'desc' => esc_html__('Show/hide Medium Screen Settings', 'workreap'),
						'type' => 'switch',
						'value' => 'off',
						'left-choice' => array(
							'value' => 'on',
							'label' => esc_html__('ON', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'off',
							'label' => esc_html__('OFF', 'workreap'),
						),
					),
					'medium_classes' => array(
						'type' => 'select',
						'value' => 'no-repeat',
						'attr' => array(),
						'label' => esc_html__('Medium Screen Class', 'workreap'),
						'desc' => esc_html__('Choose Your Medium Screen Class', 'workreap'),
						'help' => esc_html__('', 'workreap'),
						'choices' => array(
							'col-md-1' => esc_html__('col-md-1', 'workreap'),
							'col-md-2' => esc_html__('col-md-2', 'workreap'),
							'col-md-3' => esc_html__('col-md-3', 'workreap'),
							'col-md-4' => esc_html__('col-md-4', 'workreap'),
							'col-md-5' => esc_html__('col-md-5', 'workreap'),
							'col-md-6' => esc_html__('col-md-6', 'workreap'),
							'col-md-7' => esc_html__('col-md-7', 'workreap'),
							'col-md-8' => esc_html__('col-md-8', 'workreap'),
							'col-md-9' => esc_html__('col-md-9', 'workreap'),
							'col-md-10' => esc_html__('col-md-10', 'workreap'),
							'col-md-11' => esc_html__('col-md-11', 'workreap'),
							'col-md-12' => esc_html__('col-md-12', 'workreap'),
						),
					),
				),
			),
			'lg_settings' => array(
				'type' => 'tab',
				'title' => esc_html__('Large Screen', 'workreap'),
				'options' => array(
					'large_switch' => array(
						'label' => esc_html__('Large Screen Settings', 'workreap'),
						'desc' => esc_html__('Show/hide Large Screen Settings', 'workreap'),
						'type' => 'switch',
						'value' => 'off',
						'left-choice' => array(
							'value' => 'on',
							'label' => esc_html__('ON', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'off',
							'label' => esc_html__('OFF', 'workreap'),
						),
					),
					'large_classes' => array(
						'type' => 'select',
						'value' => 'no-repeat',
						'attr' => array(),
						'label' => esc_html__('Large Screen Class', 'workreap'),
						'desc' => esc_html__('Choose Your Large Screen Class', 'workreap'),
						'help' => esc_html__('', 'workreap'),
						'choices' => array(
							'col-lg-1' => esc_html__('col-lg-1', 'workreap'),
							'col-lg-2' => esc_html__('col-lg-2', 'workreap'),
							'col-lg-3' => esc_html__('col-lg-3', 'workreap'),
							'col-lg-4' => esc_html__('col-lg-4', 'workreap'),
							'col-lg-5' => esc_html__('col-lg-5', 'workreap'),
							'col-lg-6' => esc_html__('col-lg-6', 'workreap'),
							'col-lg-7' => esc_html__('col-lg-7', 'workreap'),
							'col-lg-8' => esc_html__('col-lg-8', 'workreap'),
							'col-lg-9' => esc_html__('col-lg-9', 'workreap'),
							'col-lg-10' => esc_html__('col-lg-10', 'workreap'),
							'col-lg-11' => esc_html__('col-lg-11', 'workreap'),
							'col-lg-12' => esc_html__('col-lg-12', 'workreap'),
						),
					),
				),
			),
        )
    ),
);
