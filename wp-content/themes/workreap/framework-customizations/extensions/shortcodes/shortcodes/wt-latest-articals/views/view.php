<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$title       = !empty($atts['title']) ? $atts['title'] : '';
$sub_title   = !empty($atts['sub_title']) ? $atts['sub_title'] : '';
$desc  	     = !empty($atts['description']) ? $atts['description'] : '';
$posts_ids   = !empty($atts['posts']) ? $atts['posts'] : array();

$args = array();
if( !empty( $posts_ids ) ){
	$args['post__in'] = $posts_ids;
}

$posts 	= get_posts($args);
$height = intval(200);
$width  = intval(352);
$cat_link	= '';
?>

<div class="wt-sc-latest-articals wt-latearticles">
	<div class="row justify-content-md-center">
		<?php if( !empty( $title ) || !empty( $sub_title ) || !empty( $desc ) ) {?>
			<div class="col-lg-8">
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
		<?php if( !empty( $posts ) ) {?>
			<div class="wt-articlesholder">
				<?php 
					foreach( $posts as $post ){
						$post_thumbnail_id 	= get_post_thumbnail_id($post->ID);
						$thumbnail         	= workreap_prepare_thumbnail_from_id($post->ID, $width, $height);
						$post_link			= get_permalink($post->ID);
						$enable_author     	= '';
						if (function_exists('fw_get_db_post_option')) {
							$enable_author = fw_get_db_post_option($post->ID, 'enable_author', true);
						}

						$thumb_meta = array();
						if (!empty($post_thumbnail_id)) {
							$thumb_meta = workreap_get_image_metadata($post_thumbnail_id);
						}
						
						$image_title = !empty($thumb_meta['title']) ? $thumb_meta['title'] : 'thumbnail';
						$image_alt   = !empty($thumb_meta['alt']) ? $thumb_meta['alt'] : $image_title;
						
						$post_categories 	= wp_get_post_categories( $post->ID );
						$post_view_count    = get_post_meta($post->ID, 'article_views', true);
						
						$num_comments = get_comments_number($post->ID);
						if ( comments_open($post->ID) ) {
							if ( $num_comments == 0 ) {
								$comments = esc_html__('0 Comments','workreap');
							} elseif ( $num_comments > 1 ) {
								$comments = $num_comments . esc_html__(' Comments','workreap');
							} else {
								$comments = esc_html__('1 Comment','workreap');
							}
							
							$write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
						} else {
							$write_comments =  esc_html__('Comments are off','workreap');
						}
						?>
						<div class="col-12 col-sm-12 col-md-6 col-lg-4 float-left wt-verticaltop">
							<div class="wt-articles">
								<?php if (!empty($thumbnail)) { ?>
									<figure class="wt-articleimg">
										<a href="<?php echo esc_url($post_link); ?>">
											<img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($image_alt); ?>">
										</a>
									</figure>
								<?php } ?>
								<div class="wt-articlecontents">
									<div class="wt-title">
										<?php if( !empty( $post_categories ) ){?>
											<div class="wt-titletags">
												<?php 
													foreach( $post_categories as $cat ) {
														$category = get_category( $cat );
														if( !empty( $category->name ) ) {
															$cat_link	= get_category_link($category);?>
														<a href="<?php echo esc_url( $cat_link );?>" class="wt-articleby"><?php echo esc_html( $category->name ); ?></a>
													<?php }?>
												<?php }?>
											</div>
										<?php }?>
										<h3><a href="<?php echo esc_url($post_link); ?>"><?php workreap_get_post_title($post->ID); ?></a></h3>
										<span class="wt-datetime"><i class="ti-calendar"></i><?php echo date(get_option('date_format'), strtotime(get_the_date('Y-m-d', $post->ID))); ?></span>
									</div>
									<ul class="wt-moreoptions">
										<li><a href="<?php echo esc_url( get_comments_link( $post->ID ) ); ?> "><i class="ti-comment"></i><?php echo  do_shortcode($write_comments); ?></a></li>
										<li><i class="ti-eye"></i> <?php echo intval($post_view_count); ?>&nbsp;<?php esc_html_e('View(s)','workreap');?></li>
									</ul>
								</div>
							</div>
						</div>
				<?php }?>
				<?php wp_reset_postdata(); ?>
			</div>
		<?php }?>
	</div>
</div>