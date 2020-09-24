<?php

if ( !defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$list	= array();
if( function_exists('workreap_mailchimp_list()') ){
	$list	= workreap_mailchimp_list();
}

$options = array(
	'disputes_settings' => array(
		'type' => 'tab',
		'title' => esc_html__( 'Dispute Settings', 'workreap' ),
		'options' => array(
			'dispute-settings' => array(
				'title' => esc_html__('Dispute Settings', 'workreap'),
				'type' => 'tab',
				'options' => array(
					'dispute_options' => array(
						'type' => 'addable-option',
						'value' => array(esc_html__('Employer is not releasing the payments', 'workreap'), 
										 esc_html__('Employer have cancelled the project', 'workreap'), 
										 esc_html__('Freelancer didnot respond' , 'workreap'),
										 esc_html__('Freelancer didnot work as i was expecting', 'workreap')
									),
						'label' 	=> esc_html__('Dispute Options', 'workreap'),
						'desc' 		=> esc_html__('Add leave your dispute headings.', 'workreap'),
						'option' 	=> array('type' => 'text'),
						'add-button-text' => esc_html__('Add', 'workreap'),
						'sortable' => true,
					)
				),
			),
		)
	)
);