<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    Workreap
 * @subpackage Workreap/admin
 */

/**
 * @Rename Menu
 * @return {}
 */
if (!function_exists('workreap_rename_admin_menus')) {
	add_action( 'admin_menu', 'workreap_rename_admin_menus');
	function workreap_rename_admin_menus() {
		global $menu,$submenu;
		foreach( $menu as $key => $menu_item ) {
			if( $menu_item[2] == 'edit.php?post_type=freelancers' ){
				$menu[$key][0] = esc_html__('Workreap','workreap_core');
			}
		}

	}
}

/**
 * Get user type
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_get_linked_profile_id')) {

    function workreap_get_linked_profile_id($user_identity, $type='users') {
		if( $type === 'post') {
			$linked_profile   	= get_post_meta($user_identity, '_linked_profile', true);
		}else {
			$linked_profile   	= get_user_meta($user_identity, '_linked_profile', true);
		}
		
        $linked_profile	= !empty( $linked_profile ) ? $linked_profile : '';
		
        return intval( $linked_profile );
    }
}

/**
 * @Rename Product Menu
 * return {}
 */
if (!function_exists('workreap_label_woo')) {
	add_filter('woocommerce_register_post_type_product', 'workreap_label_woo');
	function workreap_label_woo($args) {
		if(current_user_can('administrator')) {
			$labels = array(
				'name' 					=> esc_html__('Packages/Products', 'workreap_core'),
				'singular_name' 		=> esc_html__('Packages/Products', 'workreap_core'),
				'menu_name' 			=> esc_html__('Packages/Products', 'workreap_core'),
				'add_new' 				=> esc_html__('Add Package/Product', 'workreap_core'),
				'add_new_item' 			=> esc_html__('Add New Package/Product', 'workreap_core'),
				'edit' 					=> esc_html__('Edit Package/Product', 'workreap_core'),
				'edit_item' 			=> esc_html__('Edit Package/Product', 'workreap_core'),
				'new_item' 				=> esc_html__('New Package/Product', 'workreap_core'),
				'view' 					=> esc_html__('View Package/Product', 'workreap_core'),
				'view_item' 			=> esc_html__('View Package/Product', 'workreap_core'),
				'search_items' 			=> esc_html__('Search Packages/Product', 'workreap_core'),
				'not_found' 			=> esc_html__('No Packages/Products found', 'workreap_core'),
				'not_found_in_trash' 	=> esc_html__('No Packages/Products found in trash', 'workreap_core'),
				'parent' 				=> esc_html__('Parent Package/Product', 'workreap_core')
			);

			$args['labels'] = $labels;
			$args['description'] = esc_html__('This is where you can add new tours to your store.', 'workreap_core');
		}
		return $args;
	}
}


/**
 * set job/service order status
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists( 'worktic_append_statuses' ) ) {
	add_action('admin_footer-post.php', 'worktic_append_statuses');
	function worktic_append_statuses(){
		 global $post;
		 $selected = '';
		 $statuses = apply_filters('worktic_job_statuses','default');

		 if( $post->post_type == 'projects' || $post->post_type == 'services-orders' ){
			ob_start();
		?>
		<script>
			jQuery(document).ready(function($){            
			<?php 
			foreach ( $statuses as $key => $value ) {                     
				if( $post->post_status == $key ){
					$selected = 'selected';
				} else {
					$selected = '';
				}
				?>
				jQuery("#post-status-select select#post_status").append("<option value='<?php echo esc_attr( $key ); ?>' <?php if( $post->post_status == $key ){ ?> selected='selected' <?php } ?>><?php echo esc_attr( $value ); ?></option>");
				<?php if( $post->post_status == $key ){ ?>
					jQuery("#post-status-display").append("<?php echo esc_attr( $value ); ?>");
				<?php } ?>
				<?php if( $post->post_status == 'hired' || $post->post_status == 'completed' ){ ?>
					 	jQuery("#publish").val("Update");
						jQuery("#publish").attr("name","save");
						jQuery("#original_publish").val("Update");
				<?php } ?>
				<?php } ?>          
			});
		</script>
		<?php 
			echo ob_get_clean();
		 }
	}
}

/**
 * set service status
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists( 'worktic_append_serviece_statuses' ) ) {
	add_action('admin_footer-post.php', 'worktic_append_serviece_statuses');
	function worktic_append_serviece_statuses(){
		 global $post;
		 $selected = '';
		 $statuses = apply_filters('worktic_service_statuses','default');

		 if( $post->post_type == 'micro-services' ){
			ob_start();
		?>
		<script>
			jQuery(document).ready(function($){            
			<?php 
			foreach ( $statuses as $key => $value ) {                     
				if( $post->post_status == $key ){
					$selected = 'selected';
				} else {
					$selected = '';
				}
				?>
				jQuery("#post-status-select select#post_status").append("<option value='<?php echo esc_attr( $key ); ?>' <?php if( $post->post_status == $key ){ ?> selected='selected' <?php } ?>><?php echo esc_attr( $value ); ?></option>");
				<?php if( $post->post_status == $key ){ ?>
					jQuery("#post-status-display").append("<?php echo esc_attr( $value ); ?>");
				<?php } ?>
				<?php if( $post->post_status == 'deleted' ){ ?>
					 	jQuery("#publish").val("Update");
						jQuery("#publish").attr("name","save");
						jQuery("#original_publish").val("Update");
				<?php } ?>
				<?php } ?>          
			});
		</script>
		<?php 
			echo ob_get_clean();
		 }
	}
}

/**
 * Display form field with list of authors.
 * Modified version of post_author_meta_box().
 *
 * @global int $user_ID
 *
 * @param object $post
 */
