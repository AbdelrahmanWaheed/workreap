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
$portfolio_settings	= apply_filters('workreap_portfolio_settings','gadget');
if( isset($portfolio_settings) && $portfolio_settings == 'enable' ){
?>
<li class="menu-item-has-children toolip-wrapo <?php echo esc_attr( $reference === 'portfolios' ? 'wt-open' : ''); ?>">
	<span class="wt-dropdowarrow"><i class="lnr lnr-chevron-right"></i></span>
	<a href="javascript:;">
		<i class="ti-pencil-alt"></i>
		<span><?php esc_html_e('Manage Portfolios', 'workreap');?></span>
		<?php do_action('workreap_get_tooltip','element','manage-portfolios');?>
	</a>
	
	<ul class="sub-menu" <?php echo esc_attr( $reference ) === 'portfolios' ? 'style="display: block;"' : ''; ?>>
		<li class="<?php echo esc_attr( $mode === 'post_portfolio' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('portfolio', $user_identity); ?>"><?php esc_html_e('Add Portfolio','workreap');?></a></li>
		<li class="<?php echo esc_attr( $mode === 'posted' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('portfolios', $user_identity,'','posted'); ?>"><?php esc_html_e('Your Portfolios','workreap');?></a></li>
	</ul>
</li>
<?php }