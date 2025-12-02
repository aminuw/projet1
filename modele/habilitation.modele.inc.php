<?php

/**
 * Fichier de gestion des habilitations et droits d'accès
 * 
 * Rôles disponibles:
 * - HAB_ID = 1 : Visiteur (accès limité à ses propres données)
 * - HAB_ID = 2 : Délégué Régional (accès aux données de sa région)
 * - HAB_ID = 3 : Responsable Secteur (accès complet)
 */

// Constantes pour les habilitations
define('HAB_VISITEUR', 1);
define('HAB_DELEGUE', 2);
define('HAB_RESPONSABLE', 3);

/**
 * Vérifie si l'utilisateur est connecté
 * @return bool
 */
function estConnecte()
{
    return isset($_SESSION['login']) && !empty($_SESSION['login']);
}

/**
 * Vérifie si l'utilisateur a une habilitation spécifique
 * @param int $habilitationRequise ID de l'habilitation requise
 * @return bool
 */
function aHabilitation($habilitationRequise)
{
    if (!estConnecte()) {
        return false;
    }
    return isset($_SESSION['habilitation']) && $_SESSION['habilitation'] == $habilitationRequise;
}

/**
 * Vérifie si l'utilisateur a au moins le niveau d'habilitation requis
 * @param int $niveauMinimum Niveau minimum requis
 * @return bool
 */
function aHabilitationMinimum($niveauMinimum)
{
    if (!estConnecte()) {
        return false;
    }
    return isset($_SESSION['habilitation']) && $_SESSION['habilitation'] >= $niveauMinimum;
}

/**
 * Vérifie si l'utilisateur est Visiteur
 * @return bool
 */
function estVisiteur()
{
    return aHabilitation(HAB_VISITEUR);
}

/**
 * Vérifie si l'utilisateur est Délégué Régional
 * @return bool
 */
function estDelegue()
{
    return aHabilitation(HAB_DELEGUE);
}

/**
 * Vérifie si l'utilisateur est Responsable Secteur
 * @return bool
 */
function estResponsable()
{
    return aHabilitation(HAB_RESPONSABLE);
}

/**
 * Récupère la région de l'utilisateur connecté
 * @return string|null Code de la région ou null
 */
function getRegionUtilisateur()
{
    return isset($_SESSION['region']) ? $_SESSION['region'] : null;
}

/**
 * Récupère le matricule de l'utilisateur connecté
 * @return string|null Matricule ou null
 */
function getMatriculeUtilisateur()
{
    return isset($_SESSION['matricule']) ? $_SESSION['matricule'] : null;
}

/**
 * Vérifie l'accès à une fonctionnalité selon les habilitations
 * @param array $habilitationsAutorisees Liste des habilitations autorisées
 * @param string $redirectionSiRefus URL de redirection en cas de refus (optionnel)
 * @return bool
 */
function verifierAcces($habilitationsAutorisees, $redirectionSiRefus = null)
{
    if (!estConnecte()) {
        if ($redirectionSiRefus) {
            header("Location: $redirectionSiRefus");
            exit();
        }
        return false;
    }

    $habilitationUtilisateur = $_SESSION['habilitation'];

    if (!in_array($habilitationUtilisateur, $habilitationsAutorisees)) {
        if ($redirectionSiRefus) {
            $_SESSION['erreur_acces'] = "Vous n'avez pas les droits nécessaires pour accéder à cette page.";
            header("Location: $redirectionSiRefus");
            exit();
        }
        return false;
    }

    return true;
}

/**
 * Récupère le nom de l'habilitation
 * @param int $habId ID de l'habilitation
 * @return string
 */
function getNomHabilitation($habId)
{
    switch ($habId) {
        case HAB_VISITEUR:
            return 'Visiteur';
        case HAB_DELEGUE:
            return 'Délégué Régional';
        case HAB_RESPONSABLE:
            return 'Responsable Secteur';
        default:
            return 'Inconnu';
    }
}

/**
 * Définition des droits par module
 * Retourne un tableau associatif des modules et leurs droits
 */
function getMatriceHabilitations()
{
    return [
        'praticien' => [
            'consulter_tous' => [HAB_RESPONSABLE],
            'consulter_region' => [HAB_DELEGUE, HAB_RESPONSABLE],
            'modifier' => [HAB_DELEGUE, HAB_RESPONSABLE],
            'ajouter' => [HAB_DELEGUE, HAB_RESPONSABLE],
            'supprimer' => [HAB_RESPONSABLE]
        ],
        'rapport' => [
            'consulter_propres' => [HAB_VISITEUR, HAB_DELEGUE, HAB_RESPONSABLE],
            'consulter_region' => [HAB_DELEGUE, HAB_RESPONSABLE],
            'consulter_tous' => [HAB_RESPONSABLE],
            'modifier_propres' => [HAB_VISITEUR, HAB_DELEGUE, HAB_RESPONSABLE],
            'modifier_region' => [HAB_DELEGUE, HAB_RESPONSABLE],
            'modifier_tous' => [HAB_RESPONSABLE],
            'valider' => [
                HAB_DELEGUE,
                HAB_RESPONSABLE
            ],
            'ajouter' => [HAB_VISITEUR, HAB_DELEGUE, HAB_RESPONSABLE]
        ],
        'consultation' => [
            'consulter_propres' => [HAB_VISITEUR, HAB_DELEGUE, HAB_RESPONSABLE],
            'consulter_region' => [HAB_DELEGUE, HAB_RESPONSABLE],
            'consulter_tous' => [HAB_RESPONSABLE]
        ],
        'medicament' => [
            'consulter' => [HAB_VISITEUR, HAB_DELEGUE, HAB_RESPONSABLE],
            'modifier' => [HAB_RESPONSABLE]
        ]
    ];
}

/**
 * Vérifie si l'utilisateur a le droit d'effectuer une action sur un module
 * @param string $module Nom du module
 * @param string $action Nom de l'action
 * @return bool
 */
function peutEffectuerAction($module, $action)
{
    $matrice = getMatriceHabilitations();

    if (!isset($matrice[$module]) || !isset($matrice[$module][$action])) {
        return false;
    }

    $habilitationsAutorisees = $matrice[$module][$action];
    $habilitationUtilisateur = $_SESSION['habilitation'] ?? null;

    return in_array($habilitationUtilisateur, $habilitationsAutorisees);
}

/**
 * Affiche un message d'erreur si l'accès est refusé
 */
function afficherErreurAcces()
{
    if (isset($_SESSION['erreur_acces'])) {
        $message = $_SESSION['erreur_acces'];
        unset($_SESSION['erreur_acces']);
        return $message;
    }
    return null;
}
