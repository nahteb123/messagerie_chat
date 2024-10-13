<?php
	include_once("./base64URL.php");
	$userData = json_decode(file_get_contents("./users.json"),true);
	$response = array();
	$token;
	if(array_key_exists($_POST["pseudo"],$userData)) 
	{
		if(hash_hmac("SHA256",$_POST["mdp"],"wishcordROX") == $userData[$_POST["pseudo"]]["mdp"]){
			$currentUser = $userData[$_POST["pseudo"]];
			$currentUser["mdp"] = "masked";
			$response["token"] = encodeToken($currentUser,["pseudo" => $_POST["pseudo"]]);
		}
		else{
			$response["errMsg"] = "Couple login/mdp invalide";
		}
	}
	else{
		$response["errMsg"] = "Couple login/mdp invalide";
	}
	echo json_encode($response);
	
?>