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
 * CORRIGÉ : Enregistre le motif directement dans la table rapport_visite
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
                    RAP_MOTIF,
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
                    :motif,
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
        
        // Motif
        $motif = !empty($data['motif']) ? $data['motif'] : null;
        $stmt->bindParam(':motif', $motif, PDO::PARAM_INT);
        
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
        
        // NOTE: J'ai supprimé l'appel à insertMotifRapport ici.
        // Le motif est déjà inséré juste au-dessus via :motif
        
        return $result;
        
    } catch (PDOException $e) {
        echo "Erreur SQL : " . $e->getMessage();
        // echo '<br>Données : <pre>'; print_r($data); echo '</pre>'; // Décommenter pour debug
        die();
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
 * Récupère l'id du motif d'un rapport
 * CORRIGÉ : Lit directement RAP_MOTIF dans la table rapport_visite
 */
function getMotifRapport($matricule, $numRapport)
{
    try {
        $monPdo = connexionPDO();
        
        $req = 'SELECT RAP_MOTIF 
                FROM rapport_visite 
                WHERE COL_MATRICULE = :matricule AND RAP_NUM = :num';
        
        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindParam(':num', $numRapport, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        
        return $result ? $result['RAP_MOTIF'] : null;
        
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

/**
 * Met à jour un rapport existant
 * CORRIGÉ : Met à jour directement RAP_MOTIF sans appeler de fonction externe
 */
function updateRapportVisite($data)
{
    try {
        $monPdo = connexionPDO();
        
        $req = 'UPDATE rapport_visite SET
                    RAP_DATEVISITE = :date_visite,
                    RAP_BILAN = :bilan,
                    RAP_MOTIF = :motif,
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
        
        $motif = !empty($data['motif']) ? $data['motif'] : null;
        $stmt->bindParam(':motif', $motif, PDO::PARAM_INT);
        
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
        
        return $result;
        
    } catch (PDOException $e) {
        echo "Erreur SQL : " . $e->getMessage();
        return false;
    }
}

/**
 * Met à jour UNIQUEMENT le motif d'un rapport
 * CORRIGÉ : Simple UPDATE sur rapport_visite
 */
function updateMotifRapport($matricule, $numRapport, $motifId)
{
    try {
        $monPdo = connexionPDO();
        
        $req = 'UPDATE rapport_visite 
                SET RAP_MOTIF = :motif 
                WHERE COL_MATRICULE = :matricule AND RAP_NUM = :num';
                
        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindParam(':num', $numRapport, PDO::PARAM_INT);
        $stmt->bindParam(':motif', $motifId, PDO::PARAM_INT);
        
        return $stmt->execute();
        
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