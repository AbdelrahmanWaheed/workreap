<?php

if (!defined('FW'))
    die('Forbidden');



$options = array(
    'title' => array(
        'label' => esc_html__('Section title', 'workreap'),
        'desc'  => esc_html__('Add title or leave it empty to hide', 'workreap'),
        'type'  => 'text',
        'value' => ''
    ),
    'description' => array(
        'label' => esc_html__('Section description', 'workreap'),
        'desc' => esc_html__('Add description. Leave it empty to hide', 'workreap'),
        'type' => 'wp-editor',
        'editor-height' => '200'
    ),
    'blog_view' => array(
		'type' => 'multi-picker',
        'label' => false,
        'desc' => false,
        'value' => array('gadget' => 'normal'),
        'picker' => array(
            'gadget' => array(
				'type' => 'select',
				'value' => 'list',
				'label' => esc_html__('Select View', 'workreap'),
				'choices' => array(
					'grid'     => esc_html__('Grid', 'workreap'),
					'list'     => esc_html__('List', 'workreap'),
				),
			),
    	),
		'choices' => array(
            'grid' => array(
                'columns' => array(
                    'type' => 'select',
					'value' => 'list',
					'label' => esc_html__('Select Columns', 'workreap'),
					'desc' => esc_html__('Select number of columns you want to show', 'workreap'),
					'choices' => array(
						'2_cols' => esc_html__('Classic View Two Columns', 'workreap'),
						'3_cols' => esc_html__('Three Columns', 'workreap'),
						'4_cols' => esc_html__('Four Columns', 'workreap'),
					),
				),
			),
			'list' => array(
                'size' => array(
                    'type' => 'select',
					'value' => 'full',
					'label' => esc_html__('Image size', 'workreap'),
					'desc' => esc_html__('Select image size', 'workreap'),
					'choices' => array(
						'full' => esc_html__('Full', 'workreap'),
						'small' => esc_html__('Small', 'workreap'),
					),
				),
			),
		),
	),
    'get_method' => array(
        'type' => 'multi-picker',
        'label' => false,
        'desc' => false,
        'value' => array('gadget' => 'normal'),
        'picker' => array(
            'gadget' => array(
                'type' => 'select',
                'value' => 'by_cats',
                'desc' => esc_html__('Select news by category or item', 'workreap'),
                'label' => esc_html__('News By', 'workreap'),
                'choices' => array(
                    'by_cats' => esc_html__('By Categories', 'workreap'),
                    'by_posts' => esc_html__('By item', 'workreap'),
                ),
            )
        ),
        'choices' => array(
            'by_cats' => array(
                'categories' => array(
                    'type' => 'multi-select',
                    'label' => esc_html__('Select Categories', 'workreap'),
                    'population' => 'taxonomy',
                    'source' => 'category',
                    'prepopulate' => 500,
                    'desc' => esc_html__('Show posts by category selection.', 'workreap'),
                ),
                'order' => array(
                    'type' => 'select',
                    'value' => 'DESC',
                    'desc' => esc_html__('Post Order', 'workreap'),
                    'label' => esc_html__('Order By', 'workreap'),
                    'choices' => array(
                        'ASC' => esc_html__('ASC', 'workreap'),
                        'DESC' => esc_html__('DESC', 'workreap'),
                    ),
                ),
                'orderby' => array(
                    'type' => 'select',
                    'value' => 'ID',
                    'desc' => esc_html__('Your Post Order', 'workreap'),
                    'label' => esc_html__('View Posts By', 'workreap'),
                    'choices' => array(
                        'ID' => esc_html__('Order by post id', 'workreap'),
                        'author' => esc_html__('Order by author', 'workreap'),
                        'title' => esc_html__('Order by title', 'workreap'),
                        'name' => esc_html__('Order by post name', 'workreap'),
                        'date' => esc_html__('Order by date', 'workreap'),
                        'rand' => esc_html__('Random order', 'workreap'),
                        'comment_count' => esc_html__('Order by number of comments', 'workreap'),
                    ),
                ),
                'show_posts' => array(
                    'type' => 'slider',
                    'value' => 9,
                    'properties' => array(
                        'min' => 1,
                        'max' => 100,
                        'sep' => 1,
                    ),
                    'label' => esc_html__('Show No of Posts', 'workreap'),
                ),
            ),
            'by_posts' => array(
                'posts' => array(
                    'type' => 'multi-select',
                    'label' => esc_html__('Select Posts', 'workreap'),
                    'population' => 'posts',
                    'source' => 'post',
                    'prepopulate' => 500,
                    'desc' => esc_html__('Show posts by post selection.', 'workreap'),
                ),
				'show_posts' => array(
                    'type' => 'slider',
                    'value' => 9,
                    'properties' => array(
                        'min' => 1,
                        'max' => 100,
                        'sep' => 1,
                    ),
                    'label' => esc_html__('Show No of Posts', 'workreap'),
                ),
            )
        ),
        'show_borders' => true,
    ),
    'show_pagination' => array(
        'type' => 'select',
        'value' => 'no',
        'label' => esc_html__('Show Pagination', 'workreap'),
        'desc' => esc_html__('', 'workreap'),
        'choices' => array(
            'yes' => esc_html__('Yes', 'workreap'),
            'no' => esc_html__('No', 'workreap'),
        ),
        'no-validate' => false,
    ),
);
