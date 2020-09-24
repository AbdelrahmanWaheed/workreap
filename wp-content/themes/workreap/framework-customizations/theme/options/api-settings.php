<?php

if ( !defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$list	= array();
if( function_exists('workreap_mailchimp_list') ){
	$list	= workreap_mailchimp_list();
}

$options = array(
	'api_settings' => array(
		'type' => 'tab',
		'title' => esc_html__( 'API Credentials', 'workreap' ),
		'options' => array(
			'mailchimp' => array(
                'title' => esc_html__('MailChimp', 'workreap'),
                'type' => 'tab',
                'options' => array(
                    'mailchimp_key' => array(
                        'type' => 'text',
                        'value' => '',
                        'label' => esc_html__('MailChimp Key', 'workreap'),
                        'desc' => wp_kses( __( 'Get Api key From <a href="https://us11.admin.mailchimp.com/account/api/" target="_blank"> Get API KEY </a> <br/> You can create list <a href="https://us11.admin.mailchimp.com/lists/" target="_blank">here</a>'.esc_html__('Latest MailChimp List ','workreap').'<a href="javascrit:;" class="wt-latest-mailchimp-list">'.esc_html__('Click here','workreap').'</a>', 'workreap' ), array(
							'a' => array(
								'href' => array(),
								'class' => array(),
								'title' => array()
							),
							'br' => array(),
							'em' => array(),
							'strong' => array(),
						) ),
                    ),
                    'mailchimp_list' => array(
                        'type' => 'select',
                        'label' => __('MailChimp List', 'workreap'),
                        'choices' => $list,
					),
                    'mailchimp_title' => array(
                        'type' => 'text',
                        'label' => esc_html__('MailChimp Title', 'workreap'),
                        'desc'  => esc_html__('Set mailchimp form title, it will be displayed in the footer', 'workreap'),
					)
                )
            ),
			'google' => array(
				'title' => esc_html__( 'Google', 'workreap' ),
				'type' => 'tab',
				'options' => array(
					'google_key' => array(
						'type' => 'gmap-key',
						'value' => '',
						'label' => esc_html__( 'Google Map Key', 'workreap' ),
						'desc' => wp_kses( __( 'Enter google map key here. It will be used for google maps. Get and Api key From <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"> Get API KEY </a>', 'workreap' ), array(
							'a' => array(
								'href' => array(),
								'title' => array()
							),
							'br' => array(),
							'em' => array(),
							'strong' => array(),
						) ),
					),
				)
			),
			'user_promotions' => array(
				'title' => esc_html__( 'Marketing', 'workreap' ),
				'type' => 'tab',
				'options' => array(
					'user_marketing_promation_api_settings' => array(
                        'type' => 'multi-picker',
                        'label' => false,
                        'desc' => false,
                        'picker' => array(
                            'gadget' => array(
                                'label' => esc_html__('User marketing promotion', 'workreap'),
                                'type' => 'switch',
                                'value' => 'disable',
                                'desc' => esc_html__('Enable/Disable user marketing promotion on user.com.', 'workreap'),
                                'left-choice' => array(
                                    'value' => 'enable',
                                    'label' => esc_html__('Enable', 'workreap'),
                                ),
                                'right-choice' => array(
                                    'value' => 'disable',
                                    'label' => esc_html__('Disable', 'workreap'),
                                ),
                            )
                        ),
                        'choices' => array(
                            'enable' => array(
								'user_api_keys' => array(
									'type' => 'gmap-key',
									'value' => '',
									'label' => esc_html__( 'User.com API Key', 'workreap' ),
									'desc' => wp_kses( __( 'Enter User.com API Key here. It will be used for update user data. Get and Api key From <a href="https://user.com/" target="_blank"> Get API KEY </a>', 'workreap' ), array(
										'a' => array(
											'href' => array(),
											'title' => array()
										),
										'br' => array(),
										'em' => array(),
										'strong' => array(),
									) ),
								),
								'user_app_subdomain' => array(
									'label' => esc_html__('APP subdomain', 'workreap'),
									'type' => 'text',
									'value' => '',
									'desc' => esc_html__('Add APP subdomain here.', 'workreap')
								),
                            ),
                        )
                    )
					
				)
			),
		)
	)
);