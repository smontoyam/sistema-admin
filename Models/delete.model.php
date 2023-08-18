<?php

require_once "connection.php";
require_once "get.model.php";


class DeleteModel{

	/*=============================================
	Peticion Delete para eliminar datos de forma dinámica
	=============================================*/

	static public function deleteData($table,$id,$nameId){

	/*=============================================
	Validar que el ID si exista en la bd
	=============================================*/

	$response = GetModel::getDataFilter($table, $nameId, $nameId, $id, null,null,null,null);

	if(empty($response)){

		$response = array(

				"comment" => "El Id no se encontro en la base de datos"
			);

			return $response;

	}

	/*=============================================
	Eliminar registros en la bd
	=============================================*/

		$sql = "DELETE FROM $table WHERE $nameId = :$nameId";

		$link = Connection::connect();
		$stmt = $link->prepare($sql);

		$stmt->bindParam(":".$nameId, $id, PDO::PARAM_STR);

		if($stmt -> execute()){

			$response = array(

				"comment" => "Eliminado correctamente."
			);

			return $response;

		}else{

			return $link->errorInfo();

		}

		
	}

}
