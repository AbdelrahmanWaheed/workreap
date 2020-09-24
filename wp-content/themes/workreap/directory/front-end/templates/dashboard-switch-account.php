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

$identity 		= !empty($_GET['identity']) ? $_GET['identity'] : "";
$ref 			= !empty($_GET['ref']) ? $_GET['ref'] :"";

$user_identity 	 = $current_user->ID;
$post_id 		 = workreap_get_linked_profile_id($user_identity);
$user_type		= apply_filters('workreap_get_user_type', $user_identity );

if (function_exists('fw_get_db_settings_option')) {
	$login_register = fw_get_db_settings_option('enable_login_register');
	$step_two_title = fw_get_db_settings_option('social_title');
	$step_two_desc = fw_get_db_settings_option('social_desc'); 
	$hide_departments = fw_get_db_settings_option('hide_departments', $default_value = null);
}

if (!empty( $login_register ) && $login_register['enable']['registration']['gadget'] === 'enable') {
	$terms_link = !empty( $login_register['enable']['registration']['enable']['terms_link'] ) ? $login_register['enable']['registration']['enable']['terms_link'] : '';
	$terms_link = !empty( $terms_link ) ? get_the_permalink($terms_link[0]) : '';
	$term_text = !empty( $login_register['enable']['registration']['enable']['term_text'] ) ? $login_register['enable']['registration']['enable']['term_text'] : esc_html__('Agree our terms and conditions', 'workreap');
}

?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 col-xl-6 float-left">
	<div class="wt-dashboardbox">
		<div class="wt-dashboardboxtitle wt-titlewithsearch">
			<h2><?php esc_html_e('Switch Account', 'workreap'); ?></h2>
		</div>
		<div class="wt-dashboardboxcontent wt-helpsupporthead wt-registerformmain">
			<div class="wt-helpsupportcontents">
				<div class="wt-tabscontenttitle">
					<h2><?php esc_html_e('Caution!', 'workreap'); ?></h2>
				</div>
				<div class="wt-description">
					<p><?php esc_html_e('Upon switching your account, you will loss all the changes in your profile, projects, your current package. Default settings like Name, Profile image and banner, your location, You favorites will remains same. ', 'workreap'); ?></p>
				</div>
				<div class="wt-joinforms">
					<ul class="wt-joinsteps">
						<li class="wt-done-next"><a href="javascript:;"><i class="fa fa-check"></i></a></li>
						<li class="wt-active"><a href="javascript:;"><?php esc_html_e('02', 'workreap'); ?></a></li>
					</ul>
					<form class="wt-formtheme wt-formregister wt-formregister-step-two">
						<fieldset class="wt-registerformgroup">
							<div class="form-group">
								<?php do_action('worktic_get_locations_list','location',''); ?>	
							</div>
							<div class="form-group form-group-half">
								<input type="password" name="password" aautocomplete="off" class="form-control" placeholder="<?php esc_html_e('Password*', 'workreap' ); ?>">
							</div>
							<div class="form-group form-group-half">
								<input type="password" name="verify_password" autocomplete="off" class="form-control" placeholder="<?php esc_html_e('Retype Password*', 'workreap' ); ?>">
							</div>
						</fieldset>
						<fieldset class="wt-formregisterstart">
							<div class="wt-title wt-formtitle"><h4><?php esc_html_e('Start as :', 'workreap' ); ?></h4></div>
							<ul class="wt-accordionhold wt-formaccordionhold accordion">
								<?php if( !empty( $user_type ) && $user_type === 'freelancer' ) {?>
									<li>
										<div class="wt-accordiontitle" id="headingOne" data-toggle="collapse" data-target="#collapseOne">
											<span class="wt-radio">
												<input id="wt-company" type="radio" name="user_type" value="employer" checked>
												<label for="wt-company"><?php esc_html_e('Employer ', 'workreap'); ?><span> <?php esc_html_e('(Signup as company/service seeker &amp; post jobs)', 'workreap' ); ?></span></label>
											</span>
										</div>
										<?php if( !empty( $hide_departments ) && $hide_departments === 'no' ){?>
											<div class="wt-accordiondetails collapse show" id="collapseOne" aria-labelledby="headingOne">
												<div class="wt-radioboxholder">
													<div class="wt-title">
														<h4><?php esc_html_e('Your Department?', 'workreap'); ?></h4>
													</div>
													<?php do_action('worktic_get_departments_list'); ?>				
												</div>	
												<div class="wt-radioboxholder">
													<div class="wt-title">
														<h4><?php esc_html_e('No. of employees you have', 'workreap'); ?></h4>
													</div>
													<?php do_action('workreap_print_employees_list'); ?>
												</div>								
											</div>
										<?php }?>
									</li>
								<?php }elseif( !empty( $user_type ) && $user_type === 'employer' ) {?>
								<li>
									<div class="wt-accordiontitle">
										<span class="wt-radio">
											<input id="wt-freelancer" type="radio" name="user_type" value="freelancer">
											<label for="wt-freelancer"><?php esc_html_e('Freelancer', 'workreap'); ?><span><?php esc_html_e(' (Signup as freelancer &amp; get hired)', 'workreap'); ?></span></label>
										</span>
									</div>
								</li>
								<?php }?>
							</ul>
						</fieldset>
						<fieldset class="wt-termsconditions">
							<div class="wt-checkboxholder">								
								<span class="wt-checkbox">
									<input id="termsconditions" type="checkbox" name="termsconditions" value="checked">
									<label for="termsconditions"><?php echo esc_attr( $term_text ); ?>
										<?php if( !empty( $terms_link ) ) { ?>
											<a href="<?php echo esc_url( $terms_link ); ?>"><?php esc_html_e('Terms & Conditions', 'workreap'); ?></a>
										<?php } ?>
									</label>
								</span>	
								<input type="hidden" name="switch_account" value="yes">
								<a href="javascript:;" class="wt-btn social-step-two"><?php esc_html_e('Continue', 'workreap'); ?></a>						
							</div>
						</fieldset>
						<?php wp_nonce_field('workreap_social_step_two_nounce', 'workreap_social_step_two_nounce'); ?>						
					</form>
				</div>
			</div>		
		</div>
	</div>
</div>