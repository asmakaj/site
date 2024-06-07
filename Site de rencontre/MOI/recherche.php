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
    <title>Recherche de profils</title>
    <link rel="stylesheet" href="../styles/recherche.css">
    <!-- Ajout de la balise de lien pour Font Awesome pour les icônes de réseaux sociaux -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">Infinity Love<span>.<span></a>
            <ul class="menu-links">
                <li><a href="index.php#hero-section">Accueil</a></li> 
                <?php
                if ($_SESSION['user_type'] === 'utilisateur') {
                echo '<li><a href=\'index.php#features\'">Offres</a></li>';
                }
                ?>
                <?php
                if ($_SESSION['user_type'] !== 'visiteur') {
                    echo '<li><a href=\'mon_profil.php\'">Mon profil</a></li>';
                }
            ?>
            <?php
            if ($_SESSION['user_type'] !== 'utilisateur') {
                    echo '<li><a href=\'messages.php\'">Messages</a></li>';
                }
                ?>
            </ul>
            <div class="auth-buttons">
                <?php
                // Vérifiez si l'utilisateur est connecté
                session_start();
                if (isset($_SESSION['user_type']) && $_SESSION['user_type'] !== 'visiteur') {
                    echo '<button onclick="window.location.href=\'index.php?action=logout\'">Déconnexion</button>';
                } else {
                    echo '<button onclick="window.location.href=\'inscription.php\'">Inscription</button>';
                    echo '<button onclick="window.location.href=\'connexion.php\'">Connexion</button>';
                }

                $droits_utilisateur = []; // Définir les droits utilisateur ici
                if (in_array('gerer_utilisateurs', $droits_utilisateur)) {
                    echo '<button onclick="window.location.href=\'admin.php\'">Administration</button>';
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
        </nav>
    </header>

    <div class="main-content">
        <div class="sidebar">
            <form method="GET" action="recherche.php">
                <h3>Filtrer par :</h3>
                
                <label for="gender">Genre :</label>
                <select name="gender" id="gender">
                    <option value="">Tous</option>
                    <option value="homme" <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'homme') ? 'selected' : ''; ?>>Homme</option>
                    <option value="femme" <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'femme') ? 'selected' : ''; ?>>Femme</option>
                    <option value="autre" <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'autre') ? 'selected' : ''; ?>>Autre</option>
                </select>
                
                <label for="relationship_status">Statut de relation :</label>
                <select name="relationship_status" id="relationship_status">
                    <option value="">Tous</option>
                    <option value="célibataire" <?php echo (isset($_GET['relationship_status']) && $_GET['relationship_status'] == 'célibataire') ? 'selected' : ''; ?>>Célibataire</option>
                    <option value="en couple" <?php echo (isset($_GET['relationship_status']) && $_GET['relationship_status'] == 'en couple') ? 'selected' : ''; ?>>En couple</option>
                    <option value="marié(e)" <?php echo (isset($_GET['relationship_status']) && $_GET['relationship_status'] == 'marié(e)') ? 'selected' : ''; ?>>Marié(e)</option>
                </select>
                
                <label for="residence">Lieu de résidence :</label>
                <input type="text" name="residence" id="residence" placeholder="Ville..." value="<?php echo isset($_GET['residence']) ? htmlspecialchars($_GET['residence']) : ''; ?>">
                
                <label for="profession">Profession :</label>
                <input type="text" name="profession" id="profession" placeholder="Profession..." value="<?php echo isset($_GET['profession']) ? htmlspecialchars($_GET['profession']) : ''; ?>">
                
                <label for="age_min">Âge minimum :</label>
                <input type="number" name="age_min" id="age_min" placeholder="Âge minimum..." value="<?php echo isset($_GET['age_min']) ? htmlspecialchars($_GET['age_min']) : ''; ?>">
                
                <label for="age_max">Âge maximum :</label>
                <input type="number" name="age_max" id="age_max" placeholder="Âge maximum..." value="<?php echo isset($_GET['age_max']) ? htmlspecialchars($_GET['age_max']) : ''; ?>">
                
                <label for="physical_description">Description physique :</label>
                <input type="text" name="physical_description" id="physical_description" placeholder="Mots clés..." value="<?php echo isset($_GET['physical_description']) ? htmlspecialchars($_GET['physical_description']) : ''; ?>">
                
                <label for="personal_info">Informations personnelles :</label>
                <input type="text" name="personal_info" id="personal_info" placeholder="Mots clés..." value="<?php echo isset($_GET['personal_info']) ? htmlspecialchars($_GET['personal_info']) : ''; ?>">
                
                <button type="submit">Appliquer le filtre</button>
            </form>
        </div>

        <div class="content">
            <div class="search-bar">
                <h1>Recherche de profils</h1>
                <form method="GET" action="recherche.php">
                    <input type="text" name="query" placeholder="Rechercher des utilisateurs..." value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
                    <input type="hidden" name="gender" value="<?php echo isset($_GET['gender']) ? htmlspecialchars($_GET['gender']) : ''; ?>">
                    <input type="hidden" name="relationship_status" value="<?php echo isset($_GET['relationship_status']) ? htmlspecialchars($_GET['relationship_status']) : ''; ?>">
                    <input type="hidden" name="residence" value="<?php echo isset($_GET['residence']) ? htmlspecialchars($_GET['residence']) : ''; ?>">
                    <input type="hidden" name="profession" value="<?php echo isset($_GET['profession']) ? htmlspecialchars($_GET['profession']) : ''; ?>">
                    <input type="hidden" name="age_min" value="<?php echo isset($_GET['age_min']) ? htmlspecialchars($_GET['age_min']) : ''; ?>">
                    <input type="hidden" name="age_max" value="<?php echo isset($_GET['age_max']) ? htmlspecialchars($_GET['age_max']) : ''; ?>">
                    <input type="hidden" name="physical_description" value="<?php echo isset($_GET['physical_description']) ? htmlspecialchars($_GET['physical_description']) : ''; ?>">
                    <input type="hidden" name="personal_info" value="<?php echo isset($_GET['personal_info']) ? htmlspecialchars($_GET['personal_info']) : ''; ?>">
                    <button type="submit">Rechercher</button>
                </form>
            </div>

            <?php
		function calculateAge($birthdate) {
		    $birthdate = new DateTime($birthdate);
		    $today = new DateTime();
		    $age = $today->diff($birthdate)->y;
		    return $age;
		}

		function isUserBlocked($user_id_session, $user_id) {
		    $filename = 'blocked_users.txt';
		    if (!file_exists($filename)) {
		        return false;
		    }
		    $file = fopen($filename, 'r');
		    if ($file) {
		        while (($line = fgets($file)) !== false) {
		            $data = explode(',', trim($line));
		            if (count($data) == 2 && $data[0] == $user_id_session && $data[1] == $user_id) {
		                fclose($file);
		                return true;
		            }
		        }
		        fclose($file);
		    }
		    return false;
		}

		if (isset($_GET['query']) || isset($_GET['gender']) || isset($_GET['relationship_status']) || isset($_GET['residence']) || isset($_GET['profession']) || isset($_GET['age_min']) || isset($_GET['age_max']) || isset($_GET['physical_description']) || isset($_GET['personal_info'])) {
		    $query = strtolower(trim($_GET['query']));
		    $gender = strtolower(trim($_GET['gender']));
		    $relationship_status = strtolower(trim($_GET['relationship_status']));
		    $residence = strtolower(trim($_GET['residence']));
		    $profession = strtolower(trim($_GET['profession']));
		    $age_min = trim($_GET['age_min']);
		    $age_max = trim($_GET['age_max']);
		    $physical_description = strtolower(trim($_GET['physical_description']));
		    $personal_info = strtolower(trim($_GET['personal_info']));
		    $filename = '../txt/utilisateurs.txt';

		    if (file_exists($filename)) {
			$file = fopen($filename, 'r');
			if ($file) {
			    $profilesFound = false; // Variable to track if any profiles are found
			    echo '<h2>Résultats de la recherche :</h2>';
			    echo '<div class="profile-container">';

			    while (($line = fgets($file)) !== false) {
				$data = explode(',', $line);
				$user_id = $data[0];
				$first_name = $data[1];
				$last_name = $data[2];
				$email = $data[3];
				$password = $data[4];
				$gender_user = strtolower($data[5]);
				$birthdate = $data[6];
				$profession_user = strtolower($data[7]);
				$residence_user = strtolower($data[8]);
				$relationship_status_user = strtolower($data[9]);
				$physical_description_user = strtolower($data[10]);
				$personal_info_user = strtolower($data[11]);
				$photo_address = $data[12];
				$user_type = $data[13];
				$ban = $data[14];

				$age_user = calculateAge($birthdate);

				// Combine user details into a single string for easy searching
				$user_data = strtolower($first_name . ' ' . $last_name . ' ' . $email . ' ' . $profession_user . ' ' . $residence_user . ' ' . $physical_description_user . ' ' . $personal_info_user);

				// Check if the query matches any part of the user data
				$match = true;

				if ($query && strpos($user_data, $query) === false) {
				    $match = false;
				}
				if ($gender && $gender_user != $gender) {
				    $match = false;
				}
				if ($relationship_status && $relationship_status_user != $relationship_status) {
				    $match = false;
				}
				if ($residence && strpos($residence_user, $residence) === false) {
				    $match = false;
				}
				if ($profession && strpos($profession_user, $profession) === false) {
				    $match = false;
				}
				if (($age_min && $age_user < $age_min) || ($age_max && $age_user > $age_max)) {
				    $match = false;
				}
				if ($physical_description && strpos($physical_description_user, $physical_description) === false) {
				    $match = false;
				}
				if ($personal_info && strpos($personal_info_user, $personal_info) === false) {
				    $match = false;
				}

				// Exclude banned users
				if ($ban == 'oui') {
				    $match = false;
				}

				// Exclude the connected user's profile and blocked users
				if (($user_id_session && $user_id == $user_id_session) || isUserBlocked($user_id_session, $user_id)) {
				    $match = false;
				}

				if ($match) {
				    $profilesFound = true; // A profile is found
				    echo '<div class="profile-card" onclick="location.href=\'profil.php?id=' . htmlspecialchars($user_id) . '\'">';
				    echo '<div class="profile-image-container">';
				    echo '<img src="' . htmlspecialchars($photo_address) . '" alt="Photo de ' . htmlspecialchars($first_name) . '">';
				    echo '</div>';
				    echo '<div class="profile-details">';
				    echo '<h3>' . htmlspecialchars($first_name) . ' ' . htmlspecialchars($last_name) . '</h3>';
				    echo '<p>Âge: ' . $age_user . ' ans</p>';
				    echo '<p>Profession: ' . htmlspecialchars($profession_user) . '</p>';
				    echo '<p>Résidence: ' . htmlspecialchars($residence_user) . '</p>';
				    echo '<p>Statut relationnel: ' . htmlspecialchars($relationship_status_user) . '</p>';
				    echo '</div>';
				    echo '</div>';
				}
			    }

			    if (!$profilesFound) {
				// No profiles found
				echo '<p>Aucun profil trouvé.</p>';
			    }

			    echo '</div>';
			    fclose($file);
			} else {
			    echo '<p>Impossible d\'ouvrir le fichier des utilisateurs.</p>';
			}
		    } else {
			echo '<p>Le fichier des utilisateurs n\'existe pas.</p>';
		    }
		}
		?>

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
</body>
</html>
