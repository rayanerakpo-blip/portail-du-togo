# 🇹🇬 Portail du Togo

Portail des services publics togolais, inspiré de `france.gouv.fr` : un **guichet unique** qui recueille les démarches administratives, les ministères, les actualités officielles et les numéros d'urgence du Togo.

Réalisé en **PHP 8 + MySQL**, sans framework (léger et portable sous Ampps / XAMPP / WAMP).

---

## ✨ Fonctionnalités

### Site public
- **Accueil** : héros avec recherche intégrée, accès rapide, catégories de démarches, démarches populaires, actualités à la une, ministères, numéros d'urgence, statistiques.
- **Démarches & services** : 17 démarches, filtres par catégorie, recherche par mot-clé.
- **Fiche démarche** : présentation, documents requis, étapes, coût, délai, lieu, lien officiel, démarches similaires, boutons **Imprimer** et **Partager**.
- **Ministères** : 11 institutions avec fiche détaillée et coordonnées.
- **Actualités** : communiqués filtrables par rubrique + page de lecture complète (temps de lecture, partage).
- **Le Togo** : portrait du pays (chiffres clés, régions, économie, symboles nationaux).
- **Urgences** : répertoire classé par catégorie (police, pompiers, SAMU, etc.) avec liens téléphoniques.
- **Recherche globale** : démarches, ministères et actualités, avec **suggestions instantanées** (autocomplétion) dans l'en-tête et le héros.
- **Contact** : formulaire enregistré en base, validation + anti-spam (pot de miel et limitation de fréquence).
- **Page 404** personnalisée, **sitemap XML** dynamique et `robots.txt`.
- Identité visuelle du Togo : drapeau et armoiries en SVG, palette vert / jaune / rouge, devise « Travail · Liberté · Patrie ».

### Back-office (`/admin`)
- Connexion sécurisée (mots de passe hachés avec `password_hash`, protection CSRF, régénération de session).
- Tableau de bord avec statistiques.
- CRUD complet : **démarches**, **ministères**, **actualités**, **numéros d'urgence**.
- Boîte de réception des **messages** du formulaire de contact, avec **export CSV**.
- **Mon compte** : modification du profil et **changement de mot de passe** en toute sécurité.
- Publication / dépublication et ordre d'affichage.
- **Journal d'audit** des actions d'administration (`logs/admin.log`, non exposé).

### SEO, accessibilité & performance
- Balises **Open Graph** et **Twitter Card**, URL **canonique**, données structurées **JSON-LD** (`GovernmentOrganization`, `GovernmentService`, `NewsArticle`, `Country`, `WebSite` + `SearchAction`).
- Accessibilité : lien d'évitement, rôles ARIA, navigation clavier dans la recherche, états `:focus-visible`, respect de `prefers-reduced-motion`.
- **Feuille d'impression** dédiée, compression et mise en cache des ressources via `.htaccess`.

---

## 🚀 Installation

### 1. Placer les fichiers
Copiez le dossier `TOGO` dans la racine web de votre serveur, par exemple sous Ampps :

```
C:\Ampps\www\TOGO
```

> L'URL de base est **auto-détectée**. Le site fonctionne donc depuis
> `http://localhost/TOGO/`, `http://localhost/www/TOGO/` ou toute autre racine.
> Pour la forcer, décommentez `define('BASE_URL', '/TOGO');` dans `config/config.php`.

### 2. Créer la base de données
Importez `database/togo_portal.sql` :
- **Via phpMyAdmin** : http://localhost/phpmyadmin → *Importer* → choisir le fichier.
- **Via la ligne de commande** :
  ```bash
  mysql -u root -p < database/togo_portal.sql
  ```

Le script crée la base `togo_portal` et insère les données de démonstration.

### 3. Configurer la connexion
Éditez `config/database.php` :

```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'togo_portal');
define('DB_USER', 'root');
define('DB_PASS', 'mysql');   // ← Ampps par défaut : "mysql" ; XAMPP/WAMP : souvent vide
```

### 4. Ouvrir le site
- Site public : **http://localhost/TOGO/**
- Administration : **http://localhost/TOGO/admin/**

---

## 🔑 Accès administrateur

| Identifiant | Mot de passe      |
|-------------|-------------------|
| `admin`     | `Admin@Togo2026`  |

> ⚠️ **Changez ce mot de passe** dès la première connexion depuis la page
> **Administration → Mon compte** (profil et mot de passe). La régénération manuelle
> reste possible :
> ```bash
> php -r "echo password_hash('VotreNouveauMotDePasse', PASSWORD_DEFAULT);"
> ```
> puis remplacez `password_hash` dans la table `admins`.

