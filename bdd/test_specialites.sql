-- Script de test pour vérifier la structure de la base de données
-- Ce script permet de vérifier que la table posseder est correctement configurée

-- 1. Vérifier la structure de la table posseder
DESCRIBE posseder;

-- 2. Afficher toutes les spécialités disponibles
SELECT * FROM specialite;

-- 3. Afficher un exemple de praticien avec ses spécialités
SELECT 
    p.PRA_NUM,
    p.PRA_NOM,
    p.PRA_PRENOM,
    GROUP_CONCAT(s.SPE_LIBELLE SEPARATOR ', ') as Specialites
FROM praticien p
LEFT JOIN posseder po ON p.PRA_NUM = po.PRA_NUM
LEFT JOIN specialite s ON po.SPE_CODE = s.SPE_CODE
GROUP BY p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM
LIMIT 5;

-- 4. Compter le nombre de spécialités par praticien
SELECT 
    p.PRA_NUM,
    p.PRA_NOM,
    p.PRA_PRENOM,
    COUNT(po.SPE_CODE) as Nombre_Specialites
FROM praticien p
LEFT JOIN posseder po ON p.PRA_NUM = po.PRA_NUM
GROUP BY p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM
HAVING Nombre_Specialites > 0
ORDER BY Nombre_Specialites DESC;
