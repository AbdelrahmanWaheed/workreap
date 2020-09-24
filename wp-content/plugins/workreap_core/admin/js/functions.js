"use strict";
jQuery(document).ready(function($) {
	
	//Resolve Dispute Ajax
	jQuery(document).on('click', '.resolve-dispute-btn', function(event) {
		event.preventDefault();
		var _this = jQuery(this);
		var user_id     			= jQuery("input[name='user_id']:checked").val();
		var freelancer_msg 			= jQuery("#freelancer_msg"). val();
		var employer_msg 			= jQuery("#employer_msg"). val();
		var proj_serv_id			= _this.data('proj-serv-id');
		var dispute_id				= _this.data('dispute-id');
		var freelancer_id			= _this.data('freelancer-id');
		var employer_id				= _this.data('employer-id');
		var dispute_project_id		= _this.data('dispute-project-id');
		var feedback				= jQuery('#fw-option-feedback').val();
		
		jQuery('#TB_ajaxContent').append('<div class="inportusers">'+localize_vars.spinner+'</div>');
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			dataType:"json",
			data: {
				freelancer_id: freelancer_id,
				employer_id: employer_id,
				dispute_project_id: dispute_project_id,
				user_id: user_id,
				freelancer_msg: freelancer_msg,
				employer_msg: employer_msg,
				proj_serv_id : proj_serv_id,
				dispute_id : dispute_id,
				feedback : feedback,
				action	   : 'workreap_resolve_dispute',
			},
			success: function(response) {
				jQuery('#TB_window').find('.inportusers').remove();
				if((freelancer_msg == '' || employer_msg == '') && response.type == 'error') {
					jQuery.sticky(localize_vars.add_message, {classList: 'danger', speed: 200, autoclose: 5000});
					return false;
				} else if(response.type == 'success') {
					jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
					window.location.reload();
				}
			}
		});
	});

	//collapse 
	jQuery(document).on('click', '.wt-historycontentcol .collapsed .wt-dateandmsg', function(event) {
		event.preventDefault();
		var _this 	= jQuery(this);
		if( _this.next('.wt-historydescription').hasClass('messageactive') ){
			_this.next('.wt-historydescription').removeClass('messageactive').hide();
		} else{
			_this.next('.wt-historydescription').addClass('messageactive').show();
		}
	});

	//Save settings
	jQuery(document).on('click', '.save-data-settings', function(event) {
		event.preventDefault();
		var serialize_data = jQuery('.save-settings-form').serialize();
		var dataString = serialize_data + '&action=workreap_save_theme_settings';
		
		var _this = jQuery(this);
		jQuery('.wt-featurescontent').append('<div class="inportusers">'+localize_vars.spinner+'</div>');
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			dataType:"json",
			data: dataString,
			success: function(response) {
				jQuery('.wt-featurescontent').find('.inportusers').remove();
				jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
				window.location.reload();
			}
		});

    });

	//veryfy profiles
	jQuery(document).on('click', '.do_verify_user', function() {
		var _this 	= jQuery(this);
		var _type		= _this.data('type'); 
		
		if( _type === 'reject' ){
			var localize_title = localize_vars.reject_account;
			var localize_vars_message = localize_vars.reject_account_message;
		}else{
			var localize_title = localize_vars.approve_account;
			var localize_vars_message = localize_vars.approve_account_message;
		}
		 jQuery.confirm({
			'title': localize_title,
			'message': localize_vars_message,
			'buttons': {
				'Yes': {
					'class': 'blue',
					'action': function () {
						var _id			= _this.data('id'); 
						var _type		= _this.data('type'); 
						var dataString = 'type='+_type+'&id='+_id+'&action=workreap_approve_profile';
						jQuery("#linked_profile .inside").append('<div class="inportusers">'+localize_vars.spinner+'</div>');
						jQuery.ajax({
							type: "POST",
							url: ajaxurl,
							dataType:"json",
							data: dataString,
							success: function(response) {
								jQuery('.inportusers').remove();
								if( response.type === 'success' ){
									jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
									window.location.reload();
								} else{
									jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
								}
							}
						});
				
						return false;
					}
				},
				'No': {
					'class': 'gray',
					'action': function () {
						return false;
					}	// Nothing to do in this case. You can as well omit the action property.
				}
			}
		});
    });
	
	//Approve Project
	jQuery(document).on('click', '.do_approve_post', function() {
		var _this 	= jQuery(this);
		var _post	= _this.data('post'); 
		var _id		= _this.data('id'); 
		var _type	= _this.data('type'); 
		
		if( _type === 'project' ){
			var localize_title = localize_vars.approve_project;
			var localize_vars_message = localize_vars.approve_project_message;
		}else{
			var localize_title = localize_vars.approve_service;
			var localize_vars_message = localize_vars.approve_service_message;
		}
		
		 jQuery.confirm({
			'title': localize_title,
			'message': localize_vars_message,
			'buttons': {
				'Yes': {
					'class': 'blue',
					'action': function () {
						var dataString = 'type='+_type+'&post_id='+_post+'&id='+_id+'&action=workreap_approve_post';
						jQuery("#linked_profile .inside").append('<div class="inportusers">'+localize_vars.spinner+'</div>');
						jQuery.ajax({
							type: "POST",
							url: ajaxurl,
							dataType:"json",
							data: dataString,
							success: function(response) {
								jQuery('.inportusers').remove();
								if( response.type === 'success' ){
									jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
									window.location.reload();
								} else{
									jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
								}
							}
						});
				
						return false;
					}
				},
				'No': {
					'class': 'gray',
					'action': function () {
						return false;
					}	// Nothing to do in this case. You can as well omit the action property.
				}
			}
		});
    });
	
	//import dummy users
	jQuery(document).on('click', '.doc-import-users', function() {
		 jQuery.confirm({
			'title': localize_vars.import,
			'message': localize_vars.import_message,
			'buttons': {
				'Yes': {
					'class': 'blue',
					'action': function () {
						var dataString = 'action=workreap_import_users';
						var $this = jQuery(this);
						jQuery('#import-users').append('<div class="inportusers">'+localize_vars.spinner+'</div>');
						jQuery.ajax({
							type: "POST",
							url: ajaxurl,
							dataType:"json",
							data: dataString,
							success: function(response) {
								jQuery('#import-users').find('.inportusers').remove();
								jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
							}
						});
				
						return false;
					}
				},
				'No': {
					'class': 'gray',
					'action': function () {
						return false;
					}	// Nothing to do in this case. You can as well omit the action property.
				}
			}
		});
	});
	
	//Update mailchimp list
	jQuery(document).on('click', '.wt-latest-mailchimp-list', function(event) {
		event.preventDefault();
		var dataString = '&action=workreap_mailchimp_array';
		
		var _this = jQuery(this);
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			dataType:"json",
			data: dataString,
			success: function(response) {
				jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
				window.location.reload();
			}
		});
	});
	
});
	
