<?php
/**
 *  Projet: ICT-151-SandBox
 *  Filename: index.php
 *  Author: Samuel Roland
 *  Creation date: 06.02.2020
 */
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


?>