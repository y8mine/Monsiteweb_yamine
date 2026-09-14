-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 10 fév. 2026 à 15:43
-- Version du serveur : 10.4.24-MariaDB
-- Version de PHP : 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `portfolio`
--

-- --------------------------------------------------------

--
-- Structure de la table `competence`
--

CREATE TABLE `competence` (
  `id_competence` int(11) NOT NULL,
  `nom_competence` varchar(100) NOT NULL,
  `niveau` varchar(50) DEFAULT NULL,
  `categorie` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `competence`
--

INSERT INTO `competence` (`id_competence`, `nom_competence`, `niveau`, `categorie`) VALUES
(2, 'Python (Pandas, Numpy, Scikit-learn)', 'Avancé', 'Programmation'),
(3, 'R (Tidyverse, Shiny)', 'Avancé', 'Programmation'),
(4, 'SQL (PostgreSQL, Oracle)', 'Avancé', 'Bases de données'),
(5, 'Statistiques Descriptives & Inférentielles', 'Avancé', 'Data Science'),
(6, 'Machine Learning (Supervisé & Non-supervisé)', 'Intermédiaire', 'Data Science'),
(7, 'Séries Temporelles', 'Intermédiaire', 'Data Science'),
(8, 'Tableau Software / Power BI', 'Avancé', 'Data Visualization'),
(9, 'Communication et Reporting', 'Avancé', 'Soft Skills'),
(10, 'Anglais Technique', 'Intermédiaire', 'Soft Skills'),
(11, 'Deep Learning (TensorFlow/Keras)', 'Intermédiaire', 'Data Science'),
(12, 'Séries Temporelles (Prophet/Arima)', 'Avancé', 'Data Science'),
(13, 'Géomatique & Cartographie (GIS)', 'Intermédiaire', 'Visualisation'),
(14, 'Big Data (Spark/Hadoop)', 'Notions', 'Data Engineering'),
(15, 'Web Scraping (BeautifulSoup/Selenium)', 'Avancé', 'Programmation'),
(16, 'anglais', 'Intermédiaire', 'Bases de données');

-- --------------------------------------------------------

--
-- Structure de la table `experience`
--

CREATE TABLE `experience` (
  `id_experience` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `id_projet` int(11) DEFAULT NULL,
  `titre` varchar(100) NOT NULL,
  `entreprise` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `experience`
--

INSERT INTO `experience` (`id_experience`, `id_utilisateur`, `id_projet`, `titre`, `entreprise`, `description`, `date_debut`, `date_fin`, `logo`) VALUES
(1, 1, 5, 'Data Analyst (Etude des établissements Aide Sociale à Enfance)', 'Département du Nord', 'Service Pilotage et Qualité de la Donnée – Département du\r\nNord (Avril – Juin 2025);\r\n\r\nÉtude approfondie des données activité des établissements de\r\nAide Sociale à Enfance pour identifier des tendances clés;\r\n\r\nExploitation et migration des données vers une source unifiée (SI\r\nDépartemental) pour améliorer la gestion et la centralisation des\r\ninformations;\r\n\r\nAutomatisation du pilotage des activités via Qlik Sense, garantissant\r\nune meilleure qualité et fiabilité des données;\r\n\r\nIdentification des tendances et des facteurs clés pour anticiper les\r\nbesoins et optimiser les dispositifs d’accueil;\r\n\r\nConception de tableaux de bord interactifs et de visualisations\r\navancées facilitant la prise de décision;', '2025-04-04', '2025-06-16', 'uploads/logos/1770667147_logo_nord4-64915371a6f82.png');

-- --------------------------------------------------------

--
-- Structure de la table `formation`
--

CREATE TABLE `formation` (
  `id_formation` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `diplome` varchar(100) NOT NULL,
  `ecole` varchar(100) NOT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `fichier` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `formation`
--

INSERT INTO `formation` (`id_formation`, `id_utilisateur`, `diplome`, `ecole`, `ville`, `description`, `date_debut`, `date_fin`, `fichier`, `logo`) VALUES
(2, 1, 'Bachelor Universitaire Technologique en Science des Données', 'IUT de Lille - Site de Roubaix', 'Roubaix', 'Parcours Visualisations et Conceptions Outils Décisionnelles. \r\n\r\nApprentissage du cycle de vie de la donnée : collecte, nettoyage, analyse et visualisation.', '2022-09-01', NULL, 'uploads/formations/1770662396_Sans titre.png', 'uploads/1770667660_Sans titre.png');

-- --------------------------------------------------------

--
-- Structure de la table `projet`
--

CREATE TABLE `projet` (
  `id_projet` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `technologie_utilisee` text DEFAULT NULL,
  `lien_code` varchar(255) DEFAULT NULL,
  `lien_demo` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `document` varchar(255) DEFAULT NULL,
  `categorie_projet` varchar(50) DEFAULT 'Data Science',
  `contexte` varchar(100) DEFAULT 'Projet Universitaire (IUT)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `projet`
--

INSERT INTO `projet` (`id_projet`, `id_utilisateur`, `titre`, `description`, `date_debut`, `date_fin`, `technologie_utilisee`, `lien_code`, `lien_demo`, `image`, `img`, `document`, `categorie_projet`, `contexte`) VALUES
(1, 1, 'dvf_immobilier', 'on verra plus tard', '2025-09-02', '2026-02-12', 'SQL SERVER PYTHON RSTUDIO SSIS', '', '', 'uploads/projets/1770322863_img_Capture d\'écran 2026-01-21 224452.png', NULL, 'uploads/projets/1770322863_doc_LettreMotivation_Yamine_ELGAZI.pdf', 'Data Science', 'Projet Universitaire (IUT)'),
(4, 1, 'Analyse Multivariée sur données réelles', 'Étude statistique complète (ACP, AFC, Classification) pour identifier des comportements types dans un jeu de données de consommation.', '2023-11-01', '2023-12-15', 'R, FactoMineR', '', '', 'uploads/projets/1770671007_img_Capture d\'écran 2026-02-09 220122.png', NULL, NULL, 'Data Science', 'Projet Universitaire (IUT)'),
(5, 1, 'Tableau de bord de suivi d&#039;activité', 'Conception et réalisation d&#039;un dashboard interactif permettant de visualiser les KPIs d&#039;une entreprise de logistique.', '2024-02-01', '2024-03-20', 'Power BI, SQL', '', '', 'uploads/projets/1770670996_img_Capture d\'écran 2026-02-09 220115.png', NULL, NULL, 'Data Science', 'Projet Universitaire (IUT)'),
(6, 1, 'Modèle de prédiction (Machine Learning)', 'Développement d&#039;un algorithme de classification pour prédire le départ des clients (Churn) d&#039;un service bancaire.', '2024-04-15', '2024-05-30', 'Python, Scikit-learn', '', '', 'uploads/projets/1770670985_img_Capture d\'écran 2026-02-09 220108.png', NULL, NULL, 'Data Science', 'Projet Universitaire (IUT)'),
(7, 1, 'Détection de Fraude par Deep Learning', 'Mise en place d\'un réseau de neurones artificiels pour détecter les transactions bancaires frauduleuses. Comparaison avec un modèle Random Forest. Gestion du déséquilibre de classes (SMOTE).', '2024-11-01', '2025-01-15', 'Python, TensorFlow, Keras, Pandas', NULL, NULL, 'https://images.unsplash.com/photo-1555949963-aa79dcee981c?q=80&w=2070&auto=format&fit=crop', NULL, NULL, 'Data Science', 'SAÉ - IUT de Lille'),
(8, 1, 'Prévision de Consommation Énergétique', 'Analyse de séries temporelles sur la consommation électrique. Décomposition de la saisonnalité et prévision à J+7 avec l\'algorithme SARIMA et Facebook Prophet.', '2024-09-01', '2024-10-30', 'R, Prophet, TimeSeries', NULL, NULL, 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop', NULL, NULL, 'Data Science', 'Projet Universitaire (IUT)'),
(9, 1, 'Cartographie Interactive des Déserts Médicaux', 'Collecte de données Open Data et croisement avec la densité de population. Création d\'une carte interactive pour identifier les zones prioritaires d\'installation.', '2024-05-01', '2024-06-20', 'Python, Folium, GeoPandas', NULL, NULL, 'https://images.unsplash.com/photo-1524661135-423995f22d0b?q=80&w=2074&auto=format&fit=crop', NULL, NULL, 'Dataviz', 'Challenge Open Data');

-- --------------------------------------------------------

--
-- Structure de la table `projet_competence`
--

CREATE TABLE `projet_competence` (
  `id_projet` int(11) NOT NULL,
  `id_competence` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `projet_competence`
--

INSERT INTO `projet_competence` (`id_projet`, `id_competence`) VALUES
(7, 11),
(8, 12),
(9, 13);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `cv_pdf` varchar(255) DEFAULT NULL,
  `url_linkedin` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `prenom`, `email`, `telephone`, `description`, `avatar`, `cv_pdf`, `url_linkedin`, `password`) VALUES
(1, 'EL GAZI', 'Yamine', 'yamine.elgazi.etu@univ-lille.fr', '0766603630', 'Data Scientist junior issu du Bachelor Universitaire Technologique en Science des Données (Parcours Visualisations et Conceptions Outils Décisionnelles). Spécialisé dans la collecte, le traitement statistique et le déploiement de modèles prédictifs pour aider à la décision.', 'uploads/users/avatar_1770321258.jpg', 'uploads/docs/698260e560a70.pdf', 'https://www.linkedin.com/in/yamine-el-gazi-7b6472267', '$2y$10$jOhnYxioPqMRUqxFgbZsjOsntVjtffztBc0vWdNyV/GlAYITtNIdq');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `competence`
--
ALTER TABLE `competence`
  ADD PRIMARY KEY (`id_competence`);

--
-- Index pour la table `experience`
--
ALTER TABLE `experience`
  ADD PRIMARY KEY (`id_experience`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_projet` (`id_projet`);

--
-- Index pour la table `formation`
--
ALTER TABLE `formation`
  ADD PRIMARY KEY (`id_formation`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `projet`
--
ALTER TABLE `projet`
  ADD PRIMARY KEY (`id_projet`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `projet_competence`
--
ALTER TABLE `projet_competence`
  ADD PRIMARY KEY (`id_projet`,`id_competence`),
  ADD KEY `id_competence` (`id_competence`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `competence`
--
ALTER TABLE `competence`
  MODIFY `id_competence` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `experience`
--
ALTER TABLE `experience`
  MODIFY `id_experience` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `formation`
--
ALTER TABLE `formation`
  MODIFY `id_formation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `projet`
--
ALTER TABLE `projet`
  MODIFY `id_projet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `experience`
--
ALTER TABLE `experience`
  ADD CONSTRAINT `experience_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `experience_ibfk_2` FOREIGN KEY (`id_projet`) REFERENCES `projet` (`id_projet`);

--
-- Contraintes pour la table `formation`
--
ALTER TABLE `formation`
  ADD CONSTRAINT `formation_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`);

--
-- Contraintes pour la table `projet`
--
ALTER TABLE `projet`
  ADD CONSTRAINT `projet_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`);

--
-- Contraintes pour la table `projet_competence`
--
ALTER TABLE `projet_competence`
  ADD CONSTRAINT `projet_competence_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `projet` (`id_projet`),
  ADD CONSTRAINT `projet_competence_ibfk_2` FOREIGN KEY (`id_competence`) REFERENCES `competence` (`id_competence`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
