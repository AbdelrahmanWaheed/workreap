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
$user_identity 	 = $current_user->ID;
$link_id		 = workreap_get_linked_profile_id( $user_identity );
$user_type		= apply_filters('workreap_get_user_type', $user_identity );

$reference 		 = (isset($_GET['ref']) && $_GET['ref'] <> '') ? $_GET['ref'] : '';
$mode 			 = (isset($_GET['mode']) && $_GET['mode'] <> '') ? $_GET['mode'] : '';
$hide_switch_account	= '';
if ( function_exists( 'fw_get_db_settings_option' ) ) {
	$hide_switch_account 	= fw_get_db_settings_option( 'hide_switch_account', $default_value = null );
} 

if( !empty( $user_type ) && ( $user_type === 'freelancer' || $user_type === 'employer' ) && $hide_switch_account === 'no' ){?>
	<li class="toolip-wrapo <?php echo esc_attr( $reference === 'switch' ? 'wt-active' : ''); ?>">
		<a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('switch', $user_identity); ?>">
			<i class="ti-control-shuffle"></i>
			<span><?php esc_html_e('Switch Account','workreap');?></span>
			<?php do_action('workreap_get_tooltip','element','switch-account');?>
		</a>
	</li>
<?php }
