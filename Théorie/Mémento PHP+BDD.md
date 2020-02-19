# Mémento en PHP et BDD en ICT-151:
## Intégrer des BDD dans des applis WEB

### Introduction:
Dans ce cours, on travaille avec PDO (PHP Data Object s?). C'est un outil pour travailler avec les bases de données. (comme pour la pelle qui est un outil pour creuser, il y a plusieurs outils mais nous n'utiliserons dans ce cours que PDO).

Pour ce mémento, on utilise une base de donnée appelée `mcu` qui contient des données sur des films marvel.


Exemple de première utilisation (code repris de php.net et valeurs adaptées):

    //Identifiants pour la DBB
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

Avec CMDER ou tout autre shell:

    C:\Users\john\Documents\Github\ICT-151-Sandbox (master -> origin)  //on vient sur DOCUMENTROOT du serveur.
    λ php -f index.php  //fichier php à exécuter
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

Résultat: Il exécute le fichier php et affiche donc les deux enregistrements trouvés dans la BDD.

Mais c'est un problème d'avoir les identifiants en clair dans le code et de les publier sur Github.
C'est pour cette raison qu'on va faire un fichier séparé `.const.php` qui contient uniquement les constantes des informations pour la connexion à la BDD. Le fichier commence par un `.`. C'est une convention pour les fichiers cachés.

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

Ensuite:
- on ignore le fichier en l'ajoutant au `.gitignore` pour ne pas l'envoyer sur Git.
- on le récupère le contenu du fichier par un `require .const.php;` en haut de la fonction. Attention à ne pas utiliser `require_once` puisque plusieurs fonctions vont en avoir besoin.

Le problème qui arrive maintenant est que les développeurs qui travaillent avec nous ne savent pas qu'on a fait ce fichier séparé puisque ce fichier n'est pas envoyé sur Github. (sauf si il lise le `require .const.php;` mais ils ne savent pas ce qu'il y a dedans précisément). Pour résoudre ce problème, on fait une copie du fichier nommée `.const.php.example` avec les variables mais sans valeurs. Ils pourront ensuite dupliquer le fichier et le renommer, et remplir les valeurs pour arriver au même point que nous. Le `*.example` à la fin du nom du fichier est souvent utilisé dans le développement et donc facilement compréhensibles par d'autres développeurs.

Exemple de contenu du fichier:

    <?php
    /**
    *  Projet: ICT-151-Sandbox
    *  Filename: Identifiants de login
    *  Author: Samuel Roland
    *  Creation date: 07.02.2020
    */
    
    //TODO: renommer le fichier en .const.php et remplir les valeurs pour la database
    $user = "";
    $pass = "";
    $dbhost = "";
    $dbname = "";
    ?>

Pour faire une requête il y a 4 étapes:

On crée un objet PDO de cette manière:

    $dbh = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname, $user, $pass);

1. La requête SQL dans une string:

        $query = "SELECT id, lastname, firstname FROM filmmakers"; 
    

2. Préparer la requête = envoyer au serveur web (vérification de type sécuritaire):

        $statment = $dbh->prepare($query);

3. On peut éxecuter la requête:
    
        $statment->execute();

4. Aller chercher tous les résultats:
    
        $queryResult = $statment->fetchAll();
Ou un seul résultat:
$queryResult = $statment->fetch();

fetchAll() retourne un tableau d'éléments (étant des tableaux contenants les informations d'un enregistrement).


### Faire des tests unitaires:
Une fois qu'on a une fonction (changée le code précédent dans une fonction):

    function getAllItems()  //prendre tous les éléments
    {
        require_once '.const.php';
        try {
            $dbh = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname, $user, $pass);
            $query = "SELECT filmmakersnumber, lastname, firstname FROM filmmakers";    //écrire la requête
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