<?php

include_once 'bd.inc.php';

function getAllInformationCompte($matricule)
{

    try {
        $monPdo = connexionPDO();
        // CORRECTION : Récupère REG_CODE au lieu de REG_NOM pour les filtres SQL
        $req = 'SELECT c.`COL_MATRICULE` as `matricule`,c.`COL_NOM` as `nom`,`COL_PRENOM` as `prenom`,c.`COL_ADRESSE` as `adresse`,c.`COL_CP` as `cp`,c.`COL_VILLE` as `ville`, concat(DAY(COL_DATEEMBAUCHE),\'/\',MONTH(`COL_DATEEMBAUCHE`),\'/\',YEAR(`COL_DATEEMBAUCHE`)) as `date_embauche`, h.HAB_LIB as `habilitation` ,s.SEC_LIBELLE as `secteur`, r.REG_CODE as `region` FROM collaborateur c LEFT JOIN secteur s ON s.`SEC_CODE`=c.`SEC_CODE` LEFT JOIN habilitation h ON h.HAB_ID=c.HAB_ID LEFT JOIN region r ON r.REG_CODE=c.REG_CODE WHERE c.COL_MATRICULE="' . $matricule . '"';
        $res = $monPdo->query($req);
        $result = $res->fetch();

        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function checkConnexion($username, $mdp)
{

    try {
        $getInfo = connexionPDO();
        // Récupération du REG_CODE, SEC_CODE pour les filtres
        $req = $getInfo->prepare('SELECT l.LOG_ID as \'id_log\', l.COL_MATRICULE as \'matricule\', c.HAB_ID as \'habilitation\', c.REG_CODE as \'REG_CODE\', c.SEC_CODE as \'SEC_CODE\' FROM login l INNER JOIN collaborateur c ON l.COL_MATRICULE = c.COL_MATRICULE WHERE l.LOG_LOGIN = :identifiant AND l.LOG_MOTDEPASSE = "' . hash('sha512', $mdp) . '"');
        $req->bindParam(':identifiant', $username, PDO::PARAM_STR);
        $req->execute();
        $res = $req->fetch();

        return $res;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function checkMatriculeInscription($matricule)
{

    try {
        $getInfo = connexionPDO();
        $req = $getInfo->prepare('select `COL_MATRICULE` as \'matricule\' from login where `COL_MATRICULE`=:matricule');
        $req->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $req->execute();
        $res = $req->fetch();

        return $res;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function checkMatricule($matricule)
{

    try {
        $getInfo = connexionPDO();
        $req = $getInfo->prepare('select `COL_MATRICULE` as \'matricule\' from collaborateur where `COL_MATRICULE`=:matricule');
        $req->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $req->execute();
        $res = $req->fetch();

        return $res;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function checkUserInscription($username)
{

    try {
        $getInfo = connexionPDO();
        $req = $getInfo->prepare('SELECT `LOG_LOGIN` from login where `LOG_LOGIN`=:username');
        $req->bindParam(':username', $username, PDO::PARAM_STR);
        $req->execute();
        $res = $req->fetch();

        return $res;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function getAllMatriculeCollaborateur()
{

    try {

        $monPdo = connexionPDO();
        $req = 'SELECT COL_MATRICULE FROM collaborateur ORDER BY COL_MATRICULE';
        $res = $monPdo->query($req);
        $result = $res->fetchAll();

        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function getColMatricule()
{

    try {

        $monPdo = connexionPDO();
        $req = 'SELECT COL_MATRICULE FROM collaborateur ORDER BY COL_MATRICULE';
        $res = $monPdo->query($req);
        $result = $res->fetchAll();

        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function getCountMatricule()
{

    try {

        $monPdo = connexionPDO();
        $req = 'SELECT COUNT(COL_MATRICULE) as \'nb\' FROM collaborateur';
        $res = $monPdo->query($req);
        $result = $res->fetch();

        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function getRegionByLoginId($loginId)
{
    try {
        $monPdo = connexionPDO();
        $req = "
            SELECT r.REG_CODE AS region_code
            FROM login l
            JOIN collaborateur c ON l.COL_MATRICULE = c.COL_MATRICULE
            JOIN region r ON c.REG_CODE = r.REG_CODE
            WHERE l.LOG_ID = :loginId
            LIMIT 1
        ";
        $stmt = $monPdo->prepare($req);
        $stmt->bindParam(':loginId', $loginId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['region_code'] : null;
    } catch (PDOException $e) {
        die("Erreur SQL (getRegionByLoginId) : " . $e->getMessage());
    }
}

