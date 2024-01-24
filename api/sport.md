# Sport

Ce module contient des actions liées aux sports et aux équipes.

## Actions

Les actions possibles sont :

- [getSports](#getsports) : renvoie la liste de tous les sports
- [getSport](#getsport) : renvoie les informations d'un sport
- [getEquipesSport](#getequipessport) : renvoie toutes les équipes d'un sport
- [S_getSportifsSport](#s_getsportifssport) : renvoie tous les sportifs d'un sport
- [S_getCapitaines](#s_getcapitaines) : renvoie tous les capitaines d'éqiupes
- [listActions](#listactions) : renvoie la présente liste

### getSports

#### Requête

```JSON
{
    "module": "sport",
    "action": "getSports",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie une liste d'objets sports au format suivant:

```JSON
{
    "id": <id du sport>,
    "sport": "<nom du sport>",
    "sexe": "m|h|f",
    "individuel": 0|1,
    "infos": "<informations à l'intention des sportifs en markdown>"
}
```

##### Ex

```JSON
{
    "error": 0,
    "sports": [
        {
            "id": 7,
            "sport": "Raid",
            "sexe": "m",
            "individuel": 0,
            "infos": "bla bla bla"
        },
        {
            "id": 8,
            "sport": "Ski",
            "sexe": "m",
            "individuel": 1,
            "infos": "bla bla bla"
        },
        {
            "id": 10,
            "sport": "Badminton",
            "sexe": "m",
            "individuel": 0,
            "infos": "bla bla bla"
        },
        {
            "id": 11,
            "sport": "Volley",
            "sexe": "f",
            "individuel": 0,
            "infos": "bla bla bla"
        },
        {
            "id": 12,
            "sport": "Escalade",
            "sexe": "m",
            "individuel": 1,
            "infos": "bla bla bla"
        },
        [...]
    ],
    "hash": "4d4378c5997a47162e8d61c259a82cae5d107bcb"
}
```

### getSport

#### Paramètre supplémentaire

- `id` : id du sport

#### Erreurs supplémentaires

- `101` : ID du sport manquant ou invalide
- `102` : ID du sport inconnu

#### Requête

```JSON
{
    "module": "sport",
    "action": "getSport",
    "id": 7,
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie un objet sport au format suivant:

```JSON
{
    "sport": "<nom du sport>",
    "sexe": "m|h|f",
    "individuel": 0|1,
    "infos": "<informations à l'intention des sportifs en markdown>"
}
```

##### Ex

```JSON
{
    "error": 0,
    "sport": {
        "sport": "Raid",
        "sexe": "m",
        "individuel": 0,
        "infos": "#Raid\n\nLe raid est un sport de combat qui se pratique en équipe de 2.\n\n##Règles\n\nLes règles sont simples : il faut être le premier à arriver à la fin du parcours.\n\n##Parcours\n\nLe parcours est composé de 3 épreuves : course à pied, VTT et canoë.\n\n##Matériel\n\nLe matériel est fourni par l'organisation.\n\n##Inscription\n\nL'inscription se fait par équipe de 2.\n\n##Contact\n\nPour plus d'informations, contactez le responsable du sport : raid@chl",
    },
    "hash": "b2bb591d595e1bc195d10836ce1b854ee164c34c"
}
```

(et merci à chatGPT pour le texte généré de l'enfer)

### S_setSportInfo

#### Paramètres supplémentaires

- `id` : id du sport
- `infos` : informations à l'intention des sportifs en markdown

#### Erreurs supplémentaires

- `101` : ID du sport manquant ou invalide
- `102` : ID du sport inconnu
- `103` : Informations manquantes

#### Requête

```JSON
{
    "module": "sport",
    "action": "S_setSportInfo",
    "id": id_sport,
    "infos": "<texte md pour sportifs>",
    "action_token": "ca3616980df73b7eeb04cfd196dc082a747cd755",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Si tout s'est bien passé, l'action informe simplement de la réussite de l'opération.

##### Ex

```JSON
{
    "error": 0,
    "message": "Sport's infos updated",
    "hash": "b2bb591d595e1bc195d10836ce1b854ee164c34c"
}
```

### getEquipesSport

#### Paramètre supplémentaire

- `id` : id du sport

#### Erreurs supplémentaires

- `101` : ID du sport manquant ou invalide
- `102` : ID du sport inconnu

#### Requête

```JSON
{
    "module": "sport",
    "action": "getEquipesSport",
    "id": 30,
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie une liste d'objet équipe au format suivant:

```JSON
{
    "id": <id de *concurrent*>,
    "id_ecole": <id de l'école>,
    "nom": "<nom de l'école>",
    "label": "<nom de l'équipe>",
    "effectif": <nb. de personnes dans l'équipe>
}
```

##### Ex

```JSON
{
    "error": 0,
    "equipes": [
        {
            "id": 36,
            "id_ecole": 77,
            "nom": "EPFL",
            "label": "N\u00b01",
            "effectif": 2
        },
        {
            "id": 37,
            "id_ecole": 77,
            "nom": "EPFL",
            "label": "N\u00b02",
            "effectif": 2
        },
        {
            "id": 70,
            "id_ecole": 22,
            "nom": "Chimie Paristech",
            "label": "N\u00b01",
            "effectif": 2
        },
        {
            "id": 87,
            "id_ecole": 22,
            "nom": "Chimie Paristech",
            "label": "N\u00b02",
            "effectif": 2
        },
        {
            "id": 195,
            "id_ecole": 62,
            "nom": "ESPCI",
            "label": "N\u00b01",
            "effectif": 2
        },
        [...]
    ],
    "hash": "3224f1361855a05dbbb94ab9ad9ee2207ebf43b9"
}
```

### S_getSportifsSport

#### Paramètre supplémentaire

- `id` : id du sport

#### Erreurs supplémentaires

- `101` : ID du sport manquant ou invalide
- `102` : ID du sport inconnu

#### Requête

```JSON
{
    "module": "sport",
    "action": "S_getSportifsSport",
    "id": 7,
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie une liste d'objet sportif au format suivant:

```JSON
{
    "id": <id participant>,
    "nom": "<nom du sportif>",
    "prenom": "<prénom du sportif>",
    "sexe": "h|f"
}
```

##### Ex

```JSON
{
    "error": 0,
    "sportifs": [
        {
            "id": 1,
            "nom": "Massardier",
            "prenom": "Matthieu",
            "sexe": "h"
        },
        [...]   // volontairement faux, trop sensible
    ],
    "hash": "f95ce30554e3427db56e38f1dddf85987a6f3679"
}
```

### S_getCapitaines

#### Requête

```JSON
{
    "module": "sport",
    "action": "S_getCapitaines",
    "action_token": "ca3616980df73b7eeb04cfd196dc082a747cd755",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie une liste d'objet capitaines au format suivant:

```JSON
{
    "id": <id participant>,
    "nom": "<nom du capitaine>",
    "prenom": "<prénom du capitaine>",
    "sexe": "h|f"
}
```

##### Ex

```JSON
{
    "error": 0,
    "capitaines": [
        {
            "id": 1,
            "nom": "Massardier",
            "prenom": "Matthieu",
            "sexe": "h"
        },
        [...]   // volontairement faux, trop sensible
    ],
    "hash": "f95ce30554e3427db56e38f1dddf85987a6f3679"
}
```

### listActions

#### Requête

```JSON
{
    "module": "sport",
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
        "listActions": "Return all actions available in sport module",
        "getSports": "Return list of sports with id",
        "getSport": "Return sport associated to an ID",
        "getEquipesSport": "Return all equipes associated to a sport",
        "S_getSportifsSport": "Return all sportifs associated to a sport (secured action)",
        "S_getCapitaines": "Return all capitaines (secured action)"
    },
    "hash": "861f1ee44019b0fef048d1649d58cd87fad2b6b2"
}
```
