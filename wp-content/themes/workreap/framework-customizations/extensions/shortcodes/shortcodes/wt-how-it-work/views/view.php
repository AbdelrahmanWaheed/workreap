<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$image       	= !empty($atts['image']['url']) ? $atts['image']['url'] : '';
$text_color		= !empty($atts['text_color']) ? $atts['text_color'] : '';
$title       = !empty($atts['title']) ? $atts['title'] : '';
$sub_title   = !empty($atts['sub_title']) ? $atts['sub_title'] : '';
$desc  	     = !empty($atts['description']) ? $atts['description'] : '';

$work_process  		= !empty($atts['work_process']) ? $atts['work_process'] : array();
$count_process		= 0;
$flag 				= rand(9999, 999999);
?>
<div class="wt-sc-how-it-work wt-workholder dynamic-secton-<?php echo esc_attr( $flag );?>">
	<?php if( !empty( $title )  || !empty( $sub_title ) || !empty( $desc ) ){?>
		<div class="row justify-content-center align-self-center">
			<div class="col-12 col-sm-12 col-md-8 push-md-2 col-lg-8 push-lg-2">
				<div class="wt-sectionhead wt-textcenter wt-howswork">
					<?php if( !empty( $title )  || !empty( $sub_title ) ){?>
						<div class="wt-sectiontitle">
							<?php if( !empty( $title ) ) {?><h2><?php echo esc_html( $title );?></h2><?php }?>
							<?php if( !empty( $sub_title ) ) {?><span><?php echo esc_html( $sub_title );?></span><?php } ?>
						</div>
					<?php }?>
					<?php if( !empty( $desc ) ){?>
						<div class="wt-description"><?php echo do_shortcode( $desc );?></div>
					<?php } ?>
				</div>
			</div>
		</div>
	<?php } ?>
	<?php if( !empty( $work_process ) ) {?>
		<div class="wt-haslayout wt-workprocess">
			<div class="row">
				<?php
					foreach ( $work_process as $work_proc ){
						$count_process	++;
						if( intval( $count_process ) == 2 ) {
							$class	= 'wt-workdetails-border';
						} else if( intval( $count_process ) == 3 ) {
							$class	= 'wt-workdetails-bordertwo';
						} else {
							$class	= '';
						}
						
						$img_url	= !empty( $work_proc['image']['url'] ) ? $work_proc['image']['url'] : '';
						$title		= !empty( $work_proc['title'] ) ? $work_proc['title'] : '';
						$sub_title	= !empty( $work_proc['sub_title'] ) ? $work_proc['sub_title'] : '';?>
						<div class="col-12 col-sm-12 col-md-6 col-lg-4 float-left">
							<div class="wt-workdetails <?php echo esc_attr( $class );?>">
								<?php if( !empty( $img_url ) ) {?>
									<div class="wt-workdetail">
										<figure><img src="<?php echo esc_url( $img_url );?>" alt="<?php echo esc_attr( $title );?>"></figure>
									</div>
								<?php } ?>
								<div class="wt-title">
									<?php if( !empty( $title ) ) {?>
										<span><?php echo esc_html( $title );?></span>
									<?php }?>
									<?php if( !empty( $sub_title ) ){?>
										<h3><a href="javascript:;"><?php echo esc_html( $sub_title );?></a></h3>
									<?php }?>
								</div>
							</div>
						</div>
				<?php }?>
			</div>
		</div>
	<?php } ?>	
</div>
<?php 
	if( !empty ( $text_color ) ) {
		ob_start();
		?>
		.dynamic-secton-<?php echo esc_attr( $flag );?> .wt-howswork .wt-sectiontitle h2, .wt-howswork .wt-sectiontitle span, .wt-howswork .wt-description p, 
		.dynamic-secton-<?php echo esc_attr( $flag );?> .wt-workdetails .wt-title span,
		.dynamic-secton-<?php echo esc_attr( $flag );?> .wt-workdetails .wt-title h3 a{ color : <?php echo esc_html($text_color);?>}

		<?php 
		$custom_styles	= ob_get_clean();
		$css = preg_replace('/\s\s+/', '', $custom_styles);
		wp_add_inline_script('workreap-callbacks',"jQuery('#workreap-typo-inline-css').append('".$css."')",'after');
	}