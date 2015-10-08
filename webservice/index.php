<?php
	/* Define o arquivo de logs do php */
	ini_set("error_log", "php.log");
	
	/* Cabeçalhos */
	header("Content-Type: application/json");
	header('Access-Control-Allow-Origin: *');

	/* Configurações de conexão, cache e localização do recomendador */
	require_once("settings.php");  

	/* Função para exibir mensagens de erro */
	function error($msg) {
		echo json_encode(array("action" => "error", "error" => $msg));
		exit;   
  	}

  	/* Verifica se há os dados de ação e id de usuário */
	if(!isset($_GET["action"]) || !isset($_GET["iduser"])) {
		error("action or iduser undefined.");
	}

	$ACTION = $_GET["action"];
	$USER = $_GET["iduser"];

	/* Realiza a conexão ao banco de dados */
	$mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DATA);

	/* Em caso de erro de conexão ao banco de dados, retorna um erro */
	if ($mysqli->connect_errno) {
		error("not connected to database.");
	}  

	/* Verifica a ação a ser tomada */
	switch ($ACTION) {
		/* Recomendação */
		case "recommender":
			require_once("recommender.php");
			break;

		/* Ação desconhecida ou não implementada */
		default:
			error("unknown action.");
	}

	/* Fecha a conexão com o banco de dados */
	$mysqli->close();
?>