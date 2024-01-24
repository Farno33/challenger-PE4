<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/ecoles/liste.php ************************/
/* Template de la liste du module des Ecoles ***************/
/* *********************************************************/
/* Dernière modification : le 21/11/14 *********************/
/* *********************************************************/


$cql = 'T|er:erreurs:|p:participants:
F|p.id|er._date|p.prenom|p.nom|er.message|er.etat
B|er._date:DESC';


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
					<h2>
						Liste des Erreurs
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value='<?php echo $cql; ?>' />
				</form>

				<form method="post">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Message</th>
                                <th>Etat</th>
                                <th>Edition</th>
                                <th>Consultation</th>
                                <th>Message</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (!count($erreurs)) { ?> 

                            <tr class="vide">
                                <td colspan="9">Aucune erreur</td>
                            </tr>

                            <?php } foreach ($erreurs as $erreur) { ?>

                            <tr class="form">
                                <td><div><?php echo printDateTime($erreur['date']); ?></div></td>
                                <td><div><?php echo stripslashes($erreur['nom']); ?></div></td>
                                <td><div><?php echo stripslashes($erreur['prenom']); ?></div></td>
                                <td><textarea readonly><?php echo stripslashes($erreur['message']); ?></textarea></td>
                                <td><center><a href="?switch=<?php echo $erreur['erid']; ?>"><?php echo printEtatErreur($erreur['etat']); ?></a></center></td>
                                <td><div><?php if ($erreur['access']) { ?><a href="<?php url('ecoles/'.$erreur['id_ecole'].'/participants?edit='.$erreur['id']); ?>">Edition</a><?php } ?></div></td>
                                <td><div><a href="<?php echo $erreur['cle']; ?>">Consultation</a></div></td>
                                <td><div><a href="<?php url('admin/module/communication/ecrire?id='.$erreur['id']); ?>">Message</a></div></td>
                            </tr>

                            <?php } ?>

                        </tbody>
                    </table>
				</form>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
