<?php

require_once "models/delete.model.php";

class DeleteController{

	/*===================================
	Peticion Delete para eliminar datos de la bd
	===================================*/
	static public function deleteData($table,$id,$nameId){

		$response = DeleteModel::deleteData($table,$id,$nameId);


		$return = new DeleteController();
		$return -> fncResponse($response);

	}

	/*=============================================
	Respuestas del controlador
	=============================================*/

	public function fncResponse($response){

		if(!empty($response)){

			$json = array(

				'status' => 200,
				'results' => $response

			);

		}else{

			$json = array(

				'status' => 404,
				'results' => 'Not Found',
				'method' => 'Delete'

			);

		}

		echo json_encode($json, http_response_code($json["status"]));

	}

}