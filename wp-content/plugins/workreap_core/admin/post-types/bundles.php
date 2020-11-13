<?php

/**
 * @package   Workreap Core
 * @author    Amentotech
 * @link      http://amentotech.com/
 * @version 1.0
 * @since 1.0
 */
if (!class_exists('Workreap_Bundles')) {

    class Workreap_Bundles {

        /**
         * @access  public
         * @Init Hooks in Constructor
         */
        public function __construct() {
            add_action('init', array(&$this, 'init_directory_type'));
            add_filter('manage_bundles_posts_columns', array(&$this, 'bundles_columns_add'));
            add_action('manage_bundles_posts_custom_column', array(&$this, 'bundles_columns'),10, 2);	
        }

        /**
         * @Init Post Type
         * @return {post}
         */
        public function init_directory_type() {
            $this->prepare_post_type();
        }

        /**
         * @Prepare Post Type Category
         * @return post type
         */
        public function prepare_post_type() {
            $labels = array(
                'name'              => esc_html__('Bundles', 'workreap_core'),
                'all_items'         => esc_html__('Bundles', 'workreap_core'),
                'singular_name'     => esc_html__('Bundle', 'workreap_core'),
                'add_new'           => esc_html__('Add Bundle', 'workreap_core'),
                'add_new_item'      => esc_html__('Add New Bundle', 'workreap_core'),
                'edit'              => esc_html__('Edit', 'workreap_core'),
                'edit_item'         => esc_html__('Edit Bundle', 'workreap_core'),
                'new_item'          => esc_html__('New Bundle', 'workreap_core'),
                'view'              => esc_html__('View Bundle', 'workreap_core'),
                'view_item'         => esc_html__('View Bundle', 'workreap_core'),
                'search_items'      => esc_html__('Search Bundle', 'workreap_core'),
                'not_found'         => esc_html__('No Bundle found', 'workreap_core'),
                'not_found_in_trash'=> esc_html__('No Bundle found in trash', 'workreap_core'),
                'parent'            => esc_html__('Parent Bundles', 'workreap_core'),
            );
            $args = array(
                'labels'                => $labels,
                'description'           => esc_html__('This is where you can add new bundles ', 'workreap_core'),
                'public'                => false,
                'supports'              => array('title', 'page-attributes'),
                'show_ui'               => true,
                'capability_type'       => 'post',
                'map_meta_cap'          => true,
                'publicly_queryable'    => false,
                'exclude_from_search'   => false,
                'hierarchical'          => false,
                'menu_position'         => 11,
                'menu_icon'             => 'dashicons-code-standards',
                'rewrite'               => false,
                'query_var'             => false,
                'has_archive'           => 'false',
            );
            
            if( apply_filters('workreap_system_access','job_base') === true ){
                register_post_type('bundles', $args);
            }
        }

        /**
         * @Prepare Columns
         * @return {post}
         */
        public function bundles_columns_add($columns) {
            unset($columns['date']);
            $columns['category'] = esc_html__('Category', 'workreap');
            return $columns;
        }

        /**
         * @Get Columns
         * @return {}
         */
        public function bundles_columns($case) {
            switch ($case) {
                case 'category':
                    $category = fw_get_db_post_option(get_the_ID(), 'category');
                    $labels = array(
                        'one-to-one'    => 'One To One',
                        'contest'       => 'Contest',
                    );
                    echo $labels[$category];
                break;                
            }
        }
    }

    new Workreap_Bundles();
}
