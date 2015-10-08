<?php
	/* Query para verificar se há ratings do usuário */
	$QUERY1 = "
		SELECT 
			COUNT(ID_LO) 'NUM_OBJECT'
		FROM 
			lovaloration INNER JOIN loval_com ON ID_Rating = ID_Valoration
			NATURAL JOIN locomments
			NATURAL JOIN locat_dat
		WHERE
			((users_ID_User = {$USER}) AND (Rating != 0) AND (ID_Cat = {$ID_CATEGORY})) 
	";
	
	/* Query para gerar o arquivo de ratings */
	$QUERY2 = "
		SELECT DISTINCT 
			users_ID_User 'ID_USER', 
			ID_LO 'ID_OBJECT', 
			Rating 'RATING'
		FROM 
			lovaloration INNER JOIN loval_com ON ID_Rating = ID_Valoration
			NATURAL JOIN locomments
			NATURAL JOIN locat_dat
		WHERE
			((users_ID_User != 0) AND (Rating != 0) AND (ID_Cat = {$ID_CATEGORY})) 
		ORDER BY (users_ID_User)
	";
?>