# One Piece Only

One Piece Only est une plateforme de messagerie instantanée conçue pour la communauté des lecteurs et spectateurs de One Piece. L'application permet d'échanger en temps réel tout en garantissant une protection contre les révélations (spoils). Le système limite l'accès aux salons de discussion en fonction de la progression réelle de chaque utilisateur dans l'œuvre (chapitre du manga ou épisode de l'animé).

## Fonctionnalités principales

- Création de compte et authentification sécurisée.
- Gestion du profil utilisateur incluant le type de média (Manga ou Animé) et le niveau de progression.
- Création de salons de discussion thématiques.
- Système de filtrage dynamique : seuls les salons sécurisés par rapport à la progression de l'utilisateur sont accessibles.
- Messagerie instantanée en temps réel sans rechargement de page.

## Technologies utilisées

- Backend : Symfony 7.4 et PHP 8.2+.
- Base de données : PostgreSQL.
- Environnement : Docker et Docker Compose.
- Communication temps réel : Symfony Mercure Hub.
- Frontend : Moteur de templates Twig associé à Symfony UX (Stimulus et Turbo).

## Installation et configuration

Pour installer et lancer le projet dans un environnement de développement, suivez les étapes suivantes :

1. Cloner le projet :
   ```bash
   git clone <url-du-depot>
   cd one-piece-only
   ```

2. Installer les dépendances avec Composer :
   ```bash
   composer install
   ```

3. Lancement des services Docker (Base de données et Mercure) :
   ```bash
   docker compose up -d
   ```

4. Initialisation de la base de données :
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

5. Chargement des données de test :
   ```bash
   php bin/console doctrine:fixtures:load --no-interaction
   ```

6. Démarrage du serveur Symfony :
   ```bash
   symfony serve
   ```

## Organisation du code

Le projet suit l'architecture standard de Symfony pour faciliter sa compréhension :

- src/Entity : Définition des objets métiers (Utilisateurs, Salons, Messages).
- src/Controller : Gestion des routes et de la logique de présentation.
- src/Repository : Requêtes de base de données, notamment pour le calcul de la sécurité des salons.
- src/DataFixtures : Scripts pour alimenter la base de données lors du développement.
- templates : Fichiers HTML structurés avec le moteur de templates Twig.

## Validation & Tests

L'application contient des tests fonctionnels pour vérifier que tout fonctionne correctement, notamment le système anti-spoil.

### 1. Préparer la base de données de test

Les tests s'exécutent sur une base de données séparée (configurée dans `.env.test`) pour ne pas écraser vos données de développement. Avant de lancer les tests, exécutez ces commandes :

```bash
# Créer la base de test
php bin/console doctrine:database:create --env=test

# Lancer les migrations
php bin/console doctrine:migrations:migrate --env=test --no-interaction

# Charger les données de test (fixtures)
php bin/console doctrine:fixtures:load --env=test --no-interaction
```

### 2. Lancer les tests

Exécutez la commande suivante pour lancer tous les tests :

```bash
php bin/phpunit
```

*(Si vous utilisez WSL, lancez plutôt : `wsl php bin/phpunit`)*

### 3. Qu'est-ce qui est testé ? (`tests/Test.php`)

- **testHome** : Vérifie que la page d'accueil s'affiche bien (code HTTP 200).
- **testChannelBlocked** : Vérifie qu'un utilisateur qui n'a pas assez avancé (ex: chapitre 10) est bien bloqué (redirection) s'il tente d'accéder à un salon plus avancé (ex: chapitre 1100).
- **testChannelAllowed** : Vérifie qu'un utilisateur à jour (ex: chapitre 1200) peut bien accéder à un salon (ex: chapitre 1100).
- **testChannelAnon** : Vérifie qu'un utilisateur non connecté est automatiquement redirigé s'il essaie d'accéder à un salon.
- **testChannelNotFound** : S'assure qu'un salon qui n'existe pas (ID inexistant) renvoie bien une erreur 404.
- **testRegister** : Teste le formulaire d'inscription en créant un utilisateur d'essai de A à Z.

---
Documentation technique de l'application One Piece Only.
Dernière mise à jour : 21 juin 2026.
