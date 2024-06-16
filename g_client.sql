-- Déclencheurs `details`

--

DELIMITER $$

CREATE TRIGGER `UPDATE_TOTAL_PRIX_TRIGGER` AFTER INSERT 
ON `DETAILS` FOR EACH ROW BEGIN 
	UPDATE commande
	SET total_prix = (
	        SELECT SUM(total)
	        FROM details
	        WHERE
	            details.id_commande = NEW.id_commande
	    )
	WHERE
	    id_commande = NEW.id_commande;
	END 
$ 

$ DELIMITER ;

CREATE TABLE
    `details` (
        `id_commande` int(11) NOT NULL,
        `id_produit` int(11) NOT NULL,
        `nom_produit` varchar(255) NOT NULL,
        `quantite` int(11) NOT NULL,
        `prix_unitaire` int(11) NOT NULL,
        `total` int(11) GENERATED ALWAYS AS (`prix_unitaire` * `quantite`) VIRTUAL
    )