if (!function_exists('workreap_post_author_meta_box')) {
	function workreap_post_author_meta_box( $post ) {
		global $user_ID;
		?>
		<label class="screen-reader-text" for="post_author_override"><?php esc_html_e( 'Author', 'workreap_core' ); ?></label>
		<?php
		$roles	= array('employers');
		
		wp_dropdown_users( array(
			'role__in' 	=>$roles,
			'name' 		=> 'post_author_override',
			'selected' 	=> empty( $post->ID ) ? $user_ID : $post->post_author,
			'include_selected' => true,
			'show' 		=> 'display_name_with_login',
		) );
	}
}

/**
 * Display form field with list of authors.
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_post_author_meta_box_freelancer')) {
	function workreap_post_author_meta_box_freelancer( $post ) {
		global $user_ID;
		?>
		<label class="screen-reader-text" for="post_author_override"><?php esc_html_e( 'Author', 'workreap_core' ); ?></label>
		<?php
		$roles	= array('freelancers');
		wp_dropdown_users( array(
			'role__in' 	=> $roles,
			'name' 		=> 'post_author_override',
			'selected' 	=> empty( $post->ID ) ? $user_ID : $post->post_author,
			'show' 		=> 'display_name_with_login',
			'include_selected' => true,
			
		) );
	}
}

/**
 * @Prepare social sharing links
 * @return sizes
 */
