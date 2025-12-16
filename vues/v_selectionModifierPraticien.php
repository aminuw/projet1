<?php
// Les praticiens sont filtrés selon l'habilitation dans le contrôleur
?>
<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Modifier un praticien</h1>
            <p class="text text-center">
                Sélectionnez un praticien dans la liste pour modifier ses informations.
                <?php if (isset($filtreInfo) && !empty($filtreInfo)): ?>
                <br><small class="text-muted"><?php echo $filtreInfo; ?></small>
                <?php endif; ?>
            </p>
        </div>
        <div class="row align-items-center justify-content-center">
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5">
                <img class="img-fluid" src="assets/img/medecin.jpg">
            </div>
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5 py-3">
                <?php if (!empty($_SESSION['erreur'])): ?>
                    <div class="alert alert-danger text-center w-100">
                        Un problème est survenu lors de la sélection du praticien
                    </div>
                    <?php $_SESSION['erreur'] = false; ?>
                <?php endif; ?>
                
                <?php if (empty($praticiens)): ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> Aucun praticien disponible pour votre zone.
                    </div>
                <?php else: ?>
                    <form id="modifierPraticienForm" action="index.php?uc=praticien&action=modifierpraticien" method="post" class="formulaire-recherche col-12 m-0">
                        <label class="titre-formulaire" for="praticien">Praticiens disponibles :</label>
                        <select required name="praticien" id="praticien" class="form-select mt-3">
                            <option value="" class="text-center">- Choisissez un praticien à modifier -</option>
                            <?php foreach ($praticiens as $pra): ?>
                                <option value="<?php echo $pra['PRA_NUM']; ?>" class="form-control">
                                    <?php echo $pra['PRA_NUM'] . ' - ' . $pra['PRA_NOM'] . ' ' . $pra['PRA_PRENOM']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="d-flex justify-content-center mt-4">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-pencil-square me-2"></i>Modifier ce praticien
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="index.php?uc=praticien&action=gererTous" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Retour à la liste
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
