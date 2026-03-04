<?php

// Point d'entrée de l'application CLI

// Chargement des classes nécessaires
spl_autoload_register(static function(string $fqcn) {
    // $fqcn contient Src\classe
    // On remplace les \ par des / et on ajoute .php à la fin.
    // on obtient Src/classe.php
    $path = str_replace('\\', '/', $fqcn).'.php';

   // puis chargeons le fichier
    require_once($path);
});

// Initialisation de la configuration de la base de données
require_once 'configDB.php';

use Src\InputCheck;     // Classe d'analyse et de validation des entrées utilisateur
use Src\Command;        // Classe de gestion des commandes utilisateur  
use Src\DBConnect;      // Classe de gestion de la connexion à la base de données
use Src\Contact;        // Classe représentant un contact (entité métier)        
use Src\ContactManager; // Classe de gestion des opérations CRUD sur les contacts en base de données

try {

    // On initialise l'objet Command chargé d'exécuter les commandes de l'utilisateur
    $cde = new Command();

} catch (\Exception $e) {

    die ("Une erreur est survenue lors de l'initialisation de la Command : " . $e->getMessage());

    }

// Tant que l'utilisateur n'a pas saisi la commande 'quit', on continue à lui demander des commandes
while (true) {

    try {

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

                    $cde->help();
                    break;

                case 'list':

                    $cde->list();
                    break;

                case 'create':

                    // On vérifie que le nombre d'argument est conforme
                    // On vérifie que le format de l'email est valide
                    // On vérifie que le format du numéro de téléphone est valide
                    $data = InputCheck::parseAndValidateCreate($args,$command);
                    
                    // On crée un objet Contact 
                    $contact = new Contact();
                    $contact->setName($data['name']);
                    $contact->setEmail($data['email']);
                    $contact->setPhoneNumber($data['phone_number']);

                    // On insert le nouveau contact en BD
                    $cde->create($contact);

                    break;

                case 'modify':

                    // On vérifie que le nombre d'argument est conforme
                    // On vérifie que le premier argument est bien numérique
                    // On vérifie que le format de l'email est valide
                    // On vérifie que le format du numéro de téléphone est valide
                    $data = InputCheck::parseAndValidateModify($args,$command);
                    
                    // On crée un objet Contact 
                    $contact = new Contact();
                    $contact->setId($data['id']);
                    $contact->setName($data['name']);
                    $contact->setEmail($data['email']);
                    $contact->setPhoneNumber($data['phone_number']);

                    // On applique la modification en BD
                    $cde->modify($contact);

                    break;

                case 'detail':

                    // On vérifie que le premier argument est bien un numérique
                    $id = InputCheck::parseId($args,$command);

                    // On va chercher le contact associé à l'ID et on l'affiche
                    $cde->detail($id);

                    break;

                case 'delete':

                    // On vérifie que le premier argument est bien un numérique
                    $id = InputCheck::parseId($args,$command);

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

    } catch (\Exception $e) {

        // Si au moins un des contrôles échoue, on affiche un message d'erreur
        echo $e->getMessage(). PHP_EOL. PHP_EOL;
    }

}



