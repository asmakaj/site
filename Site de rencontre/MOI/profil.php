<?php
session_start();

// Inclure le fichier contenant les fonctions
require_once 'fonctions.php';

// Vérifier si un ID de profil a été passé en paramètre
if (isset($_GET['id'])) {
    $profileId = $_GET['id'];

    // Utiliser la fonction pour obtenir les détails du profil correspondant
    $filePath = "../txt/utilisateurs.txt";
    $profile = getProfileById($filePath, $profileId);
} else {
    $profile = null;
}

// Fonction pour obtenir le profil par ID
function getProfileById($filePath, $id) {
    $file = fopen($filePath, "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            $data = explode(",", trim($line));
            if ($data[0] == $id) {
                fclose($file);
                return [
                    'id' => htmlspecialchars($data[0]),
                    'prenom' => htmlspecialchars($data[1]),
                    'nom' => htmlspecialchars($data[2]),
                    'email' => htmlspecialchars($data[3]),
                    'mot_de_passe' => htmlspecialchars($data[4]),
                    'sexe' => htmlspecialchars($data[5]),
                    'date_naissance' => htmlspecialchars($data[6]), // date de naissance
                    'profession' => htmlspecialchars($data[7]),
                    'ville' => htmlspecialchars($data[8]),
                    'statut' => htmlspecialchars($data[9]),
                    'description_physique' => htmlspecialchars($data[10]),
                    'infos_personnelles' => htmlspecialchars($data[11]),
                    'photo' => htmlspecialchars($data[12]),
                    'type_utilisateur' => htmlspecialchars($data[13])
                ];
            }
        }
        fclose($file);
    }
    return null;
}

// Fonction pour calculer l'âge à partir de la date de naissance
function calculerAge($dateNaissance) {
    $aujourdhui = new DateTime();
    $naissance = new DateTime($dateNaissance);
    $age = $aujourdhui->diff($naissance)->y;
    return $age;
}

// Fonction pour vérifier si un utilisateur est bloqué
function isUserBlocked($userId, $blockedUserId) {
    $filePath = 'blocked_users.txt';
    $file = fopen($filePath, "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            list($blocker, $blocked) = explode(",", trim($line));
            if ($blocker == $userId && $blocked == $blockedUserId) {
                fclose($file);
                return true;
            }
        }
        fclose($file);
    }
    return false;
}

// Fonction pour bloquer un utilisateur
function blockUser($userId, $blockedUserId) {
    $filePath = 'blocked_users.txt';
    $file = fopen($filePath, "a");
    if ($file) {
        fwrite($file, "$userId,$blockedUserId\n");
        fclose($file);
    }
}

// Fonction pour débloquer un utilisateur
function unblockUser($userId, $blockedUserId) {
    $filePath = 'blocked_users.txt';
    $lines = file($filePath, FILE_IGNORE_NEW_LINES);
    $newLines = [];
    foreach ($lines as $line) {
        list($blocker, $blocked) = explode(",", trim($line));
        if ($blocker != $userId || $blocked != $blockedUserId) {
            $newLines[] = $line;
        }
    }
    file_put_contents($filePath, implode("\n", $newLines) . "\n");
}

// Fonction pour signaler un utilisateur
function reportUser($reporterId, $reportedId, $reason) {
    $filePath = 'signalement.txt';
    $file = fopen($filePath, "a");
    if ($file) {
        fwrite($file, "$reporterId,$reportedId,$reason\n");
        fclose($file);
    }
}

// Vérifier le type d'utilisateur connecté
$connectedUserType = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'visiteur';
$connectedUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Rediriger les visiteurs vers la page d'accueil
if ($connectedUserType == 'visiteur') {
    header("Location: index.php");
    exit;
}

// Gérer le blocage, le déblocage et le signalement des utilisateurs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['block'])) {
        blockUser($connectedUserId, $profileId);
    } elseif (isset($_POST['unblock'])) {
        unblockUser($connectedUserId, $profileId);
    } elseif (isset($_POST['report'])) {
        $reason = isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : '';
        reportUser($connectedUserId, $profileId, $reason);
    }
}

