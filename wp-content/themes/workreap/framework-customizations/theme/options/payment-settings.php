<?php

if (!defined('FW')) {
    die('Forbidden');
}
$schedules_list	= array();

if( function_exists('workreap_cron_schedule') ) {
	$schedules		= workreap_cron_schedule();
	
	if( !empty( $schedules ) ) {
		foreach ( $schedules as $key => $val ) {
			$schedules_list[$key]	= $schedules[$key]['display'];
		}
	}
}

$options = array(
    'payment-settings' => array(
        'title' => esc_html__('Payment Settings', 'workreap'),
        'type' => 'tab',
        'options' => array(
            'booking-box' => array(
                'title' => esc_html__('Hiring payment Settings', 'workreap'),
                'type' => 'box',
                'options' => array(
                    'hiring_payment_settings' => array(
                        'type' => 'multi-picker',
                        'label' => false,
                        'desc' => '',
                        'picker' => array(
                            'gadget' => array(
                                'type' => 'switch',
                                'value' => 'disable',
                                'attr' => array(),
                                'label' => esc_html__('Hiring payments?', 'workreap'),
                                'desc' => esc_html__('Enable/Disable hiring payment online.', 'workreap'),
                                'left-choice' => array(
                                    'value' => 'disable',
                                    'label' => esc_html__('Disable', 'workreap'),
                                ),
                                'right-choice' => array(
                                    'value' => 'enable',
                                    'label' => esc_html__('Enable', 'workreap'),
                                ),
                            )
                        ),
                        'choices' => array(
                            'enable' => array(
                                'pay' => array(
									'type' => 'multi-picker',
									'label' => false,
									'desc' => '',
									'picker' => array(
										'type' => array(
											'label' => esc_html__('Hiring payments type', 'workreap'),
											'type' => 'select',
											'value' => 'no-repeat',
											'desc' => esc_html__('Select your hiring payment process.', 'workreap'),
											'help' => esc_html__('', 'workreap'),
											'choices' => array(
												'woo' 			=> esc_html__('Online WooCommerce', 'workreap'),
												'offline_woo' 	=> esc_html__('Offline WooCommerce', 'workreap')
											),
										),
									),
									'choices' => array(
										'woo' => array(),
										
									),
								),
                            ),
                            'default' => array(),
                        ),
                        'show_borders' => false,
                    ),
					'min_amount' => array(
						'type' => 'text',
						'label' => esc_html__('Minimum withdraw amount', 'workreap'),
						'desc' => esc_html__('Add minimum amount to process wallet.', 'workreap'),
					),
					'hide_wallet' => array(
						'type' => 'hidden',
						'value' => 'no',
						'attr' => array(),
						'label' => esc_html__('Hide wallet?', 'workreap'),
						'desc' => esc_html__('Hide or show wallet system from users dashboard and from admin side.Hiring payments will work as it is but wallet system will not display in users dashboard.', 'workreap'),
						'left-choice' => array(
							'value' => 'yes',
							'label' => esc_html__('Yes', 'workreap'),
						),
						'right-choice' => array(
							'value' => 'no',
							'label' => esc_html__('No', 'workreap'),
						),
					),
					'service_fee' => array(
						'type' => 'slider',
						'value' => 20,
						'properties' => array(
							'min' => 0,
							'max' => 100,
							'sep' => 1,
						),
						'desc' => esc_html__('Select Service commission in percentage ( % ), set it to 0 to make commission free website', 'workreap'),
						'label' => esc_html__('Service Commission', 'workreap'),
					),
					'cron_interval' => array(
						'label'   		=> esc_html__( 'Cron job interval', 'workreap' ),
						'desc'   		=> esc_html__( 'Select interval for payouts.', 'workreap' ),
						'type'    		=> 'select',
						'value'    		=> 'basic',
						'choices' 		=> $schedules_list
					),
					/*'show_earning' => array(
						'type'  => 'select',
						'value' => 'earnings',
						'attr'  => array(),
						'label' => esc_html__('Earning type?', 'workreap'),
						'desc' 	=> wp_kses( __( 'You can select which amount do you want to show for freelancers earnings on back-end or front-end.<br> 
												Payouts) This means show the total earning of a freelancer which have been transferred/processes to their accounts<br/>
												Earning) This means show the total earning either transferred to their account or not. It will includes available balance showing in freelancer dashboard and processed/transferred amount to the freelancers<br/>
												', 'workreap' ),array(
																'a' => array(
																	'href' => array(),
																	'title' => array()
																),
																'br' => array(),
																'em' => array(),
																'strong' => array(),
															)),
						'choices' 		=> array(
							'payouts'  => esc_html__('Payouts', 'workreap'),
							'earnings' => esc_html__('Earnings', 'workreap'),
						)
					),*/
                )
            ),
        )
    )
);
