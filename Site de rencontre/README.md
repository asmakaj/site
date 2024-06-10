# Site de Rencontre

Ce projet de site de rencontre n'a pas de thème précis, c'est un site classique qui a été développé par une équipe de quatre personnes conformément aux spécifications du cahier des charges suivant :

## Cahier des charges

1. **Module "Visiteur"**
   - Un simple visiteur, qui ne se connecte pas au site, n'a accès à rien ou presque. Certains sites lui proposent un "free tour" faisant la publicité du site, rappelant le thème, et lui permettant éventuellement de voir quelques photos d'utilisateurs et d'utilisatrices avant de lui demander de s'inscrire.

2. **Module "Utilisateur"**
   - Pour pouvoir naviguer sur le site, il faut s'inscrire. Une fois connecté, un simple utilisateur se voit attribuer un profil. Il peut le modifier et consulter ceux des autres, mais sans pouvoir communiquer avec eux. Cependant, il peut consulter les offres d'abonnement et choisir l'une d'elles.

3. **Module "Abonné"**
   - Une fois abonné, un utilisateur peut communiquer avec les autres, notamment par messagerie privée. Il peut également savoir qui a consulté son profil.

4. **Module "Administrateur"**
   - Un administrateur a accès à tous les profils utilisateurs, ainsi qu'à tous leurs messages. Il peut modifier et supprimer les profils en cas d'abus, ainsi que les messages suspects.

## Fonctionnalités Implémentées

1. **Module "Visiteur"**
   - Présentation du site et page d'inscription/connexion.

2. **Module "Utilisateur"**
   - Création et gestion de profil avec informations publiques et privées.
   - Recherche et consultation des profils des autres utilisateurs.
   - Consultation des offres d'abonnement et choix d'une offre.

3. **Module "Abonné"**
   - Communication avec d'autres utilisateurs par messagerie privée.
   - Consultation des visiteurs de son profil.

4. **Module "Administrateur"**
   - Gestion des profils des utilisateurs, y compris modification et suppression.
   - Gestion des messages, y compris la réception de signalements et la suppression de messages.

## Fonctionnement Détail du Site Web

### 1. Inscription et Connexion

- **inscription.php**:
  - **Fonctionnalité**: Permet aux nouveaux utilisateurs de créer un compte.
  - **Processus**:
    1. L'utilisateur remplit un formulaire avec des informations telles que nom, prénom, email, mot de passe, etc.
    2. Ces informations sont validées (par exemple, vérification que l'email n'est pas déjà utilisé).
    3. Les données sont enregistrées dans la base de données.
    4. Un email de confirmation peut être envoyé à l'utilisateur.

- **connexion.php**:
  - **Fonctionnalité**: Permet aux utilisateurs existants de se connecter.
  - **Processus**:
    1. L'utilisateur entre son nom d'utilisateur/email et son mot de passe.
    2. Le script vérifie les informations d'identification contre la base de données.
    3. Si les informations sont correctes, l'utilisateur est connecté et redirigé vers la page d'accueil ou son profil.

### 2. Gestion de Profil

- **mon_profil.php**:
  - **Fonctionnalité**: Affiche et permet la modification des informations de profil de l'utilisateur.
  - **Processus**:
    1. L'utilisateur peut voir ses informations personnelles, y compris sa photo de profil, son nom, son email, etc.
    2. L'utilisateur peut modifier ses informations et les soumettre.
    3. Les modifications sont envoyées à `modifier_utilisateur.php` pour traitement.

