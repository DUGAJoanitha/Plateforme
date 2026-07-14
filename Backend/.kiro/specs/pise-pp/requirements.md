# PISE-PP - Document des Exigences

## Introduction

PISE-PP (Plateforme Intelligente de Suivi-Évaluation de Projets et Programmes) est une solution numérique avancée destinée aux ONG, institutions publiques et organisations de développement. Elle couvre l'ensemble du cycle de vie d'un projet : planification, exécution, suivi des KPI, contrôle budgétaire, collecte terrain hors-ligne, génération automatique de rapports et aide à la décision par l'IA.

### Objectifs

- Centraliser la gestion de projets et programmes
- Automatiser le suivi-évaluation avec des KPI
- Fournir des analyses prédictives via IA (OpenAI/Gemini)
- Permettre la collecte terrain hors-ligne (mobile)
- Générer des rapports automatiques
- Assurer la sécurité (OWASP Top 10, ISO 27001)

### Périmètre

| Inclus | Exclus |
|--------|--------|
| Gestion de projets/programmes | Comptabilité complète |
| Suivi des KPI et indicateurs | Paie/RH |
| Budget et dépenses | Gestion documentaire avancée |
| Collecte terrain mobile | Intégration ERP externe |
| Rapports automatiques | |
| IA prédictive | |

## Glossaire

| Terme | Définition |
|-------|------------|
| **Projet** | Ensemble d'activités avec objectifs, budget et durée définis |
| **Programme** | Ensemble de projets liés sous une vision commune |
| **KPI** | Key Performance Indicator - Indicateur de performance |
| **Jalon** | Point de contrôle majeur dans un projet |
| **Activité** | Tâche ou ensemble de tâches à réaliser |
| **Collecte terrain** | Saisie de données sur le terrain via mobile |

---

## Exigences Fonctionnelles

### Module 1 : Authentification et Sécurité

#### Requirement 1: Gestion des Utilisateurs

**User Story:** En tant qu'administrateur, je veux gérer les comptes utilisateurs afin de contrôler l'accès à la plateforme.

#### Acceptance Criteria

1. L'administrateur peut créer, modifier, désactiver des comptes utilisateurs
2. Chaque utilisateur a un rôle (admin, manager, field_agent, stakeholder, viewer)
3. L'inscription peut être publique ou restreinte selon configuration
4. La réinitialisation de mot de passe par email est disponible
5. Les mots de passe doivent respecter les critères de complexité (min 8 caractères, majuscule, chiffre, spécial)

---

#### Requirement 2: Authentification Multi-Facteur (2FA)

**User Story:** En tant qu'utilisateur, je veux activer l'authentification à deux facteurs afin de sécuriser mon compte.

#### Acceptance Criteria

1. L'utilisateur peut activer/désactiver le 2FA depuis son profil
2. Le 2FA supporte TOTP (Google Authenticator, Authy)
3. Le 2FA supporte l'envoi par SMS (optionnel)
4. Des codes de récupération sont générés et téléchargeables
5. Le 2FA est obligatoire pour les rôles admin et manager (configurable)

---

#### Requirement 3: Gestion des Rôles et Permissions

**User Story:** En tant qu'administrateur, je veux assigner des rôles et permissions afin de contrôler les accès aux fonctionnalités.

#### Acceptance Criteria

1. Les rôles prédéfinis sont : admin, manager, field_agent, stakeholder, viewer
2. Chaque rôle a un ensemble de permissions
3. L'administrateur peut créer des rôles personnalisés
4. Les permissions sont granulaires par module et action (create, read, update, delete)
5. L'assignation multiple de rôles est possible

---

### Module 2 : Organisations et Multi-Tenant

#### Requirement 4: Gestion des Organisations

**User Story:** En tant que super-administrateur, je veux créer et gérer des organisations afin d'isoler les données par entité.

#### Acceptance Criteria

