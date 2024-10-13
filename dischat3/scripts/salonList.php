<?php
	$pseudo = $_POST["pseudo"];
	$usersData = json_decode(file_get_contents("./users.json"),true);
	$salonList;
	if(array_key_exists($pseudo,$usersData)){
		$salonList = $usersData[$pseudo]["salons"];
	}
	else{
		$newUser = array();
		$newUser["displayName"] = $pseudo;
		$newUser["salons"] = array();
		$usersData[$pseudo] = $newUser;
		file_put_contents("./users.json",json_encode($usersData));
		$salonList = array();
	}
	echo json_encode($salonList);
?>
	