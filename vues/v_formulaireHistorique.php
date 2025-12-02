<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Historique des rapports de visite</h1>
            <p class="text text-center">
                Consultez l'historique complet des rapports de votre région
            </p>
        </div>

        <?php
        // Affichage du message d'erreur
        if (isset($_SESSION['erreur_historique'])) {
            echo '<div class="alert alert-warning m-3 text-center">
                    <i class="bi bi-exclamation-triangle"></i> ' . 
                    htmlspecialchars($_SESSION['erreur_historique']) . 
                  '</div>';
            unset($_SESSION['erreur_historique']);
        }
        ?>

        <div class="row justify-content-center py-4">
            <div class="col-12 col-lg-8 col-xl-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <form action="index.php?uc=historique&action=liste" method="post">
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 
                                <strong>Mode Délégué Régional :</strong> 
                                Vous consultez l'historique de votre région (<?php echo $_SESSION['region']; ?>)
                            </div>

                            <!-- Date de début (OBLIGATOIRE) -->
                            <div class="mb-4">
                                <h5 class="carac mb-3">
                                    <i class="bi bi-calendar-range"></i> Date de début <span class="text-danger">*</span>
                                </h5>
                                <input type="date" 
                                       class="form-control" 
                                       id="date_debut" 
                                       name="date_debut"
                                       max="<?php echo date('Y-m-d'); ?>"
                                       required>
                                <small class="text-muted">Afficher tous les rapports à partir de cette date</small>
                            </div>

                            <!-- Filtre par visiteur (OPTIONNEL) -->
                            <div class="mb-4">
                                <h5 class="carac mb-3">
                                    <i class="bi bi-person"></i> Visiteur médical
                                </h5>
                                <select class="form-select" id="visiteur" name="visiteur">
                                    <option value="">-- Tous les visiteurs --</option>
                                    <?php
                                    foreach ($visiteurs as $vis) {
                                        echo '<option value="' . $vis['COL_MATRICULE'] . '">' . 
                                             htmlspecialchars($vis['nom_complet']) . ' (' . $vis['COL_MATRICULE'] . ')' .
                                             '</option>';
                                    }
                                    ?>
                                </select>
                                <small class="text-muted">Optionnel - Filtrer par un visiteur spécifique</small>
                            </div>

                            <!-- Boutons -->
                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-info text-light px-4">
                                    <i class="bi bi-search"></i> Rechercher
                                </button>
                                <button type="reset" class="btn btn-secondary px-4">
                                    <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Aide -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="carac"><i class="bi bi-question-circle"></i> Aide</h6>
                        <ul class="small mb-0">
                            <li>La date de début est obligatoire</li>
                            <li>Tous les rapports validés depuis cette date seront affichés</li>
                            <li>Vous pouvez filtrer par un visiteur spécifique de votre région</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>