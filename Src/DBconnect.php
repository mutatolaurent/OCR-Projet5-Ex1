<?php

namespace Src;

class DBConnect
{
    // Soit un objet PDO, soit null.    
    private ?\PDO $pdo = null;

    // Configuration de la base de données
    private string $host = DB_HOST;
    private string $port = DB_PORT;
    private string $dbName = DB_NAME;
    private string $user = DB_USER;
    private string $password = DB_PASSWORD;
    private string $charset = DB_CHARSET;

    /**
     * Retourne l'objet PDO instancié.
     * Si la connexion n'existe pas encore, elle est créée.
     */
    public function getPDO(): \PDO
    {
        // On vérifie si on est déjà connecté pour ne pas recréer de connexion
        if ($this->pdo === null) 
        {
            // DSN (Data Source Name) : la chaîne de configuration
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbName};charset={$this->charset}";

            // Options de sécurité et de débogage
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, // Lance une exception en cas d'erreur SQL
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, // Récupère les données sous forme de tableau associatif
                \PDO::ATTR_EMULATE_PREPARES => false, // Utilise les vraies requêtes préparées de MySQL
            ];

            $this->pdo = new \PDO($dsn, $this->user, $this->password, $options);
                
        }

        return $this->pdo;
    }
}

// --- EXEMPLE D'UTILISATION ---

// $db = new DBConnect();
// $pdo = $db->getPDO();

// var_dump($db);

// echo "Connexion réussie !";