<?php
if (!defined('FW'))
    die('Forbidden');
/*
*  @var $atts
*/

$section_heading     	= !empty($atts['section_heading']) ? $atts['section_heading'] : '';
$locations				= !empty($atts['location']) ? $atts['location'] : array();
if( isset( $atts['preloader']['gadget'] ) && $atts['preloader']['gadget'] === 'enable' ){
	$view_title					= !empty( $atts['preloader']['enable']['btn_title'] )  ? $atts['preloader']['enable']['btn_title'] : '';
	$view_url					= !empty( $atts['preloader']['enable']['btn_link'] )  ? $atts['preloader']['enable']['btn_link'] : '';
}else {
	$view_title		= '';
	$view_url		= '';
}

$link_target     = !empty($atts['link_target']) ? $atts['link_target'] : 'jobs';
$search_page         = workreap_get_search_page_uri($link_target);
?>
<div class="wt-sc-by-location wt-haslayout">
	<?php if( !empty($locations) && apply_filters('workreap_check_plugin_activated','core') === true ) {?>
		<div class="wt-widgetskills">
			<div class="wt-fwidgettitle">
				<h3><?php echo esc_html($section_heading);?></h3>
			</div>
			<ul class="wt-fwidgetcontent">
				<?php foreach( $locations as $location ) { 
						$location      = get_term($location);
						$query_arg['location[]'] 	= urlencode($location->slug);
                    	$url                 		= add_query_arg( $query_arg, esc_url($search_page));
				?>
				<li><a href="<?php echo esc_url($url);?>"><?php echo esc_html($location->name);?></a></li>
				<?php }?>
				<?php if( !empty($view_title) ) {?>
					<li class="wt-viewmore"><a href="<?php echo esc_url($view_url);?>"><?php echo esc_html($view_title);?></a></li>
				<?php } ?>
			</ul>
		</div>
	<?php } ?>
</div>