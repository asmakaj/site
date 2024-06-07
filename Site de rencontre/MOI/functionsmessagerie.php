<?php
function getUsers() {
    $users = [];
    $filePath = '../txt/utilisateurs.txt';

    if (($handle = fopen($filePath, "r")) !== false) {
        while (($line = fgets($handle)) !== false) {
            $data = explode(",", trim($line));
            $users[] = ['id' => $data[0], 'pseudo' => $data[1],'photo' => $data[12]];
        }
        fclose($handle);
    }

    return $users;
}

function sendMessage($from, $to, $message) {
    $messageData = [$from, $to, $message, date('Y-m-d H:i:s')];
    $handle = fopen('messages.txt', 'a');
    if ($handle !== false) {
        fputcsv($handle, $messageData, '|');
        fclose($handle);
    }
}

function receiveMessages($user) {
    $messages = [];
    if (($handle = fopen('messages.txt', 'r')) !== false) {
        while (($data = fgetcsv($handle, 1000, "|")) !== false) {
            if ($data[0] === $user || $data[1] === $user) {
                $messages[] = ['from' => $data[0], 'to' => $data[1], 'message' => $data[2], 'timestamp' => $data[3]];
            }
        }
        fclose($handle);
    }
    return $messages;
}

function deleteMessage($index) {
    $messages = [];
    if (($handle = fopen('messages.txt', 'r')) !== false) {
        while (($data = fgetcsv($handle, 1000, "|")) !== false) {
            $messages[] = $data;
        }
        fclose($handle);
    }
    if (isset($messages[$index])) {
        unset($messages[$index]);
    }
    $handle = fopen('messages.txt', 'w');
    if ($handle !== false) {
        foreach ($messages as $message) {
            fputcsv($handle, $message, '|');
        }
        fclose($handle);
    }
}

function blockUser($user, $blockedUser) {
    $blockedUsers = [];
    if (($handle = fopen('blocked_users.txt', 'r')) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $blockedUsers[] = $data;
        }
        fclose($handle);
    }
    $blockedUsers[] = [$user, $blockedUser];
    $handle = fopen('blocked_users.txt', 'w');
    if ($handle !== false) {
        foreach ($blockedUsers as $blocked) {
            fputcsv($handle, $blocked);
        }
        fclose($handle);
    }
}

function getProfilePhotoUrl($userId) {
    // Chemin vers le fichier utilisateurs.txt
    $filename = '../txt/utilisateurs.txt';
    
    // Lire le contenu du fichier ligne par ligne
    $lines = file($filename, FILE_IGNORE_NEW_LINES);
    
    // Parcourir les lignes pour trouver l'utilisateur correspondant
    foreach ($lines as $line) {
        // Séparer les informations de l'utilisateur par des virgules
        $userData = explode(',', $line);
        
        // Vérifier si l'identifiant de l'utilisateur correspond
        if ($userData[0] === $userId) {
            // L'URL de la photo de profil est en avant-dernière position
            return $userData[count($userData) - 2];
        }
    }
    
    // Si l'utilisateur n'est pas trouvé, retourner null
    return null;
}


function isUserBlocked($user, $blockedUser) {
    if (($handle = fopen('blocked_users.txt', 'r')) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            if ($data[0] === $user && $data[1] === $blockedUser) {
                fclose($handle);
                return true;
            }
        }
        fclose($handle);
    }
    return false;
}

function reportMessage($index, $reason) {
    $reportedMessages = [];
    if (($handle = fopen('reported_messages.txt', 'r')) !== false) {
        while (($line = fgets($handle)) !== false) {
            $data = str_getcsv($line);
            $reportedMessages[] = $data;
        }
        fclose($handle);
    }
    $reportedMessages[] = [$index, $reason];
    $handle = fopen('reported_messages.txt', 'w');
    if ($handle !== false) {
        foreach ($reportedMessages as $reported) {
            fputcsv($handle, $reported);
        }
        fclose($handle);
    }
}
?>

