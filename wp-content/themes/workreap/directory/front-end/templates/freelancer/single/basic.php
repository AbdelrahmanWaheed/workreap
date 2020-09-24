<?php
/**
 *
 * The template used for displaying freelancer post basics
 *
 * @package   Workreap
 * @author    amentotech
 * @link      https://amentotech.com/user/amentotech/portfolio
 * @version 1.0
 * @since 1.0
 */

global $post,$current_user;

$post_id 					= $post->ID; 
$user_id					= workreap_get_linked_profile_id( $post_id, 'post' );
if (function_exists('fw_get_db_post_option')) {
	$tag_line			= fw_get_db_post_option($post_id, 'tag_line', true);
	$freelancer_stats 	= fw_get_db_settings_option('freelancer_stats');
	$application_access = fw_get_db_settings_option('application_access');
} else {
	$tag_line			= "";
	$freelancer_stats	= "show";
}

$application_access	= !empty( $application_access ) ? $application_access : '';

$basic_content	= 'withoutstats';
if( isset( $freelancer_stats ) && $freelancer_stats === 'show' ){
	$basic_content	= 'withstats';
}

$content					= get_the_content();
$completed_jobs				= workreap_count_posts_by_meta( 'projects' ,'', '_freelancer_id', $post_id, 'completed');
$total_completed_jobs		= !empty($completed_jobs) ? $completed_jobs : 0;

$ongoing_jobs				= workreap_count_posts_by_meta( 'projects' ,'', '_freelancer_id', $post_id, 'hired');
$total_ongoing_jobs			= !empty($ongoing_jobs) ? $ongoing_jobs : 0;

$cancelled_jobs				= workreap_count_posts_by_meta( 'proposals' ,$user_id, '', '', 'cancelled');
$total_cancelled_jobs		= !empty($cancelled_jobs) ? $cancelled_jobs : 0;

$earnings					= workreap_get_sum_payments_freelancer($user_id,'completed','amount');
$earnings					= !empty($earnings) ? $earnings : 0;


$completed_services			= workreap_count_posts_by_meta( 'services-orders' ,'', '_service_author', $user_id, 'completed');
$total_completed_services	= !empty($completed_services) ? $completed_services : 0;

$ongoing_services			= workreap_count_posts_by_meta( 'services-orders' ,'', '_service_author', $user_id, 'hired');
$total_ongoing_services		= !empty($ongoing_services) ? $ongoing_services : 0;

$cancelled_services			= workreap_count_posts_by_meta( 'services-orders' ,'', '_service_author', $user_id, 'cancelled');
$total_cancelled_services	= !empty($cancelled_services) ? $cancelled_services : 0;

$freelancer_avatar = apply_filters(
		'workreap_freelancer_avatar_fallback', workreap_get_freelancer_avatar( array( 'width' => 225, 'height' => 225 ), $post_id ), array( 'width' => 225, 'height' => 225 )
	);

$socialmediaurls	= array();
if( function_exists('fw_get_db_settings_option')  ){
	$socialmediaurl	= fw_get_db_settings_option('freelancer_social_profile_settings', $default_value = null);
}

$socialmediaurl 		= !empty($socialmediaurls) ? $socialmediaurls['gadget'] : '';

$social_settings	= array();
if (function_exists('workreap_get_social_media_icons_list')){
	$social_settings	= workreap_get_social_media_icons_list('no');
}

$applicationClass	= 'wt-access-both';
if( $application_access === 'job_base' ){
	$applicationClass	= 'wt-access-jobs';
} else if( $application_access === 'service_base' ){
	$applicationClass	= 'wt-access-services';
}

