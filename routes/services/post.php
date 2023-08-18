<?php

require_once "models/connection.php";
require_once "controllers/post.controller.php";

if(isset($_POST)){

	/*===================================
	Separar propiedades en un arreglo
	===================================*/

	$columns = array();

	foreach(array_keys($_POST) as $key => $value){

		array_push($columns,$value);

	}

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

	$response = new PostController();

	/*===================================
	Peticion POST para el registro de usuarios
	===================================*/

	if(isset($_GET["register"]) && $_GET["register"] == true){

		$suffix = $_GET["suffix"] ?? "user";

		$response -> postRegister($table,$_POST,$suffix);

	/*===================================
	Peticion POST para login de usuarios
	===================================*/

	}else if(isset($_GET["login"]) && $_GET["login"] == true){

		$suffix = $_GET["suffix"] ?? "user";

		$response -> postLogin($table,$_POST,$suffix);


	}else{

		if(isset($_GET["token"])){

		/*===================================
		Peticiones POST para usuarios no autorizados
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

				$response -> postData($table,$_POST);

		/*===================================
		Peticiones POST para usuarios autorizados
		===================================*/

			}else{

			$tableToken = $_GET["table"] ?? "users";
			$suffix = $_GET["suffix"] ?? "user";

			$validate = Connection::tokenValidate($_GET["token"],$tableToken,$suffix);

				/*===================================
				Solicitamos respuesta del controlador para crear datos en cualquier tabla
				===================================*/

				if($validate == "autorizado"){

				$response -> postData($table,$_POST);


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

}	