1. Le super-admin peut créer, modifier, désactiver des organisations
2. Chaque organisation a un nom, logo, adresse, contacts
3. Les données sont isolées entre organisations (multi-tenant)
4. Un utilisateur peut appartenir à plusieurs organisations
5. L'organisation a des paramètres de configuration propres

---

#### Requirement 5: Structure Hiérarchique

**User Story:** En tant que manager, je veux définir la structure hiérarchique de mon organisation afin de refléter l'organisation réelle.

#### Acceptance Criteria

1. L'organisation peut avoir des départements/divisions
2. Chaque département peut avoir un responsable
3. Les projets peuvent être rattachés à un département
4. La hiérarchie influence les droits d'accès
5. Les rapports peuvent être consolidés par niveau hiérarchique

---

### Module 3 : Projets et Programmes

#### Requirement 6: Création de Projets

**User Story:** En tant que manager, je veux créer un projet avec ses informations de base afin de l'enregistrer dans la plateforme.

#### Acceptance Criteria

1. Le projet a un nom, description, objectif, budget, dates (début/fin)
2. Le projet est rattaché à une organisation
3. Le projet peut être lié à un programme parent
4. Le statut du projet peut être : draft, active, on_hold, completed, cancelled
5. Les pièces jointes (documents) peuvent être ajoutées

---

#### Requirement 7: Cycle de Vie du Projet

**User Story:** En tant que manager, je veux suivre et mettre à jour le statut de mes projets afin de refléter leur avancement réel.

#### Acceptance Criteria

1. Le workflow de statut est configurable
2. Les transitions de statut sont journalisées avec date et auteur
3. Les notifications sont envoyées aux parties prenantes lors des changements
4. L'historique des modifications est conservé
5. La réouverture d'un projet clôturé est possible avec justification

---

#### Requirement 8: Gestion des Jalons

**User Story:** En tant que manager, je veux définir des jalons pour mon projet afin de marquer les étapes clés.

#### Acceptance Criteria

1. Le jalon a un nom, description, date prévue, date réelle
2. Le jalon peut être marqué comme atteint/non atteint
3. Les alertes sont envoyées avant la date prévue
4. Les jalons apparaissent sur la timeline du projet
5. Le retard d'un jalon impacte l'indicateur de santé du projet

---

#### Requirement 9: Activités et Tâches

**User Story:** En tant que chef de projet, je veux décomposer mon projet en activités et tâches afin de gérer finement l'exécution.

#### Acceptance Criteria

1. L'activité a un nom, description, responsable, dates, statut
2. Les tâches peuvent être créées sous une activité
3. Les tâches peuvent avoir des dépendances (prédécesseurs)
4. L'avancement des tâches peut être saisi en pourcentage
5. L'assignation multiple de responsables est possible

---

#### Requirement 10: Gestion des Programmes

**User Story:** En tant que directeur, je veux regrouper plusieurs projets dans un programme afin d'avoir une vue consolidée.

#### Acceptance Criteria

1. Le programme a un nom, description, budget global, dates
2. Les projets peuvent être ajoutés/retirés du programme
3. Le budget du programme est la somme des budgets projets
4. Les KPI du programme sont calculés à partir des projets
5. Les rapports de programme consolident les données de tous les projets

---

### Module 4 : Budget et Finance

#### Requirement 11: Planification Budgétaire

**User Story:** En tant que manager, je veux définir le budget de mon projet avec des lignes budgétaires afin de planifier les dépenses.

#### Acceptance Criteria

1. Le budget total est défini au niveau du projet
2. Les lignes budgétaires ont : catégorie, description, montant prévu
3. Les catégories budgétaires sont configurables par organisation
4. La répartition budgétaire par période (mois/trimestre) est possible
5. Le budget peut être modifié avec traçabilité des modifications

---

#### Requirement 12: Suivi des Dépenses

**User Story:** En tant que gestionnaire financier, je veux enregistrer les dépenses réelles afin de comparer avec le budget prévu.

#### Acceptance Criteria

