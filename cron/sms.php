<?php

if (SMS_ACTIVE &&
	isConnected()) {

	$envois = $pdo->query('SELECT '.
			'en.id, '.
			'en.to, '.
			'en.tentatives, '.
			'me.titre, '.
			'me.contenu, '.
			//----------
			'p.id AS p_id, '.
			'p.prenom AS p_prenom, '.
			'p.nom AS p_nom, '.
			'p.email AS p_email, '.
			'p.telephone AS p_telephone, '.
			'p.licence AS p_licence, '.
			'p.sexe AS p_sexe, '.
			'p.sportif AS p_sportif, '.
			'p.fanfaron AS p_fanfaron, '.
			'p.pompom AS p_pompom, '.
			'p.cameraman AS p_cameraman, '.
			'p.recharge AS p_recharge, '.
			'p.logeur AS p_logeur, '.
			'CASE WHEN GROUP_CONCAT(si._date) IS NOT NULL THEN 1 ELSE 0 END AS p_signature, '.
			//-----------
			't.nom AS t_nom, '.
			't.tarif AS t_tarif, '.
			't.logement AS t_logement, '.
			//-----------
			'c.numero AS c_numero, '.
			'c.nom AS c_nom, '.
			'c.prenom AS c_prenom, '.
			'c.surnom AS c_surnom, '.
			'c.telephone AS c_telephone, '.
			'c.email AS c_email, '.
			'GROUP_CONCAT(CONCAT(pb.prenom, " ", pb.nom, " (", eb.nom, ")") SEPARATOR " / ") AS c_autres, '.
			//------------
			'e.id AS e_id, '.
			'e.nom AS e_nom, '.
			'i.token AS e_image, '.
			'e.malus AS e_malus, '.
			'e.ecole_lyonnaise AS e_ecole_lyonnaise, '.
			'e.format_long AS e_format, '.
			//-------------
			'r.prenom AS r_prenom, '.
			'r.nom AS r_nom, '.
			'r.email AS r_email, '.
			'r.telephone AS r_telephone, '.
			//--------------
			'GROUP_CONCAT(sp.id_equipe SEPARATOR ",") AS eq_ids '.
		'FROM envois AS en '.
		'LEFT JOIN (participants AS p '.
			'LEFT JOIN signatures AS si ON '.
				'si.id_participant = p.id '.
			'JOIN tarifs_ecoles AS te ON '.
				'te.id = p.id_tarif_ecole AND '.
				'te._etat = "active" '.
			'JOIN tarifs AS t ON '.
				't.id = te.id_tarif AND '.
				't._etat = "active" '.
			'JOIN ecoles AS e ON '.
				'e.id = p.id_ecole AND '.
				'e._etat = "active" '.
			'LEFT JOIN images AS i ON '.
				'i.id = e.id_image AND '.
				'i._etat = "active" '.
			'JOIN utilisateurs AS r ON '.
				'r.id = e.id_respo AND '.
				'r._etat = "active" '.
			'LEFT JOIN (chambres_participants AS cp '.
				'JOIN chambres AS c ON '.
					'c.id = cp.id_chambre AND '.
					'c._etat = "active" '.
				'LEFT JOIN (chambres_participants AS cpb '.
					'JOIN participants AS pb ON '.
						'pb.id = cpb.id_participant AND '.
						'pb._etat = "active" '.
					'JOIN ecoles AS eb ON '.
						'eb.id = pb.id_ecole AND '.
						'eb._etat = "active") ON '.
					'cpb.id_chambre = c.id AND '.
					'cpb._etat = "active" AND '.
					'cpb.id_participant <> cp.id_participant) ON '.
				'cp.id_participant = p.id AND '.
				'cp._etat = "active" '.
			'LEFT JOIN sportifs AS sp ON '.
				'sp.id_participant = p.id AND '.
				'sp._etat = "active") ON '.
			'p.id = en.id_participant AND '.
			'p._etat = "active" '.
		'JOIN messages AS me ON '.
			'me.id = en.id_message AND '.
			'me.type = "sms" '.
		'WHERE '.
			'en.date <= NOW() AND '.
			'en.envoi IS NULL AND '.
			'en.tentatives < '.SMS_FAILS.' AND '.
			'en.echec IS NULL '.
		'GROUP BY en.id '.
		'ORDER BY en.date ASC '.
		'LIMIT 20')->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


	$equipes = $pdo->query('SELECT '.
			'eq.id, '.
			'eq.label, '.
			'eq.id_capitaine, '.
			//-----------
			's.sport, '.
			's.sexe, '.
			's.individuel, '.
			//----------
			'r.nom, '.
			'r.prenom, '.
			'r.email, '.
			'r.telephone, '.
			//----------
			'p.id AS pid, '.
			'p.nom AS pnom, '.
			'p.prenom AS pprenom '.
		'FROM equipes AS eq '.
		'JOIN ecoles_sports AS es ON '.
			'es.id = eq.id_ecole_sport AND '.
			'es._etat = "active" '.
		'JOIN participants AS p ON '.
			'p.id = eq.id_capitaine AND '.
			'p._etat = "active" '.
		'JOIN sports AS s ON '.
			's.id = es.id_sport AND '.
			's._etat = "active" '.
		'JOIN utilisateurs AS r ON '.
			'r.id = s.id_respo AND '.
			'r._etat = "active" '.
		'WHERE '.
			'eq._etat = "active"') 
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


	foreach ($envois as $k => $envoi) {
		$envois[$k]['p_prenom'] = ucname($envoi['p_prenom']);
		$envois[$k]['p_prenomnom'] = $envoi['p_prenom'].' '.ucname($envoi['p_nom']);
		$envois[$k]['r_prenom'] = ucname($envoi['r_prenom']);
		$envois[$k]['r_prenomnom'] = $envoi['r_prenom'].' '.ucname($envoi['r_nom']);
		$envois[$k]['c_autres'] = implode(' / ', array_unique(explode(' / ', $envoi['c_autres'])));

		$eqs = explode(',', $envoi['eq_ids']);
		$eqs = array_unique(array_filter($eqs));
		$s_capitaine = null;
		$r_prenom = [];
		$r_prenomnom = [];
		$r_email = [];
		$r_telephone = [];
		$eq_capitaine = [];
		$s_sport = [];
		$s_sexe = null; 
		$s_individuel = null;
		$eq_label = []; 

		foreach ($eqs as $eq) {
			$equipe = $equipes[$eq];
			$eq_label[] = $equipe['label'];
			$eq_capitaine[] = ucname($equipe['pprenom']).' '.ucname($equipe['pnom']);
			$r_prenom[] = ucname($equipe['prenom']);
			$r_prenomnom[] = ucname($equipe['prenom'].' '.$equipe['nom']);
			$r_email[] = $equipe['email'];
			$r_telephone[] = $equipe['telephone'];
			$s_sexe = $s_sexe === null ? $equipe['sexe'] : ($s_sexe != $equipe['sexe'] ? 'm' : $s_sexe);
			
			$capitaine = $equipe['pid'] == $envoi['p_id'] ? '1' : '0';
			$s_capitaine = $s_capitaine === null ? $capitaine : ($s_capitaine != $capitaine ? '1' : $s_capitaine);
			$s_individuel = $s_individuel === null ? $equipe['individuel'] : ($s_individuel != $equipe['individuel'] ? '0' : $s_individuel);
			$s_sport[] = strip_tags($equipe['sport'].' '.printSexe($equipe['sexe']));
		}

		$envois[$k]['rs_prenom'] = implode(' / ', $r_prenom);
		$envois[$k]['rs_prenomnom'] = implode(' / ', $r_prenomnom);
		$envois[$k]['rs_email'] = implode(' / ', $r_email);
		$envois[$k]['rs_telephone'] = implode(' / ', $r_telephone);
		$envois[$k]['s_sport'] = implode(' / ', $s_sport);
		$envois[$k]['s_sexe'] = $s_sexe;
		$envois[$k]['s_individuel'] = $s_individuel;
		$envois[$k]['s_equipe'] = implode(' / ', $eq_label);
		$envois[$k]['s_capitaine'] = implode(' / ', $eq_capitaine);
		$envois[$k]['s_is_capitaine'] = $s_capitaine;
		$envois[$k]['c_batiment'] = substr($envoi['c_numero'], 0, 1);
		$envois[$k]['c_proprio'] = ucname($envoi['c_prenom']).' '.ucname($envoi['c_nom']);
		$envois[$k]['p_cle'] = preg_replace('#/*$#', '', BASE_URL).'/'.$envoi['p_id'].'/'.substr(sha1(APP_SEED.$envoi['e_id'].'_'.$envoi['p_id']), 0, 20);

		unset($envois[$k]['eq_ids']);
		unset($envois[$k]['p_nom']);
		unset($envois[$k]['r_nom']);
		unset($envois[$k]['e_id']);
		unset($envois[$k]['p_id']);
		unset($envois[$k]['c_nom']);
		unset($envois[$k]['c_prenom']);
		unset($envois[$k]['c_email']);
		unset($envois[$k]['c_telephone']);
	}


	//Attention à la langue
	//Il manque p_malus, p_total, p_retard


	$actualise = $pdo->prepare('UPDATE envois SET '.
			'tentatives = tentatives + 1, '.
			'envoi = NOW(), '.
			'echec = NULL, '.
			'data = :data '.
		'WHERE '.
			'id = :id AND '.
			'envoi IS NULL'); 

	$tentative = $pdo->prepare('UPDATE envois SET '.
			'tentatives = tentatives + 1, '.
			'data = :data '.
		'WHERE '.
			'id = :id AND '.
			'envoi IS NULL'); 

	$echec = $pdo->prepare('UPDATE envois SET '.
			'tentatives = tentatives + :tentative, '.
			'echec = :echec, '.
			'data = :data '.
		'WHERE '.
			'id = :id AND '.
			'envoi IS NULL'); 


	foreach ($envois as $enid => $envoi) {
		$data = $envoi; 
		$data_out = [];

		unset($data['to']);
		unset($data['titre']);
		unset($data['contenu']);
		unset($data['tentatives']);

		$contenu = html_entity_decode(plainText(html_entity_decode($envoi['contenu'])), ENT_NOQUOTES, "UTF-8");
		$to = SMS_FORCED ? SMS_FORCED : ($envoi['to'] ? $envoi['to'] : $envoi['p_telephone']);
		
		if (!isValidSmsPhone($to)) {
			$echec->execute([
				':echec' => 'Destinataire invalide',
				':tentative' => 0,
				':data' => json_encode($data_out),
				':id' => $enid]);
			continue;
		}


		if (($r = sendSms(str_replace(' ', '', getPhone($to)), $contenu)) !== '1') {
			if ($envoi['tentatives'] + 1 >= SMS_FAILS) {
				$echec->execute([
					':echec' => 'Erreur lors de l\'envoi : '.$r,
					':tentative' => 1,
					':data' => json_encode($data_out),
					':id' => $enid]);
			} else {
				$tentative->execute([
					':data' => json_encode($data_out),
					':id' => $enid]);
			}
		} else {
			$actualise->execute(array(
				':data' => json_encode($data_out),
				':id' => 	$enid));
		}
	}
}