<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$title        = !empty($atts['title']) ? $atts['title'] : '';
$sub_title    = !empty($atts['sub_title']) ? $atts['sub_title'] : '';
$team_members = !empty($atts['team_members']) ? $atts['team_members'] : array();
$loop         = !empty($atts['loop'] && $atts['loop'] == 'true' ) ? 'true' : 'false';
$autoplay     = !empty($atts['autoplay'] && $atts['autoplay'] == 'true' ) ? 'true' : 'false';
$uniq_flag    = fw_unique_increment();


?>
<div class="wt-sc-teams wt-haslayout">
	<?php if( !empty( $title ) || !empty( $sub_title ) || !empty( $team_members ) ) { ?>
		<div class="wt-ourteamhold wt-haslayout wt-bgwhite">
			<div id="filter-masonry" class="wt-teamfilter wt-haslayout">
				<?php if( !empty( $title ) || !empty( $sub_title ) ) { ?>
				<div class="wt-sectionhead">
					<div class="wt-sectiontitle">
						<?php if( !empty( $title ) ) { ?>
							<h2><?php echo esc_html( $title ); ?></h2>
						<?php } ?>
						<?php if( !empty( $sub_title ) ) { ?>
							<span><?php echo esc_html( $sub_title ); ?></span>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
				<?php if( !empty( $team_members ) ) { 
					foreach( $team_members as $key => $member ) {
						$avatar 	  = !empty( $member['avatar']['url'] ) ? $member['avatar']['url'] : get_template_directory_uri().'/images/avatar.jpg';
						$name         = !empty( $member['name'] ) ? $member['name'] : '';
						$designation  = !empty( $member['designation'] ) ? $member['designation'] : '';
						$social_icons = !empty( $member['social_icons'] ) ? $member['social_icons'] : '';

						if( !empty( $avatar ) ||
							!empty( $name ) ||
							!empty( $designation ) ||
							!empty( $social_icons ) ) { 
							?>
							<div class="item wt-teamholder">
								<?php if( !empty( $avatar ) ) { ?>
									<figure class="wt-speakerimg">
										<img src="<?php echo esc_attr( $avatar ); ?>" alt="<?php esc_attr_e( 'Member Avatar', 'workreap' ) ?>">
									</figure>
								<?php } ?>
								<?php if( !empty( $name ) || !empty( $designation ) || !empty( $social_icons ) ) { ?>
									<div class="wt-teamcontent">
										<?php if( !empty( $name ) || !empty( $designation ) ) { ?>
											<div class="wt-title">
												<?php if( !empty( $name ) ) { ?>
													<h2><?php echo esc_html( $name ); ?></h2>
												<?php } ?>
												<?php if( !empty( $designation ) ) { ?>
													<span><?php echo esc_html( $designation ); ?></span>
												<?php } ?>
											</div>
										<?php } ?>
										<?php if( !empty( $social_icons ) ) { ?>
											<ul class="wt-socialicons wt-socialiconssimple">
												<?php
													foreach ($social_icons as $key => $value) {        
														$name = !empty( $value['social_name'] ) ? $value['social_name'] : '';
														$icon = !empty( $value['social_icons_list'] ) ? $value['social_icons_list'] : array();
														$url  = !empty( $value['social_url'] ) ? $value['social_url'] : '#';
														$class	= workreap_get_social_icon_name($icon);
													?>
													<li class="wt-social-icons <?php echo esc_attr( $class ); ?>">
														<a href="<?php echo esc_url( $url ); ?>">
															<i class="<?php echo esc_attr( $icon ); ?>"></i>
														</a>
													</li>
												<?php } ?>                                      
											</ul>
										<?php } ?>
									</div>
								<?php } ?>
							</div>
						<?php } ?>
					<?php } ?>
				</div>
				<?php 
					$script2	= "jQuery(function () {
									jQuery('.wt-teamholder').each(function () {
										 jQuery(this).hoverdir();
									});
								});";
					wp_add_inline_script( 'workreap-callbacks', $script2, 'after' );
				?>
			<?php } ?>
		</div>
	<?php } ?>
</div>




