````markdown
# Reserv-App

> Une application Symfony de gestion de réservations de salles avec équipements et critères ergonomiques.

## 🚀 Fonctionnalités

- Authentification (login, reset password)
- Gestion des utilisateurs (admin + utilisateur)
- Réservation de salles avec :
  - Date et créneau
  - Équipements associés
  - Critères ergonomiques
- Interface d'administration (via EasyAdmin)
- Filtrage intelligent (lieux, capacité, équipement, validation…)

## 📁 Structure du projet

- `src/Controller/` → Contrôleurs Symfony (Salle, Réservation, Login, etc.)
- `src/Entity/` → Entités Doctrine (User, Salle, Equipement…)
- `templates/` → Fichiers Twig (interface utilisateur)
- `public/` → Ressources front (JS, CSS, images…)

## 🛠️ Installation locale

```bash
git clone https://github.com/BeKibo/reserv-app.git
cd reserv-app
composer install
symfony server:start
````

> Assure-toi d’avoir PHP, Composer et Symfony CLI installés.

## 🧪 Données de test

Des fixtures sont incluses :

```bash
php bin/console doctrine:fixtures:load
```

## 📸 Aperçu

![Aperçu](./public/images/screenshot.png) <!-- à personnaliser si tu as un screenshot -->

---

## 📦 Stack technique

* PHP 8+, Symfony 6
* Doctrine ORM
* Twig
* EasyAdmin
* Bootstrap ou Tailwind (selon ce que tu utilises)

## ✨ Auteur

**BeKibo**
[GitHub Profile](https://github.com/BeKibo)
**Selligs**
[GitHub Profile](https://github.com/Selligsl)
