<?php
  	/* Verifica o tipo de recomendação */
	if(!isset($_GET["type"])) {
		error("type undefined.");
	}

	$TYPE = $_GET["type"];

	/* Verifica se o usuário existe na base de dados e qual sua categoria */
	$query = $mysqli->query("
		SELECT
			ID_Cat 'ID_CATEGORY'
		FROM 
			user_cat_dat
		WHERE 
			(ID_User = {$USER})
		ORDER BY (ID_Cat)
		LIMIT 1
	");

	if(!$query->num_rows) {
		error("user or category not found.");
	}

	$ID_CATEGORY = $query->fetch_object()->ID_CATEGORY;
	$query->close();

	switch ($TYPE) {
		case "discipline":
			require_once("recommender.discipline.php");
			break;
		case "general":
			require_once("recommender.general.php");
			break;	
		default:
			error("unknown type of recommendation.");
	}

	/* Verifica se há ratings do usuário */
	$query = $mysqli->query($QUERY1);	
	$NUM_OBJECT = $query->fetch_object()->NUM_OBJECT;
	$query->close();
	if(!$NUM_OBJECT) {
		error("user not evaluated objects.");
	}

	/* Localização do arquivo de ratings */
	$PATH_RATING = "rating";
	if (!file_exists($PATH_RATING)) {
		mkdir($PATH_RATING);	
	}

	$FILENAME_RATING = "{$PATH_RATING}/{$ID_CATEGORY}.csv";

	/* 
		Se o cache não estiver habilitado ou o arquivo de ratings da disciplina 
	 	do usuário não exitir, então realiza a consulta e gera o arquivo
	*/
	if (!CACHE || !file_exists($FILENAME_RATING)) {
		$query = $mysqli->query($QUERY2);
		$fp = fopen($FILENAME_RATING, "w");
		$i = 0;
		while($result = $query->fetch_object()) {
			if($i != 0) {
				fwrite($fp, "\r\n");				
			}
			fwrite($fp, "{$result->ID_USER},{$result->ID_OBJECT},{$result->RATING}");
			$i++;
		}
		fclose($fp);
		$query->close();
	}

	/* Mensagem de resposta */
	$reply = array("action" => "recommender", "recommender" => array(
		"objects" => array(),
		"mae" => "",
		"rms" => ""
	));

	/* Função que executa o recomendador */
	function recommender($USER, $RATINGS, $ERRORS = "false") {
		/* Executa o recomendador */
		exec("java -jar ".RECOMMENDER." {$USER} {$RATINGS} {$ERRORS}", $output, $reval);	

		/* Verifica se houve algum erro no recomendador */
		if($reval) {
			error("Merlot-Recommender error {$reval}.");
		}

		/* Pega a saída do recomendador e transforma em um objeto */
		return json_decode(stripslashes($output[0]));
	}

	/* Verifica se há dados de erros salvos na base de dados */
	$query = $mysqli->query("
		SELECT
			MAE, 
			RMS
		FROM
			app_errors
		WHERE 
			(idcategory = {$ID_CATEGORY})
		LIMIT 1
	");

	/*
		Tenta fazer uma cache dos dados de erro por disciplina, fazendo com que 
		demore somente na primeira execução por recomendações da disciplina
	*/
	if (!CACHE || !$query->num_rows) {
		$query->close();

		/* Executa o recomendador */
		$out = recommender($USER, $FILENAME_RATING, "true");

		$reply["recommender"]["mae"] = round($out->mae, 2);
		$reply["recommender"]["rms"] = round($out->rms, 2);

		if (CACHE) {
			/* Insere na base de dados os valores de erro */
			$query = $mysqli->query("INSERT INTO app_errors VALUES ('{$ID_CATEGORY}', '".round($out->mae, 2)."', '".round($out->rms, 2)."')");
		} else {
			/* Se o cache não estiver habilitado, apaga o arquivo de ratings */
			unlink($FILENAME_RATING);
		}
	} else {
		/* Pega os dados de erro */
		$APP_ERRORS = $query->fetch_object();
		$reply["recommender"]["mae"] = $APP_ERRORS->MAE;
		$reply["recommender"]["rms"] = $APP_ERRORS->RMS;
		$query->close();

		/* Executa o recomendador */
		$out = recommender($USER, $FILENAME_RATING, "false");
	}

	/* Verifica se o recomendador recomendou objetos para o usuário */
	if (!count($out->objects)) {
		error("Merlot-Recommender not recommended objects.");	
	}

	/* Monta a mensagem de resposta para o usuário, com detalhes dos objetos recomendados */
	foreach ($out->objects as $rec) {
		$query = $mysqli->query("
			SELECT
				Title, Description, Location
			FROM lodata 
			WHERE 
				(ID_LO = {$rec->idobject})
			LIMIT 1
		");

		$OBJECT = $query->fetch_object();

		$reply["recommender"]["objects"][] = array(
			"idobject" => $rec->idobject,
			"title" => $OBJECT->Title,
			"description" => $OBJECT->Description,
			"location" => $OBJECT->Location,
			"value" => round($rec->value)
		);

		$query->close();
	}

	/* Exibe na tela a mensagem de resposta */
	echo json_encode($reply);
?>