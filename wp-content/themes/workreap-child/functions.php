<?php
/**
 * Theme functions file
 */

/**
 * Enqueue parent theme styles first
 * Replaces previous method using @import
 * <http://codex.wordpress.org/Child_Themes>
 */

function workreap_theme_enqueue_styles() {
	$parent_theme_version = wp_get_theme('workreap');
	$child_theme_version  = wp_get_theme('workreap-child');
	
	 $styles	= array( 'bootstrap','workreap-min');
	
    $parent_style = 'workreap-style';
  	wp_enqueue_style( 'workreap-child-styles', get_stylesheet_directory_uri() . '/style.css', array( $parent_style ),$child_theme_version->get('Version'));
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css',$styles,$parent_theme_version->get('Version'));
	
	wp_enqueue_style('workreap-responsive', get_template_directory_uri() . '/css/responsive.css', '',$parent_theme_version->get('Version'));
	wp_enqueue_style('basictable', get_template_directory_uri() . '/css/basictable.css', '',$parent_theme_version->get('Version'));

	if (is_page_template('directory/dashboard.php')) {  
		wp_enqueue_style('workreap-dashboard', get_template_directory_uri() . '/css/dashboard.css', '',$parent_theme_version->get('Version'));
		wp_enqueue_style('workreap-dbresponsive', get_template_directory_uri() . '/css/dbresponsive.css', '',$parent_theme_version->get('Version'));
	}

	wp_enqueue_style( 'workreap-child-customization', get_stylesheet_directory_uri() . '/css/all.css', '',$child_theme_version->get('Version'));
}

add_action( 'wp_enqueue_scripts', 'workreap_theme_enqueue_styles' );