-- ============================================================
-- Script de gestion des habilitations - GSB
-- ============================================================
-- Ce script permet de créer des comptes de test pour chaque rôle
-- et de modifier les habilitations des utilisateurs existants

--  1. VÉRIFIER LES HABILITATIONS ACTUELLES
-- ============================================================
SELECT 
    c.COL_MATRICULE,
    c.COL_NOM,
    c.COL_PRENOM,
    h.HAB_LIB as Habilitation,
    r.REG_NOM as Region,
    s.SEC_LIBELLE as Secteur
FROM collaborateur c
LEFT JOIN habilitation h ON c.HAB_ID = h.HAB_ID
LEFT JOIN region r ON c.REG_CODE = r.REG_CODE
LEFT JOIN secteur s ON c.SEC_CODE = s.SEC_CODE
ORDER BY c.HAB_ID, c.COL_NOM;

-- ============================================================
-- 2. PROMOUVOIR DES UTILISATEURS POUR LES TESTS
-- ============================================================

-- Promouvoir un visiteur en Délégué Régional de Bretagne
-- Login: villou | Mot de passe: VilLou!
UPDATE collaborateur 
SET HAB_ID = 2, REG_CODE = 'BG' 
WHERE COL_MATRICULE = 'a131';

-- Promouvoir un visiteur en Responsable Secteur
-- Login: anddav | Mot de passe: AndDav!
UPDATE collaborateur 
SET HAB_ID = 3, SEC_CODE = 'E'
WHERE COL_MATRICULE = 'a17';

-- ============================================================
-- 3. CRÉER DES COMPTES DE TEST SUPPLÉMENTAIRES (Optionnel)
-- ============================================================

-- Si vous voulez créer de nouveaux comptes de test :

-- Exemple: Créer un délégué pour la région Occitanie
/*
UPDATE collaborateur 
SET HAB_ID = 2, REG_CODE = 'OC'
WHERE COL_MATRICULE = 'a55';
*/

-- Exemple: Créer un délégué pour la région Île-de-France
/*
UPDATE collaborateur 
SET HAB_ID = 2, REG_CODE = 'IF'
WHERE COL_MATRICULE = 'a93';
*/

-- ============================================================
-- 4. RÉTABLIR TOUS LES UTILISATEURS EN VISITEURS (Reset)
-- ============================================================
-- ATTENTION: Ceci remet TOUS les utilisateurs en visiteurs
-- Décommentez seulement si vous voulez faire un reset complet

/*
UPDATE collaborateur SET HAB_ID = 1;
*/

-- ============================================================
-- 5. VÉRIFICATION POST-MODIFICATION
-- ============================================================
SELECT 
    c.COL_MATRICULE,
    c.COL_NOM,
    c.COL_PRENOM,
    l.LOG_LOGIN,
    h.HAB_LIB as Habilitation,
    r.REG_NOM as Region
FROM collaborateur c
LEFT JOIN login l ON c.COL_MATRICULE = l.COL_MATRICULE
LEFT JOIN habilitation h ON c.HAB_ID = h.HAB_ID
LEFT JOIN region r ON c.REG_CODE = r.REG_CODE
WHERE c.HAB_ID IN (2, 3)
ORDER BY c.HAB_ID DESC, c.COL_NOM;

-- ============================================================
-- 6. COMPTES DE TEST RECOMMANDÉS
-- ============================================================
/*
Configuration recommandée pour les tests:

VISITEUR (HAB_ID = 1):
  - Matricule: b13
  - Login: benpas
  - Mot de passe: BenPas!
  - Région: Grand Est (GE)

DÉLÉGUÉ RÉGIONAL (HAB_ID = 2):
  - Matricule: a131
  - Login: villou
  - Mot de passe: VilLou!
  - Région: Bretagne (BG)

RESPONSABLE SECTEUR (HAB_ID = 3):
  - Matricule: a17
  - Login: anddav
  - Mot de passe: AndDav!
  - Secteur: E (Est)
*/

-- ============================================================
-- 7. STATISTIQUES PAR HABILITATION
-- ============================================================
SELECT 
    h.HAB_LIB as Habilitation,
    COUNT(*) as Nombre_Utilisateurs
FROM collaborateur c
JOIN habilitation h ON c.HAB_ID = h.HAB_ID
GROUP BY h.HAB_LIB
ORDER BY h.HAB_ID;

-- ============================================================
-- 8. LISTER LES RÉGIONS ET LEURS DÉLÉGUÉS
-- ============================================================
SELECT 
    r.REG_CODE,
    r.REG_NOM as Region,
    GROUP_CONCAT(CONCAT(c.COL_PRENOM, ' ', c.COL_NOM) SEPARATOR ', ') as Delegues
FROM region r
LEFT JOIN collaborateur c ON r.REG_CODE = c.REG_CODE AND c.HAB_ID = 2
GROUP BY r.REG_CODE, r.REG_NOM
HAVING Delegues IS NOT NULL
ORDER BY r.REG_NOM;

-- ============================================================
-- 9. VÉRIFIER LA CONNEXION D'UN UTILISATEUR
-- ============================================================
-- Remplacer 'villou' par le login à tester
SELECT 
    l.LOG_LOGIN as Login,
    c.COL_NOM as Nom,
    c.COL_PRENOM as Prenom,
    h.HAB_LIB as Habilitation,
    r.REG_NOM as Region,
    s.SEC_LIBELLE as Secteur
FROM login l
JOIN collaborateur c ON l.COL_MATRICULE = c.COL_MATRICULE
LEFT JOIN habilitation h ON c.HAB_ID = h.HAB_ID
LEFT JOIN region r ON c.REG_CODE = r.REG_CODE  
LEFT JOIN secteur s ON c.SEC_CODE = s.SEC_CODE
WHERE l.LOG_LOGIN = 'villou';

-- ============================================================
-- 10. AFFICHER TOUS LES PRATICIENS PAR RÉGION
-- ============================================================
-- Utile pour tester que les délégués ne voient que leur région
SELECT 
    d.REG_CODE,
    d.Departement,
    COUNT(p.PRA_NUM) as Nombre_Praticiens
FROM departement d
LEFT JOIN praticien p ON LEFT(p.PRA_CP, 2) = LPAD(d.NoDEPT, 2, '0')
GROUP BY d.REG_CODE, d.Departement
HAVING Nombre_Praticiens > 0
ORDER BY d.REG_CODE;
