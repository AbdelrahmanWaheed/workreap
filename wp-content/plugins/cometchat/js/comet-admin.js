
var show_friends;
var bp_group_sync;
var auth_key;
var api_key;
var enable_mycred;
var mycred_url;

jQuery(document).ready(function() {

    jQuery(".tab-links").find("li").click(function(){
        jQuery(".menus").removeClass("active");
        jQuery(this).addClass("active");
        var rel=jQuery(this).data("rel");
        jQuery(".tab").hide();
        jQuery("#"+rel).show();
    });
    jQuery('#save').on('click', function(e) {
        show_friends = jQuery("input.show_friends[type=checkbox]:checked").val();

        if(show_friends == '' || typeof(show_friends) == 'undefined'){
            show_friends = "false";
        }else{
            show_friends = "true";
        }

        bp_group_sync = jQuery("input.bp_group_sync[type=checkbox]:checked").val();

        if(bp_group_sync == '' || typeof(bp_group_sync) == 'undefined'){
            bp_group_sync = "false";
        }else{
            bp_group_sync = "true";
        }

        data = {
            'action': 'cc_action',
            'api': 'cometchat_friend_ajax',
            'show_friends': show_friends,
            'bp_group_sync': bp_group_sync
        }

        jQuery.post(ajaxurl, data, function(response){
            jQuery("#success").html("<div class='updated'><p>Settings successfully saved!</p></div>");
            jQuery(".updated").fadeOut(3000);
        });
    });

    jQuery(".enable_mycred").change(function(){
        if(jQuery(".enable_mycred")[0].checked == true){
            jQuery("#cc_roles").show();
        }else{
            jQuery("#cc_roles").hide();
        }
    });

    jQuery("input.cc_auth_key[type=text], input.cc_api_key[type=text]").focus(function(){
        jQuery(this).parent().removeClass('invalid-input');
    });

    jQuery("[name=edit_credit]").on("click",function() {
        var id = this.id;
        var role = id.replace("cc_edit_credits_","");
        var creditToDeduct = jQuery("#creditToDeduct_"+role).val();
        var creditOnMessage = jQuery("#creditOnMessage_"+role).val();
        var creditToDeductAudio = jQuery("#creditToDeductAudio_"+role).val();
        var creditToDeductAudioOnMinutes = jQuery("#creditToDeductAudioOnMinutes_"+role).val();
        var creditToDeductVideo = jQuery("#creditToDeductVideo_"+role).val();
        var creditToDeductVideoOnMinutes = jQuery("#creditToDeductVideoOnMinutes_"+role).val();

        data = {
            'action': 'cc_action',
            'api' : 'update_credeits',
            'role': role,
            'creditToDeduct' : creditToDeduct,
            'creditOnMessage' : creditOnMessage,
            'creditToDeductAudio' : creditToDeductAudio,
            'creditToDeductAudioOnMinutes' : creditToDeductAudioOnMinutes,
            'creditToDeductVideo' : creditToDeductVideo,
            'creditToDeductVideoOnMinutes' : creditToDeductVideoOnMinutes
        }

        jQuery.post(ajaxurl, data, function(response){
            jQuery("#cc_update_credeits_role_"+role).html("<div class='updated'><p>Settings successfully saved!</p></div>");
            jQuery(".updated").fadeOut(3000);
        });


    });

    jQuery("#cc_update_credeits").on('click',function(e){
        enable_mycred = jQuery("input.enable_mycred[type=checkbox]:checked").val();

        if(enable_mycred == '' || typeof(enable_mycred) == 'undefined'){
            enable_mycred = "false";
        }else{
            enable_mycred = "true";
        }
        mycred_url = location.href;
        var postion = mycred_url.search("wp-admin");
        mycred_url = mycred_url.slice(0,postion-1);
        mycred_url = mycred_url.replace("https://","");
        data = {
            'action': 'cc_action',
            'api': 'cometchat_mycred_setting',
            'enable_mycred': enable_mycred,
            'mycred_url' : mycred_url
        }

        jQuery.post(ajaxurl, data, function(response){
            jQuery("#success_mycred").html("<div class='updated'><p>Settings successfully saved!</p></div>");
            jQuery(".updated").fadeOut(3000);
        });

    });
    jQuery(".cc_role").on('click',function() {
        var id = this.id;
        if(jQuery("#cc_content_"+id).is(":visible")){
            jQuery("#cc_content_"+id).hide();
        }else{
            jQuery("#cc_content_"+id).show();
        }

    });

    jQuery('#update_auth_key').on('click', function(e) {
        var validate = true;
        auth_key = jQuery("input.cc_auth_key[type=text]").val();
        api_key  = jQuery("input.cc_api_key[type=text]").val();

        if(auth_key == '') {
            jQuery("input.cc_auth_key[type=text]").parent().addClass('invalid-input');
            validate = false;
        }
        if(api_key == '') {
            jQuery("input.cc_api_key[type=text]").parent().addClass('invalid-input');
            validate = false;
        }
        if(!validate) {
            return;
        }
        data = {
            'action': 'cc_action',
            'api': 'cometchat_update_auth_ajax',
            'cc_auth_key': auth_key,
            'cc_api_key': api_key
        }
        jQuery.post(ajaxurl, data, function(response){
            jQuery("#success_auth").html("<div class='updated'><p>Settings updated successfully!</p></div>");
            jQuery(".updated").fadeOut(3000);
        });
        if(jQuery(this).attr('level') == 'init') {
            cometGoSettings();
        }
    });


});
