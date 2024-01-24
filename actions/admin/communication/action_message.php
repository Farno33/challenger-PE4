<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/ecoles/action_edition.php *****************/
/* Edition d'une école *************************************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/

$id_message = explode('_', $id_message);

if ($id_message[0] == 'envoi') {
	$message = $pdo->query('SELECT '.
			'en.id, '.
			'en.to, '.
			'en.tentatives, '.
			'en.data, '.
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
			'me.id = en.id_message '.
		'WHERE '.
			'en.id = '.(int) $id_message[1])
			->fetch(PDO::FETCH_ASSOC);

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


	$message['p_prenom'] = ucname($message['p_prenom']);
	$message['p_prenomnom'] = $message['p_prenom'].' '.ucname($message['p_nom']);
	$message['r_prenom'] = ucname($message['r_prenom']);
	$message['r_prenomnom'] = $message['r_prenom'].' '.ucname($message['r_nom']);
	$message['c_autres'] = implode(' / ', array_unique(explode(' / ', empty($message['c_autres']) ? "" : $message['c_autres'])));

	$eqs = explode(',', empty($message['eq_ids']) ? "" : $message['eq_ids']);
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
		
		$capitaine = $equipe['pid'] == $message['p_id'] ? '1' : '0';
		$s_capitaine = $s_capitaine === null ? $capitaine : ($s_capitaine != $capitaine ? '1' : $s_capitaine);
		$s_individuel = $s_individuel === null ? $equipe['individuel'] : ($s_individuel != $equipe['individuel'] ? '0' : $s_individuel);
		$s_sport[] = strip_tags($equipe['sport'].' '.printSexe($equipe['sexe']));
	}

	$message['rs_prenom'] = implode(' / ', $r_prenom);
	$message['rs_prenomnom'] = implode(' / ', $r_prenomnom);
	$message['rs_email'] = implode(' / ', $r_email);
	$message['rs_telephone'] = implode(' / ', $r_telephone);
	$message['s_sport'] = implode(' / ', $s_sport);
	$message['s_sexe'] = $s_sexe;
	$message['s_individuel'] = $s_individuel;
	$message['s_equipe'] = implode(' / ', $eq_label);
	$message['s_capitaine'] = implode(' / ', $eq_capitaine);
	$message['s_is_capitaine'] = $s_capitaine;
	$message['c_batiment'] = substr(empty($message['c_numero']) ? "" : $message['c_numero'], 0, 1);
	$message['c_proprio'] = ucname($message['c_prenom']).' '.ucname($message['c_nom']);
	$message['p_cle'] = preg_replace('#/*$#', '', BASE_URL).'/'.$message['p_id'].'/'.substr(sha1(APP_SEED.$message['e_id'].'_'.$message['p_id']), 0, 20);

	unset($message['eq_ids']);
	unset($message['p_nom']);
	unset($message['r_nom']);
	unset($message['e_id']);
	unset($message['p_id']);
	unset($message['c_nom']);
	unset($message['c_prenom']);
	unset($message['c_email']);
	unset($message['c_telephone']);

	if (!empty($message['data'])) {
		$data = json_decode($message['data'], true);
	} else {
		$data = $message;
	}
	
	unset($message['data']);
	$data_out = [];

	die(empty($message) ? '' : strip_quotes_from_message(cleanText(html_entity_decode($message['contenu']), plainText(html_entity_decode($message['titre'])))));
} 

else {
	$message = $pdo->query('SELECT '.
			'r.id, '.
			'r.titre, '.
			'r.message '.
		'FROM recus AS r '.
		'WHERE '.
			'r.id = '.(int) $id_message[1])
			->fetch(PDO::FETCH_ASSOC);

	die(empty($message) ? '' : strip_quotes_from_message($message['message']));
}


die('');