<?php
/**
 *
 * The template used for displaying freelancer resume
 *
 * @package   Workreap
 * @author    amentotech
 * @link      https://amentotech.com/user/amentotech/portfolio
 * @version 1.0
 * @since 1.0
 */

global $post;
$post_id 	= $post->ID;

if (function_exists('fw_get_db_post_option')) {
	$resume  = fw_get_db_post_option($post_id, 'resume', true);
}

if( !empty( $resume ) ){?>
	<div id="wt-resume" class="wt-widget resume-item-download">
		<div class="wt-widgetcontent wt-skillscontent data-list">
			<a href="<?php echo esc_url( $resume['url'] );?>" download><?php esc_html_e('Download My Resume','workreap');?></a>
		</div>
	</div>
<?php }?>