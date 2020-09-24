<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$thumbnail  = !empty($atts['video_thumbnail']['url']) ? $atts['video_thumbnail']['url'] : '';
$video_link = !empty($atts['video_link']) ? $atts['video_link'] : '';
$title      = !empty($atts['title']) ? $atts['title'] : '';
$sub_title  = !empty($atts['sub_title']) ? $atts['sub_title'] : '';
$desc  	    = !empty($atts['description']) ? $atts['description'] : '';
$counters   = !empty($atts['counters']) ? $atts['counters'] : array();
$uniq_flag   = fw_unique_increment();
?>
<div class="wt-sc-greetings wt-haslayout">
	<?php 
	if( !empty( $thumbnail ) ||
		!empty( $video_link ) ||
		!empty( $title ) ||
		!empty( $sub_title ) ||
		!empty( $desc ) ||
		!empty( $counters ) ) {
		?>
		<div class="wt-greeting-holder">
			<div class="row">
				<?php 
				if( !empty( $title ) ||
					!empty( $sub_title ) ||
					!empty( $desc ) ||
					!empty( $counters ) ) {
					?>
					<div class="col-12 col-sm-12 col-md-12 col-lg-7 float-left">
						<div class="wt-greetingcontent">
							<?php 
								if( !empty( $title ) ||
									!empty( $sub_title ) ||
									!empty( $desc ) ) {
								?>
								<div class="wt-sectionhead">
									<?php 
										if( !empty( $title ) ||
											!empty( $sub_title ) 
										) {
										?>
										<div class="wt-sectiontitle">
											<?php if( !empty( $title ) ) { ?>
												<h2><?php echo esc_html( $title ); ?></h2>
											<?php } ?>
											<?php if( !empty( $sub_title ) ) { ?>
												<span><?php echo esc_html( $sub_title ); ?></span>
											<?php } ?>
										</div>
									<?php } ?>
									<?php if( !empty( $desc ) ) { ?>
										<div class="wt-description">
											<?php echo wp_kses_post( wpautop( do_shortcode( $desc ) ) ); ?>
										</div>
									<?php } ?>
								</div>
							<?php } ?>
							<?php 
								if( !empty( $counters ) ) { ?>
									<div id="wt-statistics-<?php echo esc_attr($uniq_flag); ?>" class="wt-statistics">
										<?php
											$counter_star	= 0;
											foreach ($counters as $counter) {
												$counter_star ++;
												$counter_title 		= !empty($counter['counter_title']) ? $counter['counter_title'] : '' ;
												$start_from 		=  !empty($counter['counter_start']) ? $counter['counter_start'] : intval(0);
												$counter_end 		= isset($counter['counter_end']) && $counter['counter_end'] != '' ? $counter['counter_end'] : intval(1000);
												$counter_interval 	= isset($counter['counter_interval']) && $counter['counter_interval'] != '' ? $counter['counter_interval'] : intval(50);
												$counter_speed 		= isset($counter['counter_speed']) && $counter['counter_speed'] != '' ? $counter['counter_speed'] : intval(8000);
												$counter_symbol 	=  !empty($counter['counter_symbol']) ? $counter['counter_symbol'] : '';
											?>
											<div class="wt-statisticcontent wt-countercolor<?php echo esc_attr( $counter_star );?>">
												<h3 data-from="<?php echo esc_attr($start_from); ?>" data-to="<?php echo esc_attr($counter_end) ?>" data-speed="<?php echo esc_attr($counter_speed); ?>" data-refresh-interval="<?php echo esc_attr($counter_interval); ?>"><?php echo esc_html($counter_end) ?></h3>
												<?php if( !empty( $counter_title ) ) { ?>
													<h4><?php echo esc_html( $counter_title ); ?></h4>
													<?php if( !empty( $counter_symbol ) ) {?>
														<em><?php echo esc_html( $counter_symbol );?></em>
													<?php } ?>
												<?php } ?>
											</div>
										<?php } ?>
									</div>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if( !empty( $thumbnail ) ) { ?>
					<div class="col-12 col-sm-12 col-md-12 col-lg-5 float-left">
						<div class="wt-greetingvideo">
							<figure>
								<a data-rel="prettyPhoto[video]" href="<?php echo esc_url( $video_link ); ?>">
								<img src="<?php echo esc_url($thumbnail); ?>" alt="<?php esc_attr_e('Welcome & Greetings', 'workreap') ?>">
								</a>
							</figure>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php 
			$script = "
				jQuery(document).ready(function () {
					try {
						var _wt_statistics = jQuery('#wt-statistics-".esc_js($uniq_flag)."');
						_wt_statistics.appear(function () {
							var _wt_statistics = jQuery('.wt-statisticcontent h3');
							_wt_statistics.countTo({
								formatter: function (value, options) {
									return value.toFixed(options.decimals).replace(/\B(?=(?:\d{3})+(?!\d))/g, ',');
								}
							});
						});
					} catch (err) {} 
				});
			";
			wp_add_inline_script( 'workreap-callbacks', $script, 'after' );
		?>
	<?php } ?>
</div>