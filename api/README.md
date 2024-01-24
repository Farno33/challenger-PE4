# Documentation  

## Général  

Toutes les requêtes se font en POST sur l'URI [challenger.challenge-centrale-lyon.fr/api](https://challenger.challenge-centrale-lyon.fr/api/) et répondent à cette structure:  

```HTTP
POST /api
Host: challenger.challenge-centrale-lyon.fr
Content-Type: application/x-www-form-urlencoded

module=<nom du module>&action=<nom de l'action>&public_token=<token pulique>&signature=sha1(message&<token privé>)
```

> *Lors de la signature, le contenu du message doit être trié par ordre alphabetique et url-encoded*

>Attention la requête doit est bien **url-encoded**, cependant la doc donne tout en JSON pour des questions ~~evidentes~~ de lisibilité.

Exemple pour génerer la signture en PHP:

```PHP
$url = 'https://challenger.challenge-centrale-lyon.fr/api/';
$public_token = '';
$data = array(
    'module' => 'tournoi',
    'action' => 'getTournois',
    'public_token' => $public_token,
);
ksort($data);
$query_string = http_build_query($data);
$private_token = '';
$signature = sha1($query_string . '&' . $private_token);
$data['signature'] = $signature;
$headers = array(
    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
);
$body = http_build_query($data);
// Il suffit ensuite d'envoyer la requête *POST*
```

La réponse est alors retournée sous forme de JSON:  

```JSON
{
    "error" : 0,
    ...
    "hash" : "sha1(message)"
}
```

où `...` est le contenu de la réponse, dépendant de la requête et du module.

Sauf si une erreur est survenue, dans ce cas le JSON contient:  

```JSON
{
    "error" : (int) ≠ 0,
    "message" : "message d'erreur"
}
```

### Erreurs

Les codes d'erreurs sont les suivants:

- `0` : Aucune erreur
- `1` : Public token manquant
- `2` : Public token invalide
- `3` : Signature manquante ou invalide
- `4` : Module manquant ou invalide (la liste des modules est alors renvoyée dans la réponse sous la liste `modules`)
- `5` : Module introuvable
- `6` : Action manquante ou invalide (la liste des actions est alors renvoyée dans la réponse sous la liste `actions`)
- `7` : Action introuvable
- `8` : Token d'action manquant ou invalide
- `9` : Timestamp manquant ou invalide

## Modules

### Liste des modules

Les différents modules sont:

- [general](general.md) : Tâches générales en lien avec l'API
- [sport](sport.md) : Tâches en lien avec les sports (liste, équipes, sportifs...)
- [ecole](ecole.md) : Tâches en lien avec les écoles (liste, points...)
- [tournoi](tournoi.md) : Tâches en lien avec le tournoi (liste, phases, matchs, classements...)

### Actions

#### ListActions

Tous les modules implémentent l'action `ListActions` qui permet de récupérer la liste des actions disponibles pour ce module.
La réponse est alors retournée sous forme de JSON:  

```JSON
{
    "error" : 0,
    "actions" : [
        "action1",
        "action2",
        ...
    ]
}
```

#### Chronométrées

Les actions dont le nom commence par `T_` sont chronométrées. Elles doivent donc être accompagnées d'un timestamp datant de moins de 30 secondes. Ce timestanmp peut-être récupéré par l'action [getTimestamp](general.md#gettimestamp). Il se glisse dans le JSON sous la clé `timestamp`. Exemple d'utilisation : [T_testTimestamp](general.md#t_testtimestamp).

#### Sécurisées

Les actions dont le nom commence par `S_` sont sécurisées. Elles doivent donc être accompagnées d'un token à usage unique valable 5 minutes. Ce token est récupéré par l'action [T_generateToken](general.md#t_generatetoken). Il se glisse dans le JSON sous la clé `action_token`. Exemple d'utilisation : [S_testToken](general.md#s_testtoken).
