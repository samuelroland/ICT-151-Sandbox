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

function getFilmMakerByName($lastname)
{
    require '.const.php';  //récuperer les identifiants
    try {   //essayer
        $dbh = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname, $user, $pass);   //créer un objet PDO
        $query = "SELECT * FROM filmmakers WHERE lastname = '" . $lastname . "';";//Ecrire la requête.
        $statment = $dbh->prepare($query);  //préparer la requête
        $statment->execute();   //éxecuter la requête
        $queryResult = $statment->fetch(PDO::FETCH_ASSOC);   //aller chercher le résultat
        $dbh = null;    //remettre à zéro
        return $queryResult;    //retourner le résultat
    } catch (PDOException $e) { //en cas d'erreur dans le try
        echo "Error!: " . $e->getMessage() . "<br/>";
        return null;
    }
}

function makerOf($filmname)
{    //trouver le réalisateur d'un film:
    require '.const.php';
    try {
        $dbh = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname, $user, $pass);   //créer un objet PDO
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
        echo "Error!: " . $e->getMessage() . "<br/>";
        return null;
    }

}

echo "<br/>";
echo "Test unitaire de la fonction getAllItems:";
$items = getAllItems();
if (count($items) == 4) {
    echo "getAllItems() retourne 4 éléments OK !!";
} else {
    echo "getAllItems() retourne null BUG ...";
}

echo "<br/>";
$filmname = "Ant-Man";
$filmmakers = makerOf($filmname);
echo "Test unitaire de la fonction getAllItems: Quel sont les réalisateurs de $filmname ??  \n";

if ($filmmakers[0]['firstname'] == "Jean-Philippe" && $filmmakers[2]['firstname'] == "Jean-Michel") {
    echo "makerOf() retourne Jean-Philippe et Jean-Michel donc OK !!";
} else {
    echo "makerOf() retourne null donc BUG ...";
}
echo "\n";
//Test écrit par M. Carrel (exercie de TDD.)
echo "Test unitaire de la fonction getFilmMakerName : ";
$item = getFilmMakerByName('Chamblon');
print_r($item);
if ($item['id'] == 3) {
    echo 'OK !!!';
} else {
    echo '### BUG ###';
}
echo "\n";

echo "Test unitaire de la fonction updateFilmMaker : ";
$item = getFilmMakerByName('Chamblon');
$id = $item['id']; // se souvenir de l'id pour comparer
$item['firstname'] = 'Gérard';
$item['lastname'] = 'Menfain';
updateFilmMaker($item);
$readback = getFilmMaker($id);
if (($readback['firstname'] == 'Gérard') && ($readback['lastname'] == 'Menfain')) {
    echo 'OK !!!';
} else {
    echo '### BUG ###';
}
echo "\n";

?>