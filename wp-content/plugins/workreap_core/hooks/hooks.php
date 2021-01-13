<?php
/**
 *
 * @package   Workreap Core
 * @author    amentotech
 * @link      https://themeforest.net/user/amentotech/portfolio
 * @since 1.0
 */
/**

/**
 * @get default color schemes
 * @return 
 */
if (!function_exists('workreap_get_domain')) {
	add_filter('workreap_get_domain','workreap_get_domain',10,1);
	function workreap_get_domain(){
		if( isset( $_SERVER["SERVER_NAME"] ) && $_SERVER["SERVER_NAME"] === 'amentotech.com' ){
			return true;
		} else{
			return false;
		}
	}
}

/**
 * REcaptucha
 *
 * @param json
 * @return string
 */
if (!function_exists('workreap_get_recaptcha_response')) {

    function workreap_get_recaptcha_response($recaptcha_data = '') {
		$status_cap = 0;
        if (function_exists('fw_get_db_settings_option')) {
            $response = null;
            $secret_key = fw_get_db_settings_option('secret_key', $default_value = null);

            if (!empty($secret_key)) {
                $reCaptcha = new ReCaptcha($secret_key);

                if ($recaptcha_data) {
                    $response = $reCaptcha->verifyResponse( $_SERVER["REMOTE_ADDR"], $recaptcha_data );
                }

                if ($response != null && $response->success) {
                    $status_cap = 1;
                } else {
                    $status_cap = 0;
                }
            } else {
                $status_cap = 2;
            }
        }

        return $status_cap;
    }

}

/**
 * @User social fields
 * @return fields
 */
if( !function_exists('workreap_user_social_fields')){
	function workreap_user_social_fields($user_fields) {
		$user_fields['twitter'] = esc_html__('Twitter', 'workreap_core');
		$user_fields['facebook'] = esc_html__('Facebook', 'workreap_core');
		$user_fields['google'] = esc_html__('Google+', 'workreap_core');
		$user_fields['tumblr'] = esc_html__('Tumbler', 'workreap_core');
		$user_fields['instagram'] = esc_html__('Instagram', 'workreap_core');
		$user_fields['pinterest'] = esc_html__('Pinterest', 'workreap_core');
		$user_fields['skype'] = esc_html__('Skype', 'workreap_core');
		$user_fields['linkedin'] = esc_html__('Linkedin', 'workreap_core');

		return $user_fields;
	}
	add_filter('user_contactmethods', 'workreap_user_social_fields');
}

/**
 * MSet post views
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_post_views')) {

    function workreap_post_views($post_id = '',$key='set_blog_view') {

        if (!is_single())
            return;

        if (empty($post_id)) {
            global $post;
            $post_id = $post->ID;
        }
		
        if (!isset($_COOKIE[$key . $post_id])) {
            setcookie($key . $post_id, $key, time() + 3600);
            $view_key = $key;

            $count = get_post_meta($post_id, $view_key, true);

            if ($count == '') {
                $count = 0;
                delete_post_meta($post_id, $view_key);
                add_post_meta($post_id, $view_key, '0');
            } else {
                $count++;
                update_post_meta($post_id, $view_key, $count);
            }
        }
    }

    add_action('workreap_post_views', 'workreap_post_views', 5, 2);
}

/**
 * @Wp Login
 * @return 
 */
if (!function_exists('workreap_ajax_login')) {

    function workreap_ajax_login() {        
        $user_array = array();
		$json		= array();
        $user_array['user_login'] = sanitize_text_field($_POST['username']);
        $user_array['user_password'] = sanitize_text_field($_POST['password']);
		$redirect	= !empty( $_POST['redirect'] ) ? esc_url( $_POST['redirect'] ) : '';
		
		$captcha_settings = '';
		if (function_exists('fw_get_db_settings_option')) { 
			$captcha_settings = fw_get_db_settings_option('captcha_settings', $default_value = null);
		}
		
		//recaptcha check
        if (isset($captcha_settings) && $captcha_settings === 'enable') {
            if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
                $docReResult = workreap_get_recaptcha_response($_POST['g-recaptcha-response']);

                if ($docReResult == 1) {
                    $workdone = 1;
                } else if ($docReResult == 2) {
					$json['type'] = 'error';
                    $json['message'] = esc_html__('An error occurred, please try again later.', 'workreap_core');
                    wp_send_json($json);
                } else {
					$json['type'] = 'error';
                    $json['message'] = esc_html__('Wrong reCaptcha. Please verify first.', 'workreap_core');
                    wp_send_json($json);
                }
            } else {
                wp_send_json(array('type' => 'error', 'message' => esc_html__('Please enter reCaptcha!', 'workreap_core')));
            }
        }
		
        if (isset($_POST['rememberme'])) {
            $remember = sanitize_text_field($_POST['rememberme']);
        } else {
            $remember = '';
        }

        if ($remember) {
            $user_array['remember'] = true;
        } else {
            $user_array['remember'] = false;
        }
		
        if ($user_array['user_login'] == '') {
            echo json_encode(array('type' => 'error', 'loggedin' => false, 'message' => esc_html__('Username should not be empty.', 'workreap_core')));
            exit();
        } elseif ($user_array['user_password'] == '') {
            echo json_encode(array('type' => 'error', 'loggedin' => false, 'message' => esc_html__('Password should not be empty.', 'workreap_core')));
            exit();
        } else {
			
			$user = wp_signon($user_array, false);
			
			if (is_wp_error($user)) {
				echo json_encode(array('type' => 'error', 'loggedin' => false, 'message' => esc_html__('Wrong email/username or password.', 'workreap_core')));
			} else {

				$user_meta  = get_userdata($user->ID);
				$user_roles = $user_meta->roles;

				$user_role = !empty($user_roles) ? $user_roles[0] : '';

				$user_promotions	= array();
                if( function_exists('fw_get_db_settings_option')  ){
                    $user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
                }
				
				$user_promotion 		= !empty($user_promotions['gadget']) ? $user_promotions['gadget'] : '';
				if( !empty($user_promotion) && $user_promotion ==='enable' ){
					do_action('workreap_update_users_marketing',$user->ID);
				}
				
				if (empty( $redirect )) {
					$redirect   = workreap_login_redirect($user->ID);
				}
				
				echo json_encode(array( 'job'=>'no','type' => 'success', 'role_type' => $user_role, 'redirect' => $redirect, 'url' => home_url('/'), 'loggedin' => true, 'message' => esc_html__('Successfully Logged in', 'workreap_core')));
				
			}
			
        }

        die();
    }

    add_action('wp_ajax_workreap_ajax_login', 'workreap_ajax_login');
    add_action('wp_ajax_nopriv_workreap_ajax_login', 'workreap_ajax_login');
}

/**
 * @Registration gender types
 * @return 
 */
if( !function_exists( 'workreap_gender_types' ) ){
	add_filter('workreap_gender_types', 'workreap_gender_types',10,1);
	function workreap_gender_types($list){
		$gender_list	= array();
		if ( function_exists( 'fw_get_db_settings_option' ) ) {
			$g_settings 		= fw_get_db_settings_option( 'gender_settings', $default_value = null );
			$is_true			= !empty( $g_settings['gadget'] ) ? $g_settings['gadget'] : 'no';
			$list				= !empty( $g_settings['yes']['gender_options'] ) ? $g_settings['yes']['gender_options'] : 'no';

			if( !empty( $list ) and is_array( $list ) && $is_true === 'yes' ){
				$list = array_filter($list);
				$list = array_combine(array_map('sanitize_title', $list), $list);
				$gender_list	= apply_filters('workreap_filter_gender_types',$list);
			}
		} 
		
		
		
		
		return $gender_list;
	}
}

/**
 * @Registration Step One
 * @return 
 */
