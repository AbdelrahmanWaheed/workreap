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
if( apply_filters('workreap_system_access','service_base') === true ){ ?>
	<li class="menu-item-has-children toolip-wrapo <?php echo esc_attr( $reference === 'services' ? 'wt-open' : ''); ?>">
		<span class="wt-dropdowarrow"><i class="lnr lnr-chevron-right"></i></span>
		<a href="javascript:;">
			<i class="ti-pencil-alt"></i>
			<span><?php esc_html_e('Manage Services','workreap');?></span>
			<?php do_action('workreap_get_tooltip','element','manage-services');?>
		</a>
		
		<ul class="sub-menu" <?php echo esc_attr( $reference ) === 'services' ? 'style="display: block;"' : ''; ?>>
			<?php if ( apply_filters('workreap_system_access', 'service_base') === true) { ?><li class="<?php echo esc_attr( $mode === 'post_service' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('micro_service', $user_identity); ?>"><?php esc_html_e('Post a Service','workreap');?></a></li><?php } ?>
			<li class="<?php echo esc_attr( $mode === 'posted' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('services', $user_identity,'','posted'); ?>"><?php esc_html_e('Posted Services','workreap');?></a></li>
			<li class="<?php echo esc_attr( $mode === 'listing' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('addons_service', $user_identity,'','listing'); ?>"><?php esc_html_e('Addons Services','workreap');?></a></li>
			<li class="<?php echo esc_attr( $mode === 'ongoing' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('services', $user_identity,'','ongoing'); ?>"><?php esc_html_e('Ongoing Services','workreap');?></a></li>
			<li class="<?php echo esc_attr( $mode === 'completed' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('services', $user_identity,'','completed'); ?>"><?php esc_html_e('Completed Services','workreap');?></a></li>
			<li class="<?php echo esc_attr( $mode === 'cancelled' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('services', $user_identity,'','cancelled'); ?>"><?php esc_html_e('Cancelled Services','workreap');?></a></li>
		</ul>
	</li>
<?php }?>