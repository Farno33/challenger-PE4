# Général

Ce module contient des actions générales liés à l'utilisation de l'API.

## Actions

Les actions possibles sont :

- [testConnection](#testconnection) : renvoie un message de test
- [getUserData](#getuserdata) : renvoie les données de l'utilisateur associé au token
- [getTimestamp](#gettimestamp) : renvoie le timestamp actuel du serveur
- [T_testTimestamp](#t_testtimestamp) : test le timestamp (contexte d'action chronométrée)
- [T_generateToken](#t_generatetoken) : génère un token pour une requête
- [S_testToken](#s_testtoken) : test le token d'action (contexte d'action sécurisée)
- [listActions](#listactions) : renvoie la présente liste

### testConnection

#### Requête

```JSON
{
    "module": "general",
    "action": "testConnection",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie un message de bienvenue avec le nom de l'utilisateur lié au token.  

##### Ex

```JSON
{
    "error": 0,
    "message": "Welcome Matthieu Massardier",
    "hash": "528476a5fdfb77a4691a26b03f83f38ad52dd618"
}
```

### getUserData

#### Requête

```JSON
{
    "module": "general",
    "action": "getUserData",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie l'objet utilisateur lié au token. Cet objet est défini ainsi :

```JSON
{
    "login": "<login>",
    "nom": "<nom>",
    "prenom": "<prenom>",
    "email": "<email>",
    "telephone": "<telephone>",
    "cas": 1|0,
    "responsable": 1|0,
}
```  

##### Ex

```JSON
{
    "error": 0,
    "user": {
        "login": "Thamite",
        "nom": "Massardier",
        "prenom": "Matthieu",
        "email": "matthieu.massardier@ecl21.ec-lyon.fr",
        "telephone": "+33783438729",
        "cas": 0,
        "responsable": 1
    },
    "hash": "e91007f791ee74ffd2f826357e9ccbaa546407bd"
}
```

### getTimestamp

#### Requête

```JSON
{
    "module": "general",
    "action": "getTimestamp",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Cette action renvoie le timestamp actuel du serveur.  

##### Ex

```JSON
{
    "error": 0,
    "timestamp": 1678224329,
    "hash": "5463a529684feac89d5e787bc76433e4843ab1bc"
}
```

### T_testTimestamp

#### Requête

```JSON
{
    "module": "general",
    "action": "T_testTimestamp",
    "timestamp": 1678224329,
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Lorsque le token est valide (sinon cf. [erreur 8](README.md#erreurs)), cette action renvoie un simple message de salutation.  

##### Ex

```JSON
{
    "error": 0,
    "message": "Welcome on secured action Matthieu Massardier",
    "hash": "9f1184fbba011dc4b3fd4088af58fd5604e0b307"
}
```

### T_generateToken

#### Requête

```JSON
{
    "module": "general",
    "action": "T_generateToken",
    "timestamp": 1678224329,
    "public_token": ,
    "signature": "<signature>"
}
```

#### Réponse

Lorsque le timestamp est assez recent (sinon cf. [erreur 9](README.md#erreurs)), cette action renvoie un token d'action à usage unique, valable 5 minutes.  

##### Ex

```JSON
{
    "error": 0,
    "token": "3501a34a6a084a807cbe19712604d5a0c80e6112",
    "expire": "2023-03-07 21:43:53",
    "hash": "a5632844ad5456da98355fc8f51a333fa8420360"
}
```

### S_testToken

#### Requête

```JSON
{
    "module": "general",
    "action": "S_testToken",
    "action_token": "3501a34a6a084a807cbe19712604d5a0c80e6112",
    "public_token": "98f419ecea61e81b25e97599682b12ef844279de",
    "signature": "<signature>"
}
```

#### Réponse

Lorsque le token est valide (sinon cf. [erreur 8](README.md#erreurs)), cette action renvoie un simple message de salutation.  

##### Ex

```JSON
{
    "error": 0,
    "message": "Welcome on secured action Matthieu Massardier",
    "hash": "9f1184fbba011dc4b3fd4088af58fd5604e0b307"
}
```

### listActions

#### Requête

```JSON
{
    "module": "general",
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
        "getTimestamp": "Return current timestamp on Challenger server",
        "testConnection": "Return a simple welcome to test connection",
        "listActions": "Return all actions available in general module",
        "getUserData": "Return user's pieces of data associated to public token",
        "T_generateToken": "Return a generated token for an unique request",
        "S_testToken": "Return a simple welcome (secured action)",
        "T_testTimestamp": "Return a simple welcome (timed action)"
    },
    "hash": "6253fc6b0f618d592931b56d261f77c9ef2568c7"
}
```
