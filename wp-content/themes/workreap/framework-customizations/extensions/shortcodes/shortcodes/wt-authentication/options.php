<?php

if (!defined('FW')) {
    die('Forbidden');
}

$options = array(
	 'form_type' => array(
		'label'        => esc_html__('Form type?' , 'workreap') ,
		'type' 		   => 'select',
		'value'        => 'register' ,
		'desc'         => esc_html__('Select form type' , 'workreap') ,
		'choices' => array(
			'register'  => esc_html__('Register Form', 'workreap')
		),
	)
);
