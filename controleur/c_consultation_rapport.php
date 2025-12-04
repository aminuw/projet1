<?php
include_once("modele/consultation_rapport.modele.inc.php");

if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
    $action = "formulaire";
} else {
    $action = $_REQUEST['action'];
}

switch ($action) {

    case 'formulaire': {
        // Récupérer la région si c'est un délégué ou responsable
        $region = ($_SESSION['habilitation'] == 2 || $_SESSION['habilitation'] == 3) ? $_SESSION['region'] : null;

        // Récupérer les praticiens pour le filtre
        $praticiens = getPraticiensPourFiltre($region);

        // Récupérer les visiteurs si délégué/responsable
        $visiteurs = array();
        if ($region) {
            $visiteurs = getVisiteursRegion($region);
        }

        include("vues/v_formulaireConsultationRapport.php");
        break;
    }

    case 'liste': {
        // Récupération des filtres
        $dateDebut = !empty($_POST['date_debut']) ? $_POST['date_debut'] : null;
        $dateFin = !empty($_POST['date_fin']) ? $_POST['date_fin'] : null;
        $praticien = !empty($_POST['praticien']) ? $_POST['praticien'] : null;
        $visiteur = !empty($_POST['visiteur']) ? $_POST['visiteur'] : null;

        // Vérifier le rôle
        $region = ($_SESSION['habilitation'] == 2 || $_SESSION['habilitation'] == 3) ? $_SESSION['region'] : null;

        // Récupérer les rapports
        $rapports = getRapportsAvecFiltres(
            $_SESSION['matricule'],
            $dateDebut,
            $dateFin,
            $praticien,
            $region,
            $visiteur
        );

        // Vérifier si des rapports existent
        if (empty($rapports)) {
            $_SESSION['erreur_consultation'] = "Aucun rapport trouvé pour cette période.";
            header('Location: index.php?uc=consultation&action=formulaire');
            exit();
        }

        // Afficher la liste
        include("vues/v_listeRapports.php");
        break;
    }

    case 'detail': {
        // Vérifier qu'un numéro de rapport est fourni
        if (!isset($_GET['num']) || empty($_GET['num'])) {
            $_SESSION['erreur_consultation'] = "Numéro de rapport manquant.";
            header('Location: index.php?uc=consultation&action=formulaire');
            exit();
        }

        // Vérifier qu'un matricule est fourni
        if (!isset($_GET['mat']) || empty($_GET['mat'])) {
            $_SESSION['erreur_consultation'] = "Matricule manquant.";
            header('Location: index.php?uc=consultation&action=formulaire');
            exit();
        }

        $numRapport = $_GET['num'];
        $matricule = $_GET['mat'];

        // Vérifier les droits d'accès
        if ($_SESSION['habilitation'] != 2 && $matricule != $_SESSION['matricule']) {
            $_SESSION['erreur_consultation'] = "Vous n'avez pas accès à ce rapport.";
            header('Location: index.php?uc=consultation&action=formulaire');
            exit();
        }

        // Récupérer le détail du rapport
        $rapport = getDetailRapport($matricule, $numRapport);

        if (!$rapport) {
            $_SESSION['erreur_consultation'] = "Rapport introuvable.";
            header('Location: index.php?uc=consultation&action=formulaire');
            exit();
        }

        // Récupérer les échantillons
        $echantillons = getEchantillonsDetailRapport($matricule, $numRapport);

        include("vues/v_detailRapport.php");
        break;
    }

    default: {
        header('Location: index.php?uc=consultation&action=formulaire');
        break;
    }
}
?>