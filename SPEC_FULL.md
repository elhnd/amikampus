############################################################
# SPÉCIFICATIONS FONCTIONNELLES – PLATEFORME AMIKAMPUS
# Version : Draft enrichi (copie de text.txt)
# Objectif : Centraliser la gestion des membres de l’amical, des rôles,
# des vérifications, des votes et des futurs services communautaires.
############################################################

## 1. VISION & OBJECTIFS
Amikampus est une plateforme communautaire destinée aux amicales d’étudiants d’une localité donnée. Elle permet :
- L’inscription, la vérification et la gestion du statut des membres (étudiants actifs, alumni, non vérifiés).
- L’organisation transparente des élections internes (bureau de l’amical) avec un système de vote sécurisé hybride (en ligne + physique).
- L’extension future vers des modules de services : trésorerie, logements, ressources pédagogiques, événements, dons, information.
- Une expérience moderne multi-support (PWA) accessible offline partiellement.

## 2. GLOSSAIRE
- Membre : Toute personne inscrite (vérifiée ou non).
- Membre vérifié : Profil validé après contrôle des pièces justificatives.
- Alumni : Ancien étudiant (plus d’inscription en cours) mais membre de l’amical.
- Admin : Membre avec privilèges de gestion globale (peut aussi être vérificateur).
- Vérificateur : Rôle dédié à l’examen et validation des documents.
- Bureau : Ensemble des rôles élus ou nommés (Président, Secrétaire Général, Trésorier, etc.).
- Candidat : Membre dont la candidature à une élection a été acceptée.
- Superviseur : Membre mandaté pour surveiller le déroulement d’un vote.
- Badge : Marqueur visuel du rôle (président, candidat, superviseur…).
- Session électorale : Période d’ouverture des votes pour une élection donnée.

## 3. ACTEURS & RÔLES
| Acteur        | Droits principaux |
|---------------|------------------|
| Membre        | Accès profil, mise à jour limitée, vote (si vérifié), consultation contenu |
| Membre non vérifié | Accès limité, pas de vote |
| Admin         | CRUD membres, import Excel, validation finale, configuration systèmes |
| Vérificateur  | Analyse pièces, attribut statut vérifié/non vérifié |
| Candidat      | Accès espace campagne, publication programme |
| Superviseur   | Supervision des votes, accès tableau de contrôle technique |
| Président     | Désigne autres membres du bureau après élection |

## 4. PARCOURS UTILISATEUR PRINCIPAL
### 4.1 Inscription
Modes disponibles :
1. Auto-inscription via formulaire (email ou téléphone comme identifiant primaire).
2. Pré-inscription par Admin (saisie minimale puis envoi lien d’activation).
3. Import massif depuis fichier Excel (.xlsx) standardisé (colonnes : nom, prénom, date de naissance, email, téléphone, lieu de naissance, année bac…).

### 4.2 Validation initiale
- Envoi d’un code de validation (OTP) par WhatsApp et/ou email (durée de vie configurable, ex : 10 min, 6 chiffres).
- Après validation du compte : statut = « non vérifié » par défaut.

### 4.3 Fourniture des justificatifs
Pièces acceptées :
- Bac (diplôme ou relevé) attestant localité
- Carte d’identité / Passeport (lieu de naissance)
- Autre document (ex : certificat de résidence)
- Certificat d’inscription annuel pour statut « étudiant en cours », sinon bascule en « alumni ».

Workflow vérification :
1. Membre charge documents (formats : PDF/JPG/PNG, taille max paramétrable).
2. Système calcule empreinte (hash) pour intégrité et anti-doublon.
3. Vérificateur examine, approuve ou rejette avec motif.
4. Admin peut surclasser décision.
5. Passage à « vérifié » verrouille champs sensibles : nom(s), date & lieu de naissance.

### 4.4 Connexion
- Via email + mot de passe ou téléphone + code OTP.
- Option future : authentification à deux facteurs (2FA) pour rôles sensibles.

### 4.5 Mise à jour du profil
Editable : adresse actuelle, photo, parcours académique, parcours professionnel.
Non modifiable après vérification : nom complet, date naissance, lieu naissance.

