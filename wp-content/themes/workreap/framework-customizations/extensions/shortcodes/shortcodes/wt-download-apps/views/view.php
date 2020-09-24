<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$image 	   = !empty($atts['image']['url']) ? $atts['image']['url'] : '';
$title     = !empty($atts['title']) ? $atts['title'] : '';
$sub_title = !empty($atts['sub_title']) ? $atts['sub_title'] : '';
$desc  	   = !empty($atts['description']) ? $atts['description'] : '';
$logos 	   = !empty($atts['logos']) ? $atts['logos'] : array();
?>
<div class="wt-sc-explore-cat wt-haslayout">
	<div class="row">
		<?php 
		if( !empty( $image ) ||
			!empty( $title ) ||
			!empty( $sub_title ) ||
			!empty( $desc ) ||
			!empty( $logos ) ) {
			?>
			<?php if( !empty( $image ) ) { ?>
				<div class="col-12 col-sm-12 col-md-6 col-lg-6 float-left">
					<figure class="wt-mobileimg">
						<img src="<?php echo esc_url($image); ?>" alt="<?php esc_attr_e('APP' ,'workreap') ?>">
					</figure>
				</div>
			<?php } ?>
			<?php 
			if( !empty( $title ) ||
				!empty( $sub_title ) ||
				!empty( $desc ) ||
				!empty( $logos ) ) {
				?>
				<div class="col-12 col-sm-12 col-md-6 col-lg-6 float-left">
					<div class="wt-experienceholder">
						<div class="wt-sectionhead">
							<?php if(!empty( $title )  || !empty( $sub_title )) { ?>
								<div class="wt-sectiontitle">
									<?php if( !empty( $title ) ) { ?>
										<h2><?php echo esc_html($title); ?></h2>
									<?php } ?>
									<?php if( !empty($sub_title) ) { ?>
										<span><?php echo esc_html( $sub_title ); ?></span>
									<?php } ?>
								</div>
							<?php } ?>
							<?php if( !empty( $desc) ) { ?>
								<div class="wt-description">
									<?php echo wp_kses_post( wpautop( do_shortcode( $desc ) ) ); ?>
								</div>
							<?php } ?>
							<?php if( !empty( $logos ) ) { ?>
								<ul class="wt-appicon">
									<?php 
										foreach( $logos as $key => $logo ) { 
											$image  = !empty( $logo['image']['url'] ) ? $logo['image']['url'] : '';
											$url    = !empty( $logo['link_url'] ) ? $logo['link_url'] : '#';
											$target = !empty( $logo['link_target'] ) ? $logo['link_target'] : '_blank';
											if( !empty( $image ) ) { ?>
											<li>
												<a target="<?php echo esc_attr($target); ?>" href="<?php echo esc_url($url); ?>">
													<?php if( !empty( $image ) ) { ?>
														<figure><img src="<?php echo esc_url( $image ); ?>" alt="<?php esc_attr_e('Logo', 'workreap'); ?>"></figure>
													<?php } ?>
												</a>
											</li>
										<?php } ?>
									<?php } ?>
								</ul>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
	</div>
</div>