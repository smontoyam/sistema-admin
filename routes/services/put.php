<?php

require_once "models/connection.php";
require_once "controllers/put.controller.php";

if(isset($_GET["id"]) && isset($_GET["nameId"])){

	/*=============================================
	Capturamos los datos del formulario
	=============================================*/

	$data = array();
	
	parse_str(file_get_contents('php://input'), $data); // parse_str convierte una cadena de texto en un arreglo y file_get_contents Transmite un fichero completo a una cadena.
	
	/*===================================
	Separar propiedades en un arreglo
	===================================*/

	$columns = array();
	

	foreach(array_keys($data) as $key => $value){

		array_push($columns,$value);

	}

	array_push($columns,$_GET["nameId"]);

	$columns = array_unique($columns);

	/*===================================
	Validar la tabla y las columnas en la bd
	===================================*/

	if (empty(Connection::getColumnsData($table,$columns))){

		$json = array(
				'status' => 400,
				'result' => "Error: Los campos en el formulario no coinciden con la base de datos"
			);

		echo json_encode($json, http_response_code($json["status"]));

		return;

	}

	if(isset($_GET["token"])){

	/*===================================
	Peticiones PUT para usuarios no autorizados
	===================================*/

		if($_GET["token"] == "no" && isset($_GET["except"])){

				/*===================================
				Validar la tabla y las columnas en la bd
				===================================*/

				$columns = array($_GET["except"]);

				if (empty(Connection::getColumnsData($table,$columns))){

					$json = array(
						'status' => 400,
						'result' => "Error: Los campos en el formulario no coinciden con la base de datos"
					);

					echo json_encode($json, http_response_code($json["status"]));

					return;

				}

				/*===================================
				Solicitamos respuesta del controlador para crear datos en cualquier tabla
				===================================*/

				$response = new PutController();
				$response -> putData($table,$data,$_GET["id"],$_GET["nameId"]);

		/*===================================
		Peticion PUT para usuarios autorizados
		===================================*/

			}else{

			$tableToken = $_GET["table"] ?? "users";
			$suffix = $_GET["suffix"] ?? "user";

			$validate = Connection::tokenValidate($_GET["token"],$tableToken,$suffix);

		/*===================================
		Solicitamos respuesta del controlador para editar datos en cualquier tabla
		===================================*/

			if($validate == "autorizado"){

				$response = new PutController();
				$response -> putData($table,$data,$_GET["id"],$_GET["nameId"]);

			}

			/*===================================
			Error cuando el token ha expirado
			===================================*/

			if($validate == "expirado"){

				$json = array(

					'status' => 303,
					'result' => 'Error: El token a expirado'

				);

				echo json_encode($json, http_response_code($json["status"]));

				return;

				}
			}

		/*===================================
		Error cuando el token no coincide en la bd
		===================================*/

		if($validate == "no-autorizado"){

			$json = array(

				'status' => 400,
				'result' => 'Error: El usuario no esta autorizado'

			);

			echo json_encode($json, http_response_code($json["status"]));

			return;

			}

	/*===================================
	Error cuando no envia token 
	===================================*/
		
	}else{

		$json = array(

			'status' => 400,
			'result' => 'Error: Autorizacion requerida'

		);

		echo json_encode($json, http_response_code($json["status"]));

		return;

	}

}