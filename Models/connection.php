<?php

require_once "get.model.php";

class Connection{

	/*===================================
	Información de la base de datos
	===================================*/

	static public function  infoDatabase(){

		 $infoDB = array(

		 	"database" => "database-1",
		 	"user" => "root",
		 	"pass" => ""

		 );

	return $infoDB;

	}

	/*===================================
	APIKEY
	===================================*/

	static public function apikey(){

		return "3VhXW768m2GJ2fp";

	}

	/*===================================
	Acceso público
	===================================*/
	static public function publicAccess(){

		$tables = ["courses"];         //Aqui van las tablas a las que daremos acceso publico a la bd

		return $tables;
	}

	/*===================================
	Conexión a la base de datos
	===================================*/

	static public function connect(){

		try{

			$link = new PDO(
				"mysql:host=localhost;dbname=".Connection::infoDatabase()["database"],
				Connection::infoDatabase()["user"],
				Connection::infoDatabase()["pass"]
			);

			$link->exec("set names utf8");

		}catch(PDOEcception $e){

			die("Error: ".$e->getMessage());

		}

		return $link;

	}

	/*===================================
	Funcion para validar la existencia de una tabla en la base de datos
	===================================*/
	static public function getColumnsData($table,$columns){

	/*===================================
	Traer el nombre de la base de datos
	===================================*/

		$database = Connection::infoDatabase()["database"];

	/*===================================
	Traer todas las columnas de una tabla
	===================================*/

		$validate = Connection::connect()
		->query("SELECT COLUMN_NAME AS item FROM information_schema.columns WHERE table_schema = '$database' AND table_name = '$table'")
		->fetchAll(PDO::FETCH_OBJ);

	/*===================================
	Validamos existencia de la tabla
	===================================*/

		if(empty($validate)){

			return null;

		}else{

	/*===================================
	Ajuste de seleccion de columnas globales
	===================================*/
		if($columns[0] == "*"){

			array_shift($columns);
		}


	/*===================================
	Validamos existencia de columnas
	===================================*/

			$sum = 0;

			foreach ($validate as $key => $value) {

			$sum += in_array($value->item,$columns);

			}

			return $sum == count($columns) ? $validate : null;

		}

	}

	/*===================================
	Generar token de autenticacion 
	===================================*/

	static public function jwt($id,$email){

		$time = time();

		$token = array(

			"iat" => $time, //tiempo que inicia el token
			"exp" => $time + (60*60*24), // Tiempo en que expira el token, en este caso 1 dia
			"data" => [

				"id" => $id,
				"email" => $email,
				"alg" => "HS256",

			]

		);

		return $token;

	}

	/*===================================
	Validar el token de seguridad
	===================================*/

	static public function tokenValidate($token,$table,$suffix){

	/*===================================
	Traemos al usuario de acuerdo al token
	===================================*/
	$user = GetModel::getDataFilter($table,"token_exp_".$suffix,"token_".$suffix,$token,null,null,null,null);


	if(!empty($user)){

	/*===================================
	Validamos que el token no haya expirado
	===================================*/

			$time = time();

			if($time < $user[0]->{"token_exp_".$suffix}){

				return "autorizado";

			}else{

				return "expirado";
			}

		}else{

			return "no-autorizado";
		}

	}

}