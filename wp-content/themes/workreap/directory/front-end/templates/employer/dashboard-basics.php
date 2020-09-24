<?php 
/**
 *
 * The template part for displaying the freelancer profile basics
 *
 * @package   Workreap
 * @author    Amentotech
 * @link      http://amentotech.com/
 * @since 1.0
 */
global $current_user, $wp_roles, $userdata, $post;
$user_identity 	 = $current_user->ID;
$linked_profile  = workreap_get_linked_profile_id($user_identity);
$first_name 	= get_user_meta($user_identity, 'first_name', true);
$last_name 		= get_user_meta($user_identity, 'last_name', true);
$display_name	= $current_user->display_name;

$post_id 		= $linked_profile;
$post_object 	= get_post( $post_id );
$content 	 	= $post_object->post_content;
$tag_line 		= '';

$banner_image 	= array();
if (function_exists('fw_get_db_post_option')) {	
	$tag_line     	 	= fw_get_db_post_option($post_id, 'tag_line', true);	
}

$comapny_name	='';
if( function_exists('fw_get_db_settings_option')  ){
	$comapny_name	= fw_get_db_settings_option('comapny_name', $default_value = null);
}

$company_job_title	='';
if( function_exists('fw_get_db_settings_option')  ){
	$company_job_title	= fw_get_db_settings_option('company_job_title', $default_value = null);
}


?>
<div class="wt-yourdetails wt-tabsinfo">
	<div class="wt-tabscontenttitle">
		<h2><?php esc_html_e('Your Details', 'workreap'); ?></h2>
	</div>
	<div class="wt-formtheme wt-userform">
		<fieldset>
			<div class="form-group form-group-half toolip-wrapo">
				<input type="text" value="<?php echo esc_attr( $first_name ); ?>" name="basics[first_name]" class="form-control" placeholder="<?php esc_attr_e('First Name', 'workreap'); ?>">
				<?php do_action('workreap_get_tooltip','element','first_name');?>
			</div>
			<div class="form-group form-group-half toolip-wrapo">
				<input type="text" value="<?php echo esc_attr( $last_name ); ?>" name="basics[last_name]" class="form-control" placeholder="<?php esc_attr_e('Last Name', 'workreap'); ?>">
				<?php do_action('workreap_get_tooltip','element','last_name');?>
			</div>
			<div class="form-group toolip-wrapo">
				<input type="text" name="basics[tag_line]" class="form-control" value="<?php echo esc_attr( stripslashes( $tag_line ) ); ?>" placeholder="<?php esc_attr_e('Add your tagline here', 'workreap'); ?>">
				<?php do_action('workreap_get_tooltip','element','tagline');?>
			</div>
			<div class="form-group toolip-wrapo">
				<input type="text" name="basics[display_name]" class="form-control" value="<?php echo esc_attr( $display_name ); ?>" placeholder="<?php esc_attr_e('Display name', 'workreap'); ?>">
				<?php do_action('workreap_get_tooltip','element','display_name');?>
			</div>
			<?php if(!empty($comapny_name) && $comapny_name === 'enable') { 
					$job_company_name	= '';
					if( function_exists('fw_get_db_post_option') ){
						$job_company_name	= fw_get_db_post_option($post_id, 'comapny_name', true);
					}
					
				?>
				<div class="form-group form-group-half toolip-wrapo">
					<input type="text" value="<?php echo esc_attr( $job_company_name ); ?>" name="basics[company_name]" class="form-control" placeholder="<?php esc_attr_e('Company name', 'workreap'); ?>">
					<?php do_action('workreap_get_tooltip','element','comapny_name');?>
				</div>
			<?php } ?>
			<?php 
				if(!empty($company_job_title) && $company_job_title === 'enable') {
					$job_title	= '';
					if( function_exists('fw_get_db_post_option') ){
						$job_title	= fw_get_db_post_option($post_id, 'company_job_title', true);
					}
			?>
				<div class="form-group form-group-half toolip-wrapo">
					<input type="text" value="<?php echo esc_attr( $job_title ); ?>" name="basics[comapny_name_title]" class="form-control" placeholder="<?php esc_attr_e('Job title', 'workreap'); ?>">
					<?php do_action('workreap_get_tooltip','element','comapny_job_title_name');?>
				</div>
			<?php } ?>
			<div class="form-group">
				<textarea name="basics[content]" class="form-control" placeholder="<?php esc_attr_e('Description', 'workreap'); ?>"><?php echo esc_textarea( $content ); ?></textarea>
			</div>
		</fieldset>
	</div>
</div>