---

## 📁 Arborescence

```
TOGO/
├── index.php                 Accueil
├── 404.php                   Page « introuvable » personnalisée
├── sitemap.php               Sitemap XML dynamique
├── robots.txt                Directives pour les moteurs de recherche
├── .htaccess                 Sécurité, cache, compression, ErrorDocument 404
├── api/
│   └── recherche.php         Suggestions JSON pour la recherche instantanée
├── config/
│   ├── config.php            Paramètres (nom du site, BASE_URL, session)
│   ├── database.php          Connexion PDO (identifiants MySQL)
│   └── .htaccess             Accès interdit
├── includes/
│   ├── functions.php         Helpers (e(), url(), slugify(), icônes SVG, SEO…)
│   ├── header.php            En-tête SEO + drapeau/armoiries SVG
│   ├── footer.php            Pied de page
│   └── .htaccess
├── pages/
│   ├── demarches.php         Liste + filtres
│   ├── demarche.php          Fiche démarche
│   ├── ministeres.php / ministere.php
│   ├── actualites.php / actualite.php
│   ├── a-propos.php          « Le Togo en bref »
│   ├── urgences.php          Répertoire des urgences
│   ├── recherche.php         Recherche globale
│   └── contact.php           Formulaire de contact
├── admin/
│   ├── login.php / logout.php
│   ├── index.php             Tableau de bord
│   ├── demarches.php         CRUD démarches
│   ├── ministeres.php        CRUD ministères
│   ├── actualites.php        CRUD actualités
│   ├── urgences.php          CRUD urgences
│   ├── messages.php          Messages reçus (+ export CSV)
│   ├── compte.php            Profil & changement de mot de passe
│   └── includes/auth.php     Authentification + layout admin
├── assets/
│   ├── css/style.css         Styles (thème Togo, responsive, impression)
│   ├── js/main.js            Menu mobile, recherche instantanée, partage, retour haut
│   └── img/
│       └── armoiries-togo.svg  Armoiries officielles (lions, drapeaux, devise)
├── logs/                     Journal d'audit (accès interdit) + .htaccess
└── database/
    ├── togo_portal.sql       Schéma + données
    └── .htaccess
```

---

## 🗃️ Base de données

| Table       | Rôle                                            |
|-------------|-------------------------------------------------|
| `admins`    | Comptes du back-office                          |
| `categories`| Thèmes de démarches (état civil, identité…)     |
| `demarches` | Démarches et services                           |
| `ministeres`| Ministères et institutions                      |
| `actualites`| Communiqués et actualités                       |
| `urgences`  | Numéros utiles et d'urgence                     |
| `messages`  | Messages du formulaire de contact               |

---

## 🎨 Personnalisation

- **Couleurs / polices** : variables CSS en haut de `assets/css/style.css`.
- **Coordonnées et e-mail** : `ADMIN_EMAIL` dans `config/config.php`.
- **Contenu** : soit via le back-office, soit en modifiant directement `database/togo_portal.sql`.

### Symboles nationaux
- **Armoiries** : `assets/img/armoiries-togo.svg` — deux lions rouges tenant chacun un arc et une flèche, bouclier doré « RT », drapeaux togolais croisés et devise « Travail · Liberté · Patrie ». Affichées via `togo_emblem()` dans le **logo principal**, la barre supérieure, la connexion/admin et le pied de page. Changez la taille avec un paramètre : `togo_emblem(60)`.
- **Drapeau** : `togo_flag()` (barre supérieure, pied de page, héros de l'accueil).
- Source du SVG : Wikimedia Commons, *Armoiries du Togo* (symbole officiel de l'État, domaine public).

### Réglages utiles
- **Page 404** : le chemin de `ErrorDocument 404` dans `.htaccess` (par défaut `/www/TOGO/404.php`) doit être adapté si le dossier d'installation change.
- **Sitemap** : accessible sur `…/TOGO/sitemap.php` ; pensez à mettre à jour l'URL dans `robots.txt`.
- **Suggestions de recherche** : l'endpoint `api/recherche.php` interroge la base en direct ; ajustez le nombre de résultats dans `search_suggestions()` (`includes/functions.php`).

---

## ⚠️ Avertissement

Ce portail est un **projet de démonstration** à usage d'illustration. Les contenus (démarches, ministères, numéros, actualités) sont fournis à titre d'exemple et **ne constituent pas des informations officielles** de l'État togolais. Pour toute démarche réelle, référez-vous aux sources officielles.

— *Travail · Liberté · Patrie* 🇹🇬
