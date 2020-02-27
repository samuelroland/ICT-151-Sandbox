<?php
/**
 *  Projet: ICT-151-SandBox
 *  Filename: index.php
 *  Author: Samuel Roland
 *  Creation date: 06.02.2020
 */
require "crud.php"; //import des fonctions du modèle


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
createFilmMaker($filmMakerTest);
$filmMakerBack = getFilmMakerByName("Rabin");
//Check:
if ($filmMakerBack['filmmakersnumber'] == $randNumber) {
    echo "OK !!!";
} else {
    echo "BUG ...";
}
echo "filmmakersnumber = $randNumber";

echo "\nTest GetAll() ";


echo "\nTest GetOne() ";


echo "\nTest Update() ";


echo "\nTest Delete() ";


?>