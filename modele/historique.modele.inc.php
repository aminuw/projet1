<?php

include_once 'bd.inc.php';

/**
 * Récupère tous les visiteurs d'une région pour le filtre
 */
function getVisiteursRegion($region)
{
    try {
        $monPdo = connexionPDO();

        $req = 'SELECT COL_MATRICULE, CONCAT(COL_NOM, " ", COL_PRENOM) as nom_complet
                FROM collaborateur
                WHERE REG_CODE = :region AND HAB_ID = 1
                ORDER BY COL_NOM, COL_PRENOM';

        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':region', $region, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();

    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

/**
 * Récupère tous les rapports de visite avec filtres
 * MODIFIÉ : Filtre sur ETAT_CODE = 2 (Validé) au lieu de RAP_ETAT = "valide"
 */
function getRapportsAvecFiltres($matricule, $dateDebut = null, $dateFin = null, $praticien = null, $region = null, $visiteur = null, $secteur = null)
{
    try {
        $monPdo = connexionPDO();

        $req = 'SELECT DISTINCT
                    r.COL_MATRICULE,
                    r.RAP_NUM,
                    r.RAP_DATEVISITE,
                    r.RAP_BILAN,
                    r.AUTRE_MOTIF,
                    p.PRA_NUM,
                    CONCAT(p.PRA_NOM, " ", p.PRA_PRENOM) as praticien_nom,
                    m.MOT_LIBELLE as motif,
                    r.MED_DEPOTLEGAL_1,
                    r.MED_DEPOTLEGAL_2,
                    med1.MED_NOMCOMMERCIAL as med1_nom,
                    med2.MED_NOMCOMMERCIAL as med2_nom,
                    e.ETAT_LIBELLE,
                    CONCAT(c.COL_NOM, " ", c.COL_PRENOM) as visiteur_nom
                FROM rapport_visite r
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                INNER JOIN collaborateur c ON r.COL_MATRICULE = c.COL_MATRICULE
                LEFT JOIN motif_visite m ON r.RAP_MOTIF = m.MOT_ID
                LEFT JOIN medicament med1 ON r.MED_DEPOTLEGAL_1 = med1.MED_DEPOTLEGAL
                LEFT JOIN medicament med2 ON r.MED_DEPOTLEGAL_2 = med2.MED_DEPOTLEGAL
                LEFT JOIN etat e ON r.ETAT_CODE = e.ETAT_CODE
                WHERE r.ETAT_CODE = 2'; // 2 = Validé (on consulte les rapports validés uniquement)

        // Filtres selon le rôle
        if (!empty($secteur)) {
            // Pour les responsables de secteur : voir tous les rapports des régions de leur secteur
            $req .= ' AND p.PRA_NUM IN (
                        SELECT p2.PRA_NUM 
                        FROM praticien p2
                        JOIN departement d ON CAST(LEFT(p2.PRA_CP, 2) AS UNSIGNED) = d.NoDEPT
                        JOIN region r ON d.REG_CODE = r.REG_CODE
                        WHERE r.SEC_CODE = :secteur
                    )';
        } elseif (!empty($region)) {
            // Pour les délégués régionaux : voir les rapports concernant les praticiens de leur région
            // On filtre par la région du praticien (via son code postal et la table département)
            $req .= ' AND p.PRA_NUM IN (
                        SELECT p2.PRA_NUM 
                        FROM praticien p2
                        JOIN departement d ON CAST(LEFT(p2.PRA_CP, 2) AS UNSIGNED) = d.NoDEPT
                        WHERE d.REG_CODE = :region
                    )';
        } else {
            // Pour les visiteurs : seulement leurs rapports
            $req .= ' AND r.COL_MATRICULE = :matricule';
        }

        // Filtre par date de début
        if (!empty($dateDebut)) {
            $req .= ' AND r.RAP_DATEVISITE >= :dateDebut';
        }

        // Filtre par date de fin
        if (!empty($dateFin)) {
            $req .= ' AND r.RAP_DATEVISITE <= :dateFin';
        }

        // Filtre par praticien
        if (!empty($praticien)) {
            $req .= ' AND r.PRA_NUM = :praticien';
        }

        // Filtre par visiteur (pour les délégués/responsables)
        if (!empty($visiteur)) {
            $req .= ' AND r.COL_MATRICULE = :visiteur';
        }

        // Tri par date décroissant
        $req .= ' ORDER BY r.RAP_DATEVISITE DESC';

        $stmt = $monPdo->prepare($req);

        // Bind des paramètres
        if (!empty($secteur)) {
            $stmt->bindParam(':secteur', $secteur, PDO::PARAM_STR);
        } elseif (!empty($region)) {
            $stmt->bindParam(':region', $region, PDO::PARAM_STR);
        } else {
            $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        }

        if (!empty($dateDebut)) {
            $stmt->bindParam(':dateDebut', $dateDebut, PDO::PARAM_STR);
        }

        if (!empty($dateFin)) {
            $stmt->bindParam(':dateFin', $dateFin, PDO::PARAM_STR);
        }

        if (!empty($praticien)) {
            $stmt->bindParam(':praticien', $praticien, PDO::PARAM_INT);
        }

        if (!empty($visiteur)) {
            $stmt->bindParam(':visiteur', $visiteur, PDO::PARAM_STR);
        }

        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;

    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

/**
 * Récupère le détail complet d'un rapport de visite
 * AJOUT : Jointure avec la table ETAT pour récupérer le libellé
 */
function getDetailRapport($matricule, $numRapport)
{
    try {
        $monPdo = connexionPDO();

        $req = 'SELECT 
                    r.*,
                    CONCAT(p.PRA_NOM, " ", p.PRA_PRENOM) as praticien_nom,
                    p.PRA_ADRESSE,
                    p.PRA_CP,
                    p.PRA_VILLE,
                    CONCAT(p2.PRA_NOM, " ", p2.PRA_PRENOM) as remplacant_nom,
                    m.MOT_LIBELLE as motif,
                    med1.MED_NOMCOMMERCIAL as med1_nom,
                    med2.MED_NOMCOMMERCIAL as med2_nom,
                    CONCAT(c.COL_NOM, " ", c.COL_PRENOM) as visiteur_nom,
                    e.ETAT_LIBELLE
                FROM rapport_visite r
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                INNER JOIN collaborateur c ON r.COL_MATRICULE = c.COL_MATRICULE
                LEFT JOIN praticien p2 ON r.PRA_NUM_praticien = p2.PRA_NUM
                LEFT JOIN motif_visite m ON r.RAP_MOTIF = m.MOT_ID
                LEFT JOIN medicament med1 ON r.MED_DEPOTLEGAL_1 = med1.MED_DEPOTLEGAL
                LEFT JOIN medicament med2 ON r.MED_DEPOTLEGAL_2 = med2.MED_DEPOTLEGAL
                LEFT JOIN etat e ON r.ETAT_CODE = e.ETAT_CODE
                WHERE r.COL_MATRICULE = :matricule 
                AND r.RAP_NUM = :num';

        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindParam(':num', $numRapport, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();

    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

/**
 * Récupère les échantillons d'un rapport
 */
function getEchantillonsDetailRapport($matricule, $numRapport)
{
    try {
        $monPdo = connexionPDO();

        $req = 'SELECT 
                    o.OFF_QTE,
                    o.MED_DEPOTLEGAL,
                    m.MED_NOMCOMMERCIAL
                FROM offrir o
                INNER JOIN medicament m ON o.MED_DEPOTLEGAL = m.MED_DEPOTLEGAL
                WHERE o.COL_MATRICULE = :matricule 
                AND o.RAP_NUM = :num';

        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindParam(':num', $numRapport, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();

    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

/**
 * Récupère tous les praticiens pour le filtre (selon la région ou le secteur)
 */
function getPraticiensPourFiltre($region = null, $secteur = null)
{
    try {
        $monPdo = connexionPDO();

        if (!empty($secteur)) {
            // Pour responsables secteur : praticiens de toutes les régions du secteur
            $req = "SELECT DISTINCT p.PRA_NUM, CONCAT(p.PRA_NOM, ' ', p.PRA_PRENOM) as nom_complet
                    FROM praticien p
                    JOIN departement d ON CAST(LEFT(p.PRA_CP, 2) AS UNSIGNED) = d.NoDEPT
                    JOIN region r ON d.REG_CODE = r.REG_CODE
                    WHERE r.SEC_CODE = :secteur
                    ORDER BY p.PRA_NOM, p.PRA_PRENOM";

            $stmt = $monPdo->prepare($req);
            $stmt->bindParam(':secteur', $secteur, PDO::PARAM_STR);
            $stmt->execute();
        } elseif (!empty($region)) {
            // Pour délégués : praticiens de la région
            $req = "SELECT DISTINCT p.PRA_NUM, CONCAT(p.PRA_NOM, ' ', p.PRA_PRENOM) as nom_complet
                    FROM praticien p
                    JOIN departement d ON CAST(LEFT(p.PRA_CP, 2) AS UNSIGNED) = d.NoDEPT
                    WHERE d.REG_CODE = :region
                    ORDER BY p.PRA_NOM, p.PRA_PRENOM";

            $stmt = $monPdo->prepare($req);
            $stmt->bindParam(':region', $region, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            // Pour visiteurs : tous les praticiens
            $req = "SELECT PRA_NUM, CONCAT(PRA_NOM, ' ', PRA_PRENOM) as nom_complet
                    FROM praticien
                    ORDER BY PRA_NOM, PRA_PRENOM";

            $stmt = $monPdo->query($req);
        }

        return $stmt->fetchAll();

    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

/**
 * Récupère tous les visiteurs d'une région OU d'un secteur pour le filtre
 */
function getVisiteursRegionOuSecteur($region = null, $secteur = null)
{
    try {
        $monPdo = connexionPDO();

        if (!empty($secteur)) {
            // Pour responsables secteur : tous les visiteurs de toutes les régions du secteur
            $req = 'SELECT c.COL_MATRICULE, CONCAT(c.COL_NOM, " ", c.COL_PRENOM) as nom_complet
                    FROM collaborateur c
                    JOIN region r ON c.REG_CODE = r.REG_CODE
                    WHERE r.SEC_CODE = :secteur AND c.HAB_ID = 1
                    ORDER BY c.COL_NOM, c.COL_PRENOM';

            $stmt = $monPdo->prepare($req);
            $stmt->bindParam(':secteur', $secteur, PDO::PARAM_STR);
            $stmt->execute();
        } elseif (!empty($region)) {
            // Pour délégués : visiteurs de la région uniquement
            $req = 'SELECT COL_MATRICULE, CONCAT(COL_NOM, " ", COL_PRENOM) as nom_complet
                    FROM collaborateur
                    WHERE REG_CODE = :region AND HAB_ID = 1
                    ORDER BY COL_NOM, COL_PRENOM';

            $stmt = $monPdo->prepare($req);
            $stmt->bindParam(':region', $region, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            return array();
        }

        return $stmt->fetchAll();

    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

?>