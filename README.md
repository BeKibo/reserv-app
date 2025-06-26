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
