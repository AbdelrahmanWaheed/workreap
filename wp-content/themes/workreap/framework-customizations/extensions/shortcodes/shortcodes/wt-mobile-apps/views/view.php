<?php
if (!defined('FW'))
    die('Forbidden');
/*
*  @var $atts
*/

$title       = !empty($atts['title']) ? $atts['title'] : '';
$sub_title   = !empty($atts['sub_title']) ? $atts['sub_title'] : '';
$desc  	     = !empty($atts['description']) ? $atts['description'] : '';
$google_image  	    = !empty($atts['google_image']['url']) ? $atts['google_image']['url'] : '';
$app_store_image    = !empty($atts['app_store_image']['url']) ? $atts['app_store_image']['url'] : '';
$app_store_url   	= !empty($atts['app_store_url']) ? $atts['app_store_url'] : '';
$play_store_url   	= !empty($atts['play_store_url']) ? $atts['play_store_url'] : '';
$image  	    	= !empty($atts['image']['url']) ? $atts['image']['url'] : '';
$text_color			= !empty($atts['text_color']) ? $atts['text_color'] : '';
$flag 				= rand(9999, 999999);
?>
<div class="wt-sc-mobile-apps wt-haslayout wt-nativeholder dynamic-secton-<?php echo esc_attr( $flag );?>">
	<div class="row justify-content-center align-self-center">
	    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 push-lg-2">
			<div class="wt-sectionhead wt-textcenter wt-howswork">
				<?php if( !empty( $title ) || !empty( $sub_title ) ) {?>
					<div class="wt-sectiontitle">
						<?php if( !empty( $title ) ) {?><h2><?php echo esc_html( $title );?></h2><?php }?>
						<?php if( !empty( $sub_title ) ) {?><h3><?php echo esc_html( $sub_title );?></h3><?php }?>
					</div>
				<?php }?>
				<?php if( !empty( $desc ) ){?>
					<div class="wt-description"><?php echo do_shortcode( $desc );?></div>
				<?php }?>
				<?php if( ( !empty( $google_image ) && !empty( $play_store_url ) ) || ( !empty( $app_store_image ) && !empty( $app_store_url ) ) ) {?>
					<ul class="wt-appicons">
						<?php if( !empty( $google_image ) && !empty( $play_store_url ) ) {?>
							<li><a href="<?php echo esc_url( $play_store_url );?>"><img src="<?php echo esc_url( $google_image );?>" alt="<?php esc_attr_e('Play store','workreap');?>"></a></li>
						<?php }?>
						<?php if( !empty( $app_store_image ) && !empty( $app_store_url ) ) {?>
							<li><a href="<?php echo esc_url( $app_store_url );?>"><img src="<?php echo esc_url( $app_store_image );?>" alt="<?php esc_attr_e('App store','workreap');?>"></a></li>
						<?php }?>
					</ul>
				<?php }?>
			</div>
		</div>
		<?php if( !empty( $image ) ){?>
			<div class="d-none d-lg-block col-lg-12">
				<div class="wt-nativemobile">
					<figure><img src="<?php echo esc_url( $image );?>" alt="<?php esc_attr_e('Mobile Apps','workreap');?>"></figure>										
				</div>
			</div>
		<?php }?>
	</div>
</div>
<?php 
	if( !empty ( $text_color ) ) {
		ob_start();
		?>
		.dynamic-secton-<?php echo esc_attr( $flag );?> .wt-howswork .wt-sectiontitle h2, 
		.dynamic-secton-<?php echo esc_attr( $flag );?> .wt-howswork .wt-sectiontitle h3,
		.dynamic-secton-<?php echo esc_attr( $flag );?> .wt-howswork .wt-description p{ color : <?php echo esc_html($text_color);?>}

		<?php 
			$custom_styles	= ob_get_clean();
			$css = preg_replace('/\s\s+/', '', $custom_styles);
			wp_add_inline_script('workreap-callbacks',"jQuery('#workreap-typo-inline-css').append('".$css."')",'after');
	}
