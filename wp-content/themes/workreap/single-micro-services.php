<?php
/**
 *
 * The template used for displaying single service
 *
 * @package   Workreap
 * @author    amentotech
 * @link      https://amentotech.com/user/amentotech/portfolio
 * @version 1.0
 * @since 1.0
 */
global $post;
do_action('workreap_post_views', $post->ID,'services_views');
get_header();
if( apply_filters('workreap_system_access','service_base') === true ){
	while ( have_posts() ) {
	the_post();
	global $post;
	
	$services_views_count   = get_post_meta($post->ID, 'services_views', true);
	$author_id 				= get_the_author_meta( 'ID' );  
	$linked_profile 		= workreap_get_linked_profile_id($author_id);
	$user_link				= !empty($linked_profile) ? get_the_permalink( $linked_profile ) : '';
	$freelancer_title 		= !empty($linked_profile) ? get_the_title( $linked_profile ) :'';	
	$service_url			= get_the_permalink();
	
	$post_name				= !empty($linked_profile) ? workreap_get_slug( $linked_profile ) : '';
	
	$db_project_cat 		= wp_get_post_terms($post->ID, 'project_cat');
	$db_delivery_time 		= wp_get_post_terms($post->ID, 'delivery');
	$db_response_time 		= wp_get_post_terms($post->ID, 'response_time');
	
	$db_addons				= get_post_meta($post->ID,'_addons',true);
	$db_addons				= !empty( $db_addons ) ? $db_addons : array();
	
	$queu_services			= workreap_get_services_count('services-orders',array('hired'),$post->ID);
	$completed_services		= workreap_get_services_count('services-orders',array('completed'),$post->ID);
	$completed_services		= !empty( $completed_services ) ? $completed_services : 0;
	
	$service_views			= get_post_meta($post->ID,'services_views',true);
	$service_views			= !empty( $service_views ) ? $service_views : 0;
	$db_docs			= array();
	$db_price			= '';
	$order_details		= '';
			
	if (function_exists('fw_get_db_post_option')) {
		$db_docs   			= fw_get_db_post_option($post->ID,'docs');
		$order_details   	= fw_get_db_post_option($post->ID,'order_details');
		$db_price   		= fw_get_db_post_option($post->ID,'price');
		$db_videos   		= fw_get_db_post_option($post->ID,'videos');
		$db_downloadable   	= fw_get_db_post_option($post->ID,'downloadable');
	}

	$db_downloadable	= !empty( $db_downloadable ) && $db_downloadable !== 'no' ? $db_downloadable : '';
	
	if(!empty($linked_profile)){
		$freelancer_avatar = apply_filters(
		'workreap_freelancer_avatar_fallback', workreap_get_freelancer_avatar(array('width' => 100, 'height' => 100), $linked_profile), array('width' => 100, 'height' => 100) 
	);
	
		$freelancer_banner = apply_filters(
									'workreap_freelancer_banner_fallback', workreap_get_freelancer_banner(array('width' => 350, 'height' => 172), $linked_profile), array('width' => 350, 'height' => 172) 
								);
	}
	
	$width			= 75;
	$height			= 75;
	
	$full_width			= 670;
	$full_height		= 370;
	
	$flag 				= rand(9999, 999999);
	$slider_images		= count( $db_docs );
	$slider_videos		= !empty( $db_videos ) ? count( $db_videos ) : 0;
	$slider_images		= $slider_videos + $slider_images;
		
	if( $slider_images >= 1 ){
		$owl_class	='owl-carousel';
	} else {
		$owl_class	='';
	}
?>
	<div class="container">
		<div class="row">
			<div id="wt-twocolumns" class="wt-twocolumns wt-haslayout">
				<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 col-xl-8 float-left">
					<div class="wt-usersingle wt-servicesingle-holder">
						<div class="wt-servicesingle">
							<?php do_action('workreap_service_print_featured', $post->ID); ?>
							<div class="wt-servicesingle-title">
								<?php if( !empty( $db_project_cat ) ){?>
									<div class="wt-service-tag">
										<?php foreach ( $db_project_cat as $cat ) {?>
											<a href="<?php echo get_term_link($cat);?>"><?php echo esc_html($cat->name);?></a>
										<?php }?>
									</div>
								<?php  }?>
								<div class="wt-title">
									<h2><?php the_title();?></h2>
								</div>
								<ul class="wt-userlisting-breadcrumb">
									<?php do_action('workreap_service_get_reviews',$post->ID,'v1');?>
									<?php do_action('workreap_save_services_html',$post->ID);?>
								</ul>
							</div>
							
							<?php if( !empty( $db_docs ) || !empty( $db_videos ) ) {?>
								<div class="wt-freelancers-info">
									<div id="wt-servicesslider-<?php echo intval( $flag );?>" class="wt-servicesslider <?php echo esc_attr( $owl_class );?>">
									<?php
										$full_images	= '';
										foreach( $db_docs as $key => $doc ){
											$attachment_id	= !empty( $doc['attachment_id'] ) ? $doc['attachment_id'] : '';
											$thumbnail      = workreap_prepare_image_source($attachment_id, $width, $height);
											$full_thumbnail = workreap_prepare_image_source($attachment_id, $full_width, $full_height);
											if ( strpos( $thumbnail,'media/default.png' ) === false ) {
												if( !empty( $full_thumbnail ) && !empty( $thumbnail ) ) {
													$full_images	.= '<div class="item"><figure><img src="'.esc_url($thumbnail).'" alt="'.get_the_title().'"></figure></div>'; ?>
													<figure class="item">
														<img src="<?php echo esc_url( $full_thumbnail );?>" alt="<?php the_title();?>" class="item">
													</figure>
											<?php } } ?>
									<?php } ?>
									<?php if( !empty( $db_videos ) ){
										foreach( $db_videos as $key => $vid ){
											$full_images	.= '<div class="item wt-item-video"><figure>'.apply_filters('workreap_service_video_img_fallback',workreap_service_video_img()).'</div>';
										?>
										<div class="item item-video">
											<a class="owl-video" href="<?php echo esc_attr( $vid );?>"></a>
										</div>
									<?php }}?>
									</div>
									<?php if( !empty( $full_images ) && $slider_images > 1 ){?>
										<div id="wt-servicesgallery-<?php echo intval( $flag );?>" class="wt-servicesgallery <?php echo esc_attr( $owl_class );?>">
											<?php echo do_shortcode($full_images);?>
										</div>
									<?php } ?>
								</div>
							<?php }?>
							<div class="wt-service-details">
								<div class="wt-description">
									<?php the_content();?>
								</div>
							</div>
						</div>
						<?php get_template_part('directory/front-end/templates/freelancer/single/service_feedback'); ?>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 col-xl-4 float-left">
					<aside id="wt-sidebar" class="wt-sidebar">
						<?php if(!empty( $linked_profile) ) {?>
							<div class="wt-userrating wt-userratingvtwo">
								<?php if( !empty( $db_price ) ){?>
									<div class="wt-ratingtitle">
										<h3><?php echo workreap_price_format($db_price);?></h3>
										<span><?php esc_html_e('Starting From','workreap');?></span>
									</div>
								<?php } ?>
								<div class="wt-rating-info">
									<ul class="wt-service-info">
										<?php if( !empty( $db_delivery_time[0] ) ) {?>
											<li>
												<span><i class="fa fa-calendar-check-o iconcolor1"></i>
													<strong><?php echo esc_html($db_delivery_time[0]->name);?></strong> &nbsp;<?php esc_html_e('Delivery Time','workreap');?></span>
											</li>
										<?php }?>
										<li>
											<span><i class="fa fa-search iconcolor2"></i><strong><?php echo intval( $service_views );?></strong>&nbsp;<?php esc_html_e('Views','workreap');?></span>
										</li>
										<li>
											<span><i class="fa fa-shopping-basket iconcolor3"></i><strong><?php echo intval( $completed_services );?></strong>&nbsp;<?php esc_html_e('Sales','workreap');?></span>
										</li>
										<?php if( !empty( $db_downloadable ) ) {?>
											<li>
												<span><i class="fa fa-download iconcolor5"></i><strong><?php esc_html_e('Downloadable','workreap');?></strong></span>
											</li>
										<?php }?>
										<?php if( !empty( $db_response_time[0] ) ) {?>
											<li>
												<span><i class="fa fa-clock-o iconcolor4"></i><strong><?php echo esc_html($db_response_time[0]->name);?></strong>&nbsp;<?php esc_html_e('Response Time','workreap');?></span>
											</li>
										<?php }?>
									</ul>
								</div>
								<?php if( !empty( $db_addons ) ){ ?>
								<div class="wt-addonstwo">
									<div class="wt-addonsservices wt-tabsinfo">
										<div class="wt-widgettitle">
											<h2><?php esc_html_e( 'Addons Services','workreap');?></h2>
										</div>
										<div class="wt-addonservices-content addon-list-items">
											<ul>
											<?php 
												foreach( $db_addons as $addon ) { 
													$db_price			= 0;
													if (function_exists('fw_get_db_post_option')) {
														$db_price   = fw_get_db_post_option($addon,'price');
													}
													$addon_title	= get_the_title( $addon );
													$addon_excerpt	= get_the_excerpt( $addon );
													if( !empty( $addon_title ) ){
													?>
													<li>
														<div class="wt-checkbox">
															<input data-service-id="<?php echo intval($post->ID);?>" data-addons-id="<?php echo intval($addon);?>" id="rate<?php echo intval($addon);?>" type="checkbox" name="addons" class="wt-addons-checkbox" value="<?php echo intval($addon);?>" >
															<label for="rate<?php echo intval($addon);?>">
																<?php if( !empty( $addon_title ) ){?>
																	<h3><?php echo esc_html( $addon_title );?></h3>
																<?php } ?>

																<?php if( !empty( $db_price ) ){?>
																	<strong><?php workreap_price_format($db_price);?></strong>
																<?php } ?>
															</label>
															<?php if( !empty( $addon_excerpt ) ){?>
																<p><?php echo esc_html( $addon_excerpt );?></p>
															<?php } ?>
														</div>
													</li>
													<?php } ?>
												<?php } ?>
											</ul>
										</div>
									</div>
								</div>
								<?php } ?>
								<div class="wt-ratingcontent">
									<p><em>*</em> <?php esc_html_e('This price is not as accurate as mentioned above It vary as per work nature','workreap');?></p>
									<a href="javascript:;" class="hire-service wt-btn" data-addons="" data-id="<?php echo intval( $post->ID );?>">
										<?php esc_html_e('Buy Now','workreap');?>
									</a>
								</div>	
							</div>
							<div class="wt-widget wt-user-service">
								<div class="wt-companysdetails">
									<?php if( !empty( $freelancer_banner ) ){?>
										<figure class="wt-companysimg">
											<img src="<?php echo esc_url( $freelancer_banner );?>" alt="<?php esc_attr_e('user banner','workreap');?>">
										</figure>
									<?php }?>
									<div class="wt-companysinfo">
									<?php if( !empty( $freelancer_avatar ) ){?>
										<figure><img src="<?php echo esc_url( $freelancer_avatar );?>" alt="<?php esc_attr_e('profile image','workreap');?>"></figure>
									<?php } ?>
										<div class="wt-userprofile">
											<div class="wt-title">
												<h3><?php do_action('workreap_get_verification_check',$linked_profile,$freelancer_title);?></h3>
												<?php esc_html_e('Member since','workreap');?>&nbsp;<?php echo get_the_date( get_option('date_format'), $linked_profile );?>
												<a href="<?php echo esc_url($user_link);?>" class="wt-btn"><?php esc_html_e('View Profile','workreap');?></a>

											</div>
										</div>
									</div>
								</div>
							</div>
							<?php  do_action('workreap_get_qr_code','service',intval( $post->ID ));?>

							<?php 
								if (function_exists('workreap_prepare_project_social_sharing')) {
									workreap_prepare_project_social_sharing(false, esc_html__('Share This Service','workreap'), 'true', '', $freelancer_avatar);
								}
							?>
						<?php }?>
						<?php do_action('workreap_report_post_type_form',$post->ID,'service');?>
					</aside>
				</div>
			</div>
		</div>
	</div>
	<?php 
		if ( is_user_logged_in() && $author_id != $current_user->ID ) {
			if( apply_filters('workreap_is_feature_allowed', 'wt_pr_chat', $author_id) === true ){
				if( apply_filters('workreap_chat_window_floating', 'disable') === 'enable' ){
					get_template_part('directory/front-end/templates/messages');
				}
			}
		} 
	?>
	
<?php
	if( $slider_images >= 1 ){
		$script	= "
		function load_service_slider(){
			var sync1 = jQuery('#wt-servicesslider-".esc_js($flag)."');
			var sync2 = jQuery('#wt-servicesgallery-".esc_js($flag)."');
			var slidesPerPage = 3;
			var syncedSecondary = true;
			sync1.owlCarousel({
				items : 1,
				loop: false,
				autoHeight:true,
				nav: false,
				rtl: ".workreap_owl_rtl_check().",
				dots: false,
				autoplay: false,
				slideSpeed : 2000,
				video:true,
				lazyLoad: false,
				videoHeight: 370,
				videoWidth: 670,
				navClass: ['wt-prev', 'wt-next'],
				navContainerClass: 'wt-search-slider-nav',
				navText: ['<span class=\"lnr lnr-chevron-left\"></span>', '<span class=\"lnr lnr-chevron-right\"></span>'],
				responsiveRefreshRate : 200,
			}).on('changed.owl.carousel', syncPosition);
			sync2.on('initialized.owl.carousel', function () {
				sync2.find('.owl-item').eq(0).addClass('current');
			})
			.owlCarousel({
				items:8,
				dots: false,
				nav: false,
				margin:10,
				smartSpeed: 200,
				rtl: ".workreap_owl_rtl_check().",
				slideSpeed : 500,
				slideBy: slidesPerPage,
				responsiveClass:true,
				responsive:{
					0:{items:3,},
					680:{items:6,},
					992:{items:8,}
				},
				responsiveRefreshRate : 100,
			}).on('changed.owl.carousel', syncPosition2);
			function syncPosition(el) {
				var count = el.item.count-1;
				var current = Math.round(el.item.index - (el.item.count/2) - .5);
				if(current < 0) {
					current = count;
				}
				if(current > count) {
					current = 0;
				}
				sync2
				.find('.owl-item')
				.removeClass('current')
				.eq(current)
				.addClass('current')
				var onscreen = sync2.find('.owl-item.active').length - 1;
				var start = sync2.find('.owl-item.active').first().index();
				var end = sync2.find('.owl-item.active').last().index();
				if (current > end) {
					sync2.data('owl.carousel').to(current, 100, true);
				}
				if (current < start) {
					sync2.data('owl.carousel').to(current - onscreen, 100, true);
				}
			}
			function syncPosition2(el) {
				if(syncedSecondary) {
					var number = el.item.index;
					sync1.data('owl.carousel').to(number, 100, true);
				}
			}
			sync2.on('click', '.owl-item', function(e){
				e.preventDefault();
				var number = jQuery(this).index();
				sync1.data('owl.carousel').to(number, 300, true);
			});
			
			sync1.trigger('refresh.owl.carousel');
			sync2.trigger('refresh.owl.carousel');
		}
		load_service_slider();
		
		setTimeout(function(){ load_service_slider(); }, 1000);
		";
		
		wp_add_inline_script( 'workreap-callbacks', $script, 'after' );
	}
} 
} else { ?>
	<div class="container">
	  <div class="wt-haslayout page-data">
		<?php  Workreap_Prepare_Notification::workreap_warning(esc_html__('Restricted Access', 'workreap'), esc_html__('You have not any privilege to view this page.', 'workreap'));?>
	  </div>
	</div>
<?php
}
get_footer(); 
