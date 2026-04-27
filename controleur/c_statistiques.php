<?php
/*
 * Controleur statistiques - consultation des statistiques du secteur
 * Uniquement pour les responsables de secteur
 */
include_once("modele/rapport.modele.inc.php");
include_once("modele/medicament.modele.inc.php");

// Vérification de l'habilitation (3 = Responsable Secteur)
if ($_SESSION['habilitation'] != 3) {
    $_SESSION['erreurs'] = array("Vous n'avez pas l'autorisation d'accéder à cette page.");
    header('Location: index.php?uc=accueil');
    exit;
}

$titrePage = "Statistiques de mon secteur";
$secteur = $_SESSION['secteur'];
$listeMedicaments = getAllNomMedicament(); // pour la liste déroulante

$erreurs = array();
$rechercheEffectuee = false;
$statistiques = array();

// Traitement du formulaire de recherche
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dateDebut = isset($_POST['date_debut']) ? $_POST['date_debut'] : null;
    $dateFin = isset($_POST['date_fin']) ? $_POST['date_fin'] : null;
    $medDepotLegal = isset($_POST['medicament']) ? $_POST['medicament'] : null;

    // Validation des dates
    if (empty($dateDebut) || empty($dateFin)) {
        $erreurs[] = "Veuillez saisir une date de début et une date de fin pour afficher les statistiques.";
    } elseif ($dateDebut > $dateFin) {
        $erreurs[] = "La date de début ne peut pas être postérieure à la date de fin.";
    }

    // Si aucune erreur, on lance la recherche
    if (empty($erreurs)) {
        $rechercheEffectuee = true;
        // Récupérer les statistiques par médicament
        $statistiques = getStatistiquesEchantillonsSecteur($secteur, $dateDebut, $dateFin, $medDepotLegal);
    }
} else {
    // Valeurs par défaut pour le premier affichage
    $dateDebut = '';
    $dateFin = '';
    $medDepotLegal = '';
}

include("vues/v_statistiquesVisites.php");
?>
