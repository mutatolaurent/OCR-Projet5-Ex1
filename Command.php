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
            echo "[Command->list] Une erreur est survenue sur une requête BD: " . $e->getMessage(). PHP_EOL;
        }
    }

    public function detail($id): void 
    {
        try {

            // On crée le manager en lui injectant la connexion
            $contactManager = new ContactManager($this->pdo);

            // 3. On récupère les contacts
            $contact = $contactManager->findById($id);

            // On affiche les détails du contact
            if ($contact) {
                echo $contact . PHP_EOL;
            } else {
                echo "Désolé ! Aucun contact trouvé avec l'ID $id." . PHP_EOL;
            }   

        } catch (Exception $e) {
            echo "[Command->detail] Une erreur est survenue sur une requête BD: " . $e->getMessage(). PHP_EOL;
        }
    }

    /**
     * Crée un nouveau contact
     * @param ContactNew $contact Objet avec les infos du nouveau contact
    */
    public function create (Contact $contactNew): void
    {
        try {
            // echo $contactNew . PHP_EOL;

            // On crée le manager en lui injectant la connexion
            $contactManager = new ContactManager($this->pdo);

            // On demande au manager d'insérer le nouveau contact en BD et de retourner son ID
            $id = $contactManager->insertNew($contactNew);

            // Si l'insertion à réussie, on récupère le contact créé à partir de son ID
            if ($id) {

                // On récupère en BD le contact à partir de son ID
                $contact = $contactManager->findById($id);

                // On affiche les détails du contact comme confirmation que l'opération s'est bien passée
                if ($contact) {
                    echo "Nouveau contact créé : ".$contact . PHP_EOL;
                } else {
                    echo "[Command->create] Echec confirmation création de contact avec l'ID:".$id. PHP_EOL;
                } 

            } else {
                echo "[Command->create] Echec de l'insertion d'un nouveau contact". PHP_EOL;
            }

        } catch (Exception $e) {
            echo "[Command->create] Une erreur est survenue sur une requête BD: " . $e->getMessage();
        }

    }

    public function delete($id): void 
    {
        try {

            // On crée le manager en lui injectant la connexion
            $contactManager = new ContactManager($this->pdo);

            // 3. On récupère les contacts
            $success = $contactManager->deleteById($id);

            // On affiche les détails du contact
            if ($success) {
                echo "Ce contact a bien été supprimé !" . PHP_EOL;
            } else {
                echo "Désolé ! Aucun contact trouvé avec l'ID $id." . PHP_EOL;
            }   

        } catch (Exception $e) {
            echo "[Command->delete] Une erreur est survenue sur une requête BD: " . $e->getMessage(). PHP_EOL;
        }
    }

    public function modify($id): void 
    {
        try {

            // On crée le manager en lui injectant la connexion
            $contactManager = new ContactManager($this->pdo);

            // 3. On récupère les contacts
            $success = $contactManager->modifyById($id);

            // On affiche les détails du contact modifié
            if ($success) {
                // TODO
            } else {
                // TODO
            }   

        } catch (Exception $e) {
            echo "[Command->modify] Une erreur est survenue sur une requête BD: " . $e->getMessage(). PHP_EOL;
        }
    }


    public function help (): void
    {
        echo 
        "help : affiche cette aide\n\n".
        "list : liste les contacts\n\n".
        "create [name], [email], [phone number] : crée un contact\n\n".
        "delete [id] : supprime un contact\n\n".
        "quit : quitte le programme\n\n";
    }

}