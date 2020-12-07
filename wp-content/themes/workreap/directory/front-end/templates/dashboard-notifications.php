<?php
/**
 *
 * The template part for displaying saved jobs
 *
 * @package   Workreap
 * @author    Amentotech
 * @link      http://amentotech.com/
 * @since 1.0
 */
global $current_user, $wp_roles,$userdata,$post,$paged,$woocommerce;
global $wpdb;

////testing end
$identity 		= !empty($_GET['identity']) ? $_GET['identity'] : "";
$ref 			= !empty($_GET['ref']) ? $_GET['ref'] :"";

$show_posts		= get_option('posts_per_page');
$date_formate	= get_option('date_format');
$pg_page 		= get_query_var('page') ? get_query_var('page') : 1; //rewrite the global var
$pg_paged 		= get_query_var('paged') ? get_query_var('paged') : 1; //rewrite the global var
//paged works on single pages, page - works on homepage
$paged 			= max($pg_page, $pg_paged);
$current_page 	= $paged;
$limit			= 10;
$offset 		= $current_page - 1;
?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 float-right">
	<div class="wt-dashboardbox wt-dashboardinvocies">
		<div class="wt-dashboardboxtitle wt-titlewithsearch">
			<h2><?php esc_html_e( 'Notifications', 'workreap' ); ?></h2>
		</div>
		<div class="wt-dashboardboxcontent wt-categoriescontentholder wt-categoriesholder">
			<table class="wt-tablecategories">
				<?php if ( class_exists('NotificationSystem') ) { ?>
					<?php 
						$notifications 			= NotificationSystem::getUserNotifications( $identity, $offset, $limit );
						$total_notifications 	= NotificationSystem::getTotalUserNotificationsCount( $identity );
						$max_num_pages 			= $total_notifications / $limit;
						$date_format 			= get_option('date_format') . " - " . get_option('time_format');

						// mark all notifications as seen
						NotificationSystem::markNotifications( $identity );
					?>
					<thead>
						<tr>
							<th><?php esc_html_e( '#', 'workreap' ); ?></th>
							<th><?php esc_html_e( 'Date', 'workreap' ); ?></th>
							<th><?php esc_html_e( 'Notification Message', 'workreap' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php 
							if ( !empty($notifications) ) {
								foreach ( $notifications as $index => $notification ) {
									?>
									<tr class="<?php echo $notification['status'] == 1 ? 'wt-newitem' : ''; ?>">
										<td><?php echo $index + $offset * $limit + 1; ?></td>
										<td><?php echo date($date_format, intval($notification['timestamp'])); ?></td>
										<td>
											<?php if( !empty( $notification['url'] ) ) : ?>
												<a href="<?php echo esc_url_raw( $notification['url'] ); ?>" target="_blank">
											<?php endif; ?>
											<?php echo nl2br( esc_html( $notification['message'] ) ); ?>
											<?php if( !empty( $notification['url'] ) ) : ?>
												</a>
											<?php endif; ?>
										</td>
									</tr>
								<?php } ?>
						<?php } ?>
					</tbody>
				<?php } ?>
			</table>
			<?php 
				if ( empty($notifications) ) { 
					do_action('workreap_empty_records_html','', esc_html__( 'No notification has been sent yet.', 'workreap' ), true);
				} 
			?>
											
			<?php if ( 1 < $max_num_pages ) {?>
				<nav class="wt-pagination woo-pagination">
					<?php 
						$big = 999999999;
						echo paginate_links( array(
							'base' => str_replace( $big, '%#%', esc_url_raw( get_pagenum_link( $big ) ) ),
							//'base' => '%_%',
							'format' => '?paged=%#%',
							'current' => max( 1, get_query_var('paged') ),
							'total' => $max_num_pages,
							'prev_text' => '<i class="lnr lnr-chevron-left"></i>',
							'next_text' => '<i class="lnr lnr-chevron-right"></i>'
						) );
					?>
				</nav>
			<?php }?>
		</div>
	</div>
</div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-3 float-left">
	<?php get_template_part('directory/front-end/templates/dashboard', 'sidebar-ads'); ?>
</div>
<?php 
	$script = "
	     jQuery('.wt-tablecategories').basictable({
		    breakpoint: 767
		});
	";
	wp_add_inline_script( 'basictable', $script, 'after' );