// Vérifier si l'utilisateur est bloqué
$isBlocked = isUserBlocked($connectedUserId, $profileId);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="../styles/profil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="#" class="logo">Infinity Love<span>.<span></a>
            <ul class="menu-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="recherche.php">Recherche</a></li>
                <?php
                // Vérifiez si l'utilisateur est connecté
                if (isset($_SESSION['user_type']) && $_SESSION['user_type'] !== 'visiteur') {
                    echo '<li><a href="index.php?action=logout">Déconnexion</a></li>';
                    echo '<li><a href="mon_profil.php">Mon profil</a></li>';
                } else {
                    echo '<li><button onclick="window.location.href=\'inscription.php\'">Inscription</button></li>';
                    echo '<li><button onclick="window.location.href=\'connexion.php\'">Connexion</button></li>';
                }

                if (isset($_SESSION['droits_utilisateur']) && in_array('envoyer_messages', $_SESSION['droits_utilisateur'])) {
                    echo '<li><a href="message.php">Messages</a></li>';
                }
                if (isset($_SESSION['droits_utilisateur']) && in_array('gerer_utilisateurs', $_SESSION['droits_utilisateur'])) {
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
        <div class="container">
            <?php if ($isBlocked): ?>
                <p>Vous avez bloqué cet utilisateur.</p>
                <form method="post">
                    <button type="submit" name="unblock">Débloquer</button>
                </form>
            <?php elseif ($profile): ?>
                <div class="profile-card">
                    <figure class="profile-card-image-container">
                        <img src="<?php echo htmlspecialchars($profile['photo']); ?>" alt="Photo de <?php echo htmlspecialchars($profile['prenom']); ?>" class="profile-card-image">
                    </figure>
                    <div class="profile-card-content">
                        <h2><?php echo htmlspecialchars($profile['prenom']) . " " . htmlspecialchars($profile['nom']); ?></h2>
                        <p>Sexe: <?php echo htmlspecialchars($profile['sexe']); ?></p>
                        <p>Âge: <?php echo calculerAge($profile['date_naissance']); ?> ans</p>
                        <p>Profession: <?php echo htmlspecialchars($profile['profession']); ?></p>
                        <p>Ville: <?php echo htmlspecialchars($profile['ville']); ?></p>
                        <p>Statut: <?php echo htmlspecialchars($profile['statut']); ?></p>
                        
                        <?php if ($connectedUserType == 'administrateur'): ?>
                            <p>Email: <?php echo htmlspecialchars($profile['email']); ?></p>
                            <p>Date de naissance: <?php echo htmlspecialchars($profile['date_naissance']); ?></p>
                            <p>Description physique: <?php echo htmlspecialchars($profile['description_physique']); ?></p>
                            <p>Informations personnelles: <?php echo htmlspecialchars($profile['infos_personnelles']); ?></p>
                            <p>Type d'utilisateur: <?php echo htmlspecialchars($profile['type_utilisateur']); ?></p>
                        <?php endif; ?>

                        <?php if ($connectedUserType == 'abonne' || $connectedUserType == 'administrateur'): ?>
                            <button onclick="window.location.href='messages.php?conversation_with=<?php echo htmlspecialchars($profile['id']); ?>'">Envoyer un message</button>
                            <form method="post" style="display:inline;">
                                <button type="submit" name="block">Bloquer</button>
                            </form>
                            <form method="post" style="display:inline;">
                                <input type="text" name="reason" placeholder="Motif du signalement" required>
                                <button type="submit" name="report">Signaler</button>
                            </form>
                        <?php endif; ?>

                        <?php if ($connectedUserType == 'administrateur' || $connectedUserId == $profile['id']): ?>
                            <button onclick="window.location.href='modifier_utilisateur.php?id=<?php echo htmlspecialchars($profile['id']); ?>'">Modifier le profil</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <p>Profil non trouvé.</p>
            <?php endif; ?>
        </div>
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
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="#">A propos</a></li>
                    <li><a href="#">Nous contacter</a></li>
                    <li><a href="#">Notre équipe</a></li>
                    <li><a href="#">Foire aux questions</a></li>
                </ul>
            </div>
        </div>
        <div class="footerBottom">
            <p>&copy; 2024 Infinity Love - Tous droits réservés</p>
        </div>
    </footer>
</body>
</html>
