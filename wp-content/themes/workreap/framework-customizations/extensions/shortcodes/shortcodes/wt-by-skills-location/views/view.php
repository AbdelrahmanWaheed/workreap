<?php
if (!defined('FW'))
    die('Forbidden');
/*
*  @var $atts
*/

$section_heading     	= !empty($atts['section_heading']) ? $atts['section_heading'] : '';
$skills					= !empty($atts['skills']) ? $atts['skills'] : array();
$location				= !empty($atts['location'][0]) ? $atts['location'][0] : '';
if( !empty($location) && apply_filters('workreap_check_plugin_activated','core') === true ) {
	$location      		= get_term($location);
	$location_name		= $location->name;
	$location_slug		= $location->slug;
} else {
	$location_name		= '';
	$location_slug		= '';
}
if( isset( $atts['preloader']['gadget'] ) && $atts['preloader']['gadget'] === 'enable' ){
	$view_title					= !empty( $atts['preloader']['enable']['btn_title'] )  ? $atts['preloader']['enable']['btn_title'] : '';
	$view_url					= !empty( $atts['preloader']['enable']['btn_link'] )  ? $atts['preloader']['enable']['btn_link'] : '';
}else {
	$view_title		= '';
	$view_url		= '';
}

$link_target     = !empty($atts['link_target']) ? $atts['link_target'] : 'jobs';
$search_page         = workreap_get_search_page_uri($link_target);

$query_arg	= array();
?>
<div class="wt-sc-skill-location wt-haslayout">
	<?php if( !empty($skills) && apply_filters('workreap_check_plugin_activated','core') === true) {?>
		<div class="wt-widgetskills">
			<div class="wt-fwidgettitle">
				<h3><?php echo esc_html($section_heading);?></h3>
			</div>
			<ul class="wt-fwidgetcontent">
				<?php foreach( $skills as $skill ) { 
						$skill      = get_term($skill);
						$query_arg['skills[]'] 	= urlencode($skill->slug);
						$query_arg['location[]'] 	= urlencode($location_slug);
                    	$url                 		= add_query_arg( $query_arg, esc_url($search_page));
				?>
				<li>
					<a href="<?php echo esc_url($url);?>">
						<?php echo esc_html($skill->name);?>&nbsp;<?php echo esc_html_e('in','workreap');?>&nbsp;<?php echo esc_html($location_name);?>
					</a>
				</li>
				<?php }?>
				<?php if( !empty($view_title) ) {?>
					<li class="wt-viewmore"><a href="<?php echo esc_url($view_url);?>"><?php echo esc_html($view_title);?></a></li>
				<?php } ?>
			</ul>
		</div>
	<?php } ?>
</div>