if( !function_exists( 'workreap_registration_single_step' ) ){
	function workreap_registration_single_step(){
		$image_url	= '';
		$login_register	= array();
		$single_step_logo	= '';
		$enable_google_connect 	 = '';
		$enable_facebook_connect = '';
		$enable_linkedin_connect = '';
		$captcha_settings = '';

		if (function_exists('fw_get_db_settings_option')) { 
			$enable_google_connect 	 = fw_get_db_settings_option('enable_google_connect', $default_value = null);
			$enable_facebook_connect = fw_get_db_settings_option('enable_facebook_connect', $default_value = null);
			$enable_linkedin_connect = fw_get_db_settings_option('enable_linkedin_connect', $default_value = null);
			$captcha_settings = fw_get_db_settings_option('captcha_settings', $default_value = null);

			$login_register = fw_get_db_settings_option('enable_login_register');
			$image_url				= !empty($login_register['enable']['single_step_image']['url']) ? $login_register['enable']['single_step_image']['url'] : ''; 
			$single_step_logo		= !empty($login_register['enable']['single_step_logo']['url']) ? $login_register['enable']['single_step_logo']['url'] : ''; 
		}

		if (!empty( $login_register ) && $login_register['enable']['registration']['gadget'] === 'enable') {
			$terms_link 	= !empty( $login_register['enable']['registration']['enable']['terms_link'] ) ? $login_register['enable']['registration']['enable']['terms_link'] : '';
			$terms_link 	= !empty( $terms_link ) ? get_the_permalink($terms_link[0]) : '';
			$term_text 		= !empty( $login_register['enable']['registration']['enable']['term_text'] ) ? $login_register['enable']['registration']['enable']['term_text'] : esc_html__('Agree our terms and conditions', 'workreap_core');
		}
		
		ob_start();
		?>
		<div class="row align-items-center">
			<?php if( !empty($image_url) ){?>
				<div class="col-12 col-md-6 col-xl-5">
					<figure class="wt-joinnow-img">
						<img src="<?php echo esc_url($image_url);?>" alt="<?php esc_attr_e('Regidtration','workreap_core');?>">
					</figure>
				</div>
			<?php } ?>
			<div class="col-12 col-md-6 col-xl-7">
				<div class="wt-joinnowpopup-wrap">
					<?php if( !empty($single_step_logo) ){?>
						<strong class="wt-joinnow-logo"><img src="<?php echo esc_url($single_step_logo);?>" alt="<?php esc_attr_e('Regidtration logo','workreap_core');?>"></strong>
					<?php }?>
					<form class="wt-formtheme wt-joinnow-form" id="wt-single-joinnow-form" method="post">
						<fieldset>
							<div class="wt-popuptitletwo">
								<h4><?php esc_html_e("It's Free to Sign Up and Get Started.","workreap_core");?></h4>
								<span><?php esc_html_e("Already have account?","workreap_core");?> <a href="javascript:;" id="wt-single-sigin"><?php esc_html_e("Sign In Now","workreap_core");?></a></span>
							</div>
							<div class="form-group form-group-half">
								<input type="text" name="first_name" class="form-control" value="" placeholder="<?php esc_attr_e('First Name', 'workreap_core'); ?>">
							</div>
							<div class="form-group form-group-half">
								<input type="text" name="last_name" value="" class="form-control" placeholder="<?php esc_attr_e('Last Name', 'workreap_core'); ?>">
							</div>
							<div class="form-group">
								<input type="email" name="email" class="form-control" value=""  placeholder="<?php esc_attr_e('Email', 'workreap_core'); ?>">
							</div>
							<div class="form-group wt-eyeicon">
								<input type="password" class="form-control wt-password-field" name="password" placeholder="<?php esc_attr_e('Password', 'workreap_core'); ?>">
								<a href="#" class="wt-hidepassword"><i class="ti-eye"></i></a>
							</div>
							<div class="form-group wt-checkbox-wrap">
								<h4><?php esc_html_e("I want to start as","workreap_core");?>: </h4>
								<span class="wt-radio">
									<input id="wt-freelancer-single" type="radio"  name="user_type" value="freelancer" checked>
									<label for="wt-freelancer-single"><?php esc_html_e('Freelancer', 'workreap_core'); ?></label>
								</span>
								<span class="wt-radio">
									<input id="wt-employer-single" type="radio" name="user_type" value="employer" >
									<label for="wt-employer-single"><?php esc_html_e('Employer ', 'workreap_core'); ?></label>
								</span>
							</div>
							<?php if( isset( $captcha_settings ) && $captcha_settings === 'enable' ) {?>
								<div class="domain-captcha form-group">
									<div id="recaptcha_signup"></div>
								</div>
							<?php }?>
							<div class="form-group wt-btnarea">
								<button class="wt-btn" id="wt-singe-signup"><i class="ti-lock"></i> <?php esc_html_e('Sign up now','workreap_core');?></button>
								<?php 
								if (  ( isset($enable_google_connect) && $enable_google_connect === 'enable' ) 
								   || ( isset($enable_facebook_connect) && $enable_facebook_connect === 'enable' ) 
								   || ( isset($enable_linkedin_connect) && $enable_linkedin_connect === 'enable' ) 
								) {?>
									<span>
										<?php esc_html_e('Start using with','workreap_core');?>
										<?php if (  isset($enable_facebook_connect) && $enable_facebook_connect === 'enable' ) {?><a href="javascript:;" class="sp-fb-connect">“<?php esc_html_e('Facebook','workreap_core');?>”</a><?php } ?>
										<?php if (  isset($enable_google_connect) && $enable_google_connect === 'enable' ) {?>&nbsp;<?php esc_html_e('or','workreap_core');?>&nbsp;<a href="javascript:;"  class="wt-googlebox" id="wt-gconnect-reg">“<?php esc_html_e('Google','workreap_core');?>”</a><?php } ?>
										<?php if (  isset($enable_linkedin_connect) && $enable_linkedin_connect === 'enable' ) {?>&nbsp;<?php esc_html_e('or','workreap_core');?>&nbsp;<a class="sp-linkedin-connect" href="javascript:;">“<?php esc_html_e('LinkedIn', 'workreap_core')?>”</a><?php } ?>
									</span>
								<?php } ?>
							</div>
						</fieldset>
						
					</form>
					<div class="wt-joinnowfooter-wrap">
						<div class="wt-joinnowfooter">
							<?php if( !empty($term_text) || !empty($terms_link)) {?>
								<p>
									<?php echo esc_html( $term_text ); ?>
									<?php if( !empty( $terms_link ) ) { ?>
										<a target="_blank" href="<?php echo esc_url( $terms_link ); ?>"><?php esc_html_e('Terms & Conditions', 'workreap_core'); ?></a>
									<?php } ?>
								</p>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		echo ob_get_clean();
	}
	add_action('workreap_registration_single_step', 'workreap_registration_single_step');
}

/**
 * @Login single
 * @return 
 */
if( !function_exists( 'workreap_login_single_step' ) ){
	function workreap_login_single_step(){
		$image_url	= '';
		$single_step_logo	= '';
		$enable_google_connect 	 = '';
		$enable_facebook_connect = '';
		$enable_linkedin_connect = '';
		$captcha_settings = '';
		$login_register	= array();
		if (function_exists('fw_get_db_settings_option')) { 
			$enable_google_connect 	 = fw_get_db_settings_option('enable_google_connect', $default_value = null);
			$enable_facebook_connect = fw_get_db_settings_option('enable_facebook_connect', $default_value = null);
			$enable_linkedin_connect = fw_get_db_settings_option('enable_linkedin_connect', $default_value = null);
			$login_register 		= fw_get_db_settings_option('enable_login_register');
			$image_url				= !empty($login_register['enable']['single_step_image']['url']) ? $login_register['enable']['single_step_image']['url'] : ''; 
			$single_step_logo		= !empty($login_register['enable']['single_step_logo']['url']) ? $login_register['enable']['single_step_logo']['url'] : ''; 
			$captcha_settings = fw_get_db_settings_option('captcha_settings', $default_value = null);
		}

		if (!empty( $login_register ) && $login_register['enable']['registration']['gadget'] === 'enable') {
			$terms_link 	= !empty( $login_register['enable']['registration']['enable']['terms_link'] ) ? $login_register['enable']['registration']['enable']['terms_link'] : '';
			$terms_link 	= !empty( $terms_link ) ? get_the_permalink($terms_link[0]) : '';
			$term_text 		= !empty( $login_register['enable']['registration']['enable']['term_text'] ) ? $login_register['enable']['registration']['enable']['term_text'] : esc_html__('Agree our terms and conditions', 'workreap_core');
		}
		
		
		ob_start();
		?>
		<div class="row align-items-center">
			<?php if( !empty($image_url) ){?>
				<div class="col-12 col-md-6 col-xl-5">
					<figure class="wt-joinnow-img">
						<img src="<?php echo esc_url($image_url);?>" alt="<?php esc_attr_e('Regidtration','workreap_core');?>">
					</figure>
				</div>
			<?php } ?>
			<div class="col-12 col-md-6 col-xl-7">
				<div class="wt-joinnowpopup-wrap">
					<?php if( !empty($single_step_logo) ){?>
						<strong class="wt-joinnow-logo"><img src="<?php echo esc_url($single_step_logo);?>" alt="<?php esc_attr_e('Regidtration logo','workreap_core');?>"></strong>
					<?php }?>
					
					<form class="wt-formtheme wt-joinnow-form do-login-form" id="wt-single-login-form" method="post">
						<fieldset>
							<div class="wt-popuptitletwo">
								<h4><?php esc_html_e("Sign In Now","workreap_core");?></h4>
								<span><?php esc_html_e("If you don't have an account?","workreap_core");?> <a href="javascript:;" id="wt-single-signup"><?php esc_html_e("Sign up","workreap_core");?></a></span>
							</div>
							<div class="form-group">
								<input type="text" name="username" class="form-control" value=""  placeholder="<?php esc_attr_e('Email', 'workreap_core'); ?>">
							</div>
							<div class="form-group wt-eyeicon">
								<input type="password" class="form-control wt-password-field" name="password" placeholder="<?php esc_attr_e('Password', 'workreap_core'); ?>">
								<a href="#" class="wt-hidepassword"><i class="ti-eye"></i></a>
							</div>
							<?php if( isset( $captcha_settings ) && $captcha_settings === 'enable' ) {?>
								<div class="domain-captcha form-group">
									<div id="recaptcha_signin"></div>
								</div>
							<?php }?>
							<div class="form-group wt-btnarea">
								<span class="wt-checkbox">
									<input id="wt-loginp" type="checkbox" name="rememberme">
									<label for="wt-loginp"><?php esc_html_e('Keep me logged in','workreap_core');?></label>
								</span>
								<button class="wt-btn do-login-button" ><i class="ti-lock"></i> <?php esc_html_e('Login now','workreap_core');?></button>
								<span><a href="#" class="wt-forgot-password-single" ><?php esc_html_e('Reset Password?','workreap_core');?></a></span>
								<?php 
								if (  ( isset($enable_google_connect) && $enable_google_connect === 'enable' ) 
								   || ( isset($enable_facebook_connect) && $enable_facebook_connect === 'enable' ) 
								   || ( isset($enable_linkedin_connect) && $enable_linkedin_connect === 'enable' ) 
								) {?>
									<span>
										<?php esc_html_e('Login with','workreap_core');?>
										<?php if (  isset($enable_facebook_connect) && $enable_facebook_connect === 'enable' ) {?><a href="javascript:;" class="sp-fb-connect"> “<?php esc_html_e('Facebook','workreap_core');?>”</a><?php } ?> 
										<?php if (  isset($enable_google_connect) && $enable_google_connect === 'enable' ) {?>&nbsp;<?php esc_html_e('or','workreap_core');?>&nbsp;<a href="javascript:;"  class="wt-googlebox" id="wt-gconnect">“<?php esc_html_e('Google','workreap_core');?>”</a><?php } ?>
										<?php if (  isset($enable_linkedin_connect) && $enable_linkedin_connect === 'enable' ) {?>&nbsp;<?php esc_html_e('or','workreap_core');?>&nbsp;<a class="sp-linkedin-connect" href="javascript:;">“<?php esc_html_e('LinkedIn', 'workreap_core')?>”</a><?php } ?>
									</span>
								<?php } ?>
							</div>
						</fieldset>
					</form>
					<form class="wt-formtheme wt-joinnow-form do-login-form wt-hide-form do-forgot-password-form">
						<fieldset>
							<div class="wt-popuptitletwo">
								<h4><?php esc_html_e("Forget password","workreap_core");?></h4>
								<span><?php esc_html_e("If you don't have an account?","workreap_core");?> <a href="javascript:;" id="wt-single-signup"><?php esc_html_e("Sign up","workreap_core");?></a></span>
							</div>
							<div class="form-group">
								<input type="email" name="email" class="form-control get_password" placeholder="<?php esc_html_e('Email', 'workreap_core'); ?>">
							</div>
							<?php if( isset( $captcha_settings ) && $captcha_settings === 'enable' ) {?>
								<div class="domain-captcha form-group">
									<div id="recaptcha_forgot"></div>
								</div>
							<?php }?>
							<div class="form-group wt-btnarea">
								<a href="javascript:;" class="wt-btn do-get-password-btn"><?php esc_html_e('Get Password','workreap_core');?></a>
							</div>                                                               
						</fieldset>
						<input type="hidden" name="wt_pwd_nonce" value="<?php echo wp_create_nonce("wt_pwd_nonce"); ?>" />
					</form>
				</div>
			</div>
		</div>
		<?php
		echo ob_get_clean();
	}
	add_action('workreap_login_single_step', 'workreap_login_single_step');
}

/**
 * @Registration Step One
 * @return 
 */
if( !function_exists( 'workreap_registration_step_one' ) ){
	function workreap_registration_step_one($class=''){
		$step_one_title = '';
		$step_one_desc  = '';
		$verify_user  	= 'verified';
		$selected 		= 'selected';
		$json			= array();
		
		if( !empty( $class ) ){
			$post_class		= 'wt-model-reg1';
		} else{
			$post_class		= !empty( $_POST['key'] ) ? 'wt-model-reg1' : 'rg-step-one';
		}
		
		$gender_list	= apply_filters('workreap_gender_types',array());
		
		$enable_login_register	= array();
		
		if (function_exists('fw_get_db_settings_option')) {           
            $step_one_title = fw_get_db_settings_option('step_one_title');
            $step_one_desc = fw_get_db_settings_option('step_one_desc');        
			$enable_google_connect 	 = fw_get_db_settings_option('enable_google_connect', $default_value = null);
			$enable_facebook_connect = fw_get_db_settings_option('enable_facebook_connect', $default_value = null);
			$enable_linkedin_connect = fw_get_db_settings_option('enable_linkedin_connect', $default_value = null);
			$verify_user = fw_get_db_settings_option('verify_user', $default_value = null);
			$enable_login_register = fw_get_db_settings_option('enable_login_register');
			$gender_settings = fw_get_db_settings_option('gender_settings');
        }

        if( empty( $step_one_title ) ){
        	$step_one_title = esc_html__('Join For a Good Start', 'workreap_core');
        }   

		ob_start(); ?>		
		<div class="wt-registerformmain">
			<div class="wt-registerhead">
				<div class="wt-title">
					<h3><?php echo esc_attr( $step_one_title ); ?></h3>
				</div>
				<?php if( !empty( $step_one_desc ) ) { ?>
					<div class="description">
						<?php echo do_shortcode( $step_one_desc ); ?>
					</div>
				<?php } ?>
			</div>
			<div class="wt-joinforms">
				<ul class="wt-joinsteps">
					<li class="wt-active"><a href="javascript:;"><?php esc_html_e('01', 'workreap_core'); ?></a></li>
					<li><a href="javascript:;"><?php esc_html_e('02', 'workreap_core'); ?></a></li>
					<li><a href="javascript:;">	<?php esc_html_e('03', 'workreap_core'); ?></a></li>
					<?php if( isset( $verify_user ) && $verify_user === 'verified' ){?>
						<li><a href="javascript:;"> <?php esc_html_e('04', 'workreap_core'); ?> </a></li>
					<?php }?>
				</ul>
				<form class="wt-formtheme wt-formregister">
					<fieldset class="wt-registerformgroup">
						<div class="form-group wt-form-group-dropdown form-group-half">
							<?php if( !empty( $gender_list ) ){?>
							<span class="wt-select">
								<select name="gender">
									<?php foreach( $gender_list as $key	=> $val ){?>
										<option value="<?php echo esc_attr( $key );?>"><?php echo esc_attr( $val );?></option>
									<?php }?>
								</select>
							</span>
							<?php }?>
							<input type="text" name="first_name" class="form-control" value="" placeholder="<?php esc_html_e('First Name', 'workreap_core'); ?>">
						</div>
						<div class="form-group form-group-half">
							<input type="text" name="last_name" value="" class="form-control" placeholder="<?php esc_html_e('Last Name', 'workreap_core'); ?>">
						</div>
						<div class="form-group form-group-half">
							<input type="text" name="username" class="form-control" value="" placeholder="<?php esc_html_e('username', 'workreap_core'); ?>">
						</div>
						<div class="form-group form-group-half">
							<input type="email" name="email" class="form-control" value="" placeholder="<?php esc_html_e('Email', 'workreap_core'); ?>">
						</div>
						<div class="form-group">
							<a href="javascript:;" class="wt-btn <?php echo esc_attr( $post_class );?>">
								<?php esc_html_e('Start Now', 'workreap_core'); ?>
							</a>
						</div>
					</fieldset>
					<?php 
						wp_nonce_field('workreap_register_step_one_nounce', 'workreap_register_step_one_nounce'); 
					?>
				</form>
				<?php 
					if (  ( isset($enable_google_connect) && $enable_google_connect === 'enable' ) 
					   || ( isset($enable_facebook_connect) && $enable_facebook_connect === 'enable' ) 
					   || ( isset($enable_linkedin_connect) && $enable_linkedin_connect === 'enable' ) 
					) {?>
					<div class="wt-joinnowholder">
						<div class="wt-title">
							<h4><?php esc_html_e('Join Now With', 'workreap_core'); ?></h4>
						</div>
						<div class="wt-description">
							<p><?php esc_html_e('Use a social account for faster login or easy registration to directly get in to your account', 'workreap_core'); ?></p>
						</div>
						<ul class="wt-socialicons wt-iconwithtext">
							<?php if (  isset($enable_google_connect) && $enable_google_connect === 'enable' ) {?>
								<li class="wt-googleplus"><a id="wt-gconnect-reg" class="wt-googlebox" href="javascript:;"><i class="fa fa-google-plus"></i><em><?php esc_html_e('Google', 'workreap_core'); ?></em></a></li>
							<?php }?>
							<?php if (  isset($enable_facebook_connect) && $enable_facebook_connect === 'enable' ) {?>
								<li class="wt-facebook"><a class="sp-fb-connect" href="javascript:;"><i class="fa fa-facebook-f"></i><em><?php esc_html_e('Facebook', 'workreap_core'); ?></em></a></li>
							<?php }?>
							<?php if (  isset($enable_linkedin_connect) && $enable_linkedin_connect === 'enable' ) {do_action('workreap_linkedin_login_button');}?>
						</ul>
					</div>
				<?php }?>
			</div>
		</div>
		<?php
		if( !empty( $post_class ) && $post_class === 'wt-model-reg1' && empty( $class )){
			$json['type'] 		= 'success';
			$json['html']		= ob_get_clean();
			wp_send_json($json);
		} else {
			echo ob_get_clean();
		}
		
	}
	add_action('workreap_registration_step_one', 'workreap_registration_step_one', 10,1);
	
	add_action('wp_ajax_workreap_registration_step_one', 'workreap_registration_step_one');
    add_action('wp_ajax_nopriv_workreap_registration_step_one', 'workreap_registration_step_one');
}

/**
 * @Registration Step Two
 * @return 
 */
if( !function_exists( 'workreap_registration_step_two' ) ){
	function workreap_registration_step_two(){		
		$login_register = '';
		$step_two_title = '';
		$step_two_desc  = '';
		$terms_link 	= '';
		$terms_text 	= '';
		$verify_user  	= 'verified';
		$hide_departments  	= '';
		
		$json			= array();
		$post_class		= !empty( $_POST['key'] ) ? 'wt-model-reg2' : 'wt-step-two';
		$hide_user_type	= '';
		$employer		= '';
		$freelancer		= 'checked';

		$signup_page_slug = workreap_get_signup_page_url('step', '1');	           

		if (function_exists('fw_get_db_settings_option')) {
            $login_register = fw_get_db_settings_option('enable_login_register');
            $step_two_title = fw_get_db_settings_option('step_two_title');
            $step_two_desc = fw_get_db_settings_option('step_two_desc');   
			$verify_user = fw_get_db_settings_option('verify_user', $default_value = null);
			$hide_departments = fw_get_db_settings_option('hide_departments', $default_value = null);
        }

		if( empty( $step_two_title ) ){
        	$step_two_title = esc_html__('Join For a Good Start', 'workreap_core');
        }              

        if (!empty( $login_register ) && $login_register['enable']['registration']['gadget'] === 'enable') {
            $terms_link = !empty( $login_register['enable']['registration']['enable']['terms_link'] ) ? $login_register['enable']['registration']['enable']['terms_link'] : '';
            $terms_link = !empty( $terms_link ) ? get_the_permalink($terms_link[0]) : '';
            $term_text = !empty( $login_register['enable']['registration']['enable']['term_text'] ) ? $login_register['enable']['registration']['enable']['term_text'] : esc_html__('Agree our terms and conditions', 'workreap_core');
        }
		
		if( !empty( $post_class ) && $post_class === 'wt-model-reg2'){
			$signup_page_slug	= 'javascript:;';
		} else{
			$signup_page_slug	= $signup_page_slug;
		}
		
		ob_start(); ?>
		<div class="wt-registerformmain">
			<div class="wt-registerhead">
				<div class="wt-title">
					<h3><?php echo esc_attr( $step_two_title ); ?></h3>
				</div>
				<?php if( !empty( $step_two_desc ) ) { ?>
					<div class="description">
						<?php echo do_shortcode( $step_two_desc ); ?>
					</div>
				<?php } ?>
			</div>
			<div class="wt-joinforms">
				<ul class="wt-joinsteps">
					<li class="wt-done-next"><a href="javascript:;"><i class="fa fa-check"></i></a></li>
					<li class="wt-active"><a href="javascript:;"><?php esc_html_e('02', 'workreap_core'); ?></a></li>
					<li><a href="javascript:;"><?php esc_html_e('03', 'workreap_core'); ?></a></li>
					<?php if( isset( $verify_user ) && $verify_user === 'verified' ){?>
						<li><a href="javascript:;"> <?php esc_html_e('04', 'workreap_core'); ?> </a></li>
					<?php }?>
				</ul>
				<?php wp_get_password_hint(); ?>
				<form class="wt-formtheme wt-formregister wt-formregister-step-two">
					<fieldset class="wt-registerformgroup">
						<!-- <div class="form-group"> -->
							<?php // do_action('worktic_get_locations_list','location',''); ?>	
						<!-- </div> -->
						<div class="form-group form-group-half">
							<input type="password" name="password" class="form-control" placeholder="<?php esc_html_e('Password*', 'workreap_core' ); ?>">
						</div>
						<div class="form-group form-group-half">
							<input type="password" name="verify_password" class="form-control" placeholder="<?php esc_html_e('Retype Password*', 'workreap_core' ); ?>">
						</div>
					</fieldset>
					<fieldset class="wt-formregisterstart" style="<?php echo esc_attr( $hide_user_type );?>">
						<div class="wt-title wt-formtitle"><h4><?php esc_html_e('Start as :', 'workreap_core' ); ?></h4></div>
						<ul class="wt-accordionhold wt-formaccordionhold accordion">
							<li>
								<div class="wt-accordiontitle wt-ragister-option">
									<span class="wt-radio">
										<input id="wt-freelancer" class="register-radio" type="radio" name="user_type" value="freelancer" <?php echo esc_attr( $freelancer );?> >
										<label for="wt-freelancer"><?php esc_html_e('Freelancer', 'workreap_core'); ?><span><?php esc_html_e(' (Signup as freelancer &amp; get hired)', 'workreap_core'); ?></span></label>
									</span>
								</div>
							</li>
							<li>
								<div class="wt-accordiontitle wt-ragister-option">
									<span class="wt-radio">
										<input id="wt-company" class="register-radio" type="radio" name="user_type" value="employer" <?php echo esc_attr( $employer );?> >
										<label for="wt-company"><?php esc_html_e('Employer ', 'workreap_core'); ?><span> <?php esc_html_e('(Signup as company/service seeker &amp; post jobs)', 'workreap_core' ); ?></span></label>
									</span>
								</div>
								<?php if( !empty( $hide_departments ) && $hide_departments === 'no' ){?>
									<div class="wt-accordiondetails wt-emp-register">
										<div class="wt-radioboxholder">
											<div class="wt-title">
												<h4><?php esc_html_e('Your Department?', 'workreap_core'); ?></h4>
											</div>
											<?php do_action('worktic_get_departments_list'); ?>
										</div>	
										<div class="wt-radioboxholder">
											<div class="wt-title">
												<h4><?php esc_html_e('No. of employees you have', 'workreap_core'); ?></h4>
											</div>
											<?php do_action('workreap_print_employees_list'); ?>
										</div>
									</div>
								<?php }?>
							</li>
						</ul>
					</fieldset>
					<fieldset class="wt-termsconditions">
						<div class="wt-checkboxholder">
							<span class="wt-checkbox">
								<input id="termsconditions" type="checkbox" name="termsconditions" value="checked">
								<label for="termsconditions"><?php echo esc_html( $term_text ); ?>
									<?php if( !empty( $terms_link ) ) { ?>
										<a target="_blank" href="<?php echo esc_url( $terms_link ); ?>"><?php esc_html_e('Terms & Conditions', 'workreap_core'); ?></a>
									<?php } ?>
								</label>
							</span>
							<a href="<?php echo esc_attr( $signup_page_slug ); ?>" class="wt-btn wt-back-to-one"><?php esc_html_e('Back', 'workreap_core'); ?></a>
							<a href="javascript:;" class="wt-btn <?php echo esc_attr( $post_class );?>"><?php esc_html_e('Continue', 'workreap_core'); ?></a>
						</div>
					</fieldset>
					<?php 
						wp_nonce_field('workreap_register_step_two_nounce', 'workreap_register_step_two_nounce'); 
					?>
				</form>
			</div>
		</div>
		<?php
		if( !empty( $post_class ) && $post_class === 'wt-model-reg2'){
			$json['type'] 		= 'success';
			$json['html']		= ob_get_clean();
			wp_send_json($json);
		} else {
			echo ob_get_clean();
		}
	}
	add_action('workreap_registration_step_two', 'workreap_registration_step_two', 10);
	
	add_action('wp_ajax_workreap_registration_step_two', 'workreap_registration_step_two');
    add_action('wp_ajax_nopriv_workreap_registration_step_two', 'workreap_registration_step_two');
}

/**
 * @Social Registration Step Two
 * @return 
 */
if( !function_exists( 'workreap_social_registeration' ) ){
	function workreap_social_registeration($request='',$show=''){		
		$login_register = '';
		$step_two_title = '';
		$step_two_desc  = '';
		$terms_link 	= '';
		$terms_text 	= '';
		$display		= '';
		
		if( !empty( $show ) && $show === 'no' ) {
			$display		= 'display:none;';
		}
		
		$submit_btn_class	= !empty( $request ) && $request === 'social_login' ? 'social-step-two-poup' : 'social-step-two';
		if (function_exists('fw_get_db_settings_option')) {
            $login_register = fw_get_db_settings_option('enable_login_register');
            $step_two_title = fw_get_db_settings_option('social_title');
            $step_two_desc = fw_get_db_settings_option('social_desc');  
			$hide_departments = fw_get_db_settings_option('hide_departments', $default_value = null);
        }
       
		if( empty( $step_two_title ) ){
        	$step_two_title = esc_html__('Join For a Good Start', 'workreap_core');
        }              

        if (!empty( $login_register ) && $login_register['enable']['registration']['gadget'] === 'enable') {
            $terms_link = !empty( $login_register['enable']['registration']['enable']['terms_link'] ) ? $login_register['enable']['registration']['enable']['terms_link'] : '';
            $terms_link = !empty( $terms_link ) ? get_the_permalink($terms_link[0]) : '';
            $term_text = !empty( $login_register['enable']['registration']['enable']['term_text'] ) ? $login_register['enable']['registration']['enable']['term_text'] : esc_html__('Agree our terms and conditions', 'workreap_core');
        }
		
		ob_start(); ?>		
		<div class="wt-registerformmain">
			<?php if( !empty( $step_two_title ) || !empty( $step_two_desc ) ) { ?>
				<div class="wt-registerhead">
					<?php if( !empty( $step_two_title ) ){?>
						<div class="wt-title">
							<h3><?php echo esc_attr( $step_two_title ); ?></h3>
						</div>
					<?php }?>
					<?php if( !empty( $step_two_desc ) ) { ?>
						<div class="description">
							<?php echo do_shortcode( $step_two_desc ); ?>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
			<div class="wt-joinforms">
				<ul class="wt-joinsteps">
					<li class="wt-done-next"><a href="javascript:;"><i class="fa fa-check"></i></a></li>
					<li class="wt-active"><a href="javascript:;"><?php esc_html_e('02', 'workreap_core'); ?></a></li>
				</ul>
				<form class="wt-formtheme wt-formregister wt-formregister-step-two">
					<fieldset class="wt-registerformgroup">
						<!-- <div class="form-group"> -->
							<?php // do_action('worktic_get_locations_list','location',''); ?>	
						<!-- </div> -->
						<div class="form-group form-group-half">
							<input type="password" name="password" aautocomplete="off" class="form-control" placeholder="<?php esc_html_e('Password*', 'workreap_core' ); ?>">
						</div>
						<div class="form-group form-group-half">
							<input type="password" name="verify_password" autocomplete="off" class="form-control" placeholder="<?php esc_html_e('Retype Password*', 'workreap_core' ); ?>">
						</div>
					</fieldset>
					<fieldset class="wt-formregisterstart" style="<?php echo esc_attr($display);?>">
						<div class="wt-title wt-formtitle"><h4><?php esc_html_e('Start as :', 'workreap_core' ); ?></h4></div>
						<ul class="wt-accordionhold wt-formaccordionhold accordion">
							<li>
								<div class="wt-accordiontitle wt-ragister-social">
									<span class="wt-radio">
										<input id="wt-freelancer" type="radio" name="user_type" value="freelancer" checked>
										<label for="wt-freelancer"><?php esc_html_e('Freelancer', 'workreap_core'); ?><span><?php esc_html_e(' (Signup as freelancer &amp; get hired)', 'workreap_core'); ?></span></label>
									</span>
								</div>
							</li>
							<li>
								<div class="wt-accordiontitle wt-ragister-social">
									<span class="wt-radio">
										<input id="wt-company" type="radio" name="user_type" value="employer">
										<label for="wt-company"><?php esc_html_e('Employer ', 'workreap_core'); ?><span> <?php esc_html_e('(Signup as company/service seeker &amp; post jobs)', 'workreap_core' ); ?></span></label>
									</span>
								</div>
								<?php if( !empty( $hide_departments ) && $hide_departments === 'no' ){?>
									<div class="wt-accordiondetails wt-emp-register">
										<div class="wt-radioboxholder">
											<div class="wt-title">
												<h4><?php esc_html_e('Your Department?', 'workreap_core'); ?></h4>
											</div>
											<?php do_action('worktic_get_departments_list'); ?>				
										</div>	
										<div class="wt-radioboxholder">
											<div class="wt-title">
												<h4><?php esc_html_e('No. of employees you have', 'workreap_core'); ?></h4>
											</div>
											<?php do_action('workreap_print_employees_list'); ?>
										</div>								
									</div>
								<?php }?>
							</li>
							
						</ul>
					</fieldset>
					<fieldset class="wt-termsconditions">
						<div class="wt-checkboxholder">								
							<span class="wt-checkbox">
								<input id="termsconditions" type="checkbox" name="termsconditions" value="checked">
								<label for="termsconditions"><?php echo esc_attr( $term_text ); ?>
									<?php if( !empty( $terms_link ) ) { ?>
										<a target="_blank" href="<?php echo esc_url( $terms_link ); ?>"><?php esc_html_e('Terms & Conditions', 'workreap_core'); ?></a>
									<?php } ?>
								</label>
							</span>	
							<a href="javascript:;" class="wt-btn <?php echo esc_attr( $submit_btn_class );?>"><?php esc_html_e('Continue', 'workreap_core'); ?></a>								
						</div>
					</fieldset>
					<?php wp_nonce_field('workreap_social_step_two_nounce', 'workreap_social_step_two_nounce');?>						
				</form>
			</div>
		</div>		
		<?php
		if( !empty( $request ) && $request === 'social_login' ) {
			return ob_get_clean();
		} else {
			echo ob_get_clean();	
		}
	}
	add_action('workreap_social_registeration', 'workreap_social_registeration', 10,2);
}


/**
 * @Registration Step Three
 * @return 
 */
if( !function_exists( 'workreap_registration_step_three' ) ){
	function workreap_registration_step_three(){	
		$step_three_title 	= '';
		$step_three_desc  	= '';
		$step_image 		= array();
		$image 				= '';
		$why_need_code_page = array();
		$need_code_url 		= '';
		$verify_user  		= 'verified';
		
		$json			= array();
		$post_class		= !empty( $_POST['key'] ) ? 'wt-model-reg3' : 'wt-step-three';
		
		if (function_exists('fw_get_db_settings_option')) {           
            $step_three_title 	= fw_get_db_settings_option('step_three_title');
            $step_three_desc 	= fw_get_db_settings_option('step_three_desc');   
            $step_image 		= fw_get_db_settings_option('step_image');   
            $why_need_code_page =  fw_get_db_settings_option('why_need_code_page');
			$verify_user 		= fw_get_db_settings_option('verify_user', $default_value = null);
        }        

        if( empty( $step_three_title ) ){
        	$step_three_title = esc_html__('Join For a Good Start', 'workreap_core');
        } 

        $need_code_url = !empty( $why_need_code_page[0] ) ? $why_need_code_page[0] : '';
        $need_code_url = !empty( $need_code_url ) ? get_permalink( $need_code_url ) : '';

        if( !empty( $step_image['url'] ) ){
        	$image = $step_image['url'];
        }
        
		ob_start(); ?>		
		<div class="wt-registerformmain">
			<div class="wt-registerhead">
				<div class="wt-title">
					<h3><?php echo esc_attr( $step_three_title ); ?></h3>
				</div>
				<?php if( !empty( $step_three_desc ) ) { ?>
					<div class="description">
						<?php echo do_shortcode( $step_three_desc ); ?>
					</div>
				<?php } ?>
			</div>
			<div class="wt-joinforms">
				<ul class="wt-joinsteps">
					<li class="wt-done-next"><a href="javascript:;"><i class="fa fa-check"></i></a></li>
					<li class="wt-done-next"><a href="javascript:;"><i class="fa fa-check"></i></a></li>
					<li class="wt-active"><a href="javascript:;"><?php esc_html_e('03', 'workreap_core'); ?></a></li>
					<?php if( isset( $verify_user ) && $verify_user === 'verified' ){?>
						<li><a href="javascript:;"> <?php esc_html_e('04', 'workreap_core'); ?> </a></li>
					<?php }?>
				</ul>
			</div>
			<div class="wt-joinformc">
				<?php if( !empty( $image ) ){?>
					<figure class="wt-joinformsimg">
						<img src="<?php echo esc_url( $image ); ?>" alt="<?php esc_html_e('Step 3', 'workreap_core'); ?>">
					</figure>
				<?php }?>
				<form class="wt-formtheme wt-verifyform ">
					<fieldset>
						<div class="form-group">
							<label><?php esc_html_e('We’ve sent a verification code to your email.', 'workreap_core'); ?>
								<?php if( !empty( $need_code_url ) ) { ?>
									<a href="<?php echo esc_url( $need_code_url ); ?>"> <?php esc_html_e('Why do I need a code?', 'workreap_core' ); ?></a>
								<?php } ?>
							</label>
							<input type="text" name="code" class="form-control" placeholder="<?php esc_html_e('Enter Verification Code', 'workreap_core'); ?>">
						</div>
						<div class="form-group wt-btnarea">
							<label><a href="#" class="wt-resend-code"><?php esc_html_e('Resend Verification Code', 'workreap_core'); ?></a></label>
						</div>
						<div class="form-group wt-btnarea">
							<a href="#" class="wt-btn <?php echo esc_attr( $post_class );?>"><?php esc_html_e('Verify now', 'workreap_core'); ?></a>
						</div>
					</fieldset>
				</form>
			</div>
		</div>			
		<?php
		if( !empty( $post_class ) && $post_class === 'wt-model-reg3'){
			$json['type'] 		= 'success';
			$json['html']		= ob_get_clean();
			wp_send_json($json);
		} else {
			echo ob_get_clean();
		}
	}
	add_action('workreap_registration_step_three', 'workreap_registration_step_three', 10);
	
	add_action('wp_ajax_workreap_registration_step_three', 'workreap_registration_step_three');
    add_action('wp_ajax_nopriv_workreap_registration_step_three', 'workreap_registration_step_three');
}

/**
 * @Registration Step Four
 * @return 
 */
if( !function_exists( 'workreap_registration_step_four' ) ){
	function workreap_registration_step_four(){
		global $current_user;
		$user_role = apply_filters('workreap_get_user_role', $current_user->ID);		
		$step_four_title 	= '';
		$step_four_desc  	= '';		
		$verify_user  		= 'verified';

		if (function_exists('fw_get_db_settings_option')) {                    
            $step_four_title 	= fw_get_db_settings_option('step_four_title');
            $step_four_desc 	= fw_get_db_settings_option('step_four_desc');   
			$verify_user 		= fw_get_db_settings_option('verify_user', $default_value = null);
        }                    

        if( empty( $step_four_title ) ){
        	$step_four_title = esc_html__('Congratulations', 'workreap_core');
        }

       	if( $user_role == 'employers' ){  
			$message_content = esc_html__('Would you like to add your first Job?', 'workreap_core');
		} else {
			$message_content = esc_html__('Complete your profile and get hired.', 'workreap_core');
		}
		
		ob_start(); ?>
		<div class="row justify-content-md-center">
			<div class="col-xs-12 col-sm-12 col-md-10 push-md-1 col-lg-8 push-lg-2">		
				<div class="wt-registerformmain wt-registerformhold wt-registerformmain">
					<div class="wt-registerhead">
						<div class="wt-title">
							<h3><?php echo esc_attr( $step_four_title ); ?></h3>
						</div>
						<?php if( !empty( $step_four_desc ) ) { ?>
							<div class="description">
								<?php echo do_shortcode( $step_four_desc ); ?>
							</div>
						<?php } ?>
					</div>
					<div class="wt-joinforms">
						<ul class="wt-joinsteps">
							<li class="wt-done-next"><a href="javascript:;"><i class="fa fa-check"></i></a></li>
							<li class="wt-done-next"><a href="javascript:;"><i class="fa fa-check"></i></a></li>
							<li class="wt-done-next"><a href="javascript:;"><i class="fa fa-check"></i></a></li>
							<?php if( isset( $verify_user ) && $verify_user === 'verified' ){?>
								<li class="wt-done-next"><a href="javascript:;"><i class="fa fa-check"></i></a></li>
							<?php }?>
						</ul>
					</div>				
					<div class="wt-gotodashboard">
						<span><?php echo esc_attr( $message_content ); ?></span>
						<a class="wt-btn" href="<?php echo workreap_registration_redirect(); ?>"><?php esc_html_e('Go to dashboard','workreap_core');?></a>
					</div>
				</div>	
			</div>	
		</div>			
		<?php
		echo ob_get_clean();
		
	}
	add_action('workreap_registration_step_four', 'workreap_registration_step_four', 10);
}

/**
 * @Registration Step Four
 * @return 
 */
if( !function_exists( 'workreap_registration_step_four_filter' ) ){
	function workreap_registration_step_four_filter($type='return'){
		global $current_user;
		$user_role = apply_filters('workreap_get_user_role', $current_user->ID);		
		$step_four_title 	= '';
		$step_four_desc  	= '';		
		$verify_user  		= 'verified';
		$post_class			= !empty( $_POST['key'] ) ? $_POST['key'] : '';
		
		if (function_exists('fw_get_db_settings_option')) {                    
            $step_four_title 	= fw_get_db_settings_option('step_four_title');
            $step_four_desc 	= fw_get_db_settings_option('step_four_desc');   
			$verify_user 		= fw_get_db_settings_option('verify_user', $default_value = null);
        }                    

        if( empty( $step_four_title ) ){
        	$step_four_title = esc_html__('Congratulations', 'workreap_core');
        }

       	if( $user_role == 'employers' ){  
			$message_content = esc_html__('Would you like to add your first Job?', 'workreap_core');
		} else {
			$message_content = esc_html__('Complete your profile and get hired.', 'workreap_core');
		}
		
		ob_start(); ?>
		<div class="row justify-content-md-center">
			<div class="wt-registerhead">
				<div class="wt-title">
					<h3><?php echo esc_attr( $step_four_title ); ?></h3>
				</div>
				<?php if( !empty( $step_four_desc ) ) { ?>
					<div class="description">
						<?php echo do_shortcode( $step_four_desc ); ?>
					</div>
				<?php } ?>
			</div>
			<div class="wt-joinforms">
				<ul class="wt-joinsteps">
					<li class="wt-done-next"><a href="javascript:;"><i class="fa fa-check"></i></a></li>
					<li class="wt-done-next"><a href="javascript:;"><i class="fa fa-check"></i></a></li>
					<li class="wt-done-next"><a href="javascript:;"><i class="fa fa-check"></i></a></li>
					<?php if( isset( $verify_user ) && $verify_user === 'verified' ){?>
						<li class="wt-done-next"><a href="javascript:;"><i class="fa fa-check"></i></a></li>
					<?php }?>
				</ul>
			</div>				
			<div class="wt-gotodashboard">
				<span><?php echo esc_attr( $message_content ); ?></span>
				<a class="wt-btn" href="<?php echo workreap_registration_redirect(); ?>"><?php esc_html_e('Go to dashboard','workreap_core');?></a>
			</div>	
		</div>			
		<?php
		if( !empty( $post_class ) && $post_class === 'post'){
			$json['type'] 		= 'success';
			$json['html']		= ob_get_clean();
			wp_send_json($json);
		} else {
			return ob_get_clean();
		}
		
	}
	add_filter('workreap_registration_step_four_filter', 'workreap_registration_step_four_filter',10,1);
	add_action('wp_ajax_workreap_registration_step_four_filter', 'workreap_registration_step_four_filter');
    add_action('wp_ajax_nopriv_workreap_registration_step_four_filter', 'workreap_registration_step_four_filter');
}



/**
 * @Registration process Step One
 * @return 
 */
if( !function_exists( 'workreap_process_registration_step_one' ) ){
	function workreap_process_registration_step_one(){
		session_start(array('user_data'));
		
		//Check Security
		$do_check = check_ajax_referer('workreap_register_step_one_nounce', 'workreap_register_step_one_nounce', false);
        if ( $do_check == false ) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('No kiddies please', 'workreap_core');
            echo json_encode($json);
            die;
        }
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		//Validation
		$validations = array(
            'gender' 		=> esc_html__('Gender field is required', 'workreap_core'),
			'username' 		=> esc_html__('Username is required', 'workreap_core'),
            'first_name' 	=> esc_html__('First Name is required', 'workreap_core'),
            'last_name' 	=> esc_html__('Last Name is required.', 'workreap_core'),
            'email'  		=> esc_html__('Email field is required.', 'workreap_core'),            
        );
		
		$gender_list	= apply_filters('workreap_gender_types',array());
		if( empty( $gender_list ) ){
			unset($validations['gender']);
		}
		
        foreach ( $validations as $key => $value ) {
            if ( empty( $_POST[$key] ) ) {
                $json['type'] = 'error';
                $json['message'] = $value;
                echo json_encode($json);
                die;
            }

            //Validate email address
            if ( $key === 'email' ) {
                if ( !is_email( $_POST['email'] ) ) {
                    $json['type'] = 'error';
                    $json['message'] = esc_html__('Please add a valid email address.', 'workreap_core');
                    echo json_encode($json);
                	die;
            	}
       		}	
       	}	
		
		extract($_POST);
		
		$email		=  !empty( $email ) ? $email : '';
		$gender		=  !empty( $gender ) ? $gender : '';
		$first_name	=  !empty( $first_name ) ? $first_name : '';
		$last_name	=  !empty( $last_name ) ? $last_name : '';
		$username	=  !empty( $username ) ? $username : '';
		
		$username_exist 	 = username_exists( $username );
       	$user_exists 		 = email_exists( $email );
		
		if( $username_exist ){
       		$json['type'] = 'error';
            $json['message'] = esc_html__('Username already registered', 'workreap_core');
            echo json_encode($json);
        	die;
       	}
		
		//check exists
       	if( $user_exists ){
       		$json['type'] = 'error';
            $json['message'] = esc_html__('This email already registered', 'workreap_core');
            echo json_encode($json);
        	die;
       	}
		
		$user_data							= array();
       	//Add user data to session
		
		$user_data['register']['gender'] 		= $gender;
		$user_data['register']['first_name'] 	= $first_name;
		$user_data['register']['last_name'] 	= $last_name;
		$user_data['register']['email'] 		= $email;
		$user_data['register']['username'] 		= $username;
		
		$_SESSION['user_data']	= $user_data;

		//Redirect URL		
     	$signup_page_slug = workreap_get_signup_page_url('step', '2');                   
        
		$json['type'] 	 = 'success';
        $json['message'] = esc_html__('A bit more details and its done', 'workreap_core');
        $json['retrun_url'] = htmlspecialchars_decode($signup_page_slug);
        echo json_encode($json);
        die;

	}
	add_action('wp_ajax_workreap_process_registration_step_one', 'workreap_process_registration_step_one');
    add_action('wp_ajax_nopriv_workreap_process_registration_step_one', 'workreap_process_registration_step_one');
}

/**
 * @Registration process Step Two
 * @return 
 */
if( !function_exists( 'workreap_process_registration_step_two' ) ){
	function workreap_process_registration_step_two(){
		session_start(array('user_data'));
		
		//Check Security
		$do_check = check_ajax_referer('workreap_register_step_two_nounce', 'workreap_register_step_two_nounce', false);
        if ( $do_check == false ) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('No kiddies please', 'workreap_core');
            echo json_encode($json);
            die;
        }

		//Validation
		$validations = array(
            // 'location' 			=> esc_html__('Location field is required', 'workreap_core'),
            'password' 			=> esc_html__('Password field is required', 'workreap_core'),
            'verify_password' 	=> esc_html__('Verify Password field is required.', 'workreap_core'),
            'user_type'  		=> esc_html__('User type field is required.', 'workreap_core'),            
            'termsconditions'  	=> esc_html__('You should agree to terms and conditions.', 'workreap_core'),     
        );

        foreach ( $validations as $key => $value ) {
            if ( empty( $_POST[$key] ) ) {
                $json['type'] = 'error';
                $json['message'] = $value;
                echo json_encode($json);
                die;
            }     
			
			if ($key === 'password') {
                if ( strlen( $_POST[$key] ) < 6 ) {
                    $json['type'] 	 = 'error';
                    $json['message'] = esc_html__('Password length should be minimum 6', 'workreap_core');
                    echo json_encode($json);
                    die;
                }
            } 
			
			
            if ($key === 'verify_password') {
                if ( $_POST['password'] != $_POST['verify_password']) {
                    $json['type'] = 'error';
                    $json['message'] = esc_html__('Password does not match.', 'workreap_core');
                    echo json_encode($json);
                    die;
                }
            } 

            if( $key == 'user_type'){
            	if( $_POST['user_type'] == 'company' ){
            		$employees  = !empty( $_POST['employees'] ) ? esc_attr( $_POST['employees'] ) : '';
            		$department = !empty( $_POST['department'] ) ? esc_attr( $_POST['department'] ) : '';
            		if( empty( $employees ) || empty( $department ) ){
            			$json['type'] = 'error';
	                    $json['message'] = esc_html__('Employee and department fields are required.', 'workreap_core');
	                    echo json_encode($json);
	                    die;
            		}
            	}
            }                 
       	}	

       	//Get Data
       	$location   = !empty( $_POST['location'] ) ? esc_attr( $_POST['location'] ) : '';
       	$password  	= !empty( $_POST['password'] ) ? esc_attr( $_POST['password'] ) : '';
       	$user_type 	= !empty( $_POST['user_type'] ) ? esc_attr( $_POST['user_type'] ) : '';
       	$department = !empty( $_POST['department'] ) ? esc_attr( $_POST['department'] ) : '';
       	$employees  = !empty( $_POST['employees'] ) ? esc_attr( $_POST['employees'] ) : '';

       	//Set User Role
       	$db_user_role = 'employers';
       	if( $user_type === 'freelancer' ){
       		$db_user_role = 'freelancers';
       	} else {
       		$db_user_role = 'employers';
       	}
		
		
		$user_data	= isset($_SESSION['user_data']) ? $_SESSION['user_data'] : array();

       	//Get user data from session
       	$username 	= !empty( $user_data['register']['username'] ) ? esc_attr( $user_data['register']['username'] ) : '';
		$first_name = !empty( $user_data['register']['first_name'] ) ? esc_attr( $user_data['register']['first_name'] ) : '';
       	$last_name 	= !empty( $user_data['register']['last_name'] ) ? esc_attr( $user_data['register']['last_name'] ) : '';
       	$gender 	= !empty( $user_data['register']['gender'] ) ? esc_attr( $user_data['register']['gender'] ) : '';
       	$email 		= !empty( $user_data['register']['email'] ) ? esc_attr( $user_data['register']['email'] ) : '';
		
		//Session data validation
		if( empty( $username ) 
		   || empty( $first_name ) 
		   || empty( $last_name ) 
		   || empty( $email ) 
		 ) {

			$signup_page_slug = workreap_get_signup_page_url('step', '1');		                

			$json['type'] 		= 'error';
			$json['message'] 	= esc_html__( 'All the fields are required added in first step', 'workreap_core' );
	        $json['retrun_url'] = htmlspecialchars_decode($signup_page_slug);
			echo json_encode( $json );
			die;
		}		

		//User Registration
		$random_password = $password;
		$full_name 		 = $first_name.' '.$last_name;
		$user_nicename   = sanitize_title( $full_name );
		
		$userdata = array(
			'user_login'  		=>  $username,
			'user_pass'    		=>  $random_password,
			'user_email'   		=>  $email,  
			'user_nicename'   	=>  $user_nicename,  
			'display_name'   	=>  $full_name,  
		);
		
        $user_identity 	 = wp_insert_user( $userdata );
		
        if ( is_wp_error( $user_identity ) ) {
            $json['type'] = "error";
            $json['message'] = esc_html__("User already exists. Please try another one.", 'workreap_core');
            wp_send_json($json);
        } else {
        	global $wpdb;
            wp_update_user( array('ID' => esc_sql( $user_identity ), 'role' => esc_sql( $db_user_role ), 'user_status' => 1 ) );

            $wpdb->update(
                    $wpdb->prefix . 'users', array('user_status' => 1), array('ID' => esc_sql($user_identity))
            );

			unset($_SESSION['user_data']);
            
			update_user_meta( $user_identity, 'first_name', $first_name );
            update_user_meta( $user_identity, 'last_name', $last_name );
			update_user_meta( $user_identity, 'gender', esc_attr( $gender ) );              

			update_user_meta($user_identity, 'show_admin_bar_front', false);
            update_user_meta($user_identity, 'full_name', esc_attr($full_name));

            $key_hash = rand( 1000, 9999 );
            update_user_meta( $user_identity, '_is_verified', 'no' );
			update_user_meta( $user_identity, 'confirmation_key', $key_hash );
			
			//Create Post
			$user_post = array(
                'post_title'    => wp_strip_all_tags( $full_name ),
                'post_status'   => 'publish',
                'post_author'   => $user_identity,
                'post_type'     => $db_user_role,
            );

            $post_id    = wp_insert_post( $user_post );
			
            if( !is_wp_error( $post_id ) ) {

				$shortname_option	= '';
                if( function_exists('fw_get_db_settings_option')  ){
                    $shortname_option	= fw_get_db_settings_option('shortname_option', $default_value = null);
                }
				
				if(!empty($shortname_option) && $shortname_option === 'enable' ){
					$shor_name			= workreap_get_username($user_identity);
					$shor_name_array	= array(
											'ID'        => $post_id,
											'post_name'	=> sanitize_title($shor_name)
										);
					wp_update_post($shor_name_array);
				}

				$fw_options = array();
				
				//Update user linked profile
            	update_user_meta( $user_identity, '_linked_profile', $post_id );
            	wp_set_post_terms( $post_id, $location, 'locations' );
				update_post_meta( $post_id, '_is_verified', 'no' );
				
				
				update_post_meta( $post_id, '_hourly_rate_settings', 'off' );
				
            	if( $db_user_role == 'employers' ){
					
					update_post_meta($post_id, '_user_type', 'employer');
            		update_post_meta($post_id, '_employees', $employees);            		
					update_post_meta($post_id, '_followers', '');
					
					//update department
					if( !empty( $department ) ){
						$department_term = get_term_by( 'term_id', $department, 'department' );
						if( !empty( $department_term ) ){
							wp_set_post_terms( $post_id, $department, 'department' );
						}
					}

					//Fw Options
					$fw_options['department']         = array( $department );
					$fw_options['no_of_employees']    = $employees;

            	} elseif( $db_user_role == 'freelancers' ){
					update_post_meta($post_id, '_user_type', 'freelancer');
            		update_post_meta($post_id, '_perhour_rate', '');
            		update_post_meta($post_id, 'rating_filter', 0);
            		update_post_meta($post_id, '_freelancer_type', 'rising_talent');         		           		
            		update_post_meta($post_id, '_featured_timestamp', 0); 
					update_post_meta($post_id, '_english_level', 'basic');
					//extra data in freelancer
					update_post_meta($post_id, '_gender', $gender);
					$fw_options['_perhour_rate']    = '';
					$fw_options['gender']    		= $gender;
            	}
				
				//Set country for unyson
				$locations = get_term_by( 'slug', $location, 'locations' );
				$location_data = array();
				if( !empty( $locations ) ){
					$location_data[0] = $locations->term_id;
					wp_set_post_terms( $post_id, $locations->term_id, 'locations' );
				}
				
				if ( function_exists('fw_get_db_post_option' )) {
					$dir_latitude 	= fw_get_db_settings_option('dir_latitude');
            		$dir_longitude 	= fw_get_db_settings_option('dir_longitude');
					$verify_user 	= fw_get_db_settings_option('verify_user', $default_value = null);
				} else {
					$dir_latitude	= '';
					$dir_longitude	= '';
					$verify_user  	= 'verified';
				}
				
				//add extra fields as a null
				$tagline	= '';
				update_post_meta($post_id, '_tag_line', $tagline);
				update_post_meta($post_id, '_address', '');
				update_post_meta($post_id, '_latitude', $dir_latitude);
				update_post_meta($post_id, '_longitude', $dir_longitude);
				
				$fw_options['address']    	= '';
				$fw_options['longitude']    = $dir_longitude;
				$fw_options['latitude']    	= $dir_latitude;
				$fw_options['tag_line']     = $tagline;
				//end extra data
				
				//Update User Profile
				$fw_options['country']            = $location_data;
				fw_set_db_post_option($post_id, null, $fw_options);
				
				//update privacy settings
				$settings		 = workreap_get_account_settings($user_type);
				if( !empty( $settings ) ){
					foreach( $settings as $key => $value ){
						$val = $key === '_profile_blocked' || $key === '_hourly_rate_settings'? 'off' : 'on';
						update_post_meta($post_id, $key, $val);
					}
				}

				
				update_post_meta($post_id, '_linked_profile', $user_identity);
				
				$user_promotions	= array();
                if( function_exists('fw_get_db_settings_option')  ){
                    $user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
				}
				$user_promotion 		= !empty($user_promotions) ? $user_promotions['gadget'] : '';
				
				if( !empty($user_promotion) && $user_promotion ==='enable' ){
					do_action('workreap_update_users_marketing',$user_identity);
					do_action('workreap_update_users_marketing_default_attributes',$user_identity);
					do_action('workreap_update_users_marketing_attributes',$user_identity,'is_verified');
					do_action('workreap_update_users_marketing_attributes',$user_identity,'registration_date');
				}
				
            	//Send email to users
            	if (class_exists('Workreap_Email_helper')) {
					$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
					$emailData = array();
					$emailData['name'] 				= $first_name;
					$emailData['password'] 			= $random_password;
					$emailData['email'] 			= $email;
					$emailData['verification_code'] = $key_hash;
					$emailData['site'] = $blogname;
					
					//Welcome Email
					if( $db_user_role === 'employers' ){
						if (class_exists('WorkreapRegisterEmail')) {
							$email_helper = new WorkreapRegisterEmail();
							$email_helper->send_employer_email($emailData);
						}
					} else if( $db_user_role === 'freelancers' ){
						if (class_exists('WorkreapRegisterEmail')) {
							$email_helper = new WorkreapRegisterEmail();
							$email_helper->send_freelacner_email($emailData);
						}
					}
					
					//Send code
					if( isset( $verify_user ) && $verify_user === 'verified' ){
						$json['verify_user'] 			= 'verified';
						if (class_exists('WorkreapRegisterEmail')) {
							$email_helper = new WorkreapRegisterEmail();
							$email_helper->send_verification($emailData);
						}
					} else{
						$json['verify_user'] 			= 'none';
					}
					
					//Send admin email
					if (class_exists('WorkreapRegisterEmail')) {
						$email_helper = new WorkreapRegisterEmail();
						$email_helper->send_admin_email($emailData);
					}
		        }		    
    		
            } else {
            	$json['type'] = 'error';
                $json['message'] = esc_html__('Some error occurs, please try again later', 'workreap_core');                
                wp_send_json($json);
            }			

			//User Login
			$user_array = array();
			$user_array['user_login'] 	 = $email;
        	$user_array['user_password'] = $random_password;
			$status = wp_signon($user_array, false);
			
			if( isset( $verify_user ) && $verify_user === 'none' ){
				$json_message = esc_html__("Your account have been created. Please wait while your account is verified by the admin.", 'workreap_core');
			} else{
				$json_message = esc_html__("Your account have been created. Please verify your account through verification code, an email have been sent your email address.", 'workreap_core');
			}
			
			//Delete session data
	        unset( $_SESSION['user_data'] );

			//Redirect URL		
	     	$signup_page_slug = workreap_get_signup_page_url('step', '3');	      	               
	        
			$json['type'] 			= 'success';
	        $json['message'] 		= $json_message;
	        $json['retrun_url'] 	= htmlspecialchars_decode($signup_page_slug);
	        wp_send_json($json);
        }       

	}
	
	add_action('wp_ajax_workreap_process_registration_step_two', 'workreap_process_registration_step_two');
    add_action('wp_ajax_nopriv_workreap_process_registration_step_two', 'workreap_process_registration_step_two');
}

/**
 * @Migrate portfolios
 * @return 
 */
if( !function_exists( 'workreap_migrate_portfolios' ) ){
	function workreap_migrate_portfolios(){
		$query_args = array(
			'role__in' => array('freelancers'),
		);
		
		$user_query = new WP_User_Query($query_args);
		
		foreach ($user_query->results as $user) {
			$freelance_id	= workreap_get_linked_profile_id( $user->ID,'users' );
			$projects		= array();
			$videos			= array();
			$images			= array();

			if (function_exists('fw_get_db_post_option')) {
				$projects 		= fw_get_db_post_option($freelance_id, 'projects', true);
				$videos 		= fw_get_db_post_option($freelance_id, 'videos', true);
				$images			= fw_get_db_post_option($freelance_id, 'images_gallery',$default_value = null);
			}

			$title	= esc_html__('Portfolio','Docdirect');

			if( empty($projects) ){
				if( !empty($videos) || !empty($images) ){
					$user_post = array(
						'post_title'    => wp_strip_all_tags( $title ),
						'post_status'   => 'publish',
						'post_content'  => '',
						'post_author'   => $user->ID,
						'post_type'     => 'wt_portfolio',
					);

					$post_id    		= wp_insert_post( $user_post );

					//update unyson meta
					$fw_options = array();

					if( !empty($videos) ){
						$fw_options['videos']    		= $videos;
					}

					if( !empty($images) ){
						$fw_options['gallery_imgs']    	= $images;
					}

					fw_set_db_post_option($post_id, null, $fw_options);
				}  
			} else if( !empty($projects) ){
				$count_itme = 0;

				foreach( $projects as $key => $item ){ 
					$count_itme++;
					$title		= !empty($item['title']) ? $item['title'] : esc_html('Portfolio','Docdirect').' '.$count_itme;
					$link		= !empty($item['link']) ? $item['link'] : "";
					$image_url	= !empty($item['image']) ? $item['image']: array();

					$user_post = array(
						'post_title'    => wp_strip_all_tags( $title ),
						'post_status'   => 'publish',
						'post_content'  => '',
						'post_author'   => $user->ID,
						'post_type'     => 'wt_portfolio',
					);

					$post_id    		= wp_insert_post( $user_post );

					//update unyson meta
					$fw_options = array();

					$fw_options['custom_link']  		= $link;

					if( !empty($videos) ){
						$fw_options['videos']    		= $videos;
					}

					if( !empty($images) ){
						if(!empty($image_url)){
							$new_item = array();
							$index	= count($images);
							$new_item[]	= $image_url;
							$images_new	= array_merge($images,$new_item);
						}else{
							$images_new	= $images;
						}
						
						$fw_options['gallery_imgs']    	= $images_new;
					} elseif(!empty($image_url)){
						$new_item = array();
						$new_item[]	= $image_url;
						$fw_options['gallery_imgs']    	= $images_new;
					}

					fw_set_db_post_option($post_id, null, $fw_options);

				}
			}
		}
		
		$json['type'] 			= 'success';
		$json['message'] 		= esc_html__('Portfolios updated', 'workreap_core');
		wp_send_json($json);
	}
	
	add_action('wp_ajax_workreap_migrate_portfolios', 'workreap_migrate_portfolios');
    add_action('wp_ajax_nopriv_workreap_migrate_portfolios', 'workreap_migrate_portfolios');
}

/**
 * @Registration redirect
 * @return 
 */
if( !function_exists( 'workreap_login_redirect' ) ){
	function workreap_login_redirect($userID = ''){
		global $current_user;

		$user_id = !empty($userID) ? $userID : $current_user->ID;
		$json	= array();
		
		if (function_exists('fw_get_db_settings_option')) {
			$redirect_registration = fw_get_db_settings_option('redirect_login');
		}
		
		$redirect_registration	= !empty($redirect_registration) ?  $redirect_registration : 'settings';
		
		if($redirect_registration === 'settings'){
			return Workreap_Profile_Menu::workreap_profile_menu_link('profile', $user_id,true,'settings');
		} else if($redirect_registration === 'package'){
			if(apply_filters('workreap_is_listing_free',false,$user_id) === false ){
				return Workreap_Profile_Menu::workreap_profile_menu_link('package', $user_id,true);
			} else{
				return Workreap_Profile_Menu::workreap_profile_menu_link('profile', $user_id,true,'settings');
			}
			
		} else if($redirect_registration === 'insights'){
			return Workreap_Profile_Menu::workreap_profile_menu_link('insights', $user_id,true);
		} else if($redirect_registration === 'home'){
			return esc_url(home_url('/'));
		} else{
			return Workreap_Profile_Menu::workreap_profile_menu_link('insights', $user_id,true);
		}
		
	}
}
/**
 * @Registration redirect
 * @return 
 */
if( !function_exists( 'workreap_registration_redirect' ) ){
	function workreap_registration_redirect($userID = ''){
		global $current_user;

		$user_id = !empty($userID) ? $userID : $current_user->ID;
		$json	= array();
		
		if (function_exists('fw_get_db_settings_option')) {
			$redirect_registration = fw_get_db_settings_option('redirect_registration');
		}
		
		$redirect_registration	= !empty($redirect_registration) ?  $redirect_registration : 'settings';
		
		if($redirect_registration === 'settings'){
			return Workreap_Profile_Menu::workreap_profile_menu_link('profile', $user_id,true,'settings');
		} else if($redirect_registration === 'package'){
			if(apply_filters('workreap_is_listing_free',false,$user_id) === false ){
				return Workreap_Profile_Menu::workreap_profile_menu_link('package', $user_id,true);
			} else{
				return Workreap_Profile_Menu::workreap_profile_menu_link('profile', $user_id,true,'settings');
			}
			
		} else if($redirect_registration === 'insights'){
			return Workreap_Profile_Menu::workreap_profile_menu_link('insights', $user_id,true);
		} else if($redirect_registration === 'home'){
			return esc_url(home_url('/'));
		} else{
			return Workreap_Profile_Menu::workreap_profile_menu_link('insights', $user_id,true);
		}
		
	}
}

/**
 * @Registration process Step Two
 * @return 
 */
if( !function_exists( 'workreap_process_social_registration_step_two' ) ){
	function workreap_process_social_registration_step_two(){
		global $current_user;
		$json	= array();
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$switch_account	= !empty( $_POST['switch_account'] ) ? $_POST['switch_account'] : 'no';
		
		//Validation
		$validations = array(
            // 'location' 			=> esc_html__('Location field is required', 'workreap_core'),
            'password' 			=> esc_html__('Password field is required', 'workreap_core'),
            'verify_password' 	=> esc_html__('Verify Password field is required.', 'workreap_core'),
            'user_type'  		=> esc_html__('User type field is required.', 'workreap_core'),            
            'termsconditions'  	=> esc_html__('You should agree to terms and conditions.', 'workreap_core'),     
        );

        foreach ( $validations as $key => $value ) {
            if ( empty( $_POST[$key] ) ) {
                $json['type'] = 'error';
                $json['message'] = $value;
                echo json_encode($json);
                die;
            }     
			
			if ($key === 'password') {
                if ( strlen( $_POST[$key] ) < 6 ) {
                    $json['type'] 	 = 'error';
                    $json['message'] = esc_html__('Password length should be minimum 6', 'workreap_core');
                    echo json_encode($json);
                    die;
                }
            } 
			
			
            if ($key === 'verify_password') {
                if ( $_POST['password'] != $_POST['verify_password']) {
                    $json['type'] = 'error';
                    $json['message'] = esc_html__('Password does not match.', 'workreap_core');
                    echo json_encode($json);
                    die;
                }
            } 

            if( $key == 'user_type'){
            	if( $_POST['user_type'] == 'company' ){
            		$employees  = !empty( $_POST['employees'] ) ? esc_attr( $_POST['employees'] ) : '';
            		$department = !empty( $_POST['department'] ) ? esc_attr( $_POST['department'] ) : '';
            		if( empty( $employees ) || empty( $department ) ){
            			$json['type'] = 'error';
	                    $json['message'] = esc_html__('Employee and department fields are required.', 'workreap_core');
	                    echo json_encode($json);
	                    die;
            		}
            	}
            }                 
       	}	

       	//Get Data
       	$location   = !empty( $_POST['location'] ) ? esc_attr( $_POST['location'] ) : '';
       	$password  	= !empty( $_POST['password'] ) ? esc_attr( $_POST['password'] ) : '';
       	$user_type 	= !empty( $_POST['user_type'] ) ? esc_attr( $_POST['user_type'] ) : '';
       	$department = !empty( $_POST['department'] ) ? esc_attr( $_POST['department'] ) : '';
       	$employees  = !empty( $_POST['employees'] ) ? esc_attr( $_POST['employees'] ) : '';

		$user_identity 	 = $current_user->ID;
		$user_email		= $current_user->user_email;
		
		//If not switch account
		if( !empty( $switch_account ) && $switch_account === 'no' ){
			//Set User Role
			$db_user_role = 'employers';
			if( $user_type === 'freelancer' ){
				$db_user_role = 'freelancers';
			} else {
				$db_user_role = 'employers';
			}
			
			//Update user password
			wp_set_password($password, $current_user->ID);
			
			$user_array	= array();
			$user_array['user_login'] = $user_email;
        	$user_array['user_password'] = $password;
			$user = wp_signon($user_array, false);
			//Get user data from session
			$username 	= $current_user->first_name;
			$first_name = $current_user->first_name;
			$last_name 	= '';
			$gender 	= '';
			$email 		= $current_user->user_email;	

			//User Registration
			$random_password = $password;
			$full_name 		 = $first_name;
			$user_nicename   = sanitize_title( $full_name );

			$userdata = array(
				'user_login'  		=>  $username,
				'user_pass'    		=>  $random_password,
				'user_email'   		=>  $email,  
				'user_nicename'   	=>  $user_nicename,  
			);

			$key_hash = rand( 1000, 9999 );
            update_user_meta( $user_identity, '_is_verified', 'no' );
			update_user_meta( $user_identity, 'confirmation_key', $key_hash );
			
			
			$user_promotions	= array();
			if( function_exists('fw_get_db_settings_option')  ){
				$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
			}
			
			$user_promotion 		= !empty($user_promotions['gadget']) ? $user_promotions['gadget'] : '';
			if( !empty($user_promotion) && $user_promotion ==='enable' ){
				do_action('workreap_update_users_marketing_attributes',$user_identity,'is_verified');
			}
			
			//Create Post
			$user_post = array(
				'post_title'    => wp_strip_all_tags( $full_name ),
				'post_status'   => 'publish',
				'post_author'   => $user_identity,
				'post_type'     => $db_user_role,
			);

			$post_id    = wp_insert_post( $user_post );
			
			//update shortner names
			$shortname_option	= '';
			if( function_exists('fw_get_db_settings_option')  ){
				$shortname_option	= fw_get_db_settings_option('shortname_option', $default_value = null);
			}
			
			if(!empty($shortname_option) && $shortname_option === 'enable' ){
				$shor_name			= workreap_get_username($user_identity);
				$shor_name_array	= array(
										'ID'        => $post_id,
										'post_name'	=> sanitize_title($shor_name)
									);
				wp_update_post($shor_name_array);
			}

			update_post_meta($post_id, '_is_verified', 'no');
			update_post_meta($post_id, '_linked_profile', $user_identity);

			//Send email to users
			if (class_exists('Workreap_Email_helper')) {
				if ( function_exists('fw_get_db_post_option' )) {
					$verify_user 	= fw_get_db_settings_option('verify_user', $default_value = null);
				} else {
					$verify_user  	= 'verified';
				}
				
				$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
				$emailData = array();
				$emailData['name'] 				= $first_name;
				$emailData['password'] 			= $random_password;
				$emailData['email'] 			= $email;
				$emailData['verification_code'] = $key_hash;
				$emailData['site'] = $blogname;

				//Welcome Email
				if( $db_user_role === 'employers' ){

					if (class_exists('WorkreapRegisterEmail')) {
						$email_helper = new WorkreapRegisterEmail();
						$email_helper->send_employer_email($emailData);
					}
				} else if( $db_user_role === 'freelancers' ){
					if (class_exists('WorkreapRegisterEmail')) {
						$email_helper = new WorkreapRegisterEmail();
						$email_helper->send_freelacner_email($emailData);
					}
				}

				//Send code
				if( isset( $verify_user ) && $verify_user === 'verified' ){
					$json['verify_user'] 			= 'verified';
					if (class_exists('WorkreapRegisterEmail')) {
						$email_helper = new WorkreapRegisterEmail();
						$email_helper->send_verification($emailData);
					}
				}

				//Send admin email
				if (class_exists('WorkreapRegisterEmail')) {
					$email_helper = new WorkreapRegisterEmail();
					$email_helper->send_admin_email($emailData);
				}
			}
			
		} elseif( !empty( $switch_account ) && $switch_account === 'yes' ){
			$u = new WP_User( $user_identity );

			if( $user_type === 'employer' ){
				$db_current_role = 'freelancers';
				$db_new_role 	 = 'employers';
			} else {
				$db_current_role = 'employers';
				$db_new_role 	 = 'freelancers';
			}
			
			$u->remove_role( $db_current_role );

			// Add role
			$u->add_role( $db_new_role );
			
			$db_user_role = $db_new_role;
			
			$post_id   	= workreap_get_linked_profile_id($current_user->ID);

			if ( set_post_type( $post_id, $db_new_role  ) ) {
				$post_id    = $post_id;
			}
			
			update_post_meta($post_id, '_linked_profile', $user_identity);
			update_user_meta( $current_user->ID, '_is_verified', 'yes' );
			
			//update post for users verification
			$linked_profile   	= workreap_get_linked_profile_id($current_user->ID);
			update_post_meta($linked_profile, '_is_verified', 'yes');
		}

		if( !is_wp_error( $post_id ) ) {
			$fw_options = array();
			
			//update social profile
			$social_avatar	= !empty( $current_user->social_avatar ) ? $current_user->social_avatar :'';
			if (!empty($social_avatar)) {
				delete_post_thumbnail($post_id);
				set_post_thumbnail($post_id, $social_avatar);
			} 
			
			//Update user linked profile
			update_user_meta( $user_identity, '_linked_profile', $post_id );
			wp_set_post_terms( $post_id, $location, 'locations' );
			
			global $wpdb;
			wp_update_user(array('ID' => esc_sql($user_identity), 'role' => esc_sql($db_user_role), 'user_status' => 1));

			$wpdb->update(
					$wpdb->prefix . 'users', array('user_status' => 1), array('ID' => esc_sql($user_identity))
			);
			
			if( $db_user_role == 'employers' ){

				update_post_meta($post_id, '_user_type', 'employer');
				update_post_meta($post_id, '_employees', $employees);            		
				update_post_meta($post_id, '_followers', '');

				//update department
				if( !empty( $department ) ){
					$department_term = get_term_by( 'term_id', $department, 'department' );
					if( !empty( $department_term ) ){
						wp_set_post_terms( $post_id, $department, 'department' );
					}
				}

				//Fw Options
				$fw_options['department']         = array( $department );
				$fw_options['no_of_employees']    = $employees;

			} elseif( $db_user_role == 'freelancers' ){
				update_post_meta($post_id, '_user_type', 'freelancer');
				update_post_meta($post_id, '_perhour_rate', '');
				update_post_meta($post_id, 'rating_filter', 0);
				update_post_meta($post_id, '_freelancer_type', 'rising_talent');         		           		
				update_post_meta($post_id, '_featured_timestamp', 0);
				update_post_meta($post_id, '_invitation_count', 0); 
				update_post_meta($post_id, '_english_level', 'basic');
				
				//extra data in freelancer
				update_post_meta($post_id, '_gender', '');
				$fw_options['_perhour_rate']    = '';
				$fw_options['gender']    		= '';
			}

			//Set country for unyson
			$locations = get_term_by( 'slug', $location, 'locations' );
			$location_data = array();
			if( !empty( $locations ) ){
				$location_data[0] = $locations->term_id;
				wp_set_post_terms( $post_id, $locations->term_id, 'locations' );
			}

			if ( function_exists('fw_get_db_post_option' )) {
				$dir_latitude 	= fw_get_db_settings_option('dir_latitude');
				$dir_longitude 	= fw_get_db_settings_option('dir_longitude');
			} else {
				$dir_latitude	= '';
				$dir_longitude	= '';
			}

			//add extra fields as a null
			$tagline	= '';
			update_post_meta($post_id, '_tag_line', $tagline);
			update_post_meta($post_id, '_address', '');
			update_post_meta($post_id, '_latitude', $dir_latitude);
			update_post_meta($post_id, '_longitude', $dir_longitude);

			$fw_options['address']    	= '';
			$fw_options['longitude']    = $dir_longitude;
			$fw_options['latitude']    	= $dir_latitude;
			$fw_options['tag_line']     = $tagline;
			//end extra data

			//Update User Profile
			$fw_options['country']            = $location_data;
			fw_set_db_post_option($post_id, null, $fw_options);

			//update privacy settings
			$settings		 = workreap_get_account_settings($user_type);
			if( !empty( $settings ) ){
				foreach( $settings as $key => $value ){
					$val = $key === '_profile_blocked' ? 'off' : 'on';
					update_post_meta($post_id, $key, $val);
				}
			}

			$user_promotions	= array();
			if( function_exists('fw_get_db_settings_option')  ){
				$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
			}
			
			$user_promotion 		= !empty($user_promotions['gadget']) ? $user_promotions['gadget'] : '';
			if( !empty($user_promotion) && $user_promotion ==='enable' ){
				do_action('workreap_update_users_marketing',$current_user->ID);
				do_action('workreap_update_users_marketing_default_attributes',$current_user->ID);
				do_action('workreap_update_users_marketing_attributes',$current_user->ID,'is_verified');
				do_action('workreap_update_users_marketing_attributes',$current_user->ID,'registration_date');
			}
			
			//If not switch account
			if( !empty( $switch_account ) && $switch_account === 'no' ){
				$user_type						= workreap_get_user_type( $current_user->ID );
				$freelancer_package_id			= workreap_get_package_type( 'package_type','trail_freelancer');
				$employer_package_id			= workreap_get_package_type( 'package_type','trail_employer');

				if( $user_type === 'employer' && !empty($employer_package_id) ) {
					workreap_update_pakage_data( $employer_package_id ,$current_user->ID,'' );
				} else if( $user_type === 'freelancer' && !empty($freelancer_package_id) ) {
					workreap_update_pakage_data( $freelancer_package_id ,$current_user->ID,'' );
				}
			}

			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('Your profile has been updated!', 'workreap_core');
			$json['retrun_url'] = workreap_registration_redirect();
			
	        wp_send_json($json);
			
		} else {
			$json['type'] = 'error';
			$json['message'] = esc_html__('Some error occurs, please try again later', 'workreap_core');                
			wp_send_json($json);
		}			

	}
	add_action('wp_ajax_workreap_process_social_registration_step_two', 'workreap_process_social_registration_step_two');
    add_action('wp_ajax_nopriv_workreap_process_social_registration_step_two', 'workreap_process_social_registration_step_two');
}

/**
 * @Registration process Step Three
 * @return 
 */
if( !function_exists( 'workreap_process_registration_step_three' ) ){
	function workreap_process_registration_step_three(){
		global $current_user;
        $confirmation_key = get_user_meta($current_user->ID, 'confirmation_key', true);
        $confirmation_key = !empty( $confirmation_key ) ? $confirmation_key : '';
        $code = !empty( $_POST['code'] ) ? esc_attr( $_POST['code'] ) : '';

        if( $code === $confirmation_key ){
        	update_user_meta( $current_user->ID, '_is_verified', 'yes' );
			
			if (function_exists('fw_get_db_settings_option')) {
				$enable_login_register = fw_get_db_settings_option('enable_login_register');
            }
			
			//update post for users verification
			$linked_profile   	= workreap_get_linked_profile_id($current_user->ID);
			update_post_meta($linked_profile, '_is_verified', 'yes');
			$user_promotions	= array();
			if( function_exists('fw_get_db_settings_option')  ){
				$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
			}		
			
			$user_promotion 		= !empty($user_promotions['gadget']) ? $user_promotions['gadget'] : '';
			if( !empty($user_promotion) && $user_promotion ==='enable' ){
				do_action('workreap_update_users_marketing_attributes',$current_user->ID,'is_verified');
			}
        	//Redirect URL		
	     	$signup_page_slug 				= workreap_get_signup_page_url('step', '4');	  
			$user_type						= workreap_get_user_type( $current_user->ID );
			$freelancer_package_id			= workreap_get_package_type( 'package_type','trail_freelancer');
			$employer_package_id			= workreap_get_package_type( 'package_type','trail_employer');
			
			if( $user_type === 'employer' && !empty($employer_package_id) ) {
				workreap_update_pakage_data( $employer_package_id ,$current_user->ID,'' );
			} else if( $user_type === 'freelancer' && !empty($freelancer_package_id) ) {
				workreap_update_pakage_data( $freelancer_package_id ,$current_user->ID,'' );
			}
			
			if( !empty( $enable_login_register['enable']['login_signup_type'] ) && $enable_login_register['enable']['login_signup_type'] === 'popup' ){
				$json['signup']		= 'yes';

				$json['html']		= apply_filters('workreap_registration_step_four_filter','return');
			} else{
				$json['signup']		= 'no';
			}

			$json['message'] 	= esc_html__('Your account has been verified successfully!', 'workreap_core');
			$json['type']		= 'success';

			$user_promotions	= array();
			if( function_exists('fw_get_db_settings_option')  ){
				$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
			}
			$user_promotion 		= !empty($user_promotions['gadget']) ? $user_promotions['gadget'] : '';
			if( !empty($user_promotion) && $user_promotion ==='enable' ){
				do_action('workreap_update_users_marketing_attributes',$current_user->ID,'is_verified');
			}

	        $json['retrun_url'] = htmlspecialchars_decode($signup_page_slug);
	        wp_send_json($json);
        } else {
        	$json['type'] 		= 'error';
	        $json['message'] 	= esc_html__('No kiddies please', 'workreap_core');	        
	        wp_send_json($json);
        }      
	}

	add_action('wp_ajax_workreap_process_registration_step_three', 'workreap_process_registration_step_three');
    add_action('wp_ajax_nopriv_workreap_process_registration_step_three', 'workreap_process_registration_step_three');
}

/**
 * @Approve Profile 
 * @return 
 */
if( !function_exists( 'workreap_approve_profile' ) ){
	add_action('wp_ajax_workreap_approve_profile', 'workreap_approve_profile');
    add_action('wp_ajax_nopriv_workreap_approve_profile', 'workreap_approve_profile');
	function workreap_approve_profile(){
		$user_profile_id 	= !empty( $_POST['id'] ) ? $_POST['id'] : '';
		$type 		= !empty( $_POST['type'] ) ? $_POST['type'] : '';
		
		if( empty( $user_profile_id ) ){
			
			$json['type'] = 'success';
            $json['message'] = esc_html__('No Kiddies Please', 'workreap_core');
            wp_send_json($json);
		}
		
		$is_verified 			= get_post_meta($user_profile_id, '_is_verified',true);
		if (isset($is_verified) && $is_verified === 'yes') {	
			$message_param = 'unapproved';
			
		} else {
			$message_param  = 'approved';
		}

		if(apply_filters('workreap_get_user_type', $user_profile_id ) === 'freelancer'){
			//Prepare Params
			$params_array	= array();
			$params_array['user_profile_identity'] 	= (int) $user_profile_id;
			$params_array['profile_status'] = $message_param;
			$params_array['user_role'] 		= apply_filters('workreap_get_user_type', $user_profile_id );
			$params_array['type'] 			= 'profile_approved';
			
			//child theme : update extra settings
			do_action('wt_process_profile_verified', $params_array);
		}

		$user_promotions	= array();
		if( function_exists('fw_get_db_settings_option')  ){
			$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
		}
		$user_promotion 		= !empty($user_promotions['gadget']) ? $user_promotions['gadget'] : '';
		if( !empty($user_promotion) && $user_promotion ==='enable' ){
			do_action('workreap_update_users_marketing_attributes',$user_profile_id,'is_verified');
		}

		if( isset( $type ) && $type === 'reject' ){
			update_post_meta($user_profile_id,'_is_verified', 'no');
			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('Account has been disabled', 'workreap_core');
			
            wp_send_json($json);
		} else{
			$user_id   	= workreap_get_linked_profile_id($user_profile_id,'post');
			$user_meta	= get_userdata($user_id);
			
			if( empty( $user_meta ) ){
				$json['type'] = 'error';
				$json['message'] = esc_html__('No user exists', 'workreap_core');
				wp_send_json($json);
			}

			//Send verification code
			if (class_exists('Workreap_Published')) {
				if (class_exists('Workreap_Published')) {
					$email_helper = new Workreap_Published();
					
					update_post_meta($user_profile_id,'_is_verified', 'yes');
					
					$emailData 						= array();
					$name  							= workreap_get_username( '' ,$user_profile_id );
					$emailData['name'] 				= $name;
					$emailData['email_to']			= $user_meta->user_email;
					$emailData['site_url'] 			= esc_url(home_url('/'));
					$email_helper->publish_approve_user_acount($emailData);
				}
			} 
			
			$json = array();
			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('Account has been approved and email has been sent to user.', 'workreap_core');        
			wp_send_json($json);
		}
	}
}

/**
 * @Approve project and services 
 * @return 
 */
if( !function_exists( 'workreap_approve_post' ) ){
	add_action('wp_ajax_workreap_approve_post', 'workreap_approve_post');
    add_action('wp_ajax_nopriv_workreap_approve_post', 'workreap_approve_post');
	function workreap_approve_post(){
		$user_id 	= !empty( $_POST['id'] ) ? $_POST['id'] : '';
		$post_id 	= !empty( $_POST['post_id'] ) ? $_POST['post_id'] : '';
		$type 		= !empty( $_POST['type'] ) ? $_POST['type'] : '';
		
		if( empty( $user_id ) || empty( $post_id ) || empty( $type ) ){
			$json['type'] = 'success';
            $json['message'] = esc_html__('No Kiddies Please', 'workreap_core');
            wp_send_json($json);
		}
		
		$user_meta	= get_userdata($user_id);
			
		if( empty( $user_meta ) ){
			$json['type'] = 'error';
			$json['message'] = esc_html__('No user exists', 'workreap_core');
			wp_send_json($json);
		}
		
		$emailData = array();
		$name  		= workreap_get_username( $user_id );
		$emailData['name'] 				= $name;
		$emailData['email_to']			= $user_meta->user_email;
		
		if( isset( $type ) && $type === 'project' ){
			$arg = array(
				'ID' 		=> $post_id,
				'ID' 		=> $post_id,
				'post_status' 	=> 'publish'
			);

			wp_update_post( $arg );
			
			if (class_exists('Workreap_Published')) {
				if (class_exists('Workreap_Published')) {
					$email_helper = new Workreap_Published();
					$emailData['project_name'] 		= get_the_title($post_id);
					$emailData['link'] 				= get_the_permalink($post_id);
					$email_helper->publish_approve_project($emailData);
				}
			} 
			
			$json = array();
			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('Project has been published and email has been sent to user', 'workreap_core');        
			wp_send_json($json);
			
		} elseif( isset( $type ) && $type === 'service' ){
			$arg = array(
				'ID' => $post_id,
				'post_status' => 'publish'
			);

			wp_update_post( $arg );
			
			if (class_exists('Workreap_Published')) {
				if (class_exists('Workreap_Published')) {
					$email_helper = new Workreap_Published();
					$emailData['service_name'] 	= get_the_title($post_id);
					$emailData['link'] 				= get_the_permalink($post_id);
					$email_helper->publish_approve_service($emailData);
				}
			} 
			
			$json = array();
			$json['type'] 		= 'success';
			$json['message'] 	= esc_html__('Service has been published and email has been sent to user', 'workreap_core');        
			wp_send_json($json);
		}
	}
}

/**
 * @Registration process Registration
 * @return 
 */
if( !function_exists( 'workreap_process_registration_complete' ) ) {
	function workreap_process_registration_complete(){
		global $current_user;
		$user_id = !empty( $_POST['id'] ) ? $_POST['id'] : '';

		if( empty( $user_id ) || $current_user->ID != $user_id ){
			$json['type'] = 'error';
            $json['message'] = esc_html__('No Kiddies Please', 'workreap_core');
            echo json_encode($json);
            die;
		}
		
	 	//All looks fine now
		update_user_meta( $current_user->ID, '_registerd', 'registered' );
		$profile_page	= '';
		if( function_exists('workreap_get_search_page_uri') ){
			$profile_page  = workreap_get_search_page_uri('dashboard');
		}
		
		$json['type'] = 'success';
        $json['message'] = esc_html__('Thank You', 'workreap_core');
        $json['retrun_url'] = htmlspecialchars_decode($profile_url);
        echo json_encode($json);
        die;
       
	}
	add_action('wp_ajax_workreap_process_registration_complete', 'workreap_process_registration_complete');
    add_action('wp_ajax_nopriv_workreap_process_registration_complete', 'workreap_process_registration_complete');
}


/**
 * @Send verification
 * @return 
 */
if( !function_exists( 'workreap_resend_verification_code') ){
	function workreap_resend_verification_code(){
		global $current_user; 
		$key_hash = rand( 1000, 9999 );
		update_user_meta( $current_user->ID, 'confirmation_key', $key_hash );
		$code 		= $key_hash;			
		$email 		= $current_user->data->user_email;		
		$name  		= workreap_get_username( $current_user->ID );
		$blogname   = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		
		
		//Send verification code
    	if (class_exists('Workreap_Email_helper')) {
			if (class_exists('WorkreapRegisterEmail')) {
				$email_helper = new WorkreapRegisterEmail();
				$emailData = array();
				$emailData['name'] 				= $name;
				$emailData['email']				= $email;
				$emailData['verification_code'] = $code;
				$emailData['site'] = $blogname;
				$email_helper->send_verification($emailData);
			}
        } 

        $json = array();
        $json['type'] = 'success';
        $json['message'] = esc_html__('Verification code has sent on your email', 'workreap_core');        
        wp_send_json($json);
	}
	add_action('wp_ajax_workreap_resend_verification_code', 'workreap_resend_verification_code');
    add_action('wp_ajax_nopriv_workreap_resend_verification_code', 'workreap_resend_verification_code');
}


/**
 * @Login/Form
 * @return 
 */
if( !function_exists( 'workreap_print_login_form' ) ){
	add_action('workreap_print_login_form', 'workreap_print_login_form', 10);
	function workreap_print_login_form(){
		if (function_exists('fw_get_db_settings_option')) {
			$login_register = fw_get_db_settings_option('enable_login_register'); 
			$enable_google_connect 	 = fw_get_db_settings_option('enable_google_connect', $default_value = null);
			$enable_facebook_connect = fw_get_db_settings_option('enable_facebook_connect', $default_value = null);
			$enable_linkedin_connect = fw_get_db_settings_option('enable_linkedin_connect', $default_value = null);
			$header_type 			 = fw_get_db_settings_option('header_type');
			$enable_login_register   = fw_get_db_settings_option('enable_login_register');
			$captcha_settings = fw_get_db_settings_option('captcha_settings', $default_value = null);
			$captcha_settings = fw_get_db_settings_option('captcha_settings', $default_value = null);
		} 

		$is_auth			= !empty($login_register['gadget']) ? $login_register['gadget'] : ''; 
		$is_register		= !empty($login_register['enable']['registration']['gadget']) ? $login_register['enable']['registration']['gadget'] : ''; 
		$redirect           = !empty( $_GET['redirect'] ) ? esc_url( $_GET['redirect'] ) : '';
		$signup_page_slug   = workreap_get_signup_page_url('step', '1');
		
		ob_start(); 
		
		if ( apply_filters('workreap_get_domain',false) === true ) {
			$post_name = workreap_get_post_name();
			if( $post_name === "home-page-three" ){
				$header_type['gadget'] = 'header_v3';
			}
		}
		
		if ( is_user_logged_in() ) {
			Workreap_Profile_Menu::workreap_profile_menu_top();
			Workreap_Profile_Menu::workreap_profile_menu_notification();
		} else{
			
		if( $is_auth === 'enable'){?>
			<div class="wt-loginarea">
				<?php if( !empty( $header_type['gadget'] ) && $header_type['gadget'] === 'header_v1' ){?>
					<figure class="wt-userimg">
						<img src="<?php echo esc_url(get_template_directory_uri());?>/images/user.png" alt="<?php esc_html_e('user', 'workreap_core'); ?>">
					</figure>
				<?php }?>
				
				<div class="wt-loginoption">
					<?php if( !empty( $header_type['gadget'] ) && ( $header_type['gadget'] === 'header_v2' ||  $header_type['gadget'] == 'header_v3' || $header_type['gadget'] == 'header_v5' || $header_type['gadget'] == 'header_v6' ) ){?>
						<div class="wt-loginoption wt-loginoptionvtwo">
							<?php if( !empty( $enable_login_register['enable']['login_signup_type'] ) && $enable_login_register['enable']['login_signup_type'] === 'popup' ){?>
								<a href="javascript:;"  data-toggle="modal" data-target="#loginpopup" class="wt-btn"><?php esc_html_e('Sign In','workreap_core');?></a>
							<?php } else if( !empty( $enable_login_register['enable']['login_signup_type'] ) && $enable_login_register['enable']['login_signup_type'] === 'single_step' ){?>
								<a href="javascript:;"  data-toggle="modal" data-target="#loginpopup" class="wt-btn"><?php esc_html_e('Sign In','workreap_core');?></a>
							<?php } else {?>
								<a href="javascript:;" id="wt-loginbtn" class="wt-btn"><?php esc_html_e('Sign In','workreap_core');?></a>
							<?php }?>
						</div>
					<?php }else {?>
						<?php if( !empty( $enable_login_register['enable']['login_signup_type'] ) && $enable_login_register['enable']['login_signup_type'] === 'popup' ){?>
							<a href="javascript:;"  data-toggle="modal" data-target="#loginpopup" class="wt-loginbtn"><?php esc_html_e('Sign In','workreap_core');?></a>
						<?php } else if( !empty( $enable_login_register['enable']['login_signup_type'] ) && $enable_login_register['enable']['login_signup_type'] === 'single_step' ){?>
							<a href="javascript:;"  data-toggle="modal" data-target="#loginpopup" class="wt-loginbtn"><?php esc_html_e('Sign In','workreap_core');?></a>
						<?php } else {?>
							<a href="javascript:;" id="wt-loginbtn" class="wt-loginbtn"><?php esc_html_e('Sign In','workreap_core');?></a>
						<?php }?>
					<?php }?>
					<?php if( !empty( $enable_login_register['enable']['login_signup_type'] ) && $enable_login_register['enable']['login_signup_type'] === 'pages' ){?>
					<div class="wt-loginformhold">
						<div class="wt-loginheader">
							<span><?php esc_html_e('Login','workreap_core');?></span>
							<a href="javascript:;"><i class="fa fa-times"></i></a>
						</div>
						<form class="wt-formtheme wt-loginform do-login-form">
							<fieldset>
								<div class="form-group">
									<input type="text" name="username" class="form-control" placeholder="<?php esc_html_e('Username or email', 'workreap_core'); ?>">
								</div>
								<div class="form-group">
									<input type="password" name="password" class="form-control" placeholder="<?php esc_html_e('Password', 'workreap_core'); ?>">
								</div>
								<?php if( !empty( $captcha_settings ) && $captcha_settings === 'enable' ) {?>
									<?php wp_enqueue_script('recaptcha');?>
									<div class="domain-captcha form-group">
										<div id="recaptcha_signin"></div>
									</div>
								<?php }?>
								<div class="wt-logininfo">
									<input type="submit" class="wt-btn do-login-button" value="<?php esc_attr_e('Login','workreap_core');?>">
									<span class="wt-checkbox">
										<input id="wt-login" type="checkbox" name="rememberme">
										<label for="wt-login"><?php esc_html_e('Keep me logged in','workreap_core');?></label>
									</span>
								</div>
								<?php wp_nonce_field('login_request', 'login_request'); ?>
								<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect );?>">
							</fieldset>
							<?php 
								if (  ( isset($enable_google_connect) && $enable_google_connect === 'enable' ) 
								   || ( isset($enable_facebook_connect) && $enable_facebook_connect === 'enable' ) 
								   || ( isset($enable_linkedin_connect) && $enable_linkedin_connect === 'enable' ) 
								) {?>
								<div class="wt-joinnowholder">
									<ul class="wt-socialicons wt-iconwithtext">
										<?php if (  isset($enable_facebook_connect) && $enable_facebook_connect === 'enable' ) {?>
											<li class="wt-facebook"><a class="sp-fb-connect" href="javascript:;"><i class="fa fa-facebook-f"></i><em><?php esc_html_e('Facebook', 'workreap_core'); ?></em></a></li>
										<?php }?>
										<?php if (  isset($enable_google_connect) && $enable_google_connect === 'enable' ) {?>
											<li class="wt-googleplus"><a class="wt-googlebox" id="wt-gconnect" href="javascript:;"><i class="fa fa-google-plus"></i><em><?php esc_html_e('Google', 'workreap_core'); ?></em></a></li>
										<?php }?>
										<?php if (  isset($enable_linkedin_connect) && $enable_linkedin_connect === 'enable' ) {do_action('workreap_linkedin_login_button');}?>
									</ul>
								</div>
							<?php }?>
							<div class="wt-loginfooterinfo">
								<a href="javascript:;" class="wt-forgot-password"><?php esc_html_e('Forgot password?','workreap_core');?></a>
								<?php if ( !empty($is_register) && $is_register === 'enable' ) {?>
									<a href="<?php echo esc_url(  $signup_page_slug ); ?>"><?php esc_html_e('Create account','workreap_core');?></a>
								<?php }?>
							</div>
						</form>
						
						<form class="wt-formtheme wt-loginform do-forgot-password-form wt-hide-form">
							<fieldset>
								<div class="form-group">
									<input type="email" name="email" class="form-control get_password" placeholder="<?php esc_html_e('Email', 'workreap_core'); ?>">
								</div>
								<?php if( isset( $captcha_settings ) && $captcha_settings === 'enable' ) {?>
									<div class="domain-captcha form-group">
										<div id="recaptcha_forgot"></div>
									</div>
								<?php }?>
								<div class="wt-logininfo">
									<a href="javascript:;" class="wt-btn do-get-password-btn"><?php esc_html_e('Get Password','workreap_core');?></a>
								</div>                                                               
							</fieldset>
							<input type="hidden" name="wt_pwd_nonce" value="<?php echo wp_create_nonce("wt_pwd_nonce"); ?>" />
							<div class="wt-loginfooterinfo">
								<a href="javascript:;" class="wt-show-login"><?php esc_html_e('Login Now','workreap_core');?></a>
								<?php if ( !empty($is_register) && $is_register === 'enable' ) {?>
									<a href="<?php echo esc_url(  $signup_page_slug ); ?>"><?php esc_html_e('Create account','workreap_core');?></a>
								<?php }?>
							</div>
						</form>
					</div>
					<?php }?>
				</div>
				<?php if ( !empty($is_register) && $is_register === 'enable' ) {?>
					<?php if( !empty( $enable_login_register['enable']['login_signup_type'] ) && $enable_login_register['enable']['login_signup_type'] === 'popup' ){?>
						<a href="javascript:;" data-toggle="modal" data-target="#joinpopup" class="wt-btn wt-joinnowbtn"><?php esc_html_e('Join Now','workreap_core');?></a>
					<?php } else if( !empty( $enable_login_register['enable']['login_signup_type'] ) && $enable_login_register['enable']['login_signup_type'] === 'single_step' ){?>
						<a href="javascript:;" data-toggle="modal" data-target="#joinpopup" class="wt-btn wt-joinnowbtn"><?php esc_html_e('Join Now','workreap_core');?></a>
					<?php } else {?>
						<a href="<?php echo esc_url(  $signup_page_slug ); ?>"  class="wt-btn"><?php esc_html_e('Join Now','workreap_core');?></a>
					<?php }?>
				<?php }?> 
			</div>
			<?php }
		}
		
		echo ob_get_clean();
	}
}

