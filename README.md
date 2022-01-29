# WaGamMaths

Plateforme de soutien scolaire Mathématiques.

La plateforme doit proposer des questionnaires qui seront disponibles pour des utilisateurs ayant payé l'accès a la plateforme.

## Features attendues:
- Parcour de formulaire (élève)
- Gestion du profil élève.
- Proposer une remédiation a l'élève a la fin du formulaire si nécessaire.
- Gérer des leçons mathématiques
- Parcour de paiement (utilisation d'un prestataire de paiement: exemple Paybox, à définir)
- Gestion de notifications de l'activité et resultats de l'èlèves a ses superviseurs.
- Possibilité pour un professeur de promouvoir la plateforme (possibilité de fournir un lien d'inscription a un élève).
- Page d'accueil présentant les services offert par WaGamMaths.
- Embarquer la plateforme dans des WebView Android et Swift. (Explorer la possibilité de passer par Xamarin).

# Notes techniques
 - Drupal 9
 - PHP 7.4
 - Apache 2.4
 - Mariadb 10.5

## Environnement de developpement :

### Docker

A la racine du projet se trouve un fichier docker-compose orchestrant les différents containers nécessaire au développement de projet.
Commandes utiles:

```bash
#Lancer les container
docker-compose up -d

#(re)build des containers
docker-compose build

#Rentrer dans un container en ligne de commande
docker-exec -it math-online_apache /bin/bash
docker-exec -it math-online_mariadb /bin/bash
```

### Drupal

Au cours des developpements des commandes relatives au drupal peuvent etre utiles

```bash
#Telechargement d'un module
composer require drupal/[NOM_MODULE]

#Installation d'un module
vendor/bin/drush en [NOM_MODULE]
#Désinstallation d'un module
vendor/bin/drush pmu [NOM_MODULE]

#Rebuild des tous les caches
vendor/bin/drush cr

#Export de configuration
vendor/bin/drush config-export
#Import de configuration
vendor/bin/drush config-import

#Mise a jour du schema de base de données (suite a mise a jour du coeur ou d'un module)
vendor/bin/drush updb

#Suppression d'entités
vendor/bin/drupal entity:delete [NOM_ENTITE] --all
```


#### Theming du site

Une arborescence SCSS a été initialisé (web/themes/custom/sub_theme).

Toutes les manipulations de fichiers de style doivent se faire depuis la WSL (et non depuis de container apache).

La WSL doit disposer de node et de npm (https://www.digitalocean.com/community/tutorials/how-to-install-node-js-on-debian-10)

Installation des composants (depuis web/themes/custom/sub_theme)
```bash
#Installer les dépendances (inluant les dépendances de developpement)
npm install --include=dev

#Compiler les scss en css
npm run sass-compile
#Watcher de compilation (lance la compilation a chaque modification d'un fichier .scss)
npm run sass-watch
```


