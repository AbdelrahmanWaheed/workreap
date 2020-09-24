var ajax_register_key = 1;
var fbapp_id = '';
var is_loggedin = '';
var signin_reset;
var signup_reset;
var forgot_reset;
var password_reset;

jQuery(document).ready(function(){   
	var loader_html = '<div class="wt-preloader-section"><div class="wt-preloader-holder"><div class="wt-loader"></div></div></div>'; 
	var Ajax_Register 	= {};
    window.Post_Steps 	= Ajax_Register;
    Ajax_Register.ajax_register_key = 1;
	Ajax_Register.ajax_register_key = {};
	
	fbapp_id 	= scripts_vars.fbapp_id;
	is_loggedin = scripts_vars.is_loggedin;


	//Open portfolio POPUP
	jQuery("body").one("click", ".open-modal-link", function(event) {
	   event.preventDefault();

		var _modal 		= jQuery("#popupwrapper");
		var _content 	= jQuery(".wt-portfolio-content-model");
		var _this 		= jQuery(this).attr("href") != null ? jQuery(this) : jQuery("a", this).first();
		var _link 		= _this.attr("href");
		
		var _slug 		= _link.lastIndexOf("/#") > -1 ? Getbasename(_link.substring(0, _link.lastIndexOf("/#"))) + Getbasename(_link) : Getbasename(_link);
        var _anc  		= _slug.lastIndexOf("#") !== -1 ? _slug.substring(_slug.lastIndexOf("#")) : false;
		
		
		jQuery(_content).html(scripts_vars.spinner);
		jQuery('#popupwrapper').modal('show');
		
		
		//Get page content
		jQuery.get(_link, function(html) {
			let str = Math.random().toString(36).substring(7);
			_content.html(html);
			jQuery('body').removeClass('async-hide');
			_modal.find('header').remove();
			_modal.find('footer.wt-footer').remove();
			_modal.find('.wt-appavailable').remove();
			_modal.find('.wt-innerbannerholder').remove();
			_modal.find('.preloader-outer').remove();
			_modal.find('#wpadminbar').remove();
			portfolioslider(str);
		});
		
	});
	
	//base name and slug from URL
	function Getbasename(url, slug) {
		var slug_item = url.charAt( url.length - 1 );
		
		if ( slug_item === "/" || slug_item === "\\") {
			url = url.slice(0, -1);
		}
		
		//Get base path
		url = url.replace(/^.*[/\\]/g, "");
		
		if (typeof slug === "string" && url.substr( url.length - slug.length ) === slug ) {
			url = url.substr(0, url.length - slug.length);
		}
		
		return url;
	}
	
	
	//register options hide show
	jQuery('.wt-ragister-option, .wt-ragister-social').on('click',function() {
	   var _this	= jQuery(this);
	   jQuery('.wt-accordiondetails').hide();
	   _this.next('.wt-accordiondetails').show();
	});
	
	//Google Connect
	jQuery(document).on('click', '.register-loginpop', function (event) {
		event.preventDefault();
		var _this = jQuery(this);
		jQuery('html, body').animate({scrollTop:0}, 'slow');
		
	});
	
	//Linkedin Connect
	jQuery(document).on('click', '.sp-linkedin-connect', function (event) {
		event.preventDefault();
		var _this = jQuery(this);
		jQuery('body').append(loader_html);
		
		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: '&action=workreap_linkedin_connect',
			dataType: "json",
			success: function (response) {
				if (response.type == 'success') {
					jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
					window.location.replace(response.authUrl);
				} else {
					jQuery('body').find('.provider-site-wrap').remove();
					jQuery.sticky(response.message, {classList: 'important',position:'center-center', speed: 200, autoclose: 5000});
				}
			}
		});
	});
	
	//Google Connect
	jQuery(document).on('click', '.sp-googl-connect', function (event) {
		event.preventDefault();
		var _this = jQuery(this);
		jQuery('body').append(loader_html);
		
		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: '&action=workreap_google_connect',
			dataType: "json",
			success: function (response) {
				if (response.type == 'success') {
					jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
					window.location.replace(response.authUrl);
				} else {
					jQuery('body').find('.provider-site-wrap').remove();
					jQuery.sticky(response.message, {classList: 'important',position:'center-center', speed: 200, autoclose: 5000});
				}
			}
		});
	});
	
	//facebook Connect
	jQuery(document).on('click', '.sp-fb-connect', function (event) {
		event.preventDefault();
		var _this = jQuery(this);
		jQuery('body').append(loader_html);
		
		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: '&action=workreap_fb_connect',
			dataType: "json",
			success: function (response) {
				if (response.type == 'success') {
					jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
					window.location.replace(response.authUrl);
				} else {
					jQuery('body').find('.provider-site-wrap').remove();
					jQuery.sticky(response.message, {classList: 'important',position:'center-center', speed: 200, autoclose: 5000});
				}
			}
		});
	});
	
	//Model for signup
	jQuery('.wt-registration-poup, .wt-joinnowbtn .brz-a, .wt-post-type-button.wt-joinnowbtn a').on('click', function(event){
		event.preventDefault();
		jQuery('.wt-joinnowbtn').click();
		jQuery('#loginpopup').modal('hide');
		jQuery('#joinpopup').modal('show');
	});
	
	//Registration Step One poup    
    jQuery(document).on('click', '.wt-model-reg1', function (e) { 
        e.preventDefault();
        var _this 	= jQuery(this);        
		var is_rtl  = scripts_vars.is_rtl;
        jQuery('body').append(loader_html);        
        var dataString = _this.parents('form.wt-formregister').serialize() + '&action=workreap_process_registration_step_one';
        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: dataString,
            dataType: "json",
            success: function (response) {
               jQuery('.wt-preloader-section').remove();
			   ajax_register_key = 1;
               if (response.type === 'success') {
                    jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
				   	jQuery.ajax({
						type: "POST",
						url: scripts_vars.ajaxurl,
						data: 'key=post' + '&action=workreap_registration_step_two',
						dataType: "json",
						success: function (response) {
							jQuery('.wt-registration-content-model').html(response.html);
							jQuery(".chosen-select").chosen({rtl:is_rtl});
							jQuery('.wt-ragister-option').on('click',function() {
							   var _this	= jQuery(this);
							   jQuery('.wt-accordiondetails').hide();
							   _this.next('.wt-accordiondetails').show();
							});	
							
						}
					});
                } else {
                    jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                }
            }
        });           
    });
	
	//Process Registration for model step 2  
    jQuery(document).on('click', '.wt-model-reg2', function (e) { 
        e.preventDefault();
        var _this = jQuery(this);          
        jQuery('body').append(loader_html);        
        var dataString = jQuery('.wt-formregister-step-two').serialize() + '&action=workreap_process_registration_step_two';
        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: dataString,
            dataType: "json",
            success: function (response) {
               jQuery('.wt-preloader-section').remove();
               if (response.type === 'success') {
				    if( scripts_vars.verify_user === 'none' ){
						var action = 'workreap_registration_step_four_filter';
						if( typeof(response.html) != "undefined" && response.html !== null ) {
							jQuery('.modal-post-wrap').html(response.html);
							jQuery('#joinpopup').modal('hide');
							jQuery('.wt-registration-content-model').modal('hide');
							jQuery('.modal-post-wrap').modal('show');
						}
					}else{
						var action = 'workreap_registration_step_three';
					}
					
					/*----------------For google analytics code---------------------*/
					if (typeof ga === 'function') {
						_gaEventSubmitTrigger('signup', scripts_vars.authentication_url);
					}
					/*--------------------------------------------------------------*/

                    jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
				   	jQuery.ajax({
						type: "POST",
						url: scripts_vars.ajaxurl,
						data: 'key=post' + '&action='+action,
						dataType: "json",
						success: function (response) {
							 jQuery('.wt-registerformmain').html(response.html);
						}
					});

                } else {
                    jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                }
            }
        });           
    });
	
	//Process Registration Validation   
	jQuery(document).on('click', '.wt-model-reg3', function (e) {
        e.preventDefault();
		
        var _this = jQuery(this);          
        jQuery('body').append(loader_html);        
        var dataString = jQuery('.wt-verifyform').serialize() + '&action=workreap_process_registration_step_three';
        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: dataString,
            dataType: "json",
            success: function (response) {
               jQuery('.wt-preloader-section').remove();
               if ( response.type === 'success') {
				    if( response.signup === 'yes' ){
						jQuery('.wt-registerformmain').html(response.html);
					} else{
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
						jQuery('.modal-post-wrap').html(response.html);
						jQuery('#joinpopup').modal('hide');
						jQuery('.modal-post-wrap').modal('show');
					}
                } else {
                    jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                }
            }
        });           
    });
	
	//Previous Step
	jQuery(document).on('click', '.wt-back-to-one', function (e) {
		jQuery('body').append(loader_html);     
		var dataString = 'key=1&action=workreap_registration_step_one';
		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: dataString,
			dataType: "json",
			success: function (response) {
				jQuery('.wt-preloader-section').remove();
				var is_rtl  = scripts_vars.is_rtl;
				jQuery('.wt-registration-content-model').html(response.html);
				workreap_gconnect_app();
				workreap_init_facebook();

			}
		}); 
    });
	
	//facebook login
	function workreap_init_facebook(){
		jQuery('.wt-facebookbox').on('click', function(e){
			facebookLogin();
		});

		jQuery('.wt-facebookbox-reg').on('click', function(e){
			facebookLogin();
		});
	}
	
	workreap_init_facebook();
	
	//Process Social Registration poup  
    jQuery(document).on('click', '.social-step-two-poup', function (e) { 
        e.preventDefault();
        var _this = jQuery(this);          
        jQuery('body').append(loader_html);        
        var dataString = jQuery('.wt-formregister-step-two').serialize() + '&action=workreap_process_social_registration_step_two';
        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: dataString,
            dataType: "json",
            success: function (response) {
                jQuery('.wt-preloader-section').remove();
               if (response.type === 'success') {
                    if( typeof(response.message) != "undefined" && response.message !== null ) {
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
					}
				   
				   	if( typeof(response.html) != "undefined" && response.html !== null ) {
	 					jQuery('.modal-post-wrap').html(response.html);
						jQuery('#joinpopup').modal('hide');
				   		jQuery('.modal-post-wrap').modal('show');
						
 					} else {
						 window.location.replace(response.retrun_url);
					}                  
				   	
                } else {
                    jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                }
            }
        });           
    });
	
	//Registration single step
    jQuery(document).on('click', '#wt-singe-signup', function (e) { 
        e.preventDefault();
		var _this = jQuery(this);         
        jQuery('body').append(loader_html);        
		var dataString = _this.parents('form#wt-single-joinnow-form').serialize() + '&action=workreap_single_step_registration';
		var role_type =  _this.parents('form#wt-single-joinnow-form').find('input[name=user_type]:checked').val();
        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: dataString,
            dataType: "json",
            success: function (response) {
                jQuery('.wt-preloader-section').remove();
               if (response.type === 'success') {
					jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
				   
				    if (typeof ga === 'function') {
						_gaEventSubmitTrigger('signup', '', role_type);
					}
				   
                    window.location.replace(response.retrun_url);
                } else {
					if(jQuery('#recaptcha_signup').length > 0){
                        grecaptcha.reset(recaptcha_signup);
                    }
                    jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                }
            }
        });           
	});
	
    //Registration Step One    
    jQuery(document).on('click', '.rg-step-one', function (e) { 
        e.preventDefault();
        var _this = jQuery(this);          
        jQuery('body').append(loader_html);        
        var dataString = _this.parents('form.wt-formregister').serialize() + '&action=workreap_process_registration_step_one';
        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: dataString,
            dataType: "json",
            success: function (response) {
                jQuery('.wt-preloader-section').remove();
               if (response.type === 'success') {
                    jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
                    window.location.replace(response.retrun_url);
                } else {
                    jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                }
            }
        });           
    });
    
    //Process Registration   
    jQuery(document).on('click', '.wt-step-two', function (e) { 
        e.preventDefault();
        var _this = jQuery(this);          
        jQuery('body').append(loader_html);        
        var dataString = jQuery('.wt-formregister-step-two').serialize() + '&action=workreap_process_registration_step_two';
        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: dataString,
            dataType: "json",
            success: function (response) {
                jQuery('.wt-preloader-section').remove();
               if (response.type === 'success') {
                    jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
                    window.location.replace(response.retrun_url);
                } else {
                    jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                }
            }
        });           
    });
	
	//Process Social Registration   
    jQuery(document).on('click', '.social-step-two', function (e) { 
        e.preventDefault();
        var _this = jQuery(this);          
        jQuery('body').append(loader_html);        
        var dataString = jQuery('.wt-formregister-step-two').serialize() + '&action=workreap_process_social_registration_step_two';
        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: dataString,
            dataType: "json",
            success: function (response) {
                jQuery('.wt-preloader-section').remove();
               if (response.type === 'success') {
                    jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
                    window.location.replace(response.retrun_url);
                } else {
                    jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                }
            }
        });           
    });
    
    //Submit Validation Form
    jQuery(document).on('click', '.wt-step-three', function(e){
        e.preventDefault();
         jQuery('.wt-verifyform').submit();
    });
    
    
    //Process Registration Validation    
    jQuery('.wt-verifyform').submit( function(e) { 
        e.preventDefault();
        var _this = jQuery(this);          
        jQuery('body').append(loader_html);        
        var dataString = jQuery('.wt-verifyform').serialize() + '&action=workreap_process_registration_step_three';
        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: dataString,
            dataType: "json",
            success: function (response) {
                jQuery('.wt-preloader-section').remove();
               if (response.type === 'success') {
					jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
					
                    window.location.replace(response.retrun_url);
                } else {
                    jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                }
            }
        });           
    });
        
    //Registration Complete    
    jQuery(document).on('click', '.wt-go-to-dashboard', function (e) { 
        e.preventDefault();
        var _this = jQuery(this);      
        var id = _this.data('id');   
        jQuery('body').append(loader_html);        
        var dataString = 'id='+ id+ '&action=workreap_process_registration_complete';
        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: dataString,
            dataType: "json",
            success: function (response) {
                jQuery('.wt-preloader-section').remove();
               if (response.type === 'success') {
                    jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
                    window.location.replace(response.retrun_url);
                } else {
                    jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                }
            }
        });           
    });

   
    //Resend Verification Code   
    jQuery(document).on('click', '.wt-resend-code', function (e) { 
        e.preventDefault();
        var _this = jQuery(this);       
        jQuery('body').append(loader_html);        
        var dataString = 'action=workreap_resend_verification_code';
        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: dataString,
            dataType: "json",
            success: function (response) {
                jQuery('.wt-preloader-section').remove();
               if (response.type === 'success') {
                    jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });                   
                } else {
                    jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                }
            }
        });           
    });

    
    //Reset password Ajax    
    jQuery(document).on('click', '.wt-change-password', function (event) {
        event.preventDefault();
        var _this = jQuery(this);       
        jQuery('body').append(loader_html);

        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: jQuery('.wt-reset_password_form').serialize() + '&action=workreap_ajax_reset_password',
            dataType: "json",
            success: function (response) {
                jQuery('body').find('.wt-preloader-section').remove();
                if (response.type == 'success') {
                    jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
                    jQuery('.wt-reset_password_form').get(0).reset();
                    window.location.replace(response.redirect_url);                   
                } else {                  
                    jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                }
            }
        });
    });
	
	jQuery(document).on('click', '#wt-single-sigin', function (event) {
		jQuery('#joinpopup').modal('hide');
		jQuery('#loginpopup').modal('show');
		jQuery('#wt-single-login-form').removeClass('wt-hide-form');
		jQuery('.do-forgot-password-form').addClass('wt-hide-form');

	});

	jQuery(document).on('click', '#wt-single-signup', function (event) {
		jQuery('#joinpopup').modal('show');
		jQuery('#loginpopup').modal('hide');
	});

	jQuery(document).on('click', '.wt-hidepassword', function (event) {
		event.preventDefault();
		var _this = jQuery(this);
		_this.parents().addClass('wt-passwordshow');
		_this.removeClass('wt-hidepassword');
		_this.addClass('wt-showpassword');
		_this.prev('.wt-password-field').prop("type", "text");
	});
	jQuery(document).on('click', '.wt-showpassword', function (event) {
		event.preventDefault();
		var _this = jQuery(this);
		_this.parents().removeClass('wt-passwordshow');
		_this.addClass('wt-hidepassword');
		_this.removeClass('wt-showpassword');
		_this.prev('.wt-password-field').prop("type", "password");
	});

    //Login Ajax    
    jQuery(document).on('click', '.do-login-button', function (event) {
        event.preventDefault();
        var _this = jQuery(this);
        jQuery('body').append(loader_html);
        var _serialize = _this.parents('form.do-login-form').serialize();
		
        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: _serialize + '&action=workreap_ajax_login',
            dataType: "json",
            success: function (response) {
				jQuery('body').find('.wt-preloader-section').remove();
				if (response.type === 'success') {
					if(response.job === 'post'){
						jQuery('#loginpopup').modal('hide');
						jQuery('.modal-post-wrap').html(response.html);
						jQuery('.modal-post-wrap').modal('show');
					}else {
						if (typeof ga === 'function') {
							_gaEventSubmitTrigger('signin', '', response.role_type);
						}
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 500000, position: 'top-right'});
                    	window.location.replace(response.redirect); 
					}
                                       
                } else { 
					if(jQuery('#recaptcha_signin').length > 0){
                        grecaptcha.reset(signin_reset);
                    }
                    jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                }
            }
        });
    });
    
    //Lost passowrd Box 
	function workreap_init_forgot(){
		jQuery('.wt-forgot-password').on('click', function (e) {
			var _this	= jQuery(this);
			jQuery('.do-login-form').addClass('wt-hide-form');
			jQuery('.wt-loginheader span').html(scripts_vars.forgot_password);
			jQuery('.wt-forgot-password').html(scripts_vars.login).addClass('wt-show-login').removeClass('wt-forgot-password');
			jQuery('.do-forgot-password-form').removeClass('wt-hide-form');
			workreap_init_login();
		});

		jQuery('.wt-forgot-password-single').on('click', function (e) {
			var _this	= jQuery(this);
			jQuery('.do-login-form').addClass('wt-hide-form');
			jQuery('.wt-forgot-password-single').addClass('wt-show-login');
			jQuery('.do-forgot-password-form').removeClass('wt-hide-form');
			workreap_init_login();
		});
	}
	
	function workreap_init_login(){
		jQuery('.wt-show-login').on('click', function (e) {     
			jQuery('.do-login-form').removeClass('wt-hide-form');
			jQuery('.wt-loginheader span').text(scripts_vars.login);
			jQuery('.do-forgot-password-form').addClass('wt-hide-form');
			jQuery('.wt-show-login').html(scripts_vars.forgot_password).addClass('wt-forgot-password').removeClass('wt-show-login');
			workreap_init_forgot();
		});
	}
	
	workreap_init_login();
	workreap_init_forgot();
	
    //Lost password Ajax
    jQuery(document).on('click', '.do-get-password-btn', function (event) {
        event.preventDefault();
        var _this = jQuery(this);
        var _email = _this.parents('.do-forgot-password-form').find('.get_password').val();       
        jQuery('body').append(loader_html);

        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: jQuery('.do-forgot-password-form').serialize() + '&action=workreap_ajax_lp',
            dataType: "json",
            success: function (response) {
                jQuery('body').find('.wt-preloader-section').remove();
                if (response.type == 'success') {
                    jQuery('.do-forgot-password-form').get(0).reset();
                    jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });                                       
                } else {
					if(jQuery('#recaptcha_forgot').length > 0){
                        grecaptcha.reset(forgot_reset);
                    }
                    jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                }
            }
        });
    });
    
    //Email Validation    
    function workreap_isValidEmailAddress(emailAddress) {
        var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
        return pattern.test(emailAddress);
	}    
	

	// Trigger Google Analytic Event
	var _gaEventSubmitTrigger = (type = '', url = '', args = '') => {
		if(type != null && url != null) {
			switch (type) {
				case 'signup':
					var element = document.createElement("button");
					element.setAttribute("type", 'button');
					element.setAttribute("id", 'registeration_url');
					element.setAttribute("onclick", "ga('send', {hitType: 'event',eventCategory: 'Form',eventAction: 'Registration URL',eventLabel: '"+_gaEventsURLPool(type, args)+"', eventValue:1});");
					document.body.appendChild(element);
					setTimeout(function(){ 
						var elem = document.getElementById('registeration_url');
						elem.click();
						elem.parentNode.removeChild(elem);
					}, 500);
					break;
				case 'signin':
					var element = document.createElement("button");
					element.setAttribute("type", 'button');
					element.setAttribute("id", 'signin_url');
					element.setAttribute("onclick", "ga('send', {hitType: 'event',eventCategory: 'Form',eventAction: 'Sign In',eventLabel: '"+_gaEventsURLPool(type, args)+"', eventValue:1});");
					document.body.appendChild(element);
					setTimeout(function(){ 
						var elem = document.getElementById('signin_url');
						elem.click();
						elem.parentNode.removeChild(elem);
					}, 500);
						break;
				case 'portfolio':
					var element = document.createElement("button");
					element.setAttribute("type", 'button');
					element.setAttribute("id", 'portfolio_url');
					element.setAttribute("onclick", "ga('send', {hitType: 'event',eventCategory: 'Form',eventAction: 'Portfolio',eventLabel: '"+_gaEventsURLPool(type, args)+"', eventValue:1});");
					document.body.appendChild(element);
					setTimeout(function(){ 
						var elem = document.getElementById('portfolio_url');
						elem.click();
						elem.parentNode.removeChild(elem);
					}, 500);
						break;
				case 'proposal':
					var element = document.createElement("button");
					element.setAttribute("type", 'button');
					element.setAttribute("id", 'proposal_url');
					element.setAttribute("onclick", "ga('send', {hitType: 'event', eventCategory: 'Form', eventAction: 'Project Proposal URL',eventLabel: '"+_gaEventsURLPool(type, args)+"', eventValue:1});");
					document.body.appendChild(element);
					setTimeout(function(){ 
						var elem = document.getElementById('proposal_url');
						elem.click();
						elem.parentNode.removeChild(elem);
					}, 500);
					break;
				case 'project':
					var element = document.createElement("button");
					element.setAttribute("type", 'button');
					element.setAttribute("id", 'project_post_url');
					element.setAttribute("onclick", "ga('send', {hitType: 'event', eventCategory: 'Form', eventAction: 'Project Post URL', eventLabel: '"+_gaEventsURLPool(type, args)+"', eventValue:1});");
					document.body.appendChild(element);
					setTimeout(function(){ 
						var elem = document.getElementById('project_post_url');
						elem.click();
						elem.parentNode.removeChild(elem);
					}, 500);
					break;
			}
		}
	};


	function _gaEventsURLPool(type = '', args = '') {
		let url_track = [];

		url_track['signup'] = scripts_vars.site_url+'/registration/'+args;
		url_track['signin'] = scripts_vars.site_url+'/signin/'+args;
		url_track['portfolio'] = scripts_vars.site_url+'/upload_portfolio';
		url_track['proposal'] = scripts_vars.site_url+'/submit_proposal';
		url_track['project'] = scripts_vars.site_url+'/post_project';

		return url_track[type];
	}
});


