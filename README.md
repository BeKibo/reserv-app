# Système de Gestion de Réservation de Salles

Ce projet est une application Symfony pour la gestion de réservations de salles avec un système complet d'administration.

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
3. Rendez le script d'entrée exécutable (si ce n'est pas déjà fait):

```bash
chmod +x docker-entrypoint.sh
```

4. Lancez l'application avec Docker Compose:

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
