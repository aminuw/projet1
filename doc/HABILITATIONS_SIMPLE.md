# Gestion Simplifiée des Habilitations - GSB

## Les 3 Rôles

| HAB_ID | Rôle | Description |
|--------|------|-------------|
| 1 | **Visiteur** | Accès de base, consultation uniquement |
| 2 | **Délégué Régional** | Gestion des praticiens de sa région |
| 3 | **Responsable Secteur** | Accès complet |

## Fonctions Disponibles

### Vérifier le rôle
```php
estVisiteur()      // Retourne true si l'utilisateur est visiteur
estDelegue()       // Retourne true si l'utilisateur est délégué
estResponsable()   // Retourne true si l'utilisateur est responsable
```

### Obtenir le nom du rôle
```php
getNomHabilitation($_SESSION['habilitation']);
// Retourne: "Visiteur", "Délégué Régional" ou "Responsable Secteur"
```

## Utilisation dans le Header

```php
<!-- Afficher uniquement pour Responsable -->
<?php if (estResponsable()): ?>
    <li>Menu Admin</li>
<?php endif; ?>

<!-- Afficher pour Délégué et Responsable -->
<?php if (estDelegue() || estResponsable()): ?>
    <li>Gérer Praticiens</li>
<?php endif; ?>
```

## Utilisation dans les Contrôleurs

```php
// Vérifier l'accès dans un contrôleur
if (!estDelegue() && !estResponsable()) {
    $_SESSION['erreur'] = true;
    header("Location: index.php?uc=accueil");
    exit();
}
```

## Droits par Fonctionnalité

### Menu Gérer Praticien
- **Tous Praticiens** → Responsable uniquement
- **Praticien par Région** → Délégué + Responsable
- **Ajouter Praticien** → Délégué + Responsable

### Rapports
- **Saisir** → Tous
- **Consulter** → Tous (filtrage côté serveur selon le rôle)

### Médicaments
- **Consulter** → Tous

## Fichiers Concernés

- **modele/habilitation.modele.inc.php** - Fonctions d'habilitation
- **vues/v_header.php** - Menu avec conditions d'affichage
- **controleur/c_praticien.php** - Vérifications d'accès
