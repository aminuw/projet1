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
                <?php if (isset($_SESSION['erreur_message'])) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['erreur_message']; ?>
                    </div>
                    <?php unset($_SESSION['erreur_message']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['confirmation_message'])) : ?>
                    <div class="alert alert-info" role="alert">
                        <?php echo $_SESSION['confirmation_message']; ?>
                        <form action="index.php?uc=praticien&action=valideAjout&<?php echo http_build_query($_GET); ?>" method="post">
                            <?php foreach ($form_data as $key => $value) : ?>
                                <input type="hidden" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>">
                            <?php endforeach; ?>
                            <button type="submit" class="btn btn-success">Oui</button>
                            <a href="index.php?uc=praticien&action=ajoutpraticien" class="btn btn-danger">Non</a>
                        </form>
                    </div>
                    <?php unset($_SESSION['confirmation_message']); ?>
                <?php endif; ?>
                <form action="index.php?uc=praticien&action=valideAjout" method="post">
                    <div class="form-group">
                        <label for="pra_num">Numéro</label>
                        <input type="number" class="form-control" id="pra_num" name="pra_num" value="<?php echo $newNum; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="pra_prenom">Prénom</label>
                        <input type="text" class="form-control" id="pra_prenom" name="pra_prenom" value="<?php echo htmlspecialchars($form_data['pra_prenom'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_nom">Nom</label>
                        <input type="text" class="form-control" id="pra_nom" name="pra_nom" value="<?php echo htmlspecialchars($form_data['pra_nom'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_adresse">Adresse</label>
                        <input type="text" class="form-control" id="pra_adresse" name="pra_adresse" value="<?php echo htmlspecialchars($form_data['pra_adresse'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_cp">Code Postal</label>
                        <input type="text" class="form-control" id="pra_cp" name="pra_cp" value="<?php echo htmlspecialchars($form_data['pra_cp'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_ville">Ville</label>
                        <input type="text" class="form-control" id="pra_ville" name="pra_ville" value="<?php echo htmlspecialchars($form_data['pra_ville'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pra_coefnotoriete">Coefficient Notoriété</label>
                        <input type="number" step="0.01" class="form-control" id="pra_coefnotoriete" name="pra_coefnotoriete" value="<?php echo htmlspecialchars($form_data['pra_coefnotoriete'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="typ_code">Type</label>
                        <select class="form-control" id="typ_code" name="typ_code" required>
                            <?php foreach ($lesTypes as $type) { ?>
                                <option value="<?php echo $type['TYP_CODE']; ?>" <?php echo (isset($form_data['typ_code']) && $form_data['typ_code'] == $type['TYP_CODE']) ? 'selected' : ''; ?>><?php echo $type['TYP_LIBELLE']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="spe_code">Spécialité</label>
                        <select class="form-control" id="spe_code" name="spe_code">
                            <option value="">Aucune</option>
                            <?php foreach ($lesSpecialites as $specialite) { ?>
                                <option value="<?php echo $specialite['SPE_CODE']; ?>" <?php echo (isset($form_data['spe_code']) && $form_data['spe_code'] == $specialite['SPE_CODE']) ? 'selected' : ''; ?>><?php echo $specialite['SPE_LIBELLE']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</section>
