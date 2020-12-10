<?php
/**
 *
 * Workreap function for menu
 *
 * @package   Workreap
 * @author    amentotech
 * @link      https://themeforest.net/user/amentotech/portfoliot
 * @since 1.0
 */
if (!class_exists('Workreap_Profile_Menu')) {

    class Workreap_Profile_Menu {

        protected static $instance = null;

        public function __construct() {
            //Do something
        }

		/**
		 * Returns the *Singleton* instance of this class.
		 *
		 * @throws error
		 * @author Amentotech <theamentotech@gmail.com>
		 * @return 
		 */
        public static function getInstance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

		/**
		 * Profile Menu top
		 *
		 * @throws error
		 * @author Amentotech <theamentotech@gmail.com>
		 * @return 
		 */
        public static function workreap_profile_menu_top() {
            global $current_user, $wp_roles, $userdata, $post;
            ob_start();
            $username 		= workreap_get_username($current_user->ID);
			$user_identity 	= $current_user->ID;
			$user_type		= apply_filters('workreap_get_user_type', $user_identity );
			$link_id		= workreap_get_linked_profile_id( $user_identity );
			
			if (function_exists('fw_get_db_post_option')) {
				$tag_line = fw_get_db_post_option($link_id, 'tag_line', false);
			}

			if (function_exists('fw_get_db_settings_option')) {
                $comet_chat = fw_get_db_settings_option('chat');
			}
			
			$is_cometchat = false;
			if (!empty($comet_chat['gadget']) && $comet_chat['gadget'] === 'cometchat') {
				$is_cometchat = true;
			}
			
			if ( $user_type === 'employer' ){
				$avatar = apply_filters(
										'workreap_employer_avatar_fallback', workreap_get_employer_avatar(array('width' => 150, 'height' => 150), $link_id), array('width' => 150, 'height' => 150) 
									);
			} else{
				$avatar = apply_filters(
										'workreap_freelancer_avatar_fallback', workreap_get_freelancer_avatar(array('width' => 150, 'height' => 150), $link_id), array('width' => 150, 'height' => 150) 
									);
			}

			if( $user_type === 'employer' || $user_type === 'freelancer' || $user_type === 'subscriber' ) {?>
				<div class="wt-userlogedin sp-top-menu">
					<figure class="wt-userimg">
						<img src="<?php echo esc_url($avatar); ?>" alt="<?php esc_attr_e('Profile Avatar', 'workreap'); ?>">
						<?php if( $user_type === 'freelancer' || $user_type === 'employer' ){?>
							<em class="wtunread-count">
							<?php
								if ($is_cometchat) {
									do_action('workreap_get_unread_msgs', $user_identity );
								} else {
									do_action('workreap_chat_count', $user_identity );
								}
							?>
							</em>
						<?php } ?>
					</figure>
					<div class="wt-username">
						
						<h3><?php echo esc_html($username); ?></h3>
						<?php if( !empty( $tag_line ) ){?>
							<span><?php echo esc_html(stripslashes($tag_line)); ?></span>
						<?php }?>
					</div>
					<nav class="wt-usernav">
						<?php self::workreap_profile_menu('dashboard-menu-top'); ?>
					</nav>
				</div>
            <?php
			}
            echo ob_get_clean();
        }

		/**
		 * Profile Menu top Notification Tab
		 *
		 * @throws error
		 * @author Amentotech <theamentotech@gmail.com>
		 * @return 
		 */
        public static function workreap_profile_menu_notification() {
            global $current_user;
            ob_start();
			?>
				<div class="wt-userlogedin sp-top-menu wt-notification-icon">
					<figure class="wt-notification-icon-figure">
						<a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('notifications', $current_user->ID); ?>">
							<i class="ti-bell"></i>
							<em class="wtunread-count">
								<?php if( class_exists('NotificationSystem') ) { echo NotificationSystem::getNewUserNotificationsCount($current_user->ID); } ?>
							</em>
						</a>
					</figure>
				</div>
            <?php
            echo ob_get_clean();
        }

		/**
		 * Profile Menu Left
		 *
		 * @throws error
		 * @author Amentotech <theamentotech@gmail.com>
		 * @return 
		 */
        public static function workreap_profile_menu_left() {
            global $current_user, $wp_roles, $userdata, $post;
			if ( function_exists( 'fw_get_db_settings_option' ) ) {
				$db_left_menu 	= fw_get_db_settings_option( 'db_left_menu', $default_value = null );
			} 

			if( isset( $db_left_menu ) && $db_left_menu === 'no' ){
				ob_start();
				?>
				<div class="wt-sidebarwrapper">
					<div id="wt-btnmenutoggle" class="wt-btnmenutoggle">
						<span class="menu-icon">
							<em></em>
							<em></em>
							<em></em>
						</span>
					</div>
					<div id="wt-verticalscrollbar" class="wt-verticalscrollbar">
						<?php self::workreap_do_process_userinfo(); ?>
						<nav id="wt-navdashboard" class="wt-navdashboard">
							<?php self::workreap_profile_menu('dashboard-menu-left'); ?>
						</nav>
					</div>
				</div>
				<?php
				echo ob_get_clean();
			}
        }

		/**
		 * Profile Menu
		 *
		 * @throws error
		 * @author Amentotech <theamentotech@gmail.com>
		 * @return 
		 */
        public static function workreap_profile_menu($menu_type = "dashboard-menu-left") {
            global $current_user, $wp_roles, $userdata, $post;
			$reference 		 = (isset($_GET['ref']) && $_GET['ref'] <> '') ? $_GET['ref'] : '';
			$mode 			 = (isset($_GET['mode']) && $_GET['mode'] <> '') ? $_GET['mode'] : '';
			$user_identity 	 = $current_user->ID;

			$url_identity = $user_identity;
			if (isset($_GET['identity']) && !empty($_GET['identity'])) {
				$url_identity = $_GET['identity'];
			}

			$menu_list 	= workreap_get_dashboard_menu();
            ob_start();
            ?>
            <ul class="<?php echo sanitize_html_class($menu_type); ?>">
                <?php
					if ( $url_identity == $user_identity ) {
						if( !empty( $menu_list ) ){
							foreach($menu_list as $key => $value){
								if( !empty( $value['type'] ) && ( $value['type'] == apply_filters('workreap_get_user_type', $user_identity ) ) ){
									get_template_part('directory/front-end/dashboard-menu-templates/'.$value['type'].'/profile-menu', $key);
								} else{
									get_template_part('directory/front-end/dashboard-menu-templates/profile-menu', $key);
								}
							}
						}
					} 
                ?>
            </ul>
            <?php
            echo ob_get_clean();
        }

		/**
		 * Generate Menu Link
		 *
		 * @throws error
		 * @author Amentotech <theamentotech@gmail.com>
		 * @return 
		 */
        public static function workreap_profile_menu_link($slug = '', $user_identity = '', $return = false, $mode = '', $id = '') {
			$profile_page = ''; 
			$profile_page = workreap_get_search_page_uri('dashboard');  
			
            if ( empty( $profile_page ) ) {
                $permalink = home_url('/');
            } else {
                $query_arg['ref'] = urlencode($slug);

                //mode
                if (!empty($mode)) {
                    $query_arg['mode'] = urlencode($mode);
                }
				
                //id for edit record
                if (!empty($id)) {
                    $query_arg['id'] = urlencode($id);
                }

                $query_arg['identity'] = urlencode($user_identity);

                $permalink = add_query_arg(
                        $query_arg, esc_url( $profile_page  )
                );
				
            }

            if ($return) {
                return esc_url_raw($permalink);
            } else {
                echo esc_url_raw($permalink);
            }
        }

		/**
		 * Generate Profile Avatar Image Link
		 *
		 * @throws error
		 * @author Amentotech <theamentotech@gmail.com>
		 * @return 
		 */
        public static function workreap_get_avatar() {
            global $current_user, $wp_roles, $userdata, $post;
            $user_identity 	= $current_user->ID;
			$user_type		= apply_filters('workreap_get_user_type', $user_identity );
			$link_id		= workreap_get_linked_profile_id( $user_identity );
			
			if ( apply_filters('workreap_get_user_type', $user_identity) === 'employer' ){
				
				$avatar = apply_filters(
										'workreap_employer_avatar_fallback', workreap_get_employer_avatar(array('width' => 150, 'height' => 150), $link_id), array('width' => 150, 'height' => 150) 
									);
			} else{
				$avatar = apply_filters(
										'workreap_freelancer_avatar_fallback', workreap_get_freelancer_avatar(array('width' => 150, 'height' => 150), $link_id), array('width' => 150, 'height' => 150) 
									);
			}
			
            ?>
            <figure><img src="<?php echo esc_url( $avatar );?>" alt="<?php esc_attr_e('avatar', 'workreap'); ?>"></figure>
            <?php
        }

		/**
		 * Generate Profile Banner Image Link
		 *
		 * @throws error
		 * @author Amentotech <theamentotech@gmail.com>
		 * @return 
		 */
        public static function workreap_get_banner() {
            global $current_user, $wp_roles, $userdata, $post;

            $user_identity 	= $current_user->ID;
			$user_type		= apply_filters('workreap_get_user_type', $user_identity );
			$link_id		= workreap_get_linked_profile_id($user_identity );
			if ( apply_filters('workreap_get_user_type', $user_identity) === 'employer' ){
				$banner = apply_filters(
										'workreap_employer_banner_fallback', workreap_get_employer_banner(array('width' => 350, 'height' => 172), $link_id), array('width' => 350, 'height' => 172) 
										);
			} else{
				$banner = apply_filters(
										'workreap_freelancer_banner_fallback', workreap_get_freelancer_banner(array('width' => 350, 'height' => 172), $link_id), array('width' => 350, 'height' => 172) 
										);
			}
            ?>
            
            <figure class="wt-companysimg"><img src="<?php echo esc_url( $banner );?>" alt="<?php esc_attr_e('banner', 'workreap'); ?>"></figure>
            <?php
        }
		
		/**
		 * Generate Profile Information
		 *
		 * @throws error
		 * @author Amentotech <theamentotech@gmail.com>
		 * @return 
		 */
        public static function workreap_get_user_info() {
            global $current_user;
            $user_identity = $current_user->ID;
            $user_identity = $user_identity;
            if (isset($_GET['identity']) && !empty($_GET['identity'])) {
                $user_identity = $_GET['identity'];
            }
			
			$link_id		= workreap_get_linked_profile_id( $user_identity );
			if (function_exists('fw_get_db_post_option')) {
				$tag_line = fw_get_db_post_option($link_id, 'tag_line', false);
			}
			
            $get_username 	= workreap_get_username($user_identity);
            ?>
            <div class="wt-title">
				<?php if (!empty($get_username)) { ?><h2><a target="_blank" href="<?php echo esc_url(get_the_permalink($link_id));?>"><?php echo esc_html($get_username); ?></a></h2><?php } ?>
				<?php if (!empty($tag_line)) { ?>
					<span><?php echo esc_html(stripslashes($tag_line)); ?></span>
				<?php } ?>
			</div>
            <?php
        }
		
		/**
		 * Get user info
		 *
		 * @throws error
		 * @author Amentotech <theamentotech@gmail.com>
		 * @return 
		 */
        public static function workreap_do_process_userinfo() {
            global $current_user, $wp_roles, $userdata, $post;
            $reference 		= (isset($_GET['ref']) && $_GET['ref'] <> '') ? $_GET['ref'] : $reference = '';
            $user_identity	= $current_user->ID;
			$link_id		= workreap_get_linked_profile_id($user_identity );
			workreap_return_system_access();
            ?>
            <div class="wt-companysdetails wt-usersidebar">
				<?php self::workreap_get_banner(); ?>
				<div class="wt-companysinfo">
					<?php self::workreap_get_avatar(); ?>
					<?php self::workreap_get_user_info(); ?>
					<?php if ( apply_filters('workreap_get_user_type', $user_identity) === 'employer' ){
						if( apply_filters('workreap_system_access','job_base') === true ){?>
						<div class="wt-btnarea"><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('post_job', $user_identity); ?>" class="wt-btn"><?php esc_html_e('Post a Job', 'workreap'); ?></a></div>
					<?php }}  elseif ( apply_filters('workreap_get_user_type', $user_identity) === 'freelancer' ) {
								if( apply_filters('workreap_module_access', 'projects') ){ ?>
									<div class="wt-btnarea"><a href="<?php echo esc_url(get_the_permalink( $link_id ) );?>" class="wt-btn"><?php esc_html_e('View Profile', 'workreap'); ?></a></div>
								<?php }?>
								<?php if ( apply_filters('workreap_system_access', 'service_base') === true) { ?>
									<div class="wt-btnarea">
										<a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('micro_service', $user_identity); ?>" class="wt-btn"><?php esc_html_e('Post a Service', 'workreap'); ?></a>
									</div>
							<?php } ?>
					<?php } ?>
				</div>
			</div>
            <?php
        }

    }

    new Workreap_Profile_Menu();
}
