# Mémento en PHP et BDD en ICT-151:
## Intégrer des BDD dans des applis WEB

### Introduction:
Dans ce cours, on travaille avec PDO (PHP Data Object s?). C'est un outil pour travailler avec les bases de données. (comme pour la pelle qui est un outil pour creuser, il y a plusieurs outils pour travailler avec des bases de données, mais nous n'utiliserons dans ce cours que PDO).

Pour ce mémento, on utilise une base de donnée appelée `mcu` qui contient des données sur des films marvel. On retrouve le script [ici](../Restore-MCU-PO-Final.sql).

### CRUD
En résumé, ce sont les 4 fonctionnalités de base dans beaucoup d'applications en informatiques, pour intéragir avec des données.
- **C**reate: créer
- **R**ead: lire
- **U**pdate: mettre à jour/modifier
- **D**elete: supprimer

### Notions de bases
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

fetchAll() retourne un tableau de tableaux associatifs. tandis que fetch() retourne un tableau associatif qui ne contient donc qu'un seul enregistrement.



Visuellement ca donne ca:

![Fetch-FetchAll.png](asdf)

**ATTENTION particularité**.
Pour ne pas avoir un tableau indexé et associatif (créé par fetch() ou fetchAll()) en même temps (toutes les données étant donc à double), il faut mettre un paramètre aux méthodes qui dit le type de tableau qu'il doit retourner. Ces paramètres sont des constantes internes de PDO. On les atteind de la manière suivante `PDO::NomConstante`

Une petite liste de possibilités très utiles:
- `PDO::FETCH_ASSOC` pour avoir un tableau associatif uniquement
- `PDO::FETCH_NUM` pour avoir un tableau indexé uniquement (partant de index 0)

Changement:
    
    $queryResult = $statment->fetchAll();
en
    
    $queryResult = $statment->fetchAll(PDO::FETCH_ASSOC);

source: https://www.php.net/manual/en/pdostatement.fetch


### Faire des tests unitaires:

Les tests unitaires permettent de tester le bon fonctionnement de chaque fonction séparément (unitaire donc on teste qu'une seule fonction). Dans ce cours, on fait des tests unitaires **des fonctions du modèle** et on lance les tests depuis un shell donc sans passer par un navigateur.

**IMPORTANT**: Pour faire des tests unitaires, on a besoin de données dont on le seul à modifier, et surtout on a besoin de pouvoir "connaitre" les données. En effet, si on veut tester qu'une fonction qui récupère un utilisateur, comment vérifier les différentes informations si on les connait pas ? On a besoin d'accéder à la base de données avec un client SQL (ou par un autre moyen) et pouvoir constater que l'utilisateur `2355` a les meme informations que ce que nous donne le résultat de notre fonction, par exemple.

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

on peut faire un test unitaire simple:

    //Test unitaire de la fonction getAllItems:
    $items = getAllItems();
    if (count($items) == 4) {
        echo "OK !!";
    } else {
        echo "BUG ...";
    }

### Comment construire des tests unitaires ?
Voici des explications d'une proposition de structure et d'une logique pour des tests basiques, pour des fonctions CRUD, notamment quelques critères de vérification:

Idée de structure d'un test:
- un titre "Test de la fonction getUsers()"
- Préparer des données
- Utiliser la fonction pour créer faire une action de CRUD
- Tester si le résultat est celui souhaité en vérifiant certains critères
- Affichage d'une erreur ou que le test a réussi.
    
### Du PHP dans un shell ?
Oui cest possible ! Enfin disons que le résultat généré est affiché en mode console. Donc pas vraiment fait pour une vue. Par contre pour des tests ou la gestion du serveur, c'est pratique.

#### Démarrer un serveur php si l'application php.exe est installée (avec Choco, npm, ...)
Pour ne pas utiliser d'IDE, on peut lancer le serveur de la manière suivante. 

La commande est construite ainsi: `php -S hote:port`. pour `-S` pensez à "**S**tart". Voyons voir en pratique ce que ca donne.

1. se placer à la racine du site `cd C:/Users/John/Documents/AppWeb/`
1. taper `php -S localhost:8080`. le serveur démarre et affiche les erreurs en cas de problèmes.
1. ouvrir un navigateur web à l'adresse: `localhost:8080` et on accède au site !


#### Executer un fichier php (ici un test unitaire)
1. se placer à la racine du site `cd C:/Users/John/Documents/AppWeb/`
1. savoir dans quel sous dossier se trouve le fichier de test qu'on veut lancer.
1. lancer le fichier avec la commande `php -f <testfile.php>` ou `php -f <unitTests/testfile.php>` si il est placé dans un sous-dossier.

**ATTENTION**.
Si il y a des chemins de fichiers dans le code (pour rechercher des données d'un fichier .json par ex.), les liens relatifs par rapport à la racine du site pourrait poser problème si on execute depuis le dossier `unitTests` puisque les liens seront relatifs au dossier du shell.
Pour ne pas devoir changer 2 fois tous les liens relatifs, il est possible et conseillé de lancer les tests depuis le dossier du fichier `index.php` (donc la racine du site) ou du fichier qui est appelé en premier et donc d'où les liens relatifs partent. Par la suite, il suffit de pointer le fichier de test d'un sous-dossier, par exemple `php -f unitTests/testfile.php`


### TDD = Test Driven Developpement

Littéralement: Developpement conduit/dirigé/guidé par des tests.

Principe de développement où on commence par faire les tests puis on fait le code de ce qui est testé (une fonction par exemple), et on code jusqu'à que le test fonctionne. Le développement est donc guidé par des tests.


$query = "UPDATE filmmakers SET
		filmmakers:
"