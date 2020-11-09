<?php

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'cometchat.php');

if(!empty($_REQUEST['action'])){
	$action = $_REQUEST['action'];
}

if($action == 'deductMycredPoints'){
	deductMycredPoints();
}
if($action = 'getCreditsToDeductForMyCred'){
	getCreditsToDeductForMyCred();
}
if($action = 'getMyCredCredits'){
	getMyCredCredits();
}



function getMyCredCredits($userid = 0){
	$data = json_decode(file_get_contents('php://input'));
	if(property_exists($data,'mobileapp') && $data->mobileapp == 1){
		if(property_exists($data,'UID')){
			$userexists = get_userdata($data->UID);
			if($userexists){
				$balance = mycred_get_users_balance($data->UID);
			}else{
				$balance = "Invalid UID";
			}
			$credits = array("credits" => strval($balance));
			echo json_encode($credits);
			exit();
		}
	}
	$balance = array("credits" => 0);
	if(!empty($userid)){
		$balance = array("credits" => mycred_get_users_balance($userid));
	}else{
		$balance = "userid not exist";
	}
	return $balance;
}



function deductMycredPoints(){

	$deductionInterval = 0;
	$result = [];
	$userid = 0;
	$time = 0;
	$id = 0;
	$credits = array("credits" => 0);
	$amount = 0;
	$message = "";
	$interval = "minute";
	$creditToDeduct = "";
	$timeCounter = "";
	$data = json_decode(file_get_contents('php://input'));

	if(!empty($data->UID)){
		$userid = $data->UID;
	}
	if(!empty($data->role)){
		$role = $data->role;
	}
	if(!empty($data->type)){
		$type = $data->type;
	}
	if(!empty($data->to)){
		$to = $data->to;
	}
	if(!empty($data->isGroup)){
		$isGroup = $data->isGroup;
	}

	if($isGroup == "true"){
		$id =  $userid;
	}else{
		$id = $to;
	}
	if(!empty(get_option("cc_".$role))){
		$creditToDeduct = unserialize(get_option("cc_".$role));
	}else{
		$creditToDeduct["creditsinfo"] = array("creditsToDeduct" => 0,"deductionInterval" => 0,"creditsToDeductOnMessage" => 0,"messageCount" => 0);
	}

	if(property_exists($data, 'name') && $data->name == "broadcast"){
		$creditsinfo["creditsinfo"] = array("success" => false, "errorcode" => "2","message" => "The Credit Deduction is not enabled for the broadcast plugin for the  role");
		$creditsinfo["balance"] = getMyCredCredits($userid);
		echo json_encode($creditsinfo);
		exit();
	}

	if($data->name == "avchat"){
		$name = 'Video';
	}elseif($data->name == "audiochat"){
		$name = "Audio";
	}

	if(array_key_exists("creditToDeduct".$name."OnMinutes",$creditToDeduct)){
		$deductionInterval = $creditToDeduct["creditToDeduct".$name."OnMinutes"];
	}
	if(array_key_exists("creditToDeduct".$name,$creditToDeduct)){
		$amount = $creditToDeduct["creditToDeduct".$name];
	}

	if($data->name == "core"){
		$name = "Text";
		$interval = "message";
		$amount = $creditToDeduct["creditToDeduct"];
		$deductionInterval = $creditToDeduct["creditOnMessage"];
		if(!empty(get_transient('messageCounter'.$id))){
			$messageCounter[$type.$name.$id.$isGroup] = get_transient('messageCounter'.$id);
		}else{
			$messageCounter[$type.$name.$id.$isGroup] = 0;
		}
		$messageCounter[$type.$name.$to.$isGroup]++;
	}


	if(!empty(get_transient('timer'.$id))){
		$timeCounter = get_transient('timer'.$id);
	}else{
		$timeCounter[$type.$name.$id.$isGroup] = 0;
	}
	$message = "Deducted ".$amount." credits for the ".$name." Chat for the ".$deductionInterval." number of ".$interval;
	$credits = getMyCredCredits($userid);
	if($amount == 0 || $deductionInterval == 0){
		$result["success"] = false;
		$result['errorcode'] = '2';
		$result['message'] = 'The Credit Deduction is not enabled for the '.$name.' '.$type.' for the '.$role.' role';
		$result['balance'] = $credits;
	}elseif($timeCounter[$type.$name.$id.$isGroup]>time()-$deductionInterval*60 && $name != "Text"){
			$result["success"] = false;
			$result['errorcode'] = '4';
			$result['message'] = 'Already deducted '.$creditsToDeduct.' credits for the '.$type.' '.$name.' for the interval of '.$deductionInterval.' minutes';
			$result['balance'] = $credits;
	}elseif(!empty($credits["credits"]) && $credits["credits"] >= $amount && $name != "Text"){
		$timeCounter[$type.$name.$id.$isGroup] = time();
		set_transient('timer'.$id,$timeCounter ,60 * 24);
		$balance = mycred_subtract( 'Message', $userid, $amount, $message );
		$result["success"] = true;
		$result["message"] = $message;
	}elseif($messageCounter[$type.$name.$to.$isGroup]>1 && $messageCounter[$type.$name.$to.$isGroup]<=$deductionInterval && $name == "Text"){
		if($messageCounter[$type.$name.$to.$isGroup] == $deductionInterval){
			$messageCounter[$type.$name.$to.$isGroup] = 0;
		}
		set_transient('messageCounter'.$id,$messageCounter[$type.$name.$id.$isGroup],60 * 24);
		$result["success"] = false;
		$result['errorcode'] = '4';
		$result['message'] = 'Already deducted '.$creditsToDeduct.' credits for the Text Chat on number of messages for the '.$deductionInterval.' message';
		$result['balance'] = $credits;
	}elseif(!empty($credits["credits"]) && $credits["credits"] >= $amount && $name == "Text" && ($messageCounter[$type.$name.$id.$isGroup] == 1 || $messageCounter[$type.$name.$id.$isGroup] == 0)){
		if($deductionInterval > 1){
			set_transient('messageCounter'.$id,$messageCounter[$type.$name.$id.$isGroup],60 * 24);
		}
		$balance = mycred_subtract( 'Message', $userid, $amount, $message );
		$result["success"] = true;
		$result["message"] = $message;
	}else{
		$result["success"] = false;
		$result["message"] = "You do not have sufficient credits to use this feature.";
		$result['errorcode'] = 3;
		$result['balance'] = $credits;
	}
	echo json_encode($result);
	exit();
}



