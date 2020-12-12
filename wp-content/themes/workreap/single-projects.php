<?php 
/**
 *
 * The template used for displaying projects post style
 *
 * @package   Workreap
 * @author    amentotech
 * @link      https://amentotech.com/user/amentotech/portfolio
 * @version 1.0
 * @since 1.0
 */
get_header();

while ( have_posts() ) { the_post(); 
global $post,$current_user; 
if( apply_filters('workreap_system_access','job_base') === true ){
	$author_id 			= get_the_author_meta( 'ID' );
	$company_profile_id	= workreap_get_linked_profile_id($author_id);
	$post_status		= get_post_status($post->ID);
	$employer_avatar 	= apply_filters(
		'workreap_employer_avatar_fallback', workreap_get_employer_avatar(array('width' => 100, 'height' => 100), $author_id), array('width' => 100, 'height' => 100) 
	);    		

	$proposal_page = array();
	if (function_exists('fw_get_db_post_option')) {
		$proposal_page = fw_get_db_settings_option('dir_proposal_page');
		$expiry_date   = fw_get_db_post_option($post->ID, 'expiry_date', true);
		$deadline_date   = fw_get_db_post_option($post->ID, 'deadline', true);
	}

	$proposal_page_id = !empty( $proposal_page[0] ) ? $proposal_page[0] : '';
	$submit_proposal  = !empty( $proposal_page_id ) ? get_the_permalink( $proposal_page_id ) : '';		
	$submit_proposal  = !empty( $submit_proposal ) ? add_query_arg( 'project_id', $post->ID, $submit_proposal ) : '';
	
	$db_project_type	= 'fixed';
	
	if (function_exists('fw_get_db_post_option')) {
		$db_project_type      = fw_get_db_post_option($post->ID,'project_type');
	}
	
	$price_text			= '';
	$project_cost		= '';
	$estimated_hours	= '';
	$job_type_text		= '';

	
	$project_price	= workreap_project_price($post->ID);
	$project_cost	= !empty($project_price['cost']) ? $project_price['cost'] : 0;
	$job_type_text	= !empty($project_price['text']) ? $project_price['text'] : '';
	$price_text		= !empty($project_price['price_text']) ? $project_price['price_text'] : '';
		
	?>
	<div class="wt-haslayout wt-job-single">
		<div class="container">
			<div class="row">
				<div id="wt-twocolumns" class="wt-twocolumns wt-haslayout">
					<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 float-left">
						<div class="wt-proposalholder">							
							<div class="wt-proposalhead">
								<h1><?php the_title(); ?></h1>
								<?php do_action( 'workreap_job_detail_header', $post->ID ) ?>
							</div>
							<?php 
							if( is_user_logged_in() ) {
								$user_type		= apply_filters('workreap_get_user_type', $current_user->ID );
								if( in_array($post_status, array('publish', 'private')) && $user_type === 'freelancer' ) { ?>
									<div class="wt-btnarea">
										<a href="<?php echo esc_url( $submit_proposal ); ?>" class="wt-btn wt-submit-proposal">
											<?php esc_html_e('Send Proposal', 'workreap'); ?>
										</a>
										<?php if( $post_status == 'private' ) { ?>
											<a href="javascript:;" class="wt-btn wt-btn-wraning wt-not-interested" data-project-id="<?php echo $post->ID; ?>">
												<?php esc_html_e('Not Interested', 'workreap'); ?>
											</a>
										<?php } ?>
									</div>
								<?php } ?> 
							<?php } else { ?>
								<div class="wt-btnarea"><a href="javascript:;" class="wt-btn wt-submit-proposal"><?php esc_html_e('Send Proposal', 'workreap'); ?></a></div>
							<?php } ?>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-7 col-xl-8 float-left">
						<div class="wt-projectdetail-holder">
							<?php if( '' !== $post->post_content ) {?>
								<div class="wt-title">
									<h3><?php esc_html_e( 'Project detail', 'workreap' ); ?></h3>
								</div>
								<div class="wt-projectdetail">
									<div class="wt-description"><?php the_content(); ?></div>
								</div>
							<?php }?>
							<?php do_action( 'workreap_display_project_bundle_html', $post->ID, esc_html__('Bundle', 'workreap') ); ?>
							<?php // do_action( 'workreap_print_skills_html', $post->ID, esc_html__('Skills Required', 'workreap'),5000 ); ?>	
							<?php do_action( 'workreap_display_categories_html', $post->ID); ?>
							<?php do_action( 'workreap_display_langauges_html', $post->ID); ?>
							<?php do_action( 'workreap_display_required_freelancer_html', $post->ID); ?>
							<?php do_action('workreap_job_detail_documents', $post->ID); ?>
							<?php if( !empty($deadline_date) && strtotime($deadline_date) > 0 ){?>
								<div class="wt-skillsrequired">
									<div class="wt-title">
										<h3><?php esc_html_e('Project Completion deadline', 'workreap'); ?></h3>
									</div>
									<div class="wt-deadline wt-haslayout">
										<span><?php echo date_i18n( get_option('date_format'), strtotime($deadline_date));?></span>                     
									</div>
								</div>
							<?php }?>
							<?php
								// If comments are open or we have at least one comment, load up the comment template
								if ( comments_open() || get_comments_number() ) {
									comments_template();
								}
							?>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-5 col-xl-4 float-left">
						<aside id="wt-sidebar" class="wt-sidebar">
							<div class="wt-proposalsr">
								<div class="wt-proposalsrcontent sproject-price">
									<span class="wt-proposalsicon"><i class="fa fa-angle-double-down"></i><i class="fa fa-money"></i></span>
									<div class="wt-title">
										<h3><?php echo do_shortcode( $project_cost );?></h3>
										<span><?php  echo do_shortcode($price_text); ?><?php if( !empty( $job_type_text ) ) echo do_shortcode($job_type_text);?></span>
									</div>
								</div>
								<?php if( !empty( $expiry_date ) && strtotime($expiry_date) > 0 ){
										if( current_time( 'timestamp' ) > strtotime($expiry_date) ){
											$status	=  esc_html__('Expired','workreap');
										} else{
											$status	=  date_i18n( get_option('date_format'), strtotime($expiry_date));
										}
									?>
									<div class="wt-proposalsrcontent sproject-price">
										<span class="wt-proposalsicon"><i class="fa fa-angle-double-down"></i><i class="fa fa-hourglass-half"></i></span>
										<div class="wt-title">
											<h3><?php esc_html_e('Expiry Date', 'workreap'); ?></h3>
											<span><?php echo esc_html( $status); ?></span>
										</div>
									</div>	
								<?php }?>
								<?php do_action( 'workreap_show_proposals_count', $post->ID); ?>
								<?php do_action( 'workreap_get_qr_code','project',intval( $post->ID ) );?>
								<div class="wt-clicksavearea">
									<span><?php esc_html_e('Project ID', 'workreap'); ?>:&nbsp;<?php echo sprintf('%08d', intval( $post->ID )); ?></span>
									<?php  do_action('workreap_save_project_html', $post->ID, 'v1'); ?>	
								</div>
							</div>
							<?php do_action('workreap_project_company_box', intval($company_profile_id)); ?>	
	
							<?php if (function_exists('workreap_prepare_project_social_sharing')) { workreap_prepare_project_social_sharing(false, esc_html__('Share this project', 'workreap'), 'true', '', $employer_avatar); }?>	
							<?php do_action('workreap_report_post_type_form',$post->ID,'project'); ?>
						</aside>
					</div>
				</div>
			</div>
		</div>	
	</div>
	<?php } else { ?>
		<div class="container">
		  <div class="wt-haslayout page-data">
			<?php  Workreap_Prepare_Notification::workreap_warning(esc_html__('Restricted Access', 'workreap'), esc_html__('You have not any privilege to view this page.', 'workreap'));?>
		  </div>
		</div>
	<?php
	}
}
get_footer();
