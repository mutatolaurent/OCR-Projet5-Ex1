<?php

// Inclusion code classe DBconnect
require_once ('DBconnect.php');

// Inclusion code classe Contact
require_once ('Contact.php');

// Inclusion code classe ContactManager
require_once ('ContactManager.php');

// Inclusion code classe Command
require_once ('Command.php');

try {
    // On initialise la connexion DB
    $connection = new DBConnect();
    $pdo = $connection->getPDO();
} catch (Exception $e) {
    die ("Une erreur est survenue lors de la connexion à la DB : " . $e->getMessage());
}

// On initialise l'objet Command chargé d'exécuter les commandes de l'utilisateur
$cde = new Command($pdo);

// On attend qu'une commande soit tapée au clavier
while (true) {

    $line = readline("Entrez votre commande (help, list, detail, create, delete, quit) : ");

    /**
    * EXPLICATION DE LA REGEX :
    * ^     : Début de la chaîne
    * (help|list|detail|create|delete|quit) : Groupe 1 (La commande)
    * (?:   : Début d'un groupe non-capturant (pour l'espace et le nombre)
    * \s+   : Un ou plusieurs espaces
    * (\d+) : Groupe 2 (Un ou plusieurs chiffres - l'ID)
    * )?    : Le groupe (espace + nombre) est optionnel
    * $     : Fin de la chaîne
    */
    // $pattern = '/^(help|list|detail|create|delete|quit)(?:\s+(\d+))?$/';
    // $pattern = '/^(help|list|quit)|(?:(detail|delete)\s+(\d+))|(?:(create)\s+([^,]+),\s*([^,]+),\s*([^,]+))$/';

    // --- ÉTAPE 1 : Isoler la commande du reste ---
    // On cherche le premier mot au début de la chaîne
    // ^([a-z]+) : Capture le premier mot composé de lettres
    // (?:\s+(.*))? : Groupe non-capturant pour l'espace, suivi du reste (capturé)
    $patternStep1 = '/^([a-z]+)(?:\s+(.*))?$/i';

    if (preg_match($patternStep1, $line, $matches)) {
        $command = strtolower($matches[1]);
        $args = $matches[2] ?? ''; // Le reste de la ligne après l'espace

    // if (preg_match($pattern, $line, $matches)) {
    // $matches[0] contient la chaîne entière
    // $matches[1] contient le nom de la commande
    // $matches[2] contient l'ID (si présent), sinon il n'existe pas ou est vide
        // $matches = array_values(array_filter($matches));
        // $command = $matches[1];
        echo "COMMANDE :".$command . PHP_EOL;
        // $id = isset($matches[2]) ? (int)$matches[2] : null;

        switch ($command) {
            case 'help':
                // echo "Liste des commandes disponibles : help, list, detail, create, delete, quit" . PHP_EOL;
                $cde->help();
                break;

            case 'list':
                // echo "Affichage de tous les contacts..." . PHP_EOL;
                $cde->list();
                break;

            case 'create':

                // On vérifie la p^résence de 3 paramètres séparés par des virgules
                $patternCreate = '/^([^,]+),\s*([^,]+),\s*([^,]+)$/';
                if (preg_match($patternCreate, $args, $argMatches)) {
                    $name = trim($argMatches[1]);
                    $email = trim($argMatches[2]);
                    $phone_number = trim($argMatches[3]);
                    // echo 'create [name]='.$name.', [email]='.$email.', [phone]='.$phone_number. PHP_EOL;

                    // On contrôle de la validité du format de l'email
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        echo "Erreur : [create] attend une adresse email valide.". PHP_EOL;
                        break;
                    }

                    // On contrôle de la validité du format du n° de téléphone
                    $patternPhone = '/^0[1-9](?:[\s.-]?\d{2}){4}$/';
                    if (!preg_match($patternPhone, $phone_number)) {
                        echo "Erreur : [create] attend un numéro de téléphone valide.". PHP_EOL;
                        break;
                    }
                    
                    // On crée un objet Contact 
                    $contact = new Contact();
                    $contact->setName($name);
                    $contact->setEmail($email);
                    $contact->setPhoneNumber($phone_number);

                    // Insertion nouveau contact en BD
                    $cde->create($contact);

                } else {
                    echo "Erreur : [create] attend 3 paramètres (Nom, Email, Tel) séparés par des virgules" . PHP_EOL;
                }
                break;

            case 'detail':

                // On vérifie que l'ID est numérique
                if (preg_match('/^(\d+)$/', $args, $argMatches)) {
                    $id = $argMatches[1];

                    // On va chercher le contact associé à l'ID et on l'affiche
                    $cde->detail($id);

                } else {
                    echo "Erreur : '$command' attend un ID (ex: $command 89)" . PHP_EOL;
                }
                break;

            case 'delete':

                // On vérifie que l'ID est numérique
                if (preg_match('/^(\d+)$/', $args, $argMatches)) {
                    $id = $argMatches[1];

                    // On va chercher le contact associé à l'ID et on l'affiche
                    $cde->delete($id);

                } else {
                    echo "Erreur : '$command' attend un ID (ex: $command 89)" . PHP_EOL;
                }
                break;


                if ($id === null) {
                    echo "Erreur : La commande 'delete' nécessite un ID (ex: delete 9)" . PHP_EOL;
                } else {
                    echo "Suppression de l'élément $id..." . PHP_EOL;
                }
                break;

            case 'quit':
                echo "Au revoir !" . PHP_EOL;
                exit;

            default:
                echo "Commande reconnue mais non traitée." . PHP_EOL;
                break;
        }
    } else {
        echo "Commande invalide. Tapez 'help' pour voir la liste." . PHP_EOL;
    }

}



