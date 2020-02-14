<?php
/**
 *  Projet: ICT-151-SandBox
 *  Filename: index.php
 *  Author: Samuel Roland
 *  Creation date: 06.02.2020
 */

// Recharger la base de données pour être sûr à 100% des données de test
require '.const.php';  //récuperer les identifiants
$cmd = "mysql -u $user -p$pass < Restore-MCU-PO-Final.sql";   //command system pour
exec($cmd);

function getPDO()   //create the PDO object
{
    require '.const.php';  //récuperer les identifiants
    return new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname, $user, $pass);   //créer un objet PDO
}


function getAllItems()  //prendre tous les éléments
{
    try {   //essayer
        $dbh = getPDO();
        $query = "SELECT filmmakersnumber, lastname, firstname FROM filmmakers";    //Ecrire la requête.
        $statment = $dbh->prepare($query);  //préparer la requête
        $statment->execute();   //éxecuter la requête
        $queryResult = $statment->fetchAll();   //aller chercher le résultat
        $dbh = null;    //remettre à zéro
        return $queryResult;    //retourner le résultat
    } catch (PDOException $e) { //en cas d'erreur dans le try
        echo "Error!: " . $e->getMessage() . "\n";
        return null;
    }
}

function getFilmMakerByName($lastname)
{
    try {
        $dbh = getPDO();   //créer un objet PDO
        $query = "SELECT * FROM filmmakers WHERE lastname = '" . $lastname . "';";//Ecrire la requête.
        $statment = $dbh->prepare($query);  //préparer la requête
        $statment->execute();   //éxecuter la requête
        $queryResult = $statment->fetch(PDO::FETCH_ASSOC);   //aller chercher le résultat
        $dbh = null;    //remettre à zéro
        return $queryResult;    //retourner le résultat
    } catch (PDOException $e) { //en cas d'erreur dans le try
        echo "Error!: " . $e->getMessage() . "\n";
        return null;
    }
}

function getFilmMaker($id)
{
    try {
        $dbh = getPDO();   //créer un objet PDO
        $query = "SELECT * FROM filmmakers WHERE id = " . $id . ";";;//Ecrire la requête.
        $statment = $dbh->prepare($query);  //préparer la requête
        $statment->execute();   //éxecuter la requête
        $queryResult = $statment->fetch(PDO::FETCH_ASSOC);   //aller chercher le résultat
        $dbh = null;    //remettre à zéro
        return $queryResult;    //retourner le résultat
    } catch (PDOException $e) { //en cas d'erreur dans le try
        echo "Error!: " . $e->getMessage() . "\n";
        return null;
    }
}

function makerOf($filmname)  //trouver le réalisateur d'un film:
{
    try {
        $dbh = getPDO();
        $query = "SELECT firstname, lastname FROM films
LEFT JOIN make ON films.id = make.film_id
LEFT JOIN filmmakers ON make.filmmaker_id = filmmakers.id
WHERE films.name = \"Ant-Man\";";
        $statment = $dbh->prepare($query);
        $statment->execute();
        $queryResult = $statment->fetchAll();
        $dbh = null;
        return $queryResult;
    } catch (PDOException $e) {
        echo "Error!: " . $e->getMessage() . "\n";
        return null;
    }

}

function getAllFieldFromTable($tablename)   //récuperer la liste de tous les champs d'une table
{
    try {
        $dbh = getPDO();
        $query = "SELECT COLUMN_NAME
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = 'mcu' AND TABLE_NAME = '$tablename';";
        $statment = $dbh->prepare($query);
        $statment->execute();
        $queryResult = $statment->fetchAll(PDO::FETCH_NUM);
        $dbh = null;
        return $queryResult;
    } catch (PDOException $e) {
        echo "Error!: " . $e->getMessage() . "\n";
        return null;
    }
}

function updateFilmMaker($filmMaker)
{
    $listFields = array_keys($filmMaker);   //les valeurs à modifier sont celles du tableaux. prendre les clés en un tableau
    $listToSet = "";    //liste d'éléments formatées à set pour l'update
    foreach ($listFields as $oneField) {
        if ($oneField != "id") {    //on exclut le champ id. interdit de changer.
            $listToSet .= $oneField . "=:" . $oneField . ", ";
        }
    }
    $listToSet = substr($listToSet, 0, strlen($listToSet) - 2); //enlever la string ", " de fin

    try {
        $dbh = getPDO();   //créer un objet PDO
        $query = "UPDATE filmmakers SET $listToSet WHERE id =:id;";//Ecrire la requête.

        $statment = $dbh->prepare($query);  //préparer la requête
        $statment->execute($filmMaker);   //éxecuter la requête
        $queryResult = $statment->fetch(PDO::FETCH_ASSOC);   //aller chercher le résultat
        $dbh = null;    //remettre à zéro
        return $queryResult;    //retourner le résultat
    } catch (PDOException $e) { //en cas d'erreur dans le try
        echo "Error!: " . $e->getMessage() . "\n";
        return null;
    }
}

echo "\n";
echo "Test unitaire de la fonction getAllItems:";
$items = getAllItems();
if (count($items) == 4) {
    echo "getAllItems() retourne 4 éléments OK !!";
} else {
    echo "getAllItems() retourne null BUG ...";
}

echo "\n";
$filmname = "Ant-Man";
$filmmakers = makerOf($filmname);
echo "Test unitaire de la fonction getAllItems: Quel sont les réalisateurs de $filmname ??  \n";

if ($filmmakers[0]['firstname'] == "Jean-Philippe" && $filmmakers[2]['firstname'] == "Jean-Michel") {
    echo "makerOf() retourne Jean-Philippe et Jean-Michel donc OK !!";
} else {
    echo "makerOf() retourne null donc BUG ...";
}
echo "\n";

//Test écrit par M. Carrel (exercie de TDD)
echo "Test unitaire de la fonction getFilmMakerName : ";
$item = getFilmMakerByName('Chamblon');
print_r($item);
if ($item['id'] == 3) {
    echo 'OK !!! pour \'Chamblon\'';
} else {
    echo '### BUG ###';
}
echo "\n";

echo "Test unitaire de la fonction updateFilmMaker : ";
$item = getFilmMakerByName('Chamblon');
var_dump($item);
$id = $item['id']; // se souvenir de l'id pour comparer
$item['firstname'] = 'Gérard';  //changer prénom
$item['lastname'] = 'Menfain';  //changer nom de famille
updateFilmMaker($item);
$readback = getFilmMaker($id);  //relire après modification
var_dump($readback);
if (($readback['firstname'] == 'Gérard') && ($readback['lastname'] == 'Menfain')) {
    echo 'OK !!!';
} else {
    echo '### BUG ###';
}
echo "\n";

?>