<?php

/* **************************************************************/
/* Sous-module de comptage des points pour le challenge *********/
/* Créé par Matthieu 'Thamite' Massardier et le PE81 2021-2022 **/
/* Matthieu.massardier@ecl21.ec-lyon.fr *************************/
/* **************************************************************/
/* templates/admin/tournois/points_choix_match.php **************/
/* Template de la liste des sports dont rapporter le score ******/
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
    <title>Choix Du Match</title>

    <link rel="stylesheet" href=<?php url("assets/css/choix.css") ?> />
</head>

<body>
    <div class="uwu">
        <h1>Choix du sport</h1>
    </div>
    <div class="uwu">
        <div class="menu">

            <label for="Sports"> Choississez le Sport à arbitrer : </label>
            <select id="Sports" class="oui" onChange="filterMatchs(this.value)">


                <option selected></option>
                <optgroup label="En equipe">
                    <?php $i = array();
                    foreach ($matchEquipe as $confrontation) :
                        if (array_search($confrontation["sportid"], $i) === false) : ?>
                            <option value="<?php echo $confrontation["sportid"]; ?>"><?php echo ucfirst($confrontation["sport"]) . ' ' . strtr(ucfirst($confrontation["sexe"]), array('H' => 'Homme', 'F' => 'Femme', 'M' => 'Mixte')); ?></option>
                    <?php array_push($i, $confrontation["sportid"]);
                        endif;
                    endforeach; ?>
                </optgroup>
                <optgroup label="En individuel">
                    <?php $i = array();
                    foreach ($matchSolo as $confrontation) :
                        if (array_search($confrontation["sportid"], $i) === false) : ?>
                            <option value="<?php echo $confrontation["sportid"]; ?>"><?php echo ucfirst($confrontation["sport"]) . ' ' . strtr(ucfirst($confrontation["sexe"]), array('H' => 'Homme', 'F' => 'Femme', 'M' => 'Mixte')); ?></option>
                    <?php array_push($i, $confrontation["sportid"]);
                        endif;
                    endforeach; ?>
                </optgroup>
            </select>


        </div>
    </div>
    <div class="uwu">
        <h1>Choix des équipes/concurrents</h1>
    </div>
    <div class="uwu">
        <div class="menu">
            <label for="matchs"> Choississez le match à arbitrer : </label>
            <select class="oui" id="matchs" name="Choix des équipes">
                <option selected></option>
                <?php foreach ($matchEquipe as $confrontation) : ?>
                    <option class="match <?php echo $confrontation["sportid"]; ?>" value="<?php echo $confrontation["matchid"]; ?>"><?php echo $confrontation["phase"] . ": " . $confrontation["nomA"] . " [" . $confrontation["labelA"] . "] vs " . $confrontation["nomB"] . " [" . $confrontation["labelB"] . "]" . (empty($confrontation["etape"]) ? "" : " (" . $confrontation["etape"] . ")"); ?></option>
                <?php endforeach; ?>
                <?php foreach ($matchSolo as $confrontation) : ?>
                    <option class="match <?php echo $confrontation["sportid"]; ?>" value="<?php echo $confrontation["matchid"]; ?>"><?php echo $confrontation["phase"] . ": " . ucfirst($confrontation["prenomA"]) . " " . strtoupper($confrontation["nomA"]) . " vs " . ucfirst($confrontation["prenomB"]) . " " . strtoupper($confrontation["nomB"]) . (empty($confrontation["etape"]) ? "" : " (" . $confrontation["etape"] . ")"); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</body>

</div>
<br />

<div class="uwu"> <button id="valid" onclick="window.location.href += '_'+document.getElementById('matchs').value;">Validation </button></div>

<script>
    function filterMatchs(val) {
        Array.from(document.getElementsByClassName("match")).forEach((el) => {
            el.style.display = el.classList.contains(val) || val == '' ? 'unset' : 'none';
        });
        document.getElementById("matchs").selectedIndex = 0;
    }
</script>

<?php

//Inclusion du pied de page
require DIR . 'templates/_footer.php';
