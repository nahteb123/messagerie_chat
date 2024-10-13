<?php 

include_once("./base64URL.php");
$usersFile = "./users.json"; // Spécifie le chemin vers ton fichier users.json

// Vérifier que la méthode est POST
if ($_SERVER['REQUEST_METHOD'] != "POST") {
    echo json_encode(["errMsg" => "Méthode non autorisée"]);
    exit;
}

// Récupérer les paramètres POST : salon et user_id
$salon = isset($_POST['salon']) ? $_POST['salon'] : null;
$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;

if (!$salon || !$user_id) {
    echo json_encode(["errMsg" => "Salon ou utilisateur manquant"]);
    exit;
}

try {
    // Vérifier si le fichier users.json existe
    if (!file_exists($usersFile)) {
        echo json_encode(["errMsg" => "Fichier des utilisateurs non trouvé"]);
        exit;
    }

    // Lire le contenu du fichier JSON des utilisateurs
    $fileContent = file_get_contents($usersFile);

    $usersData = json_decode($fileContent, true);

    // Vérifier s'il y a des erreurs de décodage JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["errMsg" => "Erreur de formatage du fichier JSON"]);
        exit;
    }

    // Vérifier si l'utilisateur existe dans les données
    if (!isset($usersData[$user_id]) || $user_id === "" || $user_id === "null") {
        echo json_encode(["errMsg" => "Utilisateur non trouvé ou ID invalide"]);
        exit;
    }

    // Vérifier si l'utilisateur fait partie du salon
    if (in_array($salon, $usersData[$user_id]['salons'])) {
        // Retirer l'utilisateur du salon
        $key = array_search($salon, $usersData[$user_id]['salons']);
        unset($usersData[$user_id]['salons'][$key]);
        $usersData[$user_id]['salons'] = array_values($usersData[$user_id]['salons']); // Réindexer les salons
    } else {
        echo json_encode(["errMsg" => "L'utilisateur n'est pas dans ce salon"]);
        exit;
    }

    // Mettre à jour le fichier JSON
    file_put_contents($usersFile, json_encode($usersData, JSON_PRETTY_PRINT));

    // Répondre avec un message de succès
    echo json_encode(["data" => ["msg" => "L'utilisateur a été retiré du salon avec succès !"]]);

} catch (Exception $e) {
    // Loguer l'erreur et la renvoyer dans la réponse
    file_put_contents('debug.log', "Erreur : " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo json_encode(["errMsg" => "Erreur : " . $e->getMessage()]);
}

?>
