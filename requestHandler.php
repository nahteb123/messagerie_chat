<?php
	include_once("./scripts/base64URL.php");
	$routes = [
		"login" => "doLogin.php",
		"majMsg" => "majMsg.php",
		"subscribe" => "subscribe.php",
		"listSalon" => "salonList.php",
		"send" => "sendMsg.php",
		"register" => "register.php",
		"invite" => "inviteUser.php",
		"listUsersInSalon" => "listUserInSalon.php"
	];
	$routePath ="scripts/";
	$response = array();
	$reqData;
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$reqData = $_POST;
	}
	else{
		$reqData = $_GET;
	}
	
	$currentOperation = $reqData["operation"];
	$authorized = false;
	$tokenData;
	if(!array_key_exists($currentOperation,$routes)){
		echo json_encode(["errMsg" => "Operation non supportée"]);
		exit;
	}
	else{
		if($currentOperation == "login" || $currentOperation == "register"){
			$authorized = true;
		}
		else if(isset($reqData["token"])){
			$authorized = validateToken($reqData["token"]);
			$tokenData = decodeToken($reqData["token"]);
		}
	}
	
	if($authorized){
		$respTemp = handleRequest(resolveUrl($routePath . $routes[$currentOperation]),$reqData,"POST");
		$response["data"] = json_decode($respTemp);
	}
	
	if(!isset($response["errMsg"]) && $authorized && $currentOperation != "login" && $currentOperation != "register"){
		$response["token"] = encodeToken($response["data"],$tokenData);
	}
	else if($currentOperation != "login" && $currentOperation != "register"){
		echo json_encode(["errMsg" => "Token invalide","auth" => $authorized]);
		exit;
	}
	echo json_encode($response);
	
	function resolveUrl($url){
		$explodedBaseUrl = explode("/","http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
		$argCount = count($explodedBaseUrl) - 1;
		if(count(explode("../",$url,2) ) > 1){
			$argCount--;
		}
		$baseUrl ="";
		for($i=0;$i<$argCount;$i++){
			$baseUrl = $baseUrl.$explodedBaseUrl[$i]."/";
		}
		$baseUrl = $baseUrl.$url;
		return $baseUrl;
	}
	
	function handleRequest($url, $data, $method){
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => $method,
				'content' => http_build_query($data)
			)
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		return $result;
	}
?>