<?php

include_once 'bd.inc.php';

/**
 * Récupère le prochain numéro de rapport pour un collaborateur
 */
function getNextNumeroRapport($matricule)
{
    try {
        $monPdo = connexionPDO();
        $req = 'SELECT COALESCE(MAX(RAP_NUM), 0) + 1 as prochain FROM rapport_visite WHERE COL_MATRICULE = :matricule';
        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['prochain'];
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

/**
 * Récupère tous les motifs de visite disponibles
 */
function getAllMotifs()
{
    try {
        $monPdo = connexionPDO();
        $req = 'SELECT MOT_ID, MOT_LIBELLE FROM motif_visite ORDER BY MOT_ID';
        $res = $monPdo->query($req);
        $result = $res->fetchAll();
        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

/**
 * Récupère tous les praticiens pour la liste déroulante
 */
function getAllPraticiensForSelect()
{
    try {
        $monPdo = connexionPDO();
        $req = 'SELECT PRA_NUM, CONCAT(PRA_NOM, " ", PRA_PRENOM) as nom_complet 
                FROM praticien 
                ORDER BY PRA_NOM, PRA_PRENOM';
        $res = $monPdo->query($req);
        $result = $res->fetchAll();
        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

/**
 * Insère un nouveau rapport de visite
 * @return bool true si succès, false sinon
 */
function insertRapportVisite($data)
{
    try {
        $monPdo = connexionPDO();
        
        $req = 'INSERT INTO rapport_visite (
                    COL_MATRICULE, 
                    RAP_NUM, 
                    RAP_DATEVISITE, 
                    RAP_BILAN, 
                    PRA_NUM, 
                    PRA_NUM_praticien,
                    MED_DEPOTLEGAL_1, 
                    MED_DEPOTLEGAL_2, 
                    AUTRE_MOTIF,
                    RAP_ETAT
                ) VALUES (
                    :matricule,
                    :num_rapport,
                    :date_visite,
                    :bilan,
                    :pra_num,
                    :pra_remplacant,
                    :med1,
                    :med2,
                    :autre_motif,
                    :etat
                )';
        
        $stmt = $monPdo->prepare($req);
        
        $stmt->bindParam(':matricule', $data['matricule'], PDO::PARAM_STR);
        $stmt->bindParam(':num_rapport', $data['num_rapport'], PDO::PARAM_INT);
        $stmt->bindParam(':date_visite', $data['date_visite'], PDO::PARAM_STR);
        $stmt->bindParam(':bilan', $data['bilan'], PDO::PARAM_STR);
        $stmt->bindParam(':pra_num', $data['praticien'], PDO::PARAM_INT);
        
        // Paramètres optionnels
        $pra_remplacant = !empty($data['praticien_remplacant']) ? $data['praticien_remplacant'] : null;
        $stmt->bindParam(':pra_remplacant', $pra_remplacant, PDO::PARAM_INT);
        
        $med1 = !empty($data['medicament1']) ? $data['medicament1'] : null;
        $stmt->bindParam(':med1', $med1, PDO::PARAM_STR);
        
        $med2 = !empty($data['medicament2']) ? $data['medicament2'] : null;
        $stmt->bindParam(':med2', $med2, PDO::PARAM_STR);
        
        $autre_motif = !empty($data['autre_motif']) ? $data['autre_motif'] : '';
        $stmt->bindParam(':autre_motif', $autre_motif, PDO::PARAM_STR);
        
        $stmt->bindParam(':etat', $data['etat'], PDO::PARAM_STR);
        
        $result = $stmt->execute();
        
        // Maintenant on insère le motif dans la table motif séparément
        if ($result && !empty($data['motif'])) {
            insertMotifRapport($data['matricule'], $data['num_rapport'], $data['motif']);
        }
        
        return $result;
        
    } catch (PDOException $e) {
        echo "Erreur SQL : " . $e->getMessage();
        echo '<br>Données : <pre>';
        print_r($data);
        echo '</pre>';
        die();
    }
}

/**
 * Insère le motif d'un rapport dans la table motif
 */
function insertMotifRapport($matricule, $numRapport, $motifId)
{
    try {
        $monPdo = connexionPDO();
        
        // Récupérer le libellé du motif
        $reqMotif = 'SELECT MOT_LIBELLE FROM motif_visite WHERE MOT_ID = :motif_id';
        $stmtMotif = $monPdo->prepare($reqMotif);
        $stmtMotif->bindParam(':motif_id', $motifId, PDO::PARAM_INT);
        $stmtMotif->execute();
        $motif = $stmtMotif->fetch();
        
        if ($motif) {
            // Générer un ID unique pour le motif
            $reqMaxId = 'SELECT COALESCE(MAX(ID_MOTIF), 0) + 1 as next_id FROM motif';
            $resMaxId = $monPdo->query($reqMaxId);
            $maxId = $resMaxId->fetch();
            
            $req = 'INSERT INTO motif (ID_MOTIF, LIBELLE_MOTIF, RAP_NUM, COL_MATRICULE) 
                    VALUES (:id, :libelle, :num, :matricule)';
            
            $stmt = $monPdo->prepare($req);
            $stmt->bindParam(':id', $maxId['next_id'], PDO::PARAM_INT);
            $stmt->bindParam(':libelle', $motif['MOT_LIBELLE'], PDO::PARAM_STR);
            $stmt->bindParam(':num', $numRapport, PDO::PARAM_INT);
            $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
            
            return $stmt->execute();
        }
        
        return true;
        
    } catch (PDOException $e) {
        // Si erreur, on ne bloque pas l'enregistrement du rapport
        error_log("Erreur insertion motif : " . $e->getMessage());
        return false;
    }
}

/**
 * Insère les échantillons offerts lors d'une visite
 */
function insertEchantillons($matricule, $numRapport, $echantillons)
{
    try {
        $monPdo = connexionPDO();
        
        foreach ($echantillons as $depot => $quantite) {
            if (!empty($depot) && $quantite > 0) {
                $req = 'INSERT INTO offrir (MED_DEPOTLEGAL, RAP_NUM, COL_MATRICULE, OFF_QTE) 
                        VALUES (:depot, :num, :matricule, :qte)';
                
                $stmt = $monPdo->prepare($req);
                $stmt->bindParam(':depot', $depot, PDO::PARAM_STR);
                $stmt->bindParam(':num', $numRapport, PDO::PARAM_INT);
                $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
                $stmt->bindParam(':qte', $quantite, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        return true;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        return false;
    }
}

/**
 * Récupère tous les rapports en cours de saisie d'un visiteur
 */
function getRapportsEnCours($matricule)
{
    try {
        $monPdo = connexionPDO();
        $req = 'SELECT r.RAP_NUM, r.RAP_DATEVISITE, CONCAT(p.PRA_NOM, " ", p.PRA_PRENOM) as praticien
                FROM rapport_visite r
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                WHERE r.COL_MATRICULE = :matricule AND r.RAP_ETAT = "en_cours"
                ORDER BY r.RAP_NUM DESC';
        
        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

/**
 * Récupère un rapport spécifique (pour modification)
 */
function getRapportById($matricule, $numRapport)
{
    try {
        $monPdo = connexionPDO();
        $req = 'SELECT * FROM rapport_visite 
                WHERE COL_MATRICULE = :matricule AND RAP_NUM = :num';
        
        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindParam(':num', $numRapport, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

/**
 * Récupère les échantillons d'un rapport
 */
function getEchantillonsRapport($matricule, $numRapport)
{
    try {
        $monPdo = connexionPDO();
        $req = 'SELECT MED_DEPOTLEGAL, OFF_QTE 
                FROM offrir 
                WHERE COL_MATRICULE = :matricule AND RAP_NUM = :num';
        
        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindParam(':num', $numRapport, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

/**
 * Récupère le motif d'un rapport
 */
function getMotifRapport($matricule, $numRapport)
{
    try {
        $monPdo = connexionPDO();
        $req = 'SELECT m.ID_MOTIF, mv.MOT_ID 
                FROM motif m
                LEFT JOIN motif_visite mv ON m.LIBELLE_MOTIF = mv.MOT_LIBELLE
                WHERE m.COL_MATRICULE = :matricule AND m.RAP_NUM = :num';
        
        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindParam(':num', $numRapport, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['MOT_ID'] : null;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

/**
 * Met à jour un rapport existant
 */
function updateRapportVisite($data)
{
    try {
        $monPdo = connexionPDO();
        
        $req = 'UPDATE rapport_visite SET
                    RAP_DATEVISITE = :date_visite,
                    RAP_BILAN = :bilan,
                    PRA_NUM = :pra_num,
                    PRA_NUM_praticien = :pra_remplacant,
                    MED_DEPOTLEGAL_1 = :med1,
                    MED_DEPOTLEGAL_2 = :med2,
                    AUTRE_MOTIF = :autre_motif,
                    RAP_ETAT = :etat
                WHERE COL_MATRICULE = :matricule AND RAP_NUM = :num_rapport';
        
        $stmt = $monPdo->prepare($req);
        
        $stmt->bindParam(':matricule', $data['matricule'], PDO::PARAM_STR);
        $stmt->bindParam(':num_rapport', $data['num_rapport'], PDO::PARAM_INT);
        $stmt->bindParam(':date_visite', $data['date_visite'], PDO::PARAM_STR);
        $stmt->bindParam(':bilan', $data['bilan'], PDO::PARAM_STR);
        $stmt->bindParam(':pra_num', $data['praticien'], PDO::PARAM_INT);
        
        $pra_remplacant = !empty($data['praticien_remplacant']) ? $data['praticien_remplacant'] : null;
        $stmt->bindParam(':pra_remplacant', $pra_remplacant, PDO::PARAM_INT);
        
        $med1 = !empty($data['medicament1']) ? $data['medicament1'] : null;
        $stmt->bindParam(':med1', $med1, PDO::PARAM_STR);
        
        $med2 = !empty($data['medicament2']) ? $data['medicament2'] : null;
        $stmt->bindParam(':med2', $med2, PDO::PARAM_STR);
        
        $autre_motif = !empty($data['autre_motif']) ? $data['autre_motif'] : '';
        $stmt->bindParam(':autre_motif', $autre_motif, PDO::PARAM_STR);
        
        $stmt->bindParam(':etat', $data['etat'], PDO::PARAM_STR);
        
        $result = $stmt->execute();
        
        // Mettre à jour le motif
        if ($result && !empty($data['motif'])) {
            updateMotifRapport($data['matricule'], $data['num_rapport'], $data['motif']);
        }
        
        return $result;
        
    } catch (PDOException $e) {
        echo "Erreur SQL : " . $e->getMessage();
        return false;
    }
}

/**
 * Met à jour le motif d'un rapport
 */
function updateMotifRapport($matricule, $numRapport, $motifId)
{
    try {
        $monPdo = connexionPDO();
        
        // Supprimer l'ancien motif
        $reqDelete = 'DELETE FROM motif WHERE COL_MATRICULE = :matricule AND RAP_NUM = :num';
        $stmtDelete = $monPdo->prepare($reqDelete);
        $stmtDelete->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmtDelete->bindParam(':num', $numRapport, PDO::PARAM_INT);
        $stmtDelete->execute();
        
        // Insérer le nouveau
        return insertMotifRapport($matricule, $numRapport, $motifId);
        
    } catch (PDOException $e) {
        error_log("Erreur update motif : " . $e->getMessage());
        return false;
    }
}

/**
 * Supprime les échantillons d'un rapport
 */
function deleteEchantillonsRapport($matricule, $numRapport)
{
    try {
        $monPdo = connexionPDO();
        $req = 'DELETE FROM offrir WHERE COL_MATRICULE = :matricule AND RAP_NUM = :num';
        
        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindParam(':num', $numRapport, PDO::PARAM_INT);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        return false;
    }
}

?>