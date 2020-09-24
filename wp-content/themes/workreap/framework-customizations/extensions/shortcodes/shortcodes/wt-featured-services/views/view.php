<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$title      = !empty( $atts['title'] ) ? $atts['title'] : '';
$sub_title  = !empty( $atts['sub_title'] ) ? $atts['sub_title'] : '';
$desc       = !empty( $atts['description'] ) ? $atts['description'] : '';
$btn_title  = !empty( $atts['btn_title'] ) ? $atts['btn_title'] : '';
$layout  	= !empty( $atts['layout'] ) ? $atts['layout'] : 'three';
$show_posts = !empty( $atts['show_posts'] ) ? $atts['show_posts'] : 6;
$tpl_page   = !empty( $atts['template_page'] ) ? $atts['template_page'] : array();
$page_link  = !empty( $tpl_page ) ? get_permalink((int) $tpl_page[0]) : '#';
$cat_id		= !empty( $atts['services'] ) ? $atts['services']  : '';

$width			= 352;
$height			= 200;
$flag 			= rand(9999, 999999);

$micro_services = array(
					'posts_per_page' 	=> $show_posts,
					'post_type' 	 	=> 'micro-services',
					'tax_query' => array(
						array (
							'taxonomy' 	=> 'project_cat',
							'field'		=> 'term_id',
							'terms'		=> $cat_id,
						)
					)
				);

///order by pro member
$micro_services['orderby']  	= 'ID';
$micro_services['order'] 		= 'DESC';
$micro_services['meta_key'] 	= '_featured_service_string';
$micro_services['orderby']	 	= array( 
	'meta_value' 	=> 'DESC', 
	'ID'      		=> 'DESC'
); 

$service_data = new WP_Query($micro_services); 

$column			= 4;
$columnClass	= 'three-column-holder';	

if( !empty( $layout ) && $layout === 'four'  ){
	$column			= 3;
	$columnClass	= 'four-column-holder';	
}
?>
<div class="wt-sc-micro-services wt-haslayout">
	<?php if( !empty( $title ) || !empty( $sub_title ) || !empty( $desc ) ) {?>
		<div class="row justify-content-md-center">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 push-lg-2">
				<div class="wt-sectionhead wt-textcenter wt-topservices-title">
					<div class="wt-sectiontitle">
						<?php if( !empty( $title ) ) { ?><h2><?php echo esc_html( $title );?></h2><?php } ?>
						<?php if( !empty( $sub_title ) ) { ?><span><?php echo esc_html( $sub_title);?></span><?php } ?>
					</div>
					<?php if( !empty( $desc ) ) { ?>
						<div class="wt-description">
							<p><?php echo do_shortcode( $desc ) ;?></p>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	<?php } ?>
	<div class="row">
		<div class="wt-freelancers-holder wt-freelancers-home <?php echo esc_attr($columnClass);?>">
			<?php 
			if ($service_data->have_posts()) {
				while( $service_data->have_posts() ) { 
					$service_data->the_post();
					global $post;

					$author_id 			= get_the_author_meta( 'ID' );  
					$linked_profile 	= workreap_get_linked_profile_id($author_id);	
					$service_url		= get_the_permalink();
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
				<div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-<?php echo intval($column);?> float-left wt-services-grid">
					<div class="wt-freelancers-info <?php echo esc_attr( $empty_image_class );?> <?php echo esc_attr( $is_featured );?>">
						<?php if( !empty( $db_docs ) ) {?>
							<div class="wt-freelancers <?php echo esc_attr( $class );?>">
								<?php
								foreach( $db_docs as $key => $doc ){
									$attachment_id	= !empty( $doc['attachment_id'] ) ? $doc['attachment_id'] : '';
									$thumbnail      = workreap_prepare_image_source($attachment_id, $width, $height);
									if (strpos($thumbnail,'media/default.png') === false) {
									?>
									<figure class="item">
										<a href="<?php echo esc_url( $service_url );?>">
											<img src="<?php echo esc_url($thumbnail);?>" alt="<?php esc_attr_e('Service ','workreap');?>" class="item">
										</a>
									</figure>
								<?php }} ?>
							</div>
						<?php } ?>
						<?php do_action('workreap_service_print_featured', $post->ID); ?>
						<?php do_action('workreap_service_shortdescription', $post->ID,$linked_profile); ?>
					</div>
				</div>
			<?php } wp_reset_postdata();?>
				
			<?php
				} else{
					do_action('workreap_empty_records_html','wt-empty-projects',esc_html__( 'No service found.', 'workreap' ));
				}
			?>
		</div>
		<?php if( !empty( $page_link ) && !empty( $btn_title ) ) {?>
			<div class="col-12 col-sm-12 col-md-12 col-lg-12 float-left">
				<div class="wt-btnarea btn-viewservices">
					<a href="<?php echo esc_url( $page_link );?>" class="wt-btn"><?php echo esc_html($btn_title);?></a>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
<?php
	$script	= "jQuery('.wt-freelancers-services-".esc_js($flag)."').owlCarousel({
				items: 1,
				rtl: ".workreap_owl_rtl_check().",
				loop:true,
				nav:true,
				margin: 0,
				autoplay:false,
				navClass: ['wt-prev', 'wt-next'],
				navContainerClass: 'wt-search-slider-nav',
				navText: ['<span class=\"lnr lnr-chevron-left\"></span>', '<span class=\"lnr lnr-chevron-right\"></span>'],
			});
			
			";
	wp_add_inline_script( 'workreap-callbacks', $script, 'after' );
	$masonry	= " jQuery('.wt-freelancers-holder').masonry({itemSelector: '.wt-services-grid'});
					setTimeout(function(){ 
						jQuery('.wt-freelancers-holder').masonry({itemSelector: '.wt-services-grid'});
					}, 3000);";
	wp_add_inline_script( 'jquery-masonry', $masonry, 'after' );