<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$title				= !empty($atts['title']) ? $atts['title'] : '';
$sub_title			= !empty($atts['sub_title']) ? $atts['sub_title'] : '';
$description		= !empty($atts['description']) ? $atts['description'] : '';
$video_title		= !empty($atts['video_title']) ? $atts['video_title'] : '';
$video_url			= !empty($atts['video_url']) ? $atts['video_url'] : '';
$text_color			= !empty($atts['text_color']) ? $atts['text_color'] : '';
$searchs	    	= !empty($atts['search']) ? array_keys($atts['search']) : array();
$defult_key			= !empty($searchs) ? reset($searchs) : '';
$defult_url			= !empty($defult_key) ? workreap_get_search_page_uri($defult_key) : '';
$list_names			= worktic_get_search_list('yes');
$sliders			= !empty($atts['slider']) ? $atts['slider'] : array();
$animated			= !empty($atts['animated_image']) ? $atts['animated_image'] : array();
$flag 				= rand(9999, 999999);
?>
<div class="wt-sc-slider wt-haslayout wt-bannerholdervtwo wt-bannerholderthree dynamic-secton-<?php echo esc_attr( $flag );?>">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
				<div class="wt-bannercontent">
					<?php if( !empty( $title ) || !empty( $sub_title ) || !empty( $description ) ) {?>
						<div class="wt-bannerhead">
							<?php if( !empty( $title ) || !empty( $sub_title ) ) {?>
								<div class="wt-title">
									<h1>
										<?php if( !empty( $title ) ) {?><span><?php echo do_shortcode( $title );?></span> <?php } ?>
										<?php echo esc_html($sub_title);?>
									</h1>
								</div>
							<?php } ?>
							<?php if( !empty( $description )){?>
								<div class="wt-description"><?php echo do_shortcode( $description );?></div>
							<?php } ?>
						</div>
					<?php } ?>
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
										<button type="submit" class="wt-searchbtn"><span><?php esc_html_e('Search Now','workreap');?></span><i class="fa fa-search"></i></button>
									</div>
								</div>
							</fieldset>
						</form>
					<?php } ?>
					<?php if( !empty($video_url) || !empty($video_title) ) {?>
						<div class="wt-videoholder">
							<?php if( !empty( $video_url ) ){?>
								<div class="wt-videoshow">
									<a data-rel="prettyPhoto[video]" href="<?php echo esc_url( $video_url );?>" rel="prettyPhoto[video]"><i class="fa fa-play"></i></a>
								</div>
							<?php }?>
							<?php if( !empty( $video_title ) ){?>
								<div class="wt-videocontent">
									<span><?php echo do_shortcode( $video_title );?></span>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
				<?php if( !empty( $sliders ) || !empty( $animated )) {?>
					<div class="wt-bannerslider-holder">
						<?php if( !empty( $sliders ) ){?>
							<div id="wt-bannerslider-two-<?php echo intval($flag);?>" class="wt-bannerslider-two owl-carousel">
								<?php 
									foreach( $sliders as $slide ) {
										$image	= !empty( $slide['image']['url']) ? $slide['image']['url'] : '';
									?>
										<div class="item">
											<figure class="wt-bannerimg">
												<img src="<?php echo esc_url( $image );?>" alt="<?php esc_attr_e('Slider','workreap');?>">
											</figure>
										</div>
								<?php } ?>
							</div>
						<?php } ?>
						<?php if( !empty( $animated ) ){?>
							<figure class="wt-slidericons">
								<?php 
									foreach( $animated as $slide ) {
										$image	= !empty( $slide['image']['url']) ? $slide['image']['url'] : ''; ?>
										<img src="<?php echo esc_url( $image );?>" alt="<?php esc_attr_e('Slider','workreap');?>">
									<?php } ?>
							</figure>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php
$script = "
		jQuery('#wt-bannerslider-two-".esc_js($flag)."').owlCarousel({
			rtl: ".workreap_owl_rtl_check().",
			items: 1,
			nav:false,
			loop:true,
			dots: false,
			autoplay:true,
			autoplayTimeout:5000,
			animateOut: 'fadeOut',
			animateIn: 'fadeIn',
			touchDrag: false,
			mouseDrag: false
		});
	";
	wp_add_inline_script( 'workreap-callbacks', $script, 'after' );

