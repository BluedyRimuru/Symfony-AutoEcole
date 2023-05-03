````

    ___   __  ____________  ________________  __    ______
   /   | / / / /_  __/ __ \/ ____/ ____/ __ \/ /   / ____/
  / /| |/ / / / / / / / / / __/ / /   / / / / /   / __/   
 / ___ / /_/ / / / / /_/ / /___/ /___/ /_/ / /___/ /___   
/_/  |_\____/ /_/  \____/_____/\____/\____/_____/_____/   
                                                          
````

# Symfony AutoEcole

## 🦈 • Technologies utilisées

- HTML 5
- PHP 8 [Download](https://www.php.net/)
- CSS 3
- JavaScript
- Symfony [Download](https://symfony.com/doc/current/setup.html)
- Figma [Site du model](https://www.figma.com/file/UBAmY0QgEw3Bw47FiJok8c/Auto-Ecole---Figma?node-id=0%3A1)
- Mailtrap [Voir](https://mailtrap.io/)
- 
## 🔧 • Installation

Ce projet a entièrement été crée sous Symfony vous aurez donc besoin de celui-ci. [Voir](https://symfony.com/doc/current/setup.html)

- Cloner le projet
```bash
$ git clone git@github.com:ort-montreuil/BST-SIO-G7-2023-AutoEcole-Web.git
```
### 🐳 • Docker
- Installation de docker
```bash
$ docker-compose create #Création du conteneur
$ docker stop $(docker ps -a -q) #Stopper tout les conteneurs
$ docker-compose start #Démarrer les conteneurs
```
### 🛢 • Base de données

- Installation des vendors et du fichier .env
```bash
# Configurez la partie "doctrine/doctrine-bundle" # et "symfony/mailer"
$ cp .env-example .env 
#Installation des vendors
$ composer require symfony/runtime 
```
Pour récuperer le MAILER_DSN, se rendre sur [MailerTrap](https://mailtrap.io/) 
et sélectionner "Symfony 5+" dans la partie "Integrations"

- Création de la base de données
```bash
$ symfony console doctrine:database:create #A utiliser seulement si la base n'a pas été créée
```
```bash
$ symfony console doctrine:migrations:migrate #Création des tables
```
```bash
$ symfony console doctrine:fixtures:load #Génération des données aléatoires
```

## Lancer le projet 
```bash
$ symfony server:start
```
## 👤 • Utilisateurs
Mot de passe des utilisateurs :
```
Admin : "admin"
Moniteur : "michel"
Eleve : "michel"
```