/**
 * @save project post meta data
 * @type delete
 */
if (!function_exists('workreap_save_project_meta_data')) {
	add_action('save_post', 'workreap_save_project_meta_data');
    function workreap_save_project_meta_data($post_id) {
		if (!is_admin()) {
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		//save projects
		if (get_post_type() == 'projects') {
			if (!function_exists('fw_get_db_post_option')) {
				return;
			}
			
			if (!empty($_POST['fw_options'])) {
				$project_level 	= !empty( $_POST['fw_options']['project_level'] ) ? $_POST['fw_options']['project_level'] : '' ;
				$project_type 	= !empty( $_POST['fw_options']['project_type']['gadget'] ) ? $_POST['fw_options']['project_type']['gadget'] : '' ;
				
				if( $project_type == 'hourly'){
					$project_price 		= !empty( $_POST['fw_options']['project_type']['hourly']['hourly_rate'] ) ? $_POST['fw_options']['project_type']['hourly']['hourly_rate'] : '' ;
					$max_price 			= !empty( $_POST['fw_options']['project_type']['hourly']['max_price'] ) ? $_POST['fw_options']['project_type']['hourly']['max_price'] : '' ;
					$estimated_hours 	= !empty( $_POST['fw_options']['project_type']['hourly']['estimated_hours'] ) ? $_POST['fw_options']['project_type']['hourly']['estimated_hours'] : '' ;
				} else {
					$project_price 		= '';
					$max_price 			= '';
					$estimated_hours	= '';
				}
				
				if (function_exists('fw_get_db_settings_option')) {
					$job_price_option           = fw_get_db_settings_option('job_price_option', $default_value = null);
					$job_option           		= fw_get_db_settings_option('job_option', $default_value = null);
					$milestone         			= fw_get_db_settings_option('job_milestone_option', $default_value = null);
				}
				
				$job_price_option 			= !empty($job_price_option) ? $job_price_option : '';
				$job_option 				= !empty($job_option) ? $job_option : '';
				$milestone					= !empty($milestone['gadget']) ? $milestone['gadget'] : '';

				if( !empty($job_option) && ( $job_option === 'enable' ) && !empty( $_POST['fw_options']['job_option'] ) ){
					$job_option_val 	= !empty( $_POST['fw_options']['job_option'] ) ? $_POST['fw_options']['job_option'] : '' ;
					update_post_meta($post_id, '_job_option', $job_option_val );
				}
				
				if( !empty($milestone) && ( $milestone === 'enable' ) && !empty( $_POST['fw_options']['project_type']['fixed']['milestone'] ) ){
					$milestone_val 	= !empty( $_POST['fw_options']['project_type']['fixed']['milestone'] ) ? $_POST['fw_options']['project_type']['fixed']['milestone'] : 'off' ;
					update_post_meta($post_id, '_milestone', $milestone_val );
				}
				
				if (isset($_POST['fw_options']['address'])) {
					update_post_meta($post_id, '_address',esc_attr( $_POST['fw_options']['address']));
				}

				if (isset($_POST['fw_options']['longitude'])) {
					update_post_meta($post_id, '_longitude',esc_attr( $_POST['fw_options']['longitude']));
				}

				if (isset($_POST['fw_options']['latitude'])) {
					update_post_meta($post_id, '_latitude',esc_attr( $_POST['fw_options']['latitude']));
				}

				//location 
				if (isset($_POST['fw_options']['country'])) {
					$locations = get_term_by( 'id', $_POST['fw_options']['country'], 'locations' );
					$location = array();
					if( !empty( $locations ) ){
						wp_set_post_terms( $post_id, $locations->term_id, 'locations' );
						update_post_meta($post_id, '_country',esc_attr( $locations->slug ));
					}
				}
				
				$project_duration 	= !empty( $_POST['fw_options']['project_duration'] ) ? $_POST['fw_options']['project_duration'] : '';
				$english_level 		= !empty( $_POST['fw_options']['english_level'] ) ? $_POST['fw_options']['english_level'] : '';
				$freelancer_level 	= !empty( $_POST['fw_options']['freelancer_level'] ) ? $_POST['fw_options']['freelancer_level'] : '';
				
				$freelancertype	= '';
				if( function_exists('fw_get_db_settings_option')  ){
					$freelancertype	= fw_get_db_settings_option('multiselect_freelancertype', $default_value = null);
				}

				if(!empty($freelancertype) && $freelancertype === 'enable'){
					$freelance_types	= explode("/*/",$freelancer_level);
					$freelancer_level	= $freelance_types;
				}

				//Add searchable data
				// update_post_meta($post_id, '_project_level', $project_level); 
				// update_post_meta($post_id, '_project_type', $project_type); 
				// update_post_meta($post_id, '_project_cost', $project_price);
				// update_post_meta($post_id, '_max_price', $max_price);
				// update_post_meta($post_id, '_estimated_hours', $estimated_hours);
				// update_post_meta($post_id, '_project_duration', $project_duration);
				// update_post_meta($post_id, '_english_level', $english_level);
				// update_post_meta($post_id, '_freelancer_level', $freelancer_level);	
				
				//Featured Expiry
				if (!empty($_POST['fw_options']['featured_post'])) {
					update_post_meta($post_id, '_featured_job_string',1);
					// update_post_meta($post_id, '_expiry_string', strtotime( $_POST['fw_options']['featured_expiry'] ));
				} else {
					$featured_str = get_post_meta($post_id, '_featured_job_string', true);
					$featured_str	= !empty($featured_str) ? $featured_str : 0;
					update_post_meta($post_id, '_featured_job_string',$featured_str);
				}
				
				if (!empty($_POST['fw_options']['highlighted_post'])) {
					update_post_meta($post_id, '_highlighted_job_string', 1);
				} else {
					$highlighted_str = get_post_meta($post_id, '_highlighted_job_string', true);
					$highlighted_str = !empty($highlighted_str) ? $highlighted_str : 0;
					update_post_meta($post_id, '_highlighted_job_string', $highlighted_str);
				}
			}
		}
		
		//save freelancer
		if ( get_post_type() === 'freelancers' ) {
			if (isset($_POST['fw_options']['address'])) {
				update_post_meta($post_id, '_address',esc_attr( $_POST['fw_options']['address']));
			}
			
			if (isset($_POST['fw_options']['longitude'])) {
				update_post_meta($post_id, '_longitude',esc_attr( $_POST['fw_options']['longitude']));
			}
			
			if (isset($_POST['fw_options']['latitude'])) {
				update_post_meta($post_id, '_latitude',esc_attr( $_POST['fw_options']['latitude']));
			}

			if (function_exists('fw_get_db_settings_option')) {
				$freelancer_price_option = fw_get_db_settings_option('freelancer_price_option', $default_value = null);
			}
			
			$freelancer_price_option 	= !empty($freelancer_price_option) ? $freelancer_price_option['gadget'] : '';
	
			if(!empty($freelancer_price_option) && $freelancer_price_option === 'enable' ){
				$min_price   = !empty($_POST['fw_options']['min_price'] ) ? sanitize_text_field( $_POST['fw_options']['min_price'] ) : '';
				$max_price   = !empty($_POST['fw_options']['max_price'] ) ? sanitize_text_field( $_POST['fw_options']['max_price'] ) : '';
				update_post_meta($post_id, '_min_price', $min_price);
				update_post_meta($post_id, '_max_price', $max_price);
			}
			
			//location 
			if (isset($_POST['fw_options']['country'])) {
				$locations = get_term_by( 'id', $_POST['fw_options']['country'], 'locations' );
				$location = array();
				if( !empty( $locations ) ){
					wp_set_post_terms( $post_id, $locations->term_id, 'locations' );
					update_post_meta($post_id, '_country',esc_attr( $locations->slug ));
				}
			}
			
			//Skills
			$skills = !empty( $_POST['fw_options']['skills'] ) ? $_POST['fw_options']['skills'] : array();
			$skills_term 	= array();

			$counter = 0;
			if( !empty( $skills ) ){
				foreach ($skills as $key => $value) {
					$skill_data		= json_decode (stripslashes($value));
					if( !empty($skill_data->skill[0]) ){
						$skills_term[]  = intval( $skill_data->skill[0] );
						$counter++;
					}
				} 
			}

			wp_set_post_terms( $post_id, $skills_term, 'skills' );
			
			$freelancer_specialization	= array();
			if( function_exists('fw_get_db_settings_option')  ){
				$freelancer_specialization	= fw_get_db_settings_option('freelancer_specialization', $default_value = null);
			}
			
			$specialization 			= !empty($freelancer_specialization) ? $freelancer_specialization['gadget'] : '';
			$experience	= array();
			if( function_exists('fw_get_db_settings_option')  ){
				$experience	= fw_get_db_settings_option('freelancer_industrial_experience', $default_value = null);
			}
			$experience 	= !empty($experience['gadget'] ) ? $experience['gadget'] : '';

			if(!empty($experience) && $experience === 'enable' ){
				$industrial_experiences = !empty( $_POST['fw_options']['industrial_experiences'] ) ? $_POST['fw_options']['industrial_experiences'] : array();
				$industrial_experiences_term 	= array();

				$counter = 0;
				if( !empty( $industrial_experiences ) ){
					foreach ($industrial_experiences as $key => $value) {
						$experiences_data		= json_decode (stripslashes($value));
						if( !empty($experiences_data->exp[0]) ){
							$industrial_experiences_term[]  = intval( $experiences_data->exp[0] );
							$counter++;
						}
					} 
				}

				wp_set_post_terms( $post_id, $industrial_experiences_term, 'wt-industrial-experience' );
			}
			if(!empty($experience) && $experience === 'enable' ){
				$specialization = !empty( $_POST['fw_options']['specialization'] ) ? $_POST['fw_options']['specialization'] : array();
				$specialization_term 	= array();

				$counter = 0;
				if( !empty( $specialization ) ){
					foreach ($specialization as $key => $value) {
						$specialization_data		= json_decode (stripslashes($value));
						if( !empty($specialization_data->spec[0]) ){
							$specialization_term[]  = intval( $specialization_data->spec[0] );
							$counter++;
						}
					} 
				}
				
				wp_set_post_terms( $post_id, $specialization_term, 'wt-specialization' );
			}

			//tagline
			if (isset($_POST['fw_options']['tag_line'])) {
				update_post_meta($post_id, '_tag_line',esc_attr( $_POST['fw_options']['tag_line']));
			}
			
			//perhour
			if (isset($_POST['fw_options']['_perhour_rate'])) {
				update_post_meta($post_id, '_perhour_rate',intval( $_POST['fw_options']['_perhour_rate'])); 
			}
			
			//gender
			if (isset($_POST['fw_options']['gender'])) {
				update_post_meta($post_id, '_gender',$_POST['fw_options']['gender']); 
			}

			//freelancer type
			if (isset($_POST['fw_options']['freelancer_type'])) {
				if (function_exists('fw_get_db_settings_option')) {
					$freelancerselecttype	= fw_get_db_settings_option('freelancertype_multiselect', $default_value = null);
				}
				
				$freelancer_type 	= !empty( $_POST['fw_options']['freelancer_type'] ) ? $_POST['fw_options']['freelancer_type'] : '';
				
				if(!empty($freelancerselecttype) && $freelancerselecttype === 'enable'){
					$freelancer_type	= explode("/*/",$freelancer_type);
					$freelancer_type	= $freelancer_type;
				}

				update_post_meta($post_id, '_freelancer_type', $freelancer_type);
				$freelancer_type_array	= !empty($freelancer_type) && is_array($freelancer_type) ? $freelancer_type : array($freelancer_type);	
			}
			
			//freelancer type
			if (isset($_POST['fw_options']['english_level'])) {
				update_post_meta($post_id, '_english_level',$_POST['fw_options']['english_level']); 
			}
			
			if( !empty( $_POST['payout_settings'] ) ){
				$linked_profile   	= workreap_get_linked_profile_id($post_id,'post');
				update_user_meta($linked_profile,'payrols',$_POST['payout_settings']);
			}

			//Featured Expiry
			if (!empty($_POST['fw_options']['featured_post']) && !empty( $_POST['fw_options']['featured_expiry'] )) {
				update_post_meta($post_id, '_featured_timestamp',1);
				update_post_meta($post_id, '_expiry_string',strtotime( $_POST['fw_options']['featured_expiry'] ));
			}

			$user_promotions	= array();
			if( function_exists('fw_get_db_settings_option')  ){
				$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
			}
			$user_promotion 		= !empty($user_promotions['gadget']) ? $user_promotions['gadget'] : '';
			if( !empty($user_promotion) && $user_promotion ==='enable' ){
				$user_id	= workreap_get_linked_profile_id($post_id,'post');
				do_action('workreap_update_users_marketing_attributes',$user_id,'is_verified');
			}
			
		}
		
		//save proposals
		if ( get_post_type() === 'proposals' ) {
			
			if (isset($_POST['fw_options']['proposal_duration'])) {
				update_post_meta( $post_id, '_proposed_duration', $_POST['fw_options']['proposal_duration'] );
			}
			
			if (isset($_POST['fw_options']['estimeted_time'])) {
				update_post_meta( $post_id, '_estimeted_time', $_POST['fw_options']['estimeted_time'] );
			}
			
			if (isset($_POST['fw_options']['proposed_amount'])) {
				update_post_meta( $post_id, '_amount', $_POST['fw_options']['proposed_amount'] );
			}

			if (isset($_POST['fw_options']['per_hour_amount'])) {
				update_post_meta( $post_id, '_per_hour_amount', $_POST['fw_options']['per_hour_amount'] );
			}
			
			if (isset($_POST['fw_options']['proposal_docs'])) {
				 update_post_meta( $post_id, '_proposal_docs', $_POST['fw_options']['proposal_docs']);
			}
			if (isset($_POST['fw_options']['project'])) {
				 update_post_meta( $post_id, '_project_id', $_POST['fw_options']['project'] );
			}
			
		}

		//save employer
		if ( get_post_type() === 'employers' ) {

			if (isset($_POST['fw_options']['address'])) {
				update_post_meta($post_id, '_address',esc_attr( $_POST['fw_options']['address']));
			}
			
			if (isset($_POST['fw_options']['longitude'])) {
				update_post_meta($post_id, '_longitude',esc_attr( $_POST['fw_options']['longitude']));
			}
			
			if (isset($_POST['fw_options']['latitude'])) {
				update_post_meta($post_id, '_latitude',esc_attr( $_POST['fw_options']['latitude']));
			}
			
			//location 
			if (isset($_POST['fw_options']['country'])) {
				$locations = get_term_by( 'id', $_POST['fw_options']['country'], 'locations' );
				$location = array();
				if( !empty( $locations ) ){
					wp_set_post_terms( $post_id, $locations->term_id, 'locations' );
					update_post_meta($post_id, '_country',esc_attr( $locations->slug ));
				}
			}
			
			if (isset($_POST['fw_options']['tag_line'])) {
				update_post_meta($post_id, '_tag_line',esc_attr( $_POST['fw_options']['tag_line']));
			}

			if (isset($_POST['fw_options']['no_of_employees'])) {
				update_post_meta($post_id, '_employees',esc_attr( $_POST['fw_options']['no_of_employees']));
			}
			
			//department 
			if (isset($_POST['fw_options']['department'])) {
				$departments = get_term_by( 'id', $_POST['fw_options']['department'], 'department' );
				if( !empty( $departments ) ){
					wp_set_post_terms( $post_id, $departments->term_id, 'department' );
					update_post_meta($post_id, '_department',esc_attr( $departments->slug ));
				}
			}

			//Payout settings update meta
			if( !empty( $_POST['payout_settings'] ) ){
				$linked_profile   	= workreap_get_linked_profile_id($post_id,'post');
				update_user_meta($linked_profile,'payrols',$_POST['payout_settings']);
			}

			$user_promotions	= array();
			if( function_exists('fw_get_db_settings_option')  ){
				$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
			}
			$user_promotion 		= !empty($user_promotions['gadget']) ? $user_promotions['gadget'] : '';
			if( !empty($user_promotion) && $user_promotion ==='enable' ){
				$user_id	= workreap_get_linked_profile_id($post_id,'post');
				do_action('workreap_update_users_marketing_attributes',$user_id,'is_verified');
			}
		}
		
		//save portfolio
		if ( get_post_type() === 'wt_portfolio' ) {
			if (!empty($_POST['fw_options']['gallery_imgs'])) {
				if (function_exists('fw_get_db_post_option')) {
					$db_gallery_imgs   	= fw_get_db_post_option($post_id,'gallery_imgs');
					if( !empty($db_gallery_imgs[0]['attachment_id']) ){
						set_post_thumbnail( $post_id, intval( $db_gallery_imgs[0]['attachment_id'] ) );
					}
				}
			}
		}
		
		//save services
		if ( get_post_type() === 'micro-services' ) {
			
			if (!empty($_POST['fw_options']['docs'])) {
				if (function_exists('fw_get_db_post_option')) {
					$db_docs   	= fw_get_db_post_option($post_id,'docs');
					if( !empty($db_docs[0]['attachment_id']) ){
						set_post_thumbnail( $post_id, intval( $db_docs[0]['attachment_id'] ) );
					}
				}
			}
			
			if (isset($_POST['fw_options']['price'])) {
				update_post_meta( $post_id, '_price',esc_attr( $_POST['fw_options']['price']) );
			}
			
			if (isset($_POST['fw_options']['downloadable'])) {
				update_post_meta( $post_id, '_downloadable',esc_attr( $_POST['fw_options']['downloadable']) );
			}
			
			if (isset($_POST['fw_options']['english_level'])) {
				update_post_meta( $post_id, '_english_level',esc_attr( $_POST['fw_options']['english_level']) );
			}
			
			//Featured Expiry
			if (!empty($_POST['fw_options']['featured_post']) && !empty( $_POST['fw_options']['featured_expiry'] )) {
				update_post_meta($post_id, '_featured_service_string',1);
				update_post_meta($post_id, '_expiry_string',strtotime( $_POST['fw_options']['featured_expiry'] ));
			} else {
				update_post_meta($post_id, '_featured_service_string',0);	
			}

			//location 
			if ( isset($_POST['fw_options']['country']) ) {
				$locations = get_term_by( 'id', $_POST['fw_options']['country'], 'locations' );
				$location = array();
				if( !empty( $locations ) ){
					wp_set_post_terms( $post_id, $locations->term_id, 'locations' );
					update_post_meta( $post_id, '_country',esc_attr( $locations->slug ));
				}
			}
			
			$addons	        = !empty( $_POST['service']['addons'] ) ? $_POST['service']['addons'] : array();
			if (!empty( $addons ) ) {
				update_post_meta( $post_id, '_addons', $addons );
			}
						
		}
		
		if ( get_post_type() === 'addons-services' ) {
			if (isset($_POST['fw_options']['price'])) {
				update_post_meta( $post_id, '_price',esc_attr( $_POST['fw_options']['price']) );
			}
		}
		
		//save services
		if ( get_post_type() === 'services-orders' ) {			
			if( function_exists( 'workreap_save_service_rating') && isset( $_POST['fw_options'] ) ) {
				workreap_save_service_rating( $post_id, $_POST['fw_options']);
			}
		}
	}
}

/**
 * @save project post meta data
 * @type delete
 */
if (!function_exists('workreap_delete_wp_user')) {
	add_action( 'delete_user', 'workreap_delete_wp_user' );
    function workreap_delete_wp_user($user_id) {
		$linked_profile   	= workreap_get_linked_profile_id($user_id);
		if( !empty( $linked_profile ) ){
		 	wp_delete_post( $linked_profile, true);
		}
	}
}

/**
 * @Create profile from admin create user
 * @type delete
 */
if (!function_exists('workreap_create_wp_user')) {
	add_action( 'user_register', 'workreap_create_wp_user' );
    function workreap_create_wp_user($user_id) {
		if( !empty( $user_id )  ) {
			$user_meta	= get_userdata($user_id);
			$title		= $user_meta->first_name.' '.$user_meta->last_name;
			$post_type	= !empty($user_meta->roles[0]) ? esc_attr($user_meta->roles[0]) : '';

			if( !empty($post_type) && ( $post_type === 'freelancers' || $post_type	=== 'employers' ) ){
				$post_data	= array(
								'post_title'	=> wp_strip_all_tags($title),
								'post_author'	=> $user_id,
								'post_status'   => 'publish',
								'post_type'		=> $post_type,
							);

				$post_id	= wp_insert_post( $post_data );

				if( !empty( $post_id ) ) {
					update_post_meta($post_id, '_linked_profile',intval($user_id));
					add_user_meta( $user_id, '_linked_profile', $post_id);
					
					$fw_options = array();
	
					//Update user linked profile
					update_user_meta( $user_id, '_linked_profile', $post_id );
					update_post_meta( $post_id, '_is_verified', 'yes' );

				$user_promotions	= array();
			if( function_exists('fw_get_db_settings_option')  ){
				$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
			}
					$user_promotion 		= !empty($user_promotions['gadget']) ? $user_promotions['gadget'] : '';
					if( !empty($user_promotion) && $user_promotion ==='enable' ){
						do_action('workreap_update_users_marketing_attributes',$user_id,'is_verified');
					}

					if( $post_type == 'employers' ){
						$user_type	= 'employer';
						update_post_meta($post_id, '_user_type', 'employer');
						update_post_meta($post_id, '_employees', '');            		
						update_post_meta($post_id, '_followers', '');

						//Fw Options
						$fw_options['department']         = array();
						$fw_options['no_of_employees']    = '';

					} elseif( $post_type == 'freelancers' ){
						$user_type	= 'freelancer';
						update_post_meta($post_id, '_user_type', 'freelancer');
						update_post_meta($post_id, '_perhour_rate', '');
						update_post_meta($post_id, 'rating_filter', 0);
						update_post_meta($post_id, '_freelancer_type', 'rising_talent');         		           		
						update_post_meta($post_id, '_featured_timestamp', 0); 
						update_post_meta($post_id, '_english_level', 'basic');
						//extra data in freelancer
						update_post_meta($post_id, '_gender', '');
						$fw_options['_perhour_rate']    = '';
						$fw_options['gender']    		= '';
					}

					//add extra fields as a null
					$tagline	= '';
					update_post_meta($post_id, '_tag_line', $tagline);
					update_post_meta($post_id, '_address', '');
					update_post_meta($post_id, '_latitude', '');
					update_post_meta($post_id, '_longitude', '');

					$fw_options['address']    	= '';
					$fw_options['longitude']    = '';
					$fw_options['latitude']    	= '';
					$fw_options['tag_line']     = $tagline;
					//end extra data

					//Update User Profile
					fw_set_db_post_option($post_id, null, $fw_options);

					//update privacy settings
					$settings		 = workreap_get_account_settings($user_type);
					if( !empty( $settings ) ){
						foreach( $settings as $key => $value ){
							$val = $key === '_profile_blocked' ? 'off' : 'on';
							update_post_meta($post_id, $key, $val);
						}
					}

					//update post for users verification
					$linked_profile   	= workreap_get_linked_profile_id($user_id);
					update_post_meta($linked_profile, '_is_verified', 'yes');		

				$user_promotions	= array();
			if( function_exists('fw_get_db_settings_option')  ){
				$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
			}
					$user_promotion 		= !empty($user_promotions['gadget']) ? $user_promotions['gadget'] : '';
					if( !empty($user_promotion) && $user_promotion ==='enable' ){
						do_action('workreap_update_users_marketing_attributes',$user_id,'is_verified');
					}

					$user_type						= workreap_get_user_type( $user_id );
					$freelancer_package_id			= workreap_get_package_type( 'package_type','trail_freelancer');
					$employer_package_id			= workreap_get_package_type( 'package_type','trail_employer');

					if( $user_type === 'employer' && !empty($employer_package_id) ) {
						workreap_update_pakage_data( $employer_package_id ,$user_id,'' );
					} else if( $user_type === 'freelancer' && !empty($freelancer_package_id) ) {
						workreap_update_pakage_data( $freelancer_package_id ,$user_id,'' );
					}
				}
			}
		}
	}
}

/**
 * @get default color schemes
 * @return 
 */
if (!function_exists('workreap_get_page_color')) {
	add_filter('workreap_get_page_color','workreap_get_page_color',10,1);
	function workreap_get_page_color($color='#5dc560'){
		$post_name = workreap_get_post_name();
		$pages_color	= array(
			'home-v5'		=> '#5dc560',
			'home-page-8'	=> '#017EBE',
			'home-v2'		=> '#5dc560',
			'header-v2'		=> '#5dc560',
		);

		if( isset( $_SERVER["SERVER_NAME"] ) && $_SERVER["SERVER_NAME"] === 'amentotech.com' ){
			if( isset( $pages_color[$post_name] ) ){
				return $pages_color[$post_name];
			} else{
				return $color;
			}
		} else{
			return $color;
		}
	}
}


/**
 * Removes the original author meta box and replaces it
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if (!function_exists('workreap_replace_post_author_meta_box')) {
	add_action( 'add_meta_boxes', 'workreap_replace_post_author_meta_box' );
	function workreap_replace_post_author_meta_box() {
		$post_type = get_post_type();
		$post_type_object = get_post_type_object( $post_type );
		if( $post_type == 'projects'){
			if ( post_type_supports( $post_type, 'author' ) ) {
				if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) ) {
					remove_meta_box( 'authordiv', $post_type, 'core' );
					add_meta_box( 'authordiv', esc_html__( 'Author', 'workreap_core' ), 'workreap_post_author_meta_box', null, 'normal' );
				}
			}
		}
		
		if( $post_type == 'freelancers' ){
			if ( post_type_supports( $post_type, 'author' ) ) {
				if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) ) {
					remove_meta_box( 'authordiv', $post_type, 'core' );
					add_meta_box( 'authordiv', esc_html__( 'Author', 'workreap_core' ), 'workreap_post_author_meta_box_freelancer', null, 'normal' );
				}
			}
		}
		
		if( $post_type == 'micro-services' ){
			if ( post_type_supports( $post_type, 'author' ) ) {
				if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) ) {
					remove_meta_box( 'authordiv', $post_type, 'core' );
					add_meta_box( 'authordiv', esc_html__( 'Author', 'workreap_core' ), 'workreap_post_author_meta_box_services', null, 'normal' );
				}
			}
		}
		
		if( $post_type == 'addons-services' ){
			if ( post_type_supports( $post_type, 'author' ) ) {
				if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) ) {
					remove_meta_box( 'authordiv', $post_type, 'core' );
					add_meta_box( 'authordiv', esc_html__( 'Author', 'workreap_core' ), 'workreap_post_author_meta_box_services', null, 'normal' );
				}
			}
		}
		
		if( $post_type == 'services-orders' ){
			if ( post_type_supports( $post_type, 'author' ) ) {
				if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) ) {
					remove_meta_box( 'authordiv', $post_type, 'core' );
					add_meta_box( 'authordiv', esc_html__( 'Author', 'workreap_core' ), 'workreap_post_author_meta_box_order_services', null, 'normal' );
				}
			}
		}
	}
}

/**
 * @Demo Ready
 * @return {}
 */
if (!function_exists('workreap_is_demo_site')) {
	function workreap_is_demo_site($message=''){
		$json = array();
		$message	= !empty( $message ) ? $message : esc_html__("Sorry! you are restricted to perform this action on demo site.",'workreap_core' );

		if( isset( $_SERVER["SERVER_NAME"] ) 
			&& $_SERVER["SERVER_NAME"] === 'amentotech.com' ){
			$json['type']	    =  "error";
			$json['message']	=  $message;
			echo json_encode( $json );
			exit();
		}
	}
}

/**
 * @taxonomy admin radio button
 * @return {}
 */
if (!function_exists('workreap_Walker_Category_Radio_Checklist')) {
	add_filter( 'wp_terms_checklist_args', 'workreap_Walker_Category_Radio_Checklist', 10, 2 );
	function workreap_Walker_Category_Radio_Checklist( $args, $post_id ) {
		if ( !empty($args['taxonomy']) && ( $args['taxonomy'] === 'response_time' || $args['taxonomy'] === 'delivery' ) ) {
			if ( empty( $args['walker'] ) || is_a( $args['walker'], 'Walker' ) ) { 
				if ( ! class_exists( 'Workreap_Walker_Category_Radio' ) ) {
					
					class Workreap_Walker_Category_Radio extends Walker_Category_Checklist {
						public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
							
							if ( empty( $args['taxonomy'] ) ) {
								$taxonomy = 'category';
							} else {
								$taxonomy = $args['taxonomy'];
							}

							if ( $taxonomy == 'category' ) {
								$name = 'post_category';
							} else {
								$name = 'tax_input[' . $taxonomy . ']';
							}

							$args['popular_cats'] = empty( $args['popular_cats'] ) ? array() : $args['popular_cats'];
							$class = in_array( $category->term_id, $args['popular_cats'] ) ? ' class="main-category"' : '';

							$args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];
							if ( ! empty( $args['list_only'] ) ) {
								$is_checked 	= 'false';
								$main_class 	= 'category';

								if ( in_array( $category->term_id, $args['selected_cats'] ) ) {
									$main_class 	.= ' selected';
									$is_checked 	 = 'true';
								}

								$output .= "\n" . '<li' . $class . '>' .
									'<div class="' . $main_class . '" data-term-id=' . $category->term_id .
									' tabindex="0" role="checkbox" aria-checked="' . $is_checked . '">' .
									esc_html( apply_filters( 'the_category', $category->name ) ) . '</div>';
							} else {
								$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
								'<label class="dc-radios"><input value="' . $category->term_id . '" type="radio" name="'.$name.'[]" id="dc-'.$taxonomy.'-' . $category->term_id . '"' .
								checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
								disabled( empty( $args['disabled'] ), false, false ) . ' /> ' .
								esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
							}
						}
					}
				}
				
				$args['walker'] = new Workreap_Walker_Category_Radio;
			}
		}
		return $args;
	}
}

