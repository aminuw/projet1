<?php

/**
 * Gestion simplifiée des habilitations GSB
 * 
 * Rôles :
 * - HAB_ID = 1 : Visiteur
 * - HAB_ID = 2 : Délégué Régional  
 * - HAB_ID = 3 : Responsable Secteur
 */

// Constantes des habilitations
define('HAB_VISITEUR', 1);
define('HAB_DELEGUE', 2);
define('HAB_RESPONSABLE', 3);

/**
 * Vérifie si l'utilisateur est Visiteur
 */
function estVisiteur()
{
    return isset($_SESSION['habilitation']) && $_SESSION['habilitation'] == HAB_VISITEUR;
}

/**
 * Vérifie si l'utilisateur est Délégué Régional
 */
function estDelegue()
{
    return isset($_SESSION['habilitation']) && $_SESSION['habilitation'] == HAB_DELEGUE;
}

/**
 * Vérifie si l'utilisateur est Responsable Secteur
 */
function estResponsable()
{
    return isset($_SESSION['habilitation']) && $_SESSION['habilitation'] == HAB_RESPONSABLE;
}

/**
 * Retourne le nom de l'habilitation
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
