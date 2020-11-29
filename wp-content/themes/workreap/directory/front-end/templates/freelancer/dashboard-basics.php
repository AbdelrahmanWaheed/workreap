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

$per_hour_rate 	= '';
$gender  		= '';
$tag_line 		= '';
$max_price		= '';

$banner_image 	= array();

if (function_exists('fw_get_db_post_option')) {
	$per_hour_rate     	= fw_get_db_post_option($post_id, '_perhour_rate', true);	
	$gender     	 	= fw_get_db_post_option($post_id, 'gender', true);	
	$tag_line     	 	= fw_get_db_post_option($post_id, 'tag_line', true);	
}


$hide_perhour	= 'no';
if (function_exists('fw_get_db_settings_option')) {
	$freelancer_price_option = fw_get_db_settings_option('freelancer_price_option', $default_value = null);
	$hide_perhour			= fw_get_db_settings_option('hide_freelancer_perhour', $default_value = null);
	$freelancertype			= fw_get_db_settings_option('freelancertype_multiselect', $default_value = null);
}

if(!empty($freelancer_price_option) && $freelancer_price_option === 'enable' ){
	$max_price     	= fw_get_db_post_option($post_id, 'max_price', true);
}


$per_hour_rate	=  !empty($per_hour_rate) ? $per_hour_rate : '';
$max_price		=  !empty($max_price) ? $max_price : '';

$gender_list	= array();
$gender_list	= apply_filters('workreap_gender_types',$gender_list);
$languages 		 	= workreap_get_taxonomy_array('languages');
$english_level   	= worktic_english_level_list();
$db_english_level	= get_post_meta($linked_profile, '_english_level', true);
$db_freelancer_type = get_post_meta($linked_profile, '_freelancer_type', true);
$freelancer_level   = worktic_freelancer_level_list();    
$show_freelancer_type = false;

//multiselect
$multiselect	= '';
$typename		= 'settings[freelancer_type]';
if(!empty($freelancertype) && $freelancertype === 'enable' ){
	$multiselect	= 'multiple';
	$typename		= 'settings[freelancer_type][]';
}	
?>
<div class="wt-yourdetails wt-tabsinfo">
	<div class="wt-tabscontenttitle">
		<h2><?php esc_html_e('Your Details', 'workreap'); ?></h2>
	</div>
	<div class="wt-formtheme wt-userform">
		<fieldset>
			<?php if( !empty( $gender_list ) ){?>
			<div class="form-group form-group-half">
				<span class="wt-select">
					<select name="basics[gender]" class="chosen-select">
						<option value="" disabled=""><?php esc_html_e('Select Gender', 'workreap'); ?></option>
						<?php foreach( $gender_list as $key	=> $val ){?>
							<option <?php selected( $gender, $key, true); ?> value="<?php echo esc_attr( $key );?>"><?php echo esc_attr( $val );?></option>
						<?php }?>
					</select>
				</span>
			</div>
			<?php }?>
			<div class="form-group form-group-half toolip-wrapo">
				<input type="text" value="<?php echo esc_attr( $first_name ); ?>" name="basics[first_name]" class="form-control" placeholder="<?php esc_attr_e('First Name', 'workreap'); ?>">
				<?php do_action('workreap_get_tooltip','element','first_name');?>
			</div>
			<div class="form-group form-group-half toolip-wrapo">
				<input type="text" value="<?php echo esc_attr( $last_name ); ?>" name="basics[last_name]" class="form-control" placeholder="<?php esc_attr_e('Last Name', 'workreap'); ?>">
				<?php do_action('workreap_get_tooltip','element','last_name');?>
			</div>
			<div class="form-group form-group-half toolip-wrapo">
				<input type="text" name="basics[display_name]" class="form-control" value="<?php echo esc_attr( $display_name ); ?>" placeholder="<?php esc_attr_e('Display name', 'workreap'); ?>">
				<?php do_action('workreap_get_tooltip','element','display_name');?>
			</div>
			<?php if( !empty($hide_perhour) && $hide_perhour === 'no' ){?>
				<div class="form-group toolip-wrapo form-group-half">
					<input type="number" name="basics[per_hour_rate]" class="form-control" value="<?php echo esc_attr( $per_hour_rate ); ?>" placeholder="<?php esc_attr_e('Your service minimum hourly rate ($)', 'workreap'); ?>">
					<?php do_action('workreap_get_tooltip','element','perhour');?>
				</div>
			<?php } ?>
			<?php if(!empty($freelancer_price_option) && $freelancer_price_option === 'enable' ){?>
				<div class="form-group toolip-wrapo form-group-half">
					<input type="number" name="basics[max_price]" class="form-control" value="<?php echo esc_attr( $max_price ); ?>" placeholder="<?php esc_attr_e('Your service maximum hourly rate ($)', 'workreap'); ?>">
					<?php do_action('workreap_get_tooltip','element','max_price');?>
				</div>
			<?php } ?>
			<div class="form-group toolip-wrapo">
				<input type="text" name="basics[tag_line]" class="form-control" value="<?php echo esc_attr( stripslashes( $tag_line ) ); ?>" placeholder="<?php esc_attr_e('Add your tagline here', 'workreap'); ?>">
				<?php do_action('workreap_get_tooltip','element','tagline');?>
			</div>
			
			<div class="form-group">
				<textarea name="basics[content]" class="form-control" placeholder="<?php esc_attr_e('Description', 'workreap'); ?>"><?php echo esc_textarea( $content ); ?></textarea>
			</div>
		</fieldset>
	</div>

