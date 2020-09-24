<?php
if (!defined('FW'))
    die('Forbidden');
/*
*  @var $atts
*/

$section_heading     	= !empty($atts['section_heading']) ? $atts['section_heading'] : '';
$categories          	= !empty($atts['categories']) ? $atts['categories'] : array();
$post_type           	= !empty($atts['post_type']) ? $atts['post_type'] : 'jobs';
$search_page         	= workreap_get_search_page_uri($post_type);

$uniq_flag  			= rand(1,99999);

if( is_rtl() ) {
	$rtl	= 'true';
} else {
	$rtl	= 'false';
}
?>
<div class="wt-sc-categories-freelancer">
    <div class="wt-categoriesslider-holder wt-haslayout">
    	<?php if(!empty($section_heading) ) {?>
			<div class="wt-title">
				<h2><?php echo esc_html($section_heading);?>&nbsp;</h2>
			</div>
		<?php }?>
		<?php if(!empty($categories) && count($categories)>0 && apply_filters('workreap_check_plugin_activated','core') === true ) {?>
			<div id="wt-categoriesslider-<?php echo esc_attr($uniq_flag); ?>" class="wt-categoriesslider owl-carousel">
				<?php foreach( $categories as $key => $cat_id ) { 
                    $category      = get_term($cat_id);
                    $icon          = array();
                    $category_icon = array();
                    if( function_exists( 'fw_get_db_term_option' ) ) {
                        $icon          = fw_get_db_term_option($cat_id, 'project_cat');
                        $category_icon = !empty($icon['category_icon']) ? $icon['category_icon'] : array();
                    }
	
                    $query_arg['category[]'] = urlencode($category->slug);
                    $permalink                = add_query_arg( $query_arg, esc_url($search_page));
					?>
					<div class="wt-categoryslidercontent item">
						<?php
                            if (!empty($category_icon) && $category_icon['type'] === 'icon-font') {
                                do_action('enqueue_unyson_icon_css');
                                if (!empty($category_icon['icon-class'])) {?>
                                    <figure><i class="<?php echo esc_attr($category_icon['icon-class']); ?>"></i></figure>
                                <?php
                                }
                            } elseif (!empty($category_icon['type']) && $category_icon['type'] === 'custom-upload') {
                                if (!empty($category_icon['url'])) {?>
                                    <figure><img src="<?php echo esc_url($category_icon['url']); ?>" alt="<?php esc_attr_e('Category','workreap'); ?>"></figure>
                                    <?php
                                }
                            }
                         ?>
						<div class="wt-cattitle">
							<h3><a href="<?php echo esc_url( $permalink );?>"><?php echo esc_html($category->name); ?></a></h3>
							<?php if(!empty($category) ){?>
								<span><?php esc_html_e('Items','workreap'); ?>: <?php echo intval($category->count);?></span>
							<?php }?>
						</div>
					</div>
				<?php }?>
			</div>
		<?php }?>
	</div>
</div>
<?php 
	$script = "
		jQuery(document).ready(function () {
			var _wt_categoriesslider = jQuery('#wt-categoriesslider-".esc_js($uniq_flag)."')
			_wt_categoriesslider.owlCarousel({
				item: 6,
				loop:true,
				nav:false,
				margin: 0,
				rtl: ".workreap_owl_rtl_check().",
				autoplay:true,
				center: true,
				responsiveClass:true,
				responsive:{
					0:{items:1,},
					481:{items:2,},
					768:{items:3,},
					1440:{items:4,},
					1760:{items:6,}
				}
			});
		});
	";
	wp_add_inline_script( 'workreap-callbacks', $script, 'after' );
 ?>
