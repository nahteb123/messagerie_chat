<?php
	$userData = json_decode(file_get_contents("./users.json"),true);
	$response = array();
	$newPseudo = $_POST["pseudo"];
	$mdp = $_POST["mdp"];
	
	if(!array_key_exists($_POST["pseudo"],$userData)) 
	{
		$user = array();
		$user["displayName"] = $newPseudo;
		$user["mdp"] = hash_hmac("SHA256",$mdp,"wishcordROX");
		$user["salons"] = array();
		$userData[$newPseudo] = $user;
		file_put_contents("./users.json",json_encode($userData));
		$response["msg"] = "Inscription réussie, vous pouvez vous connecter";
	}
	else{
		$response["msg"] = "Cet utilisateur existe déjà, choisissez un autre pseudo";
	}
	
	echo json_encode($response);
	
?>