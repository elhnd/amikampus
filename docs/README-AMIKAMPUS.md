# AmikAmpu - Plateforme de Gestion pour Amicales Étudiantes

## 🎯 Vision du Projet

AmikAmpu est une plateforme de gestion complète pour les amicales étudiantes. Elle offre des outils modernes pour gérer les membres, les élections, les cotisations, les événements et bien plus encore.

**Approche** : Application simple d'abord, puis évolution vers multi-tenant si besoin.

## 🏗️ Architecture Technique

- **Framework** : Symfony 7.3 avec FrankenPHP
- **Architecture** : Hexagonale (Clean Architecture)
- **Frontend** : Twig (Phase 1) → Angular/React (Phase 2)
- **Base de données** : PostgreSQL (ou MySQL)
- **Conteneurisation** : Docker avec Caddy
- **Sécurité** : Symfony Security + JWT pour API future
- **Multi-tenant** : ⏸️ En stand-by (focus sur une amicale d'abord)

## 📋 Roadmap par Itérations - Approche Incrémentale

### 🔧 ITÉRATION 0 : Fondations Techniques (1 semaine)
**Objectif : Initialiser le projet avec les bases solides**

#### Périmètre
- [x] Structure Symfony + Docker/FrankenPHP configuré
- [ ] Architecture hexagonale en place
- [ ] Authentification basique (email + mot de passe)
- [ ] Modèle de données minimal (members, roles, member_roles)
- [ ] Migrations DB + gestion .env
- [ ] Commande création user admin

#### Exclusions
- OTP/vérification multi-canal
- Vérification documents
- Vote et élections
- Import Excel

#### Critères d'acceptation
✅ Un admin peut se connecter
✅ Structure entités validée + migrations exécutables
✅ Documentation README de démarrage

---

### 👤 ITÉRATION 1 : Inscription Simple (1 semaine)
**Objectif : Permettre inscription de base sans OTP**

#### Périmètre
- [ ] Endpoint POST /auth/register (email ou téléphone + mot de passe)
- [ ] Validations serveur : unicité email/téléphone, règles mot de passe
- [ ] Endpoint POST /auth/login
- [ ] Rate limiting sur création compte et login
- [ ] Compte actif par défaut avec statut `non_vérifié`

#### Exclusions
- Activation/OTP
- Vérification documents justificatifs
- Carte membre
- Import Excel

#### Critères d'acceptation
✅ Utilisateur peut créer un compte et se connecter
✅ Doublons email/téléphone refusés avec message clair
✅ Validations côté serveur en place (formats, contraintes)

---

### 🔐 ITÉRATION 1.5 : Activation par OTP (1 semaine)
**Objectif : Sécuriser l'activation des comptes**

#### Périmètre
- [ ] Génération OTP (6 chiffres, expiration configurable)
- [ ] Endpoint /auth/verify-otp
- [ ] Rate limiting sur génération et vérification OTP
- [ ] Service d'envoi email (Symfony Mailer)
- [ ] Abstraction canal WhatsApp (intégration ultérieure)

#### Dépendances
✓ Itération 1 (inscription simple)

#### Critères d'acceptation
✅ Utilisateur reçoit un OTP et active son compte
✅ OTP expiré → activation refusée
✅ Journal des tentatives OTP consultable par admin

---

### 📄 ITÉRATION 2 : Vérification Documentaire & Statuts (2 semaines)
**Objectif : Validation identité et statut étudiant/alumni**

#### Périmètre
- [ ] Upload multi-fichiers (PDF/JPG/PNG validés)
- [ ] Génération hash fichier + stockage chemin
- [ ] Interface validation admin : accepter/rejeter avec motif
- [ ] Statuts : `non_vérifié`, `vérifié_étudiant`, `vérifié_alumni`
- [ ] Blocage modification champs sensibles après vérification

#### Exclusions
- OCR automatique
- Notifications avancées

#### Dépendances
✓ Itération 1.5 (comptes activés)

#### Critères d'acceptation
✅ Passage automatique en `vérifié_étudiant` si certificat fourni
✅ Tentative modification nom après vérification → refus
✅ Rejet document affiche motif clair

---

### 👔 ITÉRATION 3 : Rôles & Bureau de l'Amicale (1 semaine)
**Objectif : Gestion des rôles administratifs**

#### Périmètre
- [ ] CRUD roles (Président, Secrétaire, Trésorier, etc.)
- [ ] Association member_roles avec dates (début, fin)
- [ ] Badge affiché dans profil
- [ ] Expiration automatique des rôles

#### Exclusions
- Attribution automatique post-élection (voir Itération 7)

#### Dépendances
✓ Itération 2 (membres vérifiés)

#### Critères d'acceptation
✅ Admin peut ajouter rôle à un membre avec durée
✅ Expiration rôle se reflète automatiquement
✅ Badge visible dans profil

---

### 🗳️ ITÉRATION 4 : Élections - MVP Vote en Ligne (2 semaines)
**Objectif : Système de vote électronique simple et sécurisé**

#### Périmètre
- [ ] Entités : elections, candidates, votes (avec hash_anonyme)
- [ ] Création élection (admin uniquement)
- [ ] Dépôt de candidature + validation
- [ ] Vote en ligne (un vote par membre vérifié)
- [ ] Secret du vote via hash anonyme
- [ ] Résultats agrégés après clôture

#### Exclusions
- Vote physique
- Modification du vote
- Supervision avancée
- Détection fraude avancée

#### Dépendances
✓ Itération 2 (membres vérifiés)
✓ Itération 3 (rôles pour admin)

#### Critères d'acceptation
✅ Candidat validé apparaît dans liste
✅ Membre vérifié vote → enregistrement hash_anonyme
✅ Tentative deuxième vote → refus
✅ Résultats accessibles après clôture élection

---

### 🔄 ITÉRATION 5 : Amélioration Vote - Modification & Supervision (1 semaine)
**Objectif : Permettre modification unique et surveiller le processus**

#### Périmètre
- [ ] Flag `has_modified` sur vote
- [ ] Paramètre `allow_vote_edit` par élection
- [ ] Rôle superviseur
- [ ] Tableau anomalies simples (ex: votes/min)

#### Exclusions
- Vote physique
- Prévention double vote hybride

#### Dépendances
✓ Itération 4 (vote en ligne stable)

#### Critères d'acceptation
✅ Modification vote possible une fois si paramètre activé
✅ Tableau supervision listant total votes + modifications

---

### 🏢 ITÉRATION 6 : Vote Hybride (Physique + En Ligne) (2 semaines)
**Objectif : Combiner vote en ligne et vote physique**

#### Périmètre
- [ ] Champ `canal` sur vote (en_ligne | physique)
- [ ] Scan QR pour vote physique (interface opérateur)
- [ ] Vérification conflit (physique + en ligne)
- [ ] Statut `conflit` avec alerte superviseur

#### Exclusions
- Synchronisation temps réel avancée (Kafka)
- Résolution automatisée conflits

#### Dépendances
✓ Itérations 4 & 5 (vote en ligne stable)

#### Critères d'acceptation
✅ Vote physique enregistré bloque vote en ligne ultérieur
✅ Conflit détecté si ordre inversé → visible dans supervision

---

### 🎖️ ITÉRATION 7 : Attribution Automatique Bureau Post-Élection (1 semaine)
**Objectif : Automatiser nomination du président élu**

#### Périmètre
- [ ] Calcul vainqueur (max voix)
- [ ] Création automatique entrée member_roles pour Président
- [ ] Notification admin + membre élu

#### Exclusions
- Nomination automatique autres rôles (reste manuel)

#### Dépendances
✓ Itération 4 (résultats fiables)
✓ Itération 3 (système rôles)

#### Critères d'acceptation
✅ Clôture élection → rôle Président attribué au gagnant
✅ Badge visible immédiatement dans profil

---

### 📊 ITÉRATION 8 : Import Massif & Optimisation Inscription (1 semaine)
**Objectif : Accélérer l'on-boarding via import Excel**

#### Périmètre
- [ ] Upload fichier .xlsx avec validation colonnes
- [ ] Pré-création comptes en statut `non_vérifié`
- [ ] Envoi lien activation automatique
- [ ] Rapport d'erreurs (lignes invalides)

#### Exclusions
- Déduplication fuzzy avancée

#### Dépendances
✓ Itérations 1-2 (auth + vérification)

#### Critères d'acceptation
✅ Fichier avec colonnes correctes → création en lot
✅ Rapport erreurs généré pour lignes invalides

---

### 🎫 ITÉRATION 9 : Carte Membre & QR Code (2 semaines)
**Objectif : Génération carte membre digitale avec QR**

#### Périmètre
- [ ] Endpoint génération QR signé (HMAC)
- [ ] Enregistrement paiement carte (stub/module test)
- [ ] Génération PDF/image carte membre
- [ ] Affichage statut paiement dans QR

#### Exclusions
- Intégration wallets Apple/Android (phase ultérieure)

#### Dépendances
✓ Itération 2 (membre vérifié)

#### Critères d'acceptation
✅ QR scanné retourne identité publique + statut vérification
✅ Paiement "payé" débloque génération carte

---

### 🔔 ITÉRATION 10 : Notifications & Rappels (1 semaine)
**Objectif : Automatiser communications importantes**

#### Périmètre
- [ ] Cron interne (commande Symfony)
- [ ] Table notifications
- [ ] Rappels renouvellement statut étudiant
- [ ] Confirmations actions clés (vote, etc.)
- [ ] Envoi email + log statut

#### Exclusions
- Multi-canal avancé (WhatsApp réel, push PWA)

#### Dépendances
✓ Itération 2 (statuts)
✓ Itération 4-6 (vote)

#### Critères d'acceptation
✅ Tâche planifiée génère notifications renouvellement
✅ Confirmation vote envoyée sans divulguer choix

---

### � ITÉRATION 11 : PWA & Offline de Base (1 semaine)
**Objectif : Améliorer UX mobile + accès partiel hors ligne**

#### Périmètre
- [ ] Manifest.json
- [ ] Service Worker cache profils & listes candidats
- [ ] Installation mobile possible

#### Exclusions
- Synchronisation offline votes

#### Critères d'acceptation
✅ Installation possible sur mobile
✅ Accès hors ligne à profil + dernière liste élections

---

### 🛡️ ITÉRATION 12 : Supervision Avancée & Anti-Fraude (1 semaine)
**Objectif : Détection anomalies + alertes**

#### Périmètre
- [ ] Seuil votes/minute
- [ ] Suspicion double vote non résolu
- [ ] Export audit
- [ ] Tableau supervision avec alertes codées

#### Exclusions
- ML / détection comportementale

#### Dépendances
✓ Itérations 5-6 (supervision basique)

#### Critères d'acceptation
✅ Tableau affiche alertes (niveau, timestamp)
✅ Export CSV audit disponible

---

### 💰 ITÉRATION 13 : Module Trésorerie (2 semaines)
**Objectif : Gestion financière basique**

#### Périmètre
- [ ] Enregistrements transactions (cotisation, paiement carte)
- [ ] Sommes agrégées
- [ ] Tableau entrées/sorties + solde
- [ ] Filtres par période

#### Exclusions
- Comptabilité complète
- Multi-devise

#### Dépendances
✓ Itération 9 (paiement carte)
✓ Itération 3 (rôles admin)

#### Critères d'acceptation
✅ Tableau des entrées/sorties + solde visible
✅ Filtre par période fonctionnel

---

### 🎉 ITÉRATION 14 : Modules Événements & Ressources (2 semaines)
**Objectif : Enrichir l'expérience membre**

#### Périmètre
- [ ] CRUD événements avec capacité
- [ ] Inscription participants + liste
- [ ] Section ressources pédagogiques (catégories + upload)
- [ ] Liste logements étudiants (optionnel)

#### Exclusions
- Streaming vidéo
- Réservation complexe

#### Dépendances
✓ Base membres stable (Itération 2)

#### Critères d'acceptation
✅ Membre vérifié s'inscrit à un événement
✅ Ressource téléversée catégorisée consultable

---

### ⚡ ITÉRATION 15 : Optimisation & Polish Final (2 semaines)
**Objectif : Réduire dette technique et améliorer performances**

#### Périmètre
- [ ] Index DB optimisés
- [ ] Cache résultats élection
- [ ] Amélioration logs RGPD
- [ ] Tests de charge basiques
- [ ] Documentation technique complète

#### Exclusions
- Refactor majeur architecture

#### Dépendances
✓ Toutes itérations précédentes

#### Critères d'acceptation
✅ Tests de charge OK (500 votes en < 1 min)
✅ Latence < seuil défini pour endpoints critiques
✅ Couverture tests > 60% sur domaine critique

---

## 📊 Vue d'ensemble des Itérations

| Itération | Focus | Durée | Statut |
|-----------|-------|-------|--------|
| 0 | Fondations | 1 sem | ⏳ En cours |
| 1 | Inscription simple | 1 sem | 📋 Planifié |
| 1.5 | OTP | 1 sem | 📋 Planifié |
| 2 | Vérification docs | 2 sem | 📋 Planifié |
| 3 | Rôles & Bureau | 1 sem | 📋 Planifié |
| 4 | Vote en ligne MVP | 2 sem | 📋 Planifié |
| 5 | Amélioration vote | 1 sem | 📋 Planifié |
| 6 | Vote hybride | 2 sem | 📋 Planifié |
| 7 | Attribution auto | 1 sem | 📋 Planifié |
| 8 | Import Excel | 1 sem | 📋 Planifié |
| 9 | Carte membre | 2 sem | 📋 Planifié |
| 10 | Notifications | 1 sem | 📋 Planifié |
| 11 | PWA | 1 sem | 📋 Planifié |
| 12 | Anti-fraude | 1 sem | 📋 Planifié |
| 13 | Trésorerie | 2 sem | 📋 Planifié |
| 14 | Événements | 2 sem | 📋 Planifié |
| 15 | Optimisation | 2 sem | 📋 Planifié |

**Total estimé : ~23 semaines (5-6 mois)**

**Note** : Voir [ARCHITECTURE.md](ARCHITECTURE.md) pour les détails techniques complets de l'architecture hexagonale et les exemples de code.

---

## 🎨 Fonctionnalités Principales

### 👥 Gestion des Membres
- **Inscription & Authentification** : Email/téléphone + OTP
- **Profils détaillés** : Informations personnelles, académiques
- **Vérification documentaire** : Upload et validation certificats
- **Statuts** : Non vérifié, Vérifié étudiant, Vérifié alumni
- **Import massif** : Excel pour on-boarding rapide
- **Historique** : Traçabilité complète des actions

### � Gestion des Rôles & Bureau
- **Rôles flexibles** : Président, Secrétaire, Trésorier, Membre bureau
- **Dates de mandat** : Début et fin automatisées
- **Badges visibles** : Affichage dans profils
- **Attribution automatique** : Après élections

### 🗳️ Système d'Élections
- **Vote en ligne sécurisé** : Secret du vote garanti
- **Vote hybride** : En ligne + physique
- **Supervision** : Détection anomalies et conflits
- **Modification unique** : Si autorisé par l'élection
- **Résultats automatiques** : Calcul et attribution rôles

### 💰 Gestion Financière
- **Cotisations** : Suivi paiements membres
- **Transactions** : Entrées/sorties avec catégories
- **Tableau de bord** : Vue d'ensemble financière
- **Rapports** : Export et statistiques
- **Paiement carte** : Intégration module paiement

### 🎫 Carte Membre
- **QR Code sécurisé** : Signature HMAC
- **Génération automatique** : PDF/Image
- **Statut paiement** : Visible dans le QR
- **Validation rapide** : Scan et vérification

### � Événements & Activités
- **Création événements** : Avec capacité limitée
- **Inscriptions** : Gestion participants
- **Calendrier** : Vue d'ensemble activités

### � Ressources Pédagogiques
- **Bibliothèque** : Documents catégorisés
- **Upload/téléchargement** : Partage facilité
- **Organisation** : Par catégories

### 🔔 Notifications
- **Rappels automatiques** : Renouvellement statut
- **Confirmations** : Actions importantes (vote, inscription)
- **Multi-canal** : Email (+ WhatsApp prévu)

## 🔒 Sécurité & Conformité

### Sécurité
- **Authentification robuste** : Email + OTP
- **HTTPS obligatoire** : Chiffrement bout en bout
- **Secret du vote** : Hash anonyme pour élections
- **Rate limiting** : Protection contre attaques
- **Audit trail** : Logs immuables (append-only)
- **QR Code signé** : HMAC pour cartes membres

### Conformité RGPD
- **Consentement explicite** : Gestion des préférences
- **Droit à l'oubli** : Suppression données
- **Portabilité** : Export données personnelles
- **Logs audit** : Traçabilité actions sensibles
- **Privacy by design** : Sécurité native

### Anti-Fraude (Élections)
- **Détection anomalies** : Seuils votes/minute
- **Supervision** : Tableau de bord conflits
- **Vote unique** : Prévention double vote hybride
- **Export audit** : CSV pour vérifications

## 📊 KPIs & Métriques

### Métriques Adoption
- **Nombre de membres** : Total inscrits
- **Taux d'activation** : % comptes activés via OTP
- **Taux de vérification** : % membres vérifiés
- **Engagement** : Taux de participation élections/événements

### Métriques Élections
- **Participation** : % membres votants / vérifiés
- **Conflits détectés** : Vote hybride
- **Modifications** : % votes modifiés
- **Temps moyen** : Durée processus de vote

### Métriques Financières
- **Volume transactions** : Nombre/mois
- **Taux paiement** : % cotisations payées
- **Délai moyen** : Validation paiements

### Métriques Techniques
- **Performance** : Temps de réponse endpoints
- **Disponibilité** : Uptime système
- **Tests** : Couverture > 60% domaine critique
- **Sécurité** : Tentatives d'accès non autorisées

## 🚀 Déploiement

### Environnements
- **Développement** : Docker local avec FrankenPHP
- **Staging** : Environnement de test
- **Production** : Cloud (AWS/GCP/DigitalOcean)
- **CI/CD** : GitHub Actions ou GitLab CI

### Stack Production
- **Serveur** : FrankenPHP + Caddy (HTTPS auto)
- **Base de données** : PostgreSQL/MySQL managée
- **Cache** : Redis (optionnel, Itération 15)
- **Stockage fichiers** : Local ou S3-compatible
- **Monitoring** : Logs + métriques basiques

---

## 🎯 Backlog Futur (Hors Itérations Actuelles)

### Fonctionnalités Avancées
- 🤖 **OCR documents** : Automatisation partielle validation
- 📱 **Wallet Apple/Google Pass** : Intégration native
- 🎮 **Gamification** : Système réputation avancé
- 💬 **Chat/Forum interne** : Communication temps réel
- 📈 **API publique** : Export statistiques transparentes
- 🌍 **Multi-langue** : Internationalisation

### Évolution Architecture
- 🏢 **Multi-tenant** : Support plusieurs amicales
- 🔄 **Microservices** : Architecture modulaire
- ⚡ **Event sourcing** : Pour audit avancé
- 🚀 **GraphQL** : Alternative REST

---

## 📚 Critères Qualité Transversaux

### Tests
- **Couverture > 60%** : Sur domaine critique (auth, vote, vérification)
- **Tests unitaires** : Domain + Application
- **Tests intégration** : Infrastructure (DB, API)
- **Tests E2E** : Parcours utilisateur critiques

### Code Quality
- **Lint & CI** : Passage obligatoire avant production
- **PSR-12** : Standards PHP respectés
- **Documentation** : Code commenté + README à jour
- **Revue de code** : Validation par pairs

### Performance
- **< 200ms** : Endpoints critiques (login, vote)
- **500 votes/min** : Capacité élection
- **Pagination** : Listes > 100 items
- **Index DB** : Optimisés pour requêtes fréquentes

---

**Prochaines étapes** : Commencer l'Itération 0 avec l'architecture hexagonale et l'authentification de base. Prêt à démarrer ? 🚀

**Rappel** : Le multi-tenant est en stand-by. Focus sur une application simple et fonctionnelle pour une seule amicale d'abord.


# Stack Technique
Frontend:
  - Symfony UX (Stimulus, Turbo, Live Components)
  - Tailwind CSS (styling moderne)
  - Alpine.js (interactions légères)
  - Twig (templating)
  
Avantages:
  - Performance optimale
  - Progressive Enhancement
  - SEO-friendly
  - Maintenance simplifiée
  - Mobile-first responsive