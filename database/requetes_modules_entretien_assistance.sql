-- Modules Entretien, Assistance, Réparations & Suivi et Carburant & Consommation
-- Version compatible MySQL/MariaDB sans contraintes FOREIGN KEY directes.
-- Cette version évite l'erreur #1215 si les tables existantes n'ont pas
-- exactement le même moteur, charset/collation ou type de colonne id.

CREATE TABLE IF NOT EXISTS entretiens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gestionnaire_de_flotte_id BIGINT UNSIGNED NOT NULL,
    vehicule_id BIGINT UNSIGNED NOT NULL,
    chauffeur_id BIGINT UNSIGNED NULL,
    type_entretien VARCHAR(100) NOT NULL,
    titre VARCHAR(160) NOT NULL,
    description TEXT NULL,
    date_prevue DATE NULL,
    date_realisation DATE NULL,
    kilometrage INT UNSIGNED NULL,
    cout DECIMAL(12, 2) NULL,
    prestataire VARCHAR(160) NULL,
    statut ENUM('planifie', 'en_cours', 'realise', 'annule') NOT NULL DEFAULT 'planifie',
    commentaire TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_entretiens_flotte (gestionnaire_de_flotte_id),
    INDEX idx_entretiens_vehicule (vehicule_id),
    INDEX idx_entretiens_chauffeur (chauffeur_id),
    INDEX idx_entretiens_statut (statut),
    INDEX idx_entretiens_date_prevue (date_prevue),
    INDEX idx_entretiens_flotte_statut (gestionnaire_de_flotte_id, statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assistances (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gestionnaire_de_flotte_id BIGINT UNSIGNED NOT NULL,
    vehicule_id BIGINT UNSIGNED NOT NULL,
    chauffeur_id BIGINT UNSIGNED NULL,
    type_assistance VARCHAR(100) NOT NULL,
    titre VARCHAR(160) NOT NULL,
    description TEXT NULL,
    lieu VARCHAR(190) NULL,
    latitude DECIMAL(10, 7) NULL,
    longitude DECIMAL(10, 7) NULL,
    niveau_urgence ENUM('faible', 'moyen', 'eleve', 'critique') NOT NULL DEFAULT 'moyen',
    prestataire_id BIGINT UNSIGNED NULL,
    prestataire_nom VARCHAR(160) NULL,
    date_demande DATETIME NULL,
    date_intervention DATETIME NULL,
    date_cloture DATETIME NULL,
    statut ENUM('nouvelle', 'affectee', 'en_cours', 'resolue', 'annulee') NOT NULL DEFAULT 'nouvelle',
    commentaire TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_assistances_flotte (gestionnaire_de_flotte_id),
    INDEX idx_assistances_vehicule (vehicule_id),
    INDEX idx_assistances_chauffeur (chauffeur_id),
    INDEX idx_assistances_statut (statut),
    INDEX idx_assistances_urgence (niveau_urgence),
    INDEX idx_assistances_date_demande (date_demande),
    INDEX idx_assistances_flotte_statut (gestionnaire_de_flotte_id, statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reparations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gestionnaire_de_flotte_id BIGINT UNSIGNED NOT NULL,
    vehicule_id BIGINT UNSIGNED NOT NULL,
    chauffeur_id BIGINT UNSIGNED NULL,
    prestataire_id BIGINT UNSIGNED NULL,
    prestataire_nom VARCHAR(160) NULL,
    assistance_id BIGINT UNSIGNED NULL,
    titre VARCHAR(160) NOT NULL,
    description_panne TEXT NULL,
    diagnostic TEXT NULL,
    proforma_reference VARCHAR(100) NULL,
    proforma_montant DECIMAL(12, 2) NULL,
    validation_financiere ENUM('en_attente', 'validee', 'refusee') NOT NULL DEFAULT 'en_attente',
    date_entree DATE NULL,
    date_sortie_prevue DATE NULL,
    date_sortie DATE NULL,
    cout_final DECIMAL(12, 2) NULL,
    statut ENUM('nouveau', 'diagnostic', 'proforma', 'validation', 'reparation', 'termine', 'annule') NOT NULL DEFAULT 'nouveau',
    commentaire TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_reparations_flotte (gestionnaire_de_flotte_id),
    INDEX idx_reparations_vehicule (vehicule_id),
    INDEX idx_reparations_chauffeur (chauffeur_id),
    INDEX idx_reparations_prestataire (prestataire_id),
    INDEX idx_reparations_assistance (assistance_id),
    INDEX idx_reparations_statut (statut),
    INDEX idx_reparations_validation (validation_financiere),
    INDEX idx_reparations_date_entree (date_entree),
    INDEX idx_reparations_flotte_statut (gestionnaire_de_flotte_id, statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS carburants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gestionnaire_de_flotte_id BIGINT UNSIGNED NOT NULL,
    vehicule_id BIGINT UNSIGNED NOT NULL,
    chauffeur_id BIGINT UNSIGNED NULL,
    type_de_carburant_id BIGINT UNSIGNED NOT NULL,
    type_carburant VARCHAR(100) NULL,
    date_approvisionnement DATE NOT NULL,
    kilometrage INT UNSIGNED NULL,
    quantite_litres DECIMAL(10, 2) NOT NULL,
    prix_unitaire DECIMAL(12, 2) NOT NULL,
    montant_total DECIMAL(12, 2) NOT NULL,
    station VARCHAR(160) NULL,
    reference VARCHAR(100) NULL,
    mode_paiement ENUM('espece', 'carte', 'virement', 'mobile_money', 'autre') NOT NULL DEFAULT 'espece',
    commentaire TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_carburants_flotte (gestionnaire_de_flotte_id),
    INDEX idx_carburants_vehicule (vehicule_id),
    INDEX idx_carburants_chauffeur (chauffeur_id),
    INDEX idx_carburants_type (type_de_carburant_id),
    INDEX idx_carburants_date (date_approvisionnement),
    INDEX idx_carburants_flotte_date (gestionnaire_de_flotte_id, date_approvisionnement)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Diagnostic utile si tu veux ajouter de vraies contraintes FOREIGN KEY ensuite :
-- SHOW CREATE TABLE gestionnaire_de_flottes;
-- SHOW CREATE TABLE vehicules;
-- SHOW CREATE TABLE chauffeurs;
-- SHOW CREATE TABLE type_de_carburants;
--
-- Les contraintes FOREIGN KEY ne pourront être ajoutées que si :
-- 1. les tables référencées sont en InnoDB ;
-- 2. les colonnes id référencées sont BIGINT UNSIGNED ;
-- 3. les tables existent déjà avant entretiens/assistances/reparations/carburants ;
-- 4. les colonnes ont un index compatible.


CREATE TABLE IF NOT EXISTS type_entretiens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(160) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uq_type_entretiens_libelle (libelle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS type_assistances (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(160) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uq_type_assistances_libelle (libelle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS type_de_carburants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(160) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uq_type_de_carburants_libelle (libelle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS menu_features (
    `key` VARCHAR(80) PRIMARY KEY,
    libelle VARCHAR(160) NOT NULL,
    groupe VARCHAR(80) NOT NULL,
    ordre INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_menu_features_groupe (groupe),
    INDEX idx_menu_features_ordre (ordre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS chauffeur_menu_features (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chauffeur_id BIGINT UNSIGNED NOT NULL,
    menu_feature_key VARCHAR(80) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    UNIQUE KEY uq_chauffeur_feature (chauffeur_id, menu_feature_key),
    INDEX idx_chauffeur_menu_features_chauffeur (chauffeur_id),
    INDEX idx_chauffeur_menu_features_feature (menu_feature_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS fleet_roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gestionnaire_de_flotte_id BIGINT UNSIGNED NOT NULL,
    libelle VARCHAR(160) NOT NULL,
    description VARCHAR(500) NULL,
    statut TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    UNIQUE KEY uq_fleet_roles_flotte_libelle (gestionnaire_de_flotte_id, libelle),
    INDEX idx_fleet_roles_flotte (gestionnaire_de_flotte_id),
    INDEX idx_fleet_roles_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS fleet_role_menu_features (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fleet_role_id BIGINT UNSIGNED NOT NULL,
    menu_feature_key VARCHAR(80) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    UNIQUE KEY uq_fleet_role_feature (fleet_role_id, menu_feature_key),
    INDEX idx_fleet_role_menu_features_role (fleet_role_id),
    INDEX idx_fleet_role_menu_features_feature (menu_feature_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- À exécuter une seule fois si les colonnes n'existent pas encore.
ALTER TABLE gestionnaire_de_flottes ADD COLUMN parent_gestionnaire_id BIGINT UNSIGNED NULL AFTER id;
ALTER TABLE gestionnaire_de_flottes ADD COLUMN fleet_role_id BIGINT UNSIGNED NULL AFTER parent_gestionnaire_id;
ALTER TABLE gestionnaire_de_flottes ADD COLUMN statut TINYINT(1) NOT NULL DEFAULT 1 AFTER fleet_role_id;
CREATE INDEX idx_gestionnaire_parent ON gestionnaire_de_flottes (parent_gestionnaire_id);
CREATE INDEX idx_gestionnaire_fleet_role ON gestionnaire_de_flottes (fleet_role_id);
CREATE INDEX idx_gestionnaire_statut ON gestionnaire_de_flottes (statut);

-- Si tu avais exécuté l'ancienne version qui ajoutait fleet_role_id sur chauffeurs :
-- DROP INDEX idx_chauffeurs_fleet_role ON chauffeurs;
-- ALTER TABLE chauffeurs DROP COLUMN fleet_role_id;

INSERT INTO type_entretiens (libelle, created_at, updated_at) VALUES
('Vidange', NOW(), NOW()),
('Révision générale', NOW(), NOW()),
('Contrôle technique', NOW(), NOW()),
('Visite technique', NOW(), NOW()),
('Assurance', NOW(), NOW()),
('Pneumatiques', NOW(), NOW()),
('Freinage', NOW(), NOW()),
('Batterie', NOW(), NOW()),
('Climatisation', NOW(), NOW()),
('Nettoyage intérieur / extérieur', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at);

INSERT INTO type_assistances (libelle, created_at, updated_at) VALUES
('Dépannage', NOW(), NOW()),
('Remorquage', NOW(), NOW()),
('Panne mécanique', NOW(), NOW()),
('Panne électrique', NOW(), NOW()),
('Crevaison', NOW(), NOW()),
('Batterie déchargée', NOW(), NOW()),
('Accident', NOW(), NOW()),
('Assistance conducteur', NOW(), NOW()),
('Véhicule immobilisé', NOW(), NOW()),
('Intervention urgente', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at);

INSERT INTO type_de_carburants (libelle, created_at, updated_at) VALUES
('Essence', NOW(), NOW()),
('Diesel', NOW(), NOW()),
('Hybride', NOW(), NOW()),
('Électrique', NOW(), NOW()),
('GPL', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at);

INSERT INTO menu_features (`key`, libelle, groupe, ordre, created_at, updated_at) VALUES
('dashboard', 'Tableau de bord', 'Accueil', 10, NOW(), NOW()),
('vehicules', 'Véhicules', 'Opérations', 20, NOW(), NOW()),
('autodocs', 'Documents auto', 'Opérations', 30, NOW(), NOW()),
('pieces', 'Pièces & accessoires', 'Opérations', 40, NOW(), NOW()),
('entretiens', 'Entretien', 'Services flotte', 50, NOW(), NOW()),
('assistances', 'Assistance', 'Services flotte', 60, NOW(), NOW()),
('reparations', 'Réparations & Suivi', 'Services flotte', 70, NOW(), NOW()),
('carburants', 'Carburant & Conso.', 'Services flotte', 80, NOW(), NOW()),
('alertes', 'Toutes les alertes', 'Alertes', 90, NOW(), NOW()),
('alerte_assurance', 'Assurance', 'Alertes', 100, NOW(), NOW()),
('alerte_vidange', 'Vidange', 'Alertes', 110, NOW(), NOW()),
('alerte_visite', 'Visite technique', 'Alertes', 120, NOW(), NOW()),
('alerte_controle', 'Contrôle technique', 'Alertes', 130, NOW(), NOW()),
('prestataires', 'Prestataires', 'Réseau', 140, NOW(), NOW()),
('concessionnaires', 'Concessionnaires', 'Réseau', 150, NOW(), NOW()),
('offres', 'Offres', 'Réseau', 160, NOW(), NOW()),
('fonctions', 'Fonctions', 'Utilisateurs', 170, NOW(), NOW()),
('roles', 'Rôles & permissions', 'Utilisateurs', 180, NOW(), NOW()),
('admin_users', 'Users admin', 'Utilisateurs', 190, NOW(), NOW()),
('utilisateurs', 'Chauffeurs', 'Utilisateurs', 200, NOW(), NOW()),
('profil', 'Mon profil', 'Paramètres', 210, NOW(), NOW()),
('password', 'Mot de passe', 'Paramètres', 220, NOW(), NOW()),
('documentation', 'Documentation', 'Aide', 230, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    libelle = VALUES(libelle),
    groupe = VALUES(groupe),
    ordre = VALUES(ordre),
    updated_at = VALUES(updated_at);
