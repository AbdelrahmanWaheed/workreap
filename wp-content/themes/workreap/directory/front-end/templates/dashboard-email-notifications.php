<?php 
/**
 *
 * The template part for displaying the template to display email settings
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
<div class="wt-tabsinfo wt-email-settings">
	<div class="wt-tabscontenttitle">
		<h2><?php esc_html_e('Email Notifications', 'workreap'); ?></h2>
	</div>
	<div class="wt-settingscontent">
		<div class="wt-description">
			<p><?php esc_html_e('All the emails will be sent to the below email address','workreap');?></p>
		</div>
		<div class="wt-formtheme wt-userform">
			<fieldset>
				<div class="form-group form-disabeld">
					<input type="password" name="useremail" class="form-control" placeholder="<?php echo esc_attr($current_user->user_email);?>" disabled="">
				</div>
			</fieldset>
		</div>
	</div>
</div>