/* ---------------------------------------
 Confirm Box
 --------------------------------------- */
(function ($) {

		jQuery.confirm = function (params) {
	
			if (jQuery('#confirmOverlay').length) {
				// A confirm is already shown on the page:
				return false;
			}
	
			var buttonHTML = '';
			jQuery.each(params.buttons, function (name, obj) {
	
				// Generating the markup for the buttons:
				if( name == 'Yes' ){
					name	= localize_vars.yes;
				} else if( name == 'No' ){
					name	= localize_vars.no;
				} else{
					name	= name;
				}
		
				buttonHTML += '<a href="#" class="button ' + obj['class'] + '">' + name + '<span></span></a>';
	
				if (!obj.action) {
					obj.action = function () {
					};
				}
			});
	
			var markup = [
				'<div id="confirmOverlay">',
				'<div id="confirmBox">',
				'<h1>', params.title, '</h1>',
				'<p>', params.message, '</p>',
				'<div id="confirmButtons">',
				buttonHTML,
				'</div></div></div>'
			].join('');
	
			jQuery(markup).hide().appendTo('body').fadeIn();
	
			var buttons = jQuery('#confirmBox .button'),
					i = 0;
	
			jQuery.each(params.buttons, function (name, obj) {
				buttons.eq(i++).click(function () {
	
					// Calling the action attribute when a
					// click occurs, and hiding the confirm.
	
					obj.action();
					jQuery.confirm.hide();
					return false;
				});
			});
		}
	
		jQuery.confirm.hide = function () {
			jQuery('#confirmOverlay').fadeOut(function () {
				jQuery(this).remove();
			});
		}
	
})(jQuery);

