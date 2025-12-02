# 🎯 GUIDE D'IMPLÉMENTATION DES HABILITATIONS - GSB

## ✅ CE QUI A ÉTÉ FAIT

### 1. Fichiers créés
- ✅ **`modele/habilitation.modele.inc.php`** - Système central de gestion des habilitations
- ✅ **`controleur/c_praticien_avec_habilitations.php`** - Version corrigée du contrôleur praticien
- ✅ **`bdd/gestion_habilitations.sql`** - Script SQL pour configurer les comptes de test
- ✅ **`.gemini/HABILITATIONS_GUIDE.md`** - Documentation complète du système

### 2. Fichiers modifiés
- ✅ **`index.php`** - Ajout de l'inclusion du fichier d'habilitations (ligne 7)
- ✅ **`modele/praticien.modele.inc.php`** - Fonction getPraticienSpecialty() retourne un tableau
- ✅ **`vues/v_modifierPraticien.php`** - Checkboxes multi-sélection pour les spécialités

## 🚀 ÉTAPES POUR FINALISER L'IMPLÉMENTATION

### Étape 1: Remplacer le contrôleur praticien
```bash
# Faire une sauvegarde
copy c:\wamp64\www\projet1\controleur\c_praticien.php c:\wamp64\www\projet1\controleur\c_praticien_backup.php

# Remplacer par la nouvelle version
copy c:\wamp64\www\projet1\controleur\c_praticien_avec_habilitations.php c:\wamp64\www\projet1\controleur\c_praticien.php
```

**OU** copier manuellement le contenu de `c_praticien_avec_habilitations.php` dans `c_praticien.php`

### Étape 2: Configurer les comptes de test en base de données