1. La dépense a : montant, date, catégorie, description, justificatif
2. La dépense est rattachée à une ligne budgétaire
3. Le solde disponible est mis à jour automatiquement
4. Les alertes sont émises en cas de dépassement de budget
5. L'export des dépenses en Excel/CSV est possible

---

#### Requirement 13: Alertes Budgétaires

**User Story:** En tant que manager, je veux recevoir des alertes budgétaires afin d'être informé des risques de dépassement.

#### Acceptance Criteria

1. L'alerte est déclenchée à 75% et 90% de consommation
2. L'alerte est envoyée par email et notification in-app
3. Le seuil d'alerte est configurable par projet
4. L'historique des alertes est consultable
5. Les alertes peuvent être acquittées par le manager

---

### Module 5 : KPI et Indicateurs

#### Requirement 14: Définition des KPI

**User Story:** En tant que manager, je veux définir les indicateurs de performance de mon projet afin de mesurer l'avancement.

#### Acceptance Criteria

1. Le KPI a : nom, description, unité, cible, baseline, fréquence de collecte
2. Le KPI peut être quantitatif ou qualitatif
3. Le KPI peut être rattaché au projet, programme ou organisation
4. Les formules de calcul peuvent être définies pour les KPI dérivés
5. Les KPI peuvent être groupés par catégorie

---

#### Requirement 15: Collecte des Valeurs KPI

**User Story:** En tant que field_agent, je veux saisir les valeurs des indicateurs afin d'alimenter le suivi.

#### Acceptance Criteria

1. La saisie peut se faire via web ou mobile
2. La valeur est associée à une date et optionnellement un lieu
3. Les pièces justificatives peuvent être jointes
4. La validation des valeurs peut être requise (workflow)
5. La saisie hors-ligne est synchronisée quand la connexion est disponible

---

#### Requirement 16: Tableaux de Bord KPI

**User Story:** En tant que manager, je veux visualiser les KPI sous forme de tableaux de bord afin d'avoir une vue synthétique.

#### Acceptance Criteria

1. Les graphiques disponibles : courbes, barres, camemberts, jauges
2. Les tableaux de bord sont personnalisables par utilisateur
3. Les filtres par période, projet, programme sont disponibles
4. L'export en PDF et image est possible
5. Le rafraîchissement des données peut être automatique ou manuel

---

### Module 6 : Collecte Terrain (Mobile)

#### Requirement 17: Application Mobile Offline-First

**User Story:** En tant que field_agent, je veux collecter des données terrain sans connexion internet afin de travailler dans des zones isolées.

#### Acceptance Criteria

1. L'application mobile fonctionne en mode hors-ligne complet
2. Les données sont stockées localement et synchronisées à la connexion
3. Les conflits de synchronisation sont résolus automatiquement ou manuellement
4. La géolocalisation des données est capturée
5. Les photos et fichiers peuvent être joints aux collectes

---

#### Requirement 18: Formulaires Dynamiques

**User Story:** En tant que manager, je veux créer des formulaires de collecte personnalisés afin d'adapter la collecte aux besoins du projet.

#### Acceptance Criteria

1. Le créateur de formulaire permet différents types de champs (texte, nombre, date, choix, photo, GPS)
2. Les champs peuvent être obligatoires ou optionnels
3. Les règles de validation sont configurables
4. Les formulaires peuvent être versionnés
5. Le formulaire est téléchargeable sur mobile pour usage hors-ligne

---

#### Requirement 19: Synchronisation des Données

**User Story:** En tant que field_agent, je veux synchroniser mes collectes terrain quand j'ai une connexion afin d'intégrer les données au système.

#### Acceptance Criteria

1. La synchronisation peut être manuelle ou automatique
2. L'état de synchronisation est visible (en attente, synchronisé, erreur)
3. Les erreurs de synchronisation sont journalisées
4. La reprise après échec est automatique
5. La synchronisation est optimisée pour les connexions lentes

---

### Module 7 : Rapports

#### Requirement 20: Génération de Rapports

