-- Mise à jour pour donner un secteur au responsable a55
UPDATE collaborateur 
SET SEC_CODE = 'S', REG_CODE = 'OC'
WHERE COL_MATRICULE = 'a55';

-- Vérification
SELECT COL_MATRICULE, COL_NOM, COL_PRENOM, HAB_ID, SEC_CODE, REG_CODE 
FROM collaborateur 
WHERE HAB_ID = 3;
