<?php
session_start();
$user_id_session = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$user_id_session) {
    header("Location: connexion.php");
    exit;
}

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

include 'functionsmessagerie.php';

$users = getUsers();
$selected_conversation = [];
$conversation_with = null;
$conversation_with_name = null;
$conversation_with_photo = null;
if (isset($_GET['conversation_with'])) {
    $conversation_with = $_GET['conversation_with'];
    $selected_conversation = getConversation($user_id_session, $conversation_with);
    $conversation_with_name = getUserNameById($conversation_with);
    $conversation_with_photo = getUserProfilePhotoById($conversation_with);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send'])) {
        $to = $_POST['to'];
        $message = $_POST['message'];

        $isValidRecipient = false;
        foreach ($users as $user) {
            if ($user['id'] === $to) {
                $isValidRecipient = true;
                break;
            }
        }

        if ($isValidRecipient) {
            sendMessage($user_id_session, $to, $message);
            $selected_conversation = getConversation($user_id_session, $to);
        } else {
            echo "Invalid recipient.";
        }
    } elseif (isset($_POST['report'])) {
        reportMessage($_POST['index'], $_POST['reason']);
    } elseif (isset($_POST['block'])) {
        blockUser($user_id_session, $_POST['blocked_user']);
    } elseif (isset($_POST['delete'])) {
        deleteMessage($_POST['index']);
    }
}

function getConversation($user1, $user2) {
    $messages = file('messages.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $conversation = [];
    foreach ($messages as $message) {
        $message_data = explode('|', $message);
        if (($message_data[0] == $user1 && $message_data[1] == $user2) || ($message_data[0] == $user2 && $message_data[1] == $user1)) {
            $conversation[] = [
                'from' => $message_data[0],
                'to' => $message_data[1],
                'message' => $message_data[2],
                'timestamp' => $message_data[3]
            ];
        }
    }

    usort($conversation, function($a, $b) {
        return strtotime($a['timestamp']) - strtotime($b['timestamp']);
    });

    return $conversation;
}

function getUserNameById($id) {
    global $users;
    foreach ($users as $user) {
        if ($user['id'] == $id) {
            return $user['pseudo'];
        }
    }
    return null;
}

function getUserProfilePhotoById($id) {
    global $users;
    foreach ($users as $user) {
        if ($user['id'] == $id) {
            return $user['photo'];
        }
    }
    return null;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Messagerie</title>
    <link rel="stylesheet" type="text/css" href="../styles/messagerie.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<header>
    <nav class="navbar">
        <a href="index.php" class="logo">Infinity Love<span>.<span></a>
        <ul class="menu-links">
            <li><a href="index.php">Accueil</a></li> 
            <?php
            if ($user_type === 'visiteur'|| $user_type === 'utilisateur') {
                echo '<li><a href="#features">Offres</a></li>';
            }
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
                echo '<li><a href="index.php?action=logout" class="btn-logout">Déconnexion</a></li>';
            }
            if (isset($_GET['action']) && $_GET['action'] === 'logout') {
                $_SESSION = array();
                session_destroy();
                header("Location: index.php");
                exit;
            }
            ?>
        </ul>
    </nav>
</header>
<div class="container">
    <div class="sidebar">
        <h2>Profils</h2>
        <ul>
            <?php foreach ($users as $user): ?>
                <li>
                    <a href="?conversation_with=<?= htmlspecialchars($user['id']) ?>">
                        <div class="contact">
                            <div class="profile-photo">
                                <img src="<?= htmlspecialchars($user['photo']) ?>" alt="Photo de profil" class="profile-picture">
                            </div>
                            <div class="contact-name"><?= htmlspecialchars($user['pseudo']) ?></div>
                        </div>
                    </a>
                    <button class="btn-profile" onclick="window.location.href='profil.php?id=<?= htmlspecialchars($user['id']) ?>'">Profil</button>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="main-content">
        <?php if ($conversation_with): ?>
            <div class="conversation-header">
                <div class="profile-photo">
                    <img src="<?= htmlspecialchars($conversation_with_photo) ?>" alt="Photo de profil" class="profile-picture">
                </div>
                <h2>Conversation avec <?= htmlspecialchars($conversation_with_name) ?></h2>
                <button class="btn-profile" onclick="window.location.href='profil.php?id=<?= htmlspecialchars($conversation_with) ?>'">Profil</button>
            </div>
            <div class="conversation-messages">
                <?php foreach ($selected_conversation as $index => $conv_message): ?>
                    <div class="message <?= $conv_message['from'] === $user_id_session ? 'sent' : 'received' ?>">
                        <p><?= nl2br(htmlspecialchars($conv_message['message'])) ?></p>
                        <span class="timestamp"><?= htmlspecialchars($conv_message['timestamp']) ?></span>
                        <?php if ($conv_message['from'] !== $user_id_session): ?>
                            <form method="post" class="report-form">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <input type="text" name="reason" placeholder="Motif du signalement">
                                <button type="submit" name="report" class="btn-report">Signaler</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($conv_message['from'] === $user_id_session): ?>
                            <form method="post" class="delete-form">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <button type="submit" name="delete" class="btn-delete">Supprimer</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="send-message-form">
                <form method="post">
                    <input type="hidden" name="from" value="<?= htmlspecialchars($user_id_session) ?>">
                    <input type="hidden" name="to" value="<?= htmlspecialchars($conversation_with) ?>">
                    <textarea name="message" placeholder="Écrire un message..."></textarea>
                    <button type="submit" name="send" class="btn-send">Envoyer</button>
                </form>
            </div>
        <?php else: ?>
            <div class="no-conversation">
                <p>Sélectionnez un contact pour démarrer une conversation.</p>
            </div>
        <?php endif; ?>
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
