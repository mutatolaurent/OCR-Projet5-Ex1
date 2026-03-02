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
    echo "Une erreur est survenue lors de la connexion à la DB : " . $e->getMessage();
}

// On initialise l'objet Command chargé d'exécuter les commandes de l'utilisateur
$cde = new Command($pdo);

// On attend qu'une commande soit tapée au clavier
while (true) {
    
    $line = readline("Entrez votre commande : ");
        
    // Traitement de la commande list : lister tous les contacts
    if ($line == 'list') 
    { 
        $cde->list();

    } else {
        echo "Commande inconnue : $line\n";
    }  
}



