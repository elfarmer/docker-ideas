<?php
/**
 * Id4Ideas
 *
 */


/**
 * myPDO es un PDO wrapper
 *
 * Prepara (y ejecuta) de manera más amigable las instrucciones
 *   diferentes instrucciones SQL, ralizando incluso algunas verificaciones.
 *
 * @see https://phpdelusions.net/pdo/pdo_wrapper
 */
 class myPDO
{
	protected static $instance;
	protected $pdo;

	public function __construct() {
		$opt = [
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone='".SERVER_TIME_ZONE."', lc_time_names='".TIME_NAMES."';",
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
			PDO::ATTR_EMULATE_PREPARES   => false,
			PDO::ATTR_STRINGIFY_FETCHES  => false,
		];
		$dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHAR;

		try {
			$this->pdo = new PDO($dsn, DB_USER, DB_PASS, $opt);

		} catch ( Exception $e ) {
			error_log("<START>\n= ".date("Y-m-d H:i:s")." [{$_SERVER["REMOTE_ADDR"]}]\n= File: ".__FILE__."\n= Method: ".__METHOD__."\n\n= Caught: $e\n<END>\n\n", 3, PHP_ERRORS_LOG );
			throw $e;

		}

	}

	// a classical static method to make it universally available
	public static function instance()
	{
		if (self::$instance === null)
			self::$instance = new self;

		return self::$instance;
	}

	// a proxy to native methods
	public function __call($method, $args)
	{
		return call_user_func_array(array($this->pdo, $method), $args);
	}


	/**
	 * Obtiene información sobre una determinada tabla.
	 *   METODO AUN NO IMPLEMENTADO
	 *
	 * SHOW FULL COLUMNS FROM $table;  //  SHOW FULL FIELDS FROM $table;
	 * SHOW INDEX FROM $table;
	 *
	 * @param  string $table  Nombre de la table a analizar.
	 * @param  array  $args   sarasa...
	 *
	 * @return  ...  lo que sea que retorne
	 */
	public function getInfo($table, $args = [])
	{
		if ( empty($table) )
			return false;

	}


	/**
	 * Genera, prepara y ejecuta una instrucción SQL arbitraria
	 *
	 *
	 * @param  string $sql   Instrucción SQL a ejecutar.
	 * @param  array  $args  Es un array que contiene tantos "id"=>$valor como parámetros haya en la instrucción SQL.
	 *                         En el caso de que el parámetro pueda recibir múltiples valores -por ejemplo una condición "campo IN ( :ids )"- puede tomar la forma "ids"=>[$valor1, $valor2, ...]
	 *
	 * @return object|integer   Retorna un objeto PDOStatement o la cantidad de registros afectados para instrucciones INSERT, UPDATE o DELETE
	 *
	 * @throws Exception si hay algún error en la ejecución de la instrucción SQL
	 */
	public function run($sql, $args = [])
	{
		try {
			// Si existen parámetros que aceptan múltiples valores re-escribe todo (tanto la instrucción como los parámetros)
			list($sql, $args) = $this->rewriteSql($sql, $args) ;

			$stmt = $this->pdo->prepare($sql);
			$stmt->execute($args);

			if ( in_array(mb_strtoupper(strtok($sql, " ")), ["INSERT","UPDATE","DELETE"]) )
				return $stmt->rowCount();
			else
				return $stmt;

		} catch ( Exception $e ) {
			error_log("<START>\n= ".date("Y-m-d H:i:s")." [{$_SERVER["REMOTE_ADDR"]}]\n= File: ".__FILE__."\n= Method: ".__METHOD__."\n\n= Caught: $e\n<END>\n\n", 3, PHP_ERRORS_LOG );
			throw $e;

		}
	}


