<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Formulaire de praticien</h1>
            <p class="text text-center">
                Formulaire permettant d'afficher toutes les informations
                à propos d'un praticien en particulier.
            </p>
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
                <form action="index.php?uc=praticien&action=afficherpraticien" method="post" class="formulaire-recherche col-12 m-0">
                    <label class="titre-formulaire" for="listepraticien">Praticiens disponibles :</label>
                    <select required name="praticien" class="form-select mt-3">
                        <option value class="text-center">- Choisissez un praticien -</option>
                        <?php
                        var_dump($praticiens);
                        foreach ($praticiens as $praticien) {
                            echo '<option value="' . $praticien['PRA_NUM'] . '" class="form-control">' . $praticien['PRA_NUM'] . ' - ' . $praticien['PRA_NOM'] . ' ' . $praticien['PRA_PRENOM'] . '</option>';
                        }
                        ?>
                    </select>
                    <input class="btn btn-info text-light valider" type="submit" value="Afficher les informations">
                </form>
            </div>
        </div>
    </div>
</section>