/**
 * @Hide Quick edit form project and service
 * @return {}
 */
if (!function_exists('workreap_remove_quick_edit')) {
	function workreap_remove_quick_edit( $actions ) { 
		$post_type = get_post_type();
		if ( $post_type === 'micro-services' || $post_type === 'employers' || $post_type === 'freelancers' || $post_type === 'projects' || $post_type === 'services-orders') {
			unset($actions['inline hide-if-no-js']);
		}
		 return $actions;
	}
	add_filter('post_row_actions','workreap_remove_quick_edit',10,1);
}

/**
 * @create social login URL
 * Return{}
 */
if ( !function_exists( 'workreap_new_social_login_url' ) ) {
	function workreap_new_social_login_url($key='googlelogin') {
	  return site_url('wp-login.php') . '?'.$key.'=1';
	}
}

/**
 * @create social login uniqe ID
 * Return{}
 */
if(!function_exists('workreap_get_uniqid')){
    function workreap_get_uniqid(){
        if(isset($_COOKIE['workreap_uniqid'])){
            if(get_site_transient('n_'.$_COOKIE['workreap_uniqid']) !== false){
                return $_COOKIE['workreap_uniqid'];
            }
        }
		
        $_COOKIE['workreap_uniqid'] = uniqid('workreap_core', true);
        setcookie('workreap_uniqid', $_COOKIE['workreap_uniqid'], time() + 3600, '/');
        set_site_transient('n_'.$_COOKIE['workreap_uniqid'], 1, 3600);
        
        return $_COOKIE['workreap_uniqid'];
    }
}