</div>
<div class="wt-tabsinfo">
	<div class="wt-tabscontenttitle">
		<h2><?php esc_html_e('Select Languages', 'workreap'); ?></h2>
	</div>
	<div class="wt-settingscontent">
		<div class="wt-formtheme wt-userform">
			<div class="form-group">
				<select data-placeholder="<?php esc_attr_e('Languages', 'workreap'); ?>" name="settings[languages][]" multiple class="chosen-select">
					<?php if( !empty( $languages ) ){
						foreach( $languages as $key => $item ){
							$selected = '';
							if( has_term( $item->term_id, 'languages', $post_id )  ){
								$selected = 'selected';
							}
						?>
						<option <?php echo esc_attr($selected);?> value="<?php echo intval( $item->term_id );?>"><?php echo esc_html( $item->name );?></option>
					<?php }}?>
				</select>
			</div>
		</div>
	</div>
	<div class="wt-tabscontenttitle">
		<h2><?php esc_html_e('English Level', 'workreap'); ?></h2>
	</div>
	<div class="wt-settingscontent">
		<div class="wt-formtheme wt-userform">
			<div class="form-group">
				<select data-placeholder="<?php esc_attr_e('English Level', 'workreap'); ?>" name="settings[english_level]" class="chosen-select">
					<?php if( !empty( $english_level ) ){
						foreach( $english_level as $key => $item ){
					?>
					<option <?php selected( $db_english_level, $key ); ?> value="<?php echo esc_attr( $key );?>"><?php echo esc_html( $item );?></option>
					<?php }}?>
				</select>
			</div>
		</div>
	</div>

	<?php if( $show_freelancer_type ) : ?>
		<div class="wt-tabscontenttitle">
			<h2><?php esc_html_e('Freelancer Type', 'workreap'); ?></h2>
		</div>
		<div class="wt-settingscontent">
			<div class="wt-formtheme wt-userform">
				<div class="form-group">
					<select data-placeholder="<?php esc_attr_e('Freelancer Type', 'workreap'); ?>" name="<?php echo esc_html($typename);?>"  class="chosen-select" <?php echo esc_html($multiselect);?>>
						<?php if( !empty( $freelancer_level ) ){
							$db_key	= '';
							foreach( $freelancer_level as $key => $item ){
								if(!empty($freelancertype) && $freelancertype === 'enable' ){
									if(is_array($db_freelancer_type) && in_array($key,$db_freelancer_type)){
										$db_key	= $key;
									}else if(!empty($db_freelancer_type) && $db_freelancer_type === $key) {
										$db_key	= $key;
									}
								}else{
									$db_key	= $db_freelancer_type;
								}
						?>
						<option <?php selected( $db_key, $key ); ?> value="<?php echo esc_attr( $key );?>"><?php echo esc_html( $item );?></option>
						<?php }}?>
					</select>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>