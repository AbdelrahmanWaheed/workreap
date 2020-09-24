<?php
/**
 *
 * Template Name: Search services
 *
 * @package   Workreap
 * @author    Amentotech
 * @link      http://amentotech.com/
 * @since 1.0
 */
get_header();
global $paged,$query_args,$show_posts,$flag;

if( apply_filters('workreap_system_access','service_base') === true ){
	$pg_page    = get_query_var('page') ? get_query_var('page') : 1; //rewrite the global var
	$pg_paged   = get_query_var('paged') ? get_query_var('paged') : 1; //rewrite the global var

	//paged works on single pages, page - works on homepage
	$paged 		= max($pg_page, $pg_paged);
	
	if(function_exists('fw_get_db_settings_option')){
		$services_per_page 	= fw_get_db_settings_option('services_per_page');
	}
	
	$show_posts = !empty( $services_per_page ) ? $services_per_page : get_option('posts_per_page');
	
	//Search parameters
	$keyword 		= !empty( $_GET['keyword']) ? $_GET['keyword'] : '';
	$categories 	= !empty( $_GET['category']) ? $_GET['category'] : array();
	$locations 	 	= !empty( $_GET['location']) ? $_GET['location'] : array();
	$delivery 		= !empty( $_GET['service_duration'] ) ? $_GET['service_duration'] : array();
	$response_time	= !empty( $_GET['response_time'] ) ? $_GET['response_time'] : array();
	$english_level  = !empty( $_GET['english_level'] ) ? $_GET['english_level'] : array();
	$languages 		= !empty( $_GET['language']) ? $_GET['language'] : array();
	$minprice 		= !empty($_GET['minprice']) ? intval($_GET['minprice'] ): 0;
	$maxprice 		= !empty($_GET['maxprice']) ? intval($_GET['maxprice']) : '';

	$tax_query_args  = array();
	$meta_query_args = array();

	//Languages
	if ( !empty($languages[0]) && is_array($languages) ) {   
		$query_relation = array('relation' => 'OR',);
		$lang_args  	= array();

		foreach( $languages as $key => $lang ){
			$lang_args[] = array(
					'taxonomy' => 'languages',
					'field'    => 'slug',
					'terms'    => $lang,
				);
		}

		$tax_query_args[] = array_merge($query_relation, $lang_args);   
	}

	//Delivery
	if ( !empty($delivery[0]) && is_array($delivery) ) {   
		$query_relation = array('relation' => 'OR',);
		$delv_args  	= array();

		foreach( $delivery as $key => $del ){
			$delv_args[] = array(
					'taxonomy' => 'delivery',
					'field'    => 'slug',
					'terms'    => $del,
				);
		}

		$tax_query_args[] = array_merge($query_relation, $delv_args);   
	}

	//Delivery
	if ( !empty($response_time[0]) && is_array($response_time) ) {   
		$query_relation = array('relation' => 'OR',);
		$reponse_args  	= array();

		foreach( $response_time as $key => $res ){
			$reponse_args[] = array(
					'taxonomy' => 'response_time',
					'field'    => 'slug',
					'terms'    => $res,
				);
		}

		$tax_query_args[] = array_merge($query_relation, $reponse_args);   
	}

	//Categories
	if ( !empty($categories[0]) && is_array($categories) ) {   
		$query_relation = array('relation' => 'OR',);
		$category_args  = array();

		foreach( $categories as $key => $cat ){
			$category_args[] = array(
					'taxonomy' => 'project_cat',
					'field'    => 'slug',
					'terms'    => $cat,
				);
		}

		$tax_query_args[] = array_merge($query_relation, $category_args);
	}

	//Locations
	if ( !empty($locations[0]) && is_array($locations) ) {    
		$query_relation = array('relation' => 'OR',);
		$location_args  = array();

		foreach( $locations as $key => $loc ){
			$location_args[] = array(
					'taxonomy' => 'locations',
					'field'    => 'slug',
					'terms'    => $loc,
				);
		}

		$tax_query_args[] = array_merge($query_relation, $location_args);
	}

	if (!empty($maxprice)) {
		$price_range 		= array($minprice, $maxprice);
		$meta_query_args[]  = array(
			'key' 			=> '_price',
			'value' 		=> $price_range,
			'type'    		=> 'NUMERIC',
			'compare' 		=> 'BETWEEN'
		);
	}

	//Main Query
	$query_args = array(
		'posts_per_page' 	  => $show_posts,
		'post_type' 	 	  => 'micro-services',
		'paged' 		 	  => $paged,
		'ignore_sticky_posts' => 1
	);

	//keyword search
	if( !empty($keyword) ){
		$query_args['s']	=  $keyword;
	}

	$query_args['orderby']  	= 'ID';
	$query_args['order'] 		= 'DESC';
	
	//order by pro member
	$query_args['meta_key'] = '_featured_service_string';
	$query_args['orderby']	 = array( 
		'meta_value' 	=> 'DESC', 
		'ID'      		=> 'DESC'
	); 
	
	//Taxonomy Query
	if ( !empty( $tax_query_args ) ) {
		$query_relation = array('relation' => 'AND',);
		$query_args['tax_query'] = array_merge($query_relation, $tax_query_args);
	}

	//Meta Query
	if (!empty($meta_query_args)) {
		$query_relation 		= array('relation' => 'AND',);
		$meta_query_args 		= array_merge($query_relation, $meta_query_args);
		$query_args['meta_query'] = $meta_query_args;
	}

	$flag 			= rand(9999, 999999);
	$default_view = 'two';
	
	if (function_exists('fw_get_db_post_option')) {
		$services_layout = fw_get_db_settings_option('services_layout');
	}
	
	$services_layout	= !empty( $services_layout ) ? $services_layout : 'two';
	?>
	<?php if( have_posts() & !is_tax() ) {?>
	<div class="wt-haslayout wt-haslayout page-data">
		<?php 
			while ( have_posts() ) : the_post();
				the_content();
				wp_link_pages( array(
									'before'      => '<div class="wt-paginationvtwo"><nav class="wt-pagination"><ul>',
									'after'       => '</ul></nav></div>',
								) );
			endwhile;
			wp_reset_postdata();
		?>
	</div>
	<?php }?>

	<div class="search-result-template wt-haslayout">
		<div class="wt-haslayout wt-job-search">
			<div class="container">
				<div class="row">
					<div id="wt-twocolumns" class="wt-twocolumns wt-haslayout">
						<?php get_template_part('directory/front-end/services-layout/services', $services_layout.'-column');?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	$script	= "jQuery('.wt-freelancers-services-".esc_js($flag)."').owlCarousel({
				items: 1,
				loop:true,
				nav:true,
				margin: 0,
				autoplay:false,
				rtl: ".workreap_owl_rtl_check().",
				navClass: ['wt-prev', 'wt-next'],
				navContainerClass: 'wt-search-slider-nav',
				navText: ['<span class=\"lnr lnr-chevron-left\"></span>', '<span class=\"lnr lnr-chevron-right\"></span>'],
			});
			
			";
	wp_add_inline_script( 'workreap-callbacks', $script, 'after' );
	
	$masonry	= " jQuery('.wt-freelancers-holder').masonry({itemSelector: '.wt-services-grid'});
					setTimeout(function(){ 
						jQuery('.wt-freelancers-holder').masonry({itemSelector: '.wt-services-grid'});
					}, 2000);";
	wp_add_inline_script( 'jquery-masonry', $masonry, 'after' );
	
}else { ?>
	<div class="container">
	  <div class="wt-haslayout page-data">
		<?php  Workreap_Prepare_Notification::workreap_warning(esc_html__('Restricted Access', 'workreap'), esc_html__('You have not any privilege to view this page.', 'workreap'));?>
	  </div>
	</div>
<?php
	
}
get_footer();
