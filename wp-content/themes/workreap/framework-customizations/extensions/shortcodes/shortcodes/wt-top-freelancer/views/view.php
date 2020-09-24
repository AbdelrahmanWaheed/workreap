<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$title       = !empty($atts['title']) ? $atts['title'] : '';
$sub_title   = !empty($atts['sub_title']) ? $atts['sub_title'] : '';
$desc  	     = !empty($atts['description']) ? $atts['description'] : '';
$listing_type  	     = !empty($atts['listing_type']) ? $atts['listing_type'] : '';
$listing_numbers  	 = !empty($atts['listing_numbers']) ? $atts['listing_numbers'] : 4;
$freelancers_ids  	 = !empty($atts['freelancers']) ? $atts['freelancers'] : array();


$args = array(
				'post_type'		=> 'freelancers',
				'post_status'   => 'publish',
			);

$args['posts_per_page']	= $listing_numbers;

$meta_query			= array();
$meta_query[]		= array(
							'key'   => '_profile_blocked',
							'value' => 'off');
$meta_query[]		= array(
							'key'   => '_is_verified',
							'value' => 'yes');

if( !empty( $freelancers_ids ) ){
	$args['post__in']	= $freelancers_ids;
} else if( !empty($listing_type) ) {
	if( $listing_type === 'featured' ){
		
		$meta_query[]		= array(
			'key'   => '_featured_timestamp',
			'value' => 1);
		
	} else if( $listing_type === 'DESC' ){
		$args['order']			= 'DESC';
	} else if( $listing_type === 'ASC' ){
		$args['order']			= 'ASC';
	}
	
	$args['orderby']		= 'ID';
}

$args['meta_query']		= $meta_query;
$freelancers = get_posts($args);
?>
<div class="wt-sc-top-freelancers wt-latearticles">
	<div class="row justify-content-md-center">
		<?php if( !empty( $title ) || !empty( $sub_title ) || !empty( $desc ) ) {?>
			<div class="col-12 col-sm-12 col-md-8 push-md-2 col-lg-8 push-lg-2">
				<div class="wt-sectionhead wt-textcenter">
					<?php if( !empty( $title ) || !empty( $sub_title ) ) {?>
						<div class="wt-sectiontitle">
							<?php if( !empty( $title ) ) {?><h2><?php echo esc_html( $title );?></h2><?php }?>
							<?php if( !empty( $sub_title ) ) {?><span><?php echo esc_html( $sub_title );?></span><?php }?>
						</div>
					<?php } ?>
					<?php if( !empty( $desc ) ) {?>
						<div class="wt-description"><?php echo do_shortcode( $desc );?></div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
		<?php if( !empty( $freelancers ) ) {?>
			<div class="wt-topfreelancers">
				<?php 
					foreach( $freelancers as $freelancer ){
						$author_id 				= workreap_get_linked_profile_id($freelancer->ID, 'post');
						$freelancer_title 		= esc_html( get_the_title( $freelancer->ID ));
						$tagline				= workreap_get_tagline($freelancer->ID);
						$freelancer_avatar = apply_filters(
							'workreap_freelancer_avatar_fallback', workreap_get_freelancer_avatar(array('width' => 225, 'height' => 225), $freelancer->ID), array('width' => 225, 'height' => 225) 
						);

						$class	= apply_filters('workreap_featured_freelancer_tag',$author_id,'yes');
						$class	= !empty($class) ? $class : '';
						
						if (function_exists('fw_get_db_post_option')) {
							$perhour_rate	= fw_get_db_post_option($freelancer->ID, '_perhour_rate', true);	
						} else {
							$perhour_rate	= "";
						}
						
						$reviews_data 	= get_post_meta( $freelancer->ID , 'review_data');
						$reviews_rate	= !empty( $reviews_data[0]['wt_average_rating'] ) ? floatval( $reviews_data[0]['wt_average_rating'] ) : 0 ;
						$total_rating	= !empty( $reviews_data[0]['wt_total_rating'] ) ? intval( $reviews_data[0]['wt_total_rating'] ) : 0 ;
						$round_rate 		= number_format((float) $reviews_rate, 1);
						$rating_average		= ( $round_rate / 5 )*100;?>
						
						<div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 float-left wt-verticaltop">
							<div class="wt-freelanceritems">
								<div class="wt-userlistinghold <?php echo esc_attr($class);?>">
									<?php do_action('workreap_featured_freelancer_tag',$author_id);?>
									<div class="wt-userlistingcontent">
										<figure>
											<img src="<?php echo esc_url($freelancer_avatar); ?>" alt="<?php echo esc_attr($tagline); ?>">
											<?php echo do_action('workreap_print_user_status',$author_id);?>
										</figure>
										<div class="wt-contenthead">
											<div class="wt-title">
												<?php do_action( 'workreap_get_verification_check', $freelancer->ID, $freelancer_title ); ?>
												<h2><?php echo workreap_get_tagline($freelancer->ID); ?></h2>
											</div>
										</div>
										<div class="wt-viewjobholder">
											<ul>
												<?php if( !empty($perhour_rate) ){?>
													<li><span><i class="fa fa-money"></i><?php do_action('workreap_price_format',$perhour_rate);?>&nbsp;/&nbsp;<?php esc_html_e('hr','workreap');?></span></li>
												<?php }?>
												<?php do_action('workreap_print_location',$freelancer->ID);?>
												<li><?php do_action('workreap_save_freelancer_html',$freelancer->ID);?></li>
												<li>
													<a href="javascript:;" class="wt-freestars">
														<i class="fa fa-star"></i><?php echo esc_html( $round_rate );?>/<?php esc_html_e('5','workreap');?>&nbsp;<em>(<?php echo esc_html( $total_rating );?>&nbsp;<?php esc_html_e('Feedback','workreap');?>)</em>
													</a>
												</li>
											</ul>	
										</div>
									</div>
								</div>
							</div>
						</div>
				<?php }?>
			</div>
		<?php }?>
	</div>
</div>