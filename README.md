# Gestion Événements

Appli web de gestion d'événements avec budget, personnel et prestataires.

## Installation

### Prérequis

- PHP 7.4+
- PostgreSQL
- Serveur web ou `php -S`

### Setup

1. Clone
```bash
git clone https://github.com/Zegthor91/challengeWeb_eventManagement.git
cd challengeWeb_eventManagement
```

2. BDD

Ouvrir pgAdmin ou utiliser PowerShell:
```powershell
# Créer la BDD
& "C:\Program Files\PostgreSQL\15\bin\psql.exe" -U postgres -c "CREATE DATABASE gestion_events;"

# Importer le schéma
& "C:\Program Files\PostgreSQL\15\bin\psql.exe" -U postgres -d gestion_events -f database.sql
```

Ou via pgAdmin: clic droit sur Databases > Create > Database > `gestion_events`, puis Query Tool et coller le contenu de `database.sql`

3. Config

Éditer `backend/config/database.php` :
```php
$host = 'localhost';
$dbname = 'gestion_events';
$username = 'postgres';
$password = 'ton_mdp';
```

4. Lancer
```bash
php -S localhost:8000
```

5. Go

`http://localhost:8000/backend/views/login.php`

Login : `admin` / `admin123`

## Fonctionnalités

- Dashboard temps réel
- CRUD événements + duplication
- Budgets par event
- Planning personnel
- Annuaire prestataires
- Todo list tâches
- Carte interactive (Leaflet)
- Notifs auto
- Recherche/filtres

## Stack

- Backend : PHP + PostgreSQL
- Frontend : Vue.js 3, Leaflet
- CSS custom
- API REST JSON

## Structure

```
backend/
  views/        Pages
  config/       Config
  controllers/  Controllers
  models/       Models
public/
  css/         Styles
  js/          Scripts
```

## Sécurité

- Passwords hashés bcrypt
- Sessions sécurisées
- Validation serveur

Questions ? Ouvre une issue.
