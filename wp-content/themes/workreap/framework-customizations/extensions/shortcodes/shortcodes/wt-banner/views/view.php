<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$title				= !empty($atts['title']) ? $atts['title'] : '';
$text_color			= !empty($atts['text_color']) ? $atts['text_color'] : '';
$sub_title			= !empty($atts['sub_title']) ? $atts['sub_title'] : '';
$description		= !empty($atts['description']) ? $atts['description'] : '';
$video_title		= !empty($atts['video_title']) ? $atts['video_title'] : '';
$video_url			= !empty($atts['video_url']) ? $atts['video_url'] : '';
$images	    		= !empty($atts['images']) ? $atts['images'] : array();
$searchs	    	= !empty($atts['search']) ? array_keys($atts['search']) : array();
$defult_key			= !empty($searchs) ? reset($searchs) : '';
$defult_url			= !empty($defult_key) ? workreap_get_search_page_uri($defult_key) : '';
$list_names			= worktic_get_search_list('yes');
$flag 				= rand(9999, 999999);
?>
<div class="wt-sc-banner wt-haslayout dynamic-secton-<?php echo esc_attr( $flag );?>">
	<div class="row">
		<?php if( !empty($images) ) { ?>
			<div class="col-12 col-sm-12 col-md-12 col-lg-5">
				<div class="wt-bannerimages">
					<figure class="wt-bannermanimg">
						<?php 
							$image_count	= 0;
							foreach ( $images as $image ) { 
								$image_count ++;
								if( !empty($image['url'])) {
									$class	= '';
									
									if($image_count == 2) { 
										$class	= 'wt-bannermanimgone';
									} elseif($image_count == 3) {
										$class  = 'wt-bannermanimgtwo'; 
									} 
									?>
										<img class="<?php echo esc_attr( $class );?>" src="<?php echo esc_url( $image['url']);?>" alt="<?php esc_attr_e('Banner','workreap');?>">
									<?php
								}
							}
						?>
					</figure>
				</div>
			</div>
		<?php } ?>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-7">
			<div class="wt-bannercontent">
				<div class="wt-bannerhead">
					<?php if( !empty($title) ) {?>
						<div class="wt-title">
							<h1><span><?php echo do_shortcode($title);?></span><?php echo esc_html($sub_title);?></h1>
						</div>
					<?php }?>
					<?php if( !empty($description) ) { ?>
						<div class="wt-description">
							<?php echo do_shortcode( $description );?>
						</div>
					<?php } ?>
				</div>
				<?php if( !empty($searchs) ) {?>
					<form class="wt-formtheme wt-formbanner" action="<?php echo esc_url($defult_url);?>" method="get">
						<fieldset>
							<div class="form-group">
								<input type="text" name="keyword" class="form-control" placeholder="<?php esc_attr_e('I’m looking for','workreap');?>">
								<div class="wt-formoptions">
									<?php if( !empty($list_names[$defult_key]) ) { ?>
										<div class="wt-dropdown">
											<span><em class="selected-search-type"><?php echo esc_html( $list_names[$defult_key] );?></em><i class="lnr lnr-chevron-down"></i></span>
										</div>
									<?php } ?>
									<div class="wt-radioholder">
										<?php 
											  foreach( $searchs as $search ) {
												$action_url	= workreap_get_search_page_uri($search);
												if( !empty($search) && $search === $defult_key) {
													$checked	= 'checked';
												} else {
													$checked	= '';
												}
											?>
											<span class="wt-radio">
												<input id="wt-<?php echo esc_attr( $search );?>" data-url="<?php echo esc_url($action_url);?>" data-title="<?php echo esc_attr( $list_names[$search] );?>" type="radio" name="searchtype" value="<?php echo esc_attr($search);?>" <?php echo esc_attr($checked);?>>
												<label for="wt-<?php echo esc_attr( $search );?>"><?php echo esc_html( $list_names[$search] );?></label>
											</span>
										<?php } ?>
									</div>
									<button type="submit" class="wt-searchbtn"><i class="fa fa-search"></i></button>
								</div>
							</div>
						</fieldset>
					</form>
				<?php } ?>
				<?php if( !empty($video_url) ) {?>
					<div class="wt-videoholder">
						<div class="wt-videoshow">
							<a data-rel="prettyPhoto[video]" href="<?php echo esc_url($video_url); ?>"><i class="fa fa-play"></i></a>
						</div>
						<?php if( !empty($video_title) ) {?>
							<div class="wt-videocontent">
								<span><?php echo do_shortcode($video_title);?></span>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php 
	if( !empty ( $text_color ) ) {
		ob_start();
		?>
		.dynamic-secton-<?php echo esc_attr( $flag );?> .wt-bannercontent .wt-title h1, 
		.dynamic-secton-<?php echo esc_attr( $flag );?> .wt-videocontent span,
		.dynamic-secton-<?php echo esc_attr( $flag );?> .wt-bannercontent .wt-bannerhead .wt-description p, 
		.dynamic-secton-<?php echo esc_attr( $flag );?> .wt-description h2{ color : <?php echo esc_html($text_color);?>}

		<?php 
			$custom_styles	= ob_get_clean();
			$css = preg_replace('/\s\s+/', '', $custom_styles);
			wp_add_inline_script('workreap-callbacks',"jQuery('#workreap-typo-inline-css').append('".$css."')",'after');
	}