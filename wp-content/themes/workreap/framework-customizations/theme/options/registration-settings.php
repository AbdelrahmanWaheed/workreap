<?php

if (!defined('FW')) {
    die('Forbidden');
}
$options = array(
    'registration' => array(
        'title' => esc_html__('Registeration Settings', 'workreap'),
        'type' => 'tab',
        'options' => array( 
            'general' => array(
				'title' => esc_html__('Registration Settings.', 'workreap'),
				'type' => 'tab',
				'options' => array(
					'step-one-group' => array(
						'type' => 'group',
						'options' => array(              
							'enable_login_register' => array(
								'type' => 'multi-picker',
								'label' => false,
								'desc' => '',
								'picker' => array(
									'gadget' => array(
										'type' => 'switch',
										'value' => 'disable',
										'attr' => array(),
										'label' => esc_html__('Login/Register ?', 'workreap'),
										'desc' => esc_html__('Enable/Disable login/register link.', 'workreap'),
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
										//testing
										'login_signup_type' => array(
											'type' => 'select',
											'value' => 'pages',
											'attr' => array(),
											'label' => esc_html__('Login signup type?', 'workreap'),
											'desc' => esc_html__('Either pages or in POPUPS', 'workreap'),
											'choices' => array(
												'pages' => esc_html__('Pages', 'workreap'),
												'popup' => esc_html__('POPUPS', 'workreap'),
												'single_step' => esc_html__('Single Step Registration', 'workreap'),
											)
										),
										'single_step_image' => array(
											'type' => 'upload',
											'label' => esc_html__('Image', 'workreap'),
											'hint' => esc_html__('', 'workreap'),
											'desc' => esc_html__('Upload Image to be shown on right page.This is only show if login type is Single Step Registration.', 'workreap'),
											'images_only' => true,
										),
										'single_step_logo' => array(
											'type' => 'upload',
											'label' => esc_html__('Logo Image', 'workreap'),
											'hint' => esc_html__('', 'workreap'),
											'desc' => esc_html__('Upload Image to be shown top.This is only show if login type is Single Step Registration.', 'workreap'),
											'images_only' => true,
										),
										'registration' => array(
											'type' => 'multi-picker',
											'label' => false,
											'desc' => '',
											'picker' => array(
												'gadget' => array(
													'type' => 'switch',
													'value' => 'disable',
													'attr' => array(),
													'label' => esc_html__('Enable Registration Form?', 'workreap'),
													'desc' => esc_html__('Enable/Disable login/register link.', 'workreap'),
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
													'term_text' => array(
														'type' => 'textarea',
														'value' => '',
														'label' => esc_html__('Terms Text.', 'workreap'),
														'desc' => esc_html__('Add terms & Conditions text, which will serve as description on registration process', 'workreap'),                                      
													),  
													'terms_link' => array(
														'label' => esc_html__('Terms page?', 'workreap'),
														'type' => 'multi-select',
														'population' => 'posts',
														'source' => 'page',
														'desc' => esc_html__('Choose term page', 'workreap'),
														'limit' => 1,
														'prepopulate' => 100,
													),
												),
												'default' => array(),
											),
											'show_borders' => false,
										),                                            
										'login' => array(
											'type' => 'switch',
											'value' => 'enable',
											'attr' => array(),
											'label' => esc_html__('Login?', 'workreap'),
											'desc' => esc_html__('Enable login form.', 'workreap'),
											'left-choice' => array(
												'value' => 'disable',
												'label' => esc_html__('Disable', 'workreap'),
											),
											'right-choice' => array(
												'value' => 'enable',
												'label' => esc_html__('Enable', 'workreap'),
											),
										),
										'login_reg_page' => array(
											'label' => esc_html__('Choose Page', 'workreap'),
											'type' => 'multi-select',
											'population' => 'posts',
											'source' => 'page',
											'desc' => esc_html__('Choose login/register template page.', 'workreap'),
											'limit' => 1,
											'prepopulate' => 100,
										),                                            
									),
									'default' => array(),
								),
								'show_borders' => false,
							),                          
						)
					),
				)
			),
			'step-one' => array(
				'title' => esc_html__('Step One Settings', 'workreap'),
				'type' => 'tab',
				'options' => array(
					'step-one-group' => array(
						'type' => 'group',
						'options' => array(
							'step_one_title' => array(
								'type' => 'text',
								'value' => '',                                       
								'label' => esc_html__('Step Title', 'workreap'),
								'desc' => esc_html__('Add Step One title, which will serve as title on registration process', 'workreap'),                                        
							),
							'step_one_desc' => array(
								'type' => 'textarea',
								'value' => '',
								'label' => esc_html__('Step description.', 'workreap'),
								'desc' => esc_html__('Add step one description, which will serve as description on registration process', 'workreap'),                                      
							),
						)
					),
				)
			),
			'step-two' => array(
				'title' => esc_html__('Step Two Settings', 'workreap'),
				'type' => 'tab',
				'options' => array(
					'step-two-group' => array(
						'type' => 'group',
						'options' => array(
							'step_two_title' => array(
								'type' => 'text',
								'value' => '',                                       
								'label' => esc_html__('Step Title', 'workreap'),
								'desc' => esc_html__('Add step two title, which will serve as title on registration process', 'workreap'),                                        
							),
							'step_two_desc' => array(
								'type' => 'textarea',
								'value' => '',
								'label' => esc_html__('Step description.', 'workreap'),
								'desc' => esc_html__('Add step two description, which will serve as description on registration process', 'workreap'),                                      
							),
						)
					),
				)
			),
			'step-three' => array(
				'title' => esc_html__('Step Three Settings', 'workreap'),
				'type' => 'tab',
				'options' => array(
					'step-three-group' => array(
						'type' => 'group',
						'options' => array(
							'step_three_title' => array(
								'type' => 'text',
								'value' => '',                                       
								'label' => esc_html__('Step Title', 'workreap'),
								'desc' => esc_html__('Add step three title, which will serve as title on registration process', 'workreap'),                                        
							),
							'step_three_desc' => array(
								'type' => 'textarea',
								'value' => '',
								'label' => esc_html__('Step description.', 'workreap'),
								'desc' => esc_html__('Add step three description, which will serve as description on registration process', 'workreap'),                                      
							),                                    
							'step_image' => array(
								'type' => 'upload',
								'label' => esc_html__('Image', 'workreap'),
								'hint' => esc_html__('', 'workreap'),
								'desc' => esc_html__('Upload Image to be shown on the step three.', 'workreap'),
								'images_only' => true,
							),   
							'why_need_code_page' => array(
								'label' => esc_html__('Choose Page', 'workreap'),
								'type' => 'multi-select',
								'population' => 'posts',
								'source' => 'page',
								'desc' => esc_html__('Choose page to show why user needs code.', 'workreap'),
								'limit' => 1,
								'prepopulate' => 100,
							),                   
						)
					),
				)
			),  
			'step-four' => array(
				'title' => esc_html__('Step Four Settings', 'workreap'),
				'type' => 'tab',
				'options' => array(
					'step-four-group' => array(
						'type' => 'group',
						'options' => array(
							'step_four_title' => array(
								'type' => 'text',
								'value' => '',                                       
								'label' => esc_html__('Step Title', 'workreap'),
								'desc' => esc_html__('Add step four title, which will serve as title on registration process', 'workreap'),                                        
							),
							'step_four_desc' => array(
								'type' => 'textarea',
								'value' => '',
								'label' => esc_html__('Step description.', 'workreap'),
								'desc' => esc_html__('Add step four description, which will serve as description on registration process', 'workreap'),                                      
							),                                
						)
					),
				)
			),        
        )
    )
);
