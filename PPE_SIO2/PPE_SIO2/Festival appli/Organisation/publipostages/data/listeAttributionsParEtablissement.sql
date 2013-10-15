SELECT 
	Etablissement.id idEtab, Etablissement.nom nomEtab,
	Groupe.id idGroupe, Groupe.nom nomGroupe,
	 Attribution.nombreChambres  Attributions 
FROM 
	Groupe , Etablissement, Attribution
WHERE
	Etablissement.id = Attribution.idEtab
			
AND
	Groupe.id=Attribution.idGroupe
GROUP BY
	Etablissement.id, 
	Etablissement.nom,
	Groupe.id,
	 Groupe.nom 
	Attribution.nombreChambres ;
	