# Guide d'utilisation - Sélection multiple de spécialités

## 📋 Fonctionnalité mise à jour

Vous pouvez maintenant **sélectionner plusieurs spécialités** pour un praticien lors de la création ou de la modification de ses informations.

## ✅ Comment utiliser

### Lors de l'ajout d'un praticien

1. **Accédez au formulaire d'ajout** de praticien
2. **Remplissez les informations** requises (nom, prénom, adresse, etc.)
3. **Dans la section "Spécialités"**, vous verrez une liste de checkboxes
4. **Cochez toutes les spécialités** que le praticien possède
   - Vous pouvez cocher autant de spécialités que nécessaire
   - Vous pouvez aussi ne rien cocher si le praticien n'a pas de spécialité
5. **Cliquez sur "Ajouter"** pour enregistrer

### Lors de la modification d'un praticien

1. **Sélectionnez un praticien** dans la liste
2. **Cliquez sur "Modifier les informations"**
3. **Les spécialités actuelles** du praticien apparaîtront déjà cochées
4. **Modifiez les spécialités** :
   - Décochez les spécialités à retirer
   - Cochez les nouvelles spécialités à ajouter
5. **Cliquez sur "Modifier"** pour sauvegarder les changements

## 📊 Exemple pratique

**Scénario :** Dr. Martin possède actuellement la spécialité "Cardiologie"

1. Vous ouvrez le formulaire de modification
2. La checkbox "Cardiologie" est déjà cochée ✓
3. Vous voulez ajouter "Neurologie" comme deuxième spécialité
4. Cochez aussi "Neurologie" ✓
5. Maintenant Dr. Martin a 2 spécialités cochées
6. Cliquez sur "Modifier"

**Résultat :** Dr. Martin possède maintenant les spécialités "Cardiologie" ET "Neurologie"

## 🎨 Interface visuelle

La section des spécialités se présente comme suit :
- **Titre** : "Spécialités"
- **Zone scrollable** : Si vous avez beaucoup de spécialités, vous pouvez faire défiler la liste
- **Checkboxes** : Une par spécialité disponible
- **Indication visuelle** : Les spécialités déjà assignées sont pré-cochées

## 💾 Enregistrement en base de données

Lorsque vous enregistrez :
- **Ajout** : Toutes les spécialités cochées sont ajoutées dans la table `posseder`
- **Modification** : 
  1. Les anciennes spécialités sont supprimées
  2. Les nouvelles spécialités cochées sont ajoutées

## ℹ️ Notes importantes

- ✅ Vous pouvez sélectionner **autant de spécialités** que vous voulez
- ✅ Vous pouvez **ne rien sélectionner** (le système vous demandera confirmation)
- ✅ Les spécialités sont **automatiquement sauvegardées** avec des valeurs par défaut :
  - Diplôme : "DU"
  - Coefficient de prescription : 0.5
- ✅ La liste est **scrollable** si elle contient beaucoup d'éléments
- ✅ Les modifications sont **instantanément visibles** après l'enregistrement

## 🔧 Support technique

Si vous rencontrez des problèmes :
1. Vérifiez que votre navigateur supporte les formulaires HTML5
2. Assurez-vous que JavaScript est activé
3. Si les checkboxes ne s'affichent pas, actualisez la page
