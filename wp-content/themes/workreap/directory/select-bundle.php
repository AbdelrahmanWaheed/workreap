<?php
/**
 *
 * Template Name: Bundle Selection
 *
 * @package   Workreap
 * @author    Amentotech
 * @link      http://amentotech.com/
 * @since 1.0
 */
get_header();

$project_id = isset($_GET['project']) ? $_GET['project'] : 0;

the_post();
?>
<div class="wt-haslayout">
	<div class="container">
		<div class="wt-sc-packages-list wt-packages-wrap">
			<div class="row justify-content-center">
				<?php if ( $project_id == 0 ) {
					$message	= esc_html__('You can not open this page directy.', 'workreap');
					$title		= esc_html__('Error :','workreap');
					Workreap_Prepare_Notification::workreap_error($title, $message);			
				} else { ?>
					<div class="col-12 col-lg-8">
						<div class="wt-sectionheadvtwo wt-textcenter">
							<div class="wt-sectiontitlevtwo">
								<h2><?php the_title(); ?></h2>
							</div>
							<div class="wt-description">
								<?php the_content(); ?>
							</div>
						</div>
					</div>
					<?php if (class_exists('WooCommerce')) { ?>
						<div class="wt-packagestwo">
							<?php 
								$args = array(
									'post_type' 	=> 'bundles',
									'meta_key' 		=> 'fw_options',
									'meta_value' 	=> 'one-to-one',
									'meta_compare' 	=> 'LIKE',
									'orderby' 		=> 'menu_order',
									'order'			=> 'ASC', 
								);

								$bundles = new WP_Query( $args );
								if ($bundles->have_posts()) {
									$currency_symbol  = workreap_get_current_currency();
									$project_category = wp_get_post_terms($project_id, 'project_cat', array('fields' => 'ids'))[0];
									while ($bundles->have_posts()) :
										$bundles->the_post();
										$bk_settings = worrketic_hiring_payment_setting();
										if (function_exists('fw_get_db_post_option')) {
											$designs   		= fw_get_db_post_option(get_the_ID(), 'designs');
											$featured   	= fw_get_db_post_option(get_the_ID(), 'featured');
											$highlighted	= fw_get_db_post_option(get_the_ID(), 'highlighted');
											$price   		= intval(fw_get_db_post_option(get_the_ID(), 'price_cat_' . $project_category));
											$freelancer_shares = intval($price * (100 - $bk_settings['percentage']) / 100.0);
											$package_features = array(
												array('label' => 'Number Of Designs', 'value' => $designs, 'type' => 'text'),
												array('label' => 'Featured', 'value' => $featured, 'type' => 'boolean'),
												array('label' => 'Highlighted', 'value' => $highlighted, 'type' => 'boolean'),
											);
											?>
											<div class="col-md-6 col-lg-4 employer-packages">
												<div class="wt-packagetwo">
													<div class="wt-package-content">
														<h5><?php echo esc_html(get_the_title()); ?></h5>
														<?php if(!empty($bundle_img)){?>
															<img src="<?php echo esc_url($bundle_img); ?>" alt="<?php esc_attr_e('Bundle', 'workreap_core'); ?>">
														<?php }?>
														<strong>
															<sup><?php echo esc_html($currency_symbol['symbol']);?></sup>
															<?php echo $price; ?> <sub>/ <?php echo $freelancer_shares;?></sub>
														</strong>
													</div>
													<div class="jb-package-feature">
														<h6><?php esc_html_e('Package Features', 'workreap_core'); ?>:</h6>
														<ul>
														<?php
															if (!empty($package_features)) {
																foreach ($package_features as $feature) {
																	if($feature['type'] == 'text') {
																		echo "<li><p>{$feature['label']}</p><span>{$feature['value']}</span></li>";
																	} else if($feature['type'] == 'boolean') {
																		$icon = $feature['value'] == 'enabled' ? '<i class="fa fa-check-circle"></i>' : 
																			'<i class="fa fa-times-circle"></i>';
																		$class = $feature['value'] == 'enabled' ? 'class="jb-available"' : 
																			'class="jb-not-available"';
																		echo "<li><p>{$feature['label']}</p><span {$class}>{$icon}</span></li>";
																	}
																}
															}
														?>
														</ul>
														<div class="wt-btnarea">
															<a class="wt-btntwo wt-select-bundle" data-project-id="<?php echo intval($project_id);?>" 
																data-bundle-id="<?php the_ID(); ?>" href="javascript:;">
																<span><?php esc_html_e('Buy Now', 'workreap_core');?></span>
															</a>
														</div>
													</div>
												</div>
											</div>
										<?php }
									endwhile;
									wp_reset_postdata();
								} ?>
							</div>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php
get_footer();