**User Story:** En tant que manager, je veux générer des rapports automatiques afin de communiquer sur l'avancement des projets.

#### Acceptance Criteria

1. Les modèles de rapport sont prédéfinis (mensuel, trimestriel, annuel, projet)
2. Les rapports sont générés en PDF et/ou Excel
3. Les rapports incluent : résumé exécutif, KPI, budget, risques, recommandations
4. La planification de génération automatique est possible
5. Les rapports générés sont archivés et consultables

---

#### Requirement 21: Templates de Rapports

**User Story:** En tant qu'administrateur, je veux créer des templates de rapports personnalisés afin d'adapter les sorties aux besoins de l'organisation.

#### Acceptance Criteria

1. L'éditeur de template permet de définir la structure du rapport
2. Les variables dynamiques sont insérables (nom projet, KPI, dates)
3. Le logo et le branding de l'organisation sont appliqués
4. Les templates peuvent être partagés entre organisations
5. L'aperçu du rapport est disponible avant génération

---

### Module 8 : Intelligence Artificielle

#### Requirement 22: Analyse Prédictive

**User Story:** En tant que manager, je veux recevoir des prédictions IA sur mes projets afin d'anticiper les risques.

#### Acceptance Criteria

1. L'IA prédit la probabilité de respect des délais
2. L'IA prédit les risques de dépassement budgétaire
3. L'IA suggère des actions correctives
4. Les prédictions sont basées sur les données historiques
5. Le niveau de confiance est affiché pour chaque prédiction

---

#### Requirement 23: Détection d'Anomalies

**User Story:** En tant que manager, je veux être alerté des anomalies détectées par l'IA afin d'investiguer les écarts.

#### Acceptance Criteria

1. L'IA détecte les valeurs KPI anormales
2. L'IA détecte les dépenses inhabituelles
3. L'IA détecte les retards critiques
4. Les anomalies sont affichées dans un tableau de bord dédié
5. L'utilisateur peut marquer l'anomalie comme traitée ou ignorée

---

#### Requirement 24: Assistant IA

**User Story:** En tant qu'utilisateur, je veux poser des questions à l'assistant IA afin d'obtenir des insights sur mes projets.

#### Acceptance Criteria

1. L'assistant répond aux questions en langage naturel
2. L'assistant peut générer des résumés de projet
3. L'assistant peut suggérer des optimisations
4. L'historique des conversations est conservé
5. L'assistant respecte les permissions de l'utilisateur

---

### Module 9 : Sécurité et Audit

#### Requirement 25: Journal d'Audit

**User Story:** En tant qu'administrateur, je veux consulter le journal d'audit afin de tracer toutes les actions effectuées sur la plateforme.

#### Acceptance Criteria

1. Toute action CRUD est journalisée (qui, quoi, quand, détails)
2. Les connexions réussies et échouées sont journalisées
3. Le journal est consultable via interface avec filtres
4. Le journal est exportable pour analyse externe
5. La rétention des logs est configurable (minimum 1 an)

---

#### Requirement 26: Chiffrement des Données

**User Story:** En tant qu'administrateur sécurité, je veux que les données sensibles soient chiffrées afin de protéger les informations.

#### Acceptance Criteria

1. Les mots de passe sont hachés avec bcrypt/argon2
2. Les données sensibles (financières, personnelles) sont chiffrées en base (AES-256)
3. Les communications sont sécurisées via HTTPS/TLS
4. Les sauvegardes sont chiffrées
5. Les clés de chiffrement sont gérées de manière sécurisée

---

#### Requirement 27: Conformité OWASP

**User Story:** En tant que développeur, je veux que l'application respecte l'OWASP Top 10 afin de garantir un niveau de sécurité minimal.

#### Acceptance Criteria

