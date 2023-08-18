<?php

require_once "connection.php";
require_once "get.model.php";


class PutModel{

	/*=============================================
	Peticion Put para modificar datos de forma dinámica
	=============================================*/

	static public function putData($table,$data,$id,$nameId){

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
	Actualizar registrtos en la bd
	=============================================*/

		$set = "";
		
		foreach ($data as $key => $value) {

			$set .= $key." = :".$key.",";
		}

		$set = substr($set, 0, -1);

		$sql = "UPDATE $table SET $set WHERE $nameId = :$nameId";

		$link = Connection::connect();
		$stmt = $link->prepare($sql);

		foreach ($data as $key => $value) {

			$stmt->bindParam(":".$key, $data[$key], PDO::PARAM_STR);
		}

		$stmt->bindParam(":".$nameId, $id, PDO::PARAM_STR);

		if($stmt -> execute()){

			$response = array(

				"comment" => "La actualización fue correcta."
			);

			return $response;

		}else{

			return $link->errorInfo();

		}

		
	}

}
