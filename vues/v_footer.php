<?php
include_once("modele/habilitation.modele.inc.php");
?>

<div id="foot">
    <footer class="pt-4 d-flex flex-column justify-content-between">
        <div class="container">
            <div class="row pt-4">
                <div class="col-lg-3 col-12 align-left">
                    <a class="navbar-brand" href="index.php?uc=accueil">
                        <span class="text-light h5"><u>Projet GSB</u></span>
                    </a>
                    <p class="text-light my-lg-4 my-2">
                        Projet de BTS SIO 2ème année : Rédaction et suivi de rapport de visite sous forme d'un site Web pour l'entreprise GSB avec base de données.
                    </p>
                </div>

                <div class="col-lg-3 col-md-4 my-sm-0 mt-4">
                    <h3 class="h4 pb-lg-3 text-light light-300">Information</h3> 
                    <ul class="list-unstyled text-light light-300">
                        <!-- Accueil - Accessible à tous -->
                        <li class="pb-2">
                            <i class='bx-fw bx bxs-chevron-right bx-xs'></i><a class="text-decoration-none text-light" href="index.php?uc=accueil">Accueil</a>
                        </li>
                        
                        <?php if (isset($_SESSION['login'])): ?>
                            <!-- Médicaments - Accessible à tous connectés -->
                            <li class="pb-2">
                                <i class='bx-fw bx bxs-chevron-right bx-xs'></i><a class="text-decoration-none text-light py-1" href="index.php?uc=medicaments&action=formulairemedoc">Médicaments</a>
                            </li>
                            
                            <!-- Praticiens - Délégué et Responsable uniquement -->
                            <?php if (estDelegue() || estResponsable()): ?>
                            <li class="pb-2 dropdown">
                                <i class='bx-fw bx bxs-chevron-right bx-xs'></i><a class="text-decoration-none text-light py-1" href="#" id="navbarDarkDropdownMenuLinkPraticien" role="button" data-bs-toggle="dropdown" aria-expanded="false">Praticiens</a>
                                <ul class="dropdown-menu dropdown-menu-dark p-0">
                                    <!-- Tous Praticiens - Responsable uniquement -->
                                    <?php if (estResponsable()): ?>
                                    <li><a class="dropdown-item" href="index.php?uc=praticien&action=gererTous">Tous Praticiens</a></li>
                                    <?php endif; ?>
                                    
                                    <!-- Par région - Délégué et Responsable -->
                                    <li><a class="dropdown-item" href="index.php?uc=praticien&action=gererParRegion">Praticien par Région</a></li>
                                    
                                    <!-- Ajouter - Délégué et Responsable -->
                                    <li><a class="dropdown-item" href="index.php?uc=praticien&action=ajoutpraticien">Ajouter Praticien</a></li>
                                </ul>
                            </li>
                            <?php endif; ?>
                            
                            <!-- Rapports - Accessible à tous connectés -->
                            <li class="pb-2 dropdown">
                                <i class='bx-fw bx bxs-chevron-right bx-xs'></i><a class="text-decoration-none text-light py-1" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">Rapport de visite</a>
                                <ul class="dropdown-menu dropdown-menu-dark p-0">
                                    <!-- Saisir - Tous -->
                                    <li><a class="dropdown-item" href="index.php?uc=rapport&action=saisir">Saisir un rapport</a></li>
                                    
                                    <!-- Consulter mes rapports - Tous -->
                                    <li><a class="dropdown-item" href="index.php?uc=consultation&action=formulaire">Consulter mes rapports</a></li>
                                    
                                    <!-- Rapport de ma région - Délégué uniquement -->
                                    <?php if (estDelegue()): ?>
                                    <li><a class="dropdown-item" href="index.php?uc=rapportdevisite&action=rapportregion">Rapport de ma région</a></li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                            
                            <!-- Profil - Accessible à tous connectés -->
                            <li class="pb-2">
                                <i class="bx-fw bx bxs-chevron-right bx-xs"></i><a class="text-decoration-none text-light py-1" href="index.php?uc=connexion&action=profil">Profil</a>
                            </li>
                            
                        <?php else: ?>
                            <!-- Connexion - Pour les non connectés -->
                            <li class="pb-2">
                                <i class="bx-fw bx bxs-chevron-right bx-xs"></i><a class="text-decoration-none text-light py-1" href="index.php?uc=connexion&action=connexion">Connexion</a>
                            </li>
                        <?php endif; ?>
                        
                    </ul>
                </div>
            </div>
        </div>

        <div class="w-100 footercustom pt-3">
            <div class="container">
                <div class="row pt-2 d-flex justify-content-center">
                    <div class="col-lg-6 col-sm-12">
                        <p class="text-center text-light light-300">
                            © Copyright <?php echo date('Y'); ?> Randy Durelle | Tristan Da Silva.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </footer>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/isotope.pkgd.js"></script>
<script src="assets/js/custom.js"></script>