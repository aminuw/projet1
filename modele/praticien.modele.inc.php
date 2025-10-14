<?php

include_once 'bd.inc.php';

function getAllPraticiens()
{
    try {
        $monPdo = connexionPDO();
        $req = 'SELECT PRA_NUM, PRA_NOM, PRA_PRENOM FROM praticien ORDER BY PRA_NOM';
        $res = $monPdo->query($req);
        $result = $res->fetchAll();
        return $result;
    } 
    
    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function getPraticienById($id)
{
    try {
        $monPdo = connexionPDO();
        $req = 'SELECT * FROM praticien WHERE PRA_NUM = :id';
        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result;
    } 
    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function getInfosPraticien($id)
{
    try {
        $monPdo = connexionPDO();

        $req = "
            SELECT 
                p.PRA_NOM AS Nom,
                p.PRA_PRENOM AS Prenom,
                CONCAT(p.PRA_ADRESSE, ', ', p.PRA_CP, ', ', p.PRA_VILLE) AS Adresse,
                p.PRA_COEFNOTORIETE AS Telephone,
                s.SPE_LIBELLE AS Specialite,
                d.Departement AS Departement
            FROM praticien p
            LEFT JOIN posseder po ON p.PRA_NUM = po.PRA_NUM
            LEFT JOIN specialite s ON po.SPE_CODE = s.SPE_CODE
            LEFT JOIN departement d ON LEFT(p.PRA_CP, 2) = d.NoDEPT
            WHERE p.PRA_NUM = :id
        ";

        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result;

    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}


function getSpecialitePraticien($id)
{
    $monPdo = connexionPDO();
    $req = "
        SELECT s.SPE_LIBELLE
        FROM praticien p
        LEFT JOIN posseder po ON p.PRA_NUM = po.PRA_NUM
        LEFT JOIN specialite s ON po.SPE_CODE = s.SPE_CODE
        WHERE p.PRA_NUM = :id
    ";
    $stmt = $monPdo->prepare($req);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Retourne "Non spécifié" si aucune spécialité trouvée
    if (empty($result)) {
        return ["Non spécifiée"];
    }
    return $result;
}
