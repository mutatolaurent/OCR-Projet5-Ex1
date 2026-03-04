<?php

namespace Src;

/**
 * Classe InputCheck
 * Fournit des outils statiques pour analyser, nettoyer et valider 
 * les entrées utilisateur provenant de l'interface en ligne de commande.
 */
class InputCheck
{
    /**
     * Analyse la ligne de commande brute pour extraire l'action et ses arguments.
     *
     * @param string $line La ligne saisie par l'utilisateur.
     * @return array|null Retourne un tableau ['command', 'args'] ou null si le format est invalide.
     */
    public static function parseCommand(string $line): ?array
    {
        // On cherche le premier mot au début de la chaîne
        // ^([a-z]+) : Capture le premier mot composé de lettres
        // (?:\s+(.*))? : Groupe non-capturant pour l'espace, suivi du reste (capturé)
        $pattern = '/^([a-z]+)(?:\s+(.*))?$/i';
        if (preg_match($pattern, trim($line), $matches)) {
            return [
                'command' => strtolower($matches[1]),
                'args'    => $matches[2] ?? ''
            ];
        }
        return null;
    }

    /**
     * Valide le format d'une adresse email.
     *
     * @param string $email   L'adresse email à vérifier.
     * @param string $command Le nom de la commande (pour le message d'erreur).
     * @return string         L'email nettoyé.
     * @throws Exception      Si l'adresse email est syntaxiquement incorrecte.
     */
    public static function validateEmail(string $email, string $command): string
    {
        $email = trim($email);    
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            // return false;
            throw new \Exception("Erreur : [$command] attend une adresse email valide.");
        }

        return $email;
    }

    /**
     * Valide et normalise un numéro de téléphone au format français.
     *
     * @param string $phone_number Le numéro brut (avec espaces, points ou tirets).
     * @param string $command      Le nom de la commande (pour le message d'erreur).
     * @return string              Le numéro de téléphone composé uniquement de 10 chiffres.
     * @throws Exception           Si le format ne correspond pas à un numéro français.
     */
    public static function validateFrenchPhone(string $phone_number, string $command): string
    {
        $phone_number = trim($phone_number);
        
        // Regex pour format FR : commence par 0, suivi de 1 à 9, puis 4 duos de chiffres
        $pattern = '/^0[1-9](?:[\s.-]?\d{2}){4}$/';

        if (!preg_match($pattern, $phone_number)) 
        {
            // return false;
            throw new \Exception("Erreur : [$command] attend un numéro de téléphone valide (10 chiffres).");
        }

        // Nettoyage : on ne garde que les chiffres pour la base de données
        return preg_replace('/\D/', '', $phone_number);
    }

    /**
     * Découpe et valide les arguments spécifiques à la création d'un contact.
     *
     * @param string $args    La chaîne d'arguments (Nom, Email, Tel).
     * @param string $command Le nom de la commande appelante.
     * @return array          Tableau associatif des données nettoyées ['name', 'email', 'phone_number'].
     * @throws Exception      Si le nombre de paramètres est incorrect ou si les données sont invalides.
     */
    public static function parseAndValidateCreate(string $args, string $command): array
    {
        // On vérifie la p^résence de 3 paramètres séparés par des virgules
        $pattern = '/^([^,]+),\s*([^,]+),\s*([^,]+)$/';

        if (!preg_match($pattern, trim($args), $matches)) {
            throw new \Exception("Erreur : [create] attend 3 paramètres (Nom, Email, Tel) séparés par des virgules");
        }

        // On appelle nos méthodes spécifiques
        return [
            'name'  => trim($matches[1]),
            'email' => self::validateEmail($matches[2],$command),
            'phone_number' => self::validateFrenchPhone($matches[3],$command)
        ];
    }

    /**
     * Valide qu'une chaîne représente un identifiant numérique entier.
     *
     * @param string $args    La chaîne contenant l'ID.
     * @param string $command Le nom de la commande appelante.
     * @return int            L'identifiant converti en entier.
     * @throws Exception      Si l'argument n'est pas un nombre entier positif.
     */
    public static function parseId(string $args, string $command): int
    {
        if (!preg_match('/^(\d+)$/', trim($args))) {
            throw new \Exception("Erreur : [$command] L'identifiant doit être un nombre entier. (Exemple $command 89).");
        }
        return (int)$args;
    }

    /**
     * Découpe et valide les arguments spécifiques à la modification d'un contact.
     *
     * @param string $args    La chaîne d'arguments (Id, Nom, Email, Tel).
     * @param string $command Le nom de la commande appelante.
     * @return array          Tableau associatif ['id', 'name', 'email', 'phone_number'].
     * @throws Exception      Si le format global ou l'une des données est invalide.
     */
    public static function parseAndValidateModify(string $args, string $command): array
    {
        // On vérifie la p^résence de 4 paramètres séparés par des virgules
        $pattern = '/^([^,]+),\s*([^,]+),\s*([^,]+),\s*([^,]+)$/';

        if (!preg_match($pattern, trim($args), $matches)) {
            throw new \Exception("Erreur : [modify] attend 4 paramètres (Id, Nom, Email, Tel) séparés par des virgules");
        }

        // On appelle nos méthodes spécifiques
        return [
            'id' => self::parseId($matches[1],$command),
            'name'  => trim($matches[2]),
            'email' => self::validateEmail($matches[3],$command),
            'phone_number' => self::validateFrenchPhone($matches[4],$command)
        ];
    }

}