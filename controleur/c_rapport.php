<?php
/*
 * Controleur rapport - saisie et gestion des rapports de visite
 * Visiteurs et delegues peuvent saisir, reprendre et valider leurs rapports
 */
include_once("modele/rapport.modele.inc.php"); // Ou 'bd.inc.php' selon ton arborescence

if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
    $action = "saisir";
} else {
    $action = $_REQUEST['action'];
}

switch ($action) {

    case 'saisir': {
        // Vérification : seuls les visiteurs (1) et délégués (2) peuvent saisir des rapports
        if ($_SESSION['habilitation'] != 1 && $_SESSION['habilitation'] != 2) {
            $_SESSION['erreurs'] = array("Vous n'avez pas l'autorisation de saisir des rapports");
            header('Location: index.php?uc=accueil');
            exit();
        }

        // Récupérer les données nécessaires pour le formulaire
        $praticiens = getAllPraticiensForSelect();
        $medicaments = getAllNomMedicament();
        $motifs = getAllMotifs();

        // Récupérer les rapports en cours du visiteur
        $rapportsEnCours = getRapportsEnCours($_SESSION['matricule']);

        // Initialiser les variables pour le formulaire
        $rapportEnCours = null;
        $echantillons = array();
        $motifRapport = null;

        include("vues/v_formulaireRapport.php");
        break;
    }

    case 'reprendre': {
        // Vérifier qu'un numéro de rapport est fourni
        if (!isset($_GET['num']) || empty($_GET['num'])) {
            $_SESSION['erreurs'] = array("Numéro de rapport manquant");
            header('Location: index.php?uc=rapport&action=saisir');
            exit();
        }

        $numRapport = $_GET['num'];

        // Récupérer le rapport
        $rapportEnCours = getRapportById($_SESSION['matricule'], $numRapport);

        if (!$rapportEnCours) {
            $_SESSION['erreurs'] = array("Rapport introuvable");
            header('Location: index.php?uc=rapport&action=saisir');
            exit();
        }

        // --- CORRECTION ICI : On vérifie le code (1 = en cours) ---
        // On vérifie que le rapport est bien en cours
        // Si ETAT_CODE est différent de 1, alors il est validé (2) ou archivé
        if ($rapportEnCours['ETAT_CODE'] != 1) {
            $_SESSION['erreurs'] = array("Ce rapport a déjà été validé et ne peut plus être modifié");
            header('Location: index.php?uc=rapport&action=saisir');
            exit();
        }

        // Récupérer les données complémentaires
        $praticiens = getAllPraticiensForSelect();
        $medicaments = getAllNomMedicament();
        $motifs = getAllMotifs();
        $rapportsEnCours = getRapportsEnCours($_SESSION['matricule']);
        $echantillons = getEchantillonsRapport($_SESSION['matricule'], $numRapport);
        $motifRapport = getMotifRapport($_SESSION['matricule'], $numRapport);

        include("vues/v_formulaireRapport.php");
        break;
    }

    case 'enregistrer': {

        // Tableau pour stocker les erreurs
        $erreurs = array();

        // Déterminer si c'est une modification ou une création
        $isUpdate = isset($_POST['num_rapport_existant']) && !empty($_POST['num_rapport_existant']);

        // Déterminer si c'est une saisie définitive
        $saisieDefinitive = isset($_POST['saisie_definitive']) && $_POST['saisie_definitive'] == '1';

        // --- VALIDATION DES CHAMPS OBLIGATOIRES ---

        // Date de visite
        if (empty($_POST['date_visite'])) {
            $erreurs[] = "La date de visite est obligatoire";
        } else {
            // Vérifier que la date n'est pas dans le futur
            if (strtotime($_POST['date_visite']) > time()) {
                $erreurs[] = "La date de visite ne peut pas être dans le futur";
            }
        }

        // Praticien
        if (empty($_POST['praticien'])) {
            $erreurs[] = "Le praticien visité est obligatoire";
        }

        // Motif
        if (empty($_POST['motif'])) {
            $erreurs[] = "Le motif de la visite est obligatoire";
        } else {
            // Vérification motif "Autre" (Code 5)
            if ($_POST['motif'] == '5') {
                if (empty($_POST['autre_motif']) || trim($_POST['autre_motif']) == '') {
                    $erreurs[] = "Veuillez saisir le motif autre";
                }
            }
        }

        // Bilan
        if (empty($_POST['bilan']) || trim($_POST['bilan']) == '') {
            $erreurs[] = "Le bilan de la visite est obligatoire";
        }

        // --- VALIDATION SUPPLÉMENTAIRE SI SAISIE DÉFINITIVE ---
        if ($saisieDefinitive) {

            // Vérifier que tous les champs obligatoires sont remplis
            if (!empty($erreurs)) {
                $_SESSION['erreurs'] = $erreurs;
                $_SESSION['erreurs'][] = "Validation impossible : veuillez corriger les erreurs ci-dessus";
                header('Location: index.php?uc=rapport&action=saisir');
                exit();
            }

            // Avertissement si pas de médicament présenté
            if (empty($_POST['medicament1']) && empty($_POST['medicament2'])) {
                if (!isset($_POST['confirm_no_med']) && !isset($_POST['num_rapport_existant'])) {
                    $_SESSION['avertissement_type'] = 'medicament';
                    $_SESSION['avertissement'] = "Aucun médicament n'a été présenté lors de cette visite. Confirmez-vous ?";
                    $_SESSION['data_rapport'] = $_POST;
                    header('Location: index.php?uc=rapport&action=confirmer');
                    exit();
                } elseif ($isUpdate && !isset($_POST['confirm_no_med'])) {
                    $_SESSION['avertissement_type'] = 'medicament';
                    $_SESSION['avertissement'] = "Aucun médicament n'a été présenté lors de cette visite. Confirmez-vous ?";
                    $_SESSION['data_rapport'] = $_POST;
                    header('Location: index.php?uc=rapport&action=confirmer');
                    exit();
                }
            }

            // Avertissement si pas d'échantillon
            $hasEchantillons = false;
            if (!empty($_POST['echantillon_med']) && !empty($_POST['echantillon_qte'])) {
                foreach ($_POST['echantillon_qte'] as $qte) {
                    if (!empty($qte) && $qte > 0) {
                        $hasEchantillons = true;
                        break;
                    }
                }
            }

            if (!$hasEchantillons) {
                if (!isset($_POST['confirm_no_ech']) && !isset($_POST['num_rapport_existant'])) {
                    $_SESSION['avertissement_type'] = 'echantillon';
                    $_SESSION['avertissement'] = "Aucun échantillon n'a été offert lors de cette visite. Confirmez-vous ?";
                    $_SESSION['data_rapport'] = $_POST;
                    header('Location: index.php?uc=rapport&action=confirmer');
                    exit();
                } elseif ($isUpdate && !isset($_POST['confirm_no_ech'])) {
                    $_SESSION['avertissement_type'] = 'echantillon';
                    $_SESSION['avertissement'] = "Aucun échantillon n'a été offert lors de cette visite. Confirmez-vous ?";
                    $_SESSION['data_rapport'] = $_POST;
                    header('Location: index.php?uc=rapport&action=confirmer');
                    exit();
                }
            }
        }

        // --- SI ERREURS ET PAS DE SAISIE DÉFINITIVE ---
        if (!empty($erreurs) && !$saisieDefinitive) {
            $_SESSION['avertissements'] = $erreurs;
            $_SESSION['avertissements'][] = "Rapport enregistré en cours de saisie avec des champs manquants";
        }

        // --- ENREGISTREMENT EN BASE DE DONNÉES ---

        // --- CORRECTION ICI : On utilise les codes entiers ---
        // 2 = Validé, 1 = En cours
        $etat = $saisieDefinitive ? 2 : 1;

        // Déterminer le numéro de rapport
        if ($isUpdate) {
            $numRapport = $_POST['num_rapport_existant'];
        } else {
            $numRapport = getNextNumeroRapport($_SESSION['matricule']);
        }

        // Préparer les données
        $data = array(
            'matricule' => $_SESSION['matricule'],
            'num_rapport' => $numRapport,
            'date_visite' => $_POST['date_visite'] ?? null,
            'bilan' => $_POST['bilan'] ?? '',
            'motif' => $_POST['motif'] ?? null,
            'praticien' => $_POST['praticien'] ?? null,
            'praticien_remplacant' => $_POST['praticien_remplacant'] ?? null,
            'medicament1' => $_POST['medicament1'] ?? null,
            'medicament2' => $_POST['medicament2'] ?? null,
            'autre_motif' => $_POST['autre_motif'] ?? '',
            'etat' => $etat // On passe l'entier (1 ou 2)
        );

        // Insertion ou mise à jour du rapport
        if ($isUpdate) {
            $succes = updateRapportVisite($data);
        } else {
            $succes = insertRapportVisite($data);
        }

        if ($succes) {
            // Supprimer les anciens échantillons si c'est une modification
            if ($isUpdate) {
                deleteEchantillonsRapport($_SESSION['matricule'], $numRapport);
            }

            // Enregistrer les échantillons
            if (!empty($_POST['echantillon_med']) && !empty($_POST['echantillon_qte'])) {
                $echantillons = array();
                foreach ($_POST['echantillon_med'] as $index => $depot) {
                    if (!empty($depot) && !empty($_POST['echantillon_qte'][$index]) && $_POST['echantillon_qte'][$index] > 0) {
                        $echantillons[$depot] = $_POST['echantillon_qte'][$index];
                    }
                }
                if (!empty($echantillons)) {
                    insertEchantillons($_SESSION['matricule'], $numRapport, $echantillons);
                }
            }

            // --- CORRECTION ICI : Messages basés sur l'entier 2 (Validé) ---
            if ($isUpdate) {
                if ($etat == 2) {
                    $_SESSION['succes'] = "Rapport de visite n°" . $numRapport . " mis à jour et validé avec succès !";
                } else {
                    $_SESSION['succes'] = "Rapport de visite n°" . $numRapport . " mis à jour (toujours en cours de saisie).";
                }
            } else {
                if ($etat == 2) {
                    $_SESSION['succes'] = "Rapport de visite n°" . $numRapport . " enregistré avec succès et validé !";
                } else {
                    $_SESSION['succes'] = "Rapport de visite n°" . $numRapport . " enregistré en cours de saisie.";
                }
            }

            // Nettoyer les données temporaires
            unset($_SESSION['data_rapport']);

            header('Location: index.php?uc=rapport&action=saisir');
        } else {
            $_SESSION['erreurs'] = array("Une erreur est survenue lors de l'enregistrement du rapport.");
            header('Location: index.php?uc=rapport&action=saisir');
        }

        break;
    }

    case 'confirmer': {
        // Afficher la page de confirmation
        include("vues/v_confirmationRapport.php");
        break;
    }

    default: {
        header('Location: index.php?uc=rapport&action=saisir');
        break;
    }
}
?>