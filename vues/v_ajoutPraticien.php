<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Ajout d'un praticien</span></h1>
        </div>
        <div class="row align-items-center justify-content-center">
            <div class="col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5 py-3">
                <?php
                if (isset($_SESSION['form_data'])) {
                    $form_data = $_SESSION['form_data'];
                    unset($_SESSION['form_data']);
                } else {
                    $form_data = [];
                }
                ?>
                <?php if (isset($_SESSION['erreur_message'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['erreur_message']; ?>
                    </div>
                    <?php unset($_SESSION['erreur_message']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['confirmation_message'])): ?>
                    <div class="alert alert-info" role="alert">
                        <?php echo $_SESSION['confirmation_message']; ?>
                        <?php
                        // Construire l'URL avec les paramètres de confirmation
                        $confirmParams = '';
                        if (isset($_GET['confirm_type'])) {
                            $confirmParams .= '&confirm_type=true';
                        }
                        if (isset($_GET['confirm_spe'])) {
                            $confirmParams .= '&confirm_spe=true';
                        }
                        ?>
                        <form action="index.php?uc=praticien&action=valideAjout<?php echo $confirmParams; ?>"
                            method="post">
                            <?php foreach ($form_data as $key => $value): ?>
                                <?php if (is_array($value)): ?>
                                    <?php foreach ($value as $item): ?>
                                        <input type="hidden" name="<?php echo $key; ?>[]" value="<?php echo htmlspecialchars($item); ?>">
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>">
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <button type="submit" class="btn btn-success">Je confirme malgré l'absence de spécialités</button>
                            <a href="index.php?uc=praticien&action=ajoutpraticien" class="btn btn-danger">Annuler</a>
                        </form>
                    </div>
                    <?php unset($_SESSION['confirmation_message']); ?>
                <?php endif; ?>
                <form action="index.php?uc=praticien&action=valideAjout" method="post">
                    <div class="form-group" style="display: none;"> 
                        <input type="number" class="form-control" id="pra_num" name="pra_num"
                            value="<?php echo $newNum; // Numéro de praticien caché envoyé en form ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="pra_prenom">Prénom</label>
                        <input type="text" class="form-control" id="pra_prenom" name="pra_prenom"
                            value="<?php echo htmlspecialchars($form_data['pra_prenom'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_nom">Nom</label>
                        <input type="text" class="form-control" id="pra_nom" name="pra_nom"
                            value="<?php echo htmlspecialchars($form_data['pra_nom'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_adresse">Adresse</label>
                        <input type="text" class="form-control" id="pra_adresse" name="pra_adresse"
                            value="<?php echo htmlspecialchars($form_data['pra_adresse'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_cp">Code Postal</label>
                        <input type="text" class="form-control" id="pra_cp" name="pra_cp"
                            value="<?php echo htmlspecialchars($form_data['pra_cp'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_ville">Ville</label>
                        <input type="text" class="form-control" id="pra_ville" name="pra_ville"
                            value="<?php echo htmlspecialchars($form_data['pra_ville'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_coefnotoriete">Coefficient Notoriété</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="pra_coefnotoriete"
                            name="pra_coefnotoriete"
                            value="<?php echo htmlspecialchars($form_data['pra_coefnotoriete'] ?? ''); ?>"
                            pattern="^\d*\.?\d+$" required>
                    </div>
                    <div class="form-group">
                        <label for="typ_code">Type</label>
                        <select class="form-control" id="typ_code" name="typ_code" required>
                            <?php foreach ($lesTypes as $type) { ?>
                                <option value="<?php echo $type['TYP_CODE']; ?>" <?php echo (isset($form_data['typ_code']) && $form_data['typ_code'] == $type['TYP_CODE']) ? 'selected' : ''; ?>>
                                    <?php echo $type['TYP_LIBELLE']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Spécialités</label>
                        <?php
                        $selectedSpe = $form_data['spe_code'] ?? [];
                        if (!is_array($selectedSpe)) {
                            $selectedSpe = $selectedSpe !== '' ? [$selectedSpe] : [];
                        }
                        ?>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            <?php if (empty($lesSpecialites)): ?>
                                <p class="text-muted">Aucune spécialité disponible</p>
                            <?php else: ?>
                                <?php foreach ($lesSpecialites as $specialite): ?>
                                    <div class="form-check">
                                        <input 
                                            type="checkbox" 
                                            class="form-check-input" 
                                            id="spe_<?php echo $specialite['SPE_CODE']; ?>" 
                                            name="spe_code[]" 
                                            value="<?php echo $specialite['SPE_CODE']; ?>"
                                            <?php echo (in_array($specialite['SPE_CODE'], $selectedSpe)) ? 'checked' : ''; ?>
                                        >
                                        <label class="form-check-label" for="spe_<?php echo $specialite['SPE_CODE']; ?>">
                                            <?php echo htmlspecialchars($specialite['SPE_LIBELLE']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <small class="form-text text-muted">Sélectionnez une ou plusieurs spécialités</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                    <a href="index.php" class="btn btn-secondary" onclick="return confirm('Êtes-vous sûr de vouloir annuler ? Toutes les modifications non enregistrées seront perdues.');">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</section>