### 4.6 Carte de membre
- Génération carte numérique avec QR Code (format payload JSON signé : {member_id, hash, expiry}).
- Intégration wallet (Apple/Android) – phase 2.
- Paiement requis (module monétique à intégrer ultérieurement – stocker état paiement « en attente / payé / expiré »).

## 5. STATUTS & RÈGLES
Statuts principaux utilisateur :
- non_vérifié
- vérifié_étudiant (actif)
- vérifié_alumni
- bloqué (suspension accès vote et actions sensibles)

Transitions :
- non_vérifié → vérifié_étudiant : après validation docs + certificat année.
- vérifié_étudiant → vérifié_alumni : expiration certificat (non renouvelé après rappel).
- tout → bloqué : action Admin (motif obligatoire, journalisation).

## 6. MODÈLE DE DONNÉES (ESQUISSE)
Tables clés envisagées :
- members(id, nom, prenom, date_naissance, lieu_naissance, email, telephone, statut_verification, statut_cycle, created_at, updated_at)
- member_documents(id, member_id, type_document, chemin, hash, statut, motif_rejet, checked_by, checked_at)
- roles(id, code, libelle)
- member_roles(member_id, role_id, start_at, end_at)
- elections(id, type, titre, date_debut, date_fin, statut, created_at)
- candidates(id, election_id, member_id, statut, caution_payee, programme_resume, programme_detail)
- votes(id, election_id, member_id, candidat_id, canal, hash_anonyme, created_at, modified_at)
- vote_audit(id, vote_id, action, actor_id, timestamp)
- payments(id, member_id, objet, montant, statut, reference)
- notifications(id, member_id, type, canal, payload, statut_envoi, created_at)

## 7. NOTIFICATIONS
- Rappels renouvellement certificat étudiant (cron à date paramétrable – ex : début d’année académique).
- Avertissements expiration de carte membre.
- Confirmation vote enregistré ou modifié (sans révéler le candidat).

## 8. BUREAU DE L’AMICAL
- Attribution automatique badge « président » au gagnant de l’élection principale.
- Président nomme : secrétaire général, trésorier, etc. (workflow nomination + acceptation).
- Durée de mandat paramétrable (date de fin stockée dans member_roles.end_at). Alertes de fin de mandat.

## 9. SYSTÈME DE VOTE
### 9.1 Cycle électoral
1. Création élection (Admin) : période, type (bureau annuel), paramètres (durée, modification de vote autorisée = oui/non).
2. Dépôt candidatures : formulaire + pièces éventuelles + caution monétique.
3. Validation candidatures (Admin/Vérificateur) → statut « candidat » + badge.
4. Campagne : candidats publient programme (modération possible).
5. Ouverture vote : bascule élections.statut = « ouvert ».
6. Clôture : statut « clos » + verrou votes.
7. Dépouillement + consolidation hybride.

### 9.2 Règles de vote
- Un membre vérifié ne peut voter qu’une fois (possibilité de modification unique si autorisé). Stocker boolean has_modified.
- Vote secret : stockage du lien membre → vote via hash_anonyme (ex : HMAC(member_id + election_id + sel)).
- Canal vote : « en_ligne » ou « physique ».

### 9.3 Prévention du double vote (physique + en ligne)
Stratégie proposée :
1. Registre d’éligibilité généré avant ouverture : liste des member_id autorisés.
2. Pour vote physique : opérateur scanne QR membre → système crée entrée votes avec canal=physique et flag verrouillant vote en ligne.
3. Pour vote en ligne : avant insertion, vérifier absence entrée canal=physique.
4. Si vote physique ajouté après vote en ligne : interface supervision signale conflit → règle de priorité (ex : première occurrence conservée, seconde mise en révision).
5. Audit : table vote_audit journalise modifications/annulations.
6. Option avancée (phase 2) : synchronisation quasi temps réel via file d’événements (ex : Redis Stream / Kafka) pour éviter latence conflit.

### 9.4 Supervision & Sécurité
- Rôle superviseur : accès tableau monitoring (compteur votes valides, anomalies, conflits potentiels).
- Mécanismes anti-fraude : seuil d’alerte (ex : > X votes/minute), détection doublons hash_anonyme.
- Chiffrement au repos des données sensibles (documents, identifiants).