	/**
	 * Genera, prepara y ejecuta un SELECT
	 *
	 * Genera una instrucción SELECT, ya sea simple o con bastante nivel de complejidad.
	 * La prepara, ejecuta y retorna un objeto PDOStatement.
	 *
	 *
	 * @param  string $table  Nombre de la tabla.
	 * @param  array  $args   Es un array con esta estructura
	 *                          $args = [
	 *                              "select"      => ...,    // string|array <== Un cadena de la forma "campo1 AS campoX, campo2, ..." o un array de la forma ["campo1 AS campoX", "campo2", ...]
	 *                              "distinct"    => valor,  // boolean      <== Incluye (o no) la cláusula DISTINCT
	 *                              "join"        => ...,    // string|array <== Un cadena de la forma "LEFT JOIN tablaX ON condicionX, ..." o arrays de la forma ["type"=>INNER|LEFT|RIGHT, "table"=>tabla, "condition"=>condicion], ...
	 *                              "where"       => ...,    // string|array <== Un cadena de la forma "condicion1 AND|OR condicion2 AND|OR..." o un array de la forma ["condicion1", "condicion2", ...] que resulta todas unidas por AND
	 *                              "group"       => ...,    // string|array <== Un cadena de la forma "grupo1, grupo2, ..." o un array de la forma ["grupo1", "grupo2", ...]
	 *                              "having"      => ...,    // string|array <== Un cadena de la forma "resultado1 AND|OR resultado2 AND|OR..." o un array de la forma ["resultado1", "resultado2", ...] que resulta todas unidas por AND
	 *                              "order"       => ...,    // string|array <== Un cadena de la forma "orden1, orden2, ..." o un array de la forma ["orden1", "orden2", ...]
	 *                              "limit"       => valor,  // integer      <== Cantidad de registros a devolver
	 *                              "start"       => valor,  // integer      <== Cantidad de registros a partir de la cual debe devolver
	 *                              "return_type" => valor,  // string       <== Si queremos saber la cantidad de registros de la consulta usamos "count". Cualquier otro valor será ignorado.
	 *                                                                             Si especificamos "count" serán omitidos los parámetros "order", "limit" y "start"
	 *                              "args"        => [],     // array        <== Un array de la forma ["name"=>parametro, "value"=>valor, "type"=>BOOL|NULL|INT|STR|STR_NATL|STR_CHAR|LOB|STMT], ...
	 *                                                                             BOOL        PDO::PARAM_BOOL       Represents a boolean data type.
	 *                                                                             NULL        PDO::PARAM_NULL       Represents the SQL NULL data type.
	 *                                                                             INT         PDO::PARAM_INT        Represents the SQL INTEGER data type.
	 *                                                                             STR         PDO::PARAM_STR        Represents the SQL CHAR, VARCHAR, or other string data type.
	 *                                                                             STR_NATL    PDO::PARAM_STR_NATL   Flag to denote a string uses the national character set. Available since PHP 7.2.0
	 *                                                                             STR_CHAR    PDO::PARAM_STR_CHAR   Flag to denote a string uses the regular character set. Available since PHP 7.2.0
	 *                                                                             LOB         PDO::PARAM_LOB        Represents the SQL large object data type.
	 *                                                                             STMT        PDO::PARAM_STMT       Represents a recordset type. Not currently supported by any drivers.
	 *                          ]
	 *
	 * @return false|object   Retorna un objeto PDOStatement o falso (si falta algún parámetro)
	 *
	 * @throws Exception si hay algún error en la ejecución de la instrucción SQL
	 */
	public function getRows($table, $args = [])
	{
		if ( empty($table) )
			return false;

		try {
			// Select
			$sql  = "SELECT " ;

			// Distinct?
			$sql .= (!empty($args["distinct"]) && $args["distinct"]===true?"DISTINCT ":"") ;

			// Fields
			if ( !empty($args["select"]) ) {
				if ( is_array($args["select"]) ) {
					$sql .= implode(", ", $args["select"]) ;
				} else {
					$sql .= $args["select"] ;
				}
			} else {
				$sql .= "*" ;
			}

			// Table
			$sql .= " FROM $table ";

			// Others Tables
			if ( !empty($args["join"]) ) {
				if ( is_array($args["join"]) ) {
					foreach ( $args["join"] as $aEachJoin )
					{
						$sql .= " {$aEachJoin["type"]} JOIN {$aEachJoin["table"]} ON {$aEachJoin["condition"]} ";
					}
				} else {
					$sql .= " {$args["join"]} " ;
				}
			}

			// Filter
			if ( !empty($args["where"]) ) {
				$sql .= " WHERE " ;
				if ( is_array($args["where"]) ) {
					$sql .= implode(" AND ", $args["where"]) ;
				} else {
					$sql .= $args["where"] ;
				}
			}

			// Group
			if ( !empty($args["group"]) ) {
				$sql .= " GROUP BY " ;
				if ( is_array($args["group"]) ) {
					$sql .= implode(", ", $args["group"]) ;
				} else {
					$sql .= $args["group"] ;
				}
			}

			// Having
			if ( !empty($args["having"]) ) {
				$sql .= " HAVING " ;
				if ( is_array($args["having"]) ) {
					$sql .= implode(" AND ", $args["having"]) ;
				} else {
					$sql .= $args["having"] ;
				}
			}


			// Si existen parámetros que aceptan múltiples valores re-escribe todo (tanto la instrucción como los parámetros)
			if ( !isset($args["vars"]) ) $args["vars"] = [];
			list($sql, $args["vars"]) = $this->rewriteSql($sql, $args["vars"]) ;


			// ¿Qué tipo de resultados espera..? ¿Registros o cantidad..?
			if ( !empty($args["return_type"]) && $args["return_type"]=="count" ) {
				// Necesitamos la cantidad total de registros. Reformulo la instrucción.
				$sql = "SELECT COUNT(*) AS qtyRows FROM ( {$sql} ) t" ;

			} else {
				// Necesitamos los registros. Agrego -si corresponde- ORDER BY y LIMIT.

				// Order
				if ( !empty($args["order"]) ) {
					$sql .= " ORDER BY " ;
					if ( is_array($args["order"]) ) {
						$sql .= implode(", ", $args["order"]) ;
					} else {
						$sql .= $args["order"] ;
					}
				}

				// Rows
				if ( !empty($args["limit"]) && !empty($args["start"]) ) {
					$limitVar = uniqid('limit');
					$startVar = uniqid('start');

					$sql .= " LIMIT :{$startVar} , :{$limitVar} " ;

					$args["vars"][] = [ "name"=>$limitVar, "value"=>$args["limit"], "type"=>"INT" ] ;
					$args["vars"][] = [ "name"=>$startVar, "value"=>$args["start"], "type"=>"INT" ] ;

				} elseif ( !empty($args["limit"]) ) {
					$limitVar = uniqid('limit');

					$sql .= " LIMIT :{$limitVar} " ;

					$args["vars"][] = [ "name"=>$limitVar, "value"=>$args["limit"], "type"=>"INT" ] ;

				}

			}


			// Ahora si, preparo la instrucción $sql, hago bind de los valores y ejecuto...
			$stmt = $this->pdo->prepare($sql);
			foreach ( $args["vars"] as $eachVar )
			{
				if ( strpos($sql." ", ":{$eachVar["name"]} ")!==false ) {
					// Sólo hago bind de las variables que efectivamente están presentes en la instrucción Sql final
					if ( in_array(mb_strtoupper($eachVar["type"]), ["BOOL","NULL","INT","STR","STR_NATL","STR_CHAR","LOB"] ) ) {
						$stmt->bindValue(":{$eachVar["name"]}", $eachVar["value"], constant("PDO::PARAM_".strtoupper($eachVar["type"])));
					} else {
						$stmt->bindValue(":{$eachVar["name"]}", $eachVar["value"]);
					}
				}
			}
			$stmt->execute();
			return $stmt;

		} catch ( Exception $e ) {
			error_log("<START>\n= ".date("Y-m-d H:i:s")." [{$_SERVER["REMOTE_ADDR"]}]\n= File: ".__FILE__."\n= Method: ".__METHOD__."\n\n= Caught: $e\n<END>\n\n", 3, PHP_ERRORS_LOG );
			throw $e;

		}
	}


