<?php

/**
 * Classe ContactManager
 * * Assure la persistance des données entre les objets de la classe Contact 
 * et la table 'contact' de la base de données via PDO.
 */
class ContactManager
{
    /**
     * @var PDO Instance de connexion à la base de données.
     */
    private PDO $pdo;

    // ------------ OLD VERSION : AVEC INJECTION DE DEPENDANCE DE LA CONNEXION PDO ------------
    /**
     * Constructeur du Manager.
     * * @param PDO $pdo L'objet PDO connecté à la base de données.
     */
    // public function __construct(PDO $pdo)
    // {
    //     $this->pdo = $pdo;
    // }

    // ------------ NEW VERSION : SANS INJECTION DE DEPENDANCE, LE MANAGER GERE LUI-MEME SA CONNEXION ------------
    /**
     * Constructeur du Manager.
     * On initialise la connexion à la base de données en interne, sans injection de dépendance.
     */
    public function __construct()
    {
        // On initialise la connexion DB
        $connection = new DBConnect();
        $this->pdo = $connection->getPDO();
    }


    /**
     * On récupère tous les contacts de la base de données
     * @return array Tableau d'objets de la classe Contact
     */
    /**
     * Récupère la liste exhaustive des contacts présents en base de données.
     * * @return Array of Contact Tableau d'objets de la classe Contact.
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
     * Recherche un contact spécifique par son identifiant unique.
     * * @param int $id L'identifiant du contact recherché.
     * @return Contact|null L'objet Contact correspondant ou null si aucun enregistrement n'existe.
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
     * Insère un nouveau contact dans la base de données.
     * * @param Contact $contact L'objet Contact contenant les informations à enregistrer.
     * @return int|false Le nouvel ID généré par la base de données ou false en cas d'échec.
     */
    public function insertNew (Contact $contact): int|false
    {
        // Préparation de la requête d'insertion du nouveau contact
        $reqInsert = $this->pdo->prepare('INSERT INTO contact (name, email, phone_number) VALUES (:name, :email, :phone_number)');

        // Exécution de la requête d'insertion
        $success =   $reqInsert->execute([
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
    
    /**
     * Supprime définitivement un contact de la base de données.
     * * @param int $id L'identifiant du contact à supprimer.
     * @return int Le nombre de lignes supprimées (1 en cas de succès, 0 si l'ID n'existait pas).
     */
    public function deleteById (int $id): int {
        
        // Préparation de la requête de suppression du contact    
        $reqDelete = $this->pdo->prepare("DELETE FROM contact WHERE id = :id");
        
        // Exécution de la requête de suppression
        $reqDelete->execute(['id' => $id]);

        // rowCount() permet de savoir si MySQL a réellement trouvé et supprimé une ligne
        return $reqDelete->rowCount();
    }

    /**
     * Met à jour les informations d'un contact existant.
     * * @param Contact $contact L'objet Contact contenant l'ID et les nouvelles valeurs.
     * @return int Le nombre de lignes modifiées (0 si aucune donnée n'a changé ou ID inconnu).
     */
    public function modifyById (Contact $contact): int {

        $reqUpdate = $this->pdo->prepare("UPDATE contact 
                SET name = :name, email = :email, phone_number = :phone_number 
                WHERE id = :id");
        
        $reqUpdate->execute([
            'id'    => $contact->getId(),
            'name'  => $contact->getName(),
            'email' => $contact->getEmail(),
            'phone_number' => $contact->getPhoneNumber()
        ]);

        // Retourne le nombre de lignes modifiées
        return $reqUpdate->rowCount();
    }

}