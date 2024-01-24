# Centralien

Ce module contient des actions générales utiles pour permettre à l'appli de vérifier les droits et infos des centraliens.

## Actions

Les actions possibles sont :

- [getUserData](#getuserdata) : renvoie les données de l'utilisateur associé à l'identifiant
- [getRights](#getrights) : renvoie les droits d'un utilisateur
- [listActions](#listactions) : renvoie la présente liste

### getUserData

#### Paramètre supplémentaire

- `id` : email/identifiant de l'utilisateur (si on décide d'utiliser le SSO de MyECL c'est email pour tout le monde)

#### Erreurs supplémentaires

- `101` : identifiant manquant ou invalide
- `102` : identifiant inconnu

#### Requête

```JSON
{
    "module": "centralien",
    "action": "getUserData",
    "id": "matthieu.massardier@ecl21.ec-lyon.fr",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie un objet d'infos à propos de l'utilisateur lié à la clé correspondant au modele suivant :

```JSON
{
    "prenom" : "<prénom d'apres la table user>",
    "responsable": bool,
    "vp": <id du sport dont il est vp>|null,
    "id_ecole": <id de l'école de l'utilisateur>,
    "id_equipe": <id concurrent de l'équipe de l'utilisateur>|null,
    "id_sport": <id du sport de l'utilisateur>|null
}
```

##### Ex

```JSON
{
    "error": 0,
    "user": {
        "prenom" : "Matthieu",
        "responsable": 1,
        "vp": null,
        "id_ecole": 14,
        "id_equipe": null,
        "id_sport": null
    },
    "hash": "528476a5fdfb77a4691a26b03f83f38ad52dd618"
}
```

### getrights

Utilisation très similaire à [getUserData](#getuserdata), juste que cette action ne renvoie pas les données de l'utilisateur.

#### Paramètre supplémentaire

- `id` : email/identifiant de l'utilisateur (si on décide d'utiliser le SSO de MyECL c'est email pour tout le monde)

#### Erreurs supplémentaires

- `101` : identifiant manquant ou invalide
- `102` : identifiant inconnu

#### Requête

```JSON
{
    "module": "centralien",
    "action": "getRights",
    "id": "matthieu.massardier@ecl21.ec-lyon.fr",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie un objet d'infos résumé à propos de l'utilisateur lié à la clé, correspondant au modele suivant :

```JSON
{
    "prenom" : "<prénom d'apres la table user>",
    "responsable": bool,
    "vp": <id du sport dont il est vp>|null
}
```

##### Ex

```JSON
{
    "error": 0,
    "user": {
        "prenom" : "Matthieu",
        "responsable": 1,
        "vp": null,
    },
    "hash": "528476a5fdfb77a4691a26b03f83f38ad52dd618"
}
```

### listActions

#### Requête

```JSON
{
    "module": "centralien",
    "action": "listActions",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie la liste des actions disponibles dans l'objet `actions`.  

##### Ex

```JSON
{
    "error": 0,
    "actions": {
        "listActions": "Return all actions available in general module",
        "getUserData": "Return user\'s basic piece of data associated to key",
        "getRights": "Returns a simple object with user\'s rights",
    },
    "hash": "6253fc6b0f618d592931b56d261f77c9ef2568c7"
}
```
