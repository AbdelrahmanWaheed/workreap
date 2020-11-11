<?php
/**
 *
 * Template Name: Search Project Categoties
 *
 * @package   Workreap
 * @author    Amentotech
 * @link      http://amentotech.com/
 * @since 1.0
 */
get_header();

$caregories = get_terms('project_cat', array (
	'hide_empty' => false,
));

global $current_user;
$search_page 	= workreap_get_search_page_uri('jobs');
$post_job_page  = Workreap_Profile_Menu::workreap_profile_menu_link('post_job', $current_user->ID, true);
$user_type 		= apply_filters('workreap_get_user_type', $current_user->ID);

the_post();
?>
<div class="wt-sc-explore-categories wt-haslayout">
	<div class="container">
		<?php if(get_post()->post_content != '') { ?>
			<div class="wt-description wt-textcenter">
				<?php the_content(); ?>
				<br>
			</div>
		<?php } ?>
		<div class="row justify-content-md-center">
			<?php if( !empty( $caregories )  ) { ?>
				<div class="wt-categoryexpl ">
					<?php foreach( $caregories as $category ) { 
						$icon          = array();
						$category_icon = array();
						if( function_exists( 'fw_get_db_term_option' ) ) {
							$icon          = fw_get_db_term_option($category->term_id, 'project_cat');
							$category_icon = !empty($icon['category_icon']) ? $icon['category_icon'] : array();
						}	

						$query_arg['category[]'] = urlencode($category->slug);

						if(is_user_logged_in() && $user_type == 'employer') {
							$permalink = add_query_arg('category', $category->term_id, $post_job_page);
						} else {
							$permalink = add_query_arg($query_arg, esc_url($search_page));
						}
						?>
						<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 float-left">
							<div class="wt-categorycontent">
								<?php
								if (!empty($category_icon) && $category_icon['type'] === 'icon-font') {
									do_action('enqueue_unyson_icon_css');
									if (!empty($category_icon['icon-class'])) {?>
										<i class="<?php echo esc_attr($category_icon['icon-class']); ?>"></i>
									<?php
									}
								} elseif (!empty($category_icon['type']) && $category_icon['type'] === 'custom-upload') {
									if (!empty($category_icon['url'])) {?>
										<figure><img src="<?php echo esc_url($category_icon['url']); ?>" alt="<?php esc_attr_e('Category','workreap_core'); ?>"></figure>
										<?php
									}
								}
								?>
								<div class="wt-cattitle">
									<h3><a href="<?php echo esc_url( $permalink );?>"><?php echo esc_html($category->name); ?></a></h3>
								</div>
								<div class="wt-categoryslidup">
									<?php if( !empty( $category->description ) ) { ?>
										<p><?php echo esc_html($category->description); ?></p>
									<?php } ?>
									<a href="<?php echo esc_url( $permalink );?>"><?php esc_html_e('Explore', 'workreap_core') ?>&nbsp;<i class="fa fa-arrow-right"></i></a>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php
get_footer();
