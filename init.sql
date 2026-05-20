-- ========================================================= 
-- MPD - Application de planification et suivi des campagnes 
-- SGBD : PostgreSQL 15 
-- ========================================================= 
-- ========================================================= 
-- TABLE : utilisateur 
-- ========================================================= 
CREATE TABLE utilisateur ( 
    id_utilisateur INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY, 
    email VARCHAR(180) NOT NULL UNIQUE, 
    password_hash VARCHAR(255) NOT NULL, 
    nom VARCHAR(100) NOT NULL, 
    prenom VARCHAR(100) NOT NULL, 
    fonction VARCHAR(100), 
    disponibilite BOOLEAN, 
    telephone VARCHAR(20), 
    roles JSONB NOT NULL, 
    CONSTRAINT ck_utilisateur_roles_array 
        CHECK (jsonb_typeof(roles) = 'array') 
); 
 -- ========================================================= 
 -- TABLE : habilitation 
 -- ========================================================= 
CREATE TABLE habilitation ( 
    id_habilitation INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY, 
    libelle VARCHAR(150) NOT NULL, 
    description TEXT 
); 
 -- ========================================================= 
 -- TABLE : utilisateur_habilitation 
 -- ========================================================= 
CREATE TABLE utilisateur_habilitation ( 
    id_utilisateur_habilitation INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY, 
    id_utilisateur INTEGER NOT NULL, 
    id_habilitation INTEGER NOT NULL, 
    date_obtention TIMESTAMP WITHOUT TIME ZONE, 
    date_expiration TIMESTAMP WITHOUT TIME ZONE, 
    actif BOOLEAN NOT NULL DEFAULT TRUE, 
 
    CONSTRAINT uq_utilisateur_habilitation 
        UNIQUE (id_utilisateur, id_habilitation), 
 
    CONSTRAINT fk_uh_utilisateur 
        FOREIGN KEY (id_utilisateur) 
        REFERENCES utilisateur(id_utilisateur) 
        ON UPDATE CASCADE 
        ON DELETE CASCADE, 
 
    CONSTRAINT fk_uh_habilitation 
        FOREIGN KEY (id_habilitation) 
        REFERENCES habilitation(id_habilitation) 
        ON UPDATE CASCADE 
        ON DELETE CASCADE 
); 
 -- ========================================================= 
 -- TABLE : campagne_validation 
 -- ========================================================= 
CREATE TABLE campagne_validation ( 
    id_campagne INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY, 
    reference_campagne VARCHAR(100) NOT NULL UNIQUE, 
    titre VARCHAR(100) NOT NULL, 
    description TEXT, 
    statut VARCHAR(50) NOT NULL, 
    priorite VARCHAR(20), 
    date_debut_prevue TIMESTAMP WITHOUT TIME ZONE NOT NULL, 
    date_fin_prevue TIMESTAMP WITHOUT TIME ZONE NOT NULL, 
    date_debut_reelle TIMESTAMP WITHOUT TIME ZONE, 
    date_fin_reelle TIMESTAMP WITHOUT TIME ZONE, 
    commentaire_global TEXT, 
    date_creation TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT 
CURRENT_TIMESTAMP, 
    date_modification TIMESTAMP WITHOUT TIME ZONE, 
    id_responsable INTEGER NOT NULL, 
 
    CONSTRAINT fk_campagne_responsable 
        FOREIGN KEY (id_responsable) 
        REFERENCES utilisateur(id_utilisateur) 
        ON UPDATE CASCADE 
        ON DELETE RESTRICT, 
 
    CONSTRAINT ck_campagne_statut 
        CHECK (statut IN ('brouillon', 'planifiee', 'en_cours', 'terminee', 'annulee')), 
 
    CONSTRAINT ck_campagne_priorite 
        CHECK (priorite IS NULL OR priorite IN ('basse', 'moyenne', 'haute', 'critique')), 
 
    CONSTRAINT ck_campagne_dates_prevues 
        CHECK (date_fin_prevue >= date_debut_prevue), 
 
    CONSTRAINT ck_campagne_dates_reelles 
        CHECK ( 
            date_debut_reelle IS NULL 
            OR date_fin_reelle IS NULL 
            OR date_fin_reelle >= date_debut_reelle 
        ) 
); 
 -- ========================================================= 
 -- TABLE : type_test 
 -- ========================================================= 
