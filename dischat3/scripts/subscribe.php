<?php
	$salon = $_POST["salon"];
	$pseudo = $_POST["pseudo"];
	if(!file_exists("../Salons/".strtolower($salon))){
		mkdir("../Salons/".strtolower($salon));
	}
	$userData = json_decode(file_get_contents("./users.json"),true);
	if(!in_array($salon,$userData[$pseudo]["salons"])){
		$userData[$pseudo]["salons"][] = strtolower($salon);
		file_put_contents("./users.json",json_encode($userData));
	}
?>