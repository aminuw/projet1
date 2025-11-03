<?php
include_once("modele/praticien.modele.inc.php");

if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
	$action = "formulairepraticien";
} else {
	$action = $_REQUEST['action'];
}
switch ($action) {
    case 'formulairepraticien': {
        $loginId = $_SESSION['login'];
        $region = getRegionByLoginId($loginId);
        $praticiens = getAllPraticiensByRegion($region);
        include("vues/v_formulairePraticien.php");
        break;
    }

    case 'afficherpraticien': {
        if (isset($_REQUEST['praticien']) && getPraticienById($_REQUEST['praticien'])) {
            $praticien = getPraticienById($_REQUEST['praticien']);
            $infos = getInfosPraticien($_REQUEST['praticien']);
            $specialite = getSpecialitePraticien($_REQUEST['praticien']);
            include("vues/v_afficherPraticien.php");
        } else {
            $_SESSION['erreur'] = true;
            header("Location: index.php?uc=praticien&action=formulairepraticien");
        }
        break;
    }

    case 'ajoutpraticien': {
        $dernierNum = getDernierNumPraticien();
        $newNum = $dernierNum + 1;
        $lesSpecialites = getLesSpecialites();
        $lesTypes = getLesTypes();
        include("vues/v_ajoutPraticien.php");
        break;
    }

    case 'valideAjout': {
        if (isset($_POST['pra_num'], $_POST['pra_prenom'], $_POST['pra_nom'], $_POST['pra_adresse'], $_POST['pra_cp'], $_POST['pra_ville'], $_POST['pra_coefnotoriete'])) {
            $pra_num = $_POST['pra_num'];
            $pra_prenom = $_POST['pra_prenom'];
            $pra_nom = $_POST['pra_nom'];
            $pra_adresse = $_POST['pra_adresse'];
            $pra_cp = $_POST['pra_cp'];
            $pra_ville = $_POST['pra_ville'];
            $pra_coefnotoriete = $_POST['pra_coefnotoriete'];
            $typ_code = isset($_POST['typ_code']) ? $_POST['typ_code'] : '';
            $spe_code = isset($_POST['spe_code']) ? $_POST['spe_code'] : '';

            if (empty($typ_code) && !isset($_GET['confirm_type'])) {
                $_SESSION["confirmation_message"] = "Le type de praticien n'a pas été renseigné. Voulez-vous continuer ?";
                $_SESSION['form_data'] = $_POST;
                header('Location: index.php?uc=praticien&action=ajoutpraticien&confirm_type=true');
                exit();
            }

            if (empty($spe_code) && !isset($_GET['confirm_spe']) && !isset($_GET['confirm_type'])) {
                $_SESSION["confirmation_message"] = "La spécialité du praticien n'a pas été renseignée. Voulez-vous continuer ?";
                $_SESSION['form_data'] = $_POST;
                header('Location: index.php?uc=praticien&action=ajoutpraticien&confirm_spe=true');
                exit();
            }

            addPraticien($pra_num, $pra_prenom, $pra_nom, $pra_adresse, $pra_cp, $pra_ville, $pra_coefnotoriete, $typ_code, $spe_code);
            unset($_SESSION['form_data']);
            $_SESSION['success_message'] = 'Le praticien a été ajouté avec succès.';
            header('Location: index.php?uc=praticien&action=formulairepraticien');
        } else {
            $missing_fields = [];
            if (!isset($_POST['pra_prenom']) || empty($_POST['pra_prenom'])) {
                $missing_fields[] = 'Prénom';
            }
            if (!isset($_POST['pra_nom']) || empty($_POST['pra_nom'])) {
                $missing_fields[] = 'Nom';
            }
            if (!isset($_POST['pra_adresse']) || empty($_POST['pra_adresse'])) {
                $missing_fields[] = 'Adresse';
            }
            if (!isset($_POST['pra_cp']) || empty($_POST['pra_cp'])) {
                $missing_fields[] = 'Code Postal';
            }
            if (!isset($_POST['pra_ville']) || empty($_POST['pra_ville'])) {
                $missing_fields[] = 'Ville';
            }
            if (!isset($_POST['pra_coefnotoriete']) || empty($_POST['pra_coefnotoriete'])) {
                $missing_fields[] = 'Coefficient Notoriété';
            }
            $_SESSION['erreur_message'] = 'Les champs suivants sont obligatoires : ' . implode(', ', $missing_fields);
            header('Location: index.php?uc=praticien&action=ajoutpraticien');
        }
        break;
    }

    case 'modifierpraticien': {
        if (isset($_REQUEST['praticien'])) {
            $praticien = getPraticienById($_REQUEST['praticien']);
            $lesSpecialites = getLesSpecialites();
            $lesTypes = getLesTypes();
            $praticien['SPE_CODE'] = getPraticienSpecialty($_REQUEST['praticien']);
            include("vues/v_modifierPraticien.php");
        } else {
            $_SESSION['erreur'] = true;
            header("Location: index.php?uc=praticien&action=formulairepraticien");
        }
        break;
    }

    case 'valideModification': {
        if (isset($_POST['pra_num'], $_POST['pra_prenom'], $_POST['pra_nom'], $_POST['pra_adresse'], $_POST['pra_cp'], $_POST['pra_ville'], $_POST['pra_coefnotoriete'])) {
            $pra_num = $_POST['pra_num'];
            $pra_prenom = $_POST['pra_prenom'];
            $pra_nom = $_POST['pra_nom'];
            $pra_adresse = $_POST['pra_adresse'];
            $pra_cp = $_POST['pra_cp'];
            $pra_ville = $_POST['pra_ville'];
            $pra_coefnotoriete = $_POST['pra_coefnotoriete'];
            $typ_code = isset($_POST['typ_code']) ? $_POST['typ_code'] : '';
            $spe_code = isset($_POST['spe_code']) ? $_POST['spe_code'] : '';

            if (empty($typ_code) && !isset($_GET['confirm_type'])) {
                $_SESSION["confirmation_message"] = "Le type de praticien n'a pas été renseigné. Voulez-vous continuer ?";
                $_SESSION['form_data'] = $_POST;
                header('Location: index.php?uc=praticien&action=modifierpraticien&praticien=' . $pra_num . '&confirm_type=true');
                exit();
            }

            if (empty($spe_code) && !isset($_GET['confirm_spe']) && !isset($_GET['confirm_type'])) {
                $_SESSION["confirmation_message"] = "La spécialité du praticien n'a pas été renseignée. Voulez-vous continuer ?";
                $_SESSION['form_data'] = $_POST;
                header('Location: index.php?uc=praticien&action=modifierpraticien&praticien=' . $pra_num . '&confirm_spe=true');
                exit();
            }

            updatePraticien($pra_num, $pra_prenom, $pra_nom, $pra_adresse, $pra_cp, $pra_ville, $pra_coefnotoriete, $typ_code, $spe_code);
            unset($_SESSION['form_data']);
            $_SESSION['success_message'] = 'Le praticien a été modifié avec succès.';
            header('Location: index.php?uc=praticien&action=formulairepraticien');
        } else {
            $missing_fields = [];
            if (!isset($_POST['pra_prenom']) || empty($_POST['pra_prenom'])) {
                $missing_fields[] = 'Prénom';
            }
            if (!isset($_POST['pra_nom']) || empty($_POST['pra_nom'])) {
                $missing_fields[] = 'Nom';
            }
            if (!isset($_POST['pra_adresse']) || empty($_POST['pra_adresse'])) {
                $missing_fields[] = 'Adresse';
            }
            if (!isset($_POST['pra_cp']) || empty($_POST['pra_cp'])) {
                $missing_fields[] = 'Code Postal';
            }
            if (!isset($_POST['pra_ville']) || empty($_POST['pra_ville'])) {
                $missing_fields[] = 'Ville';
            }
            if (!isset($_POST['pra_coefnotoriete']) || empty($_POST['pra_coefnotoriete'])) {
                $missing_fields[] = 'Coefficient Notoriété';
            }
            $_SESSION['erreur_message'] = 'Les champs suivants sont obligatoires : ' . implode(', ', $missing_fields);
            header('Location: index.php?uc=praticien&action=modifierpraticien&praticien=' . $_POST['pra_num']);
        }
        break;
    }

    default: {
        header('Location: index.php?uc=praticien&action=formulairepraticien');
        break;
    }
}
?>