<?php
/**
 *  Projet: ICT-151-SandBox
 *  Filename: index.php
 *  Author: Samuel Roland
 *  Creation date: 06.02.2020
 */


function getAllItems()  //prendre tous les éléments
{
    require '.const.php';  //récuperer les identifiants
    try {   //essayer
        $dbh = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname, $user, $pass);   //créer un objet PDO
$query = "SELECT filmmakersnumber, lastname, firstname FROM filmmakers";//Ecrire la requête.
        $statment = $dbh->prepare($query);  //préparer la requête
        $statment->execute();   //éxecuter la requête
        $queryResult = $statment->fetchAll();   //aller chercher le résultat
        $dbh = null;    //remettre à zéro
        return $queryResult;    //retourner le résultat
    } catch (PDOException $e) { //en cas d'erreur dans le try
        echo "Error!: " . $e->getMessage() . "<br/>";
        return null;
    }
}


//Test unitaire de la fonction getAllItems:
$items = getAllItems();
if (count($items) == 4) {
    echo "getAllItems() retourne 4 éléments OK !!";
} else {
    echo "getAllItems() retourne null BUG ...";
}
?>