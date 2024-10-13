<?php
$userData = json_decode(file_get_contents("./users.json"), true);
$response = array();

if (isset($_POST["pseudo"]) && isset($_POST["newPassword"])) {
    $pseudo = $_POST["pseudo"];
    $newPassword = hash_hmac("SHA256", $_POST["newPassword"], "wishcordROX");

    // Vérifier si le pseudo existe dans les données des utilisateurs
    if (array_key_exists($pseudo, $userData)) {
        // Mise à jour du mot de passe dans les données utilisateurs
        $userData[$pseudo]["mdp"] = $newPassword;
        file_put_contents("./users.json", json_encode($userData));

        $response["message"] = "Mot de passe réinitialisé avec succès.";
    } else {
        $response["errMsg"] = "Pseudo non trouvé.";
    }
} else {
    $response["errMsg"] = "Pseudo ou mot de passe non fourni.";
}

echo json_encode($response);
?>
