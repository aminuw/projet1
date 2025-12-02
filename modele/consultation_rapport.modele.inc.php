<?php

include_once 'bd.inc.php';

/**
 * Récupère tous les rapports de visite avec filtres
 * MODIFIÉ : Filtre sur ETAT_CODE = 2 (Validé) au lieu de RAP_ETAT = "valide"
 */
function getRapportsAvecFiltres($matricule, $dateDebut = null, $dateFin = null, $praticien = null, $region = null)
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
                    e.ETAT_LIBELLE 
                FROM rapport_visite r
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                LEFT JOIN motif_visite m ON r.RAP_MOTIF = m.MOT_ID
                LEFT JOIN medicament med1 ON r.MED_DEPOTLEGAL_1 = med1.MED_DEPOTLEGAL
                LEFT JOIN medicament med2 ON r.MED_DEPOTLEGAL_2 = med2.MED_DEPOTLEGAL
                LEFT JOIN etat e ON r.ETAT_CODE = e.ETAT_CODE
                WHERE r.ETAT_CODE = 1'; // CORRECTION ICI : 2 = Validé
        
        // Filtres selon le rôle
        if (!empty($region)) {
            // Pour les délégués régionaux : voir les rapports de leur région
            $req .= ' AND r.COL_MATRICULE IN (
                        SELECT COL_MATRICULE 
                        FROM collaborateur 
                        WHERE REG_CODE = :region
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
        
        // Tri par date décroissant
        $req .= ' ORDER BY r.RAP_DATEVISITE DESC';
        
        $stmt = $monPdo->prepare($req);
        
        // Bind des paramètres
        if (!empty($region)) {
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
 * Récupère tous les praticiens pour le filtre (selon la région du visiteur)
 */
function getPraticiensPourFiltre($region = null)
{
    try {
        $monPdo = connexionPDO();
        
        if (!empty($region)) {
            // Pour délégués : praticiens de la région
            $req = "SELECT DISTINCT p.PRA_NUM, CONCAT(p.PRA_NOM, ' ', p.PRA_PRENOM) as nom_complet
                    FROM praticien p
                    JOIN departement d ON LEFT(p.PRA_CP, 2) = d.NoDEPT
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

?>