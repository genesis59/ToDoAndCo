# ToDoAndCo [![Codacy Badge](https://app.codacy.com/project/badge/Grade/478aec0b55184ab5a9ed477a942c7966)](https://app.codacy.com/gh/genesis59/ToDoAndCo/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
## Environnement de développement
### Prérequis
* git https://git-scm.com/downloads
* composer https://getcomposer.org/
* PHP 8.2
* Symfony CLI https://github.com/symfony-cli/symfony-cli
* yarn https://classic.yarnpkg.com/lang/en/docs/install/#debian-stable
* Un système de gestion de bases de données relationnelles (PosgreSQL, MySQL, ...)
### Installation du projet
1. Cloner le projet à l'aide de la commande git clone via HTTPS:
   ```bash
   git clone https://github.com/genesis59/ToDoAndCo.git
   ```
   ou par SSH nécessite que votre clé SSH soit configurée sur GitHub
   ```bash
   git clone git@github.com:genesis59/ToDoAndCo.git
   ```
   puis entrez dans le projet
   ```bash
   cd ToDoAndCo
   ```
2. Installer les dépendances PHP :
    ```bash
    composer install
    ```
3. Installer les dépendances Javascript :
   ```bash
   yarn install
   yarn build
   ```
4. Variables d'environnement
    1. Copier le fichier .env dans un fichier .env.local
    2. Renseignez avec vos données les variables d'environnement dans le fichier .env.local
        - DATABASE_URL
        - MAILER_DSN
    3. Exemple pour la variable DATABASE_URL avec une base de données PosgreSQL et MAILER_DSN dans le fichier .env exemples avec une base de données MySQL et MailHog:
   ```php
   DATABASE_URL="postgresql://user_name:your_password@localhost:5432/todo_and_co?serverVersion=15&charset=utf8"
   MAILER_DSN=smtp://localhost:1025
   ```
5. Configuration de la base de données locale :
    ```bash
    symfony console doctrine:database:create
    symfony console make:migration
    symfony console doctrine:migrations:migrate
    symfony console doctrine:fixtures:load
   ```
***
6. . Lancement du serveur PHP depuis la racine du projet
   ```bash
   symfony server:start
   ```
7. Pour finir, rendez-vous à l'adresse: https://127.0.0.1:8000

