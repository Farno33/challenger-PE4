<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8" />
		<title>API Challenger</title>

		<script src="<?php url('assets/js/jquery.min.js'); ?>"></script>
		<style type="text/css">
			table { width:calc(100% - 380px); }
			table td { border: 1px solid #CCC; }
			div { float: left; width: 350px; }
			b { color: red; }
			i { color: green; }
			input[type=text] { width: 100px; }
		</style>
	</head>

	<body>
		<div>
			<input type="text" id="url" value="<?php url('api/', true); ?>" />URL API<br />
			<input type="text" id="private" value="<?php echo !empty($user['private_token']) ? $user['private_token'] : ''; ?>" />Private token<br />
			<input type="text" id="public" value="<?php echo !empty($user['public_token']) ? $user['public_token'] : ''; ?>" />Public token<br />
			<br />

			<div>
				<input type="text" />
				<input type="text" />
				<input type="button" value="X" onclick="deleteField(this)" />
			</div>
			<input type="button" value="Add Field" onclick="addField()" />
			<input type="button" value="Send Form" onclick="sendForm()" /><br />
			<br />

			<input type="button" value="Test Connection" onclick="testConnection()" /><br />
			<input type="button" value="Get Timestamp" onclick="getTimestamp()" /><br />
			<input type="button" value="Test token (secured)" onclick="testToken()" /><br />
			<input type="button" value="Test timestamp (timed)" onclick="testTimestamp()" /><br />
			
			<br />
			<input type="button" value="Generate Token (timed)" onclick="generateToken()" /><br />
			<input type="button" value="Get User Data" onclick="getUserData()" /><br />
			<input type="button" value="Get Ecoles" onclick="getEcoles()" /><br />
			<input type="button" value="Get Sports" onclick="getSports()" /><br />
			
			<!--
			<input type="button" value="Get Equipes" onclick="getEquipes(prompt('Tournoi/Sport ID ?'))" /><br />
			<input type="button" value="Get Sportifs (secured)" onclick="getSportifs(prompt('Tournoi/Sport ID ?'))" /><br />
			<input type="button" value="Get Phases" onclick="getPhases(prompt('Tournoi/Sport ID ?'))" /><br />
			<input type="button" value="Get Matchs" onclick="getMatchs(prompt('Phase ID ?'))" /><br />
			
			<br />
			<input type="button" value="Error No Public Token" onclick="noPublicToken()" /><br />
			<input type="button" value="Error Bad Public Token" onclick="badPublicToken()" /><br />
			<input type="button" value="Error No Signature" onclick="noSignature()" /><br />
			<input type="button" value="Error No Module" onclick="noModule()" /><br />
			<input type="button" value="Error No Action" onclick="noAction()" /><br />
			-->

			
			<br /><br />
			<span></span>
		</div>

		<table style="width:auto"></table>
	</body>

	<script type="text/javascript">
	//A NE PAS FAIRE, le private token ne doit JAMAIS être visible
	//La génération de la signature se faire forcément côté serveur
	//Pour autant il convient de ne pas mettre à disposition une page qui renvoit la signature pour n'importe quelles données en entrée


	var previousDataIn;
	var previousCallback;
	var request = 1;
	var xhr;

	ksort=function(a){var b={},c=[],d=0;for(e in a)c[d++]=e;c=c.sort(),d=c.length;for(var e=0;e<d;e++)b[c[e]]=a[c[e]];return b};
	sha1=function(d){var l=0,a=0,f=[],b,c,g,h,p,e,m=[b=1732584193,c=4023233417,~b,~c,3285377520],n=[],k=unescape(encodeURI(d));for(b=k.length;a<=b;)n[a>>2]|=(k.charCodeAt(a)||128)<<8*(3-a++%4);for(n[d=b+8>>2|15]=b<<3;l<=d;l+=16){b=m;for(a=0;80>a;b=[[(e=((k=b[0])<<5|k>>>27)+b[4]+(f[a]=16>a?~~n[l+a]:e<<1|e>>>31)+1518500249)+((c=b[1])&(g=b[2])|~c&(h=b[3])),p=e+(c^g^h)+341275144,e+(c&g|c&h|g&h)+882459459,p+1535694389][0|a++/20]|0,k,c<<30|c>>>2,g,h])e=f[a-3]^f[a-8]^f[a-14]^f[a-16];for(a=5;a;)m[--a]=m[a]+b[a]|0}for(d="";40>a;)d+=(m[a>>3]>>4*(7-a++%8)&15).toString(16);return d};

	calculateSignature = function(data) {
		var sig;

		delete data['signature'];
		delete data['_debug']; 

		data = ksort(data);
		sig = sha1($.param(data) + '&' + $('#private').val());
		return sig;
	};

	apiSimple = function(dataIn, callback, keepLines) {
		previousDataIn = dataIn;
		previousCallback = callback;

		$('span').html('<b>Request N°' + request++ + '</b><br />');
		for (var i in dataIn) {
			$('span').append('<i>' + i + ' : </i> ' + dataIn[i] + '<br />');
		}
		$('span').append('<input type="button" value="Send same request" onclick="apiSimple(previousDataIn, previousCallback)" />');
		$('span').append('<input type="button" value="Map form" onclick="mapForm(previousDataIn)" />');

		if (xhr) xhr.abort();
		if (!keepLines) $('table tr').remove();

		$('table').append('<tr><th>Loading data (request n°' + (request - 1) + ') ...</th></tr>');

		xhr = $.ajax({
			url: $('#url').val(),
			method: "POST",
			data: dataIn, 
			cache: false,
			error: function() {
				if (!keepLines) $('table tr').remove();
				$('table').append('<tr>' + 
					'<td><b>Error N°X : </b>An unknown error occurered</td>' +
					'</tr>');
			},
			success: function(dataOut) { 
				if (dataOut.error) {
					if (!keepLines) $('table tr').remove();
					$('table').append('<tr>' + 
						'<td><b>Error N°' + dataOut.error + ' : </b>' + dataOut.message + '</td>' +
						'</tr>');
				} else {
					callback(dataOut);
				}
			}
		});
	};

	api = function(dataIn, callback, keepLines) {
		dataIn['public_token'] = $('#public').val();
		dataIn['signature'] = calculateSignature(dataIn);

		apiSimple(dataIn, callback, keepLines);
	};

	addField = function() {
		$('div div:last').after('<div>' + $('div div:first').html() + '</div>');
	};

	deleteField = function(item) {
		if ($('div div').length > 1)
			$(item).parent().remove();
	};

	mapForm = function(data) {
		var i, k;
		$('div div input[type=text]').val('');
		delete data['signature'];
		delete data['public_token'];

		if (!data)
			return;
		
		$('div div input[type=button]').click();
		for (i = 2; i <= Object.keys(data).length; i++)
			addField();
		
		i = 0;
		for (k in data) {
			$('div div:nth(' + i + ') input:first').val(k);
			$('div div:nth(' + i + ') input:nth(1)').val(data[k]);
			i++;
		}
	};

	sendForm = function() {
		var data = {};
		var key, value;

		$('div div').each(function() {
			key = $(this).children('input:first').val();
			value = $(this).children('input:nth(1)').val();

			if (key)
				data[key] = value;
		});

		api(data, function(dataOut) {
			$('table tr').remove();
			$('table').append('<tr>' + 
				'<td>' + displayJsonAsList(dataOut) + '</td>' + 
				'</tr><tr>' + 
				'<td>' + JSON.stringify(dataOut) + '</td>' +
				'</tr>');
		});
	};

	displayJsonAsList = function(json, parent) {
		var string = '', i; 

		for (i in json) {
			string += '<li>' + ($.isArray(json) ? '<i>#' + i + ' : </i>' : '<b>' + i + ' : </b>');
			string += typeof(json[i]) === 'object' ?
				displayJsonAsList(json[i], json) :
				(i == 'image' ? '<img style="height:1em" src="' + json[i] + '" />' : json[i]);
			string += '</li>';
		}

		return '<ul>' + string + '</ul>';
	};

	getEcoles = function() {
		api({
			module: 'ecole',
			action: 'getEcoles'
		}, function(dataOut) {
			var ecoles = dataOut.ecoles;
			var i; 

			$('table tr').remove();
			for (i in ecoles) {
				$('table').append('<tr>' + 
					'<td>' + ecoles[i].id + '</td>' +
					'<td><center>' + (ecoles[i].image ? '<img src="' + ecoles[i].image + '" height="25px" />' : '') + '</center></td>' +
					'<td>' + ecoles[i].nom + '</td>' +
					'<td>' + ecoles[i].abreviation + '</td>' +
					'<td>' + (ecoles[i].ecole_lyonnaise == '1' ? 'Oui' : 'Non') + '</td>' + 
					'</tr>');
			}
		});
	};

	getSports = function() {
		api({
			module: 'sport',
			action: 'getSports'
		}, function(dataOut) {
			var sports = dataOut.sports;
			var sexes = {'h': 'Homme', 'f': 'Femme', 'm': 'Mixte'};
			var i; 

			$('table tr').remove();
			for (i in sports) {
				$('table').append('<tr>' + 
					'<td>' + sports[i].id + '</td>' +
					'<td>' + sports[i].sport + '</td>' +
					'<td>' + sexes[sports[i].sexe] + '</td>' + 
					'<td>' + (sports[i].individuel == '1' ? 'Individuel' : 'Collectif') + '</td>' + 
					'<td><a href="#" onclick="getEquipes(' + sports[i].id + ')">Equipes</a></td>' +
					'<td><a href="#" onclick="getSportifs(' + sports[i].id + ')">Sportifs</a></td>' +
					'<td><a href="#" onclick="getPhases(' + sports[i].id + ')">Phases</a></td>' +
					'</tr>');
			}
		});
	};

	getPhases = function(id) {
		api({
			module: 'tournoi',
			action: 'getPhasesTournoi',
			id: id
		}, function(dataOut) {
			var phases = dataOut.phases;
			var i; 

			$('table tr').remove();
			for (i in phases) {
				$('table').append('<tr>' + 
					'<td>' + phases[i].id + '</td>' +
					'<td>' + phases[i].nom + '</td>' +
					'<td>' + phases[i].type + '</td>' +
					'<td>' + (phases[i].cloturee == '1' ? 'Cloturee' : 'Ouverte') + '</td>' + 
					'<td><a href="#" onclick="getMatchs(' + phases[i].id + ')">Matchs</a></td>' +
					'</tr>');
			}
		});
	};

	getEquipes = function(id) {
		api({
			module: 'sport',
			action: 'getEquipesSport',
			id: id
		}, function(dataOut) {
			var equipes = dataOut.equipes;
			var i; 

			$('table tr').remove();
			for (i in equipes) {
				$('table').append('<tr>' + 
					'<td>' + equipes[i].nom + '</td>' +
					'<td>' + equipes[i].label + '</td>' +
					'</tr>');
			}
		});
	};

	getSportifs = function(id) {
		api({
			module: 'general',
			action: 'getTimestamp'
		}, function(dataOut) {
			api({
				module: 'general',
				action: 'T_generateToken',
				timestamp: dataOut.timestamp
			}, function(dataOut) {
				api({
					module: 'sport',
					action: 'S_getSportifsSport',
					action_token: dataOut.token,
					id: id
				}, function(dataOut) {
					var sportifs = dataOut.sportifs;
					var i; 

					$('table tr').remove();
					for (i in sportifs) {
						$('table').append('<tr>' + 
							'<td>' + sportifs[i].nom + '</td>' +
							'<td>' + sportifs[i].prenom + '</td>' +
							'<td>' + sportifs[i].sexe + '</td>' +
							'</tr>');
					}
				});
			});
		});
	};

	getMatchs = function(id) {
		api({
			module: 'tournoi',
			action: 'getMatchsPhase',
			id: id
		}, function(dataOut) {
			var matchs = dataOut.matchs;
			var i; 

			$('table tr').remove();
			for (i in matchs) {
				$('table').append('<tr>' + 
					'<td>' + matchs[i].id + '</td>' +
					'<td>' + matchs[i].date + '</td>' +
					'<td>' + matchs[i].gagne + '</td>' +
					'<td>' + (matchs[i].forfait == '1' ? 'Forfait' : '') + '</td>' + 
					'</tr>');
			}
		});
	};

	testConnection = function() {
		api({
			module: 'general',
			action: 'testConnection'
		}, function(dataOut) {
			var message = dataOut.message;

			$('table tr').remove();
			$('table').append('<tr>' + 
				'<td>' + message + '</td>' +
				'</tr>');
		});
	};

	getTimestamp = function() {
		api({
			module: 'general',
			action: 'getTimestamp'
		}, function(dataOut) {
			var timestamp = dataOut.timestamp;

			$('table tr').remove();
			$('table').append('<tr>' + 
				'<td>' + timestamp + '</td>' +
				'</tr>');
		});
	};

	generateToken = function() {
		api({
			module: 'general',
			action: 'getTimestamp'
		}, function(dataOut) {
			$('table tr').remove();
			$('table').append('<tr>' + 
				'<td><i>Request ' + (request - 1) + ' : Get Timestamp</i></td>' + 
				'<td>' + dataOut.timestamp + '</td>' +
				'</tr>');

			api({
				module: 'general',
				action: 'T_generateToken',
				timestamp: dataOut.timestamp - 20
			}, function(dataOut) {
				$('table th').parent().remove();
				$('table').append('<tr>' + 
					'<td><i>Request ' + (request - 1) + ' : Generate Token</i></td>' +
					'<td>' + dataOut.token + '</td>' +
					'</tr>');
			}, true);
		});
	};

	getUserData = function() {
		api({
			module: 'general',
			action: 'getUserData'
		}, function(dataOut) {
			var user = dataOut.user;
			var i; 

			$('table tr').remove();
			for (i in user) {
				if (i == "error")
					continue;

				$('table').append('<tr>' + 
					'<td>' + i + '</td>' +
					'<td>' + user[i] + '</td>' +
					'</tr>');
			}
		});
	};

	noPublicToken = function() {
		apiSimple({});
	};

	badPublicToken = function() {
		apiSimple({
			public_token: "public_token"
		});
	};

	noSignature = function() {
		apiSimple({
			public_token: $('#public').val()
		});
	};

	noModule = function() {
		api({});
	};

	noAction = function() {
		api({
			module: 'general'
		});
	};

	testTimestamp = function() {
		api({
			module: 'general',
			action: 'getTimestamp'
		}, function(dataOut) {
			$('table tr').remove();
			$('table').append('<tr>' + 
				'<td><i>Request ' + (request - 1) + ' : Get Timestamp</i></td>' + 
				'<td>' + dataOut.timestamp + '</td>' +
				'</tr>');

			api({
				module: 'general',
				action: 'T_testTimestamp',
				timestamp: dataOut.timestamp - 20
			}, function(dataOut) {
				$('table th').parent().remove();
				$('table').append('<tr>' + 
					'<td><i>Request ' + (request - 1) + ' : Test Timestamp</i></td>' +
					'<td>' + dataOut.message + '</td>' +
					'</tr>');
			}, true);
		});
	};

	testToken = function() {
		api({
			module: 'general',
			action: 'getTimestamp'
		}, function(dataOut) {
			$('table tr').remove();
			$('table').append('<tr>' + 
				'<td><i>Request ' + (request - 1) + ' : Get Timestamp</i></td>' + 
				'<td>' + dataOut.timestamp + '</td>' +
				'</tr>');
			
			api({
				module: 'general',
				action: 'T_generateToken',
				timestamp: dataOut.timestamp
			}, function(dataOut) {
				$('table th').parent().remove();
				$('table').append('<tr>' + 
					'<td><i>Request ' + (request - 1) + ' : Generate Token</i></td>' +
					'<td>' + dataOut.token + '</td>' +
					'</tr>');

				api({
					module: 'general',
					action: 'S_testToken',
					action_token: dataOut.token
				}, function(dataOut) {
					$('table th').parent().remove();
					$('table').append('<tr>' + 
						'<td><i>Request ' + (request - 1) + ' : Test Token</i></td>' +
						'<td>' + dataOut.message + '</td>' +
						'</tr>');
				}, true);
			}, true);
		});
	};
	</script>
</html>