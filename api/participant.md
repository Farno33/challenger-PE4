# Participant

Ce module contient des actions générales utiles pour permettre à l'appli de connaitre son utilisateur.

## Actions

Les actions possibles sont :

- [getUserData](#getuserdata) : renvoie les données de l'utilisateur associé au token
- [checkKey](#checkkey) : vérifie la validité d'une clé
- [listActions](#listactions) : renvoie la présente liste

### getUserData

#### Paramètre supplémentaire

- `key` : clé du participant

#### Erreurs supplémentaires

- `101` : clé du participant manquante ou invalide
- `102` : clé du participant mal formée
- `103` : clé du participant inconnue

#### Requête

```JSON
{
    "module": "participant",
    "action": "getUserData",
    "key": "4964/b91a06bbd1884cd1f136",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie un objet d'infos à propos de l'utilisateur lié à la clé correspondant au modele suivant :

```JSON
{
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
        "prenom": "Matthieu",
        "id_ecole": 14,
        "id_equipe": null,
        "id_sport": null
    },
    "hash": "528476a5fdfb77a4691a26b03f83f38ad52dd618"
}
```

### checkKey

Utilisation très similaire à [getUserData](#getuserdata), juste que cette action ne renvoie pas les données de l'utilisateur.

#### Paramètre supplémentaire

- `key` : clé du participant

#### Erreurs supplémentaires

- `101` : clé du participant manquante ou invalide
- `102` : clé du participant mal formée
- `103` : clé du participant inconnue

#### Requête

```JSON
{
    "module": "participant",
    "action": "checkKey",
    "key": "4964/b91a06bbd1884cd1f136",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie rien, ou une erreur si la clé est invalide.

##### Ex

```JSON
{
    "error": 0,
    "hash": "528476a5fdfb77a4691a26b03f83f38ad52dd618"
}
```

### listActions

#### Requête

```JSON
{
    "module": "participant",
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
        "getUserData": "Return user's pieces of data associated to public token"
    },
    "hash": "6253fc6b0f618d592931b56d261f77c9ef2568c7"
}
```
