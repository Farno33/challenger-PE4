<?php
//Inclusion de l'entête de page
require DIR.'templates/_header_nomenu.php';
?>		


<div class="login">
	<form method="post" autocomplete="off">
		<fieldset>
			<legend>Challenger - Création de compte pour les Centraliens de Lyon</legend>
			<small>Veuillez renseigner les informations suivantes afin de créer un compte sur le Challenger.</small>

            <?php if (!$noerror){?>
            <div class="alerte alerte-erreur">
				<div class="alerte-contenu">
					<?php			
						if (!$adresseECL) 
							echo "L'email doit être votre adresse email de Centrale !";

						else if ($registered){
                            echo "Un compte a déjà été créé pour cette adresse email !";
                        }
                        else if (!$envoye)
							echo "Erreur veuillez vérifier vos informations et réessayer !";

						else
							echo 'Une erreur inconnue vient de se produire.';
					?>
				</div>
            </div>
            <?php }else if ($comptecree){?>
                <div class="alerte alerte-success">
				<div class="alerte-contenu">
                    Votre compte a été créé et un email vous a été envoyé avec les informations nécessaires pour vous connecter.
				</div>
            </div>
            <?php }?>
                
            <label for="form-login">
				<span>Email <?php if(!$admin) {?><b>Centrale</b><?php }?></span>
				<input required type="email" autocomplete="email" name="email" id="form-email" <?php if(!$admin){echo 'placeholder="@<...>ec-lyon.fr" pattern="[a-zA-Z0-9._%+-]+@((ecl|alternance|auditeur|master)[0-9]{0,4}\.)?ec-lyon\.fr$"';} ?>/>
            </label>

			<label for="form-login">
				<span>Téléphone</span>
				<input required type="tel" autocomplete="tel" name="tel" id="form-tel" pattern="(^((((\+)|(00))33 ?0? ?)|0)[1-9] ?([0-9]{2}( |-)?){4}$)|(((\+)|(00))(?!33)(?!0)[0-9]*)"/>
            </label>
                
			<label for="form-login">
				<span>Nom</span>
			   <input required type="text" autocomplete="family-name" name="nom" id="form-login"/>
			</label>

            <label for="form-login">
				<span>Prénom</span>
			    <input required type="text" autocomplete="given-name" name="prenom" id="form-login" />
			</label>

			<label for="form-login">
				<span>Sexe</span>
				<input required type="radio" id="femme" name="sexe" value="f">
				<label for="femme">Femme</label>
				<input required type="radio" id="homme" name="sexe" value="h">
				<label for="homme">Homme</label>
			    
			</label>

			<center>
				<input type="submit" class="success" name="creation" value="Me créer un compte" />
			</center>
		</fieldset>
	</form>
</div>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';