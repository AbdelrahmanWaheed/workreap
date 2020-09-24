<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$services = !empty($atts['services']) ? $atts['services'] : array();
?>
<div class="wt-sc-services wt-haslayout">
	<?php if( !empty( $services ) ) { ?>
		<div class="wt-companydetails">
			<?php 
			foreach( $services as $key => $service ) { 
				$title      = !empty( $service['title'] ) ? $service['title'] : '';
				$desc       = !empty( $service['description'] ) ? $service['description'] : '';
				$btn_title  = !empty( $service['btn_title'] ) ? $service['btn_title'] : '';
				$tpl_page   = !empty( $service['template_page'] ) ? $service['template_page'] : array();
				$page_link  = !empty( $tpl_page ) ? get_permalink((int) $tpl_page[0]) : '#';

				if( !empty( $title ) ||
					!empty( $desc ) ||
					!empty( $btn_title ) ) { 
					?>
					<div class="wt-companycontent">
						<?php if( !empty( $title ) ) { ?>
							<div class="wt-companyinfotitle">
								<h2><?php echo esc_html( $title ); ?></h2>
							</div>
						<?php } ?>
						<?php if( !empty( $desc ) ) { ?>
							<div class="wt-description">
								<?php echo wp_kses_post( wpautop( do_shortcode( $desc ) ) ); ?>
							</div>
						<?php } ?>
						<?php if( !empty( $btn_title ) ) { ?>
							<div class="wt-btnarea">
								<a href="<?php echo esc_url($page_link); ?>" class="wt-btn"><?php echo esc_attr( $btn_title ); ?></a>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
	<?php } ?>
</div>