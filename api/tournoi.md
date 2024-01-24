# Tournoi

Ce module contient des actions liées au tournois.

## Actions

Les actions possibles sont :

- [getTournois](#gettournois) : renvoie la liste de tous les tournois
- [getTournoi](#gettournoi) : renvoie les informations d'un tournoi
- [getPhasesTournoi](#getphasestournoi) : renvoie toutes les phases d'un tournoi
- [getPhase](#getphase) : renvoie les informations d'une phase
- [getGroupesPhase](#getgroupesphase) : renvoie tous les groupes d'une phase
- [getConcurrentsPhase](#getconcurrentsphase) : renvoie tous les concurents d'une phase
- [getMatchsPhase](#getmatchsphase) : renvoie tous les matchs d'une phase
- [getMatch](#getMatch) : renvoie les informations d'un match
- [getSportIDFromMatch](#getSportIDFromMatch) : renvoie unniquement l'id du sport d'un match
- [getPodiums](#getpodiums) : renvoie le podium pour un sport
- [getSites](#getsites) : renvoie la liste des sites
- [getSite](#getsite) : renvoie le site où se déroule un tournoi
- [S_setSetsMatch](#s_setsetsmatch) : modifie les sets d'un match
- [listActions](#listactions) : renvoie la présente liste

### getTournois

#### Requête

```JSON
{
    "module": "tournoi",
    "action": "getTournois",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie une liste d'objet tournois au format suivant:

```JSON
{
    "id": <ID du sport>,
    "sport": "<nom du sport>",
    "sexe": "m|h|f",
    "individuel": 0|1,
    "tournoi_initie": 0|1
}
```

##### Ex

```JSON
{
    "error": 0,
    "tournois": [
        {
            "id": 7,
            "sport": "Raid",
            "sexe": "m",
            "individuel": 0,
            "tournoi_initie": 0
        },
        {
            "id": 8,
            "sport": "Ski",
            "sexe": "m",
            "individuel": 1,
            "tournoi_initie": 0
        },
        {
            "id": 10,
            "sport": "Badminton",
            "sexe": "m",
            "individuel": 0,
            "tournoi_initie": 0
        },
        {
            "id": 11,
            "sport": "Volley",
            "sexe": "f",
            "individuel": 0,
            "tournoi_initie": 0
        },
        {
            "id": 12,
            "sport": "Escalade",
            "sexe": "m",
            "individuel": 1,
            "tournoi_initie": 0
        },
        {
            "id": 15,
            "sport": "Volley",
            "sexe": "h",
            "individuel": 0,
            "tournoi_initie": 0
        },
        [...]
    ],
    "hash": "a158aa913cc31aa1a644680dd82b74c30f2b036c"
}
```

### getTournoi

#### Paramètre supplémentaire

- `id` : ID du sport

#### Erreurs supplémentaires

- `101` : ID du sport manquant ou invalide
- `102` : ID du sport inconnu

#### Requête

```JSON
{
    "module": "tournoi",
    "action": "getTournoi",
    "id": <id du sport>,
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie un objet tournoi au format suivant:

```JSON
{
    "sport": "<nom du sport>",
    "sexe": "m|h|f",
    "individuel": 0|1
}
```

##### Ex

```JSON
{
    "error": 0,
    "tournoi": {
        "sport": "Raid",
        "sexe": "m",
        "individuel": 0
    },
    "hash": "0c15f49c21ea39a275ef6a967bfe59c0d06f93d6"
}
```

### getPhasesTournoi

#### Paramètre supplémentaire

- `id` : ID du sport

#### Erreurs supplémentaires

- `101` : ID du sport manquant ou invalide
- `102` : ID du sport inconnu

#### Requête

```JSON
{
    "module": "tournoi",
    "action": "getPhasesTournoi",
    "id": <id du sport>,
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie une liste d'objet phases au format suivant:

```JSON
{
    "id": <id de la phase>,
    "nom": "<nom de la phase>",
    "type": "elimination|poules|championnat|séries",
    "id_phase_suivante": <id de la phase suivante>|null,
    "cloturee": 0|1,

    // si type ∈ {poules, championnat}
    "points_victoire": <points pour une victoire>,
    "points_nul": <points pour un nul>,
    "points_defaite": <points pour une défaite>,
    "points_forfait": <points pour un forfait>
}
```

##### Ex

```JSON
{
    "error": 0,
    "phases": [
        {
            "id": 1,
            "nom": "Finales",
            "type": "elimination",
            "id_phase_suivante": null,
            "cloturee": 0
        },
        {
            "id": 2,
            "nom": "Poules",
            "type": "poules",
            "id_phase_suivante": 1,
            "cloturee": 0,
            "points_victoire": 3,
            "points_nul": 1,
            "points_defaite": 0,
            "points_forfait": -1
        }
    ],
    "hash": "5a69f90f1d052b09a3722de74e2b9d304bf0d487"
}
```

### getPhase

#### Paramètre supplémentaire

- `id` : id de la phase

#### Erreurs supplémentaires

- `101` : ID de la phase manquant ou invalide
- `102` : ID de la phase inconnu

#### Requête

```JSON
{
    "module": "tournoi",
    "action": "getPhase",
    "id": 2,
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie un objet phase au format suivant:

```JSON
{
    "nom": "<nom de la phase>",
    "type": "elimination|poules|championnat|séries",
    "id_phase_suivante": <id de la phase suivante>,
    "cloturee": 0|1,

    // si type ∈ {poules, championnat}
    "points_victoire": <points pour une victoire>,
    "points_nul": <points pour un nul>,
    "points_defaite": <points pour une défaite>,
    "points_forfait": <points pour un forfait>
}
```

##### Ex

```JSON
{
    "error": 0,
    "phase": {
        "nom": "Poules",
        "type": "poules",
        "id_phase_suivante": 1,
        "cloturee": 0,
        "points_victoire": 3,
        "points_nul": 1,
        "points_defaite": 0,
        "points_forfait": -1
    },
    "hash": "baad53ce8bcb9e1d4acd3621b3dc54de1e5e717e"
}
```

### getGroupesPhase

> N'as de sens quasiment que pour les phases de type poules.

#### Paramètre supplémentaire

- `id` : id de la phase

#### Erreurs supplémentaires

- `101` : ID de la phase manquant ou invalide
- `102` : ID de la phase inconnu

#### Requête

```JSON
{
    "module": "tournoi",
    "action": "getGroupesPhase",
    "id": 2,
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie une liste d'objet groupes au format suivant:

```JSON
{
    "id": <id du groupe>,
    "nom": "<nom du groupe>"
}
```

##### Ex

```JSON
{
    "error": 0,
    "groupes": [
        {
            "id": 1,
            "nom": "Poule A"
        },
        {
            "id": 2,
            "nom": "Poule B"
        },
        {
            "id": 3,
            "nom": "Poule C"
        },
        {
            "id": 4,
            "nom": "Poule D"
        }
    ],
    "hash": "33e1207971331881cbe12767d8b4037738bebdd1"
}
```

### getConcurrentsPhase

> Ne renvoit que les concurrents des phases de poules (ceux qui sont dans des groupes)

#### Paramètre supplémentaire

- `id` : id de la phase

#### Erreurs supplémentaires

- `101` : ID de la phase manquant ou invalide
- `102` : ID de la phase inconnu
- `103` : La phase n'est pas de type poules

#### Requête

```JSON
{
    "module": "tournoi",
    "action": "getConcurrentsPhase",
    "id": 2,
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie une liste d'objet concurrents au format suivant:

```JSON
{
    "id": <id du concurrent>,
    "id_sportif": <id du sportif>|null,
    "id_equipe": <id de l'équipe>|null,
    "id_groupe": <id du groupe>,
    "points": <points du concurrent>,
    "exaequo": 0|1,
    "classment": <classment du concurrent dans le groupe>
}
```

##### Ex

```JSON
{
    "error": 0,
    "concurrents": [
        {
            "id": 3,
            "id_sportif": null,
            "id_equipe": 209,
            "id_groupe": 1,
            "points": 0,
            "exaequo": 1,
            "classement": 1
        },
        {
            "id": 4,
            "id_sportif": null,
            "id_equipe": 381,
            "id_groupe": 2,
            "points": 0,
            "exaequo": 1,
            "classement": 1
        },
        [...]
    ],
    "hash": "0e345be0d402d4fec57dc82c6df5f685534c1f2d"
}
```

### getMatchsPhase

#### Paramètre supplémentaire

- `id` : id de la phase

#### Erreurs supplémentaires

- `101` : ID de la phase manquant ou invalide
- `102` : ID de la phase inconnu

#### Requête

```JSON
{
    "module": "tournoi",
    "action": "getMatchsPhase",
    "id": 2,
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie une liste d'objet matchs au format suivant:

```JSON
{
    "id": <id du match>,
    "id_concurrent_a": <id du concurrent A>,
    "id_concurrent_b": <id du concurrent B>,
    "date": "<date du match au format YYYY-MM-DD HH:MM:SS>"|null,
    "id_site": <id du site>|null,
    "sets_a": ["score du concurent A set 1",..]|null,
    "sets_b": ["score du concurent B set 1",..]|null,
    "gagne": "A|B|nul"|null,
    "forfait": 0|1,
    "Commentaire" : "<commentaire accolé au match>"
}
```

##### Ex

```JSON
{
    "error": 0,
    "matchs": [
        {
            "id": 1,
            "id_concurrent_a": 3,
            "id_concurrent_b": 7,
            "date": null,
            "id_site": null,
            "sets_a": null,
            "sets_b": null,
            "gagne": null,
            "forfait": 0
        },
        {
            "id": 2,
            "id_concurrent_a": 3,
            "id_concurrent_b": 11,
            "date": null,
            "id_site": null,
            "sets_a": null,
            "sets_b": null,
            "gagne": null,
            "forfait": 0
        },
        {
            "id": 3,
            "id_concurrent_a": 3,
            "id_concurrent_b": 15,
            "date": null,
            "id_site": null,
            "sets_a": null,
            "sets_b": null,
            "gagne": null,
            "forfait": 0
        },
        {
            "id": 4,
            "id_concurrent_a": 3,
            "id_concurrent_b": 19,
            "date": null,
            "id_site": null,
            "sets_a": [
                "1",
                "5"
            ],
            "sets_b": [
                "5",
                "1"
            ],
            "gagne": "nul",
            "forfait": 0
        },
        {
            "id": 5,
            "id_concurrent_a": 3,
            "id_concurrent_b": 23,
            "date": null,
            "id_site": null,
            "sets_a": [
                ""
            ],
            "sets_b": [
                ""
            ],
            "gagne": "a",
            "forfait": 0
        },
        [...]
    ],
    "hash": "627a0b4ae12eeca5830ce753f46f67fc59cddcbc"
}
```

### getMatch

#### Paramètre supplémentaire

- `id` : id du match

#### Erreurs supplémentaires

- `101` : ID du match manquant ou invalide

#### Requête

```JSON
{
    "module" : "tournoi",
    "action" : "getMatch",
    "id" : 1,
    "public_token" : "98f419ecea61e81b25e97599682b12ef844279de",
    "signature" : "<signature>"
}
```

#### Réponse

Cette action renvoie un seul objet match :

```JSON
{
    "id": <id du match>,
    "id_concurrent_a": <id du concurrent A>,
    "id_concurrent_b": <id du concurrent B>,
    "date": "<date du match au format YYYY-MM-DD HH:MM:SS>"|null,
    "id_site": <id du site>|null,
    "sets_a": ["score du concurent A set 1",..]|null,
    "sets_b": ["score du concurent B set 1",..]|null,
    "gagne": "A|B|nul"|null,
    "forfait": 0|1,
    "Commentaire" : "<commentaire accolé au match>"
}
```

##### Ex

```JSON
{
    "error": 0,
    "match": {
        "id": 1,
        "id_concurrent_a": 3,
        "id_concurrent_b": 7,
        "date": null,
        "id_site": null,
        "sets_a": null,
        "sets_b": null,
        "gagne": null,
        "forfait": 0
    },
    "hash": "c0627a0b4ae12eeca530ce753f4667fc59cddcbc"
}
```

### getSportIDFromMatch

Cette action est destiné à de la verification d'authorisation pour le backend de l'appli (= sur le même serveur). Elle renvoie uniquement l'id du sport d'un match. Faire voyager une requete sur le réseau pour ça serait stupide.

#### Paramètre supplémentaire

- `id` : id du match

#### Erreurs supplémentaires

- `101` : ID du match manquant ou invalide

#### Requête

```JSON
{
    "module": "tournoi",
    "action": "getSportIDFromMatch",
    "id": 1,
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

```JSON
{
    "error": 0,
    "id": int,
    "hash": "c0627a0b4ae12eeca530ce753f4667fc59cddcbc"
}
```

##### Ex

```JSON
{
    "error": 0,
    "id": 7,
    "hash": "c0627a0b4ae12eeca530ce753f4667fc59cddcbc"
}
```

### getPodiums

#### Requête

```JSON
{
    "module": "tournoi",
    "action": "getPodiums",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie une liste d'objet podiums au format suivant:

```JSON
{
    "id": <id du sport>,
    "coeff": <coefficient du podium>|null,
    "id_concurrent1": <id du concurrent 1>|null,
    "id_concurrent2": <id du concurrent 2>|null,
    "id_concurrent3": <id du concurrent 3>|null,
    "id_concurrent3ex" : <id du concurrent 3 ex aequo>|null,
    "ex_12": 0|1|null,
    "ex_23": 0|1|null,
    "ex_3": 0|1|null,
    "id_ecole1": <id de l'école du concurrent 1>|null,
    "diminutif1": "<prenom + 1ere lettre du nom>"|null,
    "label1": "<label de l'équipe>"|null,
    "id_ecole2": <id de l'école du concurrent 2>|null,
    "diminutif2": "<prenom + 1ere lettre du nom>"|null,
    "label2": "<label de l'équipe>"|null,
    "id_ecole3": <id de l'école du concurrent 3>|null,
    "diminutif3": "<prenom + 1ere lettre du nom>"|null,
    "label3": "<label de l'équipe>"|null,
    "id_ecole3ex": <id de l'école du concurrent 3 ex aequo>|null,
    "diminutif3ex": "<prenom + 1ere lettre du nom>"|null,
    "label3ex": "<label de l'équipe>"|null
}
```

##### Ex

```JSON
{
    "error": 0,
    "podiums": [
        {
            "id": 7,
            "coeff": null,
            "id_concurrent1": null,
            "id_concurrent2": null,
            "id_concurrent3": null,
            "id_concurrent3ex": null,
            "ex_12": null,
            "ex_23": null,
            "ex_3": null,
            "id_ecole1": null,
            "diminutif1": null,
            "label1": null,
            "id_ecole2": null,
            "diminutif2": null,
            "label2": null,
            "id_ecole3": null,
            "diminutif3": null,
            "label3": null,
            "id_ecole3ex": null,
            "diminutif3ex": null,
            "label3ex": null
        },
        {
            "id": 8,
            "coeff": null,
            "id_concurrent1": null,
            "id_concurrent2": null,
            "id_concurrent3": null,
            "id_concurrent3ex": null,
            "ex_12": null,
            "ex_23": null,
            "ex_3": null,
            "id_ecole1": null,
            "diminutif1": null,
            "label1": null,
            "id_ecole2": null,
            "diminutif2": null,
            "label2": null,
            "id_ecole3": null,
            "diminutif3": null,
            "label3": null,
            "id_ecole3ex": null,
            "diminutif3ex": null,
            "label3ex": null
        },
        [...]
    ],
    "hash": "1c4b94153af61bc16cfe72c494bc773dd08d5601"
}
```

### getSites

### Requête

```JSON
{
    "module": "tournoi",
    "action": "getSites",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie une liste d'objet sites au format suivant:

```JSON
{
    "id": <id du site>,
    "nom": "<nom du site>",
    "description": "<description du site>",
    "latitude": <latitude du site>,
    "longitude": <longitude du site>
}
```

##### Ex

```JSON
{
    "error": 0,
    "sites": [
        {
            "id": 1,
            "nom": "La Doua",
            "description": "NSM les voleurs",
            "latitude": 45.7852,
            "longitude": 4.87356
        }
    ],
    "hash": "3400b3ad03c0ad74c9018213d450b2c3315c5d74"
}
```

### getSite

#### Paramètre supplémentaire

- `id` : id du site

#### Erreurs supplémentaires

- `101` : ID du site manquant ou invalide
- `102` : ID du site inconnu

#### Requête

```JSON
{
    "module": "tournoi",
    "action": "getSite",
    "id": 1,
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie un objet site au format suivant:

```JSON
{
    "nom": "<nom du site>",
    "description": "<description du site>",
    "latitude": <latitude du site>,
    "longitude": <longitude du site>
}
```

##### Ex

```JSON
{
    "error": 0,
    "site": {
        "nom": "La Doua",
        "description": "NSM les voleurs",
        "latitude": 45.7852,
        "longitude": 4.87356
    },
    "hash": "cd7bf612f9bcb6023ecc12f389cc1d0dc30b6e70"
}
```

### S_setSetsMatch

#### Paramètre supplémentaire

- `id` : id du match
- `sets_a` : scores de A (tableau)
- `sets_b` : scores de B (tableau)

#### Erreurs supplémentaires

- `101` : ID du match manquant ou invalide OU sets_a et sets_b mal renseignés
- `102` : ID du match inconnu

#### Requête

```JSON
{
    "module": "tournoi",
    "action": "S_setSetsMatch",
    "action_token": "3ac5c1589d54c02ead9f96a769c7d16c894c0ce5",
    "sets_a": [0, 2],
    "sets_b": [1, 5],
    "id": 3,
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie simplement message de succes ou d'erreur

##### Ex

```JSON
{
    "error": 0,
    "success": 1,
    "hash": "a167c8e2124439931f9cd53a611827295d83093b"
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
        "listActions": "Return all actions available in tournoi module",
        "getTournois": "Return list of tournois",
        "getTournoi": "Return tournoi associated to an ID",
        "getPhasesTournoi": "Return all phases associated to a tournoi",
        "getPhase": "Return phase associated to an ID",
        "getGroupesPhase": "Return all groupes associated to a phase",
        "getConcurrentsPhase": "Return all concurrents associated to a phase",
        "getMatchsPhase": "Return all matchs associated to a phase",
        "getPodiums": "Return all podiums associated to sports' ID",
        "getSites": "Return all sites where it may have a tournoi",
        "getSite": "Return site where it may have a tournoi with ID",
        "S_setSetsMatch": "Edit sets of a match having its ID"
    },
    "hash": "3759e596d41563be305f918f31380f72b7b254e5"
}
```
