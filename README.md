# Système de Gestion de Réservation de Salles

Ce projet est une application Symfony pour la gestion de réservations de salles avec un système complet d'administration. L'application permet aux utilisateurs de réserver des salles et aux administrateurs de gérer les réservations, les salles, les équipements et les critères ergonomiques.

## Documentation

Pour une documentation détaillée sur la structure du code et le fonctionnement technique de l'application, veuillez consulter le fichier [DOCUMENTATION.md](DOCUMENTATION.md).

## Fonctionnalités

### Dashboard Administrateur
- Statistiques en temps réel (nombre de réservations, taux de confirmation, etc.)
- Notifications pour les réservations en attente
- Vue d'ensemble des réservations récentes
- Accès rapide à toutes les fonctionnalités

### Gestion des Réservations
- Création, consultation, modification et suppression de réservations
- Système de validation des pré-réservations
- Vérification automatique des disponibilités
- Code couleur pour différencier les réservations confirmées et en attente
- Notifications 5 jours avant les réservations non validées

### Gestion des Salles
- Informations détaillées (nom, lieu, capacité, description)
- Association avec des équipements
- Critères ergonomiques
- Vérification de disponibilité

### Gestion des Utilisateurs
- Système de rôles (administrateur, utilisateur standard)
- Authentification sécurisée
- Gestion des profils

### Interface Utilisateur
- Design responsive avec Bootstrap
- Navigation intuitive avec sidebar
- Système de notifications
- Filtres avancés pour toutes les listes

## Structure Technique

### Organisation du Code
- Controllers dans `src/Controller/Admin/`
- Services dédiés dans `src/Service/`
- Entités dans `src/Entity/`
- Formulaires Symfony dans `src/Form/`
- Templates Twig dans `templates/admin/`

### Sécurité
- Contrôle d'accès avec ROLE_ADMIN
- Validation complète des formulaires
- Protection CSRF
- Authentification sécurisée

## Comment lancer l'application

### Avec Docker (recommandé)

1. Assurez-vous d'avoir Docker et Docker Compose installés sur votre machine
2. Clonez ce dépôt

#### Option 1 (recommandée) - Script de démarrage unifié:

