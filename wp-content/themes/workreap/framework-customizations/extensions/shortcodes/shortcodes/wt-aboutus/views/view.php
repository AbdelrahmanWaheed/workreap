<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$bg_image   = !empty($atts['image']['url']) ? $atts['image']['url'] : '';
$title      = !empty($atts['title']) ? $atts['title'] : '';
$sub_title  = !empty($atts['sub_title']) ? $atts['sub_title'] : '';
$desc  	    = !empty($atts['description']) ? $atts['description'] : '';
$text_color = !empty( $atts['text_color'] ) ? $atts['text_color'] : '';
$buttons    = !empty($atts['buttons']) ? $atts['buttons'] : array();
$flag 		= rand(9999, 999999);
?>
<div class="wt-sc-newsletter wt-haslayout dynamic-secton-<?php echo esc_attr( $flag );?>">
	<?php 
	if( !empty( $image ) ||
		!empty( $title ) ||
		!empty( $desc ) ||
		!empty( $buttons ) ) {
		?>
		<div class="wt-signupholder" style="background: url(<?php echo esc_attr($bg_image); ?>)">
			<?php 
				if( !empty( $title ) ||
					!empty( $desc ) ||
					!empty( $buttons ) ) {
				?>
				<div class="col-12 col-sm-12 col-md-12 col-lg-6 pull-right">
					<div class="wt-signupcontent">
						<?php if( !empty( $title ) ) { ?>
							<div class="wt-title">
								<h2>
									<?php if(!empty($sub_title) ) {?>
										<span><?php echo esc_html( $sub_title ); ?></span>
									<?php } ?>
									<?php echo esc_html( $title ); ?>
								</h2>
							</div>
						<?php } ?>
						<?php if( !empty( $desc ) ) { ?>
							<div class="wt-description">
								<?php echo wp_kses_post( wpautop( do_shortcode( $desc ) ) ); ?>
							</div>
						<?php } ?>
						<?php if( !empty( $buttons ) ) { ?>
							<div class="wt-btnarea">
								<?php 
									foreach($buttons as $key => $button) { 
										$btn_text = !empty($button['button_text']) ? $button['button_text'] : ''; 
										$btn_link = !empty($button['button_link']) ? $button['button_link'] : '#'; 
										$btn_styles = !empty($button['btn_styles']) && $button['btn_styles'] === 'active'  ? 'wt-btnvtwo'  : '';
										$target = !empty($button['link_target']) ? $button['link_target'] : '_self';
									?>
									<?php if( !empty( $btn_text ) ) { ?>
										<a target="<?php echo esc_attr( $target ); ?>" href="<?php echo esc_url($btn_link); ?>" class="wt-btn <?php echo esc_attr( $btn_styles ); ?> "><?php echo esc_html( $btn_text ); ?></a>
									<?php } ?>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php 
			if( !empty ( $text_color ) ) {
				ob_start();
				?>
				.dynamic-secton-<?php echo esc_html( $flag );?> .wt-title h2, 
				.dynamic-secton-<?php echo esc_html( $flag );?> .wt-description p {
					color: <?php echo esc_html( $text_color ); ?>;
				}
				<?php 
				$custom_styles	= ob_get_clean();
				$css = preg_replace('/\s\s+/', '', $custom_styles);
				wp_add_inline_script('workreap-callbacks',"jQuery('#workreap-typo-inline-css').append('".$css."')",'after');
			}
	 } ?>
</div>