<?php

require_once "models/connection.php";
require_once "controllers/delete.controller.php";

if(isset($_GET["id"]) && isset($_GET["nameId"])){

	$columns = array($_GET["nameId"]);

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

	/*===================================
	Peticiones DELETE para usuarios autorizados
	===================================*/

	if(isset($_GET["token"])){

		$tableToken = $_GET["table"] ?? "users";
		$suffix = $_GET["suffix"] ?? "user";

		$validate = Connection::tokenValidate($_GET["token"],$tableToken,$suffix);

	/*===================================
		Solicitamos respuesta del controlador para eliminar datos en cualquier tabla de la bd
		=========*/

		if($validate == "autorizado"){

			$response = new DeleteController();
			$response -> deleteData($table,$_GET["id"],$_GET["nameId"]);

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