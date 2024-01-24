<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/public/login_admin.php ************************/
/* Template affiché lors de la connexion à l'administration*/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/_header_nomenu.php';

?>		

				<?php
				if (!empty($_SESSION['expire'])) {
					unset($_SESSION['expire'])
				?>

				<div class="alerte alerte-erreur" style="width: 600px;">
					<div class="alerte-contenu">
						Votre session a expiré après une inactivité trop longue.
					</div>
				</div>
				
				<?php } ?>

				<script type="text/javascript">
				$(function() {
					removeCookie = function(e, elem, id) {
						 e.preventDefault();
						 e.stopPropagation();
						 $(elem).parent().parent().remove();

						 $.ajax({
						 	url: "<?php url('login'); ?>",
						 	cache: false,
						 	method: "POST",
						 	dataType: 'json',
						 	data: {remove: id}
						 });
					};
				});
				</script>

				<?php foreach ($checks as $check) { ?>

				<a href="<?php url($check['id'].'/'.$check['check']); ?>" style="display:block; text-decoration: none !important; margin-bottom:20px;">
					<div class="login" style="margin-bottom:0px">
						<img src="<?php url('assets/images/actions/delete.png'); ?>" style="float:right" onclick="removeCookie(event, this, <?php echo $check['id']; ?>)" />

						<?php if (!empty($check['token'])) { ?>
						<img src="<?php url('image/'.$check['token']); ?>" style="width:75px; max-height:75px;  float:left; margin:0px 20px" />
						<?php } ?>
						<div style="margin-left:120px">
							<h3 style="text-align:left"><?php echo ucname(stripslashes($check['nom'].' '.$check['prenom'])); ?></h3>
							<br />
							<?php echo stripslashes($check['enom']); ?>
						</div>
						<div class="clearfix"></div>
					</div>
				</a>

				<?php } ?>


				<div class="login">
					<form method="post" autocomplete="off">
						<fieldset>
							<legend>Connexion au Challenger</legend>
							<small>Veuillez renseigner les identifiants donnés par l'équipe Challenge.</small>

							<?php if (!empty($user) &&
								empty($_SESSION['user']['privileges']) &&
								empty($_SESSION['user']['ecoles']) ||
								!empty($fermee)) { ?>

							<div class="alerte alerte-attention">
								<div class="alerte-contenu">
									Bonjour <b><?php echo ucname(stripslashes($user['prenom'])).' '.strtoupper(stripslashes($user['nom'])).'</b> ! <br />'; ?>

									<?php if (!empty($fermee)) { ?>

									L'inscription de l'école "<b><?php echo stripslashes($etatEcole['nom']); ?></b>" n'est pas encore ouverte.
									N'hésitez pas à <a href="<?php url('contact'); ?>">contacter l'équipe du Challenge</a>

									<?php } else { ?>
									
									Vous êtes connecté mais vous n'avez accès à aucun module du Challenger.<br />
									<a href="<?php url('contact'); ?>">Merci de contacter l'équipe du Challenge</a>.

									<?php } ?>

								</div>
							</div>

							<?php } ?>
							

							<?php 

							if (!empty($error) ||
								!empty($_SESSION['tentatives']) &&
								$_SESSION['tentatives']['count'] >= APP_MAX_TRY_AUTH) { 
							
							?>

							<div class="alerte alerte-erreur">
								<div class="alerte-contenu">
									<?php
									
									if (!empty($_SESSION['tentatives']) &&
										$_SESSION['tentatives']['count'] >= APP_MAX_TRY_AUTH) 
										echo 'Trop de tentatives ont été soumises, veuillez réessayer dans quelques minutes.';

									else if (!empty($_SESSION['tentatives']) &&
										$error == 'db')
										echo 'Les identifiants sont incorrects (tentative '.$_SESSION['tentatives']['count'].'/'.APP_MAX_TRY_AUTH.')';

									else if ($error == 'cas')
										echo 'Le compte CAS "<b>'.$cas.'</b>" n\'est pas activé sur le Challenger.';

									else 
										echo 'Une erreur inconnue vient de se produire.';
									?>
								</div>
							</div>

							<?php } ?>

							<label for="form-login">
								<span>Identifiant</span>
								<input type="text" autocomplete="off" name="login" id="form-login" value="" />
							</label>

							<label for="form-pass">
								<span>Mot de Passe</span>
								<input type="password" autocomplete="off" name="pass" id="form-pass" value="" />
							</label>

							<label for="form-remember">
								<!--<span>Se souvenir <i style="color:gray; font-size:75%; font-weight:normal">(1 mois)</i></span>-->
								<input type="hidden" autocomplete="off" name="remember" id="form-remember" value="remember" />
								<!--<label for="form-remember" style="width:100% !important"></label>-->
							</label>

							<center>
								<input type="submit" class="success" name="db" value="Connexion" />
								<!-- <input id="form-cas" type="submit" name="creation" value="Création de compte - Centrale Lyon" /> -->
							</center>

							<center>
    						    <a href="?myecl">
    						        <button type="button" class="button"><img src="https://hyperion.myecl.fr/favicon.ico"> Se connecter avec MyECL</button>
    						    </a>
    						</center>
						</fieldset>
					</form>
				</div>

				<?php if (defined('APP_ACTIVE_MESSAGE') && APP_ACTIVE_MESSAGE && 
					defined('APP_MESSAGE_LOGIN') && APP_MESSAGE_LOGIN) { ?>

				<div class="alerte alerte-info">
					<div class="alerte-contenu">
						<h3>Message de l'équipe Challenge : </h3>
						<?php echo nl2br(APP_MESSAGE_LOGIN); ?>
					</div>
				</div>
				
				<?php } ?>

				<script type="text/javascript">
				$(function() {
					$speed =  <?php echo APP_SPEED_ERROR; ?>;
			    	$analysis = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			              	$parent = elem;
			              	$login = $('#form-login');
			  				$pass = $('#form-pass');
			  				
			                if (!$login.val().trim())
			                	$login.addClass('form-error').removeClass('form-error', $speed).focus();
			                if (!$pass.val().trim())
			                	$pass.addClass('form-error').removeClass('form-error', $speed).focus();


			                if (!$login.val().trim() ||
			                	!$pass.val().trim())
			                	event.preventDefault();   
			           
			            }
			        };

			        $('#form-login').focus();
					$('form').bind('submit', function(event) { $analysis($(this), event, true); });
					$('#form-cas').on('click', function() {
						$('form').unbind('submit');
					});
			    });
				</script>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';