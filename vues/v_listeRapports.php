<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Liste des rapports de visite</h1>
            <p class="text text-center">
                <?php echo count($rapports); ?> rapport(s) trouvé(s)
            </p>
        </div>

        <div class="row justify-content-center py-4">
            <div class="col-12">
                
                <!-- Bouton retour -->
                <div class="mb-3">
                    <a href="index.php?uc=consultation&action=formulaire" class="btn btn-primary">
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

                                            <!-- Date de visite -->
                                            <td>
                                                <?php 
                                                echo date('d/m/Y', strtotime($rapport['RAP_DATEVISITE'])); 
                                                ?>
                                            </td>

                                            <!-- Praticien -->
                                            <td>
                                                <!-- <a href="index.php?uc=praticien&action=afficherpraticien&praticien=<?php echo $rapport['PRA_NUM']; ?>"  -->
                                                   <class="text-decoration-none"
                                                   title="Voir le détail du praticien">
                                                    <i class="text"></i>
                                                    <?php echo htmlspecialchars($rapport['praticien_nom']); ?>
                                                </a>
                                                <br>
                                                <small class="text-muted">N° <?php echo $rapport['PRA_NUM']; ?></small>
                                            </td>

                                            <!-- Motif -->
                                            <td>
                                                <?php 
                                                if (!empty($rapport['motif'])) {
                                                    echo '<span class="text">' . 
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
                                                    $medicaments[] = htmlspecialchars($rapport['med1_nom']);
                                                                    
                                                }
                                                
                                                if (!empty($rapport['MED_DEPOTLEGAL_2'])) {
                                                    $medicaments[] = htmlspecialchars($rapport['med2_nom']);
                                                                    
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
                                                <a href="index.php?uc=consultation&action=detail&num=<?php echo $rapport['RAP_NUM']; ?>&mat=<?php echo $rapport['COL_MATRICULE']; ?>" 
                                                   class="btn btn-sm btn-info text-light"
                                                   title="Voir le détail complet">
                                                    <i class="text"></i>Détail
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
                            <div class="col-md-4">
                                <h5 class="carac"><?php echo count($rapports); ?></h5>
                                <small class="text">Rapports trouvés</small>
                            </div>
                            <div class="col-md-4">
                                <h5 class="carac">
                                    <?php 
                                    $praticiens_uniques = array_unique(array_column($rapports, 'PRA_NUM'));
                                    echo count($praticiens_uniques); 
                                    ?>
                                </h5>
                                <small class="text">Praticiens différents</small>
                            </div>
                            <div class="col-md-4">
                                <h5 class="carac">
                                    <?php 
                                    $dates = array_column($rapports, 'RAP_DATEVISITE');
                                    $premiere_date = min($dates);
                                    $derniere_date = max($dates);
                                    echo date('d/m/Y', strtotime($premiere_date)) . ' - ' . 
                                         date('d/m/Y', strtotime($derniere_date));
                                    ?>
                                </h5>
                                <small class="text">Période couverte</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>