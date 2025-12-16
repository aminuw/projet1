<?php
ob_start();

require_once('modele/medicament.modele.inc.php');

require_once('modele/connexion.modele.inc.php');

if (!isset($_REQUEST['uc']) || empty($_REQUEST['uc']))
    $uc = 'accueil';
else {
    $uc = $_REQUEST['uc'];
}

// Détecter si c'est une requête AJAX qui ne doit pas avoir de header/footer
$isAjax = (isset($_REQUEST['action']) && $_REQUEST['action'] === 'getCoefPraticien');

?>
<?php
if (!$isAjax) {
    if (empty($_SESSION['login'])) {
        include("vues/v_headerDeconnexion.php");
    } else {
        include("vues/v_header.php");
    }
}
switch ($uc) {
    case 'accueil': {
        include("vues/v_accueil.php");
        break;
    }
    case 'medicaments': {
        if (!empty($_SESSION['login'])) {
            include("controleur/c_medicaments.php");
        } else {
            include("vues/v_accesInterdit.php");
        }
        break;
    }
    case 'praticien': {
        if (!empty($_SESSION['login'])) {
            include("controleur/c_praticien.php");
        } else {
            include("vues/v_accesInterdit.php");
        }
        break;
    }
    case 'rapport': {
        if (!empty($_SESSION['login'])) {
            include("controleur/c_rapport.php");
        } else {
            include("vues/v_accesInterdit.php");
        }
        break;
    }
    case 'consultation': {
        if (!empty($_SESSION['login'])) {
            include("controleur/c_historique.php");
        } else {
            include("vues/v_accesInterdit.php");
        }
        break;
    }

    case 'connexion': {
        include("controleur/c_connexion.php");
        break;
    }

    default: {

        include("vues/v_accueil.php");
        break;
    }
}
?>
<?php
if (!$isAjax) {
    include("vues/v_footer.php");
    ob_end_flush();
    ?>
    </body>

    </html>
<?php } ?>