<?php
if (isset($_SESSION['form_data'])) {
    $form_data = $_SESSION['form_data'];
    unset($_SESSION['form_data']);
} else {
    $form_data = $praticien;
}
?>
<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Modification du praticien</h1>
        </div>
        <div class="row align-items-center justify-content-center">
            <div class="col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5 py-3">
                <?php if (isset($_SESSION['erreur_message'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['erreur_message']; ?>
                    </div>
                    <?php unset($_SESSION['erreur_message']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['confirmation_message'])): ?>
                    <div class="alert alert-info" role="alert">
                        <?php echo $_SESSION['confirmation_message']; ?>
                        <form
                            action="index.php?uc=praticien&action=valideModification&praticien=<?php echo $praticien['PRA_NUM']; ?>&<?php echo http_build_query($_GET); ?>"
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
                            <button type="submit" class="btn btn-success">Oui</button>
                            <a href="index.php?uc=praticien&action=modifierpraticien&praticien=<?php echo $praticien['PRA_NUM']; ?>"
                                class="btn btn-danger">Non</a>
                        </form>
                    </div>
                    <?php unset($_SESSION['confirmation_message']); ?>
                <?php endif; ?>
                <form action="index.php?uc=praticien&action=valideModification" method="post">
                    <div class="form-group">
                        <label for="pra_num">Numéro</label>
                        <input type="number" class="form-control" id="pra_num" name="pra_num"
                            value="<?php echo $praticien['PRA_NUM']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="pra_prenom">Prénom</label>
                        <input type="text" class="form-control" id="pra_prenom" name="pra_prenom"
                            value="<?php echo htmlspecialchars($form_data['PRA_PRENOM'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_nom">Nom</label>
                        <input type="text" class="form-control" id="pra_nom" name="pra_nom"
                            value="<?php echo htmlspecialchars($form_data['PRA_NOM'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_adresse">Adresse</label>
                        <input type="text" class="form-control" id="pra_adresse" name="pra_adresse"
                            value="<?php echo htmlspecialchars($form_data['PRA_ADRESSE'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_cp">Code Postal</label>
                        <input type="text" class="form-control" id="pra_cp" name="pra_cp"
                            value="<?php echo htmlspecialchars($form_data['PRA_CP'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_ville">Ville</label>
                        <input type="text" class="form-control" id="pra_ville" name="pra_ville"
                            value="<?php echo htmlspecialchars($form_data['PRA_VILLE'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_coefnotoriete">Coefficient Notoriété</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="pra_coefnotoriete"
                            name="pra_coefnotoriete"
                            value="<?php echo htmlspecialchars($form_data['PRA_COEFNOTORIETE'] ?? ''); ?>"
                            pattern="^\d*\.?\d+$" required>
                    </div>
                    <div class="form-group">
                        <label for="typ_code">Type</label>
                        <select class="form-control" id="typ_code" name="typ_code">
                            <option value="">Aucun</option>
                            <?php foreach ($lesTypes as $type) { ?>
                                <option value="<?php echo $type['TYP_CODE']; ?>" <?php echo (isset($form_data['TYP_CODE']) && $form_data['TYP_CODE'] == $type['TYP_CODE']) ? 'selected' : ''; ?>>
                                    <?php echo $type['TYP_LIBELLE']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Spécialités</label>
                        <div class="specialites-checkboxes" style="border: 1px solid #ced4da; border-radius: 0.25rem; padding: 10px; max-height: 200px; overflow-y: auto;">
                            <?php 
                            // Récupérer les spécialités actuelles du praticien
                            $specialites_actuelles = isset($form_data['SPE_CODE']) && is_array($form_data['SPE_CODE']) 
                                ? $form_data['SPE_CODE'] 
                                : (isset($praticien['SPE_CODE']) && is_array($praticien['SPE_CODE']) 
                                    ? $praticien['SPE_CODE'] 
                                    : []);
                            
                            foreach ($lesSpecialites as $specialite) { 
                                $is_checked = in_array($specialite['SPE_CODE'], $specialites_actuelles);
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="spe_code[]" 
                                           value="<?php echo $specialite['SPE_CODE']; ?>" 
                                           id="spe_<?php echo $specialite['SPE_CODE']; ?>"
                                           <?php echo $is_checked ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="spe_<?php echo $specialite['SPE_CODE']; ?>">
                                        <?php echo $specialite['SPE_LIBELLE']; ?>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Modifier</button>
                </form>
            </div>
        </div>
    </div>
</section>