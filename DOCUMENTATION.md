# Documentation Technique - Système de Gestion de Réservation de Salles

Cette documentation technique détaille la structure et le fonctionnement du système de gestion de réservation de salles. Elle est destinée aux développeurs qui souhaitent comprendre, maintenir ou étendre l'application.

## Table des Matières

1. [Architecture Globale](#architecture-globale)
2. [Installation et Configuration](#installation-et-configuration)
3. [Structure du Code](#structure-du-code)
4. [Flux de l'Application](#flux-de-lapplication)
5. [Base de Données](#base-de-données)
6. [Validation des Données](#validation-des-données)
7. [Interface Utilisateur](#interface-utilisateur)
8. [Sécurité](#sécurité)
9. [Déploiement](#déploiement)
10. [Tests](#tests)
11. [Troubleshooting](#troubleshooting)
12. [Améliorations Possibles](#améliorations-possibles)

## Architecture Globale

L'application est construite avec le framework Symfony et suit une architecture MVC (Modèle-Vue-Contrôleur) :

- **Modèle** : Entités Doctrine qui représentent les données de l'application
- **Vue** : Templates Twig qui définissent l'interface utilisateur
- **Contrôleur** : Classes PHP qui gèrent les requêtes HTTP et coordonnent les interactions

## Installation et Configuration

### Prérequis
- PHP 8.1 ou supérieur
- Composer
- PostgreSQL 16 ou supérieur
- Docker et Docker Compose (pour l'installation avec Docker)

### Installation avec Docker (recommandée)

1. Clonez le dépôt Git :
```bash
git clone https://github.com/votre-repo/reserv-app.git
cd reserv-app
```

2. Rendez le script d'entrée exécutable :
```bash
chmod +x docker-entrypoint.sh
```

3. Lancez l'application avec le script de démarrage :
```bash
./start.sh
```

4. L'application sera disponible à l'adresse : http://localhost:8080
   - Interface d'administration : http://localhost:8080/admin
   - Mailpit (pour les emails) : http://localhost:8025

### Installation manuelle

1. Clonez le dépôt Git :
```bash
git clone https://github.com/votre-repo/reserv-app.git
cd reserv-app
```

2. Installez les dépendances :
```bash
composer install
```

3. Configurez votre base de données dans le fichier `.env` :
```
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
```

4. Créez la base de données et exécutez les migrations :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. Chargez les fixtures (données de test) :
```bash
php bin/console doctrine:fixtures:load
```

6. Lancez le serveur de développement :
```bash
symfony server:start
# ou
php -S localhost:8000 -t public/
```

7. Accédez à l'application via votre navigateur à l'adresse : http://localhost:8000

### Configuration

Les principaux fichiers de configuration se trouvent dans le répertoire `config/` :
- `config/packages/security.yaml` : Configuration de la sécurité
- `config/routes.yaml` : Configuration des routes
- `config/services.yaml` : Configuration des services

## Structure du Code

### Entités (Modèles de Données)

L'application utilise les entités suivantes pour représenter les données :

#### Salle (`src/Entity/Salle.php`)
- Représente une salle qui peut être réservée
- Propriétés principales :
  - `id` : Identifiant unique
  - `nom` : Nom de la salle
  - `lieu` : Emplacement de la salle
  - `capacite` : Nombre maximum de personnes
  - `image` : Chemin vers l'image de la salle
  - `description` : Description détaillée
- Relations :
  - ManyToMany avec `Equipement` : Une salle peut avoir plusieurs équipements
  - ManyToMany avec `CritErgo` : Une salle peut avoir plusieurs critères ergonomiques
  - OneToMany avec `Reservation` : Une salle peut avoir plusieurs réservations
- Méthodes notables :
  - `isReservedBetween()` : Vérifie si la salle est déjà réservée pour une période donnée

#### Reservation (`src/Entity/Reservation.php`)
- Représente une réservation de salle
- Propriétés principales :
  - `id` : Identifiant unique
  - `dateDebut` : Date et heure de début
  - `dateFin` : Date et heure de fin
  - `validation` : État de validation (confirmée ou en attente)
- Relations :
  - ManyToOne avec `Salle` : Une réservation concerne une seule salle
  - ManyToOne avec `User` : Une réservation est faite par un seul utilisateur

#### User (`src/Entity/User.php`)
- Représente un utilisateur du système
- Propriétés principales :
  - `id` : Identifiant unique
  - `email` : Adresse email (utilisée pour l'authentification)
  - `roles` : Rôles de l'utilisateur (ROLE_USER, ROLE_ADMIN)
  - `password` : Mot de passe hashé
  - `nom` : Nom de l'utilisateur
- Relations :
  - OneToMany avec `Reservation` : Un utilisateur peut avoir plusieurs réservations
- Implémente les interfaces `UserInterface` et `PasswordAuthenticatedUserInterface` pour l'intégration avec le système de sécurité de Symfony

#### Equipement (`src/Entity/Equipement.php`)
- Représente un équipement qui peut être associé à une salle
- Propriétés principales :
  - `id` : Identifiant unique
  - `nom` : Nom de l'équipement
  - `categorie` : Catégorie de l'équipement
- Relations :
  - ManyToMany avec `Salle` : Un équipement peut être associé à plusieurs salles

#### CritErgo (`src/Entity/CritErgo.php`)
- Représente un critère ergonomique qui peut être associé à une salle
- Propriétés principales :
  - `id` : Identifiant unique
  - `nom` : Nom du critère
  - `categorie` : Catégorie du critère
- Relations :
  - ManyToMany avec `Salle` : Un critère peut être associé à plusieurs salles

### Contrôleurs

L'application est divisée en deux parties principales, chacune avec ses propres contrôleurs :

#### Partie Publique
- `DefaultController` (`src/Controller/DefaultController.php`) : Gère la page d'accueil et redirige vers le tableau de bord admin ou la page de connexion
- `SecurityController` (`src/Controller/SecurityController.php`) : Gère l'authentification (connexion/déconnexion)

#### Partie Administration
Tous les contrôleurs admin héritent de `AdminController` qui :
- Vérifie que l'utilisateur a le rôle ROLE_ADMIN
- Fournit des fonctionnalités communes comme les notifications

Les contrôleurs admin spécifiques sont :
- `DashboardController` : Affiche le tableau de bord avec statistiques et réservations récentes
- `ReservationController` : Gère les opérations CRUD sur les réservations
- `SalleController` : Gère les opérations CRUD sur les salles
- `EquipementController` : Gère les opérations CRUD sur les équipements
- `CritErgoController` : Gère les opérations CRUD sur les critères ergonomiques
- `UserController` : Gère les opérations CRUD sur les utilisateurs

### Services

L'application utilise plusieurs services pour encapsuler la logique métier. Ces services suivent le principe de responsabilité unique et sont configurés dans `config/services.yaml`.

#### NotificationService (`src/Service/NotificationService.php`)

Ce service gère les notifications pour les administrateurs.

**Fonctionnalités principales :**
- Récupération des notifications pour le tableau de bord admin
- Calcul du nombre de réservations en attente
- Génération de messages d'alerte pour les réservations qui approchent de leur date limite

**Méthodes principales :**
```php
// Récupère toutes les notifications pour l'administrateur
public function getAdminNotifications(): array

// Récupère le nombre de réservations en attente de validation
public function getPendingReservationsCount(): int

// Vérifie les réservations qui approchent de leur date limite
public function checkPendingReservations(): array
```

**Exemple d'utilisation :**
```php
// Dans un contrôleur
public function dashboard(NotificationService $notificationService)
{
    $notifications = $notificationService->getAdminNotifications();
    $pendingCount = $notificationService->getPendingReservationsCount();

    // ...
}
```

#### ReservationService (`src/Service/ReservationService.php`)

Ce service gère la logique métier liée aux réservations.

**Fonctionnalités principales :**
- Calcul de statistiques sur les réservations
- Vérification de la disponibilité des salles
- Gestion du processus de validation des réservations

**Méthodes principales :**
```php
// Récupère des statistiques sur les réservations
public function getStatistics(): array

// Vérifie si une salle est disponible pour une période donnée
public function checkRoomAvailability(Salle $salle, \DateTimeImmutable $start, \DateTimeImmutable $end): bool

// Valide une réservation
public function validateReservation(Reservation $reservation): void
```

**Exemple d'utilisation :**
```php
// Dans un contrôleur
public function validateReservation(
    Request $request, 
    ReservationService $reservationService, 
    Reservation $reservation
): Response
{
    $reservationService->validateReservation($reservation);
    // ...
}
```


## Flux de l'Application

### Flux Utilisateur
1. L'utilisateur accède à l'application et est redirigé vers la page de connexion
2. Après connexion, selon son rôle :
   - Les administrateurs sont redirigés vers le tableau de bord admin
   - Les utilisateurs standard sont redirigés vers leur interface (à implémenter)

### Flux Administrateur
1. L'administrateur accède au tableau de bord qui affiche :
   - Des statistiques sur les réservations, salles et utilisateurs
   - Les réservations récentes
   - Les notifications pour les réservations en attente
2. L'administrateur peut gérer :
   - Les réservations (confirmer, modifier, supprimer)
   - Les salles (ajouter, modifier, supprimer)
   - Les équipements (ajouter, modifier, supprimer)
   - Les critères ergonomiques (ajouter, modifier, supprimer)
   - Les utilisateurs (ajouter, modifier, supprimer)

## Base de Données

### Schéma de la Base de Données

L'application utilise PostgreSQL comme système de gestion de base de données. Voici le schéma simplifié des relations entre les entités :

```
+-------------+       +---------------+       +-------------+
|    User     |       | Reservation   |       |   Salle     |
+-------------+       +---------------+       +-------------+
| id          |<----->| id            |<----->| id          |
| email       |       | dateDebut     |       | nom         |
| password    |       | dateFin       |       | lieu        |
| nom         |       | validation    |       | capacite    |
| roles       |       | user_id (FK)  |       | image       |
+-------------+       | salle_id (FK) |       | description |
                      +---------------+       +-------------+
                                              |             |
                                              v             v
                      +---------------+       +-------------+
                      |  Equipement   |<----->|  CritErgo   |
                      +---------------+       +-------------+
                      | id            |       | id          |
                      | nom           |       | nom         |
                      | categorie     |       | categorie   |
                      +---------------+       +-------------+
```

### Tables de Jointure

Pour les relations many-to-many, les tables de jointure suivantes sont utilisées :

- `salle_equipement` : Relie les salles et les équipements
- `salle_crit_ergo` : Relie les salles et les critères ergonomiques

### Migrations

Les migrations de base de données sont gérées par Doctrine Migrations et se trouvent dans le répertoire `migrations/`. Pour appliquer les migrations :

```bash
php bin/console doctrine:migrations:migrate
```

Pour créer une nouvelle migration après modification des entités :

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## Sécurité

La sécurité est un aspect fondamental de l'application et est gérée par le composant Security de Symfony.

### Authentification

- L'authentification est gérée via un formulaire de connexion (`src/Security/LoginFormAuthenticator.php`)
- Les identifiants par défaut sont :
  - Admin: admin@example.com / password
  - Utilisateur: user@example.com / password
- Le processus d'authentification utilise le composant Security de Symfony pour vérifier les identifiants et gérer les sessions

### Autorisation

- Les rôles utilisés dans l'application sont :
  - `ROLE_USER` : Attribué à tous les utilisateurs authentifiés
  - `ROLE_ADMIN` : Attribué aux administrateurs
- Toutes les routes admin sont protégées par l'attribut `#[IsGranted('ROLE_ADMIN')]` dans la classe `AdminController`
- Les contrôles d'accès sont également appliqués dans les templates Twig avec `is_granted()`

### Protection CSRF

- Tous les formulaires incluent une protection CSRF (Cross-Site Request Forgery)
- Les tokens CSRF sont générés automatiquement par Symfony et validés lors de la soumission des formulaires

### Bonnes Pratiques

- Les mots de passe sont hashés avec l'algorithme bcrypt
- Les sessions sont gérées de manière sécurisée par Symfony
- Les entrées utilisateur sont validées et échappées pour prévenir les injections SQL et XSS

## Validation des Données

L'application utilise le composant Validator de Symfony pour valider les données :
- Contraintes de longueur pour les champs texte
- Expressions régulières pour valider les formats
- Contraintes de type pour les champs numériques

## Interface Utilisateur

L'interface utilisateur est construite avec :
- Bootstrap pour le design responsive
- Twig comme moteur de templates
- Une sidebar pour la navigation dans l'interface admin
- Un système de notifications pour alerter les administrateurs

## Tests

L'application utilise PHPUnit pour les tests automatisés. Les tests sont organisés dans le répertoire `tests/`.

### Exécution des Tests

Pour exécuter tous les tests :

```bash
php bin/phpunit
```

Pour exécuter un test spécifique :

```bash
php bin/phpunit tests/chemin/vers/TestSpecifique.php
```

### Types de Tests

#### Tests Unitaires
Les tests unitaires se concentrent sur des composants individuels comme les services et les entités.

#### Tests Fonctionnels
Les tests fonctionnels vérifient le comportement de l'application dans son ensemble, en simulant des requêtes HTTP et en vérifiant les réponses.

### Bonnes Pratiques pour les Tests

- Écrire des tests pour toutes les nouvelles fonctionnalités
- Maintenir une couverture de code élevée
- Utiliser des fixtures de test pour préparer les données
- Isoler les tests pour éviter les dépendances entre eux

## Troubleshooting

Cette section aborde les problèmes courants que vous pourriez rencontrer lors du développement ou du déploiement de l'application.

### Problèmes de Connexion à la Base de Données

Si vous rencontrez des problèmes de connexion à la base de données :

1. Vérifiez que PostgreSQL est en cours d'exécution
2. Vérifiez les informations de connexion dans le fichier `.env`
3. Assurez-vous que la base de données existe
4. Vérifiez les logs pour plus d'informations :
   ```bash
   docker compose logs database
   ```

### Erreurs Courantes

#### Erreur de classe Security non trouvée

Si vous rencontrez l'erreur suivante :
```
ClassNotFoundError: Attempted to load class "Security" from namespace "Symfony\Component\Security\Core".
Did you forget a "use" statement for "Symfony\Bundle\SecurityBundle\Security"?
```

Solution :
1. Ouvrez le fichier `src/Security/LoginFormAuthenticator.php`
2. Remplacez la ligne :
```php
use Symfony\Component\Security\Core\Security;
```
par :
```php
use Symfony\Bundle\SecurityBundle\Security;
```

#### Erreur lors du chargement des fixtures

Si vous rencontrez une erreur concernant la relation "salle_equipement" :

Solution :
1. Vérifiez que les noms des propriétés dans les entités correspondent aux noms utilisés dans les annotations ORM
2. Assurez-vous que les migrations ont été correctement appliquées

## Déploiement

L'application peut être déployée de deux façons :
- Avec Docker (recommandé) : Utilise docker-compose pour créer un environnement complet
- Installation manuelle : Nécessite PHP, Composer et PostgreSQL

## Exemples de Code pour les Opérations Courantes

Cette section fournit des exemples de code pour les opérations courantes dans l'application.

### Créer une Nouvelle Réservation

```php
// Dans un contrôleur
public function createReservation(Request $request, EntityManagerInterface $entityManager): Response
{
    $reservation = new Reservation();
    $reservation->setValidation(false); // Par défaut, les réservations sont en attente

    $form = $this->createForm(ReservationType::class, $reservation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Vérifier la disponibilité de la salle
        $salle = $reservation->getSalles();
        $dateDebut = $reservation->getDateDebut();
        $dateFin = $reservation->getDateFin();

        if ($salle->isReservedBetween($dateDebut, $dateFin)) {
            $this->addFlash('error', 'La salle est déjà réservée pour cette période.');
            return $this->redirectToRoute('reservation_new');
        }

        // Associer l'utilisateur courant
        $reservation->setUsers($this->getUser());

        $entityManager->persist($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Votre demande de réservation a été enregistrée.');
        return $this->redirectToRoute('reservation_show', ['id' => $reservation->getId()]);
    }

    return $this->render('reservation/new.html.twig', [
        'form' => $form->createView(),
    ]);
}
```

### Vérifier la Disponibilité d'une Salle

```php
// Dans l'entité Salle
public function isReservedBetween(\DateTimeImmutable $start, \DateTimeImmutable $end): bool
{
    foreach ($this->reservation as $res) {
        if ($res->isValidation() && $start < $res->getDateFin() && $end > $res->getDateDebut()) {
            return true;
        }
    }
    return false;
}

// Utilisation dans un contrôleur
public function checkAvailability(Request $request, SalleRepository $salleRepository): Response
{
    $salleId = $request->query->get('salle_id');
    $dateDebut = new \DateTimeImmutable($request->query->get('date_debut'));
    $dateFin = new \DateTimeImmutable($request->query->get('date_fin'));

    $salle = $salleRepository->find($salleId);

    if (!$salle) {
        return $this->json(['available' => false, 'error' => 'Salle non trouvée']);
    }

    $available = !$salle->isReservedBetween($dateDebut, $dateFin);

    return $this->json(['available' => $available]);
}
```

### Valider une Réservation (Admin)

```php
// Dans un contrôleur admin
public function validateReservation(
    Reservation $reservation, 
    EntityManagerInterface $entityManager,
    NotificationService $notificationService
): Response
{
    $reservation->setValidation(true);
    $entityManager->flush();

    // Envoyer une notification à l'utilisateur (à implémenter)
    // $notificationService->sendReservationConfirmation($reservation);

    $this->addFlash('success', 'La réservation a été validée avec succès.');

    return $this->redirectToRoute('admin_reservation_list');
}
```

### Récupérer les Statistiques des Réservations

```php
// Dans le ReservationService
public function getStatistics(): array
{
    $totalCount = $this->reservationRepository->count([]);
    $pendingCount = $this->reservationRepository->count(['validation' => false]);
    $confirmedCount = $this->reservationRepository->count(['validation' => true]);

    $confirmationRate = $totalCount > 0 
        ? round(($confirmedCount / $totalCount) * 100) 
        : 0;

    return [
        'total' => $totalCount,
        'pending' => $pendingCount,
        'confirmed' => $confirmedCount,
        'confirmation_rate' => $confirmationRate,
    ];
}

// Utilisation dans un contrôleur
public function dashboard(ReservationService $reservationService): Response
{
    $stats = $reservationService->getStatistics();

    return $this->render('admin/dashboard.html.twig', [
        'stats' => $stats,
    ]);
}
```

## Améliorations Possibles

Voici quelques pistes d'amélioration pour l'application :
1. Développer une interface utilisateur complète pour les utilisateurs standard
2. Ajouter un système de réservation récurrente
3. Implémenter des notifications par email
4. Ajouter un calendrier visuel pour voir les disponibilités
5. Intégrer un système de commentaires/évaluations pour les salles
6. Mettre en place un système de réservation en temps réel avec WebSockets
7. Ajouter une fonctionnalité d'export des réservations (PDF, iCal)
8. Implémenter une API REST pour permettre l'intégration avec d'autres systèmes
