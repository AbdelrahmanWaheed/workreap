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

$all_skills 	 	= workreap_get_all_skills();
$job_duration       = worktic_job_duration_list();
$english_level      = worktic_english_level_list();
$freelancer_level   = worktic_freelancer_level_list();
$project_level   	= workreap_get_project_level();
$job_type 		 	= workreap_get_job_type();
$cats			    = workreap_get_taxonomy_array('project_cat');
$languages		    = workreap_get_taxonomy_array('languages');
$experiences		= workreap_get_taxonomy_array('project_experience');
$job_options		= function_exists('workreap_get_job_option') ? workreap_get_job_option() : array();

if (function_exists('fw_get_db_settings_option')) {
    $job_option_setting         = fw_get_db_settings_option('job_option', $default_value = null);
	$multiselect_freelancertype  = fw_get_db_settings_option('multiselect_freelancertype', $default_value = null);
	$job_experience_option  	= fw_get_db_settings_option('job_experience_option', $default_value = null);
	$job_price_option           = fw_get_db_settings_option('job_price_option', $default_value = null);
	$milestone         			= fw_get_db_settings_option('job_milestone_option', $default_value = null);
}

$multiselect_freelancertype = !empty($multiselect_freelancertype) && $multiselect_freelancertype === 'enable' ?  'multiple': '';
$job_experience_option 		= !empty($job_experience_option['enable']['multiselect_experience']) && $job_experience_option['enable']['multiselect_experience'] === 'multiselect' ?  'multiple class=chosen-select': '';
$job_price_option 			= !empty($job_price_option) ? $job_price_option : '';
$job_option_setting 		= !empty($job_option_setting) ? $job_option_setting : '';
$milestone					= !empty($milestone['gadget']) ? $milestone['gadget'] : '';

$description 		= '';
$name 				= 'job[description]';								
$settings 			= array('media_buttons' => false,'textarea_name'=> $name,'editor_class'=> 'customwp_editor','media_buttons','editor_height'=>300 );

if(!empty($job_price_option) && $job_price_option === 'enable') {
	$place_holder	= esc_attr__('Minimum Price','workreap');
} else{
	$place_holder	= esc_attr__('Project Price','workreap');
}

$hide = true;

