<?php
/**
 *
 * Hooks
 *
 * @package   Workreap
 * @author    amentotech
 * @link      https://themeforest.net/user/amentotech/portfolio
 * @since 1.0
 */

/**
 * @upload images to temp folder
 * @return {}
 */
if ( ! function_exists( 'workreap_temp_uploader' ) ) {
	function workreap_temp_uploader() {
		global $current_user, $wp_roles;
		$user_identity	= $current_user->ID;

		$uploadspath	= wp_upload_dir();
		$folderRalativePath = $uploadspath['baseurl']."/wp-custom-uploader";
		$folderAbsolutePath = $uploadspath['basedir']."/wp-custom-uploader";

		wp_mkdir_p($folderAbsolutePath);
		
		$temp 			= explode(".", $_FILES["workreap_uploader"]["name"]);
		$filename 		= $_FILES['workreap_uploader']['name'];
		$newfilename	= round(microtime(true)) . '.' . end($temp);

		$target_abs_path 	= $folderAbsolutePath . "/" . $newfilename;
		$target_rel_path    = $folderRalativePath . "/" . $newfilename;
		
		if(move_uploaded_file($_FILES['workreap_uploader']['tmp_name'], $target_abs_path)) {
			$ajax_response = array(
				'type' 			=> 'success',
				'url' 			=> $target_rel_path,
				'filename' 		=> $newfilename,
				'message' 		=> esc_html__('Image uploaded.','workreap')	
			);
		} else{
			$ajax_response['type']		=  'error';	
			$ajax_response['message']	= esc_html__('Some error occur, please try again later.','workreap');	
		}
		
		
		chmod("{$target_abs_path}", 0755);
		
		echo json_encode($ajax_response);
		exit;

	}
	add_action('wp_ajax_workreap_temp_uploader', 'workreap_temp_uploader');
	add_action('wp_ajax_nopriv_workreap_temp_uploader', 'workreap_temp_uploader');
}