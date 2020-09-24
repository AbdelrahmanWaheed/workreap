<?php
wp_enqueue_style("ccadmin", dirname(plugin_dir_url( __FILE__ )).'/css/comet-admin.css');
wp_enqueue_script("ccevent", dirname(plugin_dir_url( __FILE__ )).'/js/event.js');
wp_enqueue_script("comet-admin", dirname(plugin_dir_url( __FILE__ )).'/js/comet-admin.js');

$isBuddyPressActive = '';
if(!is_plugin_active('buddypress/bp-loader.php')){
	$isBuddyPressActive = 'style="display:none;"';
}
$isMyCredActive = '';
if(!is_plugin_active('mycred/mycred.php')){
	$isMyCredActive = 'style="display:none;"';
}

?>
<!DOCTYPE html>
<html>
<head></head>
<body>
	<div class="tabs">
		<h1>CometChat Settings</h1>
		<ul class="tab-links" id = "submenu">
			<li data-rel="cc_adminpanel" class="active menus"><a href="#cc_adminpanel">Admin Panel</a></li>
			<li data-rel="cc_settings" class="menus"><a href="#cc_settings" <?php echo $isBuddyPressActive; ?>>BuddyPress Settings</a></li>
			<li data-rel="cc_auth" class="menus auth"><a href="#cc_auth">Authentication Settings</a></li>
			<li data-rel="cc_MyCred" class="menus"><a href="#cc_MyCred" <?php echo $isMyCredActive; ?>>MyCred Settings</a></li>
		</ul>

		<div class="tab-content">
			<div id="cc_adminpanel" class="tab active">
				<div class="cc_admin_content">
					<h2>
						CometChat Admin Panel
					</h2>
					<p>
						To Change the layout or further customize cometchat please visit admin panel.
					</p>
					<p>
						<b>Note: </b>If you are already logged in to CometChat client area, You will be redirected to CometChat Admin Panel
					</p>
					<p style="margin-top: 20px;">
						<button type="button" class="button-primary" onclick="cometGOPanel('<?php echo $adminpanelurl; ?>');">
							Launch Client Area
						</button>
					</p>
				</div>
			</div>

			<div id="cc_settings" class="tab">
				<p class="cc-go-para">
					Extend CometChat for BuddyPress!
				</p>
				<p>
					We’ve detected that you’re using BuddyPress. Here are some additional settings that you can configure:
				</p>
				<table cellspacing="1" style="margin-top:20px;">
					<tr style="margin-top:20px;">
						<td width="550" style="padding-top: 20px;">
							<p class="cc-go-para">
								Show only Friends in Contacts list?
							</p>
							<p>
								If you tick this option, then when a user logs in, he will be able to see only his friends in the Contacts list. Note that, friends are synchronized only after they login atleast once to your site (after adding CometChat).
							</p>
						</td>
						<td valign="top" style="padding-top: 30px;">
							<input type = "checkbox" class="show_friends" value="show_friends" name="show_friends" <?php if(get_option('show_friends') === 'true') echo 'checked="checked"';?> /> Yes
						</td>
					</tr>
					<tr style="margin-top:20px;">
						<td width="550" style="padding-top: 20px;">
							<p class="cc-go-para">
								Synchronize BuddyPress Groups with CometChat
							</p>
							<p>
								If you tick this option, we will create equivalent chat groups in CometChat and add only those users part of your BuddyPress Group to it.
							</p>
							<span class="cc-go-para">
								Note :
							</span>
							<span>
								If you are facing trouble in syncing old Buddypress Groups with CometChat. Please Deactivate the CometChat plugin and Activate again.
							</span>
						</td>
						<td valign="top" style="padding-top: 30px;">
							<input type = "checkbox" class="bp_group_sync" value="bp_group_sync" name="bp_group_sync" <?php if(get_option('bp_group_sync') === 'true') echo 'checked="checked"';?> /> Yes
							<td>
							</tr>
							<tr>
								<td style="padding-top: 20px;">
									<button type="submit" value = "submit" id = "save" class = "button-primary">Save Settings</button>
								</td>
							</tr>
						</table>
						<div id = "success" class = "successmsg"></div>
					</div>

					<div id="cc_auth" class="tab">
						<div class="cc_auth_content">
							<h2>
								Enter Auth Key
							</h2>
							<p>
								<b>Note:</b> You can find your Auth Key in CometChat Admin Panel -> API Keys (top-right button)
							</p>
							<p style="margin-top: 20px;">
								<input type="text" class="cc_auth_key" name="cc_auth_key" id="auth_key_token" value="<?php echo get_option('cc_auth_key');?>" style="width: 25%;" placeholder="Enter Auth Key">
							</p>

							<h2>
								Enter API Key
							</h2>
							<p>
								<b>Note:</b> You can find your API Key in CometChat Admin Panel -> API Keys (top-right button)
							</p>
							<p style="margin-top: 20px;">
								<input type="text" class="cc_api_key" name="cc_api_key" id="api_key" value="<?php echo get_option('cc_api_key');?>" style="width: 25%;" placeholder="Enter API Key">
							</p>

						</div>
						<p style="margin-top: 20px;">
							<button type="submit" value="submit" class="button-primary" id ="update_auth_key">Update</button>
						</p>
						<div id = "success_auth" class = "successmsg"></div>
					</div>

				<div id="cc_MyCred" class="tab">
					<div id="cc_mycred_settings" >
						<div id="cc_enable_mycred">
							<h2> Integrate MyCred with CometChat</h2><br>
							<h2 style="display: inline-block; width: 360px;"> Enable MyCred With CometChat           </h2>
							<input style="display: inline-block;" type = "checkbox" class="enable_mycred" value="enable_mycred" name="enable_mycred" <?php if(get_option('enable_mycred') === 'true') echo 'checked="checked"';?> /> Yes
						</div>
						<?php if(get_option('enable_mycred') === 'true') {  $style = "display:block;";  }else{ $style = "display:none;"; }
						?>

						<div id="cc_roles" style=<?php echo $style; ?>>
							<?php
							$roles = $wp_roles->get_names();
							foreach($roles as $value) {
								$role = $value;
								$role_data = unserialize(get_option("cc_".$value));
								$creditToDeduct = empty((int) $role_data['creditToDeduct']) ? 0 : $role_data['creditToDeduct'];
								$creditOnMessage = empty((int) $role_data['creditOnMessage']) ? 0 : $role_data['creditOnMessage'];
								$creditToDeductAudio = empty((int) $role_data['creditToDeductAudio']) ? 0 : $role_data['creditToDeductAudio'];
								$creditToDeductAudioOnMinutes = empty((int) $role_data['creditToDeductAudioOnMinutes']) ? 0 : $role_data['creditToDeductAudioOnMinutes'];
								$creditToDeductVideo = empty((int) $role_data['creditToDeductVideo']) ? 0 : $role_data['creditToDeductVideo'];
								$creditToDeductVideoOnMinutes = empty((int) $role_data['creditToDeductVideoOnMinutes']) ? 0 : $role_data['creditToDeductVideoOnMinutes'];
								?>
								<hr>
								<div class="cc_role" id=<?php echo $value; ?>>
									<h2><?php echo $value; ?></h2>
								</div>
								<div style="display: none;" id=<?php echo "cc_content_".$value ?>>
									<table cellspacing="1" style="margin-top:20px;">
										<tr style="margin-top:0;">
											<td width="200" style="padding-top: 20px;">
												<p>Text Chat (on messages)  Charge</p>
											</td>
											<td width="150" style="padding-top: 20px;">
												<input type="text" class="creditToDeduct" name="creditToDeduct" value="<?php echo $creditToDeduct; ?>" style="width: 93%;" id=<?php echo "creditToDeduct_".$role; ?>>
											</td>
											<td width="90" style="padding-top: 20px;">
												<p>credits for</p>
											</td>
											<td width="150" style="padding-top: 20px;">
												<input type="text" class="creditOnMessage" name="creditOnMessage" value="<?php echo $creditOnMessage;?>" style="width: 93%;" id=<?php echo "creditOnMessage_".$role; ?>>
												<td width="90" style="padding-top: 20px;">
													<p>Messages</p>
												</td>
											</td>
										</tr>
										<tr style="margin-top:0;">
											<td width="200" style="padding-top: 20px;">
												<p>Audio Chat  Charge</p>
											</td>
											<td width="150" style="padding-top: 20px;">
												<input type="text" class="creditToDeductAudio" name="creditToDeductAudio" value="<?php echo $creditToDeductAudio;?>" style="width: 93%;" id=<?php echo "creditToDeductAudio_".$role; ?>>
											</td>
											<td width="90" style="padding-top: 20px;">
												<p>credits every</p>
											</td>
											<td width="150" style="padding-top: 20px;">
												<input type="text" class="creditToDeductAudioOnMinutes" name="creditToDeductAudioOnMinutes" value="<?php echo $creditToDeductAudioOnMinutes; ?>" style="width: 93%;"width="90" style="padding-top: 20px;" id=<?php echo "creditToDeductAudioOnMinutes_".$role; ?>>
											</td>
											<td width="90" style="padding-top: 20px;">
												<p>Minutes</p>
											</td>
										</tr>
										<tr style="margin-top:0;">
											<td width="200" style="padding-top: 20px;">
												<p>Audio/Video Chat  Charge</p>
											</td>
											<td width="150" style="padding-top: 20px;">
												<input type="text" class="creditToDeductVideo" name="creditToDeductVideo"  value="<?php echo $creditToDeductVideo; ?>" style="width: 93%;" id=<?php echo "creditToDeductVideo_".$role; ?>>
											</td>
											<td width="90" style="padding-top: 20px;">
												<p>credits every</p>
											</td>
											<td width="150" style="padding-top: 20px;">
												<input type="text" class="creditToDeductVideoOnMinutes" name="creditToDeductVideoOnMinutes" value="<?php echo  $creditToDeductVideoOnMinutes; ?>" style="width: 93%;" id=<?php echo "creditToDeductVideoOnMinutes_".$role; ?>>
											</td>
											<td width="90" style="padding-top: 20px;">
												<p>Minutes</p>
											</td>
										</tr>
										<tr>
											<td width="90" style="padding-top: 20px;">
												<div type="submit" value="submit" class="button-primary" name="edit_credit" id=<?php echo "cc_edit_credits_".$value; ?>>Update Credits					</div>
												<div id=<?php echo "cc_update_credeits_role_".$role; ?>></div>
												</td>
											</tr>
										</table>
									</div>
								</hr>
							<?php	} ?>
						</div>
						<div>
							<hr>
							<button type="submit" value = "submit" id = "cc_update_credeits" class = "button-primary">Save Settings</button>
							<div id="success_mycred">
							</div>
						</div>
					</div>
			</div>
		</div>
	</div>
</body>
</html>