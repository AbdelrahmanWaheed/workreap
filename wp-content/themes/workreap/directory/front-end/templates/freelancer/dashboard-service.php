<?php
/**
 *
 * The template part for displaying post a job
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
$args_addons = array(
					'author'        =>  $current_user->ID,
					'post_type'		=> 	'addons-services',
					'post_status'	=>  'publish',
					'orderby'       =>  'post_date',
					'order'         =>  'ASC',
					'posts_per_page' => -1
				);
$addons		= get_posts( $args_addons );

$cats			    = workreap_get_taxonomy_array('project_cat');
$deliveries	    	= workreap_get_taxonomy_array('delivery');
$languages		    = workreap_get_taxonomy_array('languages');
$response_time		= workreap_get_taxonomy_array('response_time');
$english_level      = worktic_english_level_list();

$system_access	= '';
if (function_exists('fw_get_db_post_option') ) {
	$system_access	= fw_get_db_settings_option('system_access');
}

$description 		= '';
$name 				= 'service[description]';								
$settings 			= array('media_buttons' => false,'textarea_name'=> $name,'editor_class'=> 'customwp_editor','media_buttons','editor_height'=>300 );
?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 float-left">
	<div class="wt-haslayout wt-post-job-wrap">
		<form class="post-service-form wt-haslayout">
			<div class="wt-dashboardbox">
				<div class="wt-dashboardboxtitle">
					<h2><?php esc_html_e('Post a Service','workreap');?></h2>
				</div>
				<div class="wt-dashboardboxcontent">
					<?php
						if( apply_filters('workreap_is_service_posting_allowed','wt_services', $current_user->ID) === false ){
							$link		= Workreap_Profile_Menu::workreap_profile_menu_link('package', $current_user->ID,true);
							$message	= esc_html__('Your package has reached to the limit. Please renew your package to create a new service','workreap');
							$title		= esc_html__('Alert :','workreap');
							Workreap_Prepare_Notification::workreap_warning($title, $message, $link, esc_html__("Buy Now",'workreap'));		
						} 
					?>
					<div class="wt-jobdescription wt-tabsinfo">
						<div class="wt-tabscontenttitle">
							<h2><?php esc_html_e('Service description','workreap');?></h2>
						</div>
						<div class="wt-formtheme wt-userform wt-userformvtwo">
							<fieldset>
								<div class="form-group form-group-half">
									<input type="text" name="service[title]" class="form-control" placeholder="<?php esc_attr_e('Service Title','workreap');?>">
								</div>
								<div class="form-group form-group-half wt-formwithlabel">
									<span class="wt-selects">
										<select name="service[delivery_time]" class="chosen-select">
											<option value=""><?php esc_html_e('Select Service delivery','workreap');?></option>
											<?php 
											if( !empty( $deliveries ) ){
												foreach( $deliveries as $delivery ){?>
												<option value="<?php echo intval( $delivery->term_id );?>"><?php echo esc_html( $delivery->name );?></option>
											<?php }}?>
										</select>
									</span>
								</div>
								<div class="form-group form-group-half wt-formwithlabel job-cost-input">
									<input type="text" class="wt-numeric" name="service[price]" value="" placeholder="<?php esc_attr_e('Service price','workreap');?>">
								</div>
								<div class="form-group form-group-half wt-formwithlabel">
									<span class="wt-selects">
										<select name="service[downloadable]" class="downloadable-select chosen-select">
											<option value=""><?php esc_html_e('Select downloadable service','workreap');?></option>
											<option value="no"><?php esc_html_e('No','workreap');?></option>
											<option value="yes"><?php esc_html_e('Yes','workreap');?></option>
										</select>
									</span>
								</div>
							</fieldset>
						</div>
					</div>
					<?php get_template_part('directory/front-end/templates/freelancer/dashboard', 'downloadable-service'); ?>
					
					<div class="wt-addonsservices wt-tabsinfo">
						<div class="wt-tabscontenttitle wt-addnew">
							<h2><?php esc_html_e( 'Addons Services','workreap');?></h2>
							<span class="wt-add-addons"><a href="javascript:;"><?php esc_html_e('+ Add New','workreap');?></a></span>
						</div>
						<div class="wt-addonservices-content">
							<ul>
							<?php if( !empty( $addons ) ){ 
								foreach( $addons as $addon ) { 
									$db_price			= 0;
									if (function_exists('fw_get_db_post_option')) {
										$db_price   = fw_get_db_post_option($addon->ID,'price');
									}
								?>
								<li>
									<div class="wt-checkbox">
										<input id="rate<?php echo intval($addon->ID);?>" type="checkbox" name="service[addons][]" value="<?php echo intval($addon->ID);?>">
										<label for="rate<?php echo intval($addon->ID);?>">
											<?php if( !empty( $addon->post_title ) ){?>
												<h3><?php echo esc_html( $addon->post_title );?></h3>
											<?php } ?>
											<?php if( !empty( $addon->post_excerpt ) ){?>
												<p><?php echo esc_html( $addon->post_excerpt);?></p>
											<?php } ?>
											<?php if( !empty( $db_price ) ){?>
												<strong><?php workreap_price_format($db_price);?></strong>
											<?php } ?>
										</label>
									</div>
								</li>
								<?php }} ?>
							</ul>
						</div>
					</div>
					<div class="wt-category-holder wt-tabsinfo wt-dropdown-categories">
						<div class="wt-tabscontenttitle">
							<h2><?php esc_html_e('Service Categories','workreap');?></h2>
						</div>
						<div class="wt-divtheme wt-userform wt-userformvtwo">
							<div class="form-group">
								<?php do_action('workreap_get_categories_list','service[categories][]','');?>
							</div>
						</div>
					</div>
					<div class="wt-category-holder wt-tabsinfo">
						<div class="wt-tabscontenttitle">
							<h2><?php esc_html_e('Service Response Time','workreap');?></h2>
						</div>
						<div class="wt-divtheme wt-userform wt-userformvtwo">
							<div class="form-group">
								<select data-placeholder="<?php esc_attr_e('Select Response Time','workreap');?>" name="service[response_time]"  class="chosen-select">
									<?php if( !empty( $response_time ) ){?>
										<option value=""><?php esc_html_e('Select Response Time','workreap');?></option>
										<?php						
											foreach ($response_time as $key => $item) {
												$term_id   = $item->term_id;									
												?>
												<option value="<?php echo intval( $term_id ); ?>"><?php echo esc_html( $item->name ); ?></option>
												<?php 
											}
										}
									?>		
								</select>
							</div>
						</div>
					</div>
					<div class="wt-language-holder wt-tabsinfo wt-wp-language">
						<div class="wt-divtheme wt-userform">
							<div class="form-group form-group-half ">
								<div class="wt-tabscontenttitle">
									<h2><?php esc_html_e('Languages','workreap');?></h2>
								</div>
								<select data-placeholder="<?php esc_attr_e('Select Languages','workreap');?>" multiple name="service[languages][]"  class="chosen-select">
									<?php 
										if( !empty( $languages ) ){							
											foreach ($languages as $key => $item) {
												$term_id   = $item->term_id;									
												?>
												<option value="<?php echo intval( $term_id ); ?>"><?php echo esc_html( $item->name ); ?></option>
												<?php 
											}
										}
									?>		
								</select>
							</div>
							<div class="form-group form-group-half wt-formwithlabel">
								<div class="wt-tabscontenttitle">
									<h2><?php esc_html_e('English level','workreap');?></h2>
								</div>
								<span class="wt-selects">
									<select name="service[english_level]" class="chosen-select">
										<option value=""><?php esc_html_e('Select english level','workreap');?></option>
										<?php 
										if( !empty( $english_level ) ){
											foreach( $english_level as $key => $level ){?>
											<option value="<?php echo esc_attr( $key );?>"><?php echo esc_html( $level );?></option>
										<?php }}?>
									</select>
								</span>
							</div>
						</div>
					</div>
					
					<div class="wt-jobdetails wt-tabsinfo">
						<div class="wt-tabscontenttitle">
							<h2><?php esc_html_e('Service Details','workreap');?></h2>
						</div>
						<div class="wt-formtheme wt-userform wt-userformvtwo">
							<fieldset>
								<div class="form-group">
									<?php wp_editor($description, 'service_details', $settings);?>
								</div>
							</fieldset>
						</div>
					</div>
					<?php get_template_part('directory/front-end/templates/freelancer/dashboard', 'service-videos'); ?>
					<div class="wt-jobdetails wt-attachmentsholder">
						<div class="wt-tabscontenttitle">
							<h2><?php esc_html_e('Upload Images','workreap');?></h2>
						</div>
						<div class="wt-formtheme wt-formprojectinfo wt-formcategory">
							<fieldset>
								<div class="form-group form-group-label" id="wt-service-container">
									<div class="wt-labelgroup" id="service-drag">
										<label for="file" class="wt-job-file">
											<span class="wt-btn" id="service-btn"><?php esc_html_e('Select File', 'workreap'); ?></span>			
										</label>
										<span><?php esc_html_e('Drop files here to upload', 'workreap'); ?></span>
										<em class="wt-fileuploading"><?php esc_html_e('Uploading', 'workreap'); ?><i class="fa fa-spinner fa-spin"></i></em>
									</div>
									<p class="small text-right"><?php esc_html_e(sprintf('Max file size: %d MB', workreap_get_upload_max_size()), 'workreap'); ?></p>
								</div>
								<div class="form-group">
									<ul class="wt-attachfile uploaded-placeholder"></ul>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="wt-attachmentsholder">
					   <?php if( apply_filters('workreap_is_service_posting_allowed','wt_services', $current_user->ID,'yes') === true ){?>
							<div class="wt-tabscontenttitle">
								<h2><?php esc_html_e('Featured service','workreap');?></h2>
								<div class="wt-rightarea">
									<div class="wt-on-off float-right">
										<input type="hidden" value="off" name="service[is_featured]">
										<input type="checkbox" value="on" id="featured-on" name="service[is_featured]">
										<label for="featured-on"><i></i></label>
									</div>
								</div>
							</div>
						<?php }?>
					</div>	
					<?php get_template_part('directory/front-end/templates/freelancer/dashboard', 'service-location'); ?>
					
				</div>
			</div>
			<div class="wt-updatall">
				<?php wp_nonce_field('wt_post_service_nonce', 'post_service'); ?>
				<i class="ti-announcement"></i>
				<span><?php esc_html_e('Update all the latest changes made by you, by just clicking on “Save &amp; Update button.', 'workreap'); ?></span>
				<a class="wt-btn wt-post-service" data-id="" data-type="add" href="javascript:;"><?php esc_html_e('Save &amp; Update', 'workreap'); ?></a>
			</div>
		</form>
		<script type="text/template" id="tmpl-load-service-addon">
			<li>
				<div class="wt-dashboardboxcontent addon-mainwrap">
					<div class="wt-jobdescription wt-tabsinfo">
						<div class="wt-accordioninnertitle">
							<div class="wt-projecttitle">
								<h3><span class="head-title"><?php esc_html_e('Addon service','workreap');?></span></h3>
							</div>
							<div class="wt-rightarea">
								<a href="javascript:;" class="wt-addinfo wt-edit-addons"><i class="lnr lnr-pencil"></i></a>
								<a href="javascript:;" class="wt-deleteinfo wt-delete-addon"><i class="lnr lnr-trash"></i></a>
							</div>
						</div>
						<div class="wt-formtheme wt-userform wt-userformvtwo addon-service-data elm-display-none">
							<fieldset>
								<div class="form-group">
									<input type="text" name="addons_service[{{data.counter}}][title]" class="form-control" placeholder="<?php esc_attr_e('Addons Service Title','workreap');?>" value="">
								</div>
								<div class="form-group">
									<input type="text" class="wt-numeric" name="addons_service[{{data.counter}}][price]" value="" placeholder="<?php esc_attr_e('Service price','workreap');?>">
								</div>
								<div class="form-group">
									<textarea class="form-control" placeholder="<?php esc_attr_e('Addons service detail','workreap');?>" name="addons_service[{{data.counter}}][description]"></textarea>
								</div>
							</fieldset>
						</div>
					</div>
				</div>
			</li>
		</script>
		<script type="text/template" id="tmpl-load-service-attachments">
			<li class="wt-uploading attachment-new-item wt-doc-parent" id="thumb-{{data.id}}">
				<span class="uploadprogressbar" style="width:0%"></span>
				<span>{{data.name}}</span>
				<em><?php esc_html_e('File size:', 'workreap'); ?> {{data.size}}<a href="javascript:;" class="lnr lnr-cross wt-remove-attachment"></a></em>
				<input type="hidden" class="attachment_url" name="service[service_documents][]" value="{{data.url}}">	
			</li>
		</script>	
	</div>
</div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4">
	<?php if ( is_active_sidebar( 'sidebar-dashboard' ) ) {?>
		<div class="wt-haslayout wt-dashside">
			<?php dynamic_sidebar( 'sidebar-dashboard' ); ?>
		</div>
	<?php }?>
</div>