/**
 * @create social users
 * Return{}
 */
if ( !function_exists( 'workreap_new_social_login' ) ) {
	add_action( 'login_init', 'workreap_new_social_login' );

	function workreap_new_social_login() {

		if ( isset( $_GET[ 'googlelogin' ] ) && $_GET[ 'googlelogin' ] == '1' ) {
			do_action('do_google_connect');
			workreap_new_social_redirect('google');
		} else if ( isset( $_GET[ 'facebooklogin' ] ) && $_GET[ 'facebooklogin' ] == '1' ) {
			do_action('do_facebook_connect');
			workreap_new_social_redirect('facebook');
		}else if ( isset( $_GET['linkedin'] ) && $_GET['linkedin'] == '1' ) {
			workreap_new_social_redirect('linkedin');
		}
	}
}

/**
 * @Send verification
 * @return 
 */
if( !function_exists( 'workreap_js_social_login') ){
	function workreap_js_social_login(){
		$json 		= array();
		
		if( !empty( $_POST ) ){
			$user	= !empty( $_POST ) ? $_POST : array();
			$user_email	= !empty( $_POST['email'] ) && is_email( $_POST['email'] ) ? $_POST['email']: '';
			
			$login_type	= !empty( $_POST['login_type'] ) ? $_POST['login_type']: 'facebook';
			$ID = email_exists( $user_email );
			
			$profile_page	= '';
			if( function_exists('workreap_get_search_page_uri') ){
				$profile_page  = workreap_get_search_page_uri('dashboard');
			}

			$profile_url    = '';
			if( !empty($profile_page) ) {
				$profile_url    = workreap_registration_redirect();
			}
			
			if ( $ID == false ) { // Real register
				workreap_create_social_users($login_type,$user);
				$json['type'] 		= 'success';
				$json['redirect']	= $profile_url;
				$json['message'] 	= esc_html__('You are successfully login. ', 'workreap_core');  
			} else if ( !empty( $ID ) ) { // Login
				$user_id	= workreap_do_social_login($ID,'yes');
				if( !empty($user_id) && $user_id === $ID ){
					$json['type']		= 'success';
					$json['redirect']	= $profile_url;
					$json['message'] 	= esc_html__('You are successfully login.', 'workreap_core');
				}
			}
		}
        
        wp_send_json($json);
	}
	add_action('wp_ajax_workreap_js_social_login', 'workreap_js_social_login');
    add_action('wp_ajax_nopriv_workreap_js_social_login', 'workreap_js_social_login');
}
/**
 * @get redirect URL
 * Return{}
 */
