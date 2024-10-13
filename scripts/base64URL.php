<?php
/**
 * Encode data to Base64URL
 * @param string $data
 * @return boolean|string
 */
function base64url_encode($data)
{
  // First of all you should encode $data to Base64 string
  $b64 = base64_encode($data);

  // Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
  if ($b64 === false) {
    return false;
  }

  // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
  $url = strtr($b64, '+/', '-_');

  // Remove padding character from the end of line and return the Base64URL result
  return rtrim($url, '=');
}

/**
 * Decode data from Base64URL
 * @param string $data
 * @param boolean $strict
 * @return boolean|string
 */
function base64url_decode($data, $strict = false)
{
  // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
  $b64 = strtr($data, '-_', '+/');

  // Decode Base64 string and return the original data
  return base64_decode($b64, $strict);
}

function decodeToken($token){
	return json_decode(base64url_decode(explode(".",$token)[1],true));
}

function encodeToken($data, $tokenData = []){
	$tokenHeader = [
		"alg" => "SHA256",
		"typ" => "JWT"
	];
	foreach($tokenData as $key => $value){
		$data[$key] = $value;
	}
	$data["dateValid"] = time() + 60 * 15;
	$token = base64url_encode(json_encode($tokenHeader)) . "." . base64url_encode(json_encode($data));
	return $token . "." . base64url_encode(hash_hmac("SHA256",$token,"wishcordROX"));
}

function validateToken($token){
	$tokenParts = explode(".",$token);
	$tokenTime = json_decode(base64url_decode($tokenParts[1]),true)["dateValid"];
	return base64url_encode(hash_hmac("SHA256",$tokenParts[0] . "." . $tokenParts[1],"wishcordROX")) == $tokenParts[2]
			&& $tokenTime > time();
}