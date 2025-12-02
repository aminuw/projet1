<?php
include_once("modele/historique_rapports.modele.inc.php");
include_once("modele/consultation_rapport.modele.inc.php"); // Pour le détail

// Vérifier que c'est un délégué
if ($_SESSION['habilitation'] != 2) {
    $_SESSION['erreur_historique'] = "Accès réservé aux délégués régionaux.";
    header('Location: index.php?uc=accueil');
    exit();
}

if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
    $action = "formulaire";
} else {
    $action = $_REQUEST['action'];
}

switch ($action) {
    
    case 'formulaire': {
        // Récupérer les visiteurs de la région pour le filtre
        $visiteurs = getVisiteursRegion($_SESSION['region']);
        
        include("vues/v_formulaireHistorique.php");
        break;
    }

    case 'liste': {
        // Vérifier que la date de début est fournie
        if (empty($_POST['date_debut'])) {
            $_SESSION['erreur_historique'] = "La date de début est obligatoire.";
            header('Location: index.php?uc=historique&action=formulaire');
            exit();
        }
        
        $dateDebut = $_POST['date_debut'];
        $visiteur = !empty($_POST['visiteur']) ? $_POST['visiteur'] : null;
        
        // Récupérer l'historique des rapports
        $rapports = getHistoriqueRapports(
            $_SESSION['region'], 
            $dateDebut, 
            $visiteur
        );
        
        // Vérifier si des rapports existent
        if (empty($rapports)) {
            $_SESSION['erreur_historique'] = "Aucun rapport trouvé pour cette période.";
            header('Location: index.php?uc=historique&action=formulaire');
            exit();
        }
        
        // Afficher la liste
        include("vues/v_listeHistorique.php");
        break;
    }

    case 'detail': {
        // Vérifier qu'un numéro de rapport est fourni
        if (!isset($_GET['num']) || empty($_GET['num'])) {
            $_SESSION['erreur_historique'] = "Numéro de rapport manquant.";
            header('Location: index.php?uc=historique&action=formulaire');
            exit();
        }
        
        if (!isset($_GET['mat']) || empty($_GET['mat'])) {
            $_SESSION['erreur_historique'] = "Matricule manquant.";
            header('Location: index.php?uc=historique&action=formulaire');
            exit();
        }
        
        $numRapport = $_GET['num'];
        $matricule = $_GET['mat'];
        
        // Récupérer le détail du rapport
        $rapport = getDetailRapport($matricule, $numRapport);
        
        if (!$rapport) {
            $_SESSION['erreur_historique'] = "Rapport introuvable.";
            header('Location: index.php?uc=historique&action=formulaire');
            exit();
        }
        
        // Vérifier que le rapport est de la région du délégué
        include_once("modele/connexion.modele.inc.php");
        $monPdo = connexionPDO();
        $reqCheck = 'SELECT REG_CODE FROM collaborateur WHERE COL_MATRICULE = :mat';
        $stmtCheck = $monPdo->prepare($reqCheck);
        $stmtCheck->bindParam(':mat', $matricule, PDO::PARAM_STR);
        $stmtCheck->execute();
        $collab = $stmtCheck->fetch();
        
        if ($collab['REG_CODE'] != $_SESSION['region']) {
            $_SESSION['erreur_historique'] = "Ce rapport n'appartient pas à votre région.";
            header('Location: index.php?uc=historique&action=formulaire');
            exit();
        }
        
        // Récupérer les échantillons
        $echantillons = getEchantillonsDetailRapport($matricule, $numRapport);
        
        // Variable pour différencier le retour
        $retour_url = "index.php?uc=historique&action=formulaire";
        
        include("vues/v_detailRapportHistorique.php");
        break;
    }

    default: {
        header('Location: index.php?uc=historique&action=formulaire');
        break;
    }
}
?>