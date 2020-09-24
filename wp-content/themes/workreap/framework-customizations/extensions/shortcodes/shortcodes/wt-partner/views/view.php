<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$partners     	= !empty($atts['slider']) ? $atts['slider'] : array();
$uniq_flag  	= fw_unique_increment();
if( !empty( $partners ) ){
?>
<div class="wt-sc-partner wt-haslayout wt-logoholder dynamic-secton-<?php echo esc_attr( $uniq_flag );?>">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="row">
					<ul id="wt-logos-<?php echo esc_attr( $uniq_flag );?>" class="wt-logos owl-carousel">
						<?php 
						  	foreach( $partners as $partner ) {
								$image	= !empty( $partner['image']['url']) ? $partner['image']['url'] : '';
								$title	= !empty( $partner['title']) ? $partner['title'] : esc_html__('Partner','workreap');
								$url	= !empty( $partner['url']) ? $partner['url'] : '#';
							?>
							<li class="item">
								<a href="<?php echo esc_url( $url );?>" target="_blank"><img src="<?php echo esc_url( $image );?>" alt="<?php echo esc_attr( $title );?>"></a>
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$script = "
		jQuery('#wt-logos-".esc_js($uniq_flag)."').owlCarousel({
			rtl: ".workreap_owl_rtl_check().",
			items: 7,
			nav:false,
			loop:true,
			dots: false,
			autoplay:true,
			responsiveClass:true,
			responsive:{
				0:{items:1,},
				481:{items:3,},
				767:{items:4,},
				992:{items:5,},
				1200:{items:7,}
			}
		});
	";
	wp_add_inline_script( 'workreap-callbacks', $script, 'after' );
 }
