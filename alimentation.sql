USE Portfolio;
GO


-- Désactiver IDENTITY_INSERT pour toutes les tables susceptibles de l'avoir encore activé
SET IDENTITY_INSERT projet OFF;
SET IDENTITY_INSERT utilisateur OFF;
SET IDENTITY_INSERT formation OFF;
SET IDENTITY_INSERT experience OFF;
SET IDENTITY_INSERT competence OFF;


SET IDENTITY_INSERT projet ON;
INSERT INTO projet (id_projet, id_utilisateur, titre, description, date_debut, date_fin, technologie_utilisee, lien_code)
VALUES 
(1, 1, 'Portfolio Web Personnel',
 'D�veloppement d�un site portfolio dynamique.',
 '2025-01-01', NULL,
 'HTML, CSS, JS, SQL Server', 'https://github.com/monportfolio.com'),
 (2,2, 'Analyse des Naissances en France', 
 'Cr�ation de dashboards Power BI bas�s sur des donn�es INSEE. Int�gration sous Excel et nettoyage Python.', '2024-12-15',null,'Python, Power BI, Excel','https://github.com/monportfolio');
SET IDENTITY_INSERT projet OFF;

SET IDENTITY_INSERT utilisateur ON;
INSERT INTO utilisateur (id_utilisateur, nom, prenom, email, description)
VALUES (1, 'El Gazi', 'Yamine', 'yamine@example.com',
        '�tudiant en BUT Science des Donn�es, orient� Data Engineering et BI.'),
       (2, 'BERBACHE', 'Adil', 'adil.berbache@example.com','�tudiant en BUT Science des Donn�es, sp�cialis� BI, DataViz et int�gration de donn�es.');
SET IDENTITY_INSERT utilisateur OFF;

SET IDENTITY_INSERT formation ON;
INSERT INTO formation (id_formation, id_utilisateur, diplome, ecole, description, date_debut, date_fin)
VALUES 
(1, 1, 'BUT Science des Donn�es', 'IUT de Lille',
 'Formation orient�e statistiques, data engineering, BI et programmation.',
 '2022-09-01', NULL),
 (2,2, 'Baccalaur�at G�n�ral', 'Lyc�e Faidherbe','Sp�cialit�s Maths et SES', '2020-09-01', '2023-06-30');
SET IDENTITY_INSERT formation OFF;



SET IDENTITY_INSERT experience ON;
INSERT INTO experience (id_experience, id_utilisateur, id_projet, titre, entreprise, description, date_debut, date_fin)
VALUES 
(1, 1, 1,
 'Stagiaire Data Analyst', 'CHU Lille',
 'Analyse de donn�es hospitali�res, cr�ation de tableaux de bord BI.',
 '2023-12-01', '2023-12-31'),
 (2,2,1, 'Projet �tudiant � Data Cleaning', 'IUT de Lille', 'Nettoyage et transformation d�un jeu de donn�es sur Python et SQL Server.','2024-03-01', '2024-04-30');
SET IDENTITY_INSERT experience OFF;

SET IDENTITY_INSERT competence ON;
INSERT INTO competence (id_competence, nom_competence, niveau)
VALUES 
(1, 'SQL Server', 'Avanc�'),
(2, 'Python', 'Interm�diaire'),
(3, 'Power BI', 'Interm�diaire'),
(4, 'HTML/CSS', 'Interm�diaire'),
(5, 'JavaScript', 'Interm�diaire');
SET IDENTITY_INSERT competence OFF;

INSERT INTO projet_competence (id_projet, id_competence)
VALUES 
(1, 1),
(1, 2),
(1, 3);
