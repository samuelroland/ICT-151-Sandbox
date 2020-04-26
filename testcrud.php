<?php
/**
 *  Projet: ICT-151-SandBox
 *  Filename: testcrud.php  fonctions de tests unitaires pour les fonctions du modèle crud.php
 *  Author: Samuel Roland
 *  Creation date: 06.02.2020
 */
require "crud.php"; //import des fonctions du modèle


echo "\nTest count() ";
if (countFilmMakers() == 4) {
    echo "OK !!!";
} else {
    echo "BUG ...";
}


echo "\nTest GetAll() ";
if (count(getFilmMakers()) == countFilmMakers()) {
    echo "OK !!!";
} else {
    echo "BUG ...";
}


echo "\nTest GetOne() ";
$item = getFilmMaker(1);
if ($item['lastname'] == "Bertrand" && $item['firstname'] == "Jean-Michel") {
    if (getFilmMaker(150) == null){
        echo "OK !!!";
    } else {
        echo "BUG ... id 150 trouvée ?";
    }
} else {
    echo "BUG ... pas les mêmes champs";
}

echo "\nTest Create() ";

$randNumber = rand(0, 1000000);   //nombre random pour avoir un filmmakernumber unique.
//Données d'un réalisateur fictif pour les tests
$filmMakerTest = [
    "filmmakersnumber" => $randNumber,
    "lastname" => "Rabin",
    "firstname" => "Jaquie",
    "birthname" => "2019-06-02",
    "nationality" => "Switzerland"
];
$nfm = countFilmMakers();
$newfilmmaker = createFilmMaker($filmMakerTest);

if ($newfilmmaker != null) {
    if (countFilmMakers() == $nfm + 1) {     //il y a un enregistrement de plus dans la table.
        $readback = getFilmMaker($newfilmmaker['id']);  //lire le filmmaker créé.
        if ($readback != null) {
        $filmMakerTest['id'] = $newfilmmaker['id'];  //ajouter l'id générée pour pouvoir comparer entièrement ensuite
            if ($readback['id'] == $newfilmmaker['id']) {
                if (empty(array_diff($filmMakerTest, $readback))) {   //compare les deux tableaux pour savoir si il n'y a pas de différence.
                    echo "OK !!!";
                } else {
                    echo "BUG, error in the data inserted";
                }
            } else {
                echo "BUG, error in the data inserted";
            }
        } else {
            echo "BUG, record with id {$newfilmmaker['id']} not found ...";
        }
    } else {
        echo "BUG count records unchanged...";
    }
} else {
    echo "BUG function crash";
}

echo "\nTest Update() ";
$filmmakertoupdate = getFilmMaker(4);

//Changer de certaines valeurs
$filmmakertoupdate['lastname'] = "Pico";
$filmmakertoupdate['firstname'] = "Richard";

//mise à jour
if (updateFilmMaker($filmmakertoupdate)) {   //si réussit la requête
    $readback = getFilmMakerByName("Pico");
    if (empty(array_diff($readback, $filmmakertoupdate))) { //pas de différences entre le tableau modifié et le tableau au retour de la lecture
        echo "OK !!!";
    } else {
        echo "BUG, not all fields have been updated...";
    }
} else {
    echo "BUG, error in executing the query";
}

echo "\nTest Delete() ";

$nfm = countFilmMakers();


if (deleteFilmMaker(4)) {   // si la requete réussi
    if (countFilmMakers() == $nfm - 1) {     //il y a un enregistrement de moins dans la table.
        $readback = getFilmMaker(4);
        if ($readback == null) {    //si il ne trouve plus l'enregistrement
            echo "OK !!!";
        } else {
            echo "BUG, record not deleted";
        }
    } else {
        echo "BUG count records unchanged...";
    }
} else {
    echo "BUG function crash";
}

?>