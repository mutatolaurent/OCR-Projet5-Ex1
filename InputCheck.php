<?php

class InputCheck
{
    /**
     * Analyse la ligne tapée pour séparer la commande des arguments.
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
     * Retourne l'email nettoyé ou False
     */
    public static function validateEmail(string $email, string $command): string|false
    {
        $email = trim($email);    
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            // return false;
            throw new Exception("Erreur : [$command] attend une adresse email valide.");
        }

        return $email;
    }

    public static function validateFrenchPhone(string $phone_number, string $command): string|false
    {
        $phone_number = trim($phone_number);
        
        // Regex pour format FR : commence par 0, suivi de 1 à 9, puis 4 duos de chiffres
        $pattern = '/^0[1-9](?:[\s.-]?\d{2}){4}$/';

        if (!preg_match($pattern, $phone_number)) 
        {
            // return false;
            throw new Exception("Erreur : [$command] attend un numéro de téléphone valide (10 chiffres).");
        }

        // Nettoyage : on ne garde que les chiffres pour la base de données
        return preg_replace('/\D/', '', $phone_number);
    }

    /**
     * Analyse les arguments de la commande 'create'.
     * Vérifie si le format de l'email est valide
     * Vérifie si le format du numéro de téléphone est valide
     * Retourne un tableau avec les données nettoyée.
     */
    public static function parseAndValidateCreate(string $args, string $command): array
    {
        // On vérifie la p^résence de 3 paramètres séparés par des virgules
        $pattern = '/^([^,]+),\s*([^,]+),\s*([^,]+)$/';

        if (!preg_match($pattern, trim($args), $matches)) {
            throw new Exception("Erreur : [create] attend 3 paramètres (Nom, Email, Tel) séparés par des virgules");
        }

        // On appelle nos méthodes spécifiques
        return [
            'name'  => trim($matches[1]),
            'email' => self::validateEmail($matches[2],$command),
            'phone_number' => self::validateFrenchPhone($matches[3],$command)
        ];
    }

    /**
     * Vérifie si l'argument est un ID numérique.
     */
    public static function parseId(string $args, string $command): int
    {
        if (!preg_match('/^(\d+)$/', trim($args))) {
            throw new Exception("Erreur : [$command] L'identifiant doit être un nombre entier. (Exemple $command 89).");
        }
        return (int)$args;
    }

    /**
     * Analyse les arguments de la commande 'modify'.
     * Vérifie si le premier argument est bien un entier
     * Vérifie si le format de l'email est valide
     * Vérifie si le format du numéro de téléphone est valide
     * Retourne un tableau avec les données nettoyée.
     */
    public static function parseAndValidateModify(string $args, string $command): array
    {
        // On vérifie la p^résence de 4 paramètres séparés par des virgules
        $pattern = '/^([^,]+),\s*([^,]+),\s*([^,]+),\s*([^,]+)$/';

        if (!preg_match($pattern, trim($args), $matches)) {
            throw new Exception("Erreur : [modify] attend 4 paramètres (Id, Nom, Email, Tel) séparés par des virgules");
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