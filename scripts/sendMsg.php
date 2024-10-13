<?php
	$salon = $_POST["selectedChat"];
	$filePath = "../Salons/".$salon."/chat.str";
	if(!file_exists($filePath)){
		$monTab = array();
		file_put_contents($filePath,serialize($monTab));
	}
	
	$monChat = unserialize(file_get_contents($filePath));
	$monMessage = [
		"date" => date("d-m-Y H:i:s"),
		"message" => $_POST["message"],
		"pseudo" => $_POST["pseudo"]
	];
	$monChat[] = $monMessage;
	file_put_contents($filePath,serialize($monChat));
?>