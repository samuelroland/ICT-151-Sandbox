Mémento en PHP et BDD en ICT-151:
PDO c'est un outil pour travailler avec les bases de données. (comme une pelle c'est un outil pour creuser, il y a plusieurs outils)


Exemple:

$user = "ICT-151";
$pass = "Pa\$\$w0rd";

try {
    $dbh = new PDO('mysql:host=localhost;dbname=mcu', $user, $pass);
    foreach ($dbh->query('SELECT * from actors') as $row) {
        var_dump($row);
    }
    $dbh = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

Avec CMDER:
C:\Users\samuel.roland\Documents\Github\ICT-151-Sandbox (master -> origin)
λ php -f index.php
Array
(
    [id] => 1
    [0] => 1
    [actornumber] => 1
    [1] => 1
    [lastname] => Ruffalo
    [2] => Ruffalo
    [firstname] => Mark
    [3] => Mark
    [birthdate] => 1967-11-22
    [4] => 1967-11-22
    [nationality] => USA
    [5] => USA
)
Array
(
    [id] => 2
    [0] => 2
    [actornumber] => 2
    [1] => 2
    [lastname] => Holland
    [2] => Holland
    [firstname] => Tom
    [3] => Tom
    [birthdate] => 1996-06-01
    [4] => 1996-06-01
    [nationality] => UK
    [5] => UK
)

Mais petit problème d'avoir les identifiants en clair et de les publier sur Github. donc on va faire un autre fichier '.const.php' qui contient les constantes pour la connexion.

<?php
/**
 *  Projet: ICT-151-Sandbox
 *  Filename: Identifiants de login
 *  Author: Samuel Roland
 *  Creation date: 07.02.2020
 */

$user = "ICT-151";
$pass = "Pa\$\$w0rd";
$dbhost = "localhost";
$dbname = "mcu";
?>


Puis on ignore le fichier pour ne pas l'envoyer sur Git.

sauf que les développeurs qui travaillent avec nous ne savent pas qu'on a fait ca. Donc on fait une copie en .example qu'ils pourront renommer et remplir.

<?php
/**
 *  Projet: ICT-151-Sandbox
 *  Filename: Identifiants de login
 *  Author: Samuel Roland
 *  Creation date: 07.02.2020
 */
//TODO: renommer en .const.php et remplir les valeurs pour la database
$user = "";
$pass = "";
$dbhost = "";
$dbname = "";
?>


Pour faire une requête il y a 4 étapes:

On crée un objet: $dbh = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname, $user, $pass);

1. La requête SQL dans une string:


2. Préparer la requête:
$statment = $dbh->prepare($query);  //préparer la requête = envoyer au serveur web (vérification de type sécuritaire)

3. On peut éxecuter:
$statment->execute();   //éxecuter la requête

4. Aller chercher tous les résultats:
$queryResult = $statment->fetchAll();   //aller chercher le résultat


Faire des tests unitaires:
Une fois qu'on a une fonction (changée le code précédent dans une fonction):

function getAllItems()  //prendre tous les éléments
{
    require_once '.const.php';
    try {
        $dbh = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname, $user, $pass);
        $query = "SELECT filmmakersnumber, lastname, firstname FROM filmmakers";
        $statment = $dbh->prepare($query);  //préparer la requête
        $statment->execute();   //éxecuter la requête
        $queryResult = $statment->fetchAll();   //aller chercher le résultat
        $dbh = null;
        return $queryResult;
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        return null;
    }

}

on peut faire un test unitaire:

//Test unitaire de la fonction getAllItems:
$items = getAllItems();
if (count($items) == 4) {
    echo "OK !!";
} else {
    echo "BUG ...";
}



TDD = Test Driven Developpement
Principe de développement où on commence par faire les tests puis on fait le code jusqu'à que le test fonctionne.

Pour ne pas avoir un tableau indexé et associatif (toutes les données étant donc à double), il faut mettre un paramètre au fetchAll() qui dit le type de tableau qu'il doit retourner:

- PDO::FETCH_ASSOC pour avoir un tableau associatif uniquement
- PDO::FETCH_NUM pour avoir un tableau indexé uniquement (partant de index 0)

Changement:
$queryResult = $statment->fetchAll();
en
$queryResult = $statment->fetchAll(PDO::FETCH_ASSOC);

source: https://www.php.net/manual/en/pdostatement.fetch

$query = "UPDATE filmmakers SET
		filmmakers:
"