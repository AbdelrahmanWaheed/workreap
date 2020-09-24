<?php
/**
 *
 * Service three column layout
 *
 * @package   Workreap
 * @author    Amentotech
 * @link      http://amentotech.com/
 * @since 1.0
 */
global $paged, $query_args, $show_posts,$flag;
$service_data = new WP_Query($query_args); 
$total_posts  = $service_data->found_posts;

$width			= 352;
$height			= 200;
?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 float-left">
	<form method="get" name="serach-projects" action="#" class="four-column-filter">
		<?php do_action('workreap_keyword_search','wt-service-grids'); ?>
		<div class="dynamic-column-holder wt-haslayout">
			<?php do_action('workreap_print_categories'); ?>
			<?php if( apply_filters('workreap_filter_settings','services','locations') === 'enable' ){do_action('workreap_print_locations');} ?>
			<?php if( apply_filters('workreap_filter_settings','services','dilivery') === 'enable' ){do_action('workreap_print_service_duration');} ?>
			<?php if( apply_filters('workreap_filter_settings','services','response') === 'enable' ){do_action('workreap_print_response_time');}?>
			<?php if( apply_filters('workreap_filter_settings','services','languages') === 'enable' ){do_action('workreap_print_languages');} ?>
			<?php if( apply_filters('workreap_filter_settings','services','price') === 'enable' ){do_action('workreap_print_price_range');} ?>
			<div class="wt-widget wt-effectiveholder">
				<div class="wt-widgetcontent">
					<div class="wt-applyfilters">
						<span><?php esc_html_e('Click “Apply Filter” to apply latest changes made by you.', 'workreap'); ?></span>
						<input type="submit" class="wt-btn" value="<?php esc_html_e('Apply Filters', 'workreap'); ?>">
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 float-left">
	<div class="row">
		<?php if ( $service_data->have_posts()) {?>
			<div class="wt-freelancers-holder three-column-holder">
				<?php 
					while ($service_data->have_posts()) { 
						$service_data->the_post();
						global $post;

						$author_id 				= get_the_author_meta( 'ID' );  
						$linked_profile 		= workreap_get_linked_profile_id($author_id);	
						$service_url			= get_the_permalink();
						$db_docs			= array();
						$delivery_time		= '';
						$order_details		= '';

						if (function_exists('fw_get_db_post_option')) {
							$db_docs   			= fw_get_db_post_option($post->ID,'docs');
							$delivery_time		= fw_get_db_post_option($post->ID,'delivery_time');
							$order_details   	= fw_get_db_post_option($post->ID,'order_details');

						}

						if( count( $db_docs )>1 ) {
							$class	= 'wt-freelancers-services-'.intval( $flag ).' owl-carousel';
						} else {
							$class	= '';
						}

						if( empty($db_docs) ) {
							$empty_image_class	= 'wt-empty-service-image';
							$is_featured		= workreap_service_print_featured( $post->ID, 'yes');
							$is_featured    	= !empty( $is_featured ) ? 'wt-featured-service' : '';
						} else {
							$empty_image_class	= '';
							$is_featured		= '';
						}
					?>
					<div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 float-left wt-services-grid">
						<div class="wt-freelancers-info <?php echo esc_attr( $empty_image_class );?> <?php echo esc_attr( $is_featured );?>">
							<?php if( !empty( $db_docs ) ) {?>
								<div class="wt-freelancers <?php echo esc_attr( $class );?>">
									<?php
										foreach( $db_docs as $key => $doc ){
											$attachment_id	= !empty( $doc['attachment_id'] ) ? $doc['attachment_id'] : '';
											$thumbnail      = workreap_prepare_image_source($attachment_id, $width, $height);
											if (strpos($thumbnail,'media/default.png') === false) {?>
											<figure class="item">
												<a href="<?php echo esc_url( $service_url );?>">
													<img src="<?php echo esc_url($thumbnail);?>" alt="<?php esc_attr_e('Service ','workreap');?>" class="item">
												</a>
											</figure>
									<?php } }?>
								</div>
							<?php } ?>
							<?php do_action('workreap_service_print_featured', $post->ID); ?>
							<?php do_action('workreap_service_shortdescription', $post->ID,$linked_profile); ?>
						</div>
					</div>
			<?php } wp_reset_postdata();?>
		</div>
		<?php } else{?>
				<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 float-left">
					<?php do_action('workreap_empty_records_html','wt-empty-projects',esc_html__( 'No services found.', 'workreap' ));?>
				</div>
		<?php }?>
		<?php if (!empty($total_posts) && $total_posts > $show_posts) {?>
			<div class="col-12 col-sm-12 col-md-12 col-lg-12 wp-pagination wt-service-pagination float-left">
				<?php workreap_prepare_pagination($total_posts, $show_posts); ?>
			</div>
		<?php } ?>
	</div>
</div>