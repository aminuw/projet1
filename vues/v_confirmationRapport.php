<section class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Confirmation requise</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <?php echo htmlspecialchars($_SESSION['avertissement']); ?>
                        </div>
                        
                        <p class="text-muted">
                            Vous pouvez confirmer pour continuer la validation du rapport, 
                            ou revenir en arrière pour modifier les informations.
                        </p>
                        
                        <form action="index.php?uc=rapport&action=enregistrer" method="post">
                            <?php
                            // Réinjecter toutes les données du formulaire
                            if (isset($_SESSION['data_rapport'])) {
                                foreach ($_SESSION['data_rapport'] as $key => $value) {
                                    if (is_array($value)) {
                                        foreach ($value as $subKey => $subValue) {
                                            echo '<input type="hidden" name="' . htmlspecialchars($key) . '[]" value="' . htmlspecialchars($subValue) . '">';
                                        }
                                    } else {
                                        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
                                    }
                                }
                            }
                            
                            // Ajouter la confirmation selon le type
                            if (isset($_SESSION['avertissement_type'])) {
                                if ($_SESSION['avertissement_type'] == 'medicament') {
                                    echo '<input type="hidden" name="confirm_no_med" value="1">';
                                } elseif ($_SESSION['avertissement_type'] == 'echantillon') {
                                    echo '<input type="hidden" name="confirm_no_ech" value="1">';
                                }
                            }
                            ?>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-warning px-4">
                                    <i class="bi bi-check-circle"></i> Confirmer quand même
                                </button>
                                  <button type="button" onclick="history.back()" class="btn btn-secondary px-4">
                                    <i class="bi bi-arrow-left"></i> Revenir en arrière
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Nettoyer les variables de session après affichage
unset($_SESSION['avertissement']);
unset($_SESSION['avertissement_type']);
// Garder data_rapport pour la réinjection
?>