if (!function_exists('workreap_create_social_users')) {
	add_action('workreap_create_social_users','workreap_create_social_users',10,2);
	function workreap_create_social_users($type,$user) {
		$email = filter_var( $user[ 'email' ], FILTER_SANITIZE_EMAIL );
		$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
		
		if ( function_exists( 'fw_get_db_settings_option' ) ) {
			$fb_prefix 			= fw_get_db_settings_option( 'fb_prefix' );
			$g_prefix 			= fw_get_db_settings_option( 'g_prefix' );
			$linkedin_prefix 	= fw_get_db_settings_option( 'linkedin_prefix' );
		}
		
		if( isset( $type ) && $type === 'facebook' && !empty( $fb_prefix ) ){
			$prefix	= $fb_prefix;
		} else if( isset( $type ) && $type === 'google' && !empty( $g_prefix ) ){
			$prefix	= $g_prefix;
		} else if( isset( $type ) && $type === 'linkedin' && !empty( $g_prefix ) ){
			$prefix	= $linkedin_prefix;
		} else{
			$prefix = '';
		}

		$sanitized_user_login = sanitize_title( $prefix . $user[ 'name' ]);
		
		if ( !validate_username( $sanitized_user_login ) ) {
			$sanitized_user_login = sanitize_title( $type . $user[ 'id' ]);
		}
		
		$defaul_user_name = $sanitized_user_login;
		
		$i = 1;
		while ( username_exists( $sanitized_user_login ) ) {
			$sanitized_user_login = $defaul_user_name . $i;
			$i++;
		}

		$ID = wp_create_user( $sanitized_user_login, $random_password, $email );
		
		if ( !is_wp_error( $ID ) ) {
			global $wpdb;
			$db_user_role = '';
			wp_update_user( array('ID' => esc_sql( $ID ), 'role' => 'subscriber', 'user_status' => 1 ) );

			update_user_meta( $ID, 'show_admin_bar_front', false);
			update_user_meta( $ID, 'register_with_social', 'yes' );
			update_user_meta( $ID, 'company_name', $user[ 'name' ] );
			update_user_meta( $ID, 'first_name', $user[ 'name' ] );
			update_user_meta( $ID, 'email', esc_attr( $email ) );
			update_user_meta( $ID, 'rich_editing', 'true' );
			$verify_user	= 'no';
			
			update_user_meta( $ID, '_is_verified', $verify_user );
			
			$user_promotions	= array();
			if( function_exists('fw_get_db_settings_option')  ){
				$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
			}
			
			$user_promotion 		= !empty($user_promotions['gadget']) ? $user_promotions['gadget'] : '';
			if( !empty($user_promotion) && $user_promotion ==='enable' ){
				do_action('workreap_update_users_marketing_attributes',$ID,'is_verified');
			}

			//upload avatar
			do_action('workreap_do_upload_social_user_avatar',$user,$type,$ID);
						
			$user_info = get_userdata( $ID );
			update_user_meta( $ID, 'new_'.$type.'_default_password', $user_info->user_pass );
			do_action('workreap_do_social_login',$ID);
			//Send email to user
		}
	}
}

