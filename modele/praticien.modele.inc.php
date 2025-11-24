<?php

include_once 'bd.inc.php';

function getAllPraticiens()
{
    try {
        $monPdo = connexionPDO();
        $req = 'SELECT PRA_NUM, PRA_NOM, PRA_PRENOM, TYP_CODE FROM praticien ORDER BY PRA_NUM';
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

function getAllPraticiensByRegion($reg_code)
{
    try {
        $monPdo = connexionPDO();

        $req = "
            SELECT p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM
            FROM praticien p
            JOIN departement d ON LEFT(p.PRA_CP, 2) = d.NoDEPT
            WHERE d.REG_CODE = :reg_code
            ORDER BY p.PRA_NUM
        ";

        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':reg_code', $reg_code, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        die('Erreur SQL (getAllPraticiensByRegion) : ' . $e->getMessage());
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
                d.Departement AS Departement
            FROM praticien p
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


function addPraticien($pra_num, $pra_prenom, $pra_nom, $pra_adresse, $pra_cp, $pra_ville, $pra_coefnotoriete, $typ_code, $spe_code)
{
    try {
        $monPdo = connexionPDO();
        $req = 'INSERT INTO praticien (PRA_NUM, PRA_PRENOM, PRA_NOM, PRA_ADRESSE, PRA_CP, PRA_VILLE, PRA_COEFNOTORIETE, TYP_CODE) VALUES (:pra_num, :pra_prenom, :pra_nom, :pra_adresse, :pra_cp, :pra_ville, :pra_coefnotoriete, :typ_code)';
        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':pra_num', $pra_num, PDO::PARAM_INT);
        $stmt->bindParam(':pra_prenom', $pra_prenom, PDO::PARAM_STR);
        $stmt->bindParam(':pra_nom', $pra_nom, PDO::PARAM_STR);
        $stmt->bindParam(':pra_adresse', $pra_adresse, PDO::PARAM_STR);
        $stmt->bindParam(':pra_cp', $pra_cp, PDO::PARAM_STR);
        $stmt->bindParam(':pra_ville', $pra_ville, PDO::PARAM_STR);
        $stmt->bindParam(':pra_coefnotoriete', $pra_coefnotoriete, PDO::PARAM_STR);
        $stmt->bindParam(':typ_code', $typ_code, PDO::PARAM_STR);
        $stmt->execute();

        if (!empty($spe_code)) {

            $req = 'SELECT COUNT(*) FROM posseder WHERE PRA_NUM = :pra_num AND SPE_CODE = :spe_code';
            $stmt = $monPdo->prepare($req);
            $stmt->bindParam(':pra_num', $pra_num, PDO::PARAM_INT);
            $stmt->bindParam(':spe_code', $spe_code, PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                $req = 'INSERT INTO posseder (PRA_NUM, SPE_CODE, POS_DIPLOME, POS_COEFPRESCRIPTIO) VALUES (:pra_num, :spe_code, \'DU\', 0.5)';
                $stmt = $monPdo->prepare($req);
                $stmt->bindParam(':pra_num', $pra_num, PDO::PARAM_INT);
                $stmt->bindParam(':spe_code', $spe_code, PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    } 
    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function getLesTypes()
{
    try {
        $monPdo = connexionPDO();
        $req = 'SELECT TYP_CODE, TYP_LIBELLE FROM type_praticien';
        $res = $monPdo->query($req);
        $result = $res->fetchAll();
        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function getPraticienSpecialty($id)
{
    try {
        $monPdo = connexionPDO();
        $req = 'SELECT SPE_CODE FROM posseder WHERE PRA_NUM = :id';
        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['SPE_CODE'] : null;
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

    if (empty($result)) {
        return ["Non spécifiée"];
    }
    return $result;
}

function getDernierNumPraticien()
{
    try {
        $monPdo = connexionPDO();
        $req = 'SELECT MAX(PRA_NUM) as max_num FROM praticien';
        $res = $monPdo->query($req);
        $result = $res->fetch();
        return $result['max_num'];
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function getLesSpecialites()
{
    try {
        $monPdo = connexionPDO();
        $req = 'SELECT SPE_CODE, SPE_LIBELLE FROM specialite';
        $res = $monPdo->query($req);
        $result = $res->fetchAll();
        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function updatePraticien($pra_num, $pra_prenom, $pra_nom, $pra_adresse, $pra_cp, $pra_ville, $pra_coefnotoriete, $typ_code, $spe_code)
{
    try {
        $monPdo = connexionPDO();
        $req = 'UPDATE praticien SET PRA_PRENOM = :pra_prenom, PRA_NOM = :pra_nom, PRA_ADRESSE = :pra_adresse, PRA_CP = :pra_cp, PRA_VILLE = :pra_ville, PRA_COEFNOTORIETE = :pra_coefnotoriete, TYP_CODE = :typ_code WHERE PRA_NUM = :pra_num';
        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':pra_num', $pra_num, PDO::PARAM_INT);
        $stmt->bindParam(':pra_prenom', $pra_prenom, PDO::PARAM_STR);
        $stmt->bindParam(':pra_nom', $pra_nom, PDO::PARAM_STR);
        $stmt->bindParam(':pra_adresse', $pra_adresse, PDO::PARAM_STR);
        $stmt->bindParam(':pra_cp', $pra_cp, PDO::PARAM_STR);
        $stmt->bindParam(':pra_ville', $pra_ville, PDO::PARAM_STR);
        $stmt->bindParam(':pra_coefnotoriete', $pra_coefnotoriete, PDO::PARAM_STR);
        $stmt->bindParam(':typ_code', $typ_code, PDO::PARAM_STR);
        $stmt->execute();

        if (!empty($spe_code)) {
            $req = 'SELECT COUNT(*) FROM posseder WHERE PRA_NUM = :pra_num AND SPE_CODE = :spe_code';
            $stmt = $monPdo->prepare($req);
            $stmt->bindParam(':pra_num', $pra_num, PDO::PARAM_INT);
            $stmt->bindParam(':spe_code', $spe_code, PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                $req = 'INSERT INTO posseder (PRA_NUM, SPE_CODE, POS_DIPLOME, POS_COEFPRESCRIPTIO) VALUES (:pra_num, :spe_code, \'DU\', 0.5)';
                $stmt = $monPdo->prepare($req);
                $stmt->bindParam(':pra_num', $pra_num, PDO::PARAM_INT);
                $stmt->bindParam(':spe_code', $spe_code, PDO::PARAM_STR);
                $stmt->execute();
            }
        } else {
            $req = 'DELETE FROM posseder WHERE PRA_NUM = :pra_num';
            $stmt = $monPdo->prepare($req);
            $stmt->bindParam(':pra_num', $pra_num, PDO::PARAM_INT);
            $stmt->execute();
        }
    } 
    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

