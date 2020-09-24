<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$image       	= !empty($atts['image']['url']) ? $atts['image']['url'] : '';
$title       	= !empty($atts['title']) ? $atts['title'] : '';
$sub_title   	= !empty($atts['sub_title']) ? $atts['sub_title'] : '';
$desc  	     	= !empty($atts['description']) ? $atts['description'] : '';
$video_link    	= !empty($atts['video_link']) ? $atts['video_link'] : '';

$flag 			= rand(9999, 999999);
?>
<div class="wt-sc-how-it-work wt-workholder dynamic-secton-<?php echo esc_attr( $flag );?>">
	<div class="container">
		<div class="row justify-content-md-center">
			<?php if( !empty( $title ) || !empty( $sub_title ) || !empty( $desc ) ) {?>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 push-lg-2">
					<div class="wt-sectionhead wt-textcenter wt-topservices-title">
						<?php if( !empty( $title ) || !empty( $sub_title ) ) {?>
							<div class="wt-sectiontitle">
								<?php if( !empty( $title )) { ?><h2><?php echo esc_html( $title );?></h2><?php } ?>
								<?php if( !empty( $sub_title )) { ?><span><?php echo esc_html( $sub_title );?></span><?php } ?>
							</div>
						<?php } ?>
						<?php if( !empty( $desc ) ){?>
							<div class="wt-description"><?php echo do_shortcode( $desc );?></div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<?php if( !empty( $image ) ) {?>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-lg-10 push-xl-1">
					<div class="wt-workvideo-holder">
						<figure class="wt-workvideo-img">
							<a data-rel="prettyPhoto[video]" href="<?php echo esc_url( $video_link );?>" rel="prettyPhoto[video]">
								<img src="<?php echo esc_url( $image );?>" alt="<?php esc_attr_e('How it work','workreap');?>">
							</a>
						</figure>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
	