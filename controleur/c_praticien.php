<?php
include_once("modele/praticien.modele.inc.php");

if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
	$action = "formulairepraticien";
} else {
	$action = $_REQUEST['action'];
}
switch ($action) {
	case 'formulairepraticien': {
		$praticiens = getAllPraticiens();
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

	default: {
		header('Location: index.php?uc=praticien&action=formulairepraticien');
		break;
	}
}
?>