	/**
	 * Genera, prepara y ejecuta un INSERT de un único registro
	 *
	 * Genera una instrucción de la forma "INSERT INTO tabla (campo1, campo2, ...) VALUES (valor1, valor2, ...)" usando los parámtros de $args.
	 * La prepara, ejecuta y retorna la cantidad de elementos insertados.
	 *
	 *
	 * == Limitaciones conocidas ==============================
	 * - Los $valor necesariamente deben ser valores "simples" y no pueden ser expresiones del tipo "NOW()" o "CURDATE() + INTERVAL :days DAY".
	 *   Eventualmente se podría generar un nuevo método ( insertRowV2 ) que permita a $args alamacenar estructuras más complejas, al estilo de updateRows.
	 * - No permite generar "INSERT INTO tabla (campo1, campo2, ...) VALUES (valor1, valor2, ...) ON DUPLICATE KEY campoN=valorN, ...".
	 * - No permite generar "INSERT INTO tabla (campo1, campo2, ...) VALUES (valor1a, valor2a, ...),  (valor1b, valor2b, ...), ..."
	 *   https://phpdelusions.net/pdo_examples/multiple
	 *
	 *
	 * @param  string  $table             Nombre de la tabla.
	 * @param  array   $args              Es un array que contiene tantos "campo"=>$valor como campos quiera insertar en la tabla.
	 * @param  boolean $ignoreNull        Opcional. Determina si incluye (o no) dentro de los campos del INSERT aquellos que tienen valor NULL. Por defecto están incluídos.
	 * @param  boolean $ignoreDuplicates  Opcional. Determina si incluye (o no) la claúsula IGNORE. Por defecto no está incluída.
	 *
	 * @return false|int  Retorna la cantidad de registros insertados ó falso (si falta algún parámetro)
	 *
	 * @throws Exception si hay algún error en la ejecución de la instrucción SQL
	 */
	public function insertRow($table, $args = [], $ignoreNull = false, $ignoreDuplicates = false)
	{
		if ( empty($table) )
			return false;

		try {
			$sql = "INSERT ".( ( !empty($ignoreDuplicates) && $ignoreDuplicates===true ) ? "IGNORE" : "" )." INTO $table (@@FIELD_PLACEHOLDER@@) VALUES (@@VALUE_PLACEHOLDER@@)";

			foreach ($args as $key => $value)
			{
				if ( $ignoreNull && is_null($value) )
					unset($args[$key]);
				else
					$sql = str_replace( ["@@FIELD_PLACEHOLDER@@","@@VALUE_PLACEHOLDER@@"] , ["{$key} , @@FIELD_PLACEHOLDER@@", ":{$key} , @@VALUE_PLACEHOLDER@@"] , $sql);
			}
			$sql = str_replace( [" , @@FIELD_PLACEHOLDER@@"," , @@VALUE_PLACEHOLDER@@"] , "" , $sql);

			$stmt = $this->pdo->prepare($sql);
			$stmt->execute($args);

			return $stmt->rowCount();

		} catch ( Exception $e ) {
			error_log("<START>\n= ".date("Y-m-d H:i:s")." [{$_SERVER["REMOTE_ADDR"]}]\n= File: ".__FILE__."\n= Method: ".__METHOD__."\n\n= Caught: $e\n<END>\n\n", 3, PHP_ERRORS_LOG );
			throw $e;

		}
	}


