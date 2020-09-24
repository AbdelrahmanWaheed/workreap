<?php
if (!defined('FW'))
    die('Forbidden');
/**
 * @var $atts
 */
global $paged;
$pg_page  = get_query_var('page') ? get_query_var('page') : 1; //rewrite the global var
$pg_paged = get_query_var('paged') ? get_query_var('paged') : 1; //rewrite the global var
//paged works on single pages, page - works on homepage
$paged = max($pg_page, $pg_paged);
if (isset($atts['get_method']['gadget']) && $atts['get_method']['gadget'] === 'by_posts' && !empty($atts['get_method']['by_posts']['posts'])) {
    $posts_in['post__in'] = !empty($atts['get_method']['by_posts']['posts']) ? $atts['get_method']['by_posts']['posts'] : array();
    $order 	    = 'DESC';
    $orderby    = 'ID';
    $show_posts = !empty($atts['get_mehtod']['by_posts']['show_posts']) ? $atts['get_mehtod']['by_posts']['show_posts'] : -1;
} else {
    $cat_sepration = array();
    $cat_sepration = $atts['get_method']['by_cats']['categories'];
    $order 		   = !empty($atts['get_method']['by_cats']['order']) ? $atts['get_method']['by_cats']['order'] : 'DESC';
    $orderby 	   = !empty($atts['get_method']['by_cats']['orderby']) ? $atts['get_method']['by_cats']['orderby'] : 'ID';
    $show_posts    = !empty($atts['get_method']['by_cats']['show_posts']) ? $atts['get_method']['by_cats']['show_posts'] : -1;

    if (!empty($cat_sepration)) {
        $slugs = array();
        foreach ($cat_sepration as $key => $value) {
            $term 	 = get_term($value, 'category');
            $slugs[] = $term->slug;
        }

        $filterable = $slugs;
        $tax_query['tax_query'] = array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'category',
                'terms' => $filterable,
                'field' => 'slug',
        ));
    }
}

$blog_view = !empty($atts['blog_view']) ? $atts['blog_view']['grid']['columns'] : '';

$col_class = '';
if(!empty($blog_view) && $blog_view === '2_cols'){
	$col_class  = "col-12 col-sm-12 col-md-6 col-lg-6";
	$img_width  = intval(540);
	$img_height = intval(240);
} elseif(!empty($blog_view) && $blog_view === '3_cols') {
	$col_class  = "col-12 col-sm-12 col-md-6 col-lg-4";
	$img_width  = intval(355);
	$img_height = intval(352);
} else {
	$col_class = "col-12 col-sm-12 col-md-6 col-lg-3";
	$img_width  = intval(355);
	$img_height = intval(352);
}	
//Main Query 
$query_args = array(
    'posts_per_page'      => $show_posts,
    'post_type' 	 	  => 'post',
    'paged'			 	  => $paged,
    'order' 		 	  => $order,
    'orderby' 		  	  => $orderby,
    'post_status' 		  => 'publish',
    'ignore_sticky_posts' => 1);

//By Categories
if (!empty($cat_sepration)) {
    $query_args = array_merge($query_args, $tax_query);
}
//By Posts 
if (!empty($posts_in)) {
    $query_args = array_merge($query_args, $posts_in);
}
$query 		= new WP_Query($query_args);
$count_post = $query->found_posts;
?>
<div class="wt-sc-articles wt-haslayout">
	<div class="row">
		<?php
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				global $post;
				$height = $img_height;
				$width  = $img_width;

				$post_thumbnail_id = get_post_thumbnail_id($post->ID);
				$thumbnail 		   = workreap_prepare_thumbnail($post->ID, $width, $height);
				$user_ID 		   = get_the_author_meta('ID');
				$enable_author     = '';
				if (function_exists('fw_get_db_post_option')) {
					$enable_author = fw_get_db_post_option($post->ID, 'enable_author', true);
				}

				$thumb_meta = array();
				if (!empty($post_thumbnail_id)) {
					$thumb_meta = workreap_get_image_metadata($post_thumbnail_id);
				}
				$image_title = !empty($thumb_meta['title']) ? $thumb_meta['title'] : 'no-name';
				$image_alt   = !empty($thumb_meta['alt']) ? $thumb_meta['alt'] : $image_title;
				?>
				<div class="<?php echo esc_attr($col_class); ?>">
					<div class="wt-article">
						<?php if (!empty($thumbnail)) { ?>
							<figure>
								<a href="<?php echo esc_url(get_permalink()); ?>">
									<img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($image_alt); ?>">
								</a>
							</figure>
						<?php } ?>
						<div class="wt-articlecontent">
							<div class="wt-title">
								<h2><?php workreap_get_post_title($post->ID); ?></h2>
							</div>
							<ul class="wt-postarticlemeta">
								<li><?php workreap_get_post_date($post->ID); ?></li>
								<?php if (!empty($enable_author) && $enable_author === 'enable') { ?>
									<li><?php workreap_get_post_author( $user_ID , 'linked', $post->ID ); ?></li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>
				<?php
			} wp_reset_postdata();
		}
		?>
		<?php if (isset($atts['show_pagination']) && $atts['show_pagination'] == 'yes' && $count_post > $show_posts ) : ?>
			<div class="col-12 col-sm-12 col-md-12 col-lg-12">
				<?php workreap_prepare_pagination($count_post, $show_posts); ?>
			</div>
		<?php endif; ?>
	</div>
</div>