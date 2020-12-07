<?php
/**
 * Template Name: Dashboard
 *
 * @package Workreap
 * @since Workreap 1.0
 * @desc Template used for front end dashboard.
 */
/* Define Global Variables */
global $current_user, $wp_roles;
get_header();
$user_identity 	= $current_user->ID;
$url_identity 	= !empty($_GET['identity']) ? intval($_GET['identity']) : '';;
$user_type		= apply_filters('workreap_get_user_type', $user_identity );
$post_id		= workreap_get_linked_profile_id( $user_identity );
$is_verified 	= get_post_meta($post_id, '_is_verified', true); 
$hide_switch_account	= '';
if ( function_exists( 'fw_get_db_settings_option' ) ) {
	$hide_switch_account 	= fw_get_db_settings_option( 'hide_switch_account', $default_value = null );
} 

if( have_posts() ) {?>
	<div class="wt-haslayout wt-haslayout page-data">
		<?php 
			while ( have_posts() ) : the_post();
				the_content();
				wp_link_pages( array(
									'before'      => '<div class="wt-paginationvtwo"><nav class="wt-pagination"><ul>',
									'after'       => '</ul></nav></div>',
								) );
			endwhile;
			wp_reset_postdata();
		?>
	</div>
<?php }

if (is_user_logged_in() && ( $user_type === 'employer' || $user_type === 'freelancer') || $user_type === 'subscriber' ) {
	Workreap_Profile_Menu::workreap_profile_menu_left(); 
	$verify_user  		= 'verified';
	if (function_exists('fw_get_db_settings_option')) {
		$verify_user = fw_get_db_settings_option('verify_user', $default_value = null);
	}    
?>	
	<section class="wt-haslayout wt-dbsectionspace dashboard-content-area">
		<div class="row">
				<?php
				if ( is_user_logged_in() && $user_type === 'subscriber' && $current_user->register_with_social == 'yes'  ) {?>
					<div class="wt-tabscontent tab-content wt-dashboardbox">
						<div class="wt-personalskillshold tab-pane active fade show">
							<?php do_action( 'workreap_social_registeration' );?>
						</div>
					</div>
				<?php } else{ 
					if( empty( $is_verified ) || $is_verified === 'no' ){
						$verify   = workreap_get_signup_page_url('step', '1'); 
						?>
						<div class="container">
						  <div class="wt-haslayout page-data">
						  	<?php 
								if( isset( $verify_user ) && $verify_user === 'none' ){
									Workreap_Prepare_Notification::workreap_warning(esc_html__('Verification', 'workreap'), esc_html__('Your account is not verified. Please contact to administrator to get verified', 'workreap'));
								} else{
									Workreap_Prepare_Notification::workreap_warning(esc_html__('Verification', 'workreap'), esc_html__('Your account is not verified. Please verify your account', 'workreap'),$verify,esc_html__('Verify', 'workreap'));
								}
							?>
						  </div>
						</div>
					<?php }?>

					<?php
					if (isset($_GET['ref']) && $_GET['ref'] === 'dispute' && $url_identity == $user_identity) {	
						get_template_part('directory/front-end/templates/'.$user_type.'/dashboard', 'dispute');
					} elseif (isset($_GET['ref']) && $_GET['ref'] === 'help' && $url_identity == $user_identity) {	
						get_template_part('directory/front-end/templates/dashboard', 'help');
					} elseif (isset($_GET['ref']) && $_GET['ref'] === 'package' && $url_identity == $user_identity) {
						if(apply_filters('workreap_is_listing_free',false,$user_identity) === false ){
							get_template_part('directory/front-end/templates/dashboard', 'packages');
						}
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'profile' && $url_identity == $user_identity) {	
						get_template_part('directory/front-end/templates/'.$user_type.'/dashboard', 'profile-settings');
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'project-single' && $url_identity == $user_identity) {
						if( apply_filters('workreap_system_access','job_base') === true ){
							get_template_part('directory/front-end/templates/'.$user_type.'/dashboard', 'ongoing-jobs');
						}
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'proposals' && $url_identity == $user_identity) {
						if( apply_filters('workreap_system_access','job_base') === true ){
							get_template_part('directory/front-end/templates/'.$user_type.'/dashboard', 'latest-proposals');
						}
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'switch' && $url_identity == $user_identity && $hide_switch_account === 'no') {
						get_template_part('directory/front-end/templates/dashboard', 'switch-account');
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'saved' && $url_identity == $user_identity) {	
						get_template_part('directory/front-end/templates/dashboard', 'saved-items');
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'post_job' && $url_identity == $user_identity) {	
						if( apply_filters('workreap_system_access','job_base') === true ){
							if( 'employer' == apply_filters('workreap_get_user_type', $user_identity ) ){
								get_template_part('directory/front-end/templates/employer/dashboard', 'post-job');
							}
						}
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'chat' && $url_identity == $user_identity) {		
						if( apply_filters('workreap_is_feature_allowed', 'wt_pr_chat', $user_identity) === true ){
							get_template_part('directory/front-end/templates/dashboard', 'messaging');
						}
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'jobs' && $url_identity == $user_identity) {
						if( apply_filters('workreap_system_access','job_base') === true ){
							if( 'employer' == apply_filters('workreap_get_user_type', $user_identity ) ){
								if (isset($_GET['mode']) && $_GET['mode'] === 'ongoing') {
									get_template_part('directory/front-end/templates/employer/dashboard', 'ongoing-jobs');
								} else if (isset($_GET['mode']) && $_GET['mode'] === 'completed') {		
									get_template_part('directory/front-end/templates/employer/dashboard', 'completed-jobs');
								} else if (isset($_GET['mode']) && $_GET['mode'] === 'cancelled') {		
									get_template_part('directory/front-end/templates/employer/dashboard', 'cancelled-jobs');
								} else if (isset($_GET['mode']) && !empty( $_GET['id'] ) && $_GET['mode'] === 'edit') {		
									get_template_part('directory/front-end/templates/employer/dashboard', 'edit-job');
								} else if (isset($_GET['mode']) && !empty( $_GET['id'] ) && $_GET['mode'] === 'proposals') {
									get_template_part('directory/front-end/templates/employer/dashboard', 'job-proposals');
								}else if (isset($_GET['mode']) && !empty( $_GET['id'] ) && $_GET['mode'] === 'history') {	
									get_template_part('directory/front-end/templates/employer/dashboard', 'project-history');
								} else{
									get_template_part('directory/front-end/templates/employer/dashboard', 'manage-jobs');
								}
							}else if( 'freelancer' == apply_filters('workreap_get_user_type', $user_identity ) ){
								if (isset($_GET['mode']) && !empty( $_GET['id'] ) && $_GET['mode'] === 'history') {	
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'project-history');
								} 
							}
						}
					}  else if (isset($_GET['ref']) && $_GET['ref'] === 'projects' && $url_identity == $user_identity) {
						if( apply_filters('workreap_system_access','job_base') === true ){
							if( 'freelancer' == apply_filters('workreap_get_user_type', $user_identity ) ){
								if (isset($_GET['mode']) && $_GET['mode'] === 'ongoing') {		
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'ongoing-projects');
								} else if (isset($_GET['mode']) && $_GET['mode'] === 'completed') {		
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'completed-projects');
								} else if (isset($_GET['mode']) && $_GET['mode'] === 'cancelled') {
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'cancelled-jobs');
								} else{
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'manage-projects');
								}
							}
						}
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'services' && $url_identity == $user_identity) {
						if( apply_filters('workreap_system_access','service_base') === true ){
							if (isset($_GET['mode']) && $_GET['mode'] === 'history') {	
								get_template_part('directory/front-end/templates/dashboard', 'services-history');
							}
							if( 'freelancer' == apply_filters('workreap_get_user_type', $user_identity ) ){
								if (isset($_GET['mode']) && $_GET['mode'] === 'posted') {		
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'manage-services');
								} else if (isset($_GET['mode']) && $_GET['mode'] === 'completed') {		
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'completed-services');
								} else if (isset($_GET['mode']) && $_GET['mode'] === 'cancelled') {
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'cancelled-services');
								} else if (isset($_GET['mode']) && $_GET['mode'] === 'ongoing') {
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'ongoing-services');
								}
							}else if( 'employer' == apply_filters('workreap_get_user_type', $user_identity ) ){
								if (isset($_GET['mode']) && $_GET['mode'] === 'completed') {		
									get_template_part('directory/front-end/templates/employer/dashboard', 'completed-services');
								} else if (isset($_GET['mode']) && $_GET['mode'] === 'cancelled') {
									get_template_part('directory/front-end/templates/employer/dashboard', 'cancelled-services');
								} else if (isset($_GET['mode']) && $_GET['mode'] === 'ongoing') {
									get_template_part('directory/front-end/templates/employer/dashboard', 'ongoing-services');
								}
							}
						}
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'portfolios' && $url_identity == $user_identity) {
						$portfolio_settings	= apply_filters('workreap_portfolio_settings','gadget');
						if( isset($portfolio_settings) && $portfolio_settings == 'enable' ){
							if( 'freelancer' == apply_filters('workreap_get_user_type', $user_identity ) ){
								if (isset($_GET['mode']) && $_GET['mode'] === 'posted') {	
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'manage-portfolio');
								}
							}
						}
					} else if (isset($_GET['ref']) && !empty( $_GET['ref'] ) && $_GET['ref'] === 'milestone') {	
						if (isset($_GET['mode']) && $_GET['mode'] === 'listing') {		
							get_template_part('directory/front-end/templates/'.$user_type.'/dashboard', 'milstone-listing');
						}
					} else if (isset($_GET['ref']) && !empty( $_GET['ref'] ) && $_GET['ref'] === 'payouts') {	
						get_template_part('directory/front-end/templates/dashboard', 'payouts');
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'account-settings' && $url_identity == $user_identity) {					
						get_template_part('directory/front-end/templates/dashboard', 'account-settings');
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'invoices' && $url_identity == $user_identity) {
						get_template_part('directory/front-end/templates/dashboard', 'invoices');
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'notifications' && $url_identity == $user_identity) {
						get_template_part('directory/front-end/templates/dashboard', 'notifications');
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'earnings' && $url_identity == $user_identity) {
						if( 'freelancer' == apply_filters('workreap_get_user_type', $user_identity ) ){
							get_template_part('directory/front-end/templates/freelancer/dashboard-total', 'earning');
						}
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'disputes' && $url_identity == $user_identity) {
						get_template_part('directory/front-end/templates/dashboard', 'disputes');
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'micro_service' && $url_identity == $user_identity) {	
						if( 'freelancer' == apply_filters('workreap_get_user_type', $user_identity ) ){
							if( apply_filters('workreap_system_access','service_base') === true ){
								if( !empty($_GET['mode']) && $_GET['mode'] === 'edit'  && !empty($_GET['id']) ) {
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'edit-service');
								} else {
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'service');
								}
							}
						}
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'portfolio' && $url_identity == $user_identity) {	
						$portfolio_settings	= apply_filters('workreap_portfolio_settings','gadget');
						if( isset($portfolio_settings) && $portfolio_settings == 'enable' ){
							if( 'freelancer' == apply_filters('workreap_get_user_type', $user_identity ) ){
								if( !empty($_GET['mode']) && $_GET['mode'] === 'edit'  && !empty($_GET['id']) ) {
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'edit-portfolio');
								} else {
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'add-portfolio');
								}
							}
						}
					} else if (isset($_GET['ref']) && $_GET['ref'] === 'addons_service' && $url_identity == $user_identity) {	
						if( 'freelancer' == apply_filters('workreap_get_user_type', $user_identity ) ){
							if( apply_filters('workreap_system_access','service_base') === true ){
								if( !empty( $_GET['mode'] ) && ( $_GET['mode'] === 'listing' ) ) {
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'list-addons-service');
								} else if( !empty($_GET['mode']) && $_GET['mode'] === 'edit'  && !empty($_GET['id']) ) {
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'addons-service');
								}else {
									get_template_part('directory/front-end/templates/freelancer/dashboard', 'addons-service');
								}
							}
						}
					}else {
						get_template_part('directory/front-end/templates/'.$user_type.'/dashboard', 'insights');
					}
				}
			?>
		</div>
	</section>
	<?php get_template_part('directory/front-end/templates/freelancer/dashboard', 'switch-account');?>
	<?php } else {?>
		<div class="container">
		  <div class="wt-haslayout page-data">
			<?php  Workreap_Prepare_Notification::workreap_warning(esc_html__('Restricted Access', 'workreap'), esc_html__('You have not any privilege to view this page.', 'workreap'));?>
		  </div>
		</div>
<?php }
get_footer(); 