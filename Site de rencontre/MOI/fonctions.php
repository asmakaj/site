<?php
function getRecentProfiles($filePath) {
    // Lire le contenu du fichier
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Tableau pour stocker les profils
    $profiles = [];

    // Parcourir chaque ligne du fichier
    foreach ($lines as $line) {
        // Diviser la ligne en champs
        $fields = explode(',', $line);

        // Créer un tableau associatif pour chaque profil
        $profiles[] = [
            'id' => $fields[0],
            'prenom' => $fields[1],
            'nom' => $fields[2],
            'email' => $fields[3],
            'motdepasse' => $fields[4],
            'sexe' => $fields[5],
            'date_creation' => $fields[6],
            'profession' => $fields[7],
            'ville' => $fields[8],
            'statut' => $fields[9],
            'yeux' => $fields[10],
            'extra' => $fields[11],
            'photo' => $fields[12],
            'role' => $fields[13]
        ];
    }

    // Trier les profils par date de création décroissante
    usort($profiles, function($a, $b) {
        return strtotime($b['date_creation']) - strtotime($a['date_creation']);
    });

    // Sélectionner les 10 profils les plus récents
    $recentProfiles = array_slice($profiles, 0, 10);

    return $recentProfiles;
}
?>

<?php

function getConnectedUserData() {
    // Initialiser les données de l'utilisateur connecté
    $userData = array();

    // Démarrer la session
    session_start();

    // Vérifier si l'utilisateur est connecté
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] !== 'visiteur') {
        // Ouvrir le fichier utilisateurs.txt en mode lecture
        $file = fopen("../txt/utilisateurs.txt", "r");

        // Parcourir le fichier ligne par ligne
        while (!feof($file)) {
            $line = fgets($file);
            $data = explode(",", $line);

            // Vérifier si l'identifiant de l'utilisateur connecté correspond à l'identifiant dans le fichier
            if ($_SESSION['user_id'] == $data[0]) {
                // Ajouter les données de l'utilisateur à userData
                $userData['user_id'] = $data[0];
                $userData['first_name'] = $data[1];
                $userData['name'] = $data[2];
                $userData['email'] = $data[3];
                $userData['gender'] = $data[5];
                $userData['birthdate'] = $data[6];
                $userData['profession'] = $data[7];
                $userData['residence'] = $data[8];
                $userData['relationship_status'] = $data[9];
                $userData['physical_description'] = $data[10];
                $userData['personal_info'] = $data[11];
                $userData['photo'] = $data[12];
                $userData['user_type'] = $data[13];

                // Sortir de la boucle une fois que les données sont trouvées
                break;
            }
        }

        // Fermer le fichier
        fclose($file);
    }

    // Retourner les données de l'utilisateur
    return $userData;
}
?>

<?php

function updateUserData($postData) {
    session_start();

    // Vérifier si l'utilisateur est connecté
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] !== 'visiteur') {
        // Lire toutes les lignes du fichier dans un tableau
        $lines = file("../txt/utilisateurs.txt");

        // Parcourir les lignes pour trouver et mettre à jour l'utilisateur actuel
        foreach ($lines as &$line) {
            $data = explode(",", $line);
            if ($_SESSION['user_id'] == $data[0]) {
                // Mettre à jour les données de l'utilisateur avec les valeurs soumises dans le formulaire
                $data[1] = $postData['first_name'];
                $data[2] = $postData['name'];
                $data[3] = $postData['email'];
                $data[5] = $postData['gender'];
                $data[6] = $postData['birthdate'];
                $data[7] = $postData['profession'];
                $data[8] = $postData['residence'];
                $data[9] = $postData['relationship_status'];
                $data[10] = $postData['physical_description'];
                $data[11] = $postData['personal_info'];
                $data[12] = $postData['photo'];

                // Reconstruire la ligne mise à jour
                $line = implode(",", $data);
                break;
            }
        }

        // Réécrire toutes les lignes dans le fichier
        file_put_contents("../txt/utilisateurs.txt", implode("", $lines));
    }
}
?>
