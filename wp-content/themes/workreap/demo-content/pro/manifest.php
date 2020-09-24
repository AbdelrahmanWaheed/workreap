<?php if (!defined('FW')) die('Forbidden');
/**
 * @var string $uri Demo directory url
 */

$manifest = array();
$manifest['title'] = esc_html__('Pro', 'workreap');
$manifest['screenshot'] = get_template_directory_uri(). '/demo-content/images/pro.jpg';
$manifest['preview_link'] = 'http://amentotech.com/projects/workfleek';