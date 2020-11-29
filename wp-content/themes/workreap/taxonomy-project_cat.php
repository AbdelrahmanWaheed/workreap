<?php
/**
 *
 * The template used for displaying default project category result
 *
 * @package   workreap
 * @author    Amentotech
 * @link      https://themeforest.net/user/amentotech/portfolio
 * @since 1.0
 */
global $wp_query;
get_header();
$access_type	= workreap_return_system_access();
		

if( !empty($access_type) && $access_type === 'service' ) {
	get_template_part("directory/services", "search");
} else{
	get_template_part("directory/project", "search");
}
