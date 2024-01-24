<?php
// Ces scripts reposent sur un autre script trouvable dans les configurations docker avec des droits plus haut pour modifier certains fichiers
if (!empty($_POST["APP_SEED"]) && empty(APP_SEED)) {
    // Permet de set l'appseed seulement s'il n'existe pas encore
    file_put_contents(DIR . '/update/appseed', $_POST["APP_SEED"]);
}

if (!empty($_POST["CONFIG_OIDC_SECRET"]) && empty(CONFIG_OIDC_SECRET)) {
    // Permet de set l'appseed seulement s'il n'existe pas encore
    file_put_contents(DIR . '/update/OICD-secret', $_POST["CONFIG_OIDC_SECRET"]);
}

if (!empty($_POST["EMAIL_USER"]) && empty(EMAIL_USER)) {
    // Permet de set l'appseed seulement s'il n'existe pas encore
    file_put_contents(DIR . '/update/email-user', $_POST["EMAIL_USER"]);
}
if (!empty($_POST["EMAIL_PASS"]) && empty(EMAIL_PASS)) {
    // Permet de set l'appseed seulement s'il n'existe pas encore
    file_put_contents(DIR . '/update/email-pass', $_POST["EMAIL_PASS"]);
}
if (!empty($_POST["EMAIL_MAIL"]) && empty(EMAIL_MAIL)) {
    // Permet de set l'appseed seulement s'il n'existe pas encore
    file_put_contents(DIR . '/update/email-mail', $_POST["EMAIL_MAIL"]);
}

if (file_exists(DIR . '/update/.needsupdate')) {
    http_response_code(500);    // Utilisé tel que Github/Gitlab log la reponse (je ne suis pas sûr que ça marche...)
    // Permet de retourner les erreurs s'il y en a eu à la derneriere execution
    echo file_get_contents(DIR . '/update/.needsupdate');
}
// Permet simplement de prévenir le script avec droits supérieurs qu'une mise à jour est prête à être deployée
touch(DIR . '/update/.needsupdate');
