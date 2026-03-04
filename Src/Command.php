<?php

namespace Src;

/**
 * Classe Command
 * Cette classe fait office de Contrôleur pour l'application CLI.
 * Elle reçoit les données validées, sollicite le ContactManager pour les opérations 
 * en base de données et gère l'affichage des résultats à l'utilisateur.
 */
class Command 
{
    
    /**
     * @var ContactManager Instance du manager de contacts pour les opérations CRUD.
     */
    // private PDO $pdo;
    private ContactManager $contactManager;

    /**
     * Constructeur de la classe Command.
     * Instancie un objet ContactManager en interne, qui gère lui-même sa propre connexion à la base de données.
     */
    public function __construct()
    {
        $this->contactManager = new ContactManager();
    }

    /**
     * Affiche la liste de tous les contacts enregistrés.
     * @return void
     */
    public function list(): void 
    {
        try {

            // 2. On crée le manager en lui injectant la connexion
            // $contactManager = new ContactManager($this->pdo);

            // On récupère les contacts
            $contacts = $this->contactManager->findAll();

            foreach ($contacts as $contact) {
                // Comme $contact est un objet, et qu'on a __toString(), 
                // on peut l'afficher directement avec echo
                echo $contact . PHP_EOL;
            }

        } catch (\Exception $e) {
            echo "[Command->list] Une erreur est survenue sur une requête BD: " . $e->getMessage(). PHP_EOL. PHP_EOL;
        }
    }

    /**
     * Affiche les détails d'un contact spécifique par son identifiant.
     * @param int $id L'identifiant du contact à rechercher.
     * @return void
     */
    public function detail(int $id): void 
    {
        try {

            // On crée le manager en lui injectant la connexion
            // $contactManager = new ContactManager($this->pdo);

            // 3. On récupère les contacts
            $contact = $this->contactManager->findById($id);

            if (!$contact) {
                echo PHP_EOL."Désolé ! Aucun contact trouvé avec l'ID $id." . PHP_EOL. PHP_EOL;
                return;
            }
        
            echo PHP_EOL.$contact . PHP_EOL. PHP_EOL;

        } catch (\Exception $e) {
            echo "[Command->detail] Une erreur est survenue sur une requête BD: " . $e->getMessage(). PHP_EOL. PHP_EOL;
        }
    }

    /**
     * Crée un nouveau contact dans la base de données.
     * * Après l'insertion, effectue une nouvelle requête pour confirmer 
     * l'existence du contact créé.
     * * @param Contact $contactNew L'objet Contact contenant les données à insérer.
     * @return void
     */
    public function create (Contact $contactNew): void
    {
        try {

            // On demande au manager d'insérer le nouveau contact en BD et de retourner son ID
            $id = $this->contactManager->insertNew($contactNew);

            // CAS D'ERREUR 1 : L'insertion a échoué
            if (!$id) {
                echo "[Command->create] Echec de l'insertion d'un nouveau contact" . PHP_EOL . PHP_EOL;
                return;
            }

            // On tente de récupérer en BD le contact créé
            $contact = $this->contactManager->findById($id);

            // CAS D'ERREUR 2 : Le contact est introuvable après insertion
            if (!$contact) {
                echo "[Command->create] Echec confirmation création de contact avec l'ID:" . $id . PHP_EOL . PHP_EOL;
                return;
            }

            // SUCCÈS : Tout est ok, on affiche la confirmation
            echo PHP_EOL ."Nouveau contact créé : " . $contact . PHP_EOL . PHP_EOL;

        } catch (\Exception $e) {
            echo "[Command->create] Une erreur est survenue sur une requête BD: " . $e->getMessage(). PHP_EOL. PHP_EOL;
        }

    }

    /**
     * Supprime un contact de la base de données par son identifiant.
     * * @param int $id L'identifiant du contact à supprimer.
     * @return void
     */
    public function delete(int $id): void 
    {
        try {

            // On récupère les contacts
            $count = $this->contactManager->deleteById($id);

            // CAS D'ÉCHEC : le contact n'a pas été trouvé et supprimé
            if ($count <= 0) {
                echo PHP_EOL ."Désolé ! Aucun contact trouvé avec l'ID $id." . PHP_EOL . PHP_EOL;
                return;
            }

            // SUCCÈS : On affiche un feedback du succès de la suppression
            echo PHP_EOL . "Le contact $id a bien été supprimé !" . PHP_EOL . PHP_EOL;

        } catch (\Exception $e) {
            echo "[Command->delete] Une erreur est survenue sur une requête BD: " . $e->getMessage(). PHP_EOL. PHP_EOL;
        }
    }

    /**
     * Modifie les informations d'un contact existant.
     * * @param Contact $contact L'objet Contact contenant l'ID et les nouvelles informations.
     * @return void
     */
    public function modify(Contact $contact): void 
    {
        try {

            // On modifie les informations du contact
            $count = $this->contactManager->modifyById($contact);

            // CAS D'ERREUR 1 : Rien n'a été modifié (ID introuvable ou aucune donnée changée)
            if ($count <= 0) {
                echo PHP_EOL . "[Command->modify] Echec modification ou aucun contact trouvé avec cet ID " . $contact->getId() . PHP_EOL . PHP_EOL;
                return;
            }

            // On récupère en BD le contact à partir de son ID, pour confirmation que la modification a bien fonctionné
            $contactModified = $this->contactManager->findById($contact->getId());

            // CAS D'ERREUR 2 : Le contact est introuvable juste après la modif
            if (!$contactModified) {
                echo PHP_EOL . "[Command->modify] Echec confirmation modification de contact avec l'ID:" . $contact->getId() . PHP_EOL . PHP_EOL;
                return;
            }

            // SUCCÈS : On affiche un feedback du succès de la modification
            echo PHP_EOL . "Contact modifié avec succès : " . $contactModified . PHP_EOL . PHP_EOL;

        } catch (\Exception $e) {
            echo PHP_EOL."[Command->modify] Une erreur est survenue sur une requête BD: " . $e->getMessage(). PHP_EOL. PHP_EOL;
        }
    }

    /**
     * Affiche l'aide utilisateur listant les commandes disponibles et leur syntaxe.
     * * @return void
     */
    public function help (): void
    {
        echo 
        "\nhelp : affiche cette aide\n\n".
        "list : liste les contacts\n\n".
        "create [name], [email], [phone number] : crée un contact\n\n".
        "modify [id], [name], [email], [phone number] : modifie un contact\n\n".
        "delete [id] : supprime un contact\n\n".
        "quit : quitte le programme\n\n";
    }

}