/**
 * @get redirect URL
 * Return{}
 */
if (!function_exists('workreap_do_upload_social_user_avatar')) {
	add_action('workreap_do_upload_social_user_avatar','workreap_do_upload_social_user_avatar',10,3);
	function workreap_do_upload_social_user_avatar($user,$type,$user_id) {
		$filename	= $user['id'].'.jpg';
		$size_type  = 'avatar';
		$uploaddir 	= wp_upload_dir();
		$uploadfile = $uploaddir['path'] . '/' .$filename;

		if( isset( $type ) && $type === 'facebook' ){
			$url	= 'https://graph.facebook.com/'.$user['id'].'/picture?width=600';
		} else{
			$url	= $user['picture'];
		}

		if( empty( $url ) ){ return;}

		$image_string = file_get_contents($url, false);
		$fileSaved 	  = file_put_contents($uploaddir['path'] . "/" . $filename, $image_string);

		$wp_filetype = wp_check_filetype($filename, null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => $filename,
			'post_content' => '',
			'post_status' => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $uploadfile );

		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		
		$attach_data = wp_generate_attachment_metadata( $attach_id, $uploadfile );
		wp_update_attachment_metadata( $attach_id,  $attach_data );
	    update_user_meta($user_id, 'social_avatar', $attach_id);
		
	}
}

/**
 * @get redirect URL
 * Return{}
 */
if (!function_exists('workreap_do_social_login')) {
	add_action('workreap_do_social_login','workreap_do_social_login',10,2);
	function workreap_do_social_login($ID,$return='') {
		global $auth_secure_cookie; // XXX ugly hack to pass this to wp_authenticate_cookie
		$secure_cookie = is_ssl();
		$secure_cookie = apply_filters( 'secure_signon_cookie', $secure_cookie, array() );
		$auth_secure_cookie = $secure_cookie;
		wp_set_auth_cookie( $ID, true, $secure_cookie );
		$user_info = get_userdata( $ID );
		do_action( 'wp_login', $user_info->user_login, $user_info );
		
		if( !empty( $return )  && $return === 'yes' ) {
			return $user_info->ID;
		}
	}
}

/**
 * @get redirect URL
 * Return{}
 */
if (!function_exists('workreap_new_social_redirect')) {
	function workreap_new_social_redirect($key) {
		$profile_page	= '';
		if( function_exists('workreap_get_search_page_uri') ){
			$profile_page  = workreap_get_search_page_uri('dashboard');
		}
		
		$profile_url    = '';
		if( !empty($profile_page) ) {
			$profile_url    = workreap_registration_redirect();
		}

		$redirect   = $profile_url;
		$redirect = wp_sanitize_redirect($redirect);
		$redirect = wp_validate_redirect($redirect, site_url());
		header('LOCATION: ' . $redirect);
		delete_site_transient( workreap_get_uniqid().'_'.$key.'_r');
		exit;
	}
}

/**
 * @Check if user is registered with social profiles
 * @return 
 */
if (!function_exists('workreap_is_social_user')) {

    function workreap_is_social_user($user_identity) {
		$is_social	= 'no';
        if (!empty($user_identity)) {
            $data = get_userdata($user_identity);
            if ( !empty($data->roles[0]) 
				&& $data->roles[0] === 'subscriber'
				&& !empty( $data->register_with_social_profiles ) 
				&& $data->register_with_social_profiles === 'yes'
			) {
                $is_social	= 'yes';
            } else{
				$is_social	= 'no';
			}
			
        }
		
		return $is_social;
    }

    add_filter('workreap_is_social_user', 'workreap_is_social_user', 10, 1);
}

/**
 * @Add Meta tag
 * @return 
 */
if (!function_exists('workreap_add_meta_tags')) {
	function workreap_add_meta_tags(){
		$gosocial_connect	= '';
		$client_id			= '';
		if ( function_exists('fw_get_db_settings_option' )) {
			$gosocial_connect	= fw_get_db_settings_option('enable_google_connect');
			$client_id			= fw_get_db_settings_option('client_id');
		}
		
		if( !empty( $client_id ) && !empty( $gosocial_connect ) && $gosocial_connect === 'enable' ){
			ob_start(); ?>
	  		<meta name="google-signin-client_id" content="<?php echo esc_attr( $client_id );?>">
		<?php 
			echo ob_get_clean();
		}
	}
	//add_action('wp_head', 'workreap_add_meta_tags');
}

/**
 * @Add async and defer to specfic file
 * @return 
 */

if (!function_exists('workreap_add_defer_attribute')) {
	function workreap_add_defer_attribute($tag, $handle) {
	   $scripts_to_defer = array('workreap-gconnect');

	   foreach($scripts_to_defer as $defer_script) {
		  if ($defer_script === $handle) {
			 return str_replace(' src', ' async defer src', $tag);
		  }
	   }
	   return $tag;
	}
	//add_filter('script_loader_tag', 'workreap_add_defer_attribute', 10, 2);
}

/**
 * Filters all menu item URLs for a #placeholder#.
 *
 * @param WP_Post[] $menu_items All of the nave menu items, sorted for display.
 *
 * @return WP_Post[] The menu items with any placeholders properly filled in.
 */
if (!function_exists('workreap_post_type_button')) {
	add_filter( 'wp_nav_menu_objects', 'workreap_post_type_button' );
	function workreap_post_type_button( $menu_items ) {
		global $current_user;
		$placeholders = array(
			'#post_job_button#' 	=> array(
				'shortcode' 	=> 'wt_post_button',
				'type' 			=> 'job',
			),
			'#post_service_button#' 	=> array(
				'shortcode' 	=> 'wt_post_button',
				'type' 			=> 'service',
			),
		);

		foreach ( $menu_items as $menu_item ) {
			if ( isset( $placeholders[ $menu_item->url ] ) ) {
				global $shortcode_tags;
				$placeholder = !empty( $placeholders[ $menu_item->url ] ) ? $placeholders[ $menu_item->url ] : '';
				if ( isset( $shortcode_tags[ $placeholder['shortcode'] ] ) ) {
					if (is_user_logged_in()) {
						if ( apply_filters('workreap_get_user_type', $current_user->ID) === 'employer' ){
							$menu_item->url = Workreap_Profile_Menu::workreap_profile_menu_link('post_job', $current_user->ID,'return');
							$menu_item->classes[]	= 'wt-post-type-button';
							
							if( isset( $placeholder['type'] ) && $placeholder['type'] === 'service' ){
								$menu_item->classes[]	= 'hide-post-menu';
								$menu_item->url 	= '';
								$menu_item->title   = '';
							}
							
						} elseif ( apply_filters('workreap_get_user_type', $current_user->ID) === 'freelancer' ){
							$menu_item->url = Workreap_Profile_Menu::workreap_profile_menu_link('micro_service', $current_user->ID,'return');
							$menu_item->classes[]	= 'wt-post-type-button';
							
							if( isset($placeholder['type']) && $placeholder['type'] === 'job' ){
								$menu_item->classes[]	= 'hide-post-menu';
								$menu_item->url 	= '';
								$menu_item->title   = '';
							}
							
						}else{
							$menu_item->classes[]	= 'hide-post-menu';
							$menu_item->url 	= '';
							$menu_item->title   = '';
						}
					} else{
						$menu_item->classes[]	= 'wt-post-type-button wt-joinnowbtn';
						$menu_item->url 	=  workreap_get_signup_page_url('step', '1');        
					}
				}
			}
		}

		return $menu_items;
	}
}


