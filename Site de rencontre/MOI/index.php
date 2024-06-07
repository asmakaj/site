<?php
session_start();

// Initialiser la session de base pour les visiteurs
if (!isset($_SESSION['user_type'])) {
    $_SESSION['user_type'] = 'visiteur';
}

$user_type = $_SESSION['user_type'];
$user_id_session = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Définir les droits pour chaque type d'utilisateur
$droits = [
    'visiteur' => ['voir_profil_public'],
    'utilisateur' => ['voir_profil_public', 'voir_profil_prive'],
    'abonne' => ['voir_profil_public', 'voir_profil_prive', 'envoyer_messages'],
    'administrateur' => ['voir_profil_public', 'voir_profil_prive', 'envoyer_messages', 'gerer_utilisateurs']
];

$droits_utilisateur = $droits[$user_type];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infinity'love - RencontreSite</title>
    <link rel="stylesheet" href="../styles/accueil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
</head>
<body>
    <header>
    	<nav class="navbar">
  	        <a href="#" class="logo">Infinity Love<span>.<span></a>
                <ul class="menu-links">
                    <li><a href="#hero-section">Accueil</a></li> 
                    <?php
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

                    if ($user_type !== 'visiteur' ){
                        echo '<li><a href="recherche.php">Recherche</a></li> ';
                    }
                    
                    if ($user_type !== 'visiteur'){
                  
                        echo '<li><a href="index.php?action=logout" class="btn-logout">Déconnexion</a></li>';
                       
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
        <?php if ($user_type !== 'visiteur') : ?>
            <style>
                header {
                background-color: black;
                position : relative;
                }
                </style>
        <section class="users">
            <div class="container">
                <h2>Des profils qui vous tentent ?</h2>
                <div class="p-title">
                <p>Découvrez ci-dessous les 10 derniers inscrits !</p>
                </div>
                <div id="scroll-gallery-feature-cards" class="gallery gallery-align-start gallery-feature-cards">
                    <div class="scroll-container">
                        <div class="item-container">
                            <ul class="card-set" role="list">
                                <?php
                                // Inclure le fichier contenant les fonctions
                                require_once 'fonctions.php';

                                // Utiliser la fonction pour obtenir les 10 profils les plus récents
                                $filePath = "../txt/utilisateurs.txt";
                                $recentProfiles = getRecentProfiles($filePath);

                                if (count($recentProfiles) > 0) {
                                    foreach ($recentProfiles as $user) {
                                        if ($user['id'] != $user_id_session) {
                                            echo "<li role='listitem' class='gallery-item grid-item'>";
                                            echo "<a href='profil.php?id=" . urlencode($user['id']) . "' class='feature-card-link'>";
                                            echo "<div class='feature-card card-container'>";
                                            echo "<figure class='feature-card-image-container'>";
                                            echo "<img src='" . htmlspecialchars($user['photo']) . "' alt='Photo de " . htmlspecialchars($user['prenom']) . "' class='feature-card-image'>";
                                            echo "</figure>";
                                            echo "<div class='card-modifier card-padding theme-dark fixed-width'>";
                                            echo "<div class='card-viewport-content'>";
                                            echo "<div class='feature-card-content'>";
                                            echo "<div class='feature-card-copy'>";
                                            echo "<p class='typography-feature-card-label feature-card-label'>" . htmlspecialchars($user['nom']) . " " . htmlspecialchars($user['prenom']) . "</p>";
                                            echo "<p class='typography-card-headline feature-card-headline'>Date de naissance:<br> " . htmlspecialchars($user['date_creation']) . "<br>Ville: " . htmlspecialchars($user['ville']) . "<br>Statut: " . htmlspecialchars($user['statut']) . "</p>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</a>";
                                            echo "</li>";
                                        }
                                    }
                                } else {
                                    echo "<p>Aucun utilisateur trouvé</p>";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="paddlenav paddlenav-alpha">
                        <ul class="sticky-element">
                            <li class="left-item"><button aria-label="Précédent" class="paddlenav-arrow paddlenav-arrow-previous" disabled="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path d="M21.559,12.062 L15.618,17.984 L21.5221,23.944 C22.105,24.533 22.1021,25.482 21.5131,26.065 C21.2211,26.355 20.8391,26.4999987 20.4571,26.4999987 C20.0711,26.4999987 19.6851,26.352 19.3921,26.056 L12.4351,19.034 C11.8531,18.446 11.8551,17.4999987 12.4411,16.916 L19.4411,9.938 C20.0261,9.353 20.9781,9.354 21.5621,9.941 C22.1471,10.528 22.1451,11.478 21.5591,12.062 L21.559,12.062 Z"></path></svg>
                            </button></li>
                            <li class="right-item"><button aria-label="Suivant" class="paddlenav-arrow paddlenav-arrow-next">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path d="M23.5587,16.916 C24.1447,17.4999987 24.1467,18.446 23.5647,19.034 L16.6077,26.056 C16.3147,26.352 15.9287,26.4999987 15.5427,26.4999987 C15.1607,26.4999987 14.7787,26.355 14.4867,26.065 C13.8977,25.482 13.8947,24.533 14.4777,23.944 L20.3818,17.984 L14.4408,12.062 C13.8548,11.478 13.8528,10.5279 14.4378,9.941 C15.0218,9.354 15.9738,9.353 16.5588,9.938 L23.5588,16.916 L23.5587,16.916 Z"></path></svg>
                            </button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($user_type === 'visiteur') : ?>
        <section class="hero-section" id="hero-section">
            <div class="content">
                <h2>Trouvez l'amour de votre vie</h2>
                <p>Rejoignez notre communauté dès maintenant et commencez votre voyage vers une relation significative.</p>
                <button onclick="window.location.href='inscription.php'">Je m'inscris</button>
            </div>
        </section>

        <?php endif; ?>
<?php if ($user_type === 'visiteur' || $user_type === 'utilisateur') : ?>
<section class="features" id="features">
<div class=pricing_table>
    <div class=details>
        <h1>Nos offres</h1>
        <p>Découvrez sans plus attendre nos offres d'abonnement !</p>
    </div>
    <div class="grid">
        <div class="box basic">
            <div class="title">Classique</div>
                <div class="price">
                    <b>0$</b>
                    <span>par mois</span>
                </div>
                <div class="features">
                    <div>✅ Création de compte</div>
                    <div>✅ Consultation de 10 profils</div>
                    <div>❌ Messagerie</div>
                    <div>❌ Like et amitié</div>
                </div>
                <div class="button">
                <?php if ($user_type === 'visiteur') : ?> 
                <button onclick="window.location.href='inscription.php'">Découvrir</button>
                <?php endif; ?>
                <?php if ($user_type === 'utilisateur') : ?> 
                    <button onclick="window.location.href='index.php'">Découvrir</button>
                <?php endif; ?>
                </div>
        </div>
        <div class="box pro">
            <div class="title">Premium</div>
                <div class="price">
                    <b>$14.99</b>
                    <span>par mois</span>
                </div>
                <div class="features">
                    <div>✅ Création de compte</div>
                    <div>✅ Consultation de profils illimités</div>
                    <div>✅ Messagerie </div>
                    <div>✅ Like et amitié</div>
                </div>
                <div class="button">
                <?php if ($user_type === 'visiteur') : ?> 
                <button onclick="window.location.href='inscription.php'">Découvrir</button>
                <?php endif; ?>
                <?php if ($user_type === 'utilisateur') : ?> 
                    <button onclick="window.location.href='abonnement.php'">Découvrir</button>
                <?php endif; ?>
                </div>
        
        </div>
    </div>
</div>
</section>

        
        <?php endif; ?>

    </main>
    
    <script>
      const header = document.querySelector("header");
      const hamburgerBtn = document.querySelector("#hamburger-btn");
      const closeMenuBtn = document.querySelector("#close-menu-btn");
      // Toggle mobile menu on hamburger button click
      hamburgerBtn.addEventListener("click", () => header.classList.toggle("show-mobile-menu"));
      // Close mobile menu on close button click
      closeMenuBtn.addEventListener("click", () => hamburgerBtn.click());
    </script>

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
            <ul><li><a href="#hero-section">Accueil</a></li>
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
