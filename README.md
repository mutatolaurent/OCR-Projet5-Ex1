Projet Carnet (CLI)

Résumé

- Petit projet CLI pour gérer un carnet de contacts (CRUD) en MySQL via PDO.
- Code organisé dans le namespace Src (dossier `Src/`).

Structure (fichiers PHP principaux)

- `main.php` : point d'entrée CLI. Autoloader, boucle de lecture de commandes et dispatch.
- `Src/DBconnect.php` : crée et fournit la connexion PDO (`getPDO()`).
- `Src/Contact.php` : modèle `Contact` (propriétés privées, getters/setters, `__toString()`).
- `Src/ContactManager.php` : accès aux données (DAO). Méthodes : `findAll`, `findById`, `insertNew`, `modifyById`, `deleteById`.
- `Src/Command.php` : contrôleur CLI qui orchestre les actions utilisateur en appelant `ContactManager`.
- `Src/InputCheck.php` : utilitaires statiques pour parser/valider les commandes et arguments (email, téléphone, id, etc.).

Autres éléments

- `db/initDB.sql`, `db/populateDB.sql` : scripts SQL pour initialiser et remplir la base `carnet`.

Flux principal

1. `main.php` lit la commande (ex. `list`, `create`, `modify`, `detail`, `delete`).
2. `InputCheck` parse et valide les arguments.
3. `Command` exécute l'action correspondante en appelant `ContactManager`.
4. `ContactManager` utilise `DBconnect::getPDO()` pour exécuter les requêtes via `\PDO`.
5. `Contact` représente les enregistrements manipulés.

Notes d'implémentation

- Tous les fichiers PHP sources sont dans le namespace `Src`; les références à la classe PDO utilisent le namespace global (préfixe `\PDO`).
- Le projet attend une base MySQL `carnet` (configurable dans `DBconnect.php`).
- Créer, à la racine, un fichier configDB.php avec la definition des constantes DB_NAME, DB_HOST, DB_PORT, DB_USER et DB_CHARSET nécessaire à la classe DBconnect pour se connecter à la DB. Exemple :

```text
<?php
define('DB_NAME', 'databasename');
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USER', 'username');
define('DB_PASSWORD', 'pwd');
define('DB_CHARSET', 'utf8mb4');
```

Comment lancer (local)

- Assurez-vous d'avoir `php` CLI et MySQL.
- Importez `db/initDB.sql` puis `db/populateDB.sql` si nécessaire.
- Exécutez :

```powershell
php main.php
```

Exemples de commandes

- Lister tous les contacts :

```text
list
```

- Créer un contact (Nom, Email, Téléphone) :

```text
create Dupont, dupont@example.com, 0612345678
```

- Voir le détail d'un contact :

```text
detail 3
```

- Modifier un contact (Id, Nom, Email, Téléphone) :

```text
modify 3, Dupont Jean, jean.dupont@example.com, 06-12-34-56-78
```

- Supprimer un contact :

```text
delete 3
```

- Aide et sortie :

```text
help
quit
```
