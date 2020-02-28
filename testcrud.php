<?php
/**
 *  Projet: ICT-151-SandBox
 *  Filename: index.php
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
    echo "OK !!!";
} else {
    echo "BUG ...";
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

if (createFilmMaker($filmMakerTest) != null) {
    if (countFilmMakers() == $nfm + 1) {     //il y a un enregistrement de plus dans la table.
        $item = getFilmMaker($newfilmmaker['id']);
        //if () WIP
    } else {
        echo "BUG count records unchanged...";
    }

} else {
    echo "BUG function crash";
}

$readback = getFilmMakerByName("Rabin");
//Check:
var_dump($readback);
if ($readback['filmmakersnumber'] == $randNumber) {
    echo "OK !!!";
} else {
    echo "BUG ...";
}
echo "filmmakersnumber = $randNumber";


echo "\nTest Update() ";


echo "\nTest Delete() ";


?>