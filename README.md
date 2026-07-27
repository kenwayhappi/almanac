Almanac Project
Ce projet est une application web développée avec Laravel, un framework PHP puissant et élégant. L'application permet de gérer des pays, des groupements de villages, des villages, des événements, des activités, des publicités, des personnalités, des professionnels, et des contributions, avec des fonctionnalités de recherche, de gestion de contenu, et de génération de PDF.
Prérequis
Avant de commencer, assurez-vous d'avoir les outils suivants installés sur votre machine :

PHP (>= 8.1)
Composer (pour gérer les dépendances PHP)
MySQL ou un autre système de gestion de base de données compatible avec Laravel
Node.js et NPM (pour gérer les assets front-end, si nécessaire)
Git (pour cloner le projet)
Un serveur web comme Apache ou Nginx, ou utilisez le serveur intégré de Laravel (php artisan serve)

Étapes d'installation
Suivez ces étapes pour installer et configurer le projet.
1. Cloner le projet
Clonez le dépôt Git du projet dans votre répertoire local :
git clone <URL_DU_DEPOT>
cd almanac-project

Remplacez <URL_DU_DEPOT> par l'URL de votre dépôt Git.
2. Installer les dépendances PHP
Installez les dépendances PHP listées dans composer.json à l'aide de Composer :
composer install

3. Installer les dépendances spécifiques
Le projet utilise le package barryvdh/laravel-dompdf pour générer des PDF. Installez-le avec la commande suivante :
composer require barryvdh/laravel-dompdf

4. Configurer le fichier d'environnement
Copiez le fichier .env.example pour créer un fichier .env :
cp .env.example .env

Ouvrez le fichier .env et configurez les paramètres suivants selon votre environnement :

Connexion à la base de données :
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=almanac
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe


URL de l'application (assurez-vous qu'elle correspond à votre configuration) :
APP_URL=http://localhost


Clé d'application :Générez une clé d'application unique pour sécuriser votre application :
php artisan key:generate





6. Configurer le stockage des fichiers
Le projet utilise le système de fichiers Laravel pour stocker les images et autres fichiers. Configurez le lien symbolique pour le dossier de stockage :
rm -rf public/storage
php artisan storage:link

Cette commande crée un lien symbolique entre public/storage et storage/app/public pour rendre les fichiers accessibles publiquement.
7. Créer un utilisateur administrateur
Pour accéder au tableau de bord, créez un utilisateur administrateur en insérant les informations suivantes dans la table users via une requête SQL :
INSERT INTO users (name, email, password, created_at, updated_at)
VALUES ('Administrateur', 'admin@almanac.com', '$2y$12$jWI8t5c8sIkCqYzNNFw5e.D5.NfqgVlyGVVVbC.7vefRhPL16Ewsi', NOW(), NOW());


Email : admin@almanac.com
Mot de passe : admin123

Exécutez cette requête dans votre client MySQL (par exemple, phpMyAdmin ou la ligne de commande MySQL).
Alternativement, vous pouvez créer un utilisateur via une commande Artisan personnalisée ou un seeder si vous préférez.
8. Compiler les assets front-end (si nécessaire)
Si le projet utilise des assets front-end (CSS, JavaScript) compilés avec Laravel Mix ou Vite, installez les dépendances Node.js et compilez les assets :
npm install
npm run dev

Note : Si vous utilisez Vite (par défaut dans les versions récentes de Laravel), utilisez npm run dev pour le développement ou npm run build pour la production.
9. Lancer le serveur de développement
Démarrez le serveur de développement Laravel :
php artisan serve

L'application sera accessible à l'adresse http://localhost:8000.
10. Tester l'application

Connexion : Accédez à /login et connectez-vous avec les identifiants de l'administrateur :
Email : admin@almanac.com
Mot de passe : admin123


Tableau de bord : Une fois connecté, vous serez redirigé vers /dashboard, où vous pourrez gérer les pays, groupements, villages, événements, publicités, personnalités, et plus encore.
Pages publiques : Testez les pages publiques comme la page d'accueil (/), la recherche (/recherche), et les pages de groupements ou villages.


Compilez les assets pour la production :
npm run build



Fonctionnalités principales

Gestion des pays : Créez, modifiez et supprimez des pays avec leurs divisions administratives.
Gestion des groupements et villages : Organisez les villages en groupements, avec des informations détaillées (chef, histoire, images).
Événements et contributions : Gérez les événements locaux et leurs contributions financières, avec exportation en PDF.
Publicités : Ajoutez des publicités (vidéo, photo, PDF, texte) affichées sur la page d'accueil ou de recherche.
Personnalités et professionnels : Enregistrez des personnalités notables et des artisans locaux avec leurs coordonnées.
Recherche : Recherchez des villages ou groupements par nom, pays, ou divisions administratives.

Structure du projet

Contrôleurs : Situés dans app/Http/Controllers, ils gèrent la logique métier pour chaque entité (pays, villages, événements, etc.).
Modèles : Situés dans app/Models, ils représentent les entités de la base de données (Country, Village, Event, etc.).
Vues : Situées dans resources/views, elles incluent les interfaces pour le tableau de bord (dashboard/) et les pages publiques.
Routes : Définies dans routes/web.php pour les routes web.
Stockage : Les fichiers (images, PDF) sont stockés dans storage/app/public et accessibles via public/storage.

Commandes utiles

Générer une nouvelle migration :
php artisan make:migration nom_de_la_migration


Réinitialiser la base de données :
php artisan migrate:fresh


Vider le cache :
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear



Problèmes courants et solutions

Erreur 500 (Internal Server Error) : Vérifiez les permissions des dossiers storage et bootstrap/cache. Assurez-vous que le fichier .env est correctement configuré.
Images non affichées : Assurez-vous que le lien symbolique est créé (php artisan storage:link) et que les fichiers sont dans storage/app/public.
Erreur de connexion à la base de données : Vérifiez les paramètres DB_* dans .env et assurez-vous que la base de données almanac existe.
PDF non généré : Vérifiez que barryvdh/laravel-dompdf est installé et que les polices nécessaires sont disponibles sur le serveur.

Contribution
Pour contribuer au projet, suivez les étapes suivantes :

Forkez le dépôt.
Créez une branche pour votre fonctionnalité (git checkout -b feature/nom-de-la-fonctionnalite).
Commitez vos changements (git commit -m 'Ajout de la fonctionnalité XYZ').
Poussez votre branche (git push origin feature/nom-de-la-fonctionnalite).
Créez une Pull Request.

Consultez la documentation Laravel pour plus d'informations sur le développement avec Laravel.
Licence
Ce projet est sous licence MIT, comme le framework Laravel.
