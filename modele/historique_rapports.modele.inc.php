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
 * Récupère l'historique des rapports pour un délégué
 */
function getHistoriqueRapports($region, $dateDebut, $visiteur = null)
{
    try {
        $monPdo = connexionPDO();
        
        $req = 'SELECT DISTINCT
                    r.COL_MATRICULE,
                    r.RAP_NUM,
                    r.RAP_DATEVISITE,
                    r.RAP_BILAN,
                    r.RAP_MOTIF,
                    r.AUTRE_MOTIF,
                    CONCAT(c.COL_NOM, " ", c.COL_PRENOM) as visiteur_nom,
                    p.PRA_NUM,
                    CONCAT(p.PRA_NOM, " ", p.PRA_PRENOM) as praticien_nom,
                    mv.MOT_LIBELLE as motif,
                    r.MED_DEPOTLEGAL_1,
                    r.MED_DEPOTLEGAL_2,
                    med1.MED_NOMCOMMERCIAL as med1_nom,
                    med2.MED_NOMCOMMERCIAL as med2_nom
                FROM rapport_visite r
                INNER JOIN collaborateur c ON r.COL_MATRICULE = c.COL_MATRICULE
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                LEFT JOIN motif_visite mv ON r.RAP_MOTIF = mv.MOT_ID
                LEFT JOIN medicament med1 ON r.MED_DEPOTLEGAL_1 = med1.MED_DEPOTLEGAL
                LEFT JOIN medicament med2 ON r.MED_DEPOTLEGAL_2 = med2.MED_DEPOTLEGAL
                WHERE c.REG_CODE = :region
                AND r.RAP_DATEVISITE >= :dateDebut
                AND r.ETAT_CODE = 2';
        
        // Filtre par visiteur si spécifié
        if (!empty($visiteur)) {
            $req .= ' AND r.COL_MATRICULE = :visiteur';
        }
        
        // Tri par date décroissant
        $req .= ' ORDER BY r.RAP_DATEVISITE DESC';
        
        $stmt = $monPdo->prepare($req);
        
        $stmt->bindParam(':region', $region, PDO::PARAM_STR);
        $stmt->bindParam(':dateDebut', $dateDebut, PDO::PARAM_STR);
        
        if (!empty($visiteur)) {
            $stmt->bindParam(':visiteur', $visiteur, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

?>