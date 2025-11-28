# Modifications - Gestion des Spécialités Multiples

## Résumé des changements

Les modifications permettent maintenant de gérer **plusieurs spécialités** pour un praticien, au lieu d'une seule.

## Fichiers modifiés

### 1. **modele/praticien.modele.inc.php**

#### Fonction `getPraticienSpecialty($id)`
- **Avant** : Retournait une seule spécialité (ou null)
- **Après** : Retourne un **tableau** de toutes les spécialités du praticien
- Utilise `fetchAll(PDO::FETCH_COLUMN)` au lieu de `fetch()`

```php
// Retourne maintenant un tableau : ['SPE01', 'SPE02', ...]
$praticienSpecialites = getPraticienSpecialty($praticien_id);
```

### 2. **controleur/c_praticien.php**

#### Case 'modifierpraticien'
- Récupère toutes les spécialités dans une variable séparée `$praticienSpecialites`
- Cette variable est passée à la vue pour afficher les spécialités cochées

#### Case 'valideModification'
- Le champ `spe_code` est maintenant traité comme un **tableau** (initialisé à `[]` par défaut)
- Les fonctions `addPraticien()` et `updatePraticien()` gèrent déjà les tableaux de spécialités

### 3. **vues/v_modifierPraticien.php**

#### Interface de sélection
- **Avant** : Un `<select>` simple permettant de choisir une seule spécialité
- **Après** : Des **checkboxes** permettant de sélectionner plusieurs spécialités

**Caractéristiques** :
- Affiche toutes les spécialités disponibles
- Coche automatiquement les spécialités actuelles du praticien
- Zone scrollable (max-height: 200px) pour gérer de nombreuses spécialités
- Nom du champ : `spe_code[]` (tableau)

### 4. **vues/v_ajoutPraticien.php**

#### Interface de sélection
- Harmonisé avec le formulaire de modification
- Utilise également des **checkboxes** au lieu du système de selects dynamiques
- Même apparence et comportement que le formulaire de modification

## Fonctionnement

### Affichage des spécialités
```php
<?php foreach ($lesSpecialites as $specialite): ?>
    <div class="form-check">
        <input 
            type="checkbox" 
            name="spe_code[]" 
            value="<?php echo $specialite['SPE_CODE']; ?>"
            <?php echo (in_array($specialite['SPE_CODE'], $praticienSpecialites)) ? 'checked' : ''; ?>
        >
        <label><?php echo htmlspecialchars($specialite['SPE_LIBELLE']); ?></label>
    </div>
<?php endforeach; ?>
```

### Traitement côté serveur
Les fonctions `addPraticien()` et `updatePraticien()` dans le modèle :
1. Acceptent `$spe_code` comme tableau ou valeur unique
2. Convertissent en tableau si nécessaire
3. Parcourent chaque code de spécialité
4. Insèrent dans la table `posseder` (relation many-to-many)

## Avantages

✅ **Sélection multiple** : Un praticien peut avoir plusieurs spécialités
✅ **Interface cohérente** : Même système pour ajout et modification
✅ **Visibilité** : Toutes les spécialités sont visibles d'un coup d'œil
✅ **Facilité d'utilisation** : Checkboxes plus intuitives que des selects multiples
✅ **Rétrocompatibilité** : Les fonctions du modèle gèrent aussi les valeurs uniques

## Test

Pour tester les modifications :
1. Accédez à la modification d'un praticien
2. Vous verrez toutes ses spécialités actuelles cochées
3. Cochez/décochez des spécialités
4. Soumettez le formulaire
5. Les spécialités seront mises à jour dans la base de données
