<?php
include_once("modele/habilitation.modele.inc.php");
?>

<head>
    <title>Projet GSB</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/boxicon.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/gsb.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
    <nav id="main_nav" class="navbar navbar-expand-lg navbar-light bg-white shadow">
        <div class="menuCont container">
            <a class="navbar-brand h1 my-2" href="index.php?uc=accueil">
                <span class="text-dark h4 fw-bold">Projet</span> <span class="text-info h4 fw-bold">GSB</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbar-toggler-success" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="align-self-center collapse navbar-collapse flex-fill  d-lg-flex justify-content-lg-between"
                id="navbar-toggler-success">
                <div class="flex-fill d-flex justify-content-end">
                    <ul class="nav navbar-nav d-flex justify-content-between mx-xl-5 text-center text-dark">
                        <li class="nav-item ">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold"
                                href="index.php?uc=accueil">Accueil</a>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold"
                                href="index.php?uc=medicaments&action=formulairemedoc">Médicaments</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn-outline-info rounded-pill px-3 fw-bold" href="#"
                                id="praticienDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Praticien
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="praticienDropdown">
                                <!-- Section Consulter - visible pour tous -->
                                <li class="dropdown-header text-muted">Consulter</li>
                                <li><a class="dropdown-item" href="index.php?uc=praticien&action=gererTous">
                                    <i class="bi bi-list-ul me-2"></i>Tous les Praticiens
                                </a></li>
                                <?php if (estDelegue()): ?>
                                <li><a class="dropdown-item" href="index.php?uc=praticien&action=gererParRegion">
                                    <i class="bi bi-geo-alt me-2"></i>Par Région
                                </a></li>
                                <?php endif; ?>
                                <?php if (estResponsable()): ?>
                                <li><a class="dropdown-item" href="index.php?uc=praticien&action=gererParSecteur">
                                    <i class="bi bi-geo-alt me-2"></i>Par Secteur
                                </a></li>
                                <?php endif; ?>
                                
                                <?php if (estDelegue() || estResponsable()): ?>
                                <!-- Séparateur et Section Gérer - uniquement Délégué/Responsable -->
                                <li><hr class="dropdown-divider"></li>
                                <li class="dropdown-header text-muted">Gérer</li>
                                <li><a class="dropdown-item" href="index.php?uc=praticien&action=ajoutpraticien">
                                    <i class="bi bi-plus-circle me-2"></i>Ajouter Praticien
                                </a></li>
                                <li><a class="dropdown-item" href="index.php?uc=praticien&action=selectionModifier">
                                    <i class="bi bi-pencil-square me-2"></i>Modifier Praticien
                                </a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <li class="nav-item dropdown mx-2">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold dropdown-toggle" href="#"
                                id="navbarDropdownRapport" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Rapports
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownRapport">
                                <?php if ($_SESSION['habilitation'] == 1 || $_SESSION['habilitation'] == 2): ?>
                                <li>
                                    <a class="dropdown-item" href="index.php?uc=rapport&action=saisir">
                                        <i class="navbar-toggler border-0"></i> Saisir un rapport
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if ($_SESSION['habilitation'] == 3): ?>
                                <li>
                                    <a class="dropdown-item" href="index.php?uc=rapport&action=saisir">
                                        <i class="navbar-toggler border-0"></i> Nouveau rapport de région
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li>
                                    <?php if ($_SESSION['habilitation'] == 1 || $_SESSION['habilitation'] == 2): ?>
                                    <a class="dropdown-item" href="index.php?uc=consultation&action=mesRapports">
                                        <i class="navbar-toggler border-0"></i> Consulter mes rapports
                                    </a>
                                    <?php endif; ?>
                                </li>
                                <?php if ($_SESSION['habilitation'] == 2 || $_SESSION['habilitation'] == 3): ?>
                                <li>
                                    <a class="dropdown-item" href="index.php?uc=consultation&action=formulaire">
                                        <i class="navbar-toggler border-0"></i> 
                                        <?php echo ($_SESSION['habilitation'] == 3) ? 'Historique par secteur' : 'Historique par région'; ?>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold"
                                href="index.php?uc=connexion&action=profil">Profil</a>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold"
                                href="index.php?uc=connexion&action=deconnexion"
                                onclick="return confirm('Voulez-vous vraiment vous déconnecter ?');">Déconnexion</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