function getCreditsToDeductForMyCred($params=array()){
		$data = json_decode(file_get_contents('php://input'));
		if(property_exists($data, 'roles')){
			$response = array();
			$roles = $data->roles;
			$details = !empty(get_option("cc_".$roles)) ? unserialize(get_option("cc_".$roles)) : "";
				if(!empty($details)){
					$response['audiochat'] = array('name' => 'Audio Chat', 'credit' => array('creditsToDeduct' => $details['creditToDeductAudio'] , 'deductionInterval'=> $details['creditToDeductAudioOnMinutes']));
					$response['avchat'] = array('name' => 'Audio/Video Chat', 'credit' => array('creditsToDeduct' => $details['creditToDeductVideo'] , 'deductionInterval'=> $details['creditToDeductVideoOnMinutes']));
					}else{
						$response['audiochat'] = array('name' => 'Audio Chat', 'credit' => array('creditsToDeduct' => 0 , 'deductionInterval'=> 0));
						$response['avchat'] = array('name' => 'Audio/Video Chat', 'credit' => array('creditsToDeduct' => 0 , 'deductionInterval'=> 0));
					}
				echo json_encode($response);
				exit();
		}
		if(property_exists($data, 'role')){
			$role = (!empty($data->role)) ? $data->role : "";
		}
		if(property_exists($data, 'UID')){
			$id = (!empty($data->UID)) ? $data->UID : 0;
		}
		if(property_exists($data, 'type')){
			$type = (!empty($data->type)) ? $data->type : "";
		}
		$creditsinfo['balance'] =  getMyCredCredits($id);
		if(property_exists($data, 'name') && $data->name == "avchat"){
			$name = "Video";
		}
		if(property_exists($data, 'name') && $data->name == "audiochat"){
			$name = "Audio";
		}
		if(property_exists($data, 'name') && $data->name == "core"){
			$name = $data->name;
		}

		if(property_exists($data, 'name') && $data->name == "broadcast"){
			$creditsinfo["creditsinfo"] = array("creditsToDeduct" => 0,"deductionInterval" => 0);
			echo json_encode($creditsinfo);
			exit();
		}

		$creditsinfo["creditsinfo"] = array();
		$rolefeature = get_option("cc_".$role);
		if(!empty($rolefeature)){
			$rolefeature = unserialize($rolefeature);
		}else{
			$creditsinfo["creditsinfo"] = array("creditsToDeduct" => 0,"deductionInterval" => 0,"creditsToDeductOnMessage" => 0,"messageCount" => 0);
		}

		if($type == "core" && $name == "core"){
			if(property_exists($data, 'balance') && $data->balance == 1){
				$result["balance"] = getMyCredCredits($id);
			}			
			if(!empty($rolefeature)){
				$result["creditsinfo"] = array(
					"creditsToDeduct" => "0",
					"deductionInterval" => "0",
					"creditsToDeductOnMessage" => !empty($rolefeature["creditToDeduct"]) ? $rolefeature["creditToDeduct"] : "0",
					"messageCount" => !empty($rolefeature["creditOnMessage"]) ? $rolefeature["creditOnMessage"] : "0"
				);
			}else{
				$result["creditsinfo"] = array("creditsToDeduct" => 0,"deductionInterval" => 0,"creditsToDeductOnMessage" => 0,"messageCount" => 0);
			}
			echo json_encode($result);
			exit();
		}

		if(!empty($role) && !empty($type) && !empty($name)){
			if(!empty($rolefeature) && isset( $rolefeature["creditToDeduct".$name])){
				$creditsinfo["creditsinfo"]['creditsToDeduct'] 			 =  empty($rolefeature["creditToDeduct".$name]) ? 0 : $rolefeature["creditToDeduct".$name];
				$creditsinfo["creditsinfo"]['deductionInterval'] 		 =  empty($rolefeature["creditToDeduct".$name."OnMinutes"]) ? 0 : $rolefeature["creditToDeduct".$name."OnMinutes"];

				$creditsinfo["creditsinfo"]['creditsToDeductOnMessage']	 =  0;
				$creditsinfo["creditsinfo"]['messageCount'] 			 =  0;
			}
		}

		echo json_encode($creditsinfo);
		exit();
}

?>