CREATE TABLE type_test ( 
    id_type_test INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY, 
    libelle VARCHAR(255) NOT NULL, 
    description TEXT 
); 
 -- ========================================================= 
 -- TABLE : campagne_type_test 
 -- ========================================================= 
CREATE TABLE campagne_type_test ( 
    id_campagne_type_test INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY, 
    id_campagne INTEGER NOT NULL, 
    id_type_test INTEGER NOT NULL, 
 
    CONSTRAINT uq_campagne_type_test 
        UNIQUE (id_campagne, id_type_test), 
 
    CONSTRAINT fk_ctt_campagne 
        FOREIGN KEY (id_campagne) 
        REFERENCES campagne_validation(id_campagne) 
        ON UPDATE CASCADE 
        ON DELETE CASCADE, 
 
    CONSTRAINT fk_ctt_type_test 
        FOREIGN KEY (id_type_test) 
        REFERENCES type_test(id_type_test) 
        ON UPDATE CASCADE 
        ON DELETE CASCADE 
); 
 -- ========================================================= 
 -- TABLE : tache 
 -- ========================================================= 
CREATE TABLE tache ( 
    id_tache INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY, 
    titre VARCHAR(255) NOT NULL, 
    description TEXT, 
    statut VARCHAR(50) NOT NULL, 
    priorite VARCHAR(50), 
    date_debut TIMESTAMP WITHOUT TIME ZONE, 
    date_echeance TIMESTAMP WITHOUT TIME ZONE, 
    date_fin TIMESTAMP WITHOUT TIME ZONE, 
    date_creation TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT 
CURRENT_TIMESTAMP, 
    date_modification TIMESTAMP WITHOUT TIME ZONE, 
    id_campagne INTEGER, 
    id_assigne_a INTEGER, 
    id_createur INTEGER NOT NULL, 
 
    CONSTRAINT fk_tache_campagne 
        FOREIGN KEY (id_campagne) 
        REFERENCES campagne_validation(id_campagne) 
        ON UPDATE CASCADE 
        ON DELETE SET NULL, 
 
    CONSTRAINT fk_tache_assigne 
        FOREIGN KEY (id_assigne_a) 
        REFERENCES utilisateur(id_utilisateur) 
        ON UPDATE CASCADE 
        ON DELETE SET NULL, 
 
    CONSTRAINT fk_tache_createur 
        FOREIGN KEY (id_createur) 
        REFERENCES utilisateur(id_utilisateur) 
        ON UPDATE CASCADE 
        ON DELETE RESTRICT, 
 
    CONSTRAINT ck_tache_statut 
        CHECK (statut IN ('a_faire', 'en_cours', 'terminee', 'bloquee')), 
 
    CONSTRAINT ck_tache_priorite 
        CHECK (priorite IS NULL OR priorite IN ('basse', 'moyenne', 'haute', 'critique')), 
 
    CONSTRAINT ck_tache_dates 
        CHECK ( 
            (date_debut IS NULL OR date_fin IS NULL OR date_fin >= date_debut) 
            AND 
            (date_echeance IS NULL OR date_debut IS NULL OR date_echeance >= 
date_debut) 
        ) 
); 
 -- ========================================================= 
 -- TABLE : commentaire 
 -- ========================================================= 
