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

// Vérifie si le formulaire de connexion a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données saisies dans le formulaire
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Lecture du fichier utilisateurs.txt
    $file = fopen("../txt/utilisateurs.txt", "r");

    // Boucle pour rechercher l'utilisateur dans le fichier
    while (!feof($file)) {
        $line = fgets($file);
        $data = explode(",", $line);

        // Vérification des identifiants
        if ($data[3] == $email && trim($data[4]) == $password) {
            // Si les identifiants sont valides, définir les informations de session
            $_SESSION['user_id'] = $data[0]; // ID de l'utilisateur
            $_SESSION['user_type'] = trim($data[13]); // Type d'utilisateur

            if ($data[14] == "oui"){
                // Le compte est bannis faire une page pour l'indiquer
                echo "<div class='message'>Vous êtes bannis.</div>";
                break;
            }

            fclose($file);

            // Redirection vers la page d'accueil après la connexion
            header("Location: index.php");
            exit();
        }
    }

    if ($data[14] == "non") {
        // Si les identifiants ne correspondent à aucun utilisateur, afficher un message d'erreur
        echo "<div class='message'>Nom d'utilisateur ou mot de passe incorrect.</div>";
    }

    fclose($file);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/styles.css">
    <title>Connexion</title>
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
                    } 
                    else {
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
    <main>
    <?php if ($user_type === 'visiteur') : ?>
        <section class="hero-section" id="hero-section">
            <div class="content">
            <div class="container">
        <div class="box form-box">
            <h1>Se connecter</h1>
            <form action="" method="post">
                <div class="field input">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Login">
                </div>
                <div class="links">
                    Pas encore inscrit(e) ? <a href="inscription.php">S'inscrire ici</a><br>
                </div>
            </form>
        </div>
    </div>
            </div>
        </section>


        <?php endif; ?>

        
</main>
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
