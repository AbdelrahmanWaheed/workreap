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
$user_identity   = $current_user->ID;
?>
<li class="toolip-wrapo <?php echo esc_attr( $reference === 'add-job' ? 'wt-active' : ''); ?>">
	<a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('post_job', $user_identity); ?>" class="highlight">
		<i class="ti-bolt"></i>
		<span><?php esc_html_e('Post A Job','workreap');?></span>
		<?php // do_action('workreap_get_tooltip','element','insights');?>
	</a>
</li>
