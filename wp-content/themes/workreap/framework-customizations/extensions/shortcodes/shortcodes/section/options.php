<?php

if (!defined('FW')) {
    die('Forbidden');
}

$options = array(
    'general_settings' => array(
        'type' => 'tab',
        'title' => esc_html__('Genral Settings', 'workreap'),
        'options' => array(
            'section_heading' => array(
                'label' => esc_html__('Section Heading', 'workreap'),
                'desc' => esc_html__('Enter Your Section Heading Here', 'workreap'),
                'type' => 'text',
            ),
            'is_fullwidth' => array(
                'type' => 'select',
                'value' => 'default',
                'attr' => array(),
                'label' => esc_html__('Section Settings', 'workreap'),
                'desc' => esc_html__('Select Section Settings', 'workreap'),
                'help' => esc_html__('', 'workreap'),
                'choices' => array(
                    'default' => esc_html__('Default', 'workreap'),
                    'stretch_section' => esc_html__('Stretch Section', 'workreap'),
                    'stretch_section_contents' => esc_html__('Stretch Section With Contents', 'workreap'),
                    'stretch_section_contents_corner' => esc_html__('Stretch Section With Contents(No Padding)', 'workreap'),
                ),
            ),
            'background_color' => array(
                'label' => esc_html__('Background Color', 'workreap'),
                'desc' => esc_html__('Please select the background color', 'workreap'),
                'type' => 'rgba-color-picker',
				'value' => '',
            ),
            'background_image' => array(
                'label' => esc_html__('Background Image', 'workreap'),
                'desc' => esc_html__('Please select the background image', 'workreap'),
                'type' => 'background-image',
                'choices' => array(//	in future may will set predefined images
                )
            ),
            'background_repeat' => array(
                'type' => 'select',
                'value' => 'no-repeat',
                'attr' => array(),
                'label' => esc_html__('Background Repeat', 'workreap'),
                'desc' => esc_html__('Repeat Background', 'workreap'),
                'help' => esc_html__('', 'workreap'),
                'choices' => array(
                    'no-repeat' => esc_html__('No Repeat', 'workreap'),
                    'repeat' => esc_html__('Repeat', 'workreap'),
                    'repeat-x' => esc_html__('Repeat X', 'workreap'),
                    'repeat-y' => esc_html__('Repeat Y', 'workreap'),
                ),
            ),
            'positioning_x' => array(
                'type' => 'slider',
                'value' => 0,
                'properties' => array(
                    'min' => -100,
                    'max' => 100,
                    'sep' => 1,
                ),
                'desc' => esc_html__('Background position Horizontally', 'workreap'),
                'label' => esc_html__('Position X, IN ( % )', 'workreap'),
            ),
            'positioning_y' => array(
                'type' => 'slider',
                'value' => 100,
                'properties' => array(
                    'min' => -100,
                    'max' => 100,
                    'sep' => 1,
                ),
                'desc' => esc_html__('Background position Vertically', 'workreap'),
                'label' => esc_html__('Position Y, IN ( % )', 'workreap'),
            ),
            'video' => array(
                'label' => esc_html__('Background Video', 'workreap'),
                'desc' => esc_html__('Insert Video URL to embed this video', 'workreap'),
                'type' => 'text',
				'help' => esc_html__('Note: Mobile browsers do not allow videos to auto play due to bandwidth concerns. Background will not attempt to load videos on mobile devices, instead the poster image will be displayed.', 'workreap'),
            ),
            'custom_id' => array(
                'label' => esc_html__('Custom ID', 'workreap'),
                'desc' => esc_html__('Add Custom ID', 'workreap'),
                'type' => 'text',
            ),
            'custom_classes' => array(
                'label' => esc_html__('Custom Classes', 'workreap'),
                'desc' => esc_html__('Add Custom Classes', 'workreap'),
                'type' => 'text',
            )
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
        )
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
    'parallax_settings' => array(
        'type' => 'tab',
        'title' => esc_html__('Parallax', 'workreap'),
        'options' => array(
            'parallax' => array(
                'label' => esc_html__('Parallax', 'workreap'),
                'desc' => esc_html__('Use background image as parallax.', 'workreap'),
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
            'parallax_bleed' => array(
                'type' => 'slider',
                'value' => 0,
                'properties' => array(
                    'min' => 0,
                    'max' => 500,
                    'sep' => 1,
                ),
                'label' => esc_html__('Parallax Data Bleed', 'workreap'),
            ),
            'parallax_speed' => array(
                'type' => 'select',
                'value' => 'no-repeat',
                'attr' => array(),
                'label' => esc_html__('Parallax Speed', 'workreap'),
                'desc' => esc_html__('Choose Your Parallax Speed', 'workreap'),
                'help' => esc_html__('', 'workreap'),
                'choices' => array(
                    '0' => esc_html__('0', 'workreap'),
                    '0.1' => esc_html__('0.1', 'workreap'),
                    '0.2' => esc_html__('0.2', 'workreap'),
                    '0.3' => esc_html__('0.3', 'workreap'),
                    '0.4' => esc_html__('0.4', 'workreap'),
                    '0.5' => esc_html__('0.5', 'workreap'),
                    '0.6' => esc_html__('0.6', 'workreap'),
                    '0.7' => esc_html__('0.7', 'workreap'),
                    '0.8' => esc_html__('0.8', 'workreap'),
                    '0.9' => esc_html__('0.9', 'workreap'),
                    '1.0' => esc_html__('1.0', 'workreap'),
                ),
            ),
        )
    ),
    'sidebars' => array(
        'type' => 'tab',
        'title' => esc_html__('Sidebar', 'workreap'),
        'options' => array(
            'sidebar' => array(
                'type' => 'multi-picker',
                'label' => false,
                'desc' => false,
                'value' => array('gadget' => 'full'),
                'picker' => array(
                    'gadget' => array(
                        'label' => esc_html__('Section Sidebar', 'workreap'),
                        'type' => 'image-picker',
                        'choices' => array(
                            'full' => get_template_directory_uri() . '/images/sidebars/full.png',
                            'left' => get_template_directory_uri() . '/images/sidebars/left.png',
                            'right' => get_template_directory_uri() . '/images/sidebars/right.png',
                        )
                    )
                ),
                'choices' => array(
                    'full' => array(),
                    'left' => array(
                        'left_sidebar' => array(
                            'type' => 'select',
                            'value' => '',
                            'attr' => array(),
                            'label' => esc_html__('Select Sidebar', 'workreap'),
                            'desc' => esc_html__('Select Left Sidebar', 'workreap'),
                            'help' => esc_html__('', 'workreap'),
                            'choices' => workreap_get_registered_sidebars(),
                        ),
                    ),
                    'right' => array(
                        'right_sidebar' => array(
                            'type' => 'select',
                            'value' => '',
                            'attr' => array(),
                            'label' => esc_html__('Select Sidebar', 'workreap'),
                            'desc' => esc_html__('Select Right Sidebar', 'workreap'),
                            'help' => esc_html__('', 'workreap'),
                            'choices' => workreap_get_registered_sidebars(),
                        ),
                    ),
                )
            )
        )
    ),
);
