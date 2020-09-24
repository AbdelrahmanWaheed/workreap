<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$brands     = !empty($atts['brands']) ? $atts['brands'] : array();
$loop       = !empty($atts['loop'] && $atts['loop'] == 'true' ) ? 'true' : 'false';
$autoplay   = !empty($atts['autoplay'] && $atts['autoplay'] == 'true' ) ? 'true' : 'false';
$uniq_flag  = fw_unique_increment();
?>
<div class="wt-sc-brand wt-haslayout">
	<?php if( !empty( $brands ) ) { ?>
		<div id="wt-brandslider-<?php echo esc_attr($uniq_flag); ?>" class="wt-barandslider wt-haslayout owl-carousel">
			<?php foreach ($brands as $key => $brand) { 
				$image   = !empty( $brand['image']['url'] ) ? workreap_add_http($brand['image']['url']) : '';
				$link    = !empty( $brand['link'] ) ? $brand['link'] : '#';
				$target  = !empty( $brand['link_target'] ) ? $brand['link_target'] : '_blank';
				?>
				<figure class="item wt-brandimg">
					<a target="<?php echo esc_attr($target); ?>" href="<?php echo esc_url($link); ?>">
						<img src="<?php echo esc_url( $image ); ?>" alt="<?php esc_attr_e('Brand', 'workreap'); ?>">
					</a>
				</figure>
			<?php } ?>
		</div>
		<?php 
			$script = "
				jQuery(document).ready(function () {
					jQuery('#wt-brandslider-".esc_js($uniq_flag)."').owlCarousel({
						item: 6,
						loop: ".esc_js($loop).",
						nav:false,
						rtl: ".workreap_owl_rtl_check().",
						margin: 0,
						autoplay:".esc_js($autoplay).",
						responsiveClass:true,
						responsive:{
							0:{items:1,},
							481:{items:2,},
							768:{items:3,},
							991:{items:4,},
							992:{items:5,}
						}
					});
				});
			";
			wp_add_inline_script( 'workreap-callbacks', $script, 'after' );
		?>
	<?php } ?>
</div>