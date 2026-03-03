<?php

// Inclusion code classe InputCheck
require_once ('InputCheck.php');

// Inclusion code classe DBconnect
require_once ('DBconnect.php');

// Inclusion code classe Contact
require_once ('Contact.php');

// Inclusion code classe ContactManager
require_once ('ContactManager.php');

// Inclusion code classe Command
require_once ('Command.php');

// -------- Old Version
// try {
//     // On initialise la connexion DB
//     $connection = new DBConnect();
//     $pdo = $connection->getPDO();
// } catch (Exception $e) {
//     die ("Une erreur est survenue lors de la connexion à la DB : " . $e->getMessage());
// }

// On initialise l'objet Command chargé d'exécuter les commandes de l'utilisateur
// $cde = new Command($pdo);

// -------- New Version
try {
    // On initialise l'objet Command chargé d'exécuter les commandes de l'utilisateur
    $cde = new Command();
} catch (Exception $e) {
    die ("Une erreur est survenue lors de l'initialisation de la Command : " . $e->getMessage());
}

// On attend qu'une commande soit tapée au clavier
while (true) {

    // On attend la saisie d'une commande au clavier
    $line = readline("Entrez votre commande (help, list, detail, create, modify, delete, quit) : ");

    // On vérifie que le format de commande est conforme à celui attendu
    $input = InputCheck::parseCommand($line);

    // La commande est conforme
    if ($input) {

        // On récupère la commande    
        $command = $input['command'];

        // On récupère les arguments de la commande
        $args = $input['args'];

        // En fonction de la commande, on exécute le traitement approprié
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

                try {
                    // On vérifie que le nombre d'argument est conforme
                    // On vérifie que le format de l'email est valide
                    // On vérifie que le format du numéro de téléphone est valide
                    $data = InputCheck::parseAndValidateCreate($args,$command);

                } catch (Exception $e) {

                    // Si au moins un des contrôles échoue, on affiche un message d'erreur
                    echo $e->getMessage(). PHP_EOL. PHP_EOL;
                    break;
                }
                    
                // On crée un objet Contact 
                $contact = new Contact();
                $contact->setName($data['name']);
                $contact->setEmail($data['email']);
                $contact->setPhoneNumber($data['phone_number']);

                // Insertion nouveau contact en BD
                $cde->create($contact);

                break;

            case 'modify':

                try {
                    // On vérifie que le nombre d'argument est conforme
                    // On vérifie que le premier argument est bien numérique
                    // On vérifie que le format de l'email est valide
                    // On vérifie que le format du numéro de téléphone est valide
                    $data = InputCheck::parseAndValidateModify($args,$command);

                } catch (Exception $e) {

                    // Si au moins un des contrôles échoue, on affiche un message d'erreur
                    echo $e->getMessage(). PHP_EOL. PHP_EOL;
                    break;
                }
                    
                // On crée un objet Contact 
                $contact = new Contact();
                $contact->setId($data['id']);
                $contact->setName($data['name']);
                $contact->setEmail($data['email']);
                $contact->setPhoneNumber($data['phone_number']);

                // echo "Contact à modifier :".$contact. PHP_EOL. PHP_EOL;

                // Insertion nouveau contact en BD
                $cde->modify($contact);

                break;


            case 'detail':

                try {
                    // On vérifie que le premier argument est bien un numérique
                    $id = InputCheck::parseId($args,$command);

                } catch (Exception $e) {

                    // Si ce n'est pas un numérique on affiche un message d'erreur
                    echo $e->getMessage(). PHP_EOL. PHP_EOL;
                    break;
                }

                // On va chercher le contact associé à l'ID et on l'affiche
                $cde->detail($id);

                break;

            case 'delete':

                try {
                    // On vérifie que le premier argument est bien un numérique
                    $id = InputCheck::parseId($args,$command);

                } catch (Exception $e) {

                    // Si ce n'est pas un numérique on affiche un message d'erreur
                    echo $e->getMessage(). PHP_EOL. PHP_EOL;
                    break;
                }

                // On supprime le contact correspondant à l'ID
                $cde->delete($id);

                break;

            case 'quit':
                echo "Au revoir !" . PHP_EOL;
                exit;

            default:
                echo "Commande non traitée. Tapez 'help' pour voir la liste." . PHP_EOL. PHP_EOL;
                break;
        }
    } else {
        echo "Commande invalide. Tapez 'help' pour voir la liste." . PHP_EOL. PHP_EOL;
    }

}



