<?php

if (!defined('FW')) {
    die('Forbidden');
}
$cfg = array(
    'page_builder' 		=> array(
        'title' 		=> esc_html__('Authentication', 'workreap'),
        'description' 	=> esc_html__('This will display login/registration.', 'workreap'),
        'tab' 			=> esc_html__('Workreap', 'workreap'),
		'popup_size' 	=> 'small'
    )
);