?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-9 float-left">
	<div class="wt-haslayout wt-post-job-wrap">
		<form class="post-job-form wt-haslayout">
			<div class="wt-dashboardbox">
				<div class="wt-dashboardboxtitle">
					<h2><?php esc_html_e('Post a job','workreap');?></h2>
				</div>
				<div class="wt-dashboardboxcontent">
					<?php 
						if( apply_filters('workreap_is_job_posting_allowed','wt_jobs', $current_user->ID) === false ){
							$link		= Workreap_Profile_Menu::workreap_profile_menu_link('package', $current_user->ID,true);
							$message	= esc_html__('You’ve consumed all you points to add new job.','workreap');
							$title		= esc_html__('Alert :','workreap');
							Workreap_Prepare_Notification::workreap_warning($title, $message, $link, esc_html__("Buy Now",'workreap'));		
						}
					?>
					<div class="wt-jobdescription wt-tabsinfo">
						<div class="wt-tabscontenttitle">
							<h2><?php esc_html_e('Job description','workreap');?></h2>
						</div>
						<div class="wt-formtheme wt-userform wt-userformvtwo">
							<fieldset>
								<div class="form-group">
									<input type="text" name="job[title]" class="form-control" placeholder="<?php esc_attr_e('Job Title','workreap');?>">
								</div>
								<?php if(!$hide) { ?>
									<div class="form-group form-group-half wt-formwithlabel">
										<span class="wt-selects toolip-wrapo">
											<select name="job[project_level]" class="chosen-select">
												<option value=""><?php esc_html_e('Select project level','workreap');?></option>
												<?php 
												if( !empty( $project_level ) ){
													foreach( $project_level as $key => $level ){?>
													<option value="<?php echo esc_attr( $key );?>"><?php echo esc_html( $level );?></option>
												<?php }}?>
											</select>
											<?php do_action('workreap_get_tooltip','element','project_level');?>
										</span>
									</div>
									<div class="form-group form-group-half wt-formwithlabel">
										<span class="wt-selects toolip-wrapo">
											<select name="job[project_duration]" class="chosen-select">
												<option value=""><?php esc_html_e('Select job duration','workreap');?></option>
												<?php 
												if( !empty( $job_duration ) ){
													foreach( $job_duration as $key => $level ){?>
													<option value="<?php echo esc_attr( $key );?>"><?php echo esc_html( $level );?></option>
												<?php }}?>
											</select>
											<?php do_action('workreap_get_tooltip','element','project_duration');?>
										</span>
									</div>
									<div class="form-group form-group-half wt-formwithlabel">
										<span class="wt-selects toolip-wrapo">
											<select name="job[freelancer_level][]" class="chosen-select" data-placeholder="<?php esc_attr_e('Select freelancer type','workreap');?>" <?php echo esc_attr( $multiselect_freelancertype );?> >
												<option value=""><?php esc_html_e('Select freelancer type','workreap');?></option>
												<?php 
												if( !empty( $freelancer_level ) ){
													foreach( $freelancer_level as $key => $level ){?>
													<option value="<?php echo esc_attr( $key );?>"><?php echo esc_html( $level );?></option>
												<?php }}?>
											</select>
											<?php do_action('workreap_get_tooltip','element','freelancer_level');?>
										</span>
									</div>
									<div class="form-group form-group-half wt-formwithlabel">
										<span class="wt-selects toolip-wrapo">
											<select name="job[english_level]" class="chosen-select">
												<option value=""><?php esc_html_e('Select english level','workreap');?></option>
												<?php 
												if( !empty( $english_level ) ){
													foreach( $english_level as $key => $level ){?>
													<option value="<?php echo esc_attr( $key );?>"><?php echo esc_html( $level );?></option>
												<?php }}?>
											</select>
											<?php do_action('workreap_get_tooltip','element','english_level');?>
										</span>
									</div>
									
									<?php if(!empty($job_option_setting) && $job_option_setting === 'enable' ){ ?>
										<div class="form-group form-group-half wt-formwithlabel">
											<span class="wt-selects toolip-wrapo">
												<select name="job[job_option]" class="chosen-select">
													<option value=""><?php esc_html_e('Project location type','workreap');?></option>
													<?php 
													if( !empty( $job_options ) ){
														foreach( $job_options as $key => $val ){?>
														<option value="<?php echo esc_attr( $key );?>"><?php echo esc_html( $val );?></option>
													<?php }}?>
												</select>
												<?php do_action('workreap_get_tooltip','element','job_option');?>
											</span>
										</div>
										<div class="form-group form-group-half wt-formwithlabel">
											<span class="wt-selects toolip-wrapo">
												<select name="job[experiences][]"  data-placeholder="<?php esc_attr_e('Years of Experience Preferred','workreap');?>" <?php echo esc_attr( $job_experience_option );?> class="chosen-select">
													<option value=""><?php esc_html_e('Years of Experience Preferred','workreap');?></option>
													<?php 
													if( !empty( $experiences ) ){
														foreach ($experiences as $key => $item) {
															$term_id   = $item->term_id; ?>
																<option value="<?php echo esc_attr( $term_id ); ?>"><?php echo esc_html( $item->name ); ?></option>
														<?php } ?>
													<?php } ?>
												</select>
												<?php do_action('workreap_get_tooltip','element','experiences');?>
											</span>
										</div>
									<?php } ?>
									<div class="form-group wt-formwithlabel form-group-half job-expirydate-input toolip-wrapo">
										<input type="text" name="job[expiry_date]" class="form-control wt-date-pick-job"  value="" placeholder="<?php esc_attr_e('Project Expiry Date (optional)','workreap');?>">
										<?php do_action('workreap_get_tooltip','element','expiry_date');?>
									</div>
									<div class="form-group wt-formwithlabel form-group-half job-expirydate-input toolip-wrapo">
										<input type="text" name="job[deadline]" class="form-control wt-date-pick-job" value="" placeholder="<?php esc_attr_e('Project deadline date (optional)','workreap');?>">
										<?php do_action('workreap_get_tooltip','element','deadline');?>
									</div>
								<?php } else { ?>
									<input type="hidden" name="job[project_level]" value="basic"> 
								<?php } ?>
							</fieldset>
						</div>
					</div>
					<?php if(!$hide) : ?>
						<div class="wt-category-holder wt-tabsinfo">
							<div class="wt-tabscontenttitle">
								<h2><?php esc_html_e('Prices','workreap');?></h2>
							</div>
							<div class="wt-formtheme wt-userform wt-userformvtwo">
								<fieldset>
									<?php if(!$hide) { ?>
										<div class="form-group form-group-half wt-formwithlabel">
											<span class="wt-selects">
												<select name="job[project_type]" class="wt-job-type chosen-select">
													<option value=""><?php esc_html_e('Select job type','workreap');?></option>
													<?php if( !empty( $job_type ) ){
														foreach( $job_type as $key => $level ){?>
														<option <?php selected('fixed',$key);?> data-key="<?php echo esc_attr( $key );?>" value="<?php echo esc_attr( $key );?>"><?php echo esc_html( $level );?></option>
													<?php }}?>
												</select>
											</span>
										</div>
										<div class="form-group form-group-half wt-formwithlabel job-perhour-input" style="display: none;">
											<input type="text" name="job[hourly_rate]" value=""  class="form-control wt-numeric" placeholder="<?php esc_attr_e('Minimum Price','workreap');?>">
										</div>
									<?php } else { ?>
										<input type="hidden" name="job[project_type]" value="fixed">
									<?php } ?>
									<div class="form-group wt-formwithlabel job-cost-input">
										<input type="text" name="job[project_cost]" value=""  class="form-control wt-numeric" placeholder="<?php esc_attr_e('Project Cost','workreap');?>">
									</div>
									<?php if(!$hide) { ?>
										<?php if(!empty($job_price_option) && $job_price_option === 'enable') {?>
											<div class="form-group form-group-half wt-formwithlabel">
												<input type="text" name="job[max_price]" value="" class="form-control wt-numeric" placeholder="<?php esc_attr_e('Maximum price','workreap');?>">
											</div>
										<?php }?>
										<div class="form-group  form-group-half wt-formwithlabel job-perhour-input" style="display: none;">
											<input type="text" name="job[estimated_hours]" value=""  class="form-control wt-numeric" placeholder="<?php esc_attr_e('Estimated hours','workreap');?>">
										</div>
									<?php } ?>
									
								</fieldset>
							</div>
						</div>
					<?php endif; ?>
					<?php if(!empty($milestone) && $milestone ==='enable' ){ ?>
						<div class="job-cost-input <?php echo esc_attr($fixedClass);?>">
							<div class="wt-tabscontenttitle">
								<h2><?php esc_html_e('Will this project require milestone payments?','workreap');?></h2>
								<div class="wt-rightarea">
									<div class="wt-on-off float-right">
										<input type="hidden" value="off" name="job[is_milestone]">
										<input type="checkbox" value="on" id="milestone-on" name="job[is_milestone]">
										<label for="milestone-on"><i></i></label>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
					<div class="wt-category-holder wt-tabsinfo wt-dropdown-categories">
						<div class="wt-tabscontenttitle">
							<h2><?php esc_html_e('Job Categories','workreap');?></h2>
						</div>
						<div class="wt-divtheme wt-userform wt-userformvtwo">
							<div class="form-group">
								<?php $project_cat = isset($_GET['category']) ? intval($_GET['category']) : ''; ?>
								<?php do_action('workreap_get_categories_list', 'job[categories][]', $project_cat);?>
							</div>
						</div>
					</div>
					<?php if(!$hide) { ?>
						<div class="wt-language-holder wt-tabsinfo">
							<div class="wt-tabscontenttitle">
								<h2><?php esc_html_e('Languages','workreap');?></h2>
							</div>
							<div class="wt-divtheme wt-userform wt-userformvtwo">
								<div class="form-group">
									<select data-placeholder="<?php esc_attr_e('Select Languages','workreap');?>" multiple name="job[languages][]"  class="chosen-select">
										<?php 
											if( !empty( $languages ) ){							
												foreach ($languages as $key => $item) {
													$term_id   = !empty($item->term_id ) ? $item->term_id : '';									
													?>
													<option value="<?php echo esc_attr( $term_id ); ?>"><?php echo esc_html( $item->name ); ?></option>
													<?php 
												}
											}
										?>		
									</select>
								</div>
							</div>
						</div>
					<?php } ?>
					<div class="wt-jobdetails wt-tabsinfo">
						<div class="wt-tabscontenttitle">
							<h2><?php esc_html_e('Job Type','workreap');?></h2>
						</div>
						<div class="wt-formtheme wt-userform wt-userformvtwo">
							<fieldset>
								<div class="form-group">
									<span class="wt-selects toolip-wrapo">
										<select name="job[type]" id="job_project_type">
											<option value=""><?php esc_html_e('Select job type', 'workreap');?></option>
											<option value="one-to-one"><?php esc_html_e('One To One', 'workreap');?></option>
											<option value="contest"><?php esc_html_e('Contest', 'workreap');?></option>
										</select>
									</span>
								</div>
								<div class="form-group" id="form-group-freelancers" style="display: none;">
									<?php workreap_show_freelancers_list('job[freelancers][]'); ?>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="wt-jobdetails wt-tabsinfo">
						<div class="wt-tabscontenttitle">
							<h2><?php esc_html_e('Job Details','workreap');?></h2>
						</div>
						<div class="wt-formtheme wt-userform wt-userformvtwo">
							<fieldset>
								<div class="form-group">
									<?php wp_editor($description, 'job_text_area', $settings);?>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="wt-jobskills wt-tabsinfo">
						<div class="wt-tabscontenttitle">
							<h2><?php esc_html_e('Skills Required','workreap');?></h2>
						</div>
						<div class="wt-divtheme wt-userform wt-userformvtwo">
							<div class="form-group">
								<select data-placeholder="<?php esc_attr_e('Skills','workreap');?>" name="skills"  class="chosen-select skill-dynamic-field">
									<?php 
										if( !empty( $all_skills ) ){							
											foreach ($all_skills as $key => $value) {
												$term_data = get_term_by( 'slug', $value['slug'], 'skills' );
												$term_id   = $term_data->term_id;									
												?>
												<option value="<?php echo esc_attr( $term_id ); ?>"><?php echo esc_html( $value['name'] ); ?></option>
												<?php 
											}
										}
									?>	
								</select>
							</div>
							<div class="form-group wt-btnarea">
								<a href="javascript:;" class="wt-btn add-job-skills"><?php esc_html_e('Add Skills','workreap');?></a>
							</div>
							<div class="form-group wt-myskills">
								<ul class="jobskills-wrap wt-haslayout"></ul>
							</div>
						</div>
					</div>
					<div class="wt-attachmentsholder">
						<div class="wt-tabscontenttitle">
							<h2><?php esc_html_e('Upload Relevant Project Files','workreap');?></h2>
						</div>
						<div class="wt-formtheme wt-formprojectinfo wt-formcategory">
							<fieldset>
								<div class="form-group form-group-label" id="wt-job-container">
									<div class="wt-labelgroup" id="job-drag">
										<label for="file" class="wt-job-file">
											<span class="wt-btn" id="job-btn"><?php esc_html_e('Select File', 'workreap'); ?></span>								
										</label>
										<span><?php esc_html_e('Drop files here to upload', 'workreap'); ?></span>
										<em class="wt-fileuploading"><?php esc_html_e('Uploading', 'workreap'); ?><i class="fa fa-spinner fa-spin"></i></em>
									</div>
								</div>
								<div class="form-group">
									<ul class="wt-attachfile uploaded-placeholder"></ul>
								</div>
							</fieldset>
						</div>
						<?php if(!$hide) { ?>
							<div class="wt-tabscontenttitle">
								<h2><?php esc_html_e('Attachments','workreap');?></h2>
								<div class="wt-rightarea">
									<div class="wt-on-off float-right">
										<input type="hidden" value="off" name="job[show_attachments]">
										<input type="checkbox" value="on" id="hide-on" name="job[show_attachments]">
										<label for="hide-on"><i></i></label>
									</div>
									<span><?php esc_html_e('Show “Attachments” on job detail page','workreap');?></span>
								</div>
							</div>
						<?php } else { ?>
							<input type="hidden" value="on" name="job[show_attachments]">
						<?php } ?>
						<?php if( !$hide && apply_filters('workreap_is_job_posting_allowed','wt_jobs', $current_user->ID,'yes') === true ){ ?>
							<div class="wt-tabscontenttitle">
								<h2><?php esc_html_e('Featured job','workreap');?></h2>
								<div class="wt-rightarea">
									<div class="wt-on-off float-right">
										<input type="hidden" value="off" name="job[is_featured]">
										<input type="checkbox" value="on" id="featured-on" name="job[is_featured]">
										<label for="featured-on"><i></i></label>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
					<?php
						if(!$hide) {
							get_template_part('directory/front-end/templates/employer/dashboard', 'job-location');
						}
						do_action('workreap_add_fields');
					?>
				</div>
			</div>
			<div class="wt-updatall">
				<?php wp_nonce_field('wt_post_job_nonce', 'post_job'); ?>
				<i class="ti-announcement"></i>
				<span><?php esc_html_e('Update all the latest changes made by you, by just clicking on “Save &amp; Update button.', 'workreap'); ?></span>
				<a class="wt-btn wt-post-job" data-id="" data-type="add" href="javascript:;"><?php esc_html_e('Save &amp; Update', 'workreap'); ?></a>
			</div>
		</form>
		<script type="text/template" id="tmpl-load-job-skill">
			<li class="wt-skill-list">
				<div class="wt-dragdroptool">
					<a href="javascript:" class="lnr lnr-menu"></a>
				</div>
				<span class="skill-dynamic-html">{{data.name}}</span>
				<span class="skill-dynamic-field">
					<input type="text" name="job[skills][]" value="{{data.value}}">
				</span>
				<div class="wt-rightarea">
					<a href="javascript:;" class="wt-deleteinfo wt-delete-skill"><i class="lnr lnr-trash"></i></a>
				</div>
			</li>
		</script>
		<script type="text/template" id="tmpl-load-job-attachments">
			<li class="wt-uploading attachment-new-item wt-doc-parent" id="thumb-{{data.id}}">
				<span class="uploadprogressbar" style="width:0%"></span>
				<span>{{data.name}}</span>
				<em><?php esc_html_e('File size:', 'workreap'); ?> {{data.size}}<a href="javascript:;" class="lnr lnr-cross wt-remove-attachment"></a></em>
				<input type="hidden" class="attachment_url" name="job[project_documents][]" value="{{data.url}}">	
			</li>
		</script>	
	</div>
</div>