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
    'utilisateur' => ['voir_profil_public', 'voir_profil_prive'],
    'abonne' => ['voir_profil_public', 'voir_profil_prive', 'envoyer_messages'],
    'administrateur' => ['voir_profil_public', 'voir_profil_prive', 'envoyer_messages', 'gerer_utilisateurs']
];

$droits_utilisateur = $droits[$user_type];

// Vérifie si le formulaire de mise à jour a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upgrade_to_abonne'])) {
    // Vérifie si l'utilisateur est connecté
    if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'utilisateur') {
        $user_id = $_SESSION['user_id'];
        $updated = false;

        // Lecture du fichier utilisateurs.txt
        $file = fopen("../txt/utilisateurs.txt", "r");
        $users = [];

        // Boucle pour lire et mettre à jour le type d'utilisateur
        while (!feof($file)) {
            $line = fgets($file);
            $data = explode(",", $line);

            if ($data[0] == $user_id && trim($data[13]) == 'utilisateur') {
                $data[13] = 'abonne';
                $_SESSION['user_type'] = 'abonne';
                $updated = true;
            }

            $users[] = implode(",", $data);
        }

        fclose($file);

        // Écriture des modifications dans le fichier utilisateurs.txt
        if ($updated) {
            $file = fopen("../txt/utilisateurs.txt", "w");
            foreach ($users as $user) {
                fwrite($file, $user);
            }
            fclose($file);

            // Redirection vers la page d'accueil après la mise à jour
            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/abonnement.css">
    <title>Devenir Abonné</title>
</head>
<body>
<header>
    <nav class="navbar">
        <a href="index.php" class="logo">Infinity Love<span>.<span></a>
        <ul class="menu-links">
            <li><a href="index.php">Accueil</a></li> 
            <?php
            // Vérifiez si l'utilisateur est connecté
            if ($user_type === 'visiteur'|| $user_type === 'utilisateur') {
                echo '<li><a href="#features">Offres</a></li>';
            }
            // Vérifiez si l'utilisateur est connecté
            if (isset($_SESSION['user_type']) && $_SESSION['user_type'] !== 'visiteur') {
                echo '<li><a href="mon_profil.php">Mon profil</a></li>';
            } else {
                echo '<li><button onclick="window.location.href=\'inscription.php\'">Inscription</button></li>';
                echo '<li><button onclick="window.location.href=\'connexion.php\'">Connexion</button></li>';
            }

            if (in_array('envoyer_messages', $droits_utilisateur)) {
                echo '<li><a href="message.php">Messages</a></li>';
            }
            if (in_array('gerer_utilisateurs', $droits_utilisateur)) {
                echo '<li><a href="admin.php">Administration</a></li>';
            }

            if ($user_type !== 'visiteur' && $user_type !== 'utilisateur' ){
                echo '<li><a href="recherche.php">Recherche</a></li> ';
            }

            if ($user_type !== 'visiteur'){
                echo '<li><a href="index.php?action=logout">Déconnexion</a></li>';
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
    <?php if ($user_type === 'utilisateur') : ?>
        <section class="hero-section" id="hero-section">
            <div class="content">
                <div class="container">
                    <div class="box form-box">
                        <h1>Devenir Abonné</h1>
                        <form action="" method="post">
                            <div class="field checkbox">
                                <label for="upgrade_to_abonne">
                                    <input type="checkbox" name="upgrade_to_abonne" id="upgrade_to_abonne" required> 
                                    Je veux devenir abonné
                                </label>
                            </div>

                            <div class="field">
                                <input type="submit" class="btn" name="submit" value="Valider">
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
            <ul>
                <li><a href="#hero-section">Accueil</a></li>
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
