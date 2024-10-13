<?php
// listUsersInSalon.php
file_put_contents('debug.log', 'listUsersInSalon.php appelé' . PHP_EOL, FILE_APPEND);

$usersFile = "./users.json"; // Chemin vers le fichier users.json

// Vérifie que la méthode est POST
if ($_SERVER['REQUEST_METHOD'] != "POST") {
    echo json_encode(["errMsg" => "Méthode non autorisée"]);
    exit;
}

// Récupérer le salon
$salon = isset($_POST['salon']) ? $_POST['salon'] : null;
if (!$salon) {
    echo json_encode(["errMsg" => "Salon manquant"]);
    exit;
}

try {
    // Lire le fichier users.json
    if (!file_exists($usersFile)) {
        echo json_encode(["errMsg" => "Fichier des utilisateurs non trouvé"]);
        exit;
    }

    $usersData = json_decode(file_get_contents($usersFile), true); // Lire le contenu du fichier JSON

    // Vérifier si la lecture du fichier est correcte
    if (empty($usersData)) {
        file_put_contents('debug.log', 'usersData est vide ou invalide' . PHP_EOL, FILE_APPEND);
        echo json_encode(["errMsg" => "Erreur lors de la lecture du fichier des utilisateurs"]);
        exit;
    }

    // Récupérer les utilisateurs dans le salon spécifié
    $usersInSalon = [];
    foreach ($usersData as $userId => $userInfo) {
        if (isset($userInfo['salons']) && in_array($salon, $userInfo['salons'])) {
            array_push($usersInSalon, $userId);
           

        }
    }
   
file_put_contents('debug.log', print_r($usersInSalon, true) . PHP_EOL, FILE_APPEND);

    // Vérifier si des utilisateurs ont été trouvés
    if (empty($usersInSalon)) {
        echo json_encode(["data" => ["msg" => "Aucun utilisateur trouvé dans ce salon."]]);
    } else {
        echo json_encode(["data" => ["users" => $usersInSalon]]);
    }

} catch (Exception $e) {
    echo json_encode(["errMsg" => "Erreur : " . $e->getMessage()]);
}
?>
