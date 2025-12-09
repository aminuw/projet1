<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">
                <?php
                if ($_SESSION['habilitation'] == 2) {
                    echo 'Historique par région - ' . $_SESSION['region'];
                } elseif ($_SESSION['habilitation'] == 3) {
                    echo 'Historique par secteur - ' . $_SESSION['secteur'];
                } else {
                    echo 'Historique de mes rapports de visite';
                }
                ?>
            </h1>
            <?php if ($_SESSION['habilitation'] == 2 || $_SESSION['habilitation'] == 3): ?>
                <p class="text-center text-muted">
                    <i class="bi bi-info-circle"></i>
                    <?php
                    if ($_SESSION['habilitation'] == 3) {
                        echo 'Consultez les rapports validés de votre secteur';
                    } else {
                        echo 'Consultez les rapports validés de votre région';
                    }
                    ?>
                </p>
            <?php endif; ?>
        </div>

        <?php
        // Affichage du message d'erreur si aucun rapport trouvé
        if (isset($_SESSION['erreur_consultation'])) {
            echo '<div class="alert alert-warning m-3 text-center">
                    <i class="bi bi-exclamation-triangle"></i> ' .
                htmlspecialchars($_SESSION['erreur_consultation']) .
                '</div>';
            unset($_SESSION['erreur_consultation']);
        }
        ?>

        <div class="row justify-content-center py-4">
            <div class="col-12 col-lg-8 col-xl-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <form action="index.php?uc=consultation&action=liste" method="post">

                            <!-- Période de recherche -->
                            <div class="mb-4">
                                <h5 class="carac mb-3">
                                    <i class="text"></i> Période de recherche
                                </h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="date_debut" class="form-label">Date de début</label>
                                        <input type="date" class="form-control" id="date_debut" name="date_debut"
                                            max="<?php echo date('Y-m-d'); ?>">
                                        <small class="text-muted">Optionnel</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="date_fin" class="form-label">Date de fin</label>
                                        <input type="date" class="form-control" id="date_fin" name="date_fin"
                                            max="<?php echo date('Y-m-d'); ?>">
                                        <small class="text-muted">Optionnel</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Filtre par praticien -->
                            <div class="mb-4">
                                <h5 class="carac mb-3">
                                    <i class="text"></i> Praticien
                                </h5>
                                <select class="form-select" id="praticien" name="praticien">
                                    <option value="">-- Tous les praticiens --</option>
                                    <?php
                                    foreach ($praticiens as $prat) {
                                        echo '<option value="' . $prat['PRA_NUM'] . '">' .
                                            htmlspecialchars($prat['nom_complet']) .
                                            '</option>';
                                    }
                                    ?>
                                </select>
                                <small class="text-muted">Optionnel - Filtrer par praticien spécifique</small>
                            </div>

                            <?php if ($_SESSION['habilitation'] == 2 || $_SESSION['habilitation'] == 3): ?>
                                <!-- Filtre par visiteur (uniquement pour délégués et responsables) -->
                                <div class="mb-4">
                                    <h5 class="carac mb-3">
                                        <i class="text"></i> Visiteur médical
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
                                    <small class="text-muted">Optionnel - Filtrer par visiteur spécifique de votre
                                        <?php echo ($_SESSION['habilitation'] == 3) ? 'secteur' : 'région'; ?></small>
                                </div>

                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    <strong><?php echo ($_SESSION['habilitation'] == 3) ? 'Mode Responsable Secteur :' : 'Mode Délégué Régional :'; ?></strong>
                                    Vous visualisez les rapports de votre
                                    <?php echo ($_SESSION['habilitation'] == 3) ? 'secteur (' . $_SESSION['secteur'] . ')' : 'région (' . $_SESSION['region'] . ')'; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Boutons -->
                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-info text-light px-4">
                                    <i class="text"></i> Rechercher
                                </button>
                                <button type="reset" class="btn btn-primary px-4">
                                    <i class="text"></i> Réinitialiser
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>