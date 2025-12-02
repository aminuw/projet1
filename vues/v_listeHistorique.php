<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Historique des rapports de visite</h1>
            <p class="text text-center">
                <?php echo count($rapports); ?> rapport(s) trouvé(s)
            </p>
        </div>

        <div class="row justify-content-center py-4">
            <div class="col-12">
                
                <!-- Bouton retour -->
                <div class="mb-3">
                    <a href="index.php?uc=historique&action=formulaire" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Nouvelle recherche
                    </a>
                </div>

                <!-- Tableau des rapports -->
                <div class="card shadow">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-info">
                                    <tr>
                                        <th>N° Rapport</th>
                                        <th>Visiteur</th>
                                        <th>Date visite</th>
                                        <th>Praticien</th>
                                        <th>Motif</th>
                                        <th>Médicaments présentés</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rapports as $rapport): ?>
                                        <tr>
                                            <!-- Numéro de rapport -->
                                            <td class="fw-bold">
                                                #<?php echo $rapport['RAP_NUM']; ?>
                                            </td>

                                            <!-- Visiteur -->
                                            <td>
                                                <i class="bi bi-person-badge"></i>
                                                <?php echo htmlspecialchars($rapport['visiteur_nom']); ?>
                                                <br>
                                                <small class="text-muted"><?php echo $rapport['COL_MATRICULE']; ?></small>
                                            </td>

                                            <!-- Date de visite -->
                                            <td>
                                                <?php 
                                                echo date('d/m/Y', strtotime($rapport['RAP_DATEVISITE'])); 
                                                ?>
                                            </td>

                                            <!-- Praticien -->
                                            <td>
                                                <a href="index.php?uc=praticien&action=afficherpraticien&praticien=<?php echo $rapport['PRA_NUM']; ?>" 
                                                   class="text-decoration-none"
                                                   title="Voir le détail du praticien">
                                                    <i class="bi bi-person-badge"></i>
                                                    <?php echo htmlspecialchars($rapport['praticien_nom']); ?>
                                                </a>
                                                <br>
                                                <small class="text-muted">N° <?php echo $rapport['PRA_NUM']; ?></small>
                                            </td>

                                            <!-- Motif -->
                                            <td>
                                                <?php 
                                                if (!empty($rapport['motif'])) {
                                                    echo '<span class="badge bg-secondary">' . 
                                                         htmlspecialchars($rapport['motif']) . 
                                                         '</span>';
                                                } else {
                                                    echo '<span class="text-muted">Non spécifié</span>';
                                                }
                                                ?>
                                            </td>

                                            <!-- Médicaments -->
                                            <td>
                                                <?php 
                                                $medicaments = [];
                                                
                                                if (!empty($rapport['MED_DEPOTLEGAL_1'])) {
                                                    $medicaments[] = '<a href="index.php?uc=medicaments&action=affichermedoc&medicament=' . 
                                                                    $rapport['MED_DEPOTLEGAL_1'] . '" ' .
                                                                    'class="text-decoration-none" ' .
                                                                    'title="Voir le détail du médicament">' .
                                                                    '<i class="bi bi-capsule"></i> ' .
                                                                    htmlspecialchars($rapport['MED_DEPOTLEGAL_1']) .
                                                                    '</a><br><small>' . 
                                                                    htmlspecialchars($rapport['med1_nom']) . 
                                                                    '</small>';
                                                }
                                                
                                                if (!empty($rapport['MED_DEPOTLEGAL_2'])) {
                                                    $medicaments[] = '<a href="index.php?uc=medicaments&action=affichermedoc&medicament=' . 
                                                                    $rapport['MED_DEPOTLEGAL_2'] . '" ' .
                                                                    'class="text-decoration-none" ' .
                                                                    'title="Voir le détail du médicament">' .
                                                                    '<i class="bi bi-capsule"></i> ' .
                                                                    htmlspecialchars($rapport['MED_DEPOTLEGAL_2']) .
                                                                    '</a><br><small>' . 
                                                                    htmlspecialchars($rapport['med2_nom']) . 
                                                                    '</small>';
                                                }
                                                
                                                if (!empty($medicaments)) {
                                                    echo implode('<hr class="my-1">', $medicaments);
                                                } else {
                                                    echo '<span class="text-muted">Aucun</span>';
                                                }
                                                ?>
                                            </td>

                                            <!-- Actions -->
                                            <td class="text-center">
                                                <a href="index.php?uc=historique&action=detail&num=<?php echo $rapport['RAP_NUM']; ?>&mat=<?php echo $rapport['COL_MATRICULE']; ?>" 
                                                   class="btn btn-sm btn-info text-light"
                                                   title="Voir le détail complet">
                                                    <i class="bi bi-eye"></i> Détail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <h5 class="carac"><?php echo count($rapports); ?></h5>
                                <small class="text-muted">Rapports trouvés</small>
                            </div>
                            <div class="col-md-3">
                                <h5 class="carac">
                                    <?php 
                                    $visiteurs_uniques = array_unique(array_column($rapports, 'COL_MATRICULE'));
                                    echo count($visiteurs_uniques); 
                                    ?>
                                </h5>
                                <small class="text-muted">Visiteurs différents</small>
                            </div>
                            <div class="col-md-3">
                                <h5 class="carac">
                                    <?php 
                                    $praticiens_uniques = array_unique(array_column($rapports, 'PRA_NUM'));
                                    echo count($praticiens_uniques); 
                                    ?>
                                </h5>
                                <small class="text-muted">Praticiens différents</small>
                            </div>
                            <div class="col-md-3">
                                <h5 class="carac">
                                    <?php 
                                    $dates = array_column($rapports, 'RAP_DATEVISITE');
                                    $premiere_date = min($dates);
                                    $derniere_date = max($dates);
                                    echo date('d/m/Y', strtotime($premiere_date)) . ' - ' . 
                                         date('d/m/Y', strtotime($derniere_date));
                                    ?>
                                </h5>
                                <small class="text-muted">Période couverte</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>