/**
 * add/update user for markettings
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( ! function_exists( 'workreap_update_users_marketing' ) ) {
	function workreap_update_users_marketing($user_id='') {
		
		$user_data					= array();
		$userdata 					= get_userdata($user_id);
		$user_data['email']			= !empty($userdata->user_email) ? $userdata->user_email : '';
		$user_data['first_name']	= !empty($userdata->first_name) ? $userdata->first_name : '';
		$user_data['last_name']		= !empty($userdata->last_name) ? $userdata->last_name : '';
		$user_data['phone_number']	= '';
		$user_data['user_id']		= $user_id;
		
		$crm_user_id = get_user_meta($user_id, '_crm_user_id', true);

		$user_promotions	= array();
		if( function_exists('fw_get_db_settings_option')  ){
			$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
		}
		
		$user_promotion 	= !empty($user_promotions['gadget']) ? $user_promotions['gadget'] : '';
		
		$app_key				= !empty($user_promotions['enable']['user_api_keys']) ? $user_promotions['enable']['user_api_keys'] : '';
		$subdomain				= !empty($user_promotions['enable']['user_app_subdomain']) ? $user_promotions['enable']['user_app_subdomain'] : '';
		
		if(!empty($user_data) && !empty($user_promotion) && $user_promotion ==='enable' && !empty($app_key) && !empty($subdomain)) {
			if( !empty($crm_user_id) ){
				$url	= 'https://'.$subdomain.'.user.com/api/public/users/'.$crm_user_id.'/';
				$args	= array();
				
				$args = array(
					'timeout'     => 30,
					'method'      => 'PUT',
					'headers' 	  => array('content-type' => 'application/json','authorization' => 'Token '.$app_key),
					'httpversion' => '1.1',
					'body' 		  => json_encode( $user_data ),
					
				);
				
				
				$response	= wp_remote_request($url,$args);
				$response	= wp_remote_retrieve_body($response);
				
				if($response) {
					$response	= json_decode($response);
					if(!empty($response->id)){
						update_user_meta( $user_id, '_crm_user_id', $response->id );
						do_action('workreap_update_users_marketing_attributes',$user_id,'user_type');
						do_action('workreap_update_users_marketing_attributes',$user_id,'url');
					}
				}
				
			} else{

				$is_cookie	= !empty( $_COOKIE['__ca__chat'] ) ? $_COOKIE['__ca__chat'] : '';
				if(!empty($is_cookie)) {
					$args = array(
						'timeout'     => 30,
						'headers' 	  => array('authorization' => 'Token '.$app_key),
						'httpversion' => '1.1',
					);

					$url	= "https://".$subdomain.".user.com/api/public/users/search/?key=".$is_cookie;

					$response	= wp_remote_request($url,$args);
					$response	= wp_remote_retrieve_body($response);

					if($response) {
						$response	= json_decode($response);
						if(!empty($response->id)){
							$url	= 'https://'.$subdomain.'.user.com/api/public/users/'.$response->id.'/';
							$args = array(
								'timeout'     => 30,
								'method'      => 'PUT',
								'headers' 	  => array('content-type' => 'application/json','authorization' => 'Token '.$app_key),
								'httpversion' => '1.1',
								'body' 		  => json_encode( $user_data ),

							);
							
							wp_remote_request($url,$args);
							
							update_user_meta( $user_id, '_crm_user_id', $response->id );
							do_action('workreap_update_users_marketing_attributes',$user_id,'user_type');
							do_action('workreap_update_users_marketing_attributes',$user_id,'url');
						}
					} 
				} else{

					$args = array(
						'timeout'     => 30,
						'method'      => 'POST',
						'headers' 	  => array('content-type' => 'application/json','authorization' => 'Token '.$app_key),
						'httpversion' => '1.1',
						'body' 		  => json_encode( $user_data ),

					);

					$url	= "https://".$subdomain.".user.com/api/public//users/update_or_create/";

					$response	= wp_remote_request($url,$args);
					$response	= wp_remote_retrieve_body($response);

					if($response) {
						$response	= json_decode($response);

						if(!empty($response->id)){
							update_user_meta( $user_id, '_crm_user_id', $response->id );
						}

						do_action('workreap_update_users_marketing_attributes',$user_id,'user_type');
						do_action('workreap_update_users_marketing_attributes',$user_id,'url');
					}
				}

				return $response;
			}
		}
		
	}
	add_action( 'workreap_update_users_marketing', 'workreap_update_users_marketing',10,1 );
}

/**
 * Check if user exist
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( ! function_exists( 'workreap_usercom_user_exist' ) ) {
	function workreap_usercom_user_exist($user_id) {
		if(empty($user_id)){return;}

		$userdata 		= get_userdata($user_id);
		$email			= !empty($userdata->user_email) ? $userdata->user_email : '';
		
		$user_promotions	= array();
		if( function_exists('fw_get_db_settings_option')  ){
			$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
		}
		
		
			
		$user_promotion 	= !empty($user_promotions['gadget']) ? $user_promotions['gadget'] : '';
		
		$app_key				= !empty($user_promotions['enable']['user_api_keys']) ? $user_promotions['enable']['user_api_keys'] : '';
		$subdomain				= !empty($user_promotions['enable']['user_app_subdomain']) ? $user_promotions['enable']['user_app_subdomain'] : '';
		
		if(!empty($user_promotion) && $user_promotion === 'enable' && !empty($app_key) && !empty($subdomain)) {
			$args = array(
				'timeout'     => 30,
				'headers' 	  => array('authorization' => 'Token '.$app_key),
				'httpversion' => '1.1'
			);

			$url	= "https://".$subdomain.".user.com/api/public/users/search/?email=".$email;

			$response	= wp_remote_request($url,$args);
			$response	= wp_remote_retrieve_body($response);
			
			if($response) {
				$response	= json_decode($response);
				if(!empty($response->id)){
					update_user_meta( $user_id, '_crm_user_id', $response->id );
				}
			}
		}
	}
}

/**
 * add/update user for markettings attributs
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( ! function_exists( 'workreap_update_users_marketing_attributes' ) ) {
	function workreap_update_users_marketing_attributes($user_id='',$field_name='',$value='') {
		$shor_name_array		= array();
		$user_promotions	= '';
		if( function_exists('fw_get_db_settings_option')  ){
			$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
		}
		
		$crm_user_id 			= get_user_meta($user_id, '_crm_user_id', true);
		if(empty($crm_user_id)){
			workreap_usercom_user_exist($user_id);
			$crm_user_id 	= get_user_meta($user_id, '_crm_user_id', true);
		}
		
		$user_promotion 	= !empty($user_promotions) ? $user_promotions['gadget'] : '';
		$app_key			= !empty($user_promotions['enable']['user_api_keys']) ? $user_promotions['enable']['user_api_keys'] : '';
		$subdomain			= !empty($user_promotions['enable']['user_app_subdomain']) ? $user_promotions['enable']['user_app_subdomain'] : '';

		if(!empty($user_id) && !empty($user_promotion) && $user_promotion === 'enable' && !empty($app_key) && !empty($subdomain)) {
			$attributes			= array();
			$profile_id			= workreap_get_linked_profile_id($user_id);
			
			if( !empty( $profile_id ) ){
    			$post_type	= get_post_field( 'post_type', $profile_id );
                if( !empty( $post_type) ){
        			if(!empty($post_type) && $post_type ==='employers'){
        				$post_type	= esc_html__('Employers','workreap_core');
        			} else if(!empty($post_type) && $post_type === 'freelancers') {
        				$post_type	= esc_html__('Freelancers','workreap_core');
        			}
        			
        			$attributes['user_type']	= $post_type;
        			$attributes['last_login']	= date("c",strtotime("now"));
    			}
			}

			if($field_name	=== 'experience'){
				$experience 	= array();
				if (function_exists('fw_get_db_post_option')) {
					$experience 	   		= fw_get_db_post_option($profile_id, 'experience',$default_value = null);
				}
				
				$attributes['experience']	= (!empty($experience) && (count($experience) > 0) ) ? esc_html__('Yes','workreap_core') : esc_html__('No','workreap_core');
				if( !empty($value) ){
					$attributes['experience']	= $value;
				}
			}

			if($field_name	=== 'url'){
				$url				= get_permalink( $profile_id );
				$attributes['url']	= !empty($url) ? esc_url($url) : '#';	
			}
			
			if($field_name	=== 'portfolios'){
				$total_portfolios = count_user_posts($user_id,'wt_portfolio');
				$attributes['portfolios']	= esc_html__('No','workreap_core');
				if( !empty($total_portfolios) ){
					$attributes['portfolios']	= esc_html__('Yes','workreap_core');
				}
			}

			if($field_name	=== 'images_gallery'){
				$images_gallery 	= array();
				if (function_exists('fw_get_db_post_option')) {
					$images_gallery 	   		= fw_get_db_post_option($profile_id, 'images_gallery',$default_value = null);
				}
				
				$attributes['gallery_images']		= (!empty($images_gallery) && (count($images_gallery) > 0) ) ? esc_html__('Yes','workreap_core') : esc_html__('No','workreap_core');
				if( !empty($value) ){
					$attributes['gallery_images']	= $value;
				}
			}

			if($field_name	=== 'is_verified'){
				$verified					= get_post_meta( $profile_id, '_is_verified', true );
				$attributes['is_verified']	= !empty($verified) ? $verified : 'no';
			}

			if($field_name	=== 'cv_uploaded'){
				$attributes['resume_link']	= $value;
			}

			if($field_name	=== 'freelancer_type'){
				$attributes['freelancer_type']	= $value;
			}

			if($field_name	=== 'freelancer_bid'){
				$attributes['freelancer_bid']	= esc_html__('Yes','workreap_core');
				$attributes['last_bid_date']	= date("c",strtotime("now"));
				if( !empty($value) ){
					$attributes['freelancer_bid']	= $value;
					$attributes['last_bid_date']	= '';
				}
			}

			if($field_name	=== 'profile_photo'){
				if( has_post_thumbnail($profile_id) ){
					$attributes['profile_photo']	= esc_html__('Yes','workreap_core');
				} else {
					$attributes['profile_photo']	= esc_html__('No','workreap_core');
				}
			}

			if($field_name	=== 'posted_projects'){
				$attributes['posted_projects']	= esc_html__('Yes','workreap_core');
			}

			if( $field_name	=== 'bid_invited'){
				$attributes['bid_invited']	= esc_html__('Yes','workreap_core');
			}

			if($field_name	=== 'last_completed_date'){
				$attributes['last_completed_date']	= date("c",strtotime("now"));
			}

			if($field_name	=== 'registration_date'){
				$attributes['registration_date']	= date("c",strtotime("now"));
			}

			if( !empty($crm_user_id) ){
				$curl2 = curl_init();
				curl_setopt_array($curl2, array(
					CURLOPT_URL => "https://$subdomain.user.com/api/public/users/$crm_user_id/set_multiple_attributes/",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => json_encode($attributes),
					CURLOPT_HTTPHEADER => array(
					"authorization: Token ".$app_key."",
					"content-type: application/json"
					),
				));
				curl_exec($curl2);
			}
		}
	}
	add_action( 'workreap_update_users_marketing_attributes', 'workreap_update_users_marketing_attributes',10,3 );
}

if(!function_exists('workreap_update_users_marketing_product_creation')) {
	function workreap_update_users_marketing_product_creation ($user_id='', $project_id = '', $field_name='') {

		global $current_user;
		$user_promotions	= '';
		if( function_exists('fw_get_db_settings_option')  ){
			$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
		}

		$crm_user_id 			= get_user_meta($user_id, '_crm_user_id', true);
		
		if(empty($crm_user_id)){
			workreap_usercom_user_exist($user_id);
			$crm_user_id 	= get_user_meta($user_id, '_crm_user_id', true);
		}

		$user_promotion 	= !empty($user_promotions) ? $user_promotions['gadget'] : '';
		$app_key			= !empty($user_promotions['enable']['user_api_keys']) ? $user_promotions['enable']['user_api_keys'] : '';
		$subdomain			= !empty($user_promotions['enable']['user_app_subdomain']) ? $user_promotions['enable']['user_app_subdomain'] : '';

		if(!empty($user_id) && !empty($project_id) && !empty($user_promotion) && $user_promotion === 'enable' && !empty($app_key) && !empty($subdomain)) {

			$project_create = array();
			$project_data = array();
			$client_data = array();

			$db_project_type = '';
			if (function_exists('fw_get_db_post_option')) {
				$db_project_type      = fw_get_db_post_option($project_id, 'project_type');
			}
	
			$db_max_price      = fw_get_db_post_option($project_id, 'max_price');
			$db_hourly_rate  		= !empty( $db_project_type['hourly']['hourly_rate'] ) ? $db_project_type['hourly']['hourly_rate'] : '';
			$db_estimated_hours  	= !empty( $db_project_type['hourly']['estimated_hours'] ) ? $db_project_type['hourly']['estimated_hours'] : '';
			$db_project_cost 		= !empty( $db_project_type['fixed']['project_cost'] ) ? $db_project_type['fixed']['project_cost'] : '';

			if( !empty( $db_project_type['gadget'] ) && $db_project_type['gadget'] === 'hourly' ) {
				$project_value = intval($db_max_price) * intval($db_estimated_hours);
			} else {
				$project_value = intval($db_max_price);
			}

			$db_expiry_date  = '';
			if (function_exists('fw_get_db_post_option')) {
				$db_expiry_date  = fw_get_db_post_option($project_id,'expiry_date');
			}

			$db_deadline  = '';
			if (function_exists('fw_get_db_post_option')) {
				$db_deadline    = fw_get_db_post_option($project_id,'deadline');
			}

			$post_author 		= get_post_field('post_author', $project_id);
			$job_link 			= get_permalink($project_id);

			$project_create['name'] 				= esc_html(get_the_title($project_id));
			$project_create['custom_id'] 			= esc_html(strtolower('Project_')).intval($project_id);
			
			$project_data['Person'] 				= esc_html(workreap_get_username($post_author));
			$project_data['Value'] 					= intval($project_value);
			$project_data['Currency'] 				= esc_html('USD');
			$project_data['Expected Close Date'] 	= date_i18n( get_option( 'date_format' ), strtotime( $db_expiry_date ));
			$project_data['project_url'] 			= esc_url($job_link);
			$project_data['proposal_count'] 		= intval(0);
			$project_data['project_deadline_date'] 	= date_i18n( get_option( 'date_format' ), strtotime( $db_deadline ));
			$project_data['Status'] 				= esc_html('in progress');

			$client_data['client'] 					= intval($crm_user_id);
			$client_data['event_type'] 				= esc_html('add');
			$client_data['timestamp'] 				= time();
			$client_data['data'] 					= $project_data;

			if( !empty($field_name) && $field_name === 'posted_projects') {
				$args = array(
					'timeout'     => 30,
					'method'      => 'POST',
					'headers' 	  => array('content-type' => 'application/json','authorization' => 'Token '.$app_key),
					'httpversion' => '1.1',
					'body' 		  => json_encode( $project_create ),
	
				);
	
				$url	= "https://".$subdomain.".user.com/api/public/products/";
	
				$response	= wp_remote_request($url,$args);
				$response	= wp_remote_retrieve_body($response);
	
				if($response) {
					$response	= json_decode($response);
	
					if(!empty($response->id)){ 
						update_post_meta( $project_id, '_crm_product_id', $response->id );
						$args = array(
							'timeout'     => 30,
							'method'      => 'POST',
							'headers' 	  => array('content-type' => 'application/json','authorization' => 'Token '.$app_key),
							'httpversion' => '1.1',
							'body' 		  => json_encode( $client_data ),
			
						);
			
						$url	= "https://".$subdomain.".user.com/api/public/products/".intval($response->id)."/product_event/";
	
						$event_response	= wp_remote_request($url, $args);
						$event_response	= wp_remote_retrieve_body($event_response);
	
						if($event_response) {
							$event_response	= json_decode($event_response);
						}
					}
				}
			}

			if( !empty($field_name) && $field_name === 'proposal_count_update') {
				$prposal_count = workreap_get_totoal_proposals($project_id);
				$crm_post_id  = get_post_meta( $project_id, '_crm_product_id', true);

				$attributes = array();
				$attributes['attribute'] = esc_html('proposal_count');
				$attributes['value'] = intval($prposal_count);

				if(!empty($crm_post_id)) {
					$args = array(
						'timeout'     => 30,
						'method'      => 'POST',
						'headers' 	  => array('content-type' => 'application/json','authorization' => 'Token '.$app_key),
						'httpversion' => '1.1',
						'body' 		  => json_encode( $attributes ),
		
					);
		
					$url	= "https://".$subdomain.".user.com/api/public/products/".intval($crm_post_id)."/set_attribute/";
		
					wp_remote_request($url,$args);
				}
				
			}

			if( !empty($field_name) && $field_name === 'product_status_update') {

				$crm_post_id  = get_post_meta( $project_id, '_crm_product_id', true);

				$attributes = array();
				$attributes['attribute'] = esc_html('Status');
				$attributes['value'] = esc_html('won');

				if(!empty($crm_post_id)) {
					$args = array(
						'timeout'     => 30,
						'method'      => 'POST',
						'headers' 	  => array('content-type' => 'application/json','authorization' => 'Token '.$app_key),
						'httpversion' => '1.1',
						'body' 		  => json_encode( $attributes ),
		
					);
		
					$url	= "https://".$subdomain.".user.com/api/public/products/".intval($crm_post_id)."/set_attribute/";
		
					wp_remote_request($url,$args);
				}
				
			}
		}
	}

	add_action('workreap_update_users_marketing_product_creation', 'workreap_update_users_marketing_product_creation', 10, 3);
}

/**
 * add/update user for markettings attributs
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if ( ! function_exists( 'workreap_update_users_marketing_default_attributes' ) ) {
	function workreap_update_users_marketing_default_attributes($user_id='') {
		$user_promotions	= '';
		if( function_exists('fw_get_db_settings_option')  ){
			$user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
		}
		
		$user_promotion 		= !empty($user_promotions) ? $user_promotions['gadget'] : '';
		
		$app_key			= !empty($user_promotions['enable']['user_api_keys']) ? $user_promotions['enable']['user_api_keys'] : '';
		$subdomain			= !empty($user_promotions['enable']['user_app_subdomain']) ? $user_promotions['enable']['user_app_subdomain'] : '';

		if(!empty($user_id) && !empty($user_promotion) && $user_promotion ==='enable' && !empty($app_key) && !empty($subdomain)) {
			$attributes			= array();
			$profile_id			= workreap_get_linked_profile_id($user_id);
			$user_type			= get_post_field( 'post_type', $profile_id );

			if(!empty($user_type) && $user_type === 'employers'){
				do_action('workreap_update_users_marketing_attributes',$user_id,'posted_projects',esc_html('No','workreap_core'));
				do_action('workreap_update_users_marketing_attributes',$user_id,'bid_invited',esc_html('No','workreap_core'));
				do_action('workreap_update_users_marketing_attributes',$user_id,'profile_photo',esc_html('No','workreap_core'));

			} else if(!empty($user_type) && $user_type === 'freelancers'){
				 do_action('workreap_update_users_marketing_attributes',$user_id,'experience',esc_html('No','workreap_core'));
				 do_action('workreap_update_users_marketing_attributes',$user_id,'freelancer_bid',esc_html('No','workreap_core'));
				 do_action('workreap_update_users_marketing_attributes',$user_id,'gallery_images',esc_html('No','workreap_core'));
			}
						
		}
	}
	add_action( 'workreap_update_users_marketing_default_attributes', 'workreap_update_users_marketing_default_attributes',10,1 );
}

/**
 * @Registration process Step Two
 * @return 
 */
if( !function_exists( 'workreap_single_step_registration' ) ){
	function workreap_single_step_registration(){
		$json	= array();
		session_start(array('user_data'));
		
		if( function_exists('workreap_is_demo_site') ) { 
			workreap_is_demo_site() ;
		}; //if demo site then prevent
		
		$verify_user	= '';
		if ( function_exists('fw_get_db_post_option' )) {
			$verify_user 	= fw_get_db_settings_option('verify_user', $default_value = null);
			$captcha_settings = fw_get_db_settings_option('captcha_settings', $default_value = null);
		}

		//recaptcha check
        if (isset($captcha_settings) && $captcha_settings === 'enable') {
            if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
                $docReResult = workreap_get_recaptcha_response($_POST['g-recaptcha-response']);

                if ($docReResult == 1) {
                    $workdone = 1;
                } else if ($docReResult == 2) {
					$json['type'] = 'error';
                    $json['message'] = esc_html__('An error occurred, please try again later.', 'workreap_core');
                    wp_send_json($json);
                } else {
					$json['type'] = 'error';
                    $json['message'] = esc_html__('Wrong reCaptcha. Please verify first.', 'workreap_core');
                    wp_send_json($json);
                }
            } else {
                wp_send_json(array('type' => 'error', 'message' => esc_html__('Please enter reCaptcha!', 'workreap_core')));
            }
        }
		
		//Validation
		$validations = array(
			'first_name' 		=> esc_html__('First Name field is required.', 'workreap_core'),
			'last_name'  		=> esc_html__('First Name field is required.', 'workreap_core'),
            'email' 			=> esc_html__('Email field is required', 'workreap_core'),
            'password' 			=> esc_html__('Password field is required', 'workreap_core'),
            'user_type'  		=> esc_html__('User type field is required.', 'workreap_core'),            
                 
        );
		foreach ( $validations as $key => $value ) {
            if ( empty( $_POST[$key] ) ) {
                $json['type'] = 'error';
                $json['message'] = $value;
                wp_send_json($json);
            }     
			
			if ($key === 'password') {
                if ( strlen( $_POST[$key] ) < 6 ) {
                    $json['type'] 	 = 'error';
                    $json['message'] = esc_html__('Password length should be minimum 6', 'workreap_core');
                    wp_send_json($json);
                }
			}
			//Validate email address
            if ( $key === 'email' ) {
                if ( !is_email( $_POST['email'] ) ) {
                    $json['type'] = 'error';
                    $json['message'] = esc_html__('Please add a valid email address.', 'workreap_core');
                    wp_send_json($json);
				}
				$user_exists 		 = email_exists( $_POST['email'] );
				if( $user_exists ){
					$json['type'] 		= 'error';
					$json['message'] 	= esc_html__('This email already registered', 'workreap_core');
					wp_send_json($json);
				}
       		}               
       	}	

       	//Get Data
		$first_name = !empty( $_POST['first_name'] ) ? esc_attr( $_POST['first_name'] ) : '';
		$last_name  = !empty( $_POST['last_name'] ) ? esc_attr( $_POST['last_name'] ) : '';
		$email   	= !empty( $_POST['email'] ) ? esc_attr( $_POST['email'] ) : '';
       	$password  	= !empty( $_POST['password'] ) ? esc_attr( $_POST['password'] ) : '';
       	$user_type 	= !empty( $_POST['user_type'] ) ? esc_attr( $_POST['user_type'] ) : '';

       	//Set User Role
       	$db_user_role = 'employers';
       	if( $user_type === 'freelancer' ){
       		$db_user_role = 'freelancers';
       	} else {
       		$db_user_role = 'employers';
       	}

		//User Registration
		$random_password = $password;
		$full_name 		 = $first_name.' '.$last_name;
		$user_nicename   = sanitize_title( $full_name );
		$username		 = $user_nicename;
		
		$userdata = array(
			'user_login'  		=>  $email,
			'user_pass'    		=>  $random_password,
			'user_email'   		=>  $email,  
			'user_nicename'   	=>  $user_nicename,  
			'display_name'   	=>  $full_name,  
		);
		
        $user_identity 	 = wp_insert_user( $userdata );
		
        if ( is_wp_error( $user_identity ) ) {
            $json['type'] = "error";
            $json['message'] = esc_html__("User already exists. Please try another one.", 'workreap_core');
            wp_send_json($json);
        } else {
        	global $wpdb;
            wp_update_user( array('ID' => esc_sql( $user_identity ), 'role' => esc_sql( $db_user_role ), 'user_status' => 1 ) );

            $wpdb->update(
                    $wpdb->prefix . 'users', array('user_status' => 1), array('ID' => esc_sql($user_identity))
            );

            update_user_meta( $user_identity, 'first_name', $first_name );
            update_user_meta( $user_identity, 'last_name', $last_name );             

			update_user_meta($user_identity, 'show_admin_bar_front', false);
            update_user_meta($user_identity, 'full_name', esc_attr($full_name));

			if( isset( $verify_user ) && $verify_user === 'verified' ){
				$key_hash = rand( 1000, 9999 );
				update_user_meta( $user_identity, '_is_verified', 'no' );
				update_user_meta( $user_identity, 'confirmation_key', $key_hash );
			} else {
				update_user_meta( $user_identity, '_is_verified', 'yes' );
			}
			
			//Create Post
			$user_post = array(
                'post_title'    => wp_strip_all_tags( $full_name ),
                'post_status'   => 'publish',
                'post_author'   => $user_identity,
                'post_type'     => $db_user_role,
            );

            $post_id    = wp_insert_post( $user_post );
			
            if( !is_wp_error( $post_id ) ) {
				
				$location   = !empty( $_POST['location'] ) ? esc_attr( $_POST['location'] ) : '';
				$shortname_option	= '';
                if( function_exists('fw_get_db_settings_option')  ){
                    $shortname_option	= fw_get_db_settings_option('shortname_option', $default_value = null);
                }
				
				if(!empty($shortname_option) && $shortname_option === 'enable' ){
					$shor_name			= workreap_get_username($user_identity);
					$shor_name_array	= array(
											'ID'        => $post_id,
											'post_name'	=> sanitize_title($shor_name)
										);
					wp_update_post($shor_name_array);
				}

				$fw_options = array();
				
				//Update user linked profile
            	update_user_meta( $user_identity, '_linked_profile', $post_id );
				update_post_meta( $post_id, '_is_verified', 'no' );
				
				
				update_post_meta( $post_id, '_hourly_rate_settings', 'off' );
				
            	if( $db_user_role == 'employers' ){
					
					update_post_meta($post_id, '_user_type', 'employer');
            		update_post_meta($post_id, '_employees', 'employer');            		
					update_post_meta($post_id, '_followers', '');

            	} elseif( $db_user_role == 'freelancers' ){
					update_post_meta($post_id, '_user_type', 'freelancer');
            		update_post_meta($post_id, '_perhour_rate', '');
            		update_post_meta($post_id, 'rating_filter', 0);
            		update_post_meta($post_id, '_freelancer_type', 'rising_talent');         		           		
            		update_post_meta($post_id, '_featured_timestamp', 0); 
					update_post_meta($post_id, '_english_level', 'basic');
					//extra data in freelancer
					
					$fw_options['_perhour_rate']    = '';
				}
								
				//add extra fields as a null
				$tagline	= '';
				update_post_meta($post_id, '_tag_line', $tagline);
				update_post_meta($post_id, '_address', '');
				update_post_meta($post_id, '_latitude', '');
				update_post_meta($post_id, '_longitude', '');
				
				$fw_options['address']    	= '';
				$fw_options['longitude']    = '';
				$fw_options['latitude']    	= '';
				$fw_options['tag_line']     = $tagline;
				//end extra data
				
				fw_set_db_post_option($post_id, null, $fw_options);
				
				//update privacy settings
				$settings		 = workreap_get_account_settings($user_type);
				if( !empty( $settings ) ){
					foreach( $settings as $key => $value ){
						$val = $key === '_profile_blocked' || $key === '_hourly_rate_settings'? 'off' : 'on';
						update_post_meta($post_id, $key, $val);
					}
				}

				
				update_post_meta($post_id, '_linked_profile', $user_identity);
				
				$user_promotions	= array();
                if( function_exists('fw_get_db_settings_option')  ){
                    $user_promotions	= fw_get_db_settings_option('user_marketing_promation_api_settings', $default_value = null);
				}
				$user_promotion 		= !empty($user_promotions) ? $user_promotions['gadget'] : '';
				
				if( !empty($user_promotion) && $user_promotion ==='enable' ){
					do_action('workreap_update_users_marketing',$user_identity);
					do_action('workreap_update_users_marketing_default_attributes',$user_identity);
					do_action('workreap_update_users_marketing_attributes',$user_identity,'is_verified');
					do_action('workreap_update_users_marketing_attributes',$user_identity,'registration_date');
				}
				
            	//Send email to users
            	if (class_exists('Workreap_Email_helper')) {
					$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
					$emailData = array();
					$emailData['name'] 				= $first_name;
					$emailData['password'] 			= $random_password;
					$emailData['email'] 			= $email;
					$emailData['verification_code'] = $key_hash;
					$emailData['site'] = $blogname;
					
					//Welcome Email
					if( $db_user_role === 'employers' ){
						
						if (class_exists('WorkreapRegisterEmail')) {
							$email_helper = new WorkreapRegisterEmail();
							$email_helper->send_employer_email($emailData);
						}
					} else if( $db_user_role === 'freelancers' ){
						if (class_exists('WorkreapRegisterEmail')) {
							$email_helper = new WorkreapRegisterEmail();
							$email_helper->send_freelacner_email($emailData);
						}
					}
					
					//Send code
					if( isset( $verify_user ) && $verify_user === 'verified' ){
						$json['verify_user'] 			= 'verified';
						if (class_exists('WorkreapRegisterEmail')) {
							$email_helper = new WorkreapRegisterEmail();
							$email_helper->send_verification($emailData);
						}
					} else{
						$json['verify_user'] 			= 'none';
					}
					
					//Send admin email
					if (class_exists('WorkreapRegisterEmail')) {
						$email_helper = new WorkreapRegisterEmail();
						$email_helper->send_admin_email($emailData);
					}
		        }		    
    		
            } else {
            	$json['type'] = 'error';
                $json['message'] = esc_html__('Some error occurs, please try again later', 'workreap_core');                
                wp_send_json($json);
            }			

			//User Login
			$user_array = array();
			$user_array['user_login'] 	 = $email;
        	$user_array['user_password'] = $random_password;
			$status = wp_signon($user_array, false);
			
			if( isset( $verify_user ) && $verify_user === 'none' ){
				$json_message = esc_html__("Your account have been created. Please wait while your account is verified by the admin.", 'workreap_core');
			} else{
				$json_message = esc_html__("Your account have been created. Please verify your account through verification code, an email have been sent your email address.", 'workreap_core');
			}
			
			//Delete session data
	        unset( $_SESSION['user_data'] );
			
			$user_type						= workreap_get_user_type( $user_identity );
			$freelancer_package_id			= workreap_get_package_type( 'package_type','trail_freelancer');
			$employer_package_id			= workreap_get_package_type( 'package_type','trail_employer');

			if( $user_type === 'employer' && !empty($employer_package_id) ) {
				workreap_update_pakage_data( $employer_package_id ,$user_identity,'' );
			} else if( $user_type === 'freelancer' && !empty($freelancer_package_id) ) {
				workreap_update_pakage_data( $freelancer_package_id ,$user_identity,'' );
			}
			

			//Redirect URL		
			$dashboard_page	= workreap_registration_redirect($user_identity);
			//Prepare Params
			$params_array	= array();
			$params_array['user_identity'] = $user_identity;
			
			if( $db_user_role === 'employers' ){
				$params_array['user_role'] = esc_html__("Employer", 'workreap_core');
			} else if( $db_user_role === 'freelancers' ){
				$params_array['user_role'] = esc_html__("Freelancer", 'workreap_core');
			}

			$params_array['type'] = 'register';
			
			do_action('wt_process_registration_child', $params_array);
			
			
			$json['type'] 			= 'success';
	        $json['message'] 		= $json_message;
	        $json['retrun_url'] 	= htmlspecialchars_decode($dashboard_page);
			wp_send_json($json);
		}		

	}
	add_action('wp_ajax_workreap_single_step_registration', 'workreap_single_step_registration');
	add_action('wp_ajax_nopriv_workreap_single_step_registration', 'workreap_single_step_registration');
	
}

/**
 * @OWL Carousel RTL
 * @return {}
 */
if (!function_exists('workreap_owl_rtl_check')) {

    function workreap_owl_rtl_check() {
        if (is_rtl()) {
            return 'true';
        } else {
            return 'false';
        }
    }
}