<?php

function getUserById($id) {
    // Ouvrir le fichier utilisateurs.txt en mode lecture
    $file = fopen("../txt/utilisateurs.txt", "r");

    // Lire le contenu du fichier et le stocker dans un tableau
    $users = [];
    while (!feof($file)) {
        $user = fgets($file);
        if ($user) {
            $users[] = $user;
        }
    }

    // Fermer le fichier
    fclose($file);

    // Parcourir le tableau des utilisateurs pour trouver l'utilisateur avec l'ID donné
    foreach ($users as $user) {
        $data = explode(",", $user);
        if (trim($data[0]) === $id) {
            // Retourner l'utilisateur sous forme de tableau associatif
            return [
                'id' => $data[0],
                'first_name' => $data[1],
                'name' => $data[2],
                'email' => $data[3],
                'password' => $data[4],
                'gender' => $data[5],
                'birthdate' => $data[6],
                'profession' => $data[7],
                'residence' => $data[8],
                'relationship_status' => $data[9],
                'physical_description' => $data[10],
                'personal_info' => $data[11],
                'photo_address' => $data[12],
                'user_type' => $data[13],
                'ban' => $data[14]
            ];
        }
    }

    // Si aucun utilisateur avec l'ID donné n'est trouvé, retourner null
    return null;
}

if (isset($_POST['modifier']) && isset($_POST['id'])) {
    session_start();

    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] !== 'visiteur') {
        // Lire toutes les lignes du fichier dans un tableau
        $lines = file("../txt/utilisateurs.txt");

        // Parcourir les lignes pour trouver et mettre à jour l'utilisateur actuel
        foreach ($lines as &$line) {
            $data = explode(",", $line);
            if ($_POST['id'] == trim($data[0])) {
                // Mettre à jour les données de l'utilisateur avec les valeurs soumises dans le formulaire
                $data[1] = $_POST['first_name'];
                $data[2] = $_POST['name'];
                $data[3] = $_POST['email'];
                $data[4] = $_POST['password'];
                $data[5] = $_POST['gender'];
                $data[6] = $_POST['birthdate'];
                $data[7] = $_POST['profession'];
                $data[8] = $_POST['residence'];
                $data[9] = $_POST['relationship_status'];
                $data[10] = $_POST['physical_description'];
                $data[11] = $_POST['personal_info'];
                $data[12] = $_POST['photo_address'];
                $data[13] = $_POST['user_type'];

                // Reconstruire la ligne mise à jour
                $line = implode(",", $data);
                break;
            }
        }

        // Réécrire toutes les lignes dans le fichier
        file_put_contents("../txt/utilisateurs.txt", implode("", $lines));

        // Rediriger l'utilisateur vers la page d'administration
        header("Location: admin.php");
        exit;
    } else {
        // Afficher un message d'erreur si l'utilisateur n'est pas trouvé
        echo "Aucun utilisateur avec cet ID n'a été trouvé.";
    }
}

// Récupérer l'utilisateur à modifier
$user = getUserById($_GET['id']);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier utilisateur</title>
</head>
<body>
<h1>Modifier utilisateur</h1>
<form action="modifier_utilisateur.php" method="post">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
    <label for="first_name">User ID : <?php echo htmlspecialchars($user['id']); ?></label>
    <br>
    <label for="first_name">Prénom :</label>
    <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
    <br>
    <label for="name">Nom :</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
    <br>
    <label for="email">Email :</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
    <br>
    <label for="password">Mot de passe :</label>
    <input type="password" name="password" value="<?php echo htmlspecialchars($user['password']); ?>">
    <br>
    <label for="gender">Genre :</label>
    <input type="text" name="gender" value="<?php echo htmlspecialchars($user['gender']); ?>">
    <br>
    <label for="birthdate">Date de naissance :</label>
    <input type="date" name="birthdate" value="<?php echo htmlspecialchars($user['birthdate']); ?>">
    <br>
    <label for="profession">Profession :</label>
    <input type="text" name="profession" value="<?php echo htmlspecialchars($user['profession']); ?>">
    <br>
    <label for="residence">Résidence :</label>
    <input type="text" name="residence" value="<?php echo htmlspecialchars($user['residence']); ?>">
    <br>
    <label for="relationship_status">Statut de la relation :</label>
    <input type="text" name="relationship_status" value="<?php echo htmlspecialchars($user['relationship_status']); ?>">
    <br>
    <label for="physical_description">Description physique :</label>
    <textarea name="physical_description"><?php echo htmlspecialchars($user['physical_description']); ?></textarea>
    <br>
    <label for="personal_info">Informations personnelles :</label>
    <textarea name="personal_info"><?php echo htmlspecialchars($user['personal_info']); ?></textarea>
    <br>
    <label for="photo_address">Adresse de la photo :</label>
    <input type="text" name="photo_address" value="<?php echo htmlspecialchars($user['photo_address']); ?>">
    <br>
    <label for="user_type">Type d'utilisateur :</label>
    <select name="user_type" id="user_type">
        <option value="utilisateur" <?php if ($user['user_type'] === 'utilisateur') echo 'selected'; ?>>utilisateur</option>
        <option value="administrateur" <?php if ($user['user_type'] === 'administrateur') echo 'selected'; ?>>administrateur</option>
    </select>
    <br>
    <input type="submit" name="modifier" value="Modifier">
</form>
</body>
</html>
