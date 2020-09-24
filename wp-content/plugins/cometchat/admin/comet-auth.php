<?php
	wp_enqueue_style("ccadmin", dirname(plugin_dir_url( __FILE__ )).'/css/comet-auth.css');
	wp_enqueue_script("ccevent", dirname(plugin_dir_url( __FILE__ )).'/js/event.js');
	wp_enqueue_script("comet-admin", dirname(plugin_dir_url( __FILE__ )).'/js/comet-admin.js');
?>

<!DOCTYPE html>
<html>
<head></head>
<body>
	<div class="cometchat">
		<div class="comet-locked-layout">
			<img class="cometchat-logo" src=<?php echo $cometchatAuthKey;?> />
		</div>
		<div class="comet-installation-successs">
			<div class="comet-content">
				<img class="cometchat-logo-image" src=<?php echo $cometchatLogo;?>>
	            <div id="cc_auth" class="tab">
		        	<div class="cc_auth_content">
						<h2>
							Enter Auth Key
						</h2>
						<p>
							<b>Note:</b> You can find your Auth Key in CometChat Admin Panel -> API Keys (top-right button)
						</p>
						<p style="margin-top: 20px;">
							<input type="text" class="cc_auth_key" name="cc_auth_key" id="auth_key_token" value="<?php echo get_option('cc_auth_key');?>" style="width: 60%;" placeholder="Enter Auth Key">
						</p>

						<h2>
							Enter API Key
						</h2>
						<p>
							<b>Note:</b> You can find your API Key in CometChat Admin Panel -> API Keys (top-right button)
						</p>
						<p style="margin-top: 20px;">
							<input type="text" class="cc_api_key" name="cc_api_key" id="api_key" value="<?php echo get_option('cc_api_key');?>" style="width: 60%;" placeholder="Enter API Key">
						</p>
		        	</div>
	        		<p style="margin-top: 20px;">
						<button type="submit" value="submit" class="button-primary" id ="update_auth_key" level="init">Update</button>
					</p>
		        	<div id = "success_auth" class = "successmsg"></div>
	            </div>
			</div>
		</div>
	</div>
</body>
</html>