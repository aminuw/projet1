<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Informations du praticien <span class="carac"><?php echo $infos['Prenom'] .' '. $infos['Nom']; ?></span></h1>
        </div>
        <div class="row align-items-center justify-content-center">
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5">
                <img class="img-fluid" src="assets/img/medecin.jpg">
            </div>
            
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5 py-3">
                <div class="formulaire"><?php
                //var_dump($praticien);
                //var_dump($specialite);
                // var_dump($infos);
                    if (!empty($infos)) : ?>
                        <p><span class="carac">Nom</span> : <?php echo $infos['Nom']; ?></p>
                        <p><span class="carac">Prénom</span> : <?php echo $infos['Prenom']; ?></p>
                        <p><span class="carac">Adresse</span> : <?php echo $infos['Adresse']; ?></p>
                        <p><span class="carac">Coefficient de notoriété</span> : <?php echo $infos['Telephone']; ?></p>
                        <p><span class="carac">Spécialitée(s)</span> : <?php if(!empty($specialite)) { echo implode(', ', $specialite); } else { echo 'Aucune(s) spécialitée(s)'; } ?></p>
                        <p><span class="carac">Coefficient de confiance</span> : 
                        <?php 
                        if (!empty($coefConfiance)) {
                            // Calculer la moyenne des coefficients
                            $total = 0;
                            foreach ($coefConfiance as $coef) {
                                $total += $coef['POS_COEFPRESCRIPTIO'];
                            }
                            $moyenne = $total / count($coefConfiance);
                            echo number_format($moyenne, 2);
                        } else {
                            echo 'Non défini';
                        }
                        ?></p>
                        <p><span class="carac">Département</span> : <?php echo $infos['Departement']; ?></p>
                        <p><span class="carac">Type Praticien</span> : <?php echo $praticien['TYP_CODE']; ?></p>

                    <?php endif; ?>
                    <input class="btn btn-info text-light valider col-6 col-sm-5 col-md-4 col-lg-3" type="button" onclick="history.go(-1)" value="Retour">
                </div>
            </div>
        </div>
    </div>
</section>
