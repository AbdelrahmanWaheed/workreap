<?php
/**
 *
 * Template Name: Full width template
 *
 * @package   Workreap
 * @author    amentotech
 * @link      https://themeforest.net/user/amentotech/portfolio
 * @since 1.0
 */
get_header();
$sidebar_type  = 'full';
$section_width = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
if (function_exists('fw_ext_sidebars_get_current_position')) {
    $current_position = fw_ext_sidebars_get_current_position();
    if ($current_position !== 'full' && ( $current_position == 'left' || $current_position == 'right' )) {
        $sidebar_type  = $current_position;
        $section_width = 'col-xs-12 col-sm-12 col-md-7 col-lg-7 col-xl-8';
    }
}

$height = 400;
$width  = 1140;

if (isset($sidebar_type) && ( $sidebar_type == 'full' )) {
    while (have_posts()) : the_post();
  		global $post;
        ?>
        <div class="container">
            <div class="wt-haslayout wt-haslayout page-data">
                <?php
					do_action('workreap_prepare_section_wrapper_before');
					$thumbnail = workreap_prepare_thumbnail($post->ID , $width , $height);
					if( $thumbnail ){?>
						<img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" >
						<?php
					}

					the_content();
					
					wp_link_pages( array(
										'before'      => '<div class="wt-paginationvtwo"><nav class="wt-pagination"><ul>',
										'after'       => '</ul></nav></div>',
									) );

					// If comments are open or we have at least one comment, load up the comment template.
					if (comments_open() || get_comments_number()) :
						comments_template();
					endif;
					do_action('workreap_prepare_section_wrapper_after');
                ?>
            </div>
        </div>
        <?php
    endwhile;
} else {
    if (isset($sidebar_type) && $sidebar_type == 'right') {
        $aside_class   = 'pull-right';
        $content_class = 'pull-left';
    } else {
        $aside_class   = 'pull-left';
        $content_class = 'pull-right';
    }
    ?> 
    <div class="container">
        <div class="wt-haslayout page-data">
            <div class="row">
            	<?php do_action('workreap_prepare_section_wrapper_before'); ?>
				<aside class="col-xs-12 col-sm-12 col-md-5 col-lg-5 col-xl-4 sidebar-section wt-sidebar <?php echo sanitize_html_class($aside_class); ?>" id="wt-sidebar">
					<div class="wt-sidebar page-dynamic-sidebar">
						<div class="mmobile-floating-apply">
							<span><?php esc_html_e('Open Sidebar', 'workreap'); ?></span>
							<i class="fa fa-filter"></i>
						</div>
						<div class="floating-mobile-filter">
							<div class="wt-filter-scroll wt-collapse-filter">
								<a class="wt-mobile-close" href="javascript:;"><i class="lnr lnr-cross"></i></a>
								<?php echo fw_ext_sidebars_show('blue'); ?>
							</div>
						</div>
					</div>
				</aside>
				<div class="<?php echo esc_attr($section_width); ?> <?php echo sanitize_html_class($content_class); ?>  page-section twocolumn-page-section">
					<?php
						while (have_posts()) : the_post();
							global $post;
							$thumbnail = workreap_prepare_thumbnail($post->ID , $width , $height);
							if( $thumbnail ){?>
								<img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" >
							<?php }

							the_content();
							// If comments are open or we have at least one comment, load up the comment template.
							if (comments_open() || get_comments_number()) :
								comments_template();
							endif;
						endwhile;
					?>

				</div>
				
           		<?php do_action('workreap_prepare_section_wrapper_after'); ?>
            </div>
        </div>
    </div>
<?php } ?>
<?php get_footer();