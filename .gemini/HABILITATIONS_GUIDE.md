# Système de Gestion des Habilitations - GSB

## 📋 Vue d'ensemble

Ce document décrit le système de gestion des habilitations (droits d'accès) implémenté pour l'application GSB.

## 👥 Rôles et habilitations

### 1. Visiteur (HAB_ID = 1)
**Permissions** :
- ✅ Consulter ses propres rapports de visite
- ✅ Créer ses propres rapports de visite  
- ✅ Modifier ses propres rapports (tant qu'ils ne sont pas validés)
- ✅ Consulter la liste des médicaments
- ❌ Gérer les praticiens
- ❌ Consulter les rapports des autres
- ❌ Valider des rapports

### 2. Délégué Régional (HAB_ID = 2)
**Permissions** :
- ✅ Toutes les permissions du Visiteur
- ✅ Consulter tous les rapports de sa région
- ✅ Valider les rapports des visiteurs de sa région
- ✅ Gérer les praticiens de sa région (consulter, ajouter, modifier)
- ✅ Consulter les données de tous les visiteurs de sa région
- ❌ Accéder aux données d'autres régions
- ❌ Gérer tous les praticiens (hors région)

### 3. Responsable Secteur (HAB_ID = 3)
**Permissions** :
- ✅ Toutes les permissions du Délégué
- ✅ Consulter TOUS les rapports (toutes régions)
- ✅ Gérer TOUS les praticiens (consulter, ajouter, modifier, supprimer)
- ✅ Accès complet à toutes les données
- ✅ Valider tous les rapports
- ✅ Gérer les médicaments

## 📁 Fichiers modifiés/créés

### 1. `modele/habilitation.modele.inc.php` ✅ CRÉÉ
Fichier central de gestion des habilitations contenant :
- Constantes pour les niveaux d'habilitation
- Fonctions de vérification (`estVisiteur()`, `estDelegue()`, `estResponsable()`)
- Matrice des permissions par module
- Fonction `peutEffectuerAction($module, $action)`

### 2. `index.php` ✅ MODIFIÉ
- Inclusion du fichier d'habilitations
- À faire : Ajouter vérifications pour chaque module

### 3. `controleur/c_praticien.php` ⚠️ À MODIFIER
**Actions à restreindre** :
- `gererParRegion` → Délégués + Responsables uniquement
- `gererTous` → Responsables uniquement
- `ajoutpraticien` → Délégués + Responsables
- `modifierpraticien` → Délégués + Responsables
- `valideAjout` → Délégués + Responsables
- `valideModification` → Délégués + Responsables

### 4. `controleur/c_rapport.php` ⚠️ À MODIFIER  
**Actions à restreindre** :
- Création → Tous
- Consultation propres → Tous
- Consultation région → Délégués + Responsables
- Consultation tous → Responsables uniquement
- Validation → Délégués + Responsables

### 5. `controleur/c_consultation_rapport.php` ✅ PARTIELLEMENT FAIT
Déjà partiellement implémenté :
- Ligne 14 : Filtre par région pour délégués
- Ligne 30 : Filtre par région pour délégués  
- Ligne 72 : Vérification des droits d'accès

## 🔧 Implémentation recommandée

### Étape 1 : Modifier le contrôleur praticien

Ajouter en début de chaque case qui nécessite des droits :

```php
case 'gererTous': {
    // Vérifier l'habilitation
    if (!estResponsable()) {
        $_SESSION['erreur'] = true;
        header("Location: index.php?uc=accueil");
        exit();
    }
    // Code existant...
}

case 'gererParRegion': {
    if (!estDelegue() && !estResponsable()) {
        $_SESSION['erreur'] = true;
        header("Location: index.php?uc=accueil");
        exit();
    }
    // Code existant...
}
```

### Étape 2 : Adapter l'interface selon les droits

Dans les vues (ex: `v_header.php`, `v_footer.php`), afficher les menus selon les habilitations :

```php
<?php if (peutEffectuerAction('praticien', 'consulter_tous')): ?>
    <a href="index.php?uc=praticien&action=gererTous">Tous les praticiens</a>
<?php endif; ?>

<?php if (peutEffectuerAction('praticien', 'consulter_region')): ?>
    <a href="index.php?uc=praticien&action=gererParRegion">Praticiens de ma région</a>
<?php endif; ?>
```

### Étape 3 : Modifier index.php

Protéger chaque module selon les besoins :

```php
case 'praticien' : {   
    if(!empty($_SESSION['login'])){
        // Vérifier que l'utilisateur a au moins le rôle délégué
        if (estDelegue() || estResponsable()) {
            include("controleur/c_praticien.php");
        } else {
            $_SESSION['erreur_acces'] = "Accès réservé aux délégués et responsables.";
            include("vues/v_accesInterdit.php");
        }
    } else {
        include("vues/v_accesInterdit.php");
    }
    break;
}
```

## 📊 Matrice des permissions détaillée

| Module | Action | Visiteur | Délégué | Responsable |
|--------|--------|----------|---------|-------------|
| **Praticiens** |
| | Consulter tous | ❌ | ❌ | ✅ |
| | Consulter région | ❌ | ✅ | ✅ |
| | Ajouter | ❌ | ✅ | ✅ |
| | Modifier | ❌ | ✅ | ✅ |
| | Supprimer | ❌ | ❌ | ✅ |
| **Rapports** |
| | Créer | ✅ | ✅ | ✅ |
| | Consulter propres | ✅ | ✅ | ✅ |
| | Consulter région | ❌ | ✅ | ✅ |
| | Consulter tous | ❌ | ❌ | ✅ |
| | Modifier propres | ✅ | ✅ | ✅ |
| | Modifier région | ❌ | ✅ | ✅ |
| | Valider | ❌ | ✅ | ✅ |
| **Consultation** |
| | Consulter propres | ✅ | ✅ | ✅ |
| | Consulter région | ❌ | ✅ | ✅ |
| | Consulter tous | ❌ | ❌ | ✅ |
| **Médicaments** |
| | Consulter | ✅ | ✅ | ✅ |
| | Modifier | ❌ | ❌ | ✅ |

## ⚡ Fonctions utilitaires disponibles

```php
// Vérification de connexion
estConnecte()                          // true si connecté

// Vérification d'habilitation
estVisiteur()                          // true si visiteur
estDelegue()                           // true si délégué
estResponsable()                       // true si responsable
aHabilitation($habId)                  // true si a l'habilitation exacte
aHabilitationMinimum($niveau)          // true si a au moins ce niveau

// Vérification d'actions
peutEffectuerAction($module, $action)  // true si peut faire l'action
verifierAcces($habsAutorisees, $redirect) // Vérifie et redirige si refus

// Récupération d'infos
getMatriculeUtilisateur()              // Matricule connecté
getRegionUtilisateur()                 // Région du connecté
getNomHabilitation($habId)             // Nom de l'habilitation
```

## 🔄 Flux de connexion

1. Utilisateur se connecte via `c_connexion.php`
2. Les informations sont stockées en session :
   - `$_SESSION['login']` = ID login
   - `$_SESSION['habilitation']` = ID habilitation (1, 2 ou 3)
   - `$_SESSION['matricule']` = Matricule
   - `$_SESSION['region']` = Code région
3. Chaque page vérifie les droits via les fonctions d'habilitation
4. Si refus : redirection avec message d'erreur

## 🎯 Points d'attention

1. **Toujours vérifier côté serveur** : Ne jamais se fier uniquement à l'interface
2. **Filtrage des données** : Un délégué ne doit voir QUE sa région
3. **Messages clairs** : Informer l'utilisateur pourquoi l'accès est refusé
4. **Logging** : Envisager de logger les tentatives d'accès non autorisées

## 📝 TODO Liste

- [ ] Modifier `controleur/c_praticien.php` pour ajouter toutes les vérifications
- [ ] Modifier `controleur/c_rapport.php` pour gérer les habilitations
- [ ] Adapter les vues pour afficher/cacher les options selon droits
- [ ] Modifier `index.php` pour vérifier l'accès à chaque module
- [ ] Tester chaque rôle avec des comptes différents
- [ ] Créer une page d'erreur spécifique pour les accès refusés
- [ ] Documenter les comptes de test pour chaque rôle

## 🧪 Comptes de test

basé sur la base de données, tous les utilisateurs actuels sont des Visiteurs (HAB_ID = 1).
Pour tester :
1. Créer des comptes test avec HAB_ID = 2 (délégué)
2. Créer des comptes test avec HAB_ID = 3 (responsable)
3. Affecter des régions différentes aux délégués

Example SQL :
```sql
-- Promouvoir un utilisateur en délégué de la région BG
UPDATE collaborateur SET HAB_ID = 2, REG_CODE = 'BG' WHERE COL_MATRICULE = 'a131';

-- Promouvoir un utilisateur en responsable
UPDATE collaborateur SET HAB_ID = 3 WHERE COL_MATRICULE = 'a17';
```