$freelancer_title 		= workreap_get_username('',$post_id); 
?>
<div class="container">
	<div class="row">
		<div class="col-12 col-sm-12 col-md-12 col-lg-12 float-left">
			<div class="wt-userprofileholder">
				<?php do_action('workreap_featured_freelancer_tag',$user_id);?>
				<div class="col-12 col-sm-12 col-md-12 col-lg-3 float-left">
					<div class="row">
						<div class="wt-userprofile">
							<figure>
								<img src="<?php echo esc_url( $freelancer_avatar );?>" alt="<?php esc_attr_e('freelancer','workreap');?>">
								<?php echo do_action('workreap_print_user_status',$user_id);?>
							</figure>
							<div class="wt-title">
								<h1><i class="fa fa-check-circle"></i><?php echo esc_attr( $freelancer_title );?></h1>
								<div class="wt-sinle-pmeta">
									<?php do_action('workreap_freelancer_get_reviews',$post_id,'v1');?>
									<span class="wtmember-since"><?php esc_html_e('Member since','workreap');?>&nbsp;<?php echo get_the_date( get_option('date_format') );?></span>
									<?php
										if(!empty($social_settings) && !empty($socialmediaurl) && $socialmediaurl === 'enable') { ?>
											<ul class="wt-socialiconssimple">
												<?php
													foreach($social_settings as $key => $val ) {
														$icon		= !empty( $val['icon'] ) ? $val['icon'] : '';
														$color			= !empty( $val['color'] ) ? $val['color'] : '#484848';

														$enable_value   = !empty($socialmediaurls['enable'][$key]['gadget']) ? $socialmediaurls['enable'][$key]['gadget'] : '';
														if( !empty($enable_value) && $enable_value === 'enable' ){ 
															$social_url	= '';
															if( function_exists('fw_get_db_post_option') ){
																$social_url	= fw_get_db_post_option($post_id, $key, null);
															}
															$social_url	= !empty($social_url) ? $social_url : '';
															if(!empty($social_url)) {?>
																<li>
																	<a href="<?php echo esc_url($social_url); ?>" target="_blank">
																		<i class="wt-icon <?php echo esc_attr( $icon );?>" style="color:<?php echo esc_attr( $color );?>"></i>
																	</a>
																</li>
															<?php } ?>
														<?php } ?>
													<?php } ?>
											</ul>
										<?php } ?>
								</div>
							</div>
							<?php if( isset( $freelancer_stats ) && $freelancer_stats === 'hide' ){?>
							<div class="wt-description <?php echo esc_attr( $basic_content );?>">
								<?php if( is_user_logged_in() ) {?>
									<a class="wt-btn wt-send-offers" href="javascript:;">
										<?php esc_html_e('Send Offer','workreap');?>
									</a>
								<?php } else {?>
									<a class="wt-btn wt-loginfor-offer" href="javascript:;">
										<?php esc_html_e('Send  Offer','workreap');?>
									</a>
								<?php } ?>
							</div>
							<?php }?>
						</div>
					</div>
				</div>
				<div class="col-12 col-sm-12 col-md-12 col-lg-9 float-left <?php echo esc_attr( $basic_content );?>">
					<div class="row">
						<div class="wt-profile-content-holder">
							<div class="wt-proposalhead wt-userdetails">
								<?php if( !empty( $tag_line ) ){?><h2><?php echo esc_html(stripslashes($tag_line));?></h2><?php } ?>
								<?php do_action('workreap_freelancer_breadcrumbs',$post_id,'wt-userlisting-breadcrumbvtwo');?>
								<?php if( !empty( $content ) ){?>
									<div class="wt-description">
										<?php the_content();?>
									</div>
								<?php } ?>
							</div>
							<?php if( isset( $freelancer_stats ) && $freelancer_stats === 'show' ){?>
								<div id="wt-statistics" class="wt-statistics wt-profilecounter <?php echo esc_attr( $applicationClass );?>">
									<?php if( $application_access === 'job_base' ){?>
										<div class="wt-statisticcontent wt-countercolor1">
											<h3 ><?php echo intval($total_ongoing_jobs);?></h3>
											<h4><?php esc_html_e('Ongoing','workreap');?>&nbsp;<br><?php esc_html_e('Projects','workreap');?></h4>
										</div>
										<div class="wt-statisticcontent wt-countercolor2">
											<h3><?php echo intval($total_completed_jobs);?></h3>
											<h4><?php esc_html_e('Completed','workreap');?>&nbsp;<br><?php esc_html_e('Projects','workreap');?></h4>
										</div>
										<div class="wt-statisticcontent wt-countercolor4">
											<h3><?php echo intval($total_cancelled_jobs);?></h3>
											<h4><?php esc_html_e('Cancelled','workreap');?>&nbsp;<br><?php esc_html_e('Projects','workreap');?></h4>
										</div>
									<?php }else if( $application_access === 'service_base'){?>
										<div class="wt-statisticcontent wt-countercolor1">
											<h3 ><?php echo intval($total_ongoing_services);?></h3>
											<h4><?php esc_html_e('Ongoing','workreap');?>&nbsp;<br><?php esc_html_e('Services','workreap');?></h4>
										</div>
										<div class="wt-statisticcontent wt-countercolor2">
											<h3><?php echo intval($total_completed_services);?></h3>
											<h4><?php esc_html_e('Completed','workreap');?>&nbsp;<br><?php esc_html_e('Services','workreap');?></h4>
										</div>
										<div class="wt-statisticcontent wt-countercolor4">
											<h3><?php echo intval($total_cancelled_services);?></h3>
											<h4><?php esc_html_e('Cancelled','workreap');?>&nbsp;<br><?php esc_html_e('Services','workreap');?></h4>
										</div>
									<?php } else{?>
										<div class="wt-statisticcontent wt-countercolor1">
											<h3 ><?php echo intval($total_ongoing_jobs);?></h3>
											<h4><?php esc_html_e('Ongoing','workreap');?>&nbsp;<br><?php esc_html_e('Projects','workreap');?></h4>
										</div>
										<div class="wt-statisticcontent wt-countercolor2">
											<h3><?php echo intval($total_completed_jobs);?></h3>
											<h4><?php esc_html_e('Completed','workreap');?>&nbsp;<br><?php esc_html_e('Projects','workreap');?></h4>
										</div>
										<div class="wt-statisticcontent wt-countercolor4">
											<h3><?php echo intval($total_cancelled_jobs);?></h3>
											<h4><?php esc_html_e('Cancelled','workreap');?>&nbsp;<br><?php esc_html_e('Projects','workreap');?></h4>
										</div>
										<div class="wt-statisticcontent wt-countercolor1">
											<h3 ><?php echo intval($total_ongoing_services);?></h3>
											<h4><?php esc_html_e('Ongoing','workreap');?>&nbsp;<br><?php esc_html_e('Services','workreap');?></h4>
										</div>
										<div class="wt-statisticcontent wt-countercolor2">
											<h3><?php echo intval($total_completed_services);?></h3>
											<h4><?php esc_html_e('Completed','workreap');?>&nbsp;<br><?php esc_html_e('Services','workreap');?></h4>
										</div>
										<div class="wt-statisticcontent wt-countercolor4">
											<h3><?php echo intval($total_cancelled_services);?></h3>
											<h4><?php esc_html_e('Cancelled','workreap');?>&nbsp;<br><?php esc_html_e('Services','workreap');?></h4>
										</div>
									<?php }?>
									
									<div class="wt-statisticcontent wt-countercolor3">
										<h3><?php echo workreap_price_format($earnings);?></h3>
										<h4><?php esc_html_e('Total','workreap');?>&nbsp;<br><?php esc_html_e('Earnings','workreap');?></h4>
									</div>
									
									<div class="wt-description">
										<p><?php esc_html_e('* Click the button to send an offer','workreap');?></p>
										<?php if( is_user_logged_in() ) {?>
											<a class="wt-btn wt-send-offers" href="javascript:;">
												<?php esc_html_e('Send Offer','workreap');?>
											</a>
										<?php } else {?>
											<a class="wt-btn wt-loginfor-offer" href="javascript:;">
												<?php esc_html_e('Send  Offer','workreap');?>
											</a>
										<?php } ?>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>