USE challenger;

begin ### Copie massive des quotas
DELETE FROM ecoles_sports WHERE _message='Ajout massif des quotas';
INSERT INTO ecoles_sports (id_ecole, id_sport, quota_max, quota_reserves, quota_equipes, _date, _auteur, _etat, _message)
SELECT e.id                      as id_ecole,
       s.id                      as id_sport,
       q.quota_max,
       q.quota_reserves,
       q.quota_equipes,
       NOW()                     as _date,
       1                         as _auteur,
       'active'                  as _etat,
       'Ajout massif des quotas' as _message
FROM ecoles as e
         CROSS JOIN sports s
         JOIN ecoles_sports as q on q.id_sport = s.id
WHERE q.id_ecole = :idEcoleSource AND NOT EXISTS(SELECT * FROM ecoles_sports as es where s.id = es.id_sport AND e.id = es.id_ecole) AND q._etat = 'active';
end;

begin ### Get packages
SELECT p.nom, p.prenom, s2.sport, p.sexe, t.logement as FullPackage, e2.nom AS ecole, p.date_inscription, p._date AS derniere_modification
    FROM participants p
    left JOIN sportifs s on s.id_participant = p.id AND s._etat = 'active'
    left JOIN equipes e on s.id_equipe = e.id AND e._etat = 'active'
    left join ecoles_sports es on e.id_ecole_sport = es.id AND es._etat = 'active'
    left join sports s2 on es.id_sport = s2.id and s2._etat = 'active'
    left join tarifs_ecoles te on p.id_tarif_ecole = te.id AND te._etat = 'active'
    left join tarifs t on te.id_tarif = t.id and t._etat = 'active'
    left join ecoles e2 on es.id_ecole = e2.id AND e2._etat = 'active'
WHERE p._etat = 'active' AND p.sportif AND p.id_ecole != 14 AND e2.ecole_lyonnaise = 0
ORDER BY e2.nom, s2.sport, p.sexe, p.nom;
end;

begin ### Get centraliens sans rien

SELECT u.nom, u.prenom, u.email, p.telephone, c.id
    FROM centraliens as c
    JOIN utilisateurs u on c.id_utilisateur = u.id AND u._etat = 'active'
    JOIN participants p on c.id_participant = p.id AND p._etat = 'active'
    WHERE c._etat = 'active' AND c.tombola = 0 AND c.tshirt = 0 AND c.pfsamedi = 0 AND c.soiree = 0 AND p.sportif = 0;
end;

begin ### Beta-ize;You should not use it here, create yourself an other file, or you'll mess everything up and nuke prod
#UPDATE utilisateurs SET email = '' WHERE responsable = 0;
#UPDATE participants SET email = '' WHERE email != 'matt2001@hotmail.fr';
#UPDATE ecoles SET email_corespo = '', email_ecole = '', email_respo = '';
#UPDATE utilisateurs SET email = 'beta.challenger@challenge-centrale-lyon.fr' WHERE login = 'admin';
end


