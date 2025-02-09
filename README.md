# Site de Gestion d'Événements
Bienvenue sur le projet de site de gestion d'événements MirEvent ! Ce projet permet de créer, gérer
et promouvoir des événements. Il est destiné à une utilisation principalement par les jeunes, mais
avec une possibilité d'extension vers des entreprises (ex. : bars, clubs, etc.) pour accroître leur chiffre
d'affaires en attirant des clients et en prenant une commission.
## Technologies utilisées
-
**Frontend** : React
-
**Backend** : Laravel
-
**Base de données** : MySQL
-
**Hébergement** : Local
-
**Authentification** : JWT (JSON Web Tokens)
-
**Gestion des événements** : CRUD complet pour créer, modifier, afficher, et supprimer des
événements
## Prérequis
`
`**
.env
Avant de commencer, vous devez avoir installé les outils suivants :
- https://github.com/ismaelkennedy/eventplatform
_
front (Front-END du projet)
- https://react.dev/learn/installation
- https://vite.dev/guide/
- https://tailwindcss.com/docs/installation/using-vite
- https://laravel.com/docs/11.x/installation
- https://dev.mysql.com/downloads/installer/
## Installation
**1. Clonez ce dépôt sur votre machine locale**
git clone https://github.com/Melissa-mely/event_back.git
front
_
cd event
back/
_
**2. Installer les dépendances Laravel**
Assure-toi d'avoir **Composer** installé
, puis exécute :
composer install
**3. Configurer le fichier
Copie le fichier d’exemple :
cp .env.example .env
Modifie les informations de connexion à la base de données dans **`
.env
`** :

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889(pour macOS) ou 3306(pour windows)
DB_DATABASE=mirevent
DB_USERNAME= yourusername
DB_PASSWORD= yourpassword

CLOUDINARY_URL=cloudinary://281999173195236:8Ghl2TDqLof5ESkFKnBGvOX-pxw@dv5zygmp0
CLOUDINARY_CLOUD_NAME=dv5zygmp0
CLOUDINARY_API_KEY=281999173195236
CLOUDINARY_API_SECRET=8Ghl2TDqLof5ESkFKnBGvOX

ADMIN_USERNAME=melissa
ADMIN_EMAIL=melissa19@gmail.com
ADMIN_PASSWORD=adminpassword123

**4. Créer la base de données**
Si elle n'existe pas encore, crée-la manuellement avec **phpMyAdmin** ou en ligne de commande :
mysql -u root -p -e "CREATE DATABASE nom_de_ta_BD;"

**5. Lancer les migrations et seeders**
Une fois la base configurée, exécute :
php artisan migrate --seed
**Cela va créer les tables et insérer les données initiales.
**

**6. Générer la clé d’application Laravel**
php artisan key:generate

**7. Lancer le serveur Laravel**
Si tout est bien configuré, démarre Laravel avec :
php artisan serve

**Résumé des étapes**
1.
2.
3.
4.
5.
6.
7.
**Cloner le projet** sur la nouvelle machine.
**Installer Composer** (`
composer install`).
**Créer et configurer le fichier
`
`**
.env
.
**Créer la base de données** (`CREATE DATABASE`).
**Lancer migrations et seeders** (`
php artisan migrate --seed`).
**Générer la clé d’application** (`
php artisan key:generate
`).
**Démarrer Laravel** (`
php artisan serve
`).
