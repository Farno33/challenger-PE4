#!/bin/sh

# On est dans un cas où le code n'existe pas encore
if [ ! -e /var/www/html/includes/_constantes.php.dist ]; then
    echo Téléchargement du code source
    git clone -b $BRANCH https://ChallengerServer:9UnwSa4FQQz39GfxoHsp@gitlab.com/challenge-ecl/challenger.git /var/www/html && echo Téléchargement terminé || echo Téléchargement echoué
fi

echo Démarrage du serveur Apache
# On laisse l'entrypoint normal tourner dans un coin (ça ne sert à rien )
/usr/local/bin/docker-php-entrypoint apache2-foreground &

# On est dans un cas où le site n'est pas encore initialisé (ou que la derniere fois a echoué)
if [ ! -e /var/www/html/includes/_constantes.php -a -e /var/www/html/includes/_constantes.php.dist ]; then
    echo Mise en place des fichiers de configuration
    mkdir -p /var/www/html/sessions /var/www/html/backups /var/www/html/update
    chown www-data:www-data /var/www/html/sessions /var/www/html/backups /var/www/html/update
    cp .htaccess.dist .htaccess

    echo Initialisation de la db
    if mysql -u root -h db -e "CREATE DATABASE IF NOT EXISTS challenger; INSTALL SONAME 'auth_ed25519';"; then
        :
    else
        echo On attends 10 secondes que la db se lance bien et on re-essaye
        sleep 10
    fi
    if mysql -u root -h db -e "CREATE DATABASE IF NOT EXISTS challenger; INSTALL SONAME 'auth_ed25519';"; then
        mysql -u root -h db -D challenger </var/www/html/challenger_structure.sql
        # Mise en place des utilisateurs avec les droits adaptés
        PASS=$(cat /dev/random | base64 | head -c 25) # mdp random de 25 caracteres (A-Za-z0-9+/)

        mysql -u root -h db -e "CREATE OR REPLACE USER 'local' IDENTIFIED BY '${PASS}'; \
                                REVOKE ALL ON *.* FROM 'local'; \
                                GRANT ALL ON challenger.* TO 'local';"
        # On crée l'utilisateur local avec tout les droits unniquement sur la db du challenger
        # On ne limite pas le host ici car le serveur n'est pas sensé être accessible depuis l'extérieur, et cela pose des problemes quand le compose n'as pas le bon nom

        sed -E "s#define\('DB_PASS',(( |\t)*)'.*'\);#define\('DB_PASS',\1'${PASS}'\);#gm;" /var/www/html/includes/_constantes.php.dist >/var/www/html/includes/_constantes.php
        # On met le mdp dans le fichier de config
        unset PASS

        if [ $BRANCH = "dev" ]; then
            # on active le mode debug
            sed -E "s#define\('DEBUG_ACTIVE_ONLINE',(( |\t)*)false\);#define\('DEBUG_ACTIVE_ONLINE',\1true\);#gm;" -i /var/www/html/includes/_constantes.php
        fi

        cat /dev/random | base64 | head -c 25 >/var/www/html/update/pass # mdp random de 25 caracteres (A-Za-z0-9+/)
        # On stocke le mdp admin pour qu'il soit recupéré au premier login
        mysql -u root -h db -e "ALTER USER root IDENTIFIED VIA ed25519 USING PASSWORD('$(cat /var/www/html/update/pass)');"
        echo Site prêt
    else
        echo Erreur lors de la mise en place de la base de donnée
        err=true
    fi
fi


echo Verification de mise à jour
git pull --rebase &> /var/www/html/update/.needsupdate && rm /var/www/html/update/.needsupdate

if [ ! -e /etc/apache2/pkey.pem ]; then
    echo Génération d\'une clé privé pour signer les tokens
    # Ça arrive si tard car il faut collecter un peu d'entropie... on est sur un docker quand même de base...
    openssl genpkey -outform PEM -algorithm RSA-PSS -out /etc/apache2/pkey.pem
    chmod 0600 /etc/apache2/pkey.pem
    chown www-data:www-data /etc/apache2/pkey.pem
fi

echo Démarrage de la boucle infini de simulation de cron
while :; do
    # Patch pour palier au manque de permissions de www-data
    # On pourrait aussi faire un script à base de inotify mais ça compliquerais pas mal le systeme
    if [ -e /var/www/html/update/.needsupdate ]; then
        echo Mise à jour en cours
        git pull --rebase 2>> /var/www/html/update/.needsupdate && rm /var/www/html/update/.needsupdate # On retire si jamais il n'y a pas eu d'erreur
        echo Mise à jour terminée

        if [ -e /var/www/html/update/appseed ]; then sed -E "s/define\('APP_SEED',(( |\t)*)''\);/define\('APP_SEED',\1'$(cat /var/www/html/update/appseed)'\);/" -i /var/www/html/includes/_constantes.php && rm /var/www/html/update/appseed && echo AppSeed pris en compte; fi
        if [ -e /var/www/html/update/OICD-secret ]; then sed -E "s/define\('CONFIG_OIDC_SECRET',(( |\t)*)'<Client_Secret_Token>'\);/define\('CONFIG_OIDC_SECRET',\1'$(cat /var/www/html/update/OICD-secret)'\);/" -i /var/www/html/includes/_constantes.php && rm /var/www/html/update/OICD-secret && echo OICD secret pris en compte; fi
        if [ -e /var/www/html/update/email-user ]; then sed -E "s/define\('EMAIL_USER',(( |\t)*)''\);/define\('EMAIL_USER',\1'$(cat /var/www/html/update/email-user)'\);/" -i /var/www/html/includes/_constantes.php && rm /var/www/html/update/email-user && echo Username email pris en compte; fi
        if [ -e /var/www/html/update/email-pass ]; then sed -E "s/define\('EMAIL_PASS',(( |\t)*)''\);/define\('EMAIL_PASS',\1'$(cat /var/www/html/update/email-pass)'\);/" -i /var/www/html/includes/_constantes.php && rm /var/www/html/update/email-pass && echo Mot de passe email pris en compte; fi
        if [ -e /var/www/html/update/email-mail ]; then sed -E "s/define\('EMAIL_MAIL',(( |\t)*)''\);/define\('EMAIL_MAIL',\1'$(cat /var/www/html/update/email-mail)'\);/" -i /var/www/html/includes/_constantes.php && rm /var/www/html/update/email-mail && echo Adresse email prise en compte; fi
        
        if [ ! -z ${err} ]; then echo Une mise à jour a eu lieu et le site n\'est pas bien configuré. Le docker vas procéder à une réinitialisation \(on quite avec un code ≠ 0\) && exit 1; fi # Force un restart pour refaire l'initialisation s'il y a eu un pb
    fi

    if [ "$BRANCH" = "dev" ]; then
        curl -m 120 -s localhost/cron/
    else
        curl -m 120 -s localhost/cron/ -S >/dev/null 2>&1
    fi
    
    timer=60
    until test $((timer=timer-1)) -eq 0 -o -e "/var/www/html/update/.needsupdate"; do sleep 1; done 
done
