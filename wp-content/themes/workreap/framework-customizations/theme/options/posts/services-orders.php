<?php

if (!defined('FW')) {
    die('Forbidden');
}

$dynamic_rating_data = array();
if (!empty($_GET['post'])) {
    $review_id 		= intval($_GET['post']);
    /* Get the rating headings */
    $rating_titles 	= workreap_project_ratings('services_ratings');

    if (!empty($rating_titles)) {
        foreach ($rating_titles as $slug => $label) {
            $dynamic_rating_data[$slug] = array(
                'type' => 'slider',
                'value' => $label,
                'properties' => array(
                    'min'  => intval(1),
                    'max'  => intval(5),
                    'step' => 1,
                ),
                'label' => $label,
            );
        }
    }
}

$options = array(  
	
	'micro_services_settings' => array(
				'title' 	=> esc_html__('Service setting', 'workreap'),
				'type' 		=> 'box',
				'options' 	=> array(
							'cus_project_reviews' => array(
											'type' 	=> 'html',
											'html' 	=> esc_html__('Feedback in the case of Service order is compeleted or cancelled. ', 'workreap'),
											'label' => esc_html__('', 'workreap'),
											//'desc' 	=> esc_html__('Feedback in the case of order is compeleted or cancelled. ', 'workreap'),
											'help' 	=> esc_html__('', 'workreap'),
											'images_only' => true,
										),
							'feedback' => array(
									'type' => 'textarea',
									'value' => '',
									'label' => esc_html__('Service feedback', 'workreap'),
								),
				),
		),
    'reviews_ratings' => array(
        'title' 	=> esc_html__('Individual Ratings', 'workreap'),
        'type' 		=> 'box',
        'context' 	=> 'side',
        'priority' 	=> 'high',
        'options' 	=> array(
            $dynamic_rating_data
        ),
		
    ),
);

