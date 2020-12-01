<?php
/**
 *
 * The template part for displaying job proposals
 *
 * @package   Workreap
 * @author    Amentotech
 * @link      http://amentotech.com/
 * @since 1.0
 */
global $current_user,$paged;
$user_identity 	 = $current_user->ID;
$url_identity 	 = $user_identity;
$linked_profile  = workreap_get_linked_profile_id($user_identity);
$post_id 		 = $linked_profile;
$meta_query_args = array();
$show_posts = get_option('posts_per_page') ? get_option('posts_per_page') : 10;
$pg_page = get_query_var('page') ? get_query_var('page') : 1; //rewrite the global var
$pg_paged = get_query_var('paged') ? get_query_var('paged') : 1; //rewrite the global var
//paged works on single pages, page - works on homepage
$paged = max($pg_page, $pg_paged);
$order 	 = 'DESC';
$sorting = 'ID';

$proposal_page 			= array();
$allow_proposal_edit 	= '';
if (function_exists('fw_get_db_post_option')) {
	$proposal_page 				= fw_get_db_settings_option('dir_proposal_page');
	$allow_proposal_edit    	= fw_get_db_settings_option('allow_proposal_edit');
}

$proposal_page_id = !empty( $proposal_page[0] ) ? $proposal_page[0] : '';
$submit_proposal  = !empty( $proposal_page_id ) ? get_the_permalink( $proposal_page_id ) : '';
?>
<div class="wt-haslayout wt-job-proposals">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="wt-dashboardbox">
			<div class="wt-dashboardboxtitle">
				<h2><?php esc_html_e('Latest Proposals', 'workreap'); ?></h2>
			</div>
			<div class="wt-dashboardboxcontent wt-rcvproposala emp-manage-details">
				<div class="wt-freelancerholder wt-rcvproposalholder">
					<?php 
						$query_args = array(
							'posts_per_page' 	=> $show_posts,
							'post_type' 		=> 'proposals',
							'orderby' 			=> $sorting,
							'order' 			=> $order,
							'post_status' 		=> array('publish'),
							'author' 			=> $user_identity,
							'paged' 			=> $paged,
							'suppress_filters'  => false
						);

						$pquery = new WP_Query($query_args);
						$count_post = $pquery->found_posts;

						if( $pquery->have_posts() ){?>
							<div class="emp-proposal-list">
								<div class="wt-tabscontenttitle hmargin-top">
									<h2><?php esc_html_e('Received Proposals', 'workreap'); ?></h2>
								</div>
								<div class="wt-managejobcontent">
								<?php
								while ($pquery->have_posts()) : $pquery->the_post();
									global $post;
									$author_id 			= get_the_author_meta( 'ID' );  
									$project_id			= get_post_meta($post->ID,'_project_id', true);
									$_proposal_id 		= get_post_meta($project_id, '_proposal_id', true);
									$job_status			= '';

									$proposal_hiring_status	= get_post_meta($post->ID,'_proposal_status',true);
									$proposal_hiring_status	= !empty($proposal_hiring_status) ? $proposal_hiring_status : '';
									$project_status			= get_post_status($project_id);
													
									if( !empty($_proposal_id) && ( intval($_proposal_id) === $post->ID ) ) {
										$job_status		= get_post_field('post_status',$project_id);
									}
									
									$linked_profile 	= workreap_get_linked_profile_id($author_id);
									
									if (function_exists('fw_get_db_post_option')) {
										$proposal_docs 	= fw_get_db_post_option($post->ID, 'proposal_docs', true);
									} else {
										$proposal_docs	= '';
									}

									$proposal_status		= get_post_meta($project_id,'_milestone',true);
									$proposal_status		= !empty($proposal_status) ? $proposal_status : '';

									$proposal_docs = !empty( $proposal_docs ) && is_array( $proposal_docs ) ?  count( $proposal_docs ) : 0;
													
									$freelancer_avatar = apply_filters(
											'workreap_freelancer_avatar_fallback', workreap_get_freelancer_avatar( array( 'width' => 225, 'height' => 225 ), $linked_profile ), array( 'width' => 225, 'height' => 225 )
										);
									
									$pargs	 = array( 'project_id' => $project_id, 'proposal_id' => $post->ID );
									$submit_proposal  = !empty( $submit_proposal ) ? add_query_arg( $pargs, $submit_proposal ) : '';

									$feedback = get_post_meta( $post->ID, '_feedback', true );
									$feedback_rating = get_post_meta( $post->ID, '_feedback_rating', true );
									$feedback_seen = get_post_meta( $post->ID, '_feedback_seen', true );
									?>
									<div class="wt-userlistinghold wt-featured wt-proposalitem wt-userlistingcontentvtwo" data-id="<?php echo esc_attr($post->ID);?>">
										<div class="wt-proposaldetails">
											<div class="wt-contenthead">
												<div class="wt-title">
													<a><?php the_title();?></a>
													<h2><a target="_blank" href="<?php echo get_the_permalink($project_id);?>"><?php echo get_the_title($project_id);?></a></h2>
												</div>
											</div>										
										</div>
										<div class="wt-rightarea">
											<div class="wt-btnarea">
												<?php if( $job_status === 'hired' ) { ?>
													<span class="wt-btn"><?php esc_html_e('Hired','workreap');?></span>
												<?php }elseif( $job_status === 'completed' ) {?>
													<span class="wt-btn"><?php esc_html_e('Completed','workreap');?></span>
												<?php } else if( $job_status !== 'hired' ) { ?>
													

													<?php  if( !empty($feedback) && !empty($feedback_rating) ){?>
														<a href="javascript:;" class="wt-btn show-feedback <?php echo !$feedback_seen ? 'new-feedback' : '' ?>"
															data-id="<?php echo esc_attr($post->ID);?>">
															<?php echo esc_html_e(!$feedback_seen ? 'New Feedback' : 'Show Feedback','workreap');?>
															<?php if($feedback_seen == false) : ?>
																<span class="badge badge-dark badge-pill">1</span>
															<?php endif; ?>
														</a>
														<div class="modal fade wt-offerpopup-proposal-feedback" tabindex="-1" role="dialog" id="proposalfeedbackmodal-<?php echo esc_attr($post->ID);?>">
															<div class="modal-dialog modal-dialog-centered" role="document">
																<div class="wt-modalcontent modal-content">
																	<div class="wt-popuptitle">
																		<h2><?php esc_html_e('Feedback','workreap');?></h2>
																		<a href="javascript:;" class="wt-closebtn close"><i class="fa fa-close" data-dismiss="modal"></i></a>
																	</div>
																	<div class="modal-body">
																		<form class="chat-form">
																			<div class="wt-formtheme wt-formpopup">
																				<fieldset>
																					<div class="form-group wt-ratingholder form-group-margin" data-ratingtitle="Feedback Rating">
																						<div class="wt-ratepoints wt-ratingbox-<?php echo esc_attr($post->ID); ?>">
																							<div class="counter wt-pointscounter"><?php echo number_format($feedback_rating, 1, '.', ''); ?></div>
																							<div id="jRate-<?php echo esc_attr($post->ID); ?>" class="wt-jrate"></div>
																						</div>
																						<?php
																							$script = "jQuery(function () {
																								var that = this;
																								var toolitup = jQuery('#jRate-" . esc_attr($post->ID) . "').jRate({
																									rating: $feedback_rating,
																									readOnly: true,
																									shapeGap: '6px',
																									startColor: '#fdd003',
																									endColor: '#fdd003',
																									width: 20,
																									height: 20,
																									backgroundColor: '#DFDFE0',
																								});
																							});";
																							wp_add_inline_script('workreap-user-dashboard', $script, 'after');
																						?>
																					</div>
																					<div class="form-group">
																						<div class="alert alert-warning" role="alert">
																							<br/><br/>
																							<?php echo nl2br($feedback); ?><br/>
																							<br/><br/>
																						</div>
																					</div>
																				</fieldset>
																			</div>
																		</form>
																	</div>
																</div>
															</div>
														</div>
													<?php }?>

													<?php  if( !empty($allow_proposal_edit) && $allow_proposal_edit == 'yes' ){?>
														<a target="_blank" href="<?php echo esc_attr($submit_proposal);?>" class="wt-btn"><?php echo esc_html_e('Edit Proposal','workreap');?></a>
													<?php }?>
													
													<?php if( !empty($proposal_hiring_status)  && $proposal_hiring_status === 'pending' && $project_status ==='publish' ) { ?>
														<a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('milestone', $user_identity,'','listing',$post->ID); ?>" class="wt-btn"><?php echo esc_html_e('Accept Milestones and Start Project','workreap');?></a>
													<?php } ?>
														<span class="wt-btn" ><?php esc_html_e('Pending','workreap');?></span>
												<?php } ?>
											</div>											
											<?php // do_action('worrketic_proposal_duration_and_amount',$post->ID);?>
											<?php do_action('worrketic_proposal_cover',$post->ID);?>
											<?php do_action('worrketic_proposal_view_attachments',$post->ID);?>
											<?php do_action('worrketic_proposal_attachments',$post->ID);?>													
										</div>
									</div>		
									<?php 
									endwhile;
									wp_reset_postdata();
								?>
								</div>
							</div>
						<?php } else{
							do_action('workreap_empty_records_html','',esc_html__( 'There are no proposals, have been submitted yet', 'workreap' ),true);
						}
					?>
				</div>
				<?php 	
					if ( !empty($count_post) && $count_post > $show_posts) {
						workreap_prepare_pagination($count_post, $show_posts);
					}
				?>
			</div>
		</div>
	</div>
</div>
<?php get_template_part('directory/front-end/templates/dashboard', 'cover-letter');?>