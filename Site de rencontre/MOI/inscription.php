<?php
session_start();

// Initialiser la session de base pour les visiteurs
if (!isset($_SESSION['user_type'])) {
    $_SESSION['user_type'] = 'visiteur';
}

$user_type = $_SESSION['user_type'];

// Définir les droits pour chaque type d'utilisateur
$droits = [
    'visiteur' => ['voir_profil_public'],
    'utilisateur' => ['voir_profil_public', 'voir_profil_prive', 'envoyer_messages', 'gerer_utilisateurs'],
    'abonne' => ['voir_profil_public', 'voir_profil_prive', 'envoyer_messages'],
    'administrateur' => ['voir_profil_public', 'voir_profil_prive', 'envoyer_messages', 'gerer_utilisateurs']
];

$droits_utilisateur = $droits[$user_type];
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Génération de l'identifiant utilisateur unique
    $user_id = "user_" . uniqid();
    $user_type = "utilisateur";

    $first_name = $_POST['first_name'];
    $name = $_POST['name'];
    $mail = $_POST['email'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $profession = $_POST['profession'];
    $residence = $_POST['residence'];
    $relationship_status = $_POST['relationship_status'];
    $physical_description = $_POST['physical_description'];
    $personal_info = $_POST['personal_info'];
    $ban= 'non'; //Utilisateur banni ou non (Initialement non)

    // Traitement de l'image
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);
    move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);
    $photo_address = $target_file;

    // Formatage des données pour le stockage dans le fichier
    $data = $user_id . "," . $first_name . "," . $name . "," . $mail . "," . $password . "," . $gender . "," . $birthdate . "," . $profession . "," . $residence . "," . $relationship_status . "," . $physical_description . "," . $personal_info . "," . $photo_address . "," . $user_type . "," . $ban ."\n";

    // Ouverture du fichier en mode append
    $file = fopen("../txt/utilisateurs.txt", "a");

    // Écriture des données dans le fichier
    fwrite($file, $data);

    // Fermeture du fichier
    fclose($file);

    // Redirection vers la page de connexion après l'inscription
    header("Location: connexion.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="../styles/inscription.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Inscription</title>
</head>
<body>
<header>
        
    	<nav class="navbar">
  	        <a href="#" class="logo">Infinity Love<span>.<span></a>
                <ul class="menu-links">
                    <li><a href="index.php">Accueil</a></li> 
                    <li><a href="index.php">Offres</a></li>
                    
                    <?php
                    // Vérifiez si l'utilisateur est connecté
                    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] !== 'visiteur') {
                        echo '<li><a href="index.php?action=logout">Déconnexion</a></li>';
                        echo '<li><a href="mon_profil.php">Mon profil</a></li>';
                    } else {
                        echo '<li><button onclick="window.location.href=\'inscription.php\'">Inscription</button></li>';
                        echo '<li><button onclick="window.location.href=\'connexion.php\'">Connexion</button></li>';
                    }

                    if (in_array('envoyer_messages', $droits_utilisateur)) {
                        echo '<li><a href="messages.php">Messages</a></li>';
                    }
                    if (in_array('gerer_utilisateurs', $droits_utilisateur)) {
                        echo '<li><a href="admin.php">Administration</a></li>';
                    }

                    // Si l'action de déconnexion est demandée
                    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
                        // Détruisez toutes les variables de session
                        $_SESSION = array();

                        // Détruisez la session
                        session_destroy();

                        // Redirigez l'utilisateur vers la page d'accueil après la déconnexion
                        header("Location: index.php");
                        exit;
                    }
                    ?>
                </ul>
            </nav>
    </header>
    <?php if ($user_type === 'visiteur') : ?>
    <section class="hero-section" id="hero-section">
    <div class="content">
    <div class="container">
        <div class="box form-box">
            <h1>S'inscrire</h1>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="field input">
                    <label for="first_name">Prénom</label>
                    <input type="text" name="first_name" autocomplete="off" required><br>
                </div>
                <div class="field input">
                    <label for="name">Nom</label>
                    <input type="text" name="name" autocomplete="off" required><br>
                </div>
                <div class="field input">
                    <label for="email">Email</label>
                    <input type="email" name="email" autocomplete="off" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"><br>
                </div>
                <div class="field input">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" autocomplete="off" required><br>
                </div>
                <div class="field input">
                    <label for="gender">Sexe</label>
                    <select name="gender" required>
                        <option value="">Choisissez...</option>
                        <option value="femme">Femme</option>
                        <option value="homme">Homme</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                <div class="field input">
                    <label for="birthdate">Date de naissance</label>
                    <input type="date" name="birthdate" autocomplete="off" required><br>
                </div>
                <div class="field input">
                    <label for="profession">Profession</label>
                    <input type="text" name="profession" autocomplete="off" required><br>
                </div>
                <div class="field input">
                    <label for="residence">Lieu de résidence</label>
                    <input type="text" name="residence" autocomplete="off" required><br>
                </div>
                <div class="field input">
                    <label for="relationship_status">Relations amoureuses et familiales</label>
                    <input type="text" name="relationship_status" autocomplete="off" required><br>
                </div>
                <div class="field input">
                    <label for="physical_description">Description physique</label>
                    <input type="text" name="physical_description" autocomplete="off" required><br>
                </div>
                <div class="field input">
                    <label for="personal_info">Informations personnelles</label>
                    <textarea name="personal_info"></textarea><br>
                </div>
                <div class="field input">
                    <label for="photo">Photo</label>
                    <input type="file" name="photo" accept="image/*"><br>
                </div>
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="S'inscrire" required>
                </div>
                <div class="links">
                    Déjà inscrit ? <a href="connexion.php">Se connecter</a>
                </div>
            </form>
        </div>
        <div>
    </div>
    </section>
    <?php endif; ?>
    <footer>
    <div class="footerContainer">
        <div class="socialIcons">
            <a href=""><i class="fa-brands fa-facebook"></i></a>
            <a href=""><i class="fa-brands fa-instagram"></i></a>
            <a href=""><i class="fa-brands fa-twitter"></i></a>
            <a href=""><i class="fa-brands fa-google-plus"></i></a>
            <a href=""><i class="fa-brands fa-youtube"></i></a>
        </div>
        <div class="footerNav">
            <ul><li><a href="#hero-section">Accueuil</a></li>
                <li><a href="">A propos</a></li>
                <li><a href="">Nous contacter</a></li>
                <li><a href="">Notre équipe</a></li>
                <li><a href="">Foire aux questions</a></li>
            </ul>
        </div>
        
    </div>
    <div class="footerBottom">
    <p>&copy; 2024 Infinity'love - Tous droits réservés</p>
    </div>
</footer>
</body>
</html>

    </div>
</footer>
</body>
</html>
