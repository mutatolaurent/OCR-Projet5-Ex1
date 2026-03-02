<?php

class ContactManager
{
    private PDO $pdo;

    /**
     * On passe l'objet PDO au constructeur
     * @param PDO $pdo L'objet PDO connecté à la DB
     * @return ContactManager Retourne l'objet ContactManager
     */ 
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * On récupère tous les contacts de la base de données
     * @return array Tableau d'objets de la classe Contact
     */
    public function findAll(): array
    {
        
        // On initialise la requête SQL qui va récupérer la liste de tous les contacts
        $sql = "SELECT id, name, email, phone_number FROM contact";
        
        // Comme il n'y a pas de paramètres extérieurs, on peut utiliser query()
        $statement = $this->pdo->query($sql);

        // On demande à PDO de créer des objets 'Contact'
        // PDO va automatiquement remplir les propriétés privées 
        // dont les noms correspondent aux colonnes de la BDD !
        return $statement->fetchAll(PDO::FETCH_CLASS, Contact::class);
        
    }

    /**
     * Recherche un contact par son ID
     * @param int $id L'identifiant du contact
     * @return Contact|null Retourne l'objet Contact ou null si non trouvé
     */
    public function findById(int $id): ?Contact
    {

        // On prépare la requête avec un marqueur ":" (nommé)
        $sql = "SELECT * FROM contact WHERE id = :id";
        $statement = $this->pdo->prepare($sql);

        // On lie la variable PHP à la requête et on exécute
        $statement->execute(['id' => $id]);

        // On configure le mode de récupération pour cette requête précise
        // . PDO::FETCH_CLASS = chaque ligne de résultat doit être une nouvelle instance (un objet) d'une classe
        // . PDO::FETCH_PROPS_LATE = PDO appelle d'abord le constructeur (avec des arguments vides ou par défaut), puis il injecte les valeurs de la base de données dans les propriétés.
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Contact::class);

        // On récupère le résultat
        $contact = $statement->fetch();

        // fetch() retourne false si aucune ligne n'est trouvée
        return $contact ?: null;
    
    }

    /**
     * Insère un nouveau contact et retourne son nouvel ID
     * @param Contact $contact Objet avec les infos du nouveau contact
     * @return int|false L'ID généré ou false si l'insertion a échoué
    */
    public function insertNew (Contact $contact): int|false
    {
        // Préparation de la requête d'insertion du nouveau contact
        $req = $this->pdo->prepare('INSERT INTO contact (name, email, phone_number) VALUES (:name, :email, :phone_number)');

        // Exécution de la requête d'insertion
        $success =   $req->execute([
            'name'  => $contact->getName(),
            'email' => $contact->getEmail(),
            'phone_number' => $contact->getPhoneNumber()
            ]);
            
        // Si l'insertion en BD est un succès, on récupère l'ID de l'enregistrement créé
        if ($success) {
                
            // lastInsertId() retourne une chaîne, on la caste en int
            $newId = (int)$this->pdo->lastInsertId();
        
            // Succès on retourne l'ID
            return $newId;
        }

        // Echec de l'insertion , on retourne False
        return false;
        
    }
    
    public function deleteById (int $id): bool {
        // TODO
    }

    public function modifyById (int $id): bool {
        // TODO
    }

}