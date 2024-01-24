<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/public/contact.php ****************************/
/* Template de la page de contact **************************/
/* *********************************************************/
/* Dernière modification : le 12/12/14 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/_header_nomenu.php';

?>
				<form class="form-table" method="post">
					
					<?php if (!empty($ecoles)) { ?>
					<fieldset>
						<h3>Reponsables</h3>

						<?php foreach ($ecoles as $ecole) { ?>
						<h4><?php echo stripslashes($ecole['nom']); ?></h4>

						<div class="bloc">
						
							<?php 

							$i = 0;
							foreach ($ecole['respos'] as $id => $respo) {
								if (empty($contacts[$id]))
									continue;
								
								$contact = $contacts[$id];
							?>

							<div class="clearfix contact">
								<img src="<?php echo empty($contact['photo']) ? (URL_API_ECLAIR.'?type=photo&login='.$contact['login']) : 
									url($contact['photo'], false, false); ?>" alt="<?php echo $contact['login']; ?>" />

								<div style><h4 style="text-align:left; margin-top:20px"><?php echo stripslashes(strtoupper($contact['nom']).' '.$contact['prenom']); ?></h4>
								<i>
									<?php 
									if ($respo['ecole']) echo '<b>Responsable école</b>'; 
									if (!empty($respo['sports'])) {
										echo '<ul>'.
											implode(array_map(function($sid) {
												global $sports;
												return '<li>'.$sports[$sid]['sport'].' '.printSexe($sports[$sid]['sexe']).'</li>';
											}, $respo['sports'])).'</ul>';
									}
									?></i><br />
								<a href="mailto:<?php echo $contact['email']; ?>"><?php echo $contact['email']; ?></a><br />
								<a href="tel:<?php echo $contact['telephone']; ?>"><?php echo $contact['telephone']; ?></a>
							</div>
							</div>

							<?php if (++$i == (int) (count($ecole['respos']) / 2)) { ?>

						</div>
						<div class="bloc">
							
							<?php } else { ?>

							<br />

							<?php } }  ?>

						</div>

						<?php } ?>

					</fieldset>
					<?php } ?>


					<fieldset>
						<h3>Nos adresses</h3>

						<div>
							<div class="bloc">
								<h4>Par mail</h4>
								Pour les écoles, merci de contacter en priorité le responsable affecté. Il est affiché dans le récapitulatif de votre espace. <br />
								Vous pouvez nous contacter par mail à l'adresse suivante : 
								<a href="mailto:<?php echo APP_EMAIL_CHALLENGE; ?>"><?php echo APP_EMAIL_CHALLENGE; ?></a>
							</div>

							<div class="bloc">
								<h4>Par courrier</h4>
								Vous pouvez nous écrire à l'adresse suivante :<br />
								<b>USEECL Challenge<br />
								Ecole Centrale de Lyon<br />
								36 avenue Guy de Collongue<br />
								69134 ECULLY CEDEX</b>
							</div>
						</div>
						<div>
							<h4>Sur le web !</h4>
							<center>
								<a href="<?php echo APP_URL_CHALLENGE; ?>" style="display:inline-block">
									<img src="<?php url('assets/images/social/bookmarks.png'); ?>" style="height:50px; margin:1em" height="50px" />
								</a>

								<a href="https://www.snapchat.com/add/challengeecl" style="display:inline-block">
									<img src="<?php url('assets/images/social/snapchat.png'); ?>" style="height:50px; margin:1em" height="50px" />
								</a>

								<a href="https://www.facebook.com/ChallengeCentraleLyon" style="display:inline-block">
									<img src="<?php url('assets/images/social/fb.png'); ?>" style="height:50px; margin:1em" height="50px" />
								</a>

                                                                <a href="https://www.instagram.com/challengecentralelyon/" style="display:inline-block">
                                                                        <img src="<?php url('assets/images/social/insta.png'); ?>" style="height:50px; margin:1em" height="50px" />
                                                                </a>

								<a href="https://www.youtube.com/user/ChallengeECLyon" style="display:inline-block">
									<img src="<?php url('assets/images/social/youtube.png'); ?>" style="height:50px; margin:1em" height="50px" />
								</a>

								<a href="https://twitter.com/Challenge_ECL" style="display:inline-block">
									<img src="<?php url('assets/images/social/twitter.png'); ?>" style="height:50px; margin:1em" height="50px" />
								</a>
							</center>
						</div>
					</fieldset>


					<?php if (count($contacts)) { ?>

					<fieldset>
						<h3>Contacts de l'équipe Challenge</h3>

						<div class="bloc">
						
							<?php 

							$i = 0;
							$nb = 0;
							foreach ($contacts as $contact) {
								if (empty($contact['cid']))
									continue;

								$nb++;
							}

							foreach ($contacts as $contact) {
								if (empty($contact['cid']))
									continue;

							?>

							<div class="clearfix contact">
								<img src="<?php echo empty($contact['photo']) ? (URL_API_ECLAIR.'?type=photo&login='.$contact['login']) : 
									url($contact['photo'], false, false); ?>" alt="<?php echo $contact['login']; ?>" />

								<div>
									<h4 style="text-align:left; margin-top:20px"><?php echo stripslashes(strtoupper($contact['nom']).' '.$contact['prenom']); ?></h4>
									<i><?php echo stripslashes($contact['poste']); ?></i><br /><br />
									<a href="mailto:<?php echo $contact['email']; ?>"><?php echo $contact['email']; ?></a>
								</div>
							</div>

							<?php if (++$i == (int) ($nb / 2)) { ?>

						</div>
						<div class="bloc">
							
							<?php } else { ?>

							<br />

							<?php } }  ?>

						</div>

					</fieldset>

					<?php } ?>

				</form>

				
<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
