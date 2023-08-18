<?php

require_once "models/connection.php";
require_once "controllers/get.controller.php";

$routesArray = explode("/", $_SERVER['REQUEST_URI']);
$routesArray = array_filter($routesArray);

/*===================================
Cuando no se hacen ninguna peticion a la API
===================================*/

if(count($routesArray) == 0){

	$json = array(

		'status' => 404,
		'results' => 'Not found'

	);

	echo json_encode($json, http_response_code($json["status"]));

	return;

}

/*===================================
Cuando si se hace una peticion a la API
===================================*/

if(count($routesArray) == 1 && isset($_SERVER['REQUEST_METHOD'])){

	$table = explode("?", $routesArray[1])[0];

/*===================================
Validar llave secreta
===================================*/

if(!isset(getallheaders()["Authorization"]) || getallheaders()["Authorization"] != Connection::apikey()){

	if(in_array($table, Connection::publicAccess()) == 0){

		$json = array(

			'status' => 404,
			'results' => 'No estas autorizado para hacer solicitudes'

		);

		echo json_encode($json, http_response_code($json["status"]));

		return;

	}else{

		/*===================================
		Acceso publico
		===================================*/

		$response = new GetController();
		$response -> getData($table,"*",null,null,null,null);

		return;

	}

}

/*===================================
Peticiones GET
===================================*/

	if($_SERVER['REQUEST_METHOD'] == "GET"){

include "services/get.php";

	}

	/*===================================
	Peticiones POST
	===================================*/

	if($_SERVER['REQUEST_METHOD'] == "POST"){

		include "services/post.php";

	}

	/*===================================
	Peticiones PUT
	===================================*/

		if($_SERVER['REQUEST_METHOD'] == "PUT"){

			include "services/put.php";

	}

	/*===================================
	Peticiones DELETE
	===================================*/

		if($_SERVER['REQUEST_METHOD'] == "DELETE"){

			include "services/delete.php";

	}

}


