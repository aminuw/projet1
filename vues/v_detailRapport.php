<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Détail du rapport de visite</h1>
            <p class="text text-center">
                Rapport n°<?php echo $rapport['RAP_NUM']; ?> - 
                <?php echo date('d/m/Y', strtotime($rapport['RAP_DATEVISITE'])); ?>
            </p>
        </div>

        <div class="row justify-content-center py-4">
            <div class="col-12 col-lg-10">

                <!-- Informations générales -->
                <div class="card shadow mb-3">
                    <div class="card-header bg-info text-light">
                        <h5 class="mb-0"><i class="text"></i> Informations générales</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p class="mb-1"><span class="carac">Numéro de rapport :</span></p>
                                <p class="text-muted">#<?php echo $rapport['RAP_NUM']; ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-1"><span class="carac">Date de visite :</span></p>
                                <p class="text-muted">
                                    <i class="text"></i> 
                                    <?php echo date('d/m/Y', strtotime($rapport['RAP_DATEVISITE'])); ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-1"><span class="carac">Visiteur médical :</span></p>
                                <p class="text-muted">
                                    <i class="text"></i> 
                                    <?php echo htmlspecialchars($rapport['visiteur_nom']); ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-1"><span class="carac">Motif de la visite :</span></p>
                                <p>
                                    <span class="text-muted">
                                        <?php 
                                        if (!empty($rapport['motif'])) {
                                            echo htmlspecialchars($rapport['motif']);
                                        } else {
                                            echo 'Non spécifié';
                                        }
                                        ?>
                                    </span>
                                </p>
                                <?php if (!empty($rapport['AUTRE_MOTIF'])): ?>
                                    <p class="text-muted">
                                        Précision : <?php echo htmlspecialchars($rapport['AUTRE_MOTIF']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Praticien visité -->
                <div class="card shadow mb-3">
                    <div class="card-header bg-info text-light">
                        <h5 class="mb-0"><i class="text"></i> Praticien visité</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="carac">
                                    <?php echo htmlspecialchars($rapport['praticien_nom']); ?>
                                </h6>
                                <p class="text-muted mb-1">
                                    <i class="text"></i> 
                                    <?php echo htmlspecialchars($rapport['PRA_ADRESSE']); ?>
                                </p>
                                <p class="text-muted mb-1">
                                    <?php echo htmlspecialchars($rapport['PRA_CP']); ?> 
                                    <?php echo htmlspecialchars($rapport['PRA_VILLE']); ?>
                                </p>
                                <p class="text-muted">
                                    <small>N° Praticien : <?php echo $rapport['PRA_NUM']; ?></small>
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="index.php?uc=praticien&action=afficherpraticien&praticien=<?php echo $rapport['PRA_NUM']; ?>" 
                                   class="btn btn-outline-info">
                                    <i class="text"></i> Voir la fiche praticien
                                </a>
                            </div>
                        </div>

                        <?php if (!empty($rapport['remplacant_nom'])): ?>
                            <hr>
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i> 
                                <strong>Praticien remplaçant :</strong> 
                                <?php echo htmlspecialchars($rapport['remplacant_nom']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Médicaments présentés -->
                <div class="card shadow mb-3">
                    <div class="card-header bg-info text-light">
                        <h5 class="mb-0"><i class="bi bi-capsule"></i> Médicaments présentés</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($rapport['MED_DEPOTLEGAL_1']) || !empty($rapport['MED_DEPOTLEGAL_2'])): ?>
                            <div class="row">
                                <?php if (!empty($rapport['MED_DEPOTLEGAL_1'])): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3 h-100">
                                            <h6 class="carac">Médicament 1</h6>
                                            <p class="mb-1">
                                                <strong>Dépôt légal :</strong> 
                                                <?php echo htmlspecialchars($rapport['MED_DEPOTLEGAL_1']); ?>
                                            </p>
                                            <p class="mb-2">
                                                <strong>Nom commercial :</strong> 
                                                <?php echo htmlspecialchars($rapport['med1_nom']); ?>
                                            </p>
                                            <a href="index.php?uc=medicaments&action=affichermedoc&medicament=<?php echo $rapport['MED_DEPOTLEGAL_1']; ?>" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="text"></i> Voir la fiche médicament
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($rapport['MED_DEPOTLEGAL_2'])): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3 h-100">
                                            <h6 class="carac">Médicament 2</h6>
                                            <p class="mb-1">
                                                <strong>Dépôt légal :</strong> 
                                                <?php echo htmlspecialchars($rapport['MED_DEPOTLEGAL_2']); ?>
                                            </p>
                                            <p class="mb-2">
                                                <strong>Nom commercial :</strong> 
                                                <?php echo htmlspecialchars($rapport['med2_nom']); ?>
                                            </p>
                                            <a href="index.php?uc=medicaments&action=affichermedoc&medicament=<?php echo $rapport['MED_DEPOTLEGAL_2']; ?>" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="text"></i> Voir la fiche médicament
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center mb-0">
                                <i class="text"></i> 
                                Aucun médicament présenté lors de cette visite
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Échantillons offerts -->
                <?php if (!empty($echantillons)): ?>
                    <div class="card shadow mb-3">
                        <div class="card-header bg-info text-light">
                            <h5 class="mb-0"><i class="text"></i> Échantillons offerts</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Dépôt légal</th>
                                            <th>Nom commercial</th>
                                            <th class="text-center">Quantité</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($echantillons as $ech): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($ech['MED_DEPOTLEGAL']); ?></td>
                                                <td><?php echo htmlspecialchars($ech['MED_NOMCOMMERCIAL']); ?></td>
                                                <td class="text-center">
                                                    <span class="text"><?php echo $ech['OFF_QTE']; ?></span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="index.php?uc=medicaments&action=affichermedoc&medicament=<?php echo $ech['MED_DEPOTLEGAL']; ?>" 
                                                     class="text-muted"> Afficher</i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Bilan de la visite -->
                <div class="card shadow mb-3">
                    <div class="card-header bg-info text-light">
                        <h5 class="mb-0"><i class="text"></i> Bilan de la visite</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-muted">
                            <p class="mb-0" style="white-space: pre-wrap;"><?php echo htmlspecialchars($rapport['RAP_BILAN']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Bouton retour en bas -->
                <div class="text-center mb-4">
                    <a href="index.php?uc=consultation&action=formulaire" class="btn btn-primary px-3">
                        <i class="texte"></i> Retour aux filtres
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

