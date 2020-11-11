<?php

if (!defined('FW')) {
    die('Forbidden');
}

$options = array(
    'bundle_data' => array(
        'title' => esc_html__('Bundle Data', 'workreap'),
        'type' => 'box',
        'options' => array(
            'bundle_features' => array(
                'title' => esc_html__('Features', 'workreap'),
                'type' => 'tab',
                'options' => array(
                    'category' => array(
                        'type' => 'select',
                        'value' => '',
                        'label' => esc_html__('Category', 'workreap'),
                        'choices' => array(
                            'one-to-one' => esc_html__('One To One', 'workreap'),
                            'contest' => esc_html__('Contest', 'workreap'),
                        ),
                    ),
                    'designs' => array(
                        'label' => esc_html__('Number Of Designs', 'workreap'),
                        'type'  => 'text',
                        'value' => '',
                    ),
                    'money_back_guarantee' => array(
                        'label' => esc_html__('100% Money Back Guarantee', 'workreap'),
                        'type' => 'switch',
                        'value' => 'disabled',
                        'left-choice' => array(
                            'value' => 'enabled',
                            'label' => esc_html__('Enabled', 'workreap'),
                        ),
                        'right-choice' => array(
                            'value' => 'disabled',
                            'label' => esc_html__('Disabled', 'workreap'),
                        ),
                    ),
                    'copyright_ownership' => array(
                        'label' => esc_html__('Full Copyright Ownership', 'workreap'),
                        'type' => 'switch',
                        'value' => 'disabled',
                        'left-choice' => array(
                            'value' => 'enabled',
                            'label' => esc_html__('Enabled', 'workreap'),
                        ),
                        'right-choice' => array(
                            'value' => 'disabled',
                            'label' => esc_html__('Disabled', 'workreap'),
                        ),
                    ),
                    'highlighted' => array(
                        'label' => esc_html__('Highlighted Project', 'workreap'),
                        'type' => 'switch',
                        'value' => 'disabled',
                        'left-choice' => array(
                            'value' => 'enabled',
                            'label' => esc_html__('Enabled', 'workreap'),
                        ),
                        'right-choice' => array(
                            'value' => 'disabled',
                            'label' => esc_html__('Disabled', 'workreap'),
                        ),
                    ),
                    'featured' => array(
                        'label' => esc_html__('Featured Project', 'workreap'),
                        'type' => 'switch',
                        'value' => 'disabled',
                        'left-choice' => array(
                            'value' => 'enabled',
                            'label' => esc_html__('Enabled', 'workreap'),
                        ),
                        'right-choice' => array(
                            'value' => 'disabled',
                            'label' => esc_html__('Disabled', 'workreap'),
                        ),
                    ),
                    'prioritized_support' => array(
                        'label' => esc_html__('Prioritized Support', 'workreap'),
                        'type' => 'switch',
                        'value' => 'disabled',
                        'left-choice' => array(
                            'value' => 'enabled',
                            'label' => esc_html__('Enabled', 'workreap'),
                        ),
                        'right-choice' => array(
                            'value' => 'disabled',
                            'label' => esc_html__('Disabled', 'workreap'),
                        ),
                    ),
                ),
            ),
            'bundle_prices' => array(
                'title' => esc_html__('Prices', 'workreap'),
                'desc' => 'Set the price for each project category',
                'type' => 'tab',
                'options' => array()
            ),
        ),
    ),
);

$project_categories = get_terms('project_cat', array(
    'hide_empty' => false,
));

foreach ($project_categories as $category) {
    $options["bundle_data"]["options"]["bundle_prices"]["options"]["price_cat_$category->term_id"] = array(
        'label' => wp_specialchars_decode( $category->name ),
        'type'  => 'text',
        'value' => '',
    );
}