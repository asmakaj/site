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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de l'utilisateur</title>
    <link rel="stylesheet" href="../styles/mon_profil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="navbar">
            <a href="index.php" class="logo">Infinity Love<span>.</span></a>
            <ul class="menu-links">
                <li><a href="index.php#hero-section">Accueil</a></li>
                <li><a href="recherche.php">Recherche</a></li>
            <?php
            if (in_array('envoyer_messages', $droits_utilisateur)) {
                    echo '<A href=\'message.php\'">Messages</a></li>';
                }
            ?>
            <?php
            if ($_SESSION['user_type'] === 'utilisateur') {
                echo '<li><a href=\'index.php#features\'">Offres</a></li>';
            }
            ?>
            <?php
           if ($_SESSION['user_type'] === 'administrateur') {
            echo '<li><a href=\'admin.php\'">Administration</a></li>';
            }
            ?>


            </ul>
            <div class="auth-buttons">
                <?php
                // Vérifiez si l'utilisateur est connecté
                if (isset($_SESSION['user_type']) && $_SESSION['user_type'] !== 'visiteur') {
                    echo '<button onclick="window.location.href=\'index.php?action=logout\'">Déconnexion</button>';
                } else {
                    echo '<button onclick="window.location.href=\'inscription.php\'">Inscription</button>';
                    echo '<button onclick="window.location.href=\'connexion.php\'">Connexion</button>';
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

                $user_id_session = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Stocker l'ID de l'utilisateur connecté
                ?>
            </div>
        </div>
    </header>
    
    <div class="hero-section">
        <div class="background-overlay"></div>
        <div class="content">
            <div class="container">
                <div class="box form-box">
                    <h1>Mes informations</h1>
                    <header>Profil de l'utilisateur</header>
                    <?php
                    require_once 'fonctions.php';

                    // Récupérer les données de l'utilisateur connecté
                    $userData = getConnectedUserData();

                    

                    // Vérifier si le formulaire de modification a été soumis
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        // Mettre à jour les données de l'utilisateur dans le fichier utilisateurs.txt
                        if(isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
                            $target_dir = "uploads/";
                            $target_file = $target_dir . basename($_FILES["photo"]["name"]);
                            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                            // Supprimer l'ancienne image
                            if(file_exists($userData['photo'])) {
                                unlink($userData['photo']);
                            }
                            // Déplacer la nouvelle image téléchargée vers le répertoire uploads
                            move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);
                            // Mettre à jour le chemin de l'image dans les données de l'utilisateur
                            $userData['photo'] = $target_file;
                        }
                        // Mettre à jour les autres champs de données de l'utilisateur
                        $userData['first_name'] = $_POST['first_name'];
                        $userData['name'] = $_POST['name'];
                        $userData['email'] = $_POST['email'];
                        $userData['gender'] = $_POST['gender'];
                        $userData['birthdate'] = $_POST['birthdate'];
                        $userData['profession'] = $_POST['profession'];
                        $userData['residence'] = $_POST['residence'];
                        $userData['relationship_status'] = $_POST['relationship_status'];
                        $userData['physical_description'] = $_POST['physical_description'];
                        $userData['personal_info'] = $_POST['personal_info'];
                        // Mettre à jour les données de l'utilisateur dans le fichier utilisateurs.txt
                        updateUserData($userData);
                        $message = "Modification bien enregistrée.";

                        // Redirect to index.php after 1.5 seconds
                        echo "<script>
                            setTimeout(function() {
                                window.location.href = 'index.php';
                            }, 1500);
                        </script>";
                    }

                    // Afficher les données de l'utilisateur
                    if (!empty($userData)) {
                        echo "<form method='post' enctype='multipart/form-data'>";
                        echo "<div class='field'><label for='photo'>Photo:</label><div class='img-container'><img src='" . $userData['photo'] . "' alt='Photo de profil' style='max-width: 200px;'></div><input type='file' name='photo' id='photo' accept='image/*'></div>";
                        echo "<div class='field'><label for='first_name'>Prénom:</label><input type='text' name='first_name' id='first_name' value='" . $userData['first_name'] . "'></div>";
                        echo "<div class='field'><label for='name'>Nom:</label><input type='text' name='name' id='name' value='" . $userData['name'] . "'></div>";
                        echo "<div class='field'><label for='email'>Email:</label><input type='email' name='email' id='email' value='" . $userData['email'] . "'></div>";
                        echo "<div class='field'><label for='gender'>Sexe:</label><select name='gender' id='gender'><option value='homme'" . ($userData['gender'] == 'homme' ? ' selected' : '') . ">Homme</option><option value='femme'" . ($userData['gender'] == 'femme' ? ' selected' : '') . ">Femme</option><option value='autre'" . ($userData['gender'] == 'autre' ? ' selected' : '') . ">Autre</option></select></div>";
                        echo "<div class='field'><label for='birthdate'>Date de naissance:</label><input type='date' name='birthdate' id='birthdate' value='" . $userData['birthdate'] . "'></div>";
                        echo "<div class='field'><label for='profession'>Profession:</label><input type='text' name='profession' id='profession' value='" . $userData['profession'] . "'></div>";
                        echo "<div class='field'><label for='residence'>Résidence:</label><input type='text' name='residence' id='residence' value='" . $userData['residence'] . "'></div>";
                        echo "<div class='field'><label for='relationship_status'>Statut relationnel:</label><input type='text' name='relationship_status' id='relationship_status' value='" . $userData['relationship_status'] . "'></div>";
                        echo "<div class='field'><label for='physical_description'>Description physique:</label><input type='text' name='physical_description' id='physical_description' value='" . $userData['physical_description'] . "'></div>";
                        echo "<div class='field'><label for='personal_info'>Informations personnelles:</label><textarea name='personal_info' id='personal_info'>" . $userData['personal_info'] . "</textarea></div>";
                        echo "<button type='submit' class='btn submit'>Enregistrer les modifications</button>";
                        echo "</form>";
                    } else {
                        echo "<p>Aucune donnée d'utilisateur trouvée.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

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

    <script>
        // JavaScript for toggling mobile menu
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const closeMenuBtn = document.getElementById('close-menu-btn');
        const header = document.querySelector('header');

        hamburgerBtn.addEventListener('click', () => {
            header.classList.add('show-mobile-menu');
        });

        closeMenuBtn.addEventListener('click', () => {
            header.classList.remove('show-mobile-menu');
        });
    </script>
</body>
</html>
