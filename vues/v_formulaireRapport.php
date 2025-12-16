<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Saisir un rapport de visite</h1>
            <p class="text text-center">
                Formulaire de saisie d'un compte-rendu de visite auprès d'un praticien.
            </p>
        </div>

        <?php
        // Affichage des messages d'erreur
        if (isset($_SESSION['erreurs']) && !empty($_SESSION['erreurs'])) {
            echo '<div class="alert alert-danger m-3">';
            echo '<h5><i class="bi bi-x-circle"></i> Erreurs de validation :</h5>';
            echo '<ul class="mb-0">';
            foreach ($_SESSION['erreurs'] as $erreur) {
                echo '<li>' . htmlspecialchars($erreur) . '</li>';
            }
            echo '</ul>';
            if (isset($_POST['saisie_definitive'])) {
                echo '<p class="mt-2 mb-0"><strong>Validation impossible.</strong> Veuillez corriger les erreurs ci-dessus.</p>';
            }
            echo '</div>';
            unset($_SESSION['erreurs']);
        }

        // Affichage des avertissements (pour saisie en cours)
        if (isset($_SESSION['avertissements']) && !empty($_SESSION['avertissements'])) {
            echo '<div class="alert alert-warning m-3">';
            echo '<h5><i class="bi bi-exclamation-triangle"></i> Avertissements :</h5>';
            echo '<ul class="mb-0">';
            foreach ($_SESSION['avertissements'] as $avertissement) {
                echo '<li>' . htmlspecialchars($avertissement) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            unset($_SESSION['avertissements']);
        }

        // Affichage du message de succès
        if (isset($_SESSION['succes'])) {
            echo '<div class="alert alert-success m-3 text-center"><i class="bi bi-check-circle"></i> ' . htmlspecialchars($_SESSION['succes']) . '</div>';
            unset($_SESSION['succes']);
        }

        // Affichage des rapports en cours
        if (!empty($rapportsEnCours)) {
            echo '<div class="alert alert-info m-3">';
            echo '<h5>Vous avez des rapports en cours de saisie :</h5>';
            echo '<ul>';
            foreach ($rapportsEnCours as $rapport) {
                echo '<li>';
                echo 'Rapport n°' . $rapport['RAP_NUM'] . ' - ';
                echo 'Praticien : ' . htmlspecialchars($rapport['praticien']) . ' - ';
                echo 'Date : ' . ($rapport['RAP_DATEVISITE'] ? date('d/m/Y', strtotime($rapport['RAP_DATEVISITE'])) : 'Non définie');
                echo ' <a href="index.php?uc=rapport&action=reprendre&num=' . $rapport['RAP_NUM'] . '" class="btn btn-sm btn-info text-light">Reprendre</a>';
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        ?>

        <div class="row justify-content-center py-4">
            <div class="col-12 col-lg-10 col-xl-8">
                <form action="index.php?uc=rapport&action=enregistrer" method="post" class="formulaire p-4">

                    <?php
                    // Si on modifie un rapport existant, ajouter un champ caché
                    if (isset($rapportEnCours) && $rapportEnCours) {
                        echo '<input type="hidden" name="num_rapport_existant" value="' . $rapportEnCours['RAP_NUM'] . '">';
                        echo '<div class="alert alert-info"><i class="bi bi-pencil"></i> <strong>Mode modification</strong> : Vous modifiez le rapport n°' . $rapportEnCours['RAP_NUM'] . '</div>';
                    }
                    ?>

                    <!-- Date de visite -->
                    <div class="mb-3">
                        <label for="date_visite" class="form-label carac">Date de visite <span
                                class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="date_visite" name="date_visite" required
                            max="<?php echo date('Y-m-d'); ?>"
                            value="<?php echo isset($rapportEnCours['RAP_DATEVISITE']) ? $rapportEnCours['RAP_DATEVISITE'] : ''; ?>">
                    </div>

                    <!-- Praticien visité -->
                    <div class="mb-3">
                        <label for="praticien" class="form-label carac">Praticien visité <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="praticien" name="praticien" required>
                            <option value="">-- Sélectionner un praticien --</option>
                            <?php
                            foreach ($praticiens as $prat) {
                                $selected = (isset($rapportEnCours['PRA_NUM']) && $rapportEnCours['PRA_NUM'] == $prat['PRA_NUM']) ? 'selected' : '';
                                echo '<option value="' . $prat['PRA_NUM'] . '" ' . $selected . '>' .
                                    htmlspecialchars($prat['nom_complet']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Praticien remplaçant (optionnel) -->
                    <div class="mb-3">
                        <label for="praticien_remplacant" class="form-label carac">Praticien remplaçant (si
                            applicable)</label>
                        <select class="form-select" id="praticien_remplacant" name="praticien_remplacant">
                            <option value="">-- Aucun remplaçant --</option>
                            <?php
                            foreach ($praticiens as $prat) {
                                $selected = (isset($rapportEnCours['PRA_NUM_praticien']) && $rapportEnCours['PRA_NUM_praticien'] == $prat['PRA_NUM']) ? 'selected' : '';
                                echo '<option value="' . $prat['PRA_NUM'] . '" ' . $selected . '>' .
                                    htmlspecialchars($prat['nom_complet']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Motif de la visite -->
                    <div class="mb-3">
                        <label for="motif" class="form-label carac">Motif de la visite <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="motif" name="motif" required onchange="toggleAutreMotif()">
                            <option value="">-- Choisir un motif --</option>
                            <?php
                            foreach ($motifs as $motif) {
                                $selected = (isset($motifRapport) && $motifRapport == $motif['MOT_ID']) ? 'selected' : '';
                                echo '<option value="' . $motif['MOT_ID'] . '" ' . $selected . '>' .
                                    htmlspecialchars($motif['MOT_LIBELLE']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Autre motif (si motif = "Autre") -->
                    <div class="mb-3" id="div_autre_motif"
                        style="display:<?php echo (isset($motifRapport) && $motifRapport == 5) ? 'block' : 'none'; ?>;">
                        <label for="autre_motif" class="form-label carac">Précisez le motif <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="autre_motif" name="autre_motif"
                            placeholder="Précisez le motif de la visite"
                            value="<?php echo isset($rapportEnCours['AUTRE_MOTIF']) ? htmlspecialchars($rapportEnCours['AUTRE_MOTIF']) : ''; ?>">
                    </div>

                    <!-- Bilan de la visite -->
                    <div class="mb-3">
                        <label for="bilan" class="form-label carac">Bilan de la visite <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="bilan" name="bilan" rows="5" required
                            placeholder="Décrivez le déroulement et le bilan de la visite..."><?php echo isset($rapportEnCours['RAP_BILAN']) ? htmlspecialchars($rapportEnCours['RAP_BILAN']) : ''; ?></textarea>
                    </div>

                    <!-- Médicaments présentés -->
                    <div class="mb-3">
                        <label class="form-label carac">Médicaments présentés (maximum 2)</label>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <select class="form-select" name="medicament1">
                                    <option value="">-- Médicament 1 (optionnel) --</option>
                                    <?php
                                    foreach ($medicaments as $med) {
                                        $selected = (isset($rapportEnCours['MED_DEPOTLEGAL_1']) && $rapportEnCours['MED_DEPOTLEGAL_1'] == $med['MED_DEPOTLEGAL']) ? 'selected' : '';
                                        echo '<option value="' . $med['MED_DEPOTLEGAL'] . '" ' . $selected . '>' .
                                            htmlspecialchars($med['MED_DEPOTLEGAL'] . ' - ' . $med['MED_NOMCOMMERCIAL']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <select class="form-select" name="medicament2">
                                    <option value="">-- Médicament 2 (optionnel) --</option>
                                    <?php
                                    foreach ($medicaments as $med) {
                                        $selected = (isset($rapportEnCours['MED_DEPOTLEGAL_2']) && $rapportEnCours['MED_DEPOTLEGAL_2'] == $med['MED_DEPOTLEGAL']) ? 'selected' : '';
                                        echo '<option value="' . $med['MED_DEPOTLEGAL'] . '" ' . $selected . '>' .
                                            htmlspecialchars($med['MED_DEPOTLEGAL'] . ' - ' . $med['MED_NOMCOMMERCIAL']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Échantillons offerts -->
                    <div class="mb-3">
                        <label class="form-label carac">Échantillons offerts</label>
                        <p class="text-muted small">Indiquez la quantité d'échantillons distribués pour chaque
                            médicament</p>
                        <div id="echantillons_container">
                            <?php
                            // Afficher les échantillons existants
                            if (!empty($echantillons)) {
                                foreach ($echantillons as $ech) {
                                    echo '<div class="row mb-2">
                                            <div class="col-md-8">
                                                <select class="form-select" name="echantillon_med[]">
                                                    <option value="">-- Sélectionner un médicament --</option>';
                                    foreach ($medicaments as $med) {
                                        $selected = ($ech['MED_DEPOTLEGAL'] == $med['MED_DEPOTLEGAL']) ? 'selected' : '';
                                        echo '<option value="' . $med['MED_DEPOTLEGAL'] . '" ' . $selected . '>' .
                                            htmlspecialchars($med['MED_DEPOTLEGAL'] . ' - ' . $med['MED_NOMCOMMERCIAL']) . '</option>';
                                    }
                                    echo '</select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" class="form-control" name="echantillon_qte[]" 
                                                       placeholder="Quantité" min="0" value="' . $ech['OFF_QTE'] . '">
                                            </div>
                                        </div>';
                                }
                            } else {
                                // Ligne par défaut si aucun échantillon
                                echo '<div class="row mb-2">
                                        <div class="col-md-8">
                                            <select class="form-select" name="echantillon_med[]">
                                                <option value="">-- Sélectionner un médicament --</option>';
                                foreach ($medicaments as $med) {
                                    echo '<option value="' . $med['MED_DEPOTLEGAL'] . '">' .
                                        htmlspecialchars($med['MED_DEPOTLEGAL'] . ' - ' . $med['MED_NOMCOMMERCIAL']) . '</option>';
                                }
                                echo '</select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" class="form-control" name="echantillon_qte[]" 
                                                   placeholder="Quantité" min="0">
                                        </div>
                                    </div>';
                            }
                            ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" onclick="ajouterEchantillon()">
                            + Ajouter un médicament
                        </button>
                    </div>
                    <!-- Coef de confiance -->
                    <div class="mb-3">
                        <label for="coef_confiance" class="form-label carac">Coef de confiance <span
                                class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="coef_confiance" name="coef_confiance" max="1000"
                            min="0" step="0.1" required placeholder="Notez le niveau de confiance..."
                            value="<?php echo isset($rapportEnCours['PRA_COEFCONF']) ? $rapportEnCours['PRA_COEFCONF'] : ''; ?>">
                    </div>

                    <!-- Case à cocher "Saisie définitive" -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="saisie_definitive" name="saisie_definitive"
                            value="1">
                        <label class="form-check-label carac" for="saisie_definitive">
                            Saisie définitive (cochez pour valider définitivement le rapport)
                        </label>
                    </div>

                    <!-- Boutons -->
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-info text-light px-4">
                            Enregistrer le rapport
                        </button>
                        <a href="index.php?uc=accueil" class="btn btn-primary px-4">Annuler</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>

<script>
    // Afficher/masquer le champ "Autre motif"
    function toggleAutreMotif() {
        var motif = document.getElementById('motif').value;
        var divAutreMotif = document.getElementById('div_autre_motif');
        var inputAutreMotif = document.getElementById('autre_motif');

        if (motif == '5') { // 5 = Autre
            divAutreMotif.style.display = 'block';
            inputAutreMotif.required = true;
        } else {
            divAutreMotif.style.display = 'none';
            inputAutreMotif.required = false;
            inputAutreMotif.value = '';
        }
    }

    // Charger le coefficient de confiance du praticien sélectionné
    function chargerCoefConfiance() {
        var praticienSelect = document.getElementById('praticien');
        var coefInput = document.getElementById('coef_confiance');
        var praNum = praticienSelect.value;

        console.log('Praticien sélectionné:', praNum);

        if (praNum) {
            // Requête AJAX pour récupérer le coefficient
            fetch('index.php?uc=praticien&action=getCoefPraticien&pra_num=' + praNum)
                .then(response => {
                    console.log('Réponse reçue:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('Données reçues:', data);
                    if (data.success) {
                        coefInput.value = data.coef_confiance || '';
                        console.log('Coefficient chargé:', data.coef_confiance);
                    } else {
                        console.error('Erreur dans la réponse:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement du coefficient:', error);
                });
        } else {
            coefInput.value = '';
        }
    }

    // Ajouter l'événement au chargement de la page
    document.addEventListener('DOMContentLoaded', function () {
        var praticienSelect = document.getElementById('praticien');
        praticienSelect.addEventListener('change', chargerCoefConfiance);

        // Charger le coefficient au démarrage si un praticien est déjà sélectionné
        if (praticienSelect.value) {
            chargerCoefConfiance();
        }
    });

    // Ajouter une ligne pour un échantillon supplémentaire
    function ajouterEchantillon() {
        var container = document.getElementById('echantillons_container');
        var newRow = document.createElement('div');
        newRow.className = 'row mb-2';
        newRow.innerHTML = `
        <div class="col-md-8">
            <select class="form-select" name="echantillon_med[]">
                <option value="">-- Sélectionner un médicament --</option>
                <?php
                foreach ($medicaments as $med) {
                    echo '<option value="' . $med['MED_DEPOTLEGAL'] . '">' .
                        htmlspecialchars($med['MED_DEPOTLEGAL'] . ' - ' . $med['MED_NOMCOMMERCIAL']) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-4">
            <input type="number" class="form-control" name="echantillon_qte[]" 
                   placeholder="Quantité" min="0">
        </div>
    `;
        container.appendChild(newRow);
    }
</script>