<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$title      = !empty($atts['title']) ? $atts['title'] : '';
$desc  	    = !empty($atts['description']) ? $atts['description'] : '';
?>
<div class="wt-terms-pages wt-haslayout">
	<?php  if( !empty( $title ) ) { ?>
		<div class="wt-submitreportholder wt-bgwhite">
			<div class="wt-titlebar">
				<h2><?php echo esc_html($title);?></h2>
			</div>
			<div class="wt-reportdescription">
				<?php echo wp_kses_post( wpautop( do_shortcode( $desc ) ) ); ?>
			</div>
		</div>
	<?php } ?>
</div>