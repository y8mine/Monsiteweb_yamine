-- 1. CRÉATION DE LA BASE DE DONNÉES (Si elle n'existe pas)
CREATE DATABASE IF NOT EXISTS portfolio;
USE portfolio;

-- 2. TABLE UTILISATEUR (Avec les champs Avatar et CV)
CREATE TABLE IF NOT EXISTS utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    telephone VARCHAR(20),
    description TEXT,
    avatar VARCHAR(255) DEFAULT NULL, -- Pour la photo de profil
    cv_pdf VARCHAR(255) DEFAULT NULL  -- Pour le fichier PDF du CV
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. TABLE FORMATION (Avec champ fichier joint)
CREATE TABLE IF NOT EXISTS formation (
    id_formation INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    diplome VARCHAR(100) NOT NULL,
    ecole VARCHAR(100) NOT NULL,
    ville VARCHAR(100),
    description TEXT,
    date_debut DATE,
    date_fin DATE,
    fichier VARCHAR(255) DEFAULT NULL, -- Pour un diplôme scanné par exemple
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. TABLE PROJET (Avec image de couverture)
CREATE TABLE IF NOT EXISTS projet (
    id_projet INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    titre VARCHAR(100) NOT NULL,
    description TEXT,
    date_debut DATE,
    date_fin DATE,
    technologie_utilisee TEXT,
    lien_code VARCHAR(255),
    lien_demo VARCHAR(255),
    image VARCHAR(255) DEFAULT NULL, -- Pour l'image de couverture du projet
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. TABLE EXPÉRIENCE (Avec logo entreprise)
CREATE TABLE IF NOT EXISTS experience (
    id_experience INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    id_projet INT NULL,
    titre VARCHAR(100) NOT NULL,
    entreprise VARCHAR(100) NOT NULL,
    description TEXT,
    date_debut DATE,
    date_fin DATE,
    logo VARCHAR(255) DEFAULT NULL, -- Pour le logo de l'entreprise
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur),
    FOREIGN KEY (id_projet) REFERENCES projet(id_projet)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. TABLE COMPÉTENCE
CREATE TABLE IF NOT EXISTS competence (
    id_competence INT AUTO_INCREMENT PRIMARY KEY,
    nom_competence VARCHAR(100) NOT NULL,
    niveau VARCHAR(50),
    categorie VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. TABLE DE LIAISON (Projet <-> Compétence)
CREATE TABLE IF NOT EXISTS projet_competence (
    id_projet INT,
    id_competence INT,
    PRIMARY KEY (id_projet, id_competence),
    FOREIGN KEY (id_projet) REFERENCES projet(id_projet),
    FOREIGN KEY (id_competence) REFERENCES competence(id_competence)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. DONNÉES PAR DÉFAUT (Pour ne pas avoir un site vide au début)
-- On vérifie d'abord si la table est vide pour ne pas créer de doublons
INSERT INTO utilisateur (id_utilisateur, nom, prenom, email, telephone, description)
SELECT 1, 'NomParDefaut', 'PrenomParDefaut', 'email@exemple.com', '0600000000', 'Bonjour, ceci est la description par défaut. Allez dans l''admin pour la modifier.'
WHERE NOT EXISTS (SELECT 1 FROM utilisateur WHERE id_utilisateur = 1);


-- Ajouter une photo de profil et un CV à l'utilisateur
ALTER TABLE utilisateur ADD COLUMN avatar VARCHAR(255) DEFAULT NULL;
ALTER TABLE utilisateur ADD COLUMN cv_pdf VARCHAR(255) DEFAULT NULL;

-- Ajouter une image de couverture aux projets
ALTER TABLE projet ADD COLUMN image VARCHAR(255) DEFAULT NULL;

-- Ajouter un logo aux expériences
ALTER TABLE experience ADD COLUMN logo VARCHAR(255) DEFAULT NULL;

-- Ajouter un logo/document aux formations
ALTER TABLE formation ADD COLUMN fichier VARCHAR(255) DEFAULT NULL;



ALTER TABLE utilisateur ADD COLUMN url_linkedin VARCHAR(255) DEFAULT NULL;



-- sécurité 
ALTER TABLE utilisateur ADD COLUMN password VARCHAR(255) DEFAULT NULL;


-- Ajout de la colonne pour l'image de couverture
ALTER TABLE projet ADD COLUMN img VARCHAR(255) DEFAULT NULL;

-- Ajout de la colonne pour le fichier joint (PDF, Zip, etc.)
ALTER TABLE projet ADD COLUMN document VARCHAR(255) DEFAULT NULL;