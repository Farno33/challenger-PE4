<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/public/login.php ******************************/
/* Template affiché pour le changement de module ***********/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/

//Inclusion de l'entête de page

if ($admin_actif) {
	require DIR . 'templates/admin/_header_admin.php';

	if (file_exists(DIR . '/update/pass')) { ?>
		<div class="alerte alerte-attention">
			<div class="alerte-contenu">
				Bienvenue ! Voici le mot de passe admin de la base de donnée : <b><?php echo file_get_contents(DIR . '/update/pass');
																					unlink(DIR . '/update/pass') // Pas de repis plus de fichier 
																					?></b>
				<p> Attention c'est la seule fois où vous pourrez le voir
			</div>
		</div>
<?php }
} else
	require DIR . 'templates/_header_nomenu.php';

?>

<div class="login">
	<fieldset>
		<legend>Sélection du module</legend>
	</fieldset>

	<div class="alerte alerte-info">
		<div class="alerte-contenu">
			Bonjour <b><?php echo ucname(stripslashes($user['prenom'])) . ' ' . strtoupper(stripslashes($user['nom'])) . '</b> ! <br />'; ?>
				Plusieurs modules ou écoles vous sont accessibles.
		</div>
	</div>

	<?php if ($admin_actif) { ?>

		<form method="get" action="<?php url('admin'); ?>">
			<fieldset>
				<label for="form-admin">
					<span>Administration</span>
					<input type="submit" id="form-admin" class="success" value="Connexion à l'administration" />
				</label>
			</fieldset>
		</form>

		<?php if ($communication_active) { ?>

			<form method="get" action="<?php url('admin/module/communication'); ?>">
				<fieldset>
					<label for="form-participant">
						<span>Communication</span>
						<input type="submit" id="form-admin" class="delete" value="Voir <?php
																						if ($unread > 1) echo 'les ' . $unread . ' nouveaux messages';
																						else if ($unread) echo 'le nouveau message';
																						else echo 'les messages'; ?>" />
					</label>
				</fieldset>
			</form>

		<?php }
		if ($competition_active) { ?>

			<form method="post">
				<fieldset>
					<label for="form-participant">
						<span>Participant</span>
						<input type="text" id="form-participant" value="" />
					</label>
				</fieldset>
			</form>

			<script type="text/javascript">
				$(function() {
					var canSearch = false;
					var onlyOnEnter = false;
					$("#form-participant").autocomplete({
						source: function(request, response) {
							var $me = this.element;
							$.ajax({
								url: "<?php url('accueil'); ?>",
								method: "POST",
								cache: false,
								dataType: "json",
								data: {
									filter: request.term
								},
								success: function(data) {
									response(data);
								}
							});
						},
						minLength: 2,
						select: function(e, ui) {
							e.preventDefault();
							window.location.href = ui.item.url;
						},
						focus: function(e, ui) {
							return false;
						},
						search: function(e, ui) {
							var canTempSearch = canSearch;
							canSearch = false;
							return !onlyOnEnter || onlyOnEnter && canTempSearch;
						}
					}).bind('keyup', function(e) {
						if (e.keyCode == 13) {
							canSearch = true;
							$(this).autocomplete("search", $(this).val());
						}
					}).focus(function() {
						if (!onlyOnEnter)
							$(this).autocomplete("search");
					}).blur(function() {});

					$.ui.autocomplete.prototype._renderItem = function(ul, item) {
						return $("<li>")
							.append("<a>" + item.nom + ' ' + item.prenom + "<br /><small>" + item.enom + "</small></a>")
							.appendTo(ul);
					};
				});
			</script>

		<?php }
	}
	if ($ecoles_actives) { ?>

		<form method="post" action="<?php url('accueil'); ?>">
			<fieldset>
				<label for="form-ecole">
					<span>Ecole</span>

					<select id="form-ecole" name="ecole" onchange="$(this).parent().parent().parent().submit()">
						<option value="" selected></option>

						<?php foreach (!empty($_SESSION['user']['privileges']) &&
							in_array('ecoles', $_SESSION['user']['privileges']) ?
							array_keys($nomsEcoles) : (!empty($_SESSION['user']['ecoles']) ?
								$_SESSION['user']['ecoles'] : []) as $id) { ?>

							<option value="<?php echo $id; ?>"><?php echo stripslashes($nomsEcoles[$id]['nom']); ?></option>

						<?php } ?>

					</select>
				</label>

				<?php if (!empty($fermee)) { ?>

					<div class="alerte alerte-erreur">
						<div class="alerte-contenu">
							L'inscription de école "<b><?php echo stripslashes($nomsEcoles[$fermee]['nom']); ?></b>" n'est pas encore ouverte,
							<a href="<?php url('contact'); ?>">contactez-nous</a>
						</div>
					</div>

				<?php } ?>

			</fieldset>
		</form>

	<?php } ?>

</div>

<?php if (
	defined('APP_ACTIVE_MESSAGE') && APP_ACTIVE_MESSAGE &&
	defined('APP_MESSAGE_LOGIN') && APP_MESSAGE_LOGIN
) { ?>

	<div class="alerte alerte-info">
		<div class="alerte-contenu">
			<h3>Message de l'équipe Challenge : </h3>
			<?php echo nl2br(APP_MESSAGE_LOGIN); ?>
		</div>
	</div>

<?php } ?>

<?php

//Inclusion du pied de page
require DIR . 'templates/_footer.php';
