<?php

class ContactManager
{
    private PDO $pdo;

    // On passe l'objet PDO au constructeur (Injection de dépendance)
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * On récupère tous les contacts de la base de données
     * @return array Tableau de tableaux associatifs
     */
    public function findAll(): array
    {
        // On initialise la requête SQL qui va récupérer la liste de tous les contacts
        $sql = "SELECT id, name, email, phone_number FROM contact";
        
        // Comme il n'y a pas de paramètres extérieurs, on peut utiliser query()
        $statement = $this->pdo->query($sql);

        // fetchAll récupère toutes les lignes dans un tableau
        // Grâce à l'option FETCH_ASSOC dans DBConnect
        // return $statement->fetchAll();

        // On demande à PDO de créer des objets 'Contact'
        // PDO va automatiquement remplir les propriétés privées 
        // dont les noms correspondent aux colonnes de la BDD !
        return $statement->fetchAll(PDO::FETCH_CLASS, Contact::class);
    }
}