CREATE TABLE commentaire ( 
    id_commentaire INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY, 
    contenu TEXT NOT NULL, 
    date_creation TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT 
CURRENT_TIMESTAMP, 
    date_modification TIMESTAMP WITHOUT TIME ZONE, 
    modere BOOLEAN NOT NULL DEFAULT FALSE, 
    id_auteur INTEGER NOT NULL, 
    id_campagne INTEGER, 
    id_tache INTEGER, 
 
    CONSTRAINT fk_commentaire_auteur 
        FOREIGN KEY (id_auteur) 
        REFERENCES utilisateur(id_utilisateur) 
        ON UPDATE CASCADE 
        ON DELETE RESTRICT, 
 
    CONSTRAINT fk_commentaire_campagne 
        FOREIGN KEY (id_campagne) 
        REFERENCES campagne_validation(id_campagne) 
        ON UPDATE CASCADE 
        ON DELETE CASCADE, 
 
    CONSTRAINT fk_commentaire_tache 
        FOREIGN KEY (id_tache) 
        REFERENCES tache(id_tache) 
        ON UPDATE CASCADE 
        ON DELETE CASCADE, 
 
    CONSTRAINT ck_commentaire_cible 
        CHECK ( 
            (id_campagne IS NOT NULL AND id_tache IS NULL) 
            OR 
            (id_campagne IS NULL AND id_tache IS NOT NULL) 
        ) 
); 
 -- ========================================================= 
 -- TABLE : notification 
 -- ========================================================= 
CREATE TABLE notification ( 
    id_notification INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY, 
    type_notification VARCHAR(50) NOT NULL, 
    titre VARCHAR(255) NOT NULL, 
    message TEXT NOT NULL, 
    canal VARCHAR(20) NOT NULL, 
    est_lue BOOLEAN NOT NULL DEFAULT FALSE, 
    date_envoi TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT 
CURRENT_TIMESTAMP, 
    date_lecture TIMESTAMP WITHOUT TIME ZONE, 
    id_utilisateur INTEGER NOT NULL, 
    id_tache INTEGER, 
    id_campagne INTEGER, 
 
    CONSTRAINT fk_notification_utilisateur 
        FOREIGN KEY (id_utilisateur) 
        REFERENCES utilisateur(id_utilisateur) 
        ON UPDATE CASCADE 
        ON DELETE CASCADE, 
 
    CONSTRAINT fk_notification_tache 
        FOREIGN KEY (id_tache) 
        REFERENCES tache(id_tache) 
        ON UPDATE CASCADE 
        ON DELETE SET NULL, 
 
    CONSTRAINT fk_notification_campagne 
        FOREIGN KEY (id_campagne) 
        REFERENCES campagne_validation(id_campagne) 
        ON UPDATE CASCADE 
        ON DELETE SET NULL,  
 
    CONSTRAINT ck_notification_canal 
        CHECK (canal IN ('app', 'email')) 
); 
 -- ========================================================= 
 -- INDEXES 
 -- ========================================================= 
 
CREATE INDEX idx_campagne_responsable 
    ON campagne_validation(id_responsable); 
 
CREATE INDEX idx_tache_campagne 
    ON tache(id_campagne); 
 
CREATE INDEX idx_tache_assigne_a 
    ON tache(id_assigne_a); 
 
CREATE INDEX idx_tache_createur 
    ON tache(id_createur); 
 
CREATE INDEX idx_commentaire_auteur 
    ON commentaire(id_auteur); 
 
CREATE INDEX idx_commentaire_campagne 
    ON commentaire(id_campagne); 
 
CREATE INDEX idx_commentaire_tache 
    ON commentaire(id_tache); 
 
CREATE INDEX idx_notification_utilisateur 
    ON notification(id_utilisateur); 
CREATE INDEX idx_notification_tache 
ON notification(id_tache); 
CREATE INDEX idx_notification_campagne 
ON notification(id_campagne); 
CREATE INDEX idx_uh_utilisateur 
ON utilisateur_habilitation(id_utilisateur); 
CREATE INDEX idx_uh_habilitation 
ON utilisateur_habilitation(id_habilitation); 
CREATE INDEX idx_ctt_campagne 
ON campagne_type_test(id_campagne); 
CREATE INDEX idx_ctt_type_test 
ON campagne_type_test(id_type_test);