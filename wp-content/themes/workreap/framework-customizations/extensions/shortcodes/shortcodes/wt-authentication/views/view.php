<?php
if (!defined('FW'))
    die('Forbidden');
/**
 * @var $atts
 */

$uniq_flag = fw_unique_increment();
?>
<div class="wt-sc-shortcode wt-haslayout">
	<?php
		if( isset( $atts['form_type'] ) && $atts['form_type'] === 'register' ){
			echo do_shortcode('[workreap_authentication]');
		} else if( isset( $atts['form_type'] ) && $atts['form_type'] === 'login' ){
			echo do_shortcode('[workreap_login]');
		} 
	?>
</div>