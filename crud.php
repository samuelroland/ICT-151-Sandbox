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

//Créer la liste formatée pour la requête sql. ex: "firstname=:firstname, lastname=:lastname, ..." selon les champs d'un tableau associatif
function createListFieldsForQuery($tablename)
{
    $listFields = getAllFieldFromTable($tablename);   //les valeurs à modifier sont celles du tableaux. prendre les clés en un tableau
    $listToSet = "";    //liste d'éléments formatées à set pour l'update
    foreach ($listFields as $oneField) {
        if ($oneField != "id") {    //on exclut le champ id. interdit de changer.
            $listToSet .= $oneField[0] . "=:" . $oneField[0] . ", ";
        }
    }
    $listToSet = substr($listToSet, 0, strlen($listToSet) - 2); //enlever la string ", " de fin
    return $listToSet;
}

function countFilmMakers()
{
    try {
        $dbh = getPDO();   //créer un objet PDO
        $query = "SELECT count(*) as nb FROM filmmakers";//Ecrire la requête.
        $statment = $dbh->prepare($query);  //préparer la requête
        $statment->execute();   //éxecuter la requête
        $queryResult = $statment->fetch(PDO::FETCH_ASSOC);   //aller chercher le résultat
        $dbh = null;    //remettre à zéro
        extract($queryResult);  //$nb seulement
        return $nb;    //retourner le résultat
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

function getFilmMakers()  //prendre tous les éléments
{
    try {
        $dbh = getPDO();
        $query = "SELECT * FROM filmmakers";    //Ecrire la requête.
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


function createFilmMaker($filmMaker)
{
    unset($filmMaker['id']);    //enlever id pour ne pas la update.

    //Préparation de la liste des données
    $listDataToCreate = ":";   //au départ
    $listDataToCreate = ":";   //au départ
    $listDataToCreate .= implode(", :", array_keys($filmMaker));  //liste des données en paramètre.
    $listDataToCreate .= "";

    //Préparation de la liste de champs qui vont être insérés.
    $listFieldToCreate = implode(", ", array_keys($filmMaker));  //liste des données des champs à insérer, séparés par des ", "

    try {
        $dbh = getPDO();   //créer un objet PDO
        $query = "INSERT INTO filmmakers ($listFieldToCreate) VALUES ($listDataToCreate);";//Ecrire la requête.
        $statment = $dbh->prepare($query);  //préparer la requête
        $statment->execute($filmMaker);   //éxecuter la requête
        $filmMaker['id'] = $dbh->lastInsertId();    //enregistrer l'id générée par la base de données.
        $dbh = null;    //remettre à zéro
        return $filmMaker;
    } catch (PDOException $e) { //en cas d'erreur dans le try
        echo "Error!: " . $e->getMessage() . "\n";
        return null;
    }
}

function updateFilmMaker($filmMaker)
{
    $listToSet = createListFieldsForQuery("filmmakers");
    try {
        $dbh = getPDO();   //créer un objet PDO
        $query = "UPDATE filmmakers SET $listToSet WHERE id =:id;";//Ecrire la requête.
        $statment = $dbh->prepare($query);  //préparer la requête
        $statment->execute($filmMaker);   //éxecuter la requête
        return true;    //retourner le résultat
    } catch (PDOException $e) { //en cas d'erreur dans le try
        echo "Error!: " . $e->getMessage() . "\n";
        return false;
    }
}


function deleteFilmMaker($id)
{
    try {
        $dbh = getPDO();   //créer un objet PDO
        $query = "DELETE FROM filmmakers WHERE id=:id;";//Ecrire la requête.
        $statment = $dbh->prepare($query);  //préparer la requête
        $statment->execute(["id" => $id]);   //éxecuter la requête
        $dbh = null;    //remettre à zéro
        return true;    //retourner le résultat
    } catch (PDOException $e) { //en cas d'erreur dans le try
        echo "Error!: " . $e->getMessage() . "\n";
        return false;
    }
}


?>