	/**
	 * Genera, prepara y ejecuta un UPDATE
	 *
	 * Genera una instrucción de la forma "UPDATE tabla SET campo1=valor1, campo2=valor2, ...WHERE condicion" usando los parámtros de $args.
	 * La prepara, ejecuta y retorna la cantidad de elementos actualizados.
	 *
	 *
	 * == Ejemplo de uso ======================================
	 * Esta instrucción
	 * --------------------------------------------------------
	 * updateRows("Accounts", [
	 *         "values" => [
	 *             "CstMLounge"=>"Yes",
	 *         ],
	 *         "expressions" => [
	 *             "CstTokens"        => "CstTokens + :credits ",
	 *             "CstDateLastLogin" => "NOW()",
	 *         ],
	 *         "parameters" => [
	 *             "credits"   => 4,
	 *             "site"      => "bba",
	 *             "usernames" => ["ASR-cre-BBA", "ASR-mmb-BBA"],
	 *         ],
	 *     ], "CstSite = :site AND CstUser IN ( :usernames )"
	 * );
	 *
	 * Genera esto
	 * --------------------------------------------------------
	 * UPDATE Accounts SET CstMLounge=:CstMLounge , CstTokens=CstTokens + :credits  , CstDateLastLogin=NOW() WHERE CstSite = :site AND CstUser IN ( :usernamesId0 , :usernamesId1 )
	 * [
	 *     CstMLounge   => Yes
	 *     credits      => 4
	 *     site         => bba
	 *     usernamesId0 => ASR-cre-BBA
	 *     usernamesId1 => ASR-mmb-BBA
	 * ]
	 *
	 *
	 * @param  string $table  Nombre de la tabla.
	 * @param  array  $args   Es un array con esta estructura
	 *                          $args = [
	 *                              "values"      => ...,    // array <== Tantos "campo"=>$valor como campos quiera actualizar con valores "simples"
	 *                              "expressions" => ...,    // array <== Tantos "campo"=>"expresion" como campos quiera actualizar con valores "expresiones" ( ej "campo1"=>"NOW()", "campo2"=>"CstTokens + :credits " )
	 *                              "parameters"  => ...,    // array <== Tantos "parametro"=>valor como parámetros haya en las expressions y la condición WHERE
	 *                                                                      En el caso de que el parámetro pueda recibir múltiples valores -por ejemplo una condición "campo IN ( :ids )"- puede tomar la forma "ids"=>[$valor1, $valor2, ...]
	 *                          ]
	 * @param  string $where  Condición WHERE. Naturalmente toma formas como las siguientes "Campo = :id", "Campo < NOW()", "Campo1 = :id AND Campo < NOW()", etc.
	 *                          Para evitar errores tiene que necesariamente existir. Si quisiera actualizar todos los registros de la tabla debería pasar algo así como "1=1".
	 *
	 * @return false|int      Retorna la cantidad de registros eliminados ó falso (si falta algún parámetro)
	 *
	 * @throws Exception si hay algún error en la ejecución de la instrucción SQL
	 */
	public function updateRows($table, $args = [], $where)
	{
		if ( empty($table) || empty($args) || empty($where) )
			return false;

		if ( !isset($args["values"]) )       $args["values"]      = [];
		if ( !isset($args["expressions"]) )  $args["expressions"] = [];
		if ( !isset($args["parameters"]) )   $args["parameters"]  = [];

		try {
			// Si existen parámetros que aceptan múltiples valores re-escribe todo (tanto la instrucción como los parámetros)
			list($where, $args["parameters"]) = $this->rewriteSql($where, $args["parameters"]) ;

			// Armo la instrucción sql y la ejecuto
			$sql = "UPDATE $table SET @@FIELD_PLACEHOLDER@@ WHERE $where";

			foreach (array_keys($args["values"]) as $field)
			{
				$sql = str_replace( "@@FIELD_PLACEHOLDER@@" , "{$field}=:{$field} , @@FIELD_PLACEHOLDER@@" , $sql);
			}

			foreach ($args["expressions"] as $field => $value)
			{
				$sql = str_replace( "@@FIELD_PLACEHOLDER@@" , "{$field}={$value} , @@FIELD_PLACEHOLDER@@" , $sql);
			}

			$sql = str_replace( " , @@FIELD_PLACEHOLDER@@" , "" , $sql);

			$stmt = $this->pdo->prepare($sql);
			$stmt->execute( array_merge($args["values"],$args["parameters"]) );

			return $stmt->rowCount();

		} catch ( Exception $e ) {
			error_log("<START>\n= ".date("Y-m-d H:i:s")." [{$_SERVER["REMOTE_ADDR"]}]\n= File: ".__FILE__."\n= Method: ".__METHOD__."\n\n= Caught: $e\n<END>\n\n", 3, PHP_ERRORS_LOG );
			throw $e;

		}
	}