if (!function_exists('workreap_prepare_social_sharing')) {

    function workreap_prepare_social_sharing($default_icon = 'false', $social_title = 'Share', $title_enable = 'true', $classes = '', $thumbnail = '') {
		global $wp_query;
        $output    = '';

        if (function_exists('fw_get_db_post_option')) {
            $social_facebook = fw_get_db_settings_option('social_facebook');
            $social_twitter = fw_get_db_settings_option('social_twitter');
			$social_pinterest = fw_get_db_settings_option('social_pinterest');
            $twitter_username = !empty($social_twitter['enable']['twitter_username']) ? $social_twitter['enable']['twitter_username'] : '';
        } else {
            $social_facebook 	= 'enable';
            $social_twitter 	= 'enable';
			$social_pinterest 	= 'enable';
            $twitter_username 	= '';
        }

		//author page
		if( is_author() ){
			$author_profile = $wp_query->get_queried_object();
			$permalink		= esc_url(get_author_posts_url($author_profile->ID));
			$title			= workreap_get_username($author_profile->ID);;
		} else{
			$permalink	= get_the_permalink();
			$title		=  get_the_title();
		}

        $output .= "<ul class='wt-socialiconssimple wt-blogsocialicons'>";
        if ($title_enable == 'true' && !empty( $social_title )) {
            $output .= '<li class="wt-sharejob"><span>' . $social_title . ':</span></li>';
        }
		
        if (isset($social_facebook) && $social_facebook == 'enable') {
            $output .= '<li class="wt-facebook"><a href="//www.facebook.com/sharer.php?u=' . urlencode(esc_url($permalink)) . '" onclick="window.open(this.href, \'post-share\',\'left=50,top=50,width=600,height=350,toolbar=0\'); return false;"><i class="fa fa-facebook-f"></i></a></li>';
        }

        if (isset($social_twitter['gadget']) &&
                $social_twitter['gadget'] == 'enable'
        ) {
            $output .= '<li class="wt-twitter"><a href="//twitter.com/intent/tweet?text=' . htmlspecialchars(urlencode(html_entity_decode($title, ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') . '&url=' . urlencode(esc_url($permalink)) . '&via=' . urlencode(!empty($twitter_username) ? $twitter_username : get_bloginfo('name') ) . '"  ><i class="fa fa-twitter"></i></a></li>';
            $tweets = '!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");';
            wp_add_inline_script('workreap_callbacks', $tweets);
		}
		
        if (isset($social_pinterest) && $social_pinterest == 'enable') {
            $output .= '<li class="wt-pinterestp"><a href="//pinterest.com/pin/create/button/?url=' . esc_url($permalink) . '&amp;media=' . (!empty($thumbnail) ? $thumbnail : '' ) . '&description=' . htmlspecialchars(urlencode(html_entity_decode($title, ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') . '" onclick="window.open(this.href, \'post-share\',\'left=50,top=50,width=600,height=350,toolbar=0\'); return false;"><i class="fa fa-pinterest-p"></i></a></li>';
        }

        $output .= '</ul>';
		
        echo do_shortcode($output, true);
    }

}

/**
 * Prepare social sharing links for job
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_prepare_project_social_sharing')) {

    function workreap_prepare_project_social_sharing($default_icon = 'false', $social_title = 'Share', $title_enable = 'true', $classes = '', $thumbnail = '') {        
        global $wp_query;
		$output    = '';
		
		if (function_exists('fw_get_db_post_option')) {
            $social_facebook = fw_get_db_settings_option('social_facebook');
            $social_twitter = fw_get_db_settings_option('social_twitter');
            $social_pinterest = fw_get_db_settings_option('social_pinterest');
            $social_linkedin = fw_get_db_settings_option('social_linkedin');
            $twitter_username = !empty($social_twitter['enable']['twitter_username']) ? $social_twitter['enable']['twitter_username'] : '';
        } else {
            $social_facebook 	= 'enable';
            $social_twitter 	= 'enable';
            $social_pinterest 	= 'enable';
            $social_linkedin 	= 'enable';
            $twitter_username 	= '';
        }

        if (function_exists('fw_get_db_post_option')) {
            $twitter_username = !empty($social_twitter['enable']['twitter_username']) ? $social_twitter['enable']['twitter_username'] : '';
			$hide_hideshares = fw_get_db_settings_option('hide_hideshares');
        } else {            
            $twitter_username = 'twitter';
        }
        
		if(!empty( $hide_hideshares ) && $hide_hideshares === 'no'){
			$permalink  = get_the_permalink();
			$title      = get_the_title();
			
			if( !empty( $social_title ) ){
				$output .= '<div class="wt-widget wt-sharejob">';
				$output .= '<div class="wt-widgettitle">';
				$output .= '<h2>'.esc_attr($social_title).'</h2>';
				$output .= '</div>';
			}
			
			$output .= "<div class='wt-widgetcontent'><ul class='wt-socialiconssimple'>"; 
				if (isset($social_linkedin) && $social_linkedin == 'enable') {
					$output .= '<li class="wt-linkedin"><a href="https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode(esc_url($permalink)) . '&title=' . htmlspecialchars(urlencode(html_entity_decode($title, ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') . '" rel="noopener" target="_blank"><i class="fa fa-linkedin"></i><span>'.esc_html__("Share on linkedin", "workreap_core").'<span></a></li>';
				}
                if (isset($social_facebook) && $social_facebook == 'enable') {
                    $output .= '<li class="wt-facebook"><a href="//www.facebook.com/sharer.php?u=' . urlencode(esc_url($permalink)) . '" onclick="window.open(this.href, \'post-share\',\'left=50,top=50,width=600,height=350,toolbar=0\'); return false;"><i class="fa fa-facebook-f"></i><span>'.esc_html__("Share on Facebook", "workreap_core").'</span></a></li>';
				}
				if (isset($social_twitter['gadget']) &&
                $social_twitter['gadget'] == 'enable'
        		) {
                    $output .= '<li class="wt-twitter"><a href="//twitter.com/intent/tweet?text=' . htmlspecialchars(urlencode(html_entity_decode($title, ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') . '&url=' . urlencode(esc_url($permalink)) . '&via=' . urlencode(!empty($twitter_username) ? $twitter_username : get_bloginfo('name')) . '"  ><i class="fa fa-twitter"></i><span>'.esc_html__("Share on Twitter", "workreap_core").'</span></a></li>';
                    $tweets = '!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");';
                    wp_add_inline_script('workreap-callbacks', $tweets);
				}
                if (isset($social_pinterest) && $social_pinterest == 'enable') {
                    $output .= '<li class="wt-pinterestp"><a href="//pinterest.com/pin/create/button/?url=' . esc_url($permalink) . '&amp;media=' . (!empty($thumbnail) ? $thumbnail : '') . '&description=' . htmlspecialchars(urlencode(html_entity_decode($title, ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') . '" onclick="window.open(this.href, \'post-share\',\'left=50,top=50,width=600,height=350,toolbar=0\'); return false;"><i class="fa fa-pinterest-p"></i><span>'.esc_html__("Share on Pinterest", "workreap_core").'</span></a></li>';
                }
			$output .= '</ul></div>';
			$output .= '</div>';
			echo do_shortcode($output, true);
		}
    }
}

/**
 * @Import User Menu
 * @return {}
 */
if (!function_exists('workreap_import_users_menu')) {
	add_action('admin_menu', 'workreap_import_users_menu');
	function  workreap_import_users_menu(){
		add_submenu_page('edit.php?post_type=freelancers', 
							 esc_html__('Import User','workreap_core'), 
							 esc_html__('Import User','workreap_core'), 
							 'manage_options', 
							 'import_users',
							 'workreap_import_users_template'
						 );

	}
}

/**
 * @Import Users
 * @return {}
 */
if (!function_exists('workreap_import_users_template')) {
	function  workreap_import_users_template(){
		
		$permalink = add_query_arg( 
								array(
									'&type=file',
								)
							);	
		
		//Import users via file
		if ( !empty( $_FILES['users_csv']['tmp_name'] ) ) {
			$import_user	= new SP_Import_User();
			$import_user->workreap_import_user();
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e('User Imported Successfully','workreap_core');?></p>
			</div>
			<?php
		}
	   ?>
       <h3 class="theme-name"><?php esc_html_e('Import Employers/Freelancers','workreap_core');?></h3>
       <div id="import-users" class="import-users">
            <div class="theme-screenshot">
                <img alt="<?php esc_attr_e('Import Users','workreap_core');?>" src="<?php echo get_template_directory_uri();?>/admin/images/users.jpg">
            </div>
			<h3 class="theme-name"><?php esc_html_e('Import Users','workreap_core');?></h3>
            <div class="user-actions">
                <a href="javascript:;" class="button button-primary doc-import-users"><?php esc_html_e('Import Dummy','workreap_core');?></a>
            </div>
	   </div>
       <div id="import-users" class="import-users custom-import">
            <form method="post" action="<?php echo cus_prepare_final_url('file','import_users'); ?>"  enctype="multipart/form-data">
				<div class="theme-screenshot">
					<img alt="<?php esc_attr_e('Import Users','workreap_core');?>" src="<?php echo get_template_directory_uri();?>/admin/images/excel.jpg">
				</div>
				<h3 class="theme-name">
					<input id="upload-dummy-csv" type="file" name="users_csv" >
					<label for="upload-dummy-csv" class="button button-primary upload-dummy-csv"><?php esc_html_e('Choose File','workreap_core');?></lable>
				</h3>
				<div class="user-actions">
					<input type="submit" class="button button-primary" value="<?php esc_html_e('Import From File','workreap_core');?>">
					
				</div>
            </form>
		</div>
        <?php
	}
}


/**
 * @init            tab url
 * @package         Amentotech
 * @subpackage      tailors-online/admin/partials
 * @since           1.0
 * @desc            Display The Tab System URL
 */
if (!function_exists('cus_prepare_final_url')) {

    function cus_prepare_final_url($tab='',$page='import_users') {
		$permalink = '';
		$permalink = add_query_arg( 
								array(
									'?page'	=>   urlencode( $page ) ,
									'tab'	=>   urlencode( $tab ) ,
								)
							);	
		
		return esc_url( $permalink );
	}
}

/**
 * @Import Users
 * @return {}
 */
if (!function_exists('workreap_import_users')) {
	function  workreap_import_users(){
		$import_user	= new SP_Import_User();
		$import_user->workreap_import_user();
		
		
		if ( function_exists('workreap_update_authors')) { workreap_update_authors(); }
		if ( function_exists('workreap_update_project_authors')) { workreap_update_project_authors(); }
		if ( function_exists('workreap_update_service_authors')) { workreap_update_service_authors(); }
		if ( function_exists('workreap_addon_services')) { workreap_addon_services(); }
		
		$json	= array();
		$json['type']	= 'success';	
		$json['message']	= esc_html__('User Imported Successfully','workreap_core' );
		echo json_encode( $json );
		die;	
	}
	add_action('wp_ajax_workreap_import_users', 'workreap_import_users');	
}



/**
 * @Import Users
 * @return {}
 */
if (!function_exists('workreap_save_theme_settings')) {
	function  workreap_save_theme_settings(){
		$settings	= $_POST['settings'];
		$json		= array();
		
		update_option( 'wt_theme_settings', $settings, true );
		
		$json['type']	= 'success';	
		$json['message']	= esc_html__('Settings updated','workreap_core' );
		echo json_encode( $json );
		die;	
	}
	add_action('wp_ajax_workreap_save_theme_settings', 'workreap_save_theme_settings');	
}

/**
 * @get settings
 * @return {}
 */
if (!function_exists('workreap_get_theme_settings')) {
	function  workreap_get_theme_settings($key='',$type=''){
		$sp_theme_settings = get_option( 'wt_theme_settings' );
		$setting	= !empty( $sp_theme_settings[$type][$key] ) ? $sp_theme_settings[$key] : $sp_theme_settings;
		return $setting;
	}
	add_filter('workreap_get_theme_settings', 'workreap_get_theme_settings', 10, 2);
}

/**
 * @Resolve Dispute
 * @return {}
 */
if (!function_exists('workreap_resolve_dispute')) {
	function  workreap_resolve_dispute(){
		global $wpdb;
		$current_date 	= current_time('mysql');
		$gmt_time		= current_time( 'mysql', 1 );
		
		$json			= array();

		$user_id 				= !empty($_POST['user_id']) ? $_POST['user_id'] : '';
		$dispute_id 			= !empty($_POST['dispute_id']) ? $_POST['dispute_id'] : '';
		$disputed_project_id 	= !empty($_POST['dispute_project_id']) ? $_POST['dispute_project_id'] : '';
		$proj_serv_id 			= !empty($_POST['proj_serv_id']) ? $_POST['proj_serv_id'] : '';
		$freelancer_msg 		= !empty($_POST['freelancer_msg']) ? $_POST['freelancer_msg'] : '';
		$employer_msg 			= !empty($_POST['employer_msg']) ? $_POST['employer_msg'] : '';
		$project_type			= get_post_type($disputed_project_id);

        if (!empty($user_id) && !empty($freelancer_msg) && !empty($employer_msg)) {
            $feedback		= !empty($_POST['feedback']) ? $_POST['feedback'] : '';
            if (function_exists('fw_set_db_post_option')) {
                fw_set_db_post_option($dispute_id, 'feedback', $feedback);
            }

            $linked_profile = workreap_get_linked_profile_id($user_id);
            $post_type  	= get_post_type($linked_profile);
			
        
			
			$freelancer_id	= !empty($_POST['freelancer_id']) ? $_POST['freelancer_id'] : '';
			$employer_id	= !empty($_POST['employer_id']) ? $_POST['employer_id'] : '';
			
			$raised_by_id	= get_post_meta($dispute_id, '_send_by', true);
			$raised_by_name	= workreap_get_username($raised_by_id);
            $earnings 		= workreap_get_row_earnings($freelancer_id, $proj_serv_id);
			
			$amount_share = '0.00';
			
			if(!empty($post_type) && $post_type == 'freelancers') {
				$amount_share = $earnings->freelancer_amount;
			} else if (!empty($post_type) && $post_type == 'employers') {
				$amount_share = $earnings->amount;
			}

			$earning	= array();
			

            $earning['freelancer_amount']	= $amount_share;
            $earning['admin_amount'] 		= '0.00';
            $earning['user_id']				= $user_id;
            $earning['amount']				= $amount_share;
            $earning['project_id']			= $earnings->project_id;
            $earning['order_id']		    = $earnings->order_id;
            $earning['process_date'] 	    = date('Y-m-d H:i:s', strtotime($current_date));
            $earning['date_gmt'] 		    = date('Y-m-d H:i:s', strtotime($gmt_time));
            $earning['year'] 			    = date('Y', strtotime($current_date));
            $earning['month'] 			    = date('m', strtotime($current_date));
            $earning['timestamp'] 		    = strtotime($current_date);
            $earning['project_type'] 		= $earnings->project_type;
            $earning['status'] 			    = 'completed';
        
            if (function_exists('workreap_get_current_currency')) {
                $currency					= workreap_get_current_currency();
                $earning['currency_symbol']	= $currency['symbol'];
            } else {
                $earning['currency_symbol']	= '$';
			}

			$table_name = $wpdb->prefix . "wt_earnings";
			
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {

				$tablename = $wpdb->prefix.'wt_earnings';

				if($post_type == 'freelancers') {
					
					$where	= array( 
						'project_id' => intval($proj_serv_id),
					);
					$update		= array('status' => 'completed');
					workreap_update_earning($where, $update, 'wt_earnings');

					wp_update_post(array(
						'ID'    	    =>  intval($proj_serv_id),
						'post_status'   =>  'cancelled'
					));

				} else if( $post_type == 'employers' ) {
					$wpdb->insert($tablename, $earning);

					$where	= array( 
						'project_id' => intval($proj_serv_id),
						'status' 	 => 'hired',
					);
					
					$update		= array('status' => 'cancelled');

					workreap_update_earning($where, $update, 'wt_earnings');

					wp_update_post(array(
						'ID'    	    =>  intval($proj_serv_id),
						'post_status'   =>  'cancelled'
					));
				}

				if (!empty($project_type) && $project_type === 'proposals') {
                    update_post_meta($disputed_project_id, 'dispute', 'yes');
                } else if (!empty($project_type) && $project_type === 'services-orders') {
					update_post_meta($proj_serv_id, 'dispute', 'yes');
				}

                update_post_meta($dispute_id, 'winning_party', $user_id);
                wp_publish_post($dispute_id);

                //Send email to user
                if (class_exists('Workreap_Email_helper')) {
                    if (class_exists('WorkreapSendDispute')) {
                        $email_helper = new WorkreapSendDispute();
                        $emailData = array();

                        $emailData['dispute_raised_by'] = $raised_by_name;
						
						//Freelancer
                        $freelancer_email 				= get_userdata($freelancer_id)->user_email;
						$emailData['freelancer_email']  = $freelancer_email;
						$emailData['freelancer_name']  	= workreap_get_username($freelancer_id);
						$emailData['admin_message']  	= $freelancer_msg;

						$email_helper->send_resolved_dispute_freelancer($emailData);
						
						//employer 
						$employer_email 				= get_userdata($employer_id)->user_email;
						$emailData['employer_email']  	= $employer_email;
						$emailData['employer_name']  	= workreap_get_username($employer_id);
						$emailData['admin_message']  	= $employer_msg;

						$email_helper->send_resolved_dispute_employer($emailData);
                    }
                }
            }
			$json['type']		= 'success';	
			$json['message']	= esc_html__('Dispute Resolved', 'workreap_core' );
			echo json_encode( $json );
			die;	
        } else {
			$json['type']		= 'error';	
			echo json_encode( $json );
			die;
		}

	}
	add_action('wp_ajax_workreap_resolve_dispute', 'workreap_resolve_dispute');	
}