# Configuration des Variables d'Environnement

## 🔐 Sécurité des Secrets

Les fichiers `.env.dev`, `.env.test`, et `.env.prod` contiennent des informations sensibles et **NE DOIVENT JAMAIS** être commités dans Git.

## 🚀 Configuration Initiale

### Pour un Nouveau Développeur

1. Copier le template :
   ```bash
   cp .env.dev.dist .env.dev
   ```

2. Générer un nouveau `APP_SECRET` :
   ```bash
   openssl rand -hex 32
   ```

3. Remplacer `APP_SECRET` dans `.env.dev` avec la valeur générée

4. Configurer les autres variables selon votre environnement local

## 📁 Structure des Fichiers

```
.env              # Valeurs par défaut (COMMIT ✅)
.env.dev          # Dev local - secrets réels (IGNORE ❌)
.env.dev.dist     # Template dev (COMMIT ✅)
.env.test         # Test - secrets réels (IGNORE ❌)
.env.prod         # Production - secrets réels (IGNORE ❌)
.env.local        # Override local (IGNORE ❌)
```

## 🔑 Variables Obligatoires

### APP_SECRET
Secret utilisé pour le chiffrement Symfony. **DOIT** être unique par environnement.

```bash
# Génération
openssl rand -hex 32
```

### DATABASE_URL
URL de connexion à la base de données.

```bash
# PostgreSQL (recommandé)
DATABASE_URL="postgresql://user:password@host:5432/dbname?serverVersion=16&charset=utf8"

# MySQL (alternative)
DATABASE_URL="mysql://user:password@host:3306/dbname?serverVersion=8.0"
```

### MAILER_DSN
Configuration du serveur mail.

```bash
# Développement (Mailpit)
MAILER_DSN=smtp://mailpit:1025

# Production (SMTP)
MAILER_DSN=smtp://user:password@smtp.example.com:587

# Production (Gmail)
MAILER_DSN=gmail+smtp://username:password@default
```

## 🏭 Production avec Symfony Secrets

En production, utiliser le système de secrets de Symfony :

```bash
# Générer les clés de chiffrement
php bin/console secrets:generate-keys

# Définir un secret
php bin/console secrets:set APP_SECRET

# Lister les secrets
php bin/console secrets:list --reveal

# Les secrets sont stockés dans config/secrets/prod/
```

### Fichiers Générés

- `config/secrets/prod/prod.encrypt.public.php` → À commiter ✅
- `config/secrets/prod/prod.decrypt.private.php` → À ignorer ❌ (déjà dans .gitignore)

## ⚠️ Règles de Sécurité

### ✅ À FAIRE

- Utiliser des secrets différents par environnement
- Générer des secrets aléatoires forts (32+ caractères)
- Utiliser Symfony Secrets en production
- Documenter les variables requises dans `.env.dev.dist`
- Commiter les templates `.dist`

### ❌ À NE JAMAIS FAIRE

- Commiter `.env.dev`, `.env.test`, `.env.prod`
- Réutiliser le même `APP_SECRET` sur plusieurs environnements
- Partager des secrets via email, Slack, etc.
- Commiter `config/secrets/prod/prod.decrypt.private.php`
- Utiliser des secrets faibles ou prévisibles

## 🔄 Rotation des Secrets

Si un secret est compromis :

1. Générer un nouveau secret
2. Mettre à jour tous les environnements
3. Nettoyer l'historique Git si nécessaire
4. Invalider les sessions actives si applicable

## 🐳 Docker Compose

Les variables d'environnement sont automatiquement chargées depuis `.env.dev` dans les conteneurs Docker.

```yaml
# compose.yaml
services:
  php:
    environment:
      APP_SECRET: ${APP_SECRET}
      DATABASE_URL: ${DATABASE_URL}
```

## 📚 Ressources

- [Symfony Environment Variables](https://symfony.com/doc/current/configuration.html#configuration-based-on-environment-variables)
- [Symfony Secrets](https://symfony.com/doc/current/configuration/secrets.html)
- [Doctrine DBAL Configuration](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html)

## 🆘 Support

En cas de problème avec la configuration :

1. Vérifier que `.env.dev` existe et contient toutes les variables
2. Vérifier les permissions des fichiers
3. Nettoyer le cache : `php bin/console cache:clear`
4. Consulter les logs : `var/log/dev.log`