	/**
	 * Genera, prepara y ejecuta un DELETE
	 *
	 * Genera una instrucción de la forma "DELETE FROM tabla WHERE condicion" usando los parámtros de $args.
	 * La prepara, ejecuta y retorna la cantidad de elementos eliminados.
	 *
	 *
	 * @param  string $table  Nombre de la tabla.
	 * @param  array  $args   Es un array que contiene tantos "id"=>$valor como parámetros haya en la condición WHERE.
	 *                          En el caso de que el parámetro pueda recibir múltiples valores -por ejemplo una condición "campo IN ( :ids )"- puede tomar la forma "ids"=>[$valor1, $valor2, ...]
	 * @param  string $where  Condición WHERE. Naturalmente toma formas como las siguientes "Campo = :id", "Campo < NOW()", "Campo1 = :id AND Campo < NOW()", etc.
	   *                        Para evitar errores tiene que necesariamente existir. Si quisiera eliminar todos los registros de la tabla debería pasar algo así como "1=1".
	 *
	 * @return false|int      Retorna la cantidad de registros eliminados ó falso (si falta algún parámetro)
	 *
	 * @throws Exception si hay algún error en la ejecución de la instrucción SQL
	 */
	public function deleteRows($table, $args = [], $where = "")
	{
		if ( empty($table) || empty($where) )
			return false;

		try {
			// Si existen parámetros que aceptan múltiples valores re-escribe todo (tanto la instrucción como los parámetros)
			list($where, $args) = $this->rewriteSql($where, $args) ;

			// Armo la instrucción sql y la ejecuto
			$sql = "DELETE FROM $table WHERE $where";

			$stmt = $this->pdo->prepare($sql);
			$stmt->execute($args);

			return $stmt->rowCount();

		} catch ( Exception $e ) {
			error_log("<START>\n= ".date("Y-m-d H:i:s")." [{$_SERVER["REMOTE_ADDR"]}]\n= File: ".__FILE__."\n= Method: ".__METHOD__."\n\n= Caught: $e\n<END>\n\n", 3, PHP_ERRORS_LOG );
			throw $e;

		}

	}


