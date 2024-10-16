<?php 

//file_put_contents('debug.log', 'inviteUser.php appelé' . PHP_EOL, FILE_APPEND);
include_once("./base64URL.php");
$usersFile = "./users.json"; // Spécifie le chemin vers ton fichier users.json


if ($_SERVER['REQUEST_METHOD'] != "POST") {
    echo json_encode(["errMsg" => "Méthode non autorisée"]);
    exit;
}


$salon = isset($_POST['salon']) ? $_POST['salon'] : null;
$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
//file_put_contents('debug.log', "Salon : $salon, User ID : $user_id" . PHP_EOL, FILE_APPEND);

if (!$salon || !$user_id) {
    echo json_encode(["errMsg" => "Salon ou utilisateur manquant"]);
    exit;
}

try {
    if (!file_exists($usersFile)) {
        echo json_encode(["errMsg" => "Fichier des utilisateurs non trouvé"]);
        exit;
    }

    $fileContent = file_get_contents($usersFile);
    //file_put_contents('debug.log', "Contenu brut de users.json : " . $fileContent . PHP_EOL, FILE_APPEND);

    $usersData = json_decode($fileContent, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        file_put_contents('debug.log', "Erreur de décodage JSON : " . json_last_error_msg() . PHP_EOL, FILE_APPEND);
        echo json_encode(["errMsg" => "Erreur de formatage du fichier JSON"]);
        exit;
    }

    if (!isset($usersData[$user_id]) || $user_id === "" || $user_id === "null") {
        echo json_encode(["errMsg" => "Utilisateur non trouvé ou ID invalide"]);
        exit;
    }

    if (!in_array($salon, $usersData[$user_id]['salons'])) {
        $usersData[$user_id]['salons'][] = $salon;
    } else {
        echo json_encode(["errMsg" => "L'utilisateur est déjà dans ce salon"]);
        exit;
    }

    file_put_contents($usersFile, json_encode($usersData, JSON_PRETTY_PRINT));

    echo json_encode(["data" => ["msg" => "Invitation envoyée avec succès !"]]);

} catch (Exception $e) {
    // Loguer l'erreur et la renvoyer dans la réponse
    file_put_contents('debug.log', "Erreur : " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo json_encode(["errMsg" => "Erreur : " . $e->getMessage()]);
}
?>