//facebook connect
(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "https://connect.facebook.net/en_US/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
 
  // initialize the facebook sdk

window.fbAsyncInit = function() {
	if( is_loggedin === 'false' ){
		FB.init({
		  appId      : fbapp_id,//'417766098693476',
		  cookie     : true,  // enable cookies to allow the server to access 
							  // the session
		  xfbml      : true,  // parse social plugins on this page
		  version    : 'v3.1' // The Graph API version to use for the call
		});
	}
}

// add event listener on the logout button
function facebookLogin() {
	jQuery('body').append(loader_html);
	FB.getLoginStatus(function(response) {
	   statusChangeCallback(response);
	});
}

//Status change callback
function statusChangeCallback(response) {
	 if(response.status === "connected") {
		fetchUserProfile();
	 } else{
		 // Logging the user to Facebook by a Dialog Window
		 facebookLoginByDialog();
	 }
}

//Fetch Profile Data
function fetchUserProfile() {
   FB.api('/me?fields=id,first_name,last_name,middle_name,picture,short_name,name,email,gender', function(response) { 
		var dataString = 'email=' + response.email +'&id=' + response.id + '&name=' + response.name + '&action=workreap_js_social_login';   
		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: dataString,
			dataType: "json",
			success: function (response) {
				jQuery('body').find('.wt-preloader-section').remove();
				if (response.type === 'success') {  
					jQuery('#loginpopup').modal('hide');
					if( typeof(response.html) != "undefined" && response.html !== null && response.html !== '' ) {
						jQuery('.modal-post-wrap').html(response.html);
						jQuery('#taskpopup').modal('show');
						jQuery('.wt-registration-content-model').html(response.html);
						jQuery('.wt-registration-parent-model').modal('show');
						var is_rtl  = scripts_vars.is_rtl;
						
						var config = {
								'.chosen-select'           : {rtl:is_rtl},
								'.chosen-select-deselect'  : {allow_single_deselect:true},
								'.chosen-select-no-single' : {disable_search_threshold:10},
								'.chosen-select-no-results': {no_results_text:scripts_vars.nothing},
								'.chosen-select-width'     : {width:"95%"}
						}

						for (var selector in config) {
							jQuery(selector).chosen(config[selector]);
						}
					} else {
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000 });
						window.location.reload();
					}
				} else {
					jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
				}
			}
		});
   });
}

//Facebook dialog
function facebookLoginByDialog() {
	jQuery('body').find('.wt-preloader-section').remove();
	FB.login(function(response) {
	   statusChangeCallback(response);

	}, {scope: 'public_profile,email'});
}

// logging out the user from Facebook
function facebookLogout() {
	FB.logout(function(response) {
	   statusChangeCallback(response);
	});
}

					