# Challenger

# Déploiement

## Déploiement via Docker Container
Le challenger a été dockerisé pour l'édition 2020 par Gryffo. Se référer aux fichiers de config dans le projet `docker_config` pour cette partie.
Placer dans un volume Docker le code du site, et la database dans un autre (obtenir un shell dans le container mariadb et importer la structure .sql).

## Déploiement en localhost / sur VM
Installer un serveur MySQL sur la machine en question + Apache + PHP (et dépendences - cf Dockerfile dans le projet `docker_config`).

## Points communs déploiements

* Créer un dossier `sessions/` avec **0777** CHMOD  (cookies de sessions stockés dans ce fichier, ils sont régulièrement supprimés par une tâche CRON)
* Copier `.htaccess.dist` vers `.htaccess` et le configurer (`RewriteBase /` qui est parfois à modifier selon les configurations effectuées)
* Copier `includes/_constantes.php.dist` vers `includes/_constantes.php` et le remplir (LOCAL correspond au site deploye en local sur vos machines pour le developpement)
* Configurer MySQL (username, password, database...) 
* Importer la structure .sql de la base de données

# Tâches CRON Challenger
Créer une tâche CRON sur un serveur qui toutes les minutes fait appel à /cron (script qui vérifie si nouveau mail, exécute les backups, supprime les cookies dans sessions...)
Voici l'entrée à ajouter à une crontab : 
`* * * * * curl -m 120 -s https://challenger.challenge-centrale-lyon.fr/cron  >/dev/null 2>&1`

# Problèmes connus : 

## Cookies de sessions
* Cookies de sessions dans sessions folder posent problème si pas supprimés régulièrement : plusieurs milliers de petits fichiers créés = plus d'inodes dispoibles sur l'hôte
* Tâche CRON ajoutée par Gryffo qui supprime le contenu de sessions
* Dans l'idéal supprimer la génération de ces fichiers (mais fonctionnalites de certaines pages qui reposent sur leur existence quand on navigue le site je crois...)

## Add to table centraliens columns gourde and vegetarien
## Add to column utilisateur colum sexe pour les centraliens 
ALTER TABLE utilisateurs ADD COLUMN sexe enum('h','f') DEFAULT NULL;

## CAS ECL non fonctionnel
* DSI de l'ECL filtre à présent les accès CAS ECL : nous ne pouvons plus utiliser cela pour vérifier l'identité des centraliens.
* On utilise maintenant MyECL avec titan

## Export des participants en excel: issue avec colonne soiree
Code non portable d'une année sur l'autre : car le test dépend du nom du package
Chaque année il faudra donc modifier ce test pour qu'il soit correct.
CF commit de Methye `d41ca10534b7c649b839dcbc89c6bcb4a6b0395c` qui a ajouté cette colonne

# TODO

 - Ajouter la possibilité d'avoir des coachs
 - Pour les gens vraiment déter, faire un nouveau challenger, Cf. propositions ci-dessous
 - Ajouter des presets tarifs

# Améliorations à prendre en considération si reprogrammation d'un nouveau Challenger
* Focus RGPD, RESTFUL API
* Documentation/commentaires plus importante du code, comment le déployer et fonctionnement général (tuto)
* Création de vrais comptes persos pour chaque participant (et pas génération d'un lien unique vers leur profil), ainsi chaque participant aurait un compte qui pourrait être utilisé dans l'appli, pour le système de paiement... Possibilité de donner la main beaucoup plus tôt au participant à son inscription
* Revoir module tournois : génération des poules, des matches, des tableaux (automatiser dans la mesure du possible)
* Ajouter plus de modularité : pour éviter les work-around actuels sur tous les différents packages et options (soirée, gourde...), calcul des points pour le classement ...
* Intégration inventaire tentes ? du site de contrôle de l'application, des écrans, des navettes, des perms ? système de paiement et rechargement
* Ajout tableau de bord pour les challengers ? liste des choses à faire...
* Fonctionnalité SMS (si le budget le permet)
* Simplifier création écoles, sports, tarifs, quotas...
* Page contacts : upload direct depuis le challenger des photos