1. Protection contre les injections SQL (ORM, requêtes paramétrées)
2. Protection contre XSS (échappement des sorties)
3. Protection contre CSRF (tokens)
4. Protection contre les attaques de session
5. Validation stricte des entrées utilisateur
6. Gestion sécurisée des erreurs (pas de divulgation d'infos)
7. Headers de sécurité configurés (CSP, X-Frame-Options, etc.)
8. Mise à jour régulière des dépendances

---

## Exigences Non-Fonctionnelles

### Performance

| ID | Exigence | Critère |
|----|----------|---------|
| NF-01 | Temps de réponse API | < 500ms pour 95% des requêtes |
| NF-02 | Capacité utilisateurs | 1000 utilisateurs simultanés |
| NF-03 | Taille base de données | Support jusqu'à 10M enregistrements |
| NF-04 | Temps de génération rapport | < 30 secondes pour un rapport standard |

### Disponibilité

| ID | Exigence | Critère |
|----|----------|---------|
| NF-05 | Uptime | 99.5% de disponibilité |
| NF-06 | RTO (Recovery Time Objective) | < 4 heures |
| NF-07 | RPO (Recovery Point Objective) | < 1 heure |

### Compatibilité

| ID | Exigence | Critère |
|----|----------|---------|
| NF-08 | Navigateurs | Chrome, Firefox, Safari, Edge (2 dernières versions) |
| NF-09 | Mobile | Android 8+, iOS 13+ |
| NF-10 | API | REST JSON, documentation OpenAPI |

### Maintenabilité

| ID | Exigence | Critère |
|----|----------|---------|
| NF-11 | Code | PSR-12, ESLint, tests unitaires > 80% coverage |
| NF-12 | Documentation | API documentée, README, guide déploiement |
| NF-13 | CI/CD | Pipeline automatisé (tests, lint, build, deploy) |

---

## Contraintes

### Techniques

- Backend : Laravel 11.x (PHP 8.2+)
- Frontend : Nuxt.js 3.x (Vue 3)
- Mobile : Flutter 3.x
- Base de données : MySQL 8.x / PostgreSQL 15+
- Cache : Redis
- IA : OpenAI API / Google Gemini API
- Conteneurisation : Docker

### Réglementaires

- RGPD pour les données personnelles (si UE)
- Norme ISO 27001 pour la sécurité
- OWASP Top 10

### Budgétaires

- Utilisation d'API IA payantes (OpenAI/Gemini) avec gestion des coûts

---

## Dépendances

| Dépendance | Description | Impact |
|------------|-------------|--------|
| OpenAI/Gemini API | Moteur IA | Module IA non fonctionnel si indisponible |
| Service SMS | 2FA par SMS | 2FA limité à TOTP si indisponible |
| Service Email | Notifications, reset password | Notifications limitées si indisponible |

---

## Risques

| Risque | Probabilité | Impact | Mitigation |
|--------|-------------|--------|------------|
| Retard développement | Moyenne | Élevé | Priorisation MVP, méthodologie agile |
| Coûts API IA élevés | Moyenne | Moyen | Quotas, cache, modèles locaux alternatifs |
| Problèmes sécurité | Faible | Critique | Audit sécurité, tests de pénétration |
| Adoption utilisateur | Moyenne | Élevé | Formation, UX soignée, support |

---

## Annexes

### Modèle de Données Conceptuel

```
Organisation 1---N Users
Organisation 1---N Projects
Organisation 1---N Programs

Program 1---N Projects
Project 1---N Activities
Activity 1---N Tasks
Project 1---N Milestones
Project 1---N Budgets
Budget 1---N BudgetLines
BudgetLine 1---N Expenses
Project 1---N KPIs
KPI 1---N KPIValues
Project 1---N Reports
Project 1---N FieldData
```

### Stack Technique

| Couche | Technologie |
|--------|-------------|
| Backend | Laravel 11.x |
| Frontend Web | Nuxt.js 3.x |
| Mobile | Flutter 3.x |
| Base de données | MySQL / PostgreSQL |
| Cache | Redis |
| Queue | Redis / Database |
| Storage | Local / S3 |
| IA | OpenAI / Gemini API |
| Déploiement | Docker |

---

*Document généré pour PISE-PP - Version 1.0*
