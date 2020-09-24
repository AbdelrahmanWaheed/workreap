<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    Workreap
 * @subpackage Workreap/admin
 */
/**
 * @init            Theme Admin Menu init
 * @package         Amentotech
 * @subpackage      workreap_core/admin/partials
 * @since           1.0
 * @desc            This Function Will Produce All Tabs View.
 */
if (!function_exists('workreap_core_admin_menu')) {
    add_action('admin_menu', 'workreap_core_admin_menu');

    function workreap_core_admin_menu() {
        $url = admin_url();
        add_submenu_page('edit.php?post_type=freelancers', 
						 esc_html__('Settings', 'workreap_core'), 
						 esc_html__('Settings', 'workreap_core'), 
						 'manage_options', 
						 'workreap_settings', 
						 'workreap_admin_page'
        );
		
    }
}


/**
 * @init            Settings Admin Page
 * @package         Workreap
 * @subpackage      Workreap/admin/partials
 * @since           1.0
 * @desc            This Function Will Produce All Tabs View.
 */
if (!function_exists('workreap_admin_page')) {

    function workreap_admin_page() {
		$settings	= workreap_get_theme_settings();

		$protocol = is_ssl() ? 'https' : 'http';
		$post_args	= array( '_builtin' => false, 
							 'publicly_queryable' => true, 
							 'show_ui' => true 
						 );

		$term_args	= array( '_builtin' => false, 
						 'publicly_queryable' => true, 
						 'show_ui' => true 
					 );

		$taxonomies = get_taxonomies( $term_args, 'objects' ); 
		$post_types = get_post_types( $post_args,'objects' );
        $protocol = is_ssl() ? 'https' : 'http';
        ob_start();
		
        ?>
        <div id="wt-main" class="wt-main wt-addnew">
            <div class="wrap">
                <div id="wt-tab1s" class="wt-tabs">
					
                    <div class="wt-tabscontent">
                        <div id="wt-main" class="wt-main wt-features settings-main-wrap">
						    <div class="wt-featurescontent">
                                <div class="wt-twocolumns">
                                <ul class="wt-tabsnav">
									<li class="<?php echo isset( $_GET['tab'] ) && $_GET['tab'] === 'welcome' ? 'wt-active' : ''; ?>">
										<a href="<?php echo cus_prepare_final_url('welcome','settings'); ?>">
											<?php esc_html_e("What's New?", 'workreap_core'); ?>
										</a>
									</li> 
									<li class="<?php echo isset( $_GET['tab'] ) && $_GET['tab'] === 'settings'? 'wt-active' : ''; ?>">
										<a href="<?php echo cus_prepare_final_url('settings','settings'); ?>">
											<?php esc_html_e('Settings', 'workreap_core'); ?>
										</a>
									</li>
								</ul>
								<?php if( isset( $_GET['tab'] ) && $_GET['tab'] === 'settings' ){?>
										<div class="settings-wrap">
											<div class="wt-boxarea">
												<div id="tabone">
													<div class="wt-titlebox">
														<h3><?php esc_html_e('Rewrite URL', 'workreap_core'); ?></h3>
													</div>
													<form method="post" class="save-settings-form">
														<?php if( !empty( $post_types ) ){
															foreach ($post_types as $key => $post_type) {?>
															<div class="wt-privacysetting">
																<span class="wt-tooltipbox">
																	<i>?</i>
																	<span class="tooltiptext"><?php esc_html_e('It will be used at post / Taxonomy detail page in URL as slug. Please use words without spaces.', 'workreap_core'); ?></span>
																</span>
																<span><?php echo esc_attr($post_type->label);?></span>
																<div class="sp-input-setting">
																	<div class="form-group">
																		<input type="text" name="settings[post][<?php echo esc_attr( $key );?>]" class="form-control" value="<?php echo  !empty( $settings['post'][$key] ) ?  esc_attr( $settings['post'][$key] ) : '';?>">
																	</div>
																</div>
															</div>
														<?php }}?>
														<?php if( !empty( $taxonomies ) ){ 
														foreach ($taxonomies as $key => $term) {?>
															<div class="wt-privacysetting">
																<span class="wt-tooltipbox">
																	<i>?</i>
																	<span class="tooltiptext"><?php esc_html_e('It will be used at post / Taxonomy detail page in URL as slug. Please use words without spaces.', 'workreap_core'); ?></span>
																</span>
																<span><?php echo esc_attr($term->label);?></span>
																<div class="sp-input-setting">
																	<div class="form-group">
																		<input type="text" name="settings[term][<?php echo esc_attr( $key );?>]" class="form-control" value="<?php echo  !empty( $settings['term'][$key] ) ?  esc_attr( $settings['term'][$key] ) : '';?>">
																	</div>
																</div>
															</div>
														<?php }}?>
														
														<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary save-data-settings" value="<?php esc_html_e('Save Changes', 'workreap_core'); ?>"></p>
													</form>
												</div>
											</div>
										</div>
										<?php }else{?>
											<div class="wt-content">
												<div class="wt-boxarea">
													<div class="wt-title">
														<h3><?php esc_html_e('Minimum System Requirements', 'workreap_core'); ?></h3>
													</div>
													<div class="wt-contentbox">
														<ul class="wt-liststyle wt-dotliststyle wt-twocolumnslist">
															<li><?php esc_html_e('PHP version should be 7.0 or above','workreap_core');?></li>
															<li><?php esc_html_e('PHP Zip extension Should','workreap_core');?></li>
															<li><?php esc_html_e('max_execution_time = 300','workreap_core');?></li>
															<li><?php esc_html_e('max_input_time = 300','workreap_core');?></li>
															<li><?php esc_html_e('memory_limit = 300','workreap_core');?></li>
															<li><?php esc_html_e('post_max_size = 100M','workreap_core');?></li>
															<li><?php esc_html_e('upload_max_filesize = 100M','workreap_core');?></li>
															<li><?php esc_html_e('Node.js for real-time chat','workreap_core');?></li>
															<li><?php esc_html_e('allow_url_fopen and allow_url_include must be on','workreap_core');?></li>
														</ul>
													</div>
												</div>
											</div>
											<aside class="wt-sidebar">
												<div class="wt-widgetbox wt-widgetboxquicklinks">
													<div class="wt-title">
														<h3><?php esc_html_e('Video Tutorial', 'workreap_core'); ?></h3>
													</div>
													<figure>
														<div style="position:relative;height:0;padding-bottom:56.25%">
															<iframe width="640" height="360" src="https://www.youtube.com/embed/EgeOgt6nqcU?controls=0" frameborder="0" style="position:absolute;width:100%;height:100%;left:0" allowfullscreen></iframe>
														</div>
													</figure>
												</div>

												<div class="wt-widgetbox wt-widgetboxquicklinks">
													<div class="wt-title">
														<h3><?php esc_html_e('Get Support', 'workreap_core'); ?></h3>
													</div>
													<a class="wt-btn" target="_blank" href="https://amentotech.ticksy.com/"><?php esc_html_e('Create support ticket', 'workreap_core'); ?></a>
												</div>
											</aside>
										<?php }?>	
                                </div>
                                <div class="wt-socialandcopyright">
                                    <span class="wt-copyright"><?php echo date('Y'); ?>&nbsp;<?php esc_html_e('All Rights Reserved', 'workreap_core'); ?> &copy; <a target="_blank"  href="https://themeforest.net/user/amentotech/"><?php esc_html_e('Amentotech', 'workreap_core'); ?></a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        echo ob_get_clean();
    }
}