/*
	Sticky v2.1.2 by Andy Matthews
	http://twitter.com/commadelimited

	forked from Sticky by Daniel Raftery
	http://twitter.com/ThrivingKings
*/
(function ($) {

	jQuery.sticky = jQuery.fn.sticky = function (note, options, callback) {

		// allow options to be ignored, and callback to be second argument
		if (typeof options === 'function') callback = options;

		// generate unique ID based on the hash of the note.
		var hashCode = function(str){
				
				var hash = 0,
					i = 0,
					c = '',
					len = str.length;
				if (len === 0) return hash;
				for (i = 0; i < len; i++) {
					c = str.charCodeAt(i);
					hash = ((hash<<5)-hash) + c;
					hash &= hash;
				}
				return 's'+Math.abs(hash);
			},
			o = {
				position: 'top-right', // top-left, top-right, bottom-left, or bottom-right
				speed: 'fast', // animations: fast, slow, or integer
				allowdupes: true, // true or false
				autoclose: 5000,  // delay in milliseconds. Set to 0 to remain open.
				classList: '' // arbitrary list of classes. Suggestions: success, warning, important, or info. Defaults to ''.
			},
			uniqID = hashCode(note), // a relatively unique ID
			display = true,
			duplicate = false,
			tmpl = '<div class="sticky border-POS CLASSLIST" id="ID"><span class="sticky-close"></span><p class="sticky-note">NOTE</p></div>',
			positions = ['top-right', 'top-center', 'top-left', 'bottom-right', 'bottom-center', 'bottom-left'];

		// merge default and incoming options
		if (options) jQuery.extend(o, options);

		// Handling duplicate notes and IDs
		jQuery('.sticky').each(function () {
			if (jQuery(this).attr('id') === hashCode(note)) {
				duplicate = true;
				if (!o.allowdupes) display = false;
			}
			if (jQuery(this).attr('id') === uniqID) uniqID = hashCode(note);
		});

		// Make sure the sticky queue exists
		if (!jQuery('.sticky-queue').length) {
			jQuery('body').append('<div class="sticky-queue ' + o.position + '">');
		} else {
			// if it exists already, but the position param is different,
			// then allow it to be overridden
			jQuery('.sticky-queue').removeClass( positions.join(' ') ).addClass(o.position);
		}

		// Can it be displayed?
		if (display) {
			// Building and inserting sticky note
			jQuery('.sticky-queue').prepend(
				tmpl
					.replace('POS', o.position)
					.replace('ID', uniqID)
					.replace('NOTE', note)
					.replace('CLASSLIST', o.classList)
			).find('#' + uniqID)
			.slideDown(o.speed, function(){
				display = true;
				// Callback function?
				if (callback && typeof callback === 'function') {
					callback({
						'id': uniqID,
						'duplicate': duplicate,
						'displayed': display
					});
				}
			});

		}

		// Listeners
		jQuery('.sticky').ready(function () {
			// If 'autoclose' is enabled, set a timer to close the sticky
			if (o.autoclose) {
				jQuery('#' + uniqID).delay(o.autoclose).fadeOut(o.speed, function(){
					// remove element from DOM
					jQuery(this).remove();
				});
			}
		});

		// Closing a sticky
		jQuery('.sticky-close').on('click', function () {
			jQuery('#' + jQuery(this).parent().attr('id')).dequeue().fadeOut(o.speed, function(){
				// remove element from DOM
				jQuery(this).remove();
			});
		});

	};
})(jQuery);