	/**
	 * Re-escribe la instrucción Sql y genera los parámetros adecuados
	 *   para soportar variables que aceptan múltiples valores
	 *
	 *
	 * @param  string $sql         Instrucción Sql a re-ecribir.
	 * @param  array  $parameters  Contiene los parámetros de la instrucción. Pueden ser la forma simple "parameterId"=>parameterValue o
	 *                               de la forma más completa ["name"=>parameterId, "value"=>parameterValue, "type"=>parameterType]
	 *
	 * @return array  Retorna un array con dos posiciones, en la primera la instrucción Sql y en la segunda los parámetros.
	 *                  Eventualmente, en el probable caso de que no existan valores múltiples, retorma exactamente lo que recibe.
	 */
	private function rewriteSql($sql = "", $parameters = [])
	{
		if ( empty($sql) || empty($parameters) )
			return [$sql, $parameters];

		// Antes de prepapar la instrucción me fijo si hay que reformularla por variables que aceptan múltiples valores
		foreach ( $parameters as $key => $value )
		{
			if ( isset($value["name"]) && isset($value["value"]) ) {
				// Viene con la forma completa del parámetro ( posiblemente desde getRows() )
				//   "$key" => ["name"=>parameterId, "value"=>parameterValue, "type"=>parameterType]
				$idName  = $value["name"];
				$idValue = $value["value"];
				$rowId   = $key;
			} else {
				// Viene con la forma simple del parámetro
				//   "$parameterId" => parameterValue
				$idName  = $key;
				$idValue = $value;
				$rowId   = $key;
			}

			if ( is_array($idValue) && strpos($sql." ", ":{$idName} ")!==false ) {
				if ( !empty($idValue) ) {
					// Esta variable acepta múltiples valores
					$allNewVars = "";
					$oldVars[]  = $rowId ;

					foreach ( $idValue as $index => $eachValue )
					{
						// Consigo entonces todos los valores posibles de la variable
						$allNewVars .= ":{$idName}Id{$index} , ";

						if ( $rowId!==$idName ) {     // Es de la forma completa
							$newVars[] = [ "name"=>"{$idName}Id{$index}", "value"=>$eachValue, "type"=>$value["type"] ] ;
						} else {                      // Es de la forma simple
							$newVars["{$idName}Id{$index}"] = $eachValue ;
						}
					}

					// Cambio en la instrucción $sql el nombre de la variable original por las nuevas
					$sql = str_replace(":{$idName} ", rtrim($allNewVars,", ")." ", $sql." ");

				} else {
					// Es un array, pero está vacío
					if ( $rowId!==$idName ) {     // Es de la forma completa
						$parameters[$rowId]["value"] = "";
					} else {                      // Es de la forma simple
						$parameters[$rowId] = "";
					}

				}
			}
		}

		// Elimino y agrego en el array de variables
		if ( isset($oldVars) ) {
			$parameters = array_merge( array_diff_key( $parameters, array_flip($oldVars) ), $newVars ) ;
		}

		return [$sql, $parameters];
	}


}