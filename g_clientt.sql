-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 23 juin 2023 à 09:56
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `g_client`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE `articles` (
  `id_produit` int(11) NOT NULL,
  `nom_produit` varchar(255) DEFAULT NULL,
  `prix_uni` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`id_produit`, `nom_produit`, `prix_uni`) VALUES
(1, 'Huile d\'Argan', 25.99),
(2, 'Huile de Graines de Cactus', 19.99),
(3, 'Safran', 9.99),
(4, 'Couverture Handira', 49.99),
(5, 'Chaussons Babouche', 12.99),
(6, 'Pot Tagine', 39.99),
(7, 'Tapis Berbère', 79.99),
(8, 'Chapeau Fez', 14.99),
(9, 'Ensemble Thé à la Menthe', 29.99),
(10, 'Lanterne Marocaine', 34.99);

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `telephone` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `adresse` varchar(45) DEFAULT NULL,
  `ville` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`id`, `name`, `telephone`, `email`, `adresse`, `ville`) VALUES
(6, 'Kaoutar Bahan', '+212689813791', 'kaoutarbahan@gmail.com', 'Drarga Agadir', 6),
(7, 'Anass Ouzaouit', '0654431414', 'anassanasszakaria@gmail.com', 'Drarga Agadir', 6);

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `id_commande` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `observation` varchar(255) DEFAULT NULL,
  `total_prix` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'en cours'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id_commande`, `client_id`, `date`, `observation`, `total_prix`, `status`) VALUES
(39, 6, '2023-06-22', 'test', 1675.00, 'en cours'),
(40, 7, '2022-05-05', 'test', 1300.00, 'en cours');

-- --------------------------------------------------------

--
-- Structure de la table `details`
--

CREATE TABLE `details` (
  `id_commande` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `nom_produit` varchar(255) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` int(11) NOT NULL,
  `total` int(11) GENERATED ALWAYS AS (`prix_unitaire` * `quantite`) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `details`
--

INSERT INTO `details` (`id_commande`, `id_produit`, `nom_produit`, `quantite`, `prix_unitaire`) VALUES
(39, 8, 'Chapeau Fez', 100, 15),
(39, 10, 'Lanterne Marocaine', 5, 35),
(40, 5, 'Chaussons Babouche', 100, 13);

--
-- Déclencheurs `details`
--
DELIMITER $$
CREATE TRIGGER `update_total_prix_trigger` AFTER INSERT ON `details` FOR EACH ROW BEGIN
    UPDATE commande
    SET total_prix = (
        SELECT SUM(total)
        FROM details
        WHERE details.id_commande = NEW.id_commande
    )
    WHERE id_commande = NEW.id_commande;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `age` varchar(255) DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `salt`, `name`, `age`, `localisation`) VALUES
(7, 'Kaoutar', 'kaoutarbahan@gmail.com', '$2y$10$gKMFGL6V9A4W1KSTWLWyvOG1A.jSvGxXZk9lWm2mbO5NWa5J24j7O', '68f9c2213a57a06be17b60d0bb795bfe', 'Kaoutar Bahan', '20', 'drarga agadir'),
(9, 'anvss', 'anassanasszakaria@gmail.com', '$2y$10$OmAkjfE0eLQPX2LL0IhnXOuKRuMP4mt7l7fqtvyB4K2Qi6kVcxZ6y', 'd3c11c2eb6a79738bd663c4fbfbe4733', NULL, NULL, NULL),
(24, '888fwe', 'ewew@gmail.com', '$2y$10$XCFvvRtIaoQagzz9jN5trO3jaA9QGCDPYN74SrCpcE8WSk8mkEqi.', 'd0e16a78db5b8c9349f314a975466634', NULL, NULL, NULL),
(25, '888fwee3', 'swqsws@gmail.com', '$2y$10$SVgX26dKsnpK/ayX5DP4..tJoRrOjbsLDF66Uwpm1WcUrBvuujIUm', 'a88ffd51d17d4b5a8656820f6e0eea02', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `ville`
--

CREATE TABLE `ville` (
  `id_ville` int(11) NOT NULL,
  `nom_ville` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ville`
--

INSERT INTO `ville` (`id_ville`, `nom_ville`) VALUES
(1, 'Casablanca'),
(2, 'Rabat'),
(3, 'Fes'),
(4, 'Marrakech'),
(5, 'Tangier'),
(6, 'Agadir'),
(7, 'Meknes'),
(8, 'Oujda'),
(9, 'Kenitra'),
(10, 'Tetouan'),
(11, 'Salé'),
(12, 'Temara'),
(13, 'Nador'),
(14, 'Khouribga'),
(15, 'Ouarzazate');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id_produit`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ville` (`ville`);

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `commande_ibfk_1` (`client_id`);

--
-- Index pour la table `details`
--
ALTER TABLE `details`
  ADD PRIMARY KEY (`id_commande`,`id_produit`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `prix_uni` (`prix_unitaire`),
  ADD KEY `nom_produit` (`nom_produit`) USING BTREE;

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_username` (`username`);

--
-- Index pour la table `ville`
--
ALTER TABLE `ville`
  ADD PRIMARY KEY (`id_ville`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `ville` FOREIGN KEY (`ville`) REFERENCES `ville` (`id_ville`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `details`
--
ALTER TABLE `details`
  ADD CONSTRAINT `details_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`) ON DELETE CASCADE,
  ADD CONSTRAINT `details_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `articles` (`id_produit`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
