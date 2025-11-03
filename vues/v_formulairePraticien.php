<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Gestion des praticiens</h1>
            <p class="text text-center">
                Sélectionnez un praticien pour afficher, modifier ses informations, ou ajoutez un nouveau praticien.
            </p>
            <?php //var_dump($praticiens); ?>
        </div>
        <div class="row align-items-center justify-content-center">
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5">
                <img class="img-fluid" src="assets/img/medecin.jpg">
            </div>
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5 py-3">
                <?php if (!empty($_SESSION['erreur'])) {
                    echo '<p class="alert alert-danger text-center w-100">Un problème est survenu lors de la sélection du praticien</p>';
                    $_SESSION['erreur'] = false;
                } ?>
                <?php if (isset($_SESSION['success_message'])) : ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_SESSION['success_message']; ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                <form id="praticienForm" action="" method="post" class="formulaire-recherche col-12 m-0">
                    <label class="titre-formulaire" for="listepraticien">Praticiens disponibles :</label>
                    <select required name="praticien" class="form-select mt-3">
                        <option value class="text-center">- Choisissez un praticien -</option>
                        <?php
                        foreach ($praticiens as $praticien) {
                            echo '<option value="' . $praticien['PRA_NUM'] . '" class="form-control">' . $praticien['PRA_NUM'] . ' - ' . $praticien['PRA_NOM'] . ' ' . $praticien['PRA_PRENOM'] . '</option>';
                        }
                        ?>
                    </select>
                    <div class="d-flex justify-content-around mt-3">
                        <button type="submit" class="btn btn-info text-light" onclick="setAction('afficherpraticien')">Afficher les informations</button>
                        <button type="submit" class="btn btn-warning" onclick="setAction('modifierpraticien')">Modifier les informations</button>
                    </div>
                </form>
                <div class="mt-3 text-center">
                    <a href="index.php?uc=praticien&action=ajoutpraticien" class="btn btn-success">
                        Ajouter un praticien
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function setAction(action) {
    document.getElementById('praticienForm').action = 'index.php?uc=praticien&action=' + action;
}
</script>