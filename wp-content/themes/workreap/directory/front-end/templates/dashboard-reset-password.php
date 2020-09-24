<?php 
/**
 *
 * The template part for displaying the template reset password
 *
 * @package   Workreap
 * @author    Amentotech
 * @link      http://amentotech.com/
 * @since 1.0
 */
global $current_user, $wp_roles, $userdata, $post;
$user_identity 	 = $current_user->ID;
$linked_profile  = workreap_get_linked_profile_id($user_identity);
$post_id 		 = $linked_profile;
?>
<div class="wt-yourdetails wt-tabsinfo wt-reset-password">
	<div class="wt-tabscontenttitle">
		<h2><?php esc_html_e('Reset Password', 'workreap'); ?></h2>
	</div>
	<form class="wt-formtheme wt-userform changepassword-user-form">
		<fieldset>
			<div class="form-group form-group-half">
				<input type="password" name="password" class="form-control" placeholder="<?php esc_attr_e('Your current password', 'workreap'); ?>">
			</div>
			<div class="form-group form-group-half">
				<input type="password" name="retype" class="form-control" placeholder="<?php esc_attr_e('New Password', 'workreap'); ?>">
			</div>
			<div class="form-group form-group-half wt-btnarea">
				<?php wp_nonce_field('wt_change_password_nonce', 'change_password'); ?>
				<a href="javascript:;" class="wt-btn change-password"><?php esc_html_e('Change Password','workreap');?></a>
			</div>
		</fieldset>
	</form>
</div>
