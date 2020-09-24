<?php
if (!defined('FW'))
    die('Forbidden');
/*
*  @var $atts
*/

$section_heading     	= !empty($atts['section_heading']) ? $atts['section_heading'] : '';
$section_sub_heading 	= !empty($atts['section_subheading']) ? $atts['section_subheading'] : '';
$btn_title           	= !empty($atts['btn_title']) ? $atts['btn_title'] : '';
$btn_link            	= !empty($atts['btn_link']) ? $atts['btn_link'] : '';
$post_type           	= !empty($atts['post_type']) ? $atts['post_type'] : 'jobs';
$categories          	= !empty($atts['categories']) ? $atts['categories'] : array();
$search_page         	= workreap_get_search_page_uri($post_type);
?>
<div class="wt-sc-explore-categories wt-haslayout">
    <div class="row justify-content-md-center">
        <?php if (!empty($section_heading) || !empty($section_sub_heading)) { ?>
            <div class="col-xs-12 col-sm-12 col-md-8 push-md-2 col-lg-6 push-lg-3">
                <div class="wt-sectionhead wt-textcenter">
                    <div class="wt-sectiontitle">
                        <?php if (!empty($section_heading)) { ?>
                            <h2><?php echo esc_html($section_heading); ?></h2>
                        <?php } ?>
                        <?php if (!empty($section_sub_heading)) { ?>
                            <span><?php echo esc_html($section_sub_heading); ?></span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if( !empty( $categories ) && apply_filters('workreap_check_plugin_activated','core') === true  ) { ?>
            <div class="wt-categoryexpl ">
                <?php foreach( $categories as $key => $cat_id ) { 
                    $category      = get_term($cat_id);
                    $icon          = array();
                    $category_icon = array();
				
                    if( function_exists( 'fw_get_db_term_option' ) ) {
                        $icon          = fw_get_db_term_option($cat_id, 'project_cat');
                        $category_icon = !empty($icon['category_icon']) ? $icon['category_icon'] : array();
                    }
	
                    $query_arg['category[]'] = urlencode($category->slug);
                    $permalink                 = add_query_arg( $query_arg, esc_url($search_page));
                    ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 float-left">
                        <div class="wt-categorycontent">
                            <?php
                            if (!empty($category_icon) && $category_icon['type'] === 'icon-font') {
                                do_action('enqueue_unyson_icon_css');
                                if (!empty($category_icon['icon-class'])) {?>
                                    <i class="<?php echo esc_attr($category_icon['icon-class']); ?>"></i>
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
                            </div>
                            <div class="wt-categoryslidup">
                                <?php if( !empty( $category->description ) ) { ?>
                                    <p><?php echo esc_html($category->description); ?></p>
                                <?php } ?>
                                <a href="<?php echo esc_url( $permalink );?>"><?php esc_html_e('Explore', 'workreap') ?>&nbsp;<i class="fa fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if( $btn_title ) { ?>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 float-left">
                        <div class="wt-btnarea">
                            <a href="<?php echo esc_url( $btn_link ); ?>" class="wt-btn"><?php echo esc_html( $btn_title ); ?></a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>