### 9.5 Dépouillement & Résultats
- Agrégation : SELECT candidat_id, COUNT(*) GROUP BY candidat_id (votes valides).
- Séparation canal : stats par canal + total consolidé.
- Publication progressive (dashboard temps réel) si autorisé.
- Attribution automatique rôle « président » au vainqueur (création entrée member_roles).

### 9.6 Interface Candidats
- Page liste candidats (photo, nom, résumé programme).
- Page détail : vision, objectifs, documents de campagne.
- Module édition → statut « brouillon » puis « publié ».

## 10. PERFORMANCE & PWA
- Mise en cache offline : profil membre, liste élections ouvertes, programmes.
- Stratégie caching : Stale-While-Revalidate pour contenu semi-statique.
- Workers : génération QR + pré-calcul statistiques vote en tâche arrière.

## 11. SÉCURITÉ & CONFORMITÉ
- RGPD : consentement explicite, droit à suppression (sauf traces légales minimales d’audit anonymisées).
- Journaux : actions sensibles (vérification, blocage, vote) horodatées.
- Limitation brute force : throttle login (ex : 5 tentatives/15 min/IP).
- Hash documents : SHA-256 pour éviter ré-upload identique.
- Politique blocage : nécessite motif + durée (indéterminée ou jusqu’à date).

## 12. API (ÉBAUCHE ENDPOINTS)
- POST /auth/register
- POST /auth/verify-otp
- POST /auth/login
- GET /members/{id}
- POST /members/{id}/documents
- POST /members/import (Excel)
- GET /elections
- POST /elections/{id}/candidates
- POST /elections/{id}/vote
- PUT /elections/{id}/vote (modification unique)
- GET /elections/{id}/results
- GET /dashboard/votes

## 13. BACKLOG PRIORISÉ
MVP :
1. Inscription + Validation OTP
2. Vérification documents + statuts
3. Gestion rôles basiques (admin, vérificateur)
4. Création élection + vote en ligne simple + résultats
5. Prévention double vote minimal (verrouillage canal)

Phase 2 :
6. Import Excel
7. Carte membre QR + paiement
8. Campagne candidats avancée
9. Supervision + alertes fraude
10. PWA offline enrichie

Phase 3 : Modules services (trésorerie, logements, événements, ressources pédagogiques, dons, information).

## 14. KPI SUIVIS
- Taux de conversion inscription → vérification
- Nombre de conflits de vote détectés
- Temps moyen validation documents
- Taux de renouvellement statut étudiant
- Adoption carte membre (commandes vs membres vérifiés)

## 15. RISQUES & ATTÉNUATIONS
- Fraude vote hybride : verrou canal + audit + supervision temps réel.
- Saturation vérification docs : file d’attente + priorisation + notifications retard.
- Perte confidentialité : minimiser champ stocké, hash anonymisation vote.
- Mauvaise UX import Excel : gabarit modèle + validation serveur détaillée.

## 16. ANNEXES
### 16.1 Exemple flux vote en ligne
1. Authentification membre vérifié.
2. Requête GET /elections/{id} (statut = ouvert ?)
3. Affichage liste candidats.
4. POST /elections/{id}/vote {candidat_id}
5. Système : génère hash_anonyme + insère vote.
6. Retour : confirmation (sans nom candidat dans log audit public).

### 16.2 Format QR Code (proposition payload)
{
	"mid": 12345,
	"ts": 1741200000,
	"sig": "<HMAC>"
}

### 16.3 Règle modification vote
- Autorisée si election.allow_vote_edit = true ET vote.modified_at IS NULL.
- Audit crée entrée action = "MODIFICATION".

## 17. AMÉLIORATIONS FUTURES (IDÉES)
- Vérification semi-automatique OCR des documents.
- Système réputation (participation événements, ponctualité). 
- Export anonymisé statistiques pour transparence.
- Gamification (badges supplémentaires : mentor, bénévole).

############################################################
# FIN DU DOCUMENT
############################################################
