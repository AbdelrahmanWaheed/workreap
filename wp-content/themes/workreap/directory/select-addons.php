<?php
/**
 *
 * Template Name: Addons Selection
 *
 * @package   Workreap
 * @author    Amentotech
 * @link      http://amentotech.com/
 * @since 1.0
 */
get_header();

$project_id = isset($_GET['project']) ? $_GET['project'] : 0;

the_post();
?>
<div class="wt-haslayout">
	<div class="container">
		<div class="wt-sc-packages-list wt-packages-wrap">
			<div class="row justify-content-center">
				<?php if ( $project_id == 0 ) {
					$message	= esc_html__('You can not open this page directy.', 'workreap');
					$title		= esc_html__('Error :','workreap');
					Workreap_Prepare_Notification::workreap_error($title, $message);			
				} else { ?>
					<div class="col-md-12">
						<div class="wt-sectionheadvtwo wt-textcenter">
							<div class="wt-sectiontitlevtwo">
								<h2><?php the_title(); ?></h2>
							</div>
							<div class="wt-description">
								<?php the_content(); ?>
							</div>
						</div>
					</div>
					<div class="col-lg-8 col-md-12 project-addons">
						<form>
							<div class="wt-dashboardboxcontent">
								<div class="wt-tabscontenttitle">
									<h2><?php esc_html_e('Private Project ?','workreap');?></h2>
									<div class="wt-rightarea">
										<div class="wt-on-off float-right">
											<input type="checkbox" value="on" id="private-project" name="addon[private_project]" class="toggle" data-toggle-id="private-project-fees">
											<label for="private-project"><i></i></label>
										</div>
									</div>
								</div>
								<div class="wt-formtheme wt-userform wt-userformvtwo">
									<ul>
										<li>The project will not be shown after it is completed.</li>
										<li id="private-project-fees" style="display:none;">Additional fees: $100</li>
									</ul>
								</div>
							</div>
							<div class="wt-dashboardboxcontent">
								<div class="wt-tabscontenttitle">
									<h2><?php esc_html_e('Faster Project ?','workreap');?></h2>
									<div class="wt-rightarea">
										<div class="wt-on-off float-right">
											<input type="checkbox" value="on" id="faster-project" name="addon[faster_project]" class="toggle" data-toggle-id="fieldset-project-deadline">
											<label for="faster-project"><i></i></label>
										</div>
									</div>
								</div>
								<div class="wt-formtheme wt-userform wt-userformvtwo">
									<ul>
										<li>The project will be open for proposals for a short period to start working as fast as possible.</li>
									</ul>
									<fieldset id="fieldset-project-deadline" style="display:none;">
										<div class="form-group">
											<span class="wt-select">
												<select name="addon[project_deadline]">
													<option value="" disabled=""><?php esc_html_e("Select Deadline","workreap");?></option>
													<option value="one-day">One Day ($100)</option>
													<option value="three-day">Three Days ($120)</option>
													<option value="five-day">Five Days ($140)</option>
												</select>
											</span>
										</div>
									</fieldset>
								</div>
							</div>
							<div class="wt-dashboardboxcontent">
								<div class="wt-tabscontenttitle">
									<h2><?php esc_html_e('Participation Fees ?','workreap');?></h2>
									<div class="wt-rightarea">
										<div class="wt-on-off float-right">
											<input type="checkbox" value="on" id="participation-fees" name="addon[participation_fees]" class="toggle" 
												data-toggle-id="fieldset-participation-fees">
											<label for="participation-fees"><i></i></label>
										</div>
									</div>
								</div>
								<div class="wt-formtheme wt-userform wt-userformvtwo">
									<ul>
										<li>The project fees will distributed among multiple of freelancers.</li>
									</ul>
									<fieldset id="fieldset-participation-fees" style="display:none;">
										<div class="form-group">
											<span class="wt-select">
												<select name="addon[participation]">
													<option value="" disabled=""><?php esc_html_e("Select Participation","workreap");?></option>
													<option value="two-freelancer">Two Freelancers ($100)</option>
													<option value="three-freelancer">Three Freelancers ($120)</option>
													<option value="four-freelancer">Four Freelancers ($140)</option>
												</select>
											</span>
										</div>
									</fieldset>
								</div>
							</div>
							<div class="wt-dashboardboxcontent">
								<?php wp_nonce_field('wt_select_job_addons_nonce', 'post_job'); ?>
								<a class="wt-btn wt-select-addons" data-project-id="<?php echo intval($project_id);?>" href="javascript:;">
									<?php esc_html_e('Save &amp; Continue', 'workreap'); ?>
								</a>
							</div>
						</form>
					</div>
					<div class="col-lg-4 col-md-12"></div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php
get_footer();
