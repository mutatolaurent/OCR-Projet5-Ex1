<?php

class Command 
{
    private PDO $pdo;

    // On passe l'objet PDO au constructeur (Injection de dépendance)
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function list(): void 
    {
        try {

            // 1. On initialise la connexion
            // $connection = new DBConnect();
            // $pdo = $connection->getPDO();

            // 2. On crée le manager en lui injectant la connexion
            $contactManager = new ContactManager($this->pdo);

            // 3. On récupère les contacts
            $contacts = $contactManager->findAll();

            foreach ($contacts as $contact) {
                // Comme $contact est un objet, et qu'on a __toString(), 
                // on peut l'afficher directement avec echo
                echo $contact . PHP_EOL;
            }

        } catch (Exception $e) {
            echo "[list] Une erreur est survenue sur une requête BD: " . $e->getMessage();
        }
    }
}