3. Rendez le script de démarrage exécutable (si ce n'est pas déjà fait):

```bash
chmod +x start
```

4. Lancez l'application avec le script de démarrage unifié:

```bash
./start
```

Ce script détectera automatiquement votre système d'exploitation et utilisera le script approprié.

#### Option 2 - Scripts spécifiques à la plateforme:

##### Pour les utilisateurs Linux/macOS:

3. Rendez les scripts exécutables (si ce n'est pas déjà fait):

```bash
chmod +x start.sh docker-entrypoint.sh
```

4. Lancez l'application avec le script de démarrage:

```bash
./start.sh
```

##### Pour les utilisateurs Windows:

3. Option 1 - Utiliser PowerShell:

```powershell
.\start.ps1
```

   Note: Un script PowerShell équivalent pour `docker-entrypoint.sh` est également disponible (`docker-entrypoint.ps1`). Ce script est utilisé à l'intérieur du conteneur Docker et ne nécessite généralement pas d'être exécuté manuellement.

3. Option 2 - Utiliser Git Bash:

```bash
./start.sh
```

3. Option 3 - Utiliser WSL (Windows Subsystem for Linux):

```bash
./start.sh
```

#### Pour tous les utilisateurs - Lancement manuel:

Vous pouvez également démarrer l'application manuellement avec Docker Compose:

```bash
docker compose up -d
```

5. L'application sera automatiquement initialisée avec:
   - Configuration de la base de données PostgreSQL
   - Exécution des migrations
   - Chargement des données de test (fixtures)

6. Accédez à l'application via votre navigateur:
   - Application: http://localhost:8080
   - Mailpit (pour les emails): http://localhost:8025

7. Pour voir les logs de l'application:

```bash
docker compose logs -f app
```

8. Pour arrêter l'application:

```bash
docker compose down
```

9. Note pour les utilisateurs de Mac M1/M2:
   - Si vous rencontrez des problèmes de compatibilité avec les images Docker, ajoutez l'option `--platform linux/amd64` lors du lancement des conteneurs:
   ```bash
   docker compose up -d --platform linux/amd64
   ```

### Résolution des problèmes courants

#### Problèmes avec Docker Compose

Si vous rencontrez des avertissements concernant l'attribut `version` obsolète dans docker-compose.yml, ne vous inquiétez pas. Cet attribut a été supprimé car il n'est plus nécessaire dans les versions récentes de Docker Compose.

Si vous rencontrez des erreurs indiquant que tous les conteneurs ne sont pas en cours d'exécution, les scripts de démarrage ont été améliorés pour vérifier chaque conteneur individuellement et fournir des messages d'erreur plus spécifiques. Vérifiez les logs du conteneur spécifique mentionné dans le message d'erreur :

```bash
# Pour voir les logs du conteneur app
docker compose logs app

# Pour voir les logs du conteneur database
docker compose logs database

# Pour voir les logs du conteneur mailer
docker compose logs mailer
```

#### Problèmes spécifiques à Windows

Si vous rencontrez des problèmes lors de l'exécution de l'application sur Windows, voici quelques solutions :

1. **Erreur d'autoload** : Si vous voyez une erreur comme `Failed to open stream: No such file or directory in /app/public/index.php` ou `Failed opening required '/app/vendor/autoload_runtime.php'`, essayez les solutions suivantes dans l'ordre :

   - Utilisez le script PowerShell fourni (`start.ps1`) au lieu du script shell si vous êtes sur Windows
   - Redémarrez Docker Desktop complètement
   - Exécutez `docker compose down -v` pour supprimer tous les volumes, puis redémarrez l'application
   - Si l'erreur persiste, vous pouvez forcer la réinstallation des dépendances Composer en exécutant :
     ```bash
     docker exec reserv-app composer install --no-interaction --no-progress
     ```
   - Exécutez un dump-autoload pour régénérer les fichiers d'autoload :
     ```bash
     docker exec reserv-app composer dump-autoload --optimize
     ```
   - Si le problème persiste toujours, essayez de générer explicitement le fichier autoload_runtime.php :
     ```bash
     docker exec reserv-app composer require symfony/runtime --no-interaction --no-progress
     ```
   - Vérifiez que le fichier existe dans le conteneur :
     ```bash
     docker exec reserv-app ls -la /app/vendor/autoload_runtime.php
     ```
   - Si le fichier n'existe pas, vérifiez les logs du conteneur pour plus d'informations :
     ```bash
     docker logs reserv-app
     ```
   - Vérifiez si le package symfony/runtime est correctement installé :
     ```bash
     docker exec reserv-app composer show symfony/runtime
     ```
   - Utilisez le script de copie du répertoire vendor fourni pour copier manuellement les dépendances dans le conteneur :
     ```bash
     # Script unifié (recommandé) - détecte automatiquement votre système d'exploitation
     chmod +x copy-vendor
     ./copy-vendor

     # Ou utilisez directement le script spécifique à votre plateforme
     # Sur Linux/macOS
     chmod +x copy-vendor.sh
     ./copy-vendor.sh

     # Sur Windows avec PowerShell
     .\copy-vendor.ps1
     ```

   - En dernier recours, essayez de reconstruire l'image Docker et de recréer les conteneurs :
     ```bash
     docker compose down -v
     docker compose build --no-cache
     docker compose up -d
     ```

2. **Problèmes de fins de ligne** : Si les scripts ne s'exécutent pas correctement, vérifiez que les fins de ligne sont au format Unix (LF) :

   - Configurez Git : `git config --global core.autocrlf input`
   - Utilisez un éditeur de texte qui peut convertir les fins de ligne (VS Code, Notepad++, etc.)

3. **Problèmes de montage de volumes** : Si les fichiers ne sont pas correctement montés dans le conteneur :

   - Assurez-vous que Docker Desktop est configuré pour partager le lecteur où se trouve votre projet
   - Utilisez des chemins absolus dans Docker Compose
   - Essayez d'utiliser WSL 2 comme backend pour Docker Desktop

4. **Problèmes de performance** : Si l'application est lente sur Windows :

   - Assurez-vous que WSL 2 est utilisé comme backend pour Docker Desktop
   - Limitez le nombre de fichiers montés en utilisant des volumes nommés pour les répertoires volumineux

#### Erreur de classe Security non trouvée

Si vous rencontrez l'erreur suivante:
```
ClassNotFoundError: Attempted to load class "Security" from namespace "Symfony\Component\Security\Core".
Did you forget a "use" statement for "Symfony\Bundle\SecurityBundle\Security"?
```

Cette erreur est due à un changement dans la structure des composants de sécurité de Symfony. Pour résoudre ce problème:

1. Ouvrez le fichier `src/Security/LoginFormAuthenticator.php`
2. Remplacez la ligne:
```php
use Symfony\Component\Security\Core\Security;
```
par:
```php
use Symfony\Bundle\SecurityBundle\Security;
```

3. Ajoutez la constante LAST_USERNAME dans la classe LoginFormAuthenticator:
```php
public const LAST_USERNAME = '_security.last_username';
```

4. Remplacez l'utilisation de Security::LAST_USERNAME par self::LAST_USERNAME:
```php
$request->getSession()->set(self::LAST_USERNAME, $email);
```

5. Redémarrez l'application:
```bash
docker compose down
docker compose up -d
```

#### Problème de connexion à la base de données

Si vous rencontrez des problèmes de connexion à la base de données, voici quelques étapes à suivre:

1. Vérifiez que tous les conteneurs sont en cours d'exécution:
```bash
docker ps
```

2. Si le conteneur de l'application n'est pas en cours d'exécution, vérifiez les logs:
```bash
docker logs reserv-app
```

3. Si vous voyez des messages "Database not ready yet", essayez de redémarrer les conteneurs:
```bash
docker compose down
docker compose up -d
```

4. Si le problème persiste, vérifiez que le conteneur de la base de données est accessible depuis le conteneur de l'application:
```bash
docker exec -it reserv-app apt-get update
docker exec -it reserv-app apt-get install -y iputils-ping
docker exec -it reserv-app ping -c 3 database
```

5. Vérifiez que les informations de connexion à la base de données sont correctes dans le fichier `.env.docker`:
```bash
docker exec -it reserv-app cat /var/www/html/.env.docker
```

6. Si nécessaire, modifiez le fichier `.env.docker` pour correspondre à votre configuration de base de données.

#### Erreur lors du chargement des fixtures

Si vous rencontrez l'erreur suivante lors du chargement des fixtures:
```
An exception occurred while executing a query: SQLSTATE[42P01]: Undefined table: 7 ERROR: relation "salle_equipement" does not exist
LINE 1: DELETE FROM salle_equipement
```

Cette erreur est due à un problème de correspondance entre les mappings des entités. Pour résoudre ce problème:

1. Ouvrez le fichier `src/Entity/Equipement.php`
2. Localisez la ligne:
```php
#[ORM\ManyToMany(targetEntity: Salle::class, mappedBy: 'equipements')]
```
3. Modifiez-la pour qu'elle corresponde au nom de la propriété dans l'entité Salle:
```php
#[ORM\ManyToMany(targetEntity: Salle::class, mappedBy: 'Equipement')]
```
4. Redémarrez l'application et réessayez de charger les fixtures:
```bash
docker compose down
docker compose up -d
docker exec -it reserv-app php bin/console doctrine:fixtures:load --no-interaction
```

### Installation manuelle

1. Prérequis:
   - PHP 8.1 ou supérieur
   - Composer
   - PostgreSQL 16 ou supérieur
   - Symfony CLI (optionnel)

2. Clonez ce dépôt

3. Installez les dépendances:
```bash
composer install
```

4. Configurez votre base de données dans le fichier `.env`:
```
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
```

5. Créez la base de données et exécutez les migrations:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

6. Chargez les fixtures (données de test):
```bash
php bin/console doctrine:fixtures:load
```

7. Lancez le serveur de développement:
```bash
symfony server:start
# ou
php -S localhost:8000 -t public/
```

8. Accédez à l'application via votre navigateur à l'adresse: http://localhost:8000

## Comptes utilisateurs par défaut

- Admin: admin@example.com / password
- Utilisateur: user@example.com / password