1. Ouvrir phpMyAdmin (http://localhost:3307/phpmyadmin)
2. Sélectionner la base de données `agnaou_projet1`
3. Aller dans l'onglet SQL
4. Copier-coller et exécuter ces requêtes :

```sql
-- Promouvoir un visiteur en Délégué Régional de Bretagne
UPDATE collaborateur 
SET HAB_ID = 2, REG_CODE = 'BG' 
WHERE COL_MATRICULE = 'a131';

-- Promouvoir un visiteur en Responsable Secteur
UPDATE collaborateur 
SET HAB_ID = 3, SEC_CODE = 'E'
WHERE COL_MATRICULE = 'a17';
```

### Étape 3: Modifier index.php pour protéger le module praticien

Remplacer le case 'praticien' dans `index.php` (lignes 36-44) par :

```php
case 'praticien' : {   
    if(!empty($_SESSION['login'])){
        // Vérifier que l'utilisateur a au moins le rôle délégué
        if (estDelegue() || estResponsable()) {
            include("controleur/c_praticien.php");
        } else {
            $_SESSION['erreur_acces'] = "La gestion des praticiens est réservée aux délégués et responsables.";
            include("vues/v_accesInterdit.php");
        }
    } else {
        include("vues/v_accesInterdit.php");
    }
    break;
}
```

### Étape 4: Adapter le menu de navigation

Modifier `vues/v_footer.php` ou `vues/v_header.php` pour afficher les menus selon les droits.

Exemple à ajouter dans le menu :

```php
<?php if (estDelegue() || estResponsable()): ?>
    <li class="nav-item">
        <?php if (estResponsable()): ?>
            <a class="nav-link" href="index.php?uc=praticien&action=gererTous">
                <i class="fas fa-user-md"></i> Tous les praticiens
            </a>
        <?php elseif (estDelegue()): ?>
            <a class="nav-link" href="index.php?uc=praticien&action=gererParRegion">
                <i class="fas fa-user-md"></i> Praticiens de ma région
            </a>
        <?php endif; ?>
    </li>
<?php endif; ?>
```

### Étape 5: Créer une page d'erreur améliorée (optionnel)

Modifier `vues/v_accesInterdit.php` pour afficher le message d'erreur spécifique :

```php
<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Accès refusé</h1>
            <?php if (isset($_SESSION['erreur_acces'])): ?>
                <div class="alert alert-danger text-center">
                    <?php 
                    echo $_SESSION['erreur_acces']; 
                    unset($_SESSION['erreur_acces']);
                    ?>
                </div>
            <?php else: ?>
                <p class="text text-center">
                    Vous n'avez pas les droits nécessaires pour accéder à cette page.
                </p>
            <?php endif; ?>
            <div class="text-center mt-4">
                <a href="index.php?uc=accueil" class="btn btn-primary">
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</section>
```

## 🧪 COMPTES DE TEST

Après avoir exécuté les requêtes SQL de l'étape 2, vous aurez :

| Rôle | Login | Mot de passe | Matricule | Région/Secteur |
|------|-------|--------------|-----------|----------------|
| **Délégué Régional** | villou | VilLou! | a131 | Bretagne (BG) |
| **Responsable Secteur** | anddav | AndDav! | a17 | Secteur E (Est) |
| **Visiteur** | benpas | BenPas! | b13 | Grand Est (GE) |

## 🎯 TESTER LE SYSTÈME

### Test 1: Visiteur (benpas)
1. Se connecter avec: `benpas` / `BenPas!`
2. ✅ Devrait voir le menu rapports
3. ✅ Devrait voir le menu médicaments
4. ❌ NE DOIT PAS voir le menu praticiens
5. ❌ Si accès direct à `index.php?uc=praticien`, doit voir "Accès refusé"

### Test 2: Délégué Régional (villou)
1. Se connecter avec: `villou` / `VilLou!`
2. ✅ Devrait voir le menu praticiens
3. ✅ Devrait voir uniquement les praticiens de Bretagne (BG)
4. ✅ Peut ajouter un nouveau praticien
5. ✅ Peut modifier un praticien
6. ❌ NE DOIT PAS voir tous les praticiens (gererTous)

### Test 3: Responsable Secteur (anddav)
1. Se connecter avec: `anddav` / `AndDav!`
2. ✅ Devrait voir le menu praticiens
3. ✅ Devrait voir TOUS les praticiens
4. ✅ Peut ajouter un nouveau praticien
5. ✅ Peut modifier un praticien
6. ✅ A accès à toutes les fonctionnalités

## 📋 CHECKLIST DE VÉRIFICATION

- [ ] Le fichier `modele/habilitation.modele.inc.php` existe
- [ ] Le fichier `index.php` inclut le fichier d'habilitations (ligne 7)
- [ ] Le `c_praticien.php` a été mis à jour avec les vérifications
- [ ] Les comptes de test ont été créés en BDD (SQL exécuté)
- [ ] Le menu affiche les bons liens selon le rôle
- [ ] Testé avec chaque rôle (visiteur, délégué, responsable)
- [ ] Les délégués ne voient que leur région
- [ ] Les visiteurs n'ont pas accès aux praticiens
- [ ] Les messages d'erreur s'affichent correctement

## 🔄 EN CAS DE PROBLÈME

### Erreur: "Call to undefined function estDelegue()"
**Solution**: Vérifier que `modele/habilitation.modele.inc.php` est bien inclus dans `index.php`

### Les habilitations ne fonctionnent pas
**Solution**: Vérifier que les requêtes SQL ont bien été exécutées :
```sql
SELECT COL_MATRICULE, COL_NOM, HAB_ID 
FROM collaborateur 
WHERE COL_MATRICULE IN ('a131', 'a17', 'b13');
```

### Un délégué voit tous les praticiens
**Solution**: Vérifier que `gererParRegion` utilise bien le filtre par région :
```php
$praticiens = getAllPraticiensByRegion($region);
```

### Messages d'erreur ne s'affichent pas
**Solution**: Ajouter en haut de la vue concernée :
```php
<?php if (isset($_SESSION['erreur_acces'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['erreur_acces']; unset($_SESSION['erreur_acces']); ?>
    </div>
<?php endif; ?>
```

## 📞 AIDE SUPPLÉMENTAIRE

Pour plus de détails, consultez :
- `.gemini/HABILITATIONS_GUIDE.md` - Documentation complète
- `bdd/gestion_habilitations.sql` - Toutes les requêtes SQL utiles
- `controleur/c_praticien_avec_habilitations.php` - Code de référence

## 🎉 FÉLICITATIONS !

Une fois toutes ces étapes complétées, votre système de gestion des habilitations sera entièrement fonctionnel !

Les praticiens seront gérés selon les droits :
- **Visiteurs** : Aucun accès
- **Délégués** : Gestion de leur région uniquement
- **Responsables** : Gestion complète de tous les praticiens
