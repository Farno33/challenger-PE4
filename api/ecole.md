# École

Ce module contient des actions liées à l'école.

## Actions

Les actions possibles sont :

- [getPoints](#getpoints) : renvoie les points de toutes les écoles
- [getEcoles](#getecoles) : renvoie la liste des écoles
- [getEcole](#getecole) : renvoie les informations d'une école
- [listActions](#listactions) : renvoie la présente liste

### getPoints

#### Requête

```JSON
{
    "module": "ecole",
    "action": "getPoints",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie une liste d'objets points correspondant au format suivant:

```JSON
{
    "id": <id de l'école concernée>,
    "dd": <nb. de points bonus dd>,
    "pompom": <nb. de points pompom>,
    "fairplay": <nb. de points fairplay>,
    "sport": <nb. de points sport>,
    "total": <nb. de points total>,
    "exaequo": 0|1,
    "classment": <classment>|null (null dans le cas de Centrale Lyon)
}
```

##### Ex

```JSON
{
    "error": 0,
    "ecoles": [
        {
            "id": 1,
            "dd": 0,
            "pompom": 0,
            "fairplay": 0,
            "sports": 0,
            "total": 0,
            "exaequo": 1,
            "classement": 1
        },
        {
            "id": 7,
            "dd": 0,
            "pompom": 0,
            "fairplay": 0,
            "sports": 0,
            "total": 0,
            "exaequo": 1,
            "classement": 1
        },
        {
            "id": 8,
            "dd": 0,
            "pompom": 0,
            "fairplay": 0,
            "sports": 0,
            "total": 0,
            "exaequo": 1,
            "classement": 1
        },
        {
            "id": 9,
            "dd": 0,
            "pompom": 0,
            "fairplay": 0,
            "sports": 0,
            "total": 0,
            "exaequo": 1,
            "classement": 1
        },
        {
            "id": 10,
            "dd": 0,
            "pompom": 0,
            "fairplay": 0,
            "sports": 0,
            "total": 0,
            "exaequo": 1,
            "classement": 1
        },
        {
            "id": 11,
            "dd": 0,
            "pompom": 0,
            "fairplay": 0,
            "sports": 0,
            "total": 0,
            "exaequo": 1,
            "classement": 1
        },
        {
            "id": 12,
            "dd": 0,
            "pompom": 0,
            "fairplay": 0,
            "sports": 0,
            "total": 0,
            "exaequo": 1,
            "classement": 1
        },
        {
            "id": 14,
            "dd": 0,
            "pompom": 0,
            "fairplay": 0,
            "sports": 0,
            "total": 0,
            "exaequo": 1,
            "classement": null
        },
        [...]
    ],
    "hash": "8a0fd8e845aff50b0aa96b16baff5033da0bc6d5"
}
```

### getEcoles

#### Requête

```JSON
{
    "module": "ecole",
    "action": "getEcoles",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie une liste d'objets école correspondant au format suivant:

```JSON
{
    "id": <id>,
    "nom": "<nom>",
    "abreviation": "<abreviation>",
    "ecole_lyonnaise": 0|1,
    "image": "<url de l'image>"|null
}
```

##### Ex

```JSON
{
    "error": 0,
    "ecoles": [
        {
            "id": 1,
            "nom": "Centrale Marseille",
            "abreviation": "",
            "ecole_lyonnaise": 0,
            "image": "http:\/\/challenger.challenge-centrale-lyon.fr\/image\/493c7622d07c8516604f1baf9fa468f739192488"
        },
        {
            "id": 7,
            "nom": "Centrale Sup\u00e9lec",
            "abreviation": "CS",
            "ecole_lyonnaise": 0,
            "image": "http:\/\/challenger.challenge-centrale-lyon.fr\/image\/91088ccd380573f226b22e88c1ae3dd2711e71f0"
        },
        {
            "id": 8,
            "nom": "Centrale Lille",
            "abreviation": "",
            "ecole_lyonnaise": 0,
            "image": "http:\/\/challenger.challenge-centrale-lyon.fr\/image\/e6a9accc0cd0b58f4da47452b446bf483692aaab"
        },
        {
            "id": 9,
            "nom": "Centrale Nantes",
            "abreviation": "",
            "ecole_lyonnaise": 0,
            "image": "http:\/\/challenger.challenge-centrale-lyon.fr\/image\/b96cd58bd62d4b4f46c5ef669a02a20b9c2560d9"
        },
        {
            "id": 10,
            "nom": "Mines Paris",
            "abreviation": "",
            "ecole_lyonnaise": 0,
            "image": "http:\/\/challenger.challenge-centrale-lyon.fr\/image\/1e77a73b89eda4134b34921495cb3439f95dccb1"
        },
        {
            "id": 11,
            "nom": "Polytechnique",
            "abreviation": "X",
            "ecole_lyonnaise": 0,
            "image": "http:\/\/challenger.challenge-centrale-lyon.fr\/image\/f126ddbbfbb4d0ba3db4ace7848cf1415b69afdf"
        },
        {
            "id": 12,
            "nom": "Ponts",
            "abreviation": "",
            "ecole_lyonnaise": 0,
            "image": "http:\/\/challenger.challenge-centrale-lyon.fr\/image\/fee2313b5a45fbb8f7033a79fc28b290e34cbd5e"
        },
        {
            "id": 14,
            "nom": "Centrale Lyon",
            "abreviation": "ECL",
            "ecole_lyonnaise": 1,
            "image": "http:\/\/challenger.challenge-centrale-lyon.fr\/image\/fffcdb2d22869a17dd908f094c5fc247d971ee46"
        },
        [...]
    ],
    "hash": "a13d1ee56b68ef22a3c610f749572c18d080a84a"
}
```

### getEcole

#### Paramètre supplémentaire

- `id` : id de l'école

#### Erreur supplémentaire

- `101` : ID de l'école manquant ou invalide
- `102` : ID de l'école inconnu

#### Requête

```JSON
{
    "module": "ecole",
    "action": "getEcole",
    "id": 14,
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie l'objet école correspondant à l'ID fourni au format suivant:

```JSON
{
    "nom": "<nom>",
    "abreviation": "<abreviation>",
    "ecole_lyonnaise": 0|1,
    "image": "<url de l'image>"
}
```

##### Ex

```JSON
{
    "error": 0,
    "ecole": {
        "nom": "Centrale Lyon",
        "abreviation": "ECL",
        "ecole_lyonnaise": 1,
        "image": "http:\/\/challenger.challenge-centrale-lyon.fr\/image\/fffcdb2d22869a17dd908f094c5fc247d971ee46"
    },
    "hash": "491c482e5bacd825e9406ed3d88d924c6b5478a1"
}
```

### listActions

#### Requête

```JSON
{
    "module": "ecole",
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
        "listActions": "Return all actions available in ecole module",
        "getEcoles": "Return list of ecoles with ID",
        "getEcole": "Return ecole associated to an ID",
        "getPoints": "Return points of each ecole"
    },
    "hash": "bf74e91d02f6cabdc487144c4a1520656adac4b5"
}
```
