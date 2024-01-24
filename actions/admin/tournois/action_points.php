<?php
/* **************************************************************/
/* Sous-module de comptage des points pour le challenge *********/
/* Créé par Matthieu 'Thamite' Massardier et le PE81 2021-2022 **/
/* Matthieu.massardier@ecl21.ec-lyon.fr *************************/
/* **************************************************************/
/* actions/admin/tournois/action_points.php *********************/
/* Liste les matchs, leurs infos et stock les nvx. points *******/
/* **************************************************************/
/* Dernière modification : le 22/03/2022 ************************/
/* **************************************************************/
if (!empty($_POST)) {
    if (isset($_POST['point'])) {
        $value = $_POST['point'];

        if (substr($value, 0, 1) == 'A') {
            if (!empty($_POST['submit'])) {
                $query = $pdo->prepare("INSERT INTO scores (id_match, notation, score_a, score_b, sets_nb, sets_a, sets_b, temps_a, _date, _auteur, _message)
                        VALUES (:id,
                        :notation,
                        coalesce((SELECT s.score_a FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1), 0) + :a, 
                        coalesce((SELECT s.score_b FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1), 0),
                        coalesce((SELECT s.sets_nb FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1), 1),
                        coalesce((SELECT s.sets_a  FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1), 0),
                        coalesce((SELECT s.sets_b  FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1), 0),
                        :tps, 
                        NOW(),
                        :author,
                        :commentaire);");
            } else {
                $query = $pdo->prepare("INSERT INTO scores (id_match, notation, score_a, score_b, temps_a, _date, _auteur, _message)
                        VALUES (:id,
                            :notation,
                            coalesce((SELECT s.score_a FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1), 0) + :a, 
                            coalesce((SELECT s.score_b FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1), 0),
                            :tps, 
                            NOW(),
                            :author,
                            :commentaire);");
            }
            $query->execute([
                'author' => intval($_SESSION['user']['id']),
                'id' => $id_match,
                'notation' => empty($_POST['submit']) ? 'score' : 'sets',
                'commentaire' => $_POST['ameliorer'],
                'a' => intval(substr($value, 1)),
                'tps' => intval(empty($_POST['tempsA']) ? 0 : $_POST['tempsA'])
            ]);
        } else if (substr($value, 0, 1) == 'B') {
            if (!empty($_POST['submit'])) { // = il s'agit d'un sport à sets
                $query = $pdo->prepare("INSERT INTO scores (id_match, notation, score_a, score_b, sets_nb, sets_a, sets_b, temps_b, _date, _auteur, _message)
                        VALUES (:id,
                            :notation,
                            coalesce((SELECT s.score_a FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1), 0), 
                            coalesce((SELECT s.score_b FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1), 0) + :b,
                            coalesce((SELECT s.sets_nb FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1), 1),
                            coalesce((SELECT s.sets_a  FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1), 0),
                            coalesce((SELECT s.sets_b  FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1), 0),
                            :tps, 
                            NOW(),
                            :author,
                            :commentaire);");
            } else {
                $query = $pdo->prepare("INSERT INTO scores (id_match, notation, score_a, score_b, temps_b, _date, _auteur, _message)
                        VALUES (:id,
                            :notation,
                            coalesce((SELECT s.score_a FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1), 0), 
                            coalesce((SELECT s.score_b FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1), 0) + :b,
                            :tps, 
                            NOW(),
                            :author,
                            :commentaire);");
            }
            $query->execute([
                'author' => intval($_SESSION['user']['id']),
                'id' => $id_match,
                'notation' => empty($_POST['submit']) ? 'score' : 'sets',
                'commentaire' => $_POST['ameliorer'],
                'b' => intval(substr($value, 1)),
                'tps' => intval(empty($_POST['tempsB']) ? 0 : $_POST['tempsB'])
            ]);
        } else if ($value == '-') {
            $query = $pdo->prepare("UPDATE scores SET scores._etat = 'desactive' WHERE _etat='active' and id_match=:id ORDER BY id desc limit 1");
            $query->execute([
                'id' => $id_match
            ]);
        }
    } else if (!empty($_POST["JoueurFautifA"]) or !empty($_POST["JoueurFautifB"])) {
        $idFautif = (empty($_POST["JoueurFautifA"])) ? $_POST["JoueurFautifB"] : $_POST["JoueurFautifA"];
        $query = $pdo->prepare("INSERT INTO scores (id_match, notation, score_a, score_b, sets_nb, sets_a, sets_b, penalite, _date, _auteur, _message)
                VALUES (:id,
                    (SELECT s.notation FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1),
                    coalesce((SELECT s.score_a FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1), 0), 
                    coalesce((SELECT s.score_b FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1), 0),
                    (SELECT s.sets_nb FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1),
                    (SELECT s.sets_a  FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1),
                    (SELECT s.sets_b  FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1),
                    :fautif,
                    NOW(),
                    :author,
                    :commentaire);");
        $query->execute([
            'author' => intval($_SESSION['user']['id']),
            'id' => $id_match,
            'commentaire' => $_POST['ameliorer'],
            'fautif' => intval($idFautif)
        ]);
    } else if ($_POST['submit'] == 'Fin de set') {
        $query = $pdo->prepare("SELECT s.score_a, s.score_b FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1");
        $query->execute(['id' => $id_match]);
        $data = $query->fetch();

        $a = intval($data["score_a"] > $data["score_b"]);
        $b = 1 - $a;
        // On part ici du principe qu'aucun set ne peut se finir avec une egalité, à modifier si un sport fait ce genre de supercheries

        $query = $pdo->prepare("INSERT INTO scores (id_match, notation, score_a, score_b, sets_nb, sets_a, sets_b, _date, _auteur, _message)
            VALUES (:id,
                coalesce((SELECT s.notation FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1), :notation),
                0, 
                0,
                coalesce((SELECT s.sets_nb  FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1), 1) + 1,
                coalesce((SELECT s.sets_a   FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1), 0) + :a,
                coalesce((SELECT s.sets_b   FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1), 0) + :b,
                NOW(),
                :author,
                :commentaire);");
        $query->execute([
            'author' => intval($_SESSION['user']['id']),
            'id' => $id_match,
            'notation' => empty($_POST['submit']) ? 'score' : 'set',
            'a' => $a,
            'b' => $b,
            'commentaire' => $_POST['ameliorer']
        ]);
    } else {
        $query = $pdo->prepare("INSERT INTO scores (id_match, notation, score_a, score_b, sets_nb, sets_a, sets_b, _date, _auteur, _message)
            VALUES (:id,
                coalesce((SELECT s.notation FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1), :notation),
                coalesce((SELECT s.score_a  FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1), 0), 
                coalesce((SELECT s.score_b  FROM scores as s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC limit 1), 0),
                (SELECT s.sets_nb FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1),
                (SELECT s.sets_a FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1),
                (SELECT s.sets_b FROM scores AS s WHERE s._etat='active' AND s.id_match = :id ORDER BY s.id DESC LIMIT 1),
                NOW(),
                :author,
                :commentaire);");
        $query->execute([
            'author' => intval($_SESSION['user']['id']),
            'id' => $id_match,
            'notation' => empty($_POST['submit']) ? 'score' : 'set',
            'commentaire' => $_POST['ameliorer']
        ]);
    }
}

if ($id_match == 0) {
    $reponse = $pdo->query("SELECT matchs.id as matchid, e.nom as nomA, eq.label as labelA, e2.nom as nomB, eq2.label as labelB, sports.id as sportid, sports.sport as sport, sports.sexe as sexe, p.nom as phase, matchs3.phase_elimination as etape
        from matchs
                inner join concurrents c on matchs.id_concurrent_a = c.id
                inner join equipes eq on c.id_equipe = eq.id
                inner join ecoles_sports es on eq.id_ecole_sport = es.id
                inner join ecoles e on e.id = es.id_ecole,
            matchs as matchs2
                inner join concurrents c2 on matchs2.id_concurrent_b = c2.id
                inner join equipes eq2 on c2.id_equipe = eq2.id
                inner join ecoles_sports es2 on eq2.id_ecole_sport = es2.id
                inner join ecoles e2 on e2.id = es2.id_ecole,
            matchs as matchs3
                inner join phases p on matchs3.id_phase = p.id
                inner join sports on p.id_sport = sports.id
        where matchs.id = matchs2.id
            and matchs.id = matchs3.id
            and matchs._etat = 'active'");
    $matchEquipe = $reponse->fetchAll();

    $reponse = $pdo->query("SELECT matchs.id as matchid, p.nom as nomA, p.prenom as prenomA, p2.nom as nomB, p2.prenom as prenomB, sports.id as sportid, sports.sport as sport, sports.sexe as sexe, p3.nom as phase, matchs3.phase_elimination as etape
        from matchs
                inner join concurrents c on matchs.id_concurrent_a = c.id
                inner join sportifs s on c.id_sportif = s.id
                inner join participants p on p.id = s.id_participant,
            matchs as matchs2
                inner join concurrents c2 on matchs2.id_concurrent_b = c2.id
                inner join sportifs s2 on c2.id_sportif = s2.id
                inner join participants p2 on p2.id = s2.id_participant,
            matchs as matchs3
                inner join phases p3 on matchs3.id_phase = p3.id
                inner join sports on p3.id_sport = sports.id
        where matchs.id = matchs2.id
            and matchs.id = matchs3.id
            and matchs._etat = 'active'");
    $matchSolo = $reponse->fetchAll();

    require DIR . 'templates/admin/tournois/points_choix_match.php';
} else {
    $data_match = [
        'idMatch' => $id_match
    ];

    $reponse = $pdo->prepare("SELECT sports.individuel, sports.sport, sports.sexe, p.nom, matchs.phase_elimination, sports.id
                                    from matchs 
                                        inner join phases p on matchs.id_phase = p.id 
                                        inner join sports on p.id_sport = sports.id
                                    where matchs.id = :id
                                            AND p._etat = 'active'
                                            AND matchs._etat = 'active'
                                            AND sports._etat = 'active'");
    $reponse->execute(['id' => $data_match['idMatch']]);
    $donnees = $reponse->fetch();

    $data_match = [
        'idMatch' => $data_match['idMatch'],
        'idSport' => $donnees['id'],
        'indiv'   => $donnees["individuel"],
        'sport'   => $donnees['sport'],
        'sexe'    => $donnees['sexe'],
        'phase'   => ($donnees['nom'] . (empty($donnees['phase_elimination']) ? "" : ": " . $donnees['phase_elimination']))
    ];


    if ($data_match['indiv']) {
        $reponse = $pdo->prepare("SELECT p.nom as nomA, p.prenom as prenomA, p2.nom as nomB, p2.prenom as prenomB, scores.score_a as scoreA, scores.score_b as scoreB, scores.sets_a as setsA, scores.sets_b as setsB, scores.sets_nb as setN
            from scores
                    join matchs m on scores.id_match = m.id
                    join concurrents c on m.id_concurrent_a = c.id
                    join sportifs s on c.id_sportif = s.id
                    join participants p on p.id = s.id_participant,
                scores as scores2
                    join matchs m2 on scores2.id_match = m2.id
                    join concurrents c2 on m2.id_concurrent_b = c2.id
                    join sportifs s2 on c2.id_sportif = s2.id
                    join participants p2 on p2.id = s2.id_participant
            where scores.id = scores2.id
                AND scores._etat = 'active'
                AND scores.id_match = :id
            order by scores.id DESC limit 1");
        $reponse->execute(['id' => $data_match['idMatch']]);

        $donnees = $reponse->fetch();

        if (empty($donnees)) {
            $reponse = $pdo->prepare("SELECT p.nom as nomA, p.prenom as prenomA, p2.nom as nomB, p2.prenom as prenomB
                    from matchs
                            inner join concurrents c on matchs.id_concurrent_a = c.id
                            inner join sportifs s on c.id_sportif = s.id
                            inner join participants p on p.id = s.id_participant,
                         matchs as matchs2
                            inner join concurrents c2 on matchs2.id_concurrent_b = c2.id
                            inner join sportifs s2 on c2.id_sportif = s2.id
                            inner join participants p2 on p2.id = s2.id_participant
                    where matchs.id = matchs2.id
                      AND matchs._etat = 'active'
                      AND matchs.id= :id");
            $reponse->execute(['id' => $data_match['idMatch']]);
            $donnees = $reponse->fetch();
        }
    } else {
        $reponse = $pdo->prepare("SELECT e.nom as nomA, eq.label as labelA, e2.nom as nomB, eq2.label as labelB, scores.score_a as scoreA, scores.score_b as scoreB, scores.sets_a as setsA, scores.sets_b as setsB, scores.sets_nb as setN
            from scores
                     join matchs m on scores.id_match = m.id
                     join concurrents c on m.id_concurrent_a = c.id
                     join equipes eq on c.id_equipe = eq.id
                     join ecoles_sports es on eq.id_ecole_sport = es.id
                     join ecoles e on e.id = es.id_ecole,
                scores as scores2
                     join matchs m2 on scores2.id_match = m2.id
                     join concurrents c2 on m2.id_concurrent_b = c2.id
                     join equipes eq2 on c2.id_equipe = eq2.id
                     join ecoles_sports es2 on eq2.id_ecole_sport = es2.id
                     join ecoles e2 on e2.id = es2.id_ecole
            where scores.id = scores2.id
                AND scores._etat = 'active'
                AND scores.id_match = :id
            order by scores.id DESC limit 1");

        $reponse->execute(['id' => $data_match['idMatch']]);
        $donnees = $reponse->fetch();


        if (empty($donnees)) {
            $reponse = $pdo->prepare("SELECT e.nom as nomA, eq.label as labelA, e2.nom as nomB, eq2.label as labelB
                    from matchs
                             inner join concurrents c on matchs.id_concurrent_a = c.id
                             inner join equipes eq on c.id_equipe = eq.id
                             inner join ecoles_sports es on eq.id_ecole_sport = es.id
                             inner join ecoles e on e.id = es.id_ecole,
                         matchs as matchs2
                             inner join concurrents c2 on matchs2.id_concurrent_b = c2.id
                             inner join equipes eq2 on c2.id_equipe = eq2.id
                             inner join ecoles_sports es2 on eq2.id_ecole_sport = es2.id
                             inner join ecoles e2 on e2.id = es2.id_ecole
                    where matchs.id = matchs2.id
                      AND matchs._etat = 'active'
                      AND matchs.id= :id");
            $reponse->execute(['id' => $data_match['idMatch']]);
            $donnees = $reponse->fetch();
        }

        $reponse = $pdo->prepare("SELECT s.id as id, p.nom as nom, p.prenom as prenom FROM matchs
                JOIN concurrents c on c.id = matchs.id_concurrent_a
                JOIN equipes e on e.id = c.id_equipe
                JOIN sportifs s on e.id = s.id_equipe
                JOIN participants p on s.id_participant = p.id
            WHERE matchs._etat = 'active'
                AND matchs.id = :id
            ORDER BY p.nom");
        $reponse->execute(['id' => $data_match['idMatch']]);
        $joueursA = $reponse->fetchAll();

        $reponse = $pdo->prepare("SELECT s.id as id, p.nom as nom, p.prenom as prenom FROM matchs
                JOIN concurrents c on c.id = matchs.id_concurrent_b
                JOIN equipes e on e.id = c.id_equipe
                JOIN sportifs s on e.id = s.id_equipe
                JOIN participants p on s.id_participant = p.id
            WHERE matchs._etat = 'active'
                AND matchs.id = :id
            ORDER BY p.nom");
        $reponse->execute(['id' => $data_match['idMatch']]);
        $joueursB = $reponse->fetchAll();
    }

    $data_match = [
        'sport'      => $data_match['sport'],
        'sexe'       => $data_match['sexe'],
        'idSport'    => $data_match['idSport'],
        'idMatch'    => $data_match['idMatch'],
        'indiv'      => $data_match['indiv'],
        'type'       => in_array($data_match['sport'], array("Volley", "Tennis", "Tennis de table", "Badminton")) ? 'set' : (in_array($data_match['sport'], array("Raid", "Athlétisme", "Natation", "Ski", "Pompoms", "Escalade")) ? 'classement' : 'score'),
        //classement, set, score //
        'phase'      => $data_match['phase'],
        'scoreA'     => empty($donnees["scoreA"]) ? 0 : $donnees["scoreA"],
        'scoreB'     => empty($donnees["scoreB"]) ? 0 : $donnees["scoreB"],
        'setN'       => empty($donnees['setN'])   ? (in_array($data_match['sport'], array("Volley", "Tennis", "Tennis de table", "Badminton")) ? 1 : 0) : $donnees['setN'],
        'setsA'      => empty($donnees['setsA'])  ? 0 : $donnees['setsA'],
        'setsB'      => empty($donnees['setsB'])  ? 0 : $donnees['setsB'],
        'concurentA' => ucfirst($donnees["nomA"]) . ($data_match['indiv'] ? " " . $donnees["prenomA"] : " [" . $donnees['labelA'] . "]"),
        'concurentB' => ucfirst($donnees["nomB"]) . ($data_match['indiv'] ? " " . $donnees["prenomB"] : " [" . $donnees['labelB'] . "]")
    ];

    if ($data_match['type'] == 'classement') {
        $sqlQuery = $pdo->prepare("SELECT s.id as id, p.nom as nom, p.prenom as prenom
                FROM concurrents c
                    JOIN sportifs s on c.id_sportif = s.id
                    JOIN participants p on s.id_participant = p.id
                WHERE c._etat = 'active'
                    AND c.id_sport = :id
                ORDER BY p.nom");
        $sqlQuery->execute(['id' => $data_match['idSport']]);
        $joueur = $sqlQuery->fetchAll();
    }

    if ($data_match['sport'] == 'Pompoms') {
        $sqlQuery = $pdo->prepare("SELECT ecoles.nom, ecoles.id
            FROM ecoles
                     join participants on participants.id_ecole = ecoles.id
            WHERE participants.pompom = 1
              AND participants._etat = 'active'
              AND ecoles._etat = 'active'
            GROUP BY ecoles.nom;");
        $sqlQuery->execute(['id' => $data_match['idSport']]);
        $ecoles = $sqlQuery->fetchAll();
    }

    if ($data_match['setN'] == 0 && $data_match['type'] == 'set') {
        $data_match['setN'] = 1;
    }

    require DIR . 'templates/admin/tournois/points.php';
}