- **modifier_utilisateur.php**:
  - **Fonctionnalité**: Traite les modifications de profil soumises.
  - **Processus**:
    1. Reçoit les nouvelles informations de profil de `mon_profil.php`.
    2. Valide les nouvelles données (par exemple, vérifie que le nouvel email n'est pas déjà utilisé).
    3. Met à jour les informations dans la base de données.
    4. Confirme les changements à l'utilisateur.

### 3. Abonnements

- **abonnement.php**:
  - **Fonctionnalité**: Gère les abonnements des utilisateurs.
  - **Processus**:
    1. L'utilisateur peut voir les différents plans d'abonnement disponibles.
    2. Sélectionne un plan d'abonnement et entre ses informations de paiement.
    3. Les informations de paiement sont traitées par un service de paiement sécurisé.
    4. L'abonnement est activé, et l'utilisateur reçoit une confirmation.

### 4. Messagerie

- **message.php**:
  - **Fonctionnalité**: Permet l'envoi et la réception de messages privés entre utilisateurs.
  - **Processus**:
    1. L'utilisateur peut rédiger un nouveau message et sélectionner un destinataire.
    2. Le message est envoyé et enregistré dans la base de données.
    3. Le destinataire reçoit une notification et peut lire le message dans sa boîte de réception.

- **functionsmessagerie.php**:
  - **Fonctionnalité**: Contient des fonctions spécifiques à la gestion de la messagerie.
  - **Processus**:
    1. Fonctions pour formater les messages, vérifier les nouveaux messages, marquer les messages comme lus/non lus.
    2. Gestion des discussions et archivage des messages anciens.

### 5. Administration

- **admin.php**:
  - **Fonctionnalité**: Interface d'administration pour la gestion du site.
  - **Processus**:
    1. Les administrateurs peuvent se connecter pour accéder aux outils d'administration.
    2. Gestion des utilisateurs : Ajouter, supprimer ou modifier les comptes utilisateurs.
    3. Modération du contenu : Supprimer ou modifier des messages signalés, gérer les abonnements.
    4. Surveillance du site : Voir les statistiques d'utilisation, surveiller les activités suspectes.

- **recherche.php**:
  - **Fonctionnalité**: Permet de rechercher des utilisateurs ou du contenu.
  - **Processus**:
    1. L'utilisateur entre des critères de recherche (par exemple, nom d'utilisateur, mots-clés).
    2. Le script effectue une recherche dans la base de données.
    3. Les résultats de la recherche sont affichés à l'utilisateur.

### 6. Fonctionnalités supplémentaires

- **index.php**:
  - **Fonctionnalité**: Page d'accueil du site.
  - **Processus**:
    1. Présente un aperçu des fonctionnalités du site.
    2. Affiche des informations générales et des liens vers les différentes sections du site.

- **fonctions.php**:
  - **Fonctionnalité**: Contient des fonctions réutilisables dans plusieurs fichiers du site.
  - **Processus**:
    1. Fonctions de sécurité (par exemple, pour vérifier les sessions utilisateur).
    2. Fonctions de validation des données (par exemple, pour valider les formulaires d'inscription).

### Fichiers de Données

- **utilisateurs.txt**:
  - **Contenu**: Informations des utilisateurs telles que leurs identifiants, noms, emails.
  - **Usage**: Utilisé pour vérifier et gérer les comptes utilisateurs.

- **messages.txt**:
  - **Contenu**: Stocke les messages échangés entre les utilisateurs.
  - **Usage**: Utilisé pour l'affichage des conversations dans la messagerie.

- **blocked_users.txt**:
  - **Contenu**: Liste des utilisateurs bloqués et des utilisateurs qui les ont bloqués.
  - **Usage**: Utilisé pour empêcher les interactions entre certains utilisateurs.

- **reported_messages.txt**:
  - **Contenu**: Messages signalés par les utilisateurs pour des comportements inappropriés ou des violations des règles du site.
  - **Usage**: Utilisé par les administrateurs pour modérer et prendre des mesures appropriées.

### Fonctionnement Global

Le site web fonctionne comme une plateforme sociale où les utilisateurs peuvent s'inscrire, se connecter, gérer leurs profils, envoyer et recevoir des messages privés, s'abonner à des services, et interagir avec d'autres utilisateurs. Les administrateurs du site ont des outils pour gérer les utilisateurs, modérer le contenu, et surveiller l'activité globale du site.

## Technologies

Ce projet a été réalisé principalement sur un environnement Mac et utilise les technologies suivantes :
- HTML, CSS pour l'interface utilisateur.
- PHP pour la logique côté serveur.

Les fichiers sont enregistrés sur GitHub à l'adresse suivante : [https://github.com/asmakaj/site.git](https://github.com/asmakaj/site.git).

## Auteurs

- [@KAJEIOU Asma](https://github.com/asmakaj)
- [@DESTIN Deulyne](lien_vers_profil_GitHub_Auteur2)
- [@DOS SANTOS Emma](lien_vers_profil_GitHub_Auteur3)
- [@GACIL Camil](lien_vers_profil_GitHub_Auteur3)

---
