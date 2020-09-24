<?php
/**
 *
 * The template part for displaying the dashboard menu
 *
 * @package   workreap
 * @author    Amentotech
 * @link      http://amentotech.com/
 * @since 1.0
 */

global $current_user, $wp_roles, $userdata, $post;
$reference 		 = (isset($_GET['ref']) && $_GET['ref'] <> '') ? $_GET['ref'] : '';
$mode 			 = (isset($_GET['mode']) && $_GET['mode'] <> '') ? $_GET['mode'] : '';
$user_identity = $current_user->ID;
if( apply_filters('workreap_system_access','job_base') === true ){
?>
	<li class="menu-item-has-children <?php echo esc_attr( $reference === 'jobs' ? 'wt-open' : ''); ?>">
		<span class="wt-dropdowarrow"><i class="lnr lnr-chevron-right"></i></span>
		<a href="javascript:;">
			<i class="ti-bag"></i>
			<span><?php esc_html_e('Manage Jobs','workreap');?></span>
			<?php do_action('workreap_get_tooltip','element','manage-jobs');?>
		</a>
		<ul class="sub-menu" <?php echo esc_attr( $reference ) === 'jobs' ? 'style="display: block;"' : ''; ?>>
			<li class="<?php echo esc_attr( $mode === 'posted' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('post_job', $user_identity); ?>"><?php esc_html_e('Post a job','workreap');?></a></li>
			<li class="<?php echo esc_attr( $mode === 'posted' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('jobs', $user_identity,'','posted'); ?>"><?php esc_html_e('Posted Jobs','workreap');?></a></li>
			<li class="<?php echo esc_attr( $mode === 'ongoing' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('jobs', $user_identity,'','ongoing'); ?>"><?php esc_html_e('Ongoing Jobs','workreap');?></a></li>
			<li class="<?php echo esc_attr( $mode === 'completed' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('jobs', $user_identity,'','completed'); ?>"><?php esc_html_e('Completed Jobs','workreap');?></a></li>
			<li class="<?php echo esc_attr( $mode === 'cancelled' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('jobs', $user_identity,'','cancelled'); ?>"><?php esc_html_e('Cancelled Jobs','workreap');?></a></li>
		</ul>
	</li>
<?php } ?>
