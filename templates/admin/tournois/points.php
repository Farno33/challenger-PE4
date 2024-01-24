<?php

/* **************************************************************/
/* Sous-module de comptage des points pour le challenge *********/
/* Créé par Matthieu 'Thamite' Massardier et le PE81 2021-2022 **/
/* Matthieu.massardier@ecl21.ec-lyon.fr *************************/
/* **************************************************************/
/* templates/admin/tournois/points.php **************************/
/* Template de la page pour rapporter le score en direct ********/
/* **************************************************************/
/* Dernière modification : le 22/03/2022 ************************/
/* **************************************************************/

//Inclusion de l'entête de page
require DIR . 'templates/admin/_header_admin.php';


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href=<?php url("assets/css/score.css") ?> />

    <title><?php echo $data_match['sport'] . ' | ' . ($data_match['concurentA'] . '-' . $data_match['concurentB']); ?></title>
</head>

<body>

    <?php if ($data_match['type'] == 'score' || $data_match['type'] == 'set') : ?>
        <h1><?php echo ('Match de ' . $data_match['sport'] . ' ' . strtr(ucfirst($data_match['sexe']), array('H' => 'Homme', 'F' => 'Femme', 'M' => 'Mixte')) . ' (' . $data_match['phase'] . '): ' . $data_match['concurentA'] . ' - ' . $data_match['concurentB']); ?> </h1> <!-- METTRE UNE PHOTO DU CHALLENGE-->
        <form method="post" action="points_<?php echo $data_match['idMatch']; ?>">

            <h2>SCORE <?php if ($data_match['type'] == 'set') {
                            echo 'du set n°' . $data_match['setN'] . ' (en cours) ';
                        }; ?>:
                <b><?php echo $data_match['scoreA'] . ' - ' . $data_match['scoreB']; ?></b>
            </h2>
            <?php if ($data_match['type'] == 'set') : ?>
                <h3><?php echo "Set n°" . $data_match['setN'] . ": " . $data_match['setsA'] . " sets à " . $data_match['setsB'] ?></h3>
            <?php endif; ?>

        <?php else : ?>
            <h1> Classement <?php echo $data_match['sport'] . '-' . strtr(ucfirst($data_match['sexe']), array('H' => 'Homme', 'F' => 'Femme', 'M' => 'Mixte')); ?> </h1>
            <form method="post" action="Classement_data.php?id=<?php echo $data_match['idMatch']; ?>">
            <?php endif; ?>


            <!--Comptage  points-->
            <?php if ($data_match['type'] == 'score' || $data_match['type'] == 'set') : ?>
                <fieldset>
                    <legend>Pour <?php echo ($data_match["indiv"] ? "" : "l'équipe de ") . $data_match['concurentA']; ?> </legend>
                    <?php if ($data_match['sport'] == 'Basket') : ?>
                        <div class="golaz"> <label for="bouton1A">+1 point </label>:<input type="radio" name='point' id="bouton1A" value="A1" /> </div>
                        <div class="golaz"> <label for="bouton2A">+2 points</label>:<input type="radio" name='point' id="bouton2A" value="A2" /> </div>
                        <div class="golaz"> <label for="bouton3A">+3 points</label>:<input type="radio" name='point' id="bouton3A" value="A3" /> </div>

                    <?php elseif ($data_match['sport'] == 'Rugby') : ?>
                        <div class="golaz"> <label for="bouton3A">Drop ou pénalité (+3)</label>:<input type="radio" name='point' id="bouton3A" value="A3" /> </div>
                        <div class="golaz"> <label for="bouton5A">Essai (+5)</label>:<input type="radio" name='point' id="bouton5A" value="A5" /> </div>
                        <div class="golaz"> <label for="bouton7A">Transformation (+7)</label>:<input type="radio" name='point' id="bouton7A" value="A7" /> </div>

                    <?php elseif ($data_match['sport'] == 'Foot') : ?>
                        <div class="minute"><label for="butA">But à la :</label><input type="number" name='tempsA' id="butA" value="butAtemps" placeholder="Temps" min="0" max="120" step="1" /> ième minute.</div>
                        <div class="golaz"> <label for="bouton1A">+1 but</label>:<input type="radio" name='point' id="bouton1A" value="A1" /> </div>

                    <?php else : ?>
                        <div class="golaz"> <label for="bouton1A">+1 point</label>:<input type="radio" name='point' id="bouton1A" value="A1" /> </div>
                    <?php endif; ?>


                    <?php if (!$data_match['indiv']) : ?>
                        <div class="faute">
                            <details>
                                <summary> Faute éliminatoire </summary>
                                <select class="fautes" name="JoueurFautifA">
                                    <option value="" selected>Choississez le joueur qui ne dois plus jouer d'autres match</option>
                                    <?php foreach ($joueursA as $joueur) : ?>
                                        <option class="joueur joueurA" value="<?php echo $joueur["id"]; ?>"><?php echo ucfirst($joueur["prenom"]); ?> <?php echo strtoupper($joueur["nom"]); ?></option>
                                    <?php endforeach; ?>
                                </select>

                                </datalist>

                            </details>
                        </div>
                    <?php endif; ?>
                </fieldset>
                <fieldset>
                    <legend>Pour <?php echo ($data_match["indiv"] ? "" : "l'équipe de ") . $data_match['concurentB']; ?> </legend>
                    <!--Adapté en fonction du PHP ?-->
                    <!--if (condition1){instruction1;} elseif(condition2){instruction2;} else{instruction3;}-->
                    <?php if ($data_match['sport'] == 'Basket') : ?>
                        <div class="golaz"> <label for="bouton1B">+1 point </label>:<input type="radio" name='point' id="bouton1B" value="B1" /> </div>
                        <div class="golaz"> <label for="bouton2B">+2 points</label>:<input type="radio" name='point' id="bouton2B" value="B2" /> </div>
                        <div class="golaz"> <label for="bouton3B">+3 points</label>:<input type="radio" name='point' id="bouton3B" value="B3" /> </div>

                    <?php elseif ($data_match['sport'] == 'Rugby') : ?>
                        <div class="golaz"> <label for="bouton3B">Drop ou pénalité (+3)</label>:<input type="radio" name='point' id="bouton3B" value="B3" /> </div>
                        <div class="golaz"> <label for="bouton5B">Essai (+5)</label>:<input type="radio" name='point' id="bouton5B" value="B5" /> </div>
                        <div class="golaz"> <label for="bouton7B">Transformation (+7)</label>:<input type="radio" name='point' id="bouton7B" value="B7" /> </div>

                    <?php elseif ($data_match['sport'] == 'Foot') : ?>
                        <div class="minute"> <label for="butB">But à la :</label><input type="number" name='tempsB' id="butB" value="butBtemps" placeholder="Temps" min="0" max="120" step="1" /> ième minute.</div>
                        <div class="golaz"> <label for="bouton1B">+1 but</label>:<input type="radio" name='point' id="bouton1B" value="B1" /> </div>
                    <?php else : ?>
                        <div class="golaz"> <label for="bouton1B">+1 point</label>:<input type="radio" name='point' id="bouton1B" value="B1" /> </div>
                    <?php endif; ?>

                    <?php if (!$data_match['indiv']) : ?>
                        <div class="faute">
                            <details>
                                <summary> Faute éliminatoire </summary>
                                <select class="fautes" name="JoueurFautifB">
                                    <option value="" selected>Choississez le joueur qui ne dois plus jouer d'autres match</option>
                                    <?php foreach ($joueursB as $joueur) : ?>
                                        <option class="joueur joueurB" value="<?php echo $joueur["id"]; ?>"><?php echo ucfirst($joueur["prenom"]); ?> <?php echo strtoupper($joueur["nom"]); ?></option>
                                    <?php endforeach; ?>
                                </select>

                                </datalist>
                            </details>
                        </div>
                    <?php endif; ?>
                </fieldset>


            <?php elseif ($data_match['sport'] == 'Natation') : ?>
                <!-- on commence les classements -->
                <!--NATATION-->
                <fieldset>
                    <legend> Choississez le type d'épreuve</legend>
                    <div class="menu">
                        <select class="oui" id="matchs" name="Choix des équipes">
                            <option selected></option>

                            <option class="match " value="Indiv-50m">Indiv 50m</option>
                            <option class="match " value="Indiv-100m">Indiv 100m</option>
                            <option class="match " value="Indiv-200m">Indiv 200m</option>
                            <option class="match " value="Relai-4-nages">Relai 4 nages</option>
                            <option class="match " value="Relai-nage-libre">Relai nage libre</option>

                        </select>
                    </div>
                </fieldset>


            <?php elseif ($data_match['sport'] == 'Ski') : ?>
                <!--SKI-->
                <fieldset>
                    <legend> Choississez le type d'épreuve</legend>
                    <div class="menu">

                        <select class="oui" id="matchs" name="Choix des équipes">
                            <option selected></option>
                            <option class="match " value="Manche-1">Manche 1</option>
                            <option class="match " value="Manche-2">Manche 2</option>
                        </select>
                    </div>
                </fieldset>



            <?php elseif ($data_match['sport'] == 'Athlétisme') : ?>
                <!--Athlétisme-->
                <fieldset>
                    <legend> Choississez le type d'épreuve</legend>
                    <div class="menu">

                        <select class="oui" id="matchs" name="Choix des équipes">
                            <option selected></option>
                            <option class="match " value="100m">100m</option>
                            <option class="match " value="800m">800m</option>
                            <option class="match " value="100m-haies">100m haies</option>
                            <option class="match " value="110m-haies">110m haies</option>
                            <option class="match " value="Saut-en-longueur">Saut en longueur</option>
                            <option class="match " value="Lancer-de-poids">Lancer de poids</option>
                            <option class="match " value="Triple-saut">Triple saut</option>
                            <option class="match " value="Relai-15min">Relai 15min</option>
                        </select>
                    </div>
                </fieldset>


            <?php elseif ($data_match['sport'] == 'Raid') : ?>
                <!--RAID-->
                <fieldset>
                    <legend> Choississez le type d'épreuve</legend>
                    <div class="menu">

                        <select class="oui" id="matchs" name="Choix des équipes">
                            <option selected></option>
                            <option class="match " value="Trail-1">Trail 1</option>
                            <option class="match " value="Trail-2">Trail 2</option>
                            <option class="match " value="VTT-1">VTT 1</option>
                            <option class="match " value="VTT-2">VTT 2</option>
                            <option class="match " value="Run&Bike">Run & Bike</option>
                        </select>
                    </div>
                </fieldset>
                <fieldset>


                <?php elseif ($data_match['sport'] == 'Pompoms') : ?>
                    <!-- POMPOMS -->
                    <fieldset>
                        <legend> Classement</legend>
                        <?php foreach ($ecoles as $ecole) : ?>
                            <!--Acceder à la liste de tous les EQUIPES inscrit pour ce sport -->
                            <div class="minute">
                                <?php echo ucfirst($ecole["nom"])  ?> <input type="number" name='tempsA' id='<?php echo $ecole["id"]; ?>' value="butAtemps" />
                            </div>
                        <?php endforeach; ?>

                    </fieldset>

                <?php else : ?>
                    <!-- on commence les classements RESTE -->
                    <fieldset>
                        <legend> Classement</legend>
                        <?php foreach ($joueur as $joueurI) : ?>
                            <!--Acceder à la liste de tous les joueurs inscrit pour ce sport -->
                            <div class="minute">
                                <?php echo ucfirst($joueurI["prenom"]) . ' ' . strtoupper($joueurI["nom"])  ?> <input type="number" name='tempsA' id='<?php echo $joueurI["id"]; ?>' value="butAtemps" />
                            </div>
                        <?php endforeach; ?>

                    </fieldset>

                <?php endif; ?>

                <!--Afficher Classement-->
                <?php if ($data_match['type'] == 'classement') : ?>
                    <fieldset>
                        <legend> Choix du classement</legend>
                        <p class="droite"> Classement <?php if ($data_match['sport'] == 'Raid' || $data_match['sport'] == 'Athlétisme' || $data_match['sport'] == 'Natation' || $data_match['sport'] == 'Ski') {
                                                            echo '| Temps';
                                                        }; ?></p>
                        <?php foreach ($joueur as $joueurI) : ?>
                            <!--Acceder à la liste de tous les joueurs inscrit pour ce sport -->
                            <div class="minute">
                                <label for="<?php echo $joueur["id"]; ?>_classement"><?php echo ucfirst(strtoupper($joueurI["nom"])) . ' ' . ($joueurI["prenom"])  ?> </label>

                                <!-- Celui de droite-->
                                <?php if ($data_match['sport'] == 'Raid' || $data_match['sport'] == 'Athlétisme' || $data_match['sport'] == 'Natation' || $data_match['sport'] == 'Ski') : ?>
                                    <input type="number" class="nchv" name='temps' id='<?php echo $joueur["id"]; ?>_temps' value="temps_classement" />
                                <?php endif; ?>

                                <!-- Celui de gauche-->
                                <input type="number" class="nchv" name='clssmnt' id='<?php echo $joueur["id"]; ?>_classement' value="classement" />


                            </div>
                        <?php endforeach; ?>
                    </fieldset>
                <?php endif; ?>

                <!--VALIDATION -->
                <fieldset>
                    <legend>Valider </legend>
                    <div class="V"><input type="submit" id="submit" name="<?php echo (($data_match['type'] == 'set') ? "submit" : "") ?>" value="Validation" /></div>
                    <?php if ($data_match['type'] == 'set') : ?> <div class="V"> <input type="submit" id="fin-submit" name="submit" value="Fin de set" /></div> <?php endif; ?>
                    <div class="suite">
                        <details>
                            <summary> Pour annuler la derniere opération :</summary>
                            <p> <label for="bouton-"></label>Annulation</label>:<input type="radio" name='point' id="bouton-" value="-" /><br /> </p>
                        </details>
                    </div>
                </fieldset>
                <!-- Pied de page : section commentaire -->

                <fieldset class="com">
                    <legend>Des commentaires ?</legend>

                    <div>
                        <p>Zone de commentaire où l'assistant arbitre challenge peut s'exprimer : retour par rapport au match, s'il y a eu une situation litigieuse...</p>
                    </div>

                    <div>
                        <textarea name="ameliorer" id="ameliorer" rows="3" cols="20"></textarea>
                    </div>
                </fieldset>
            </form>
            <!--<input type="submit" id="bouton3" value="Fin Match" /><br />-->

            <!-- TODO: Under construction
    <div id="historique">
        <h3>Historique des actions</h3>
        
    </div>
            -->
            <?php

            //Inclusion du pied de page
            require DIR . 'templates/_footer.php';
