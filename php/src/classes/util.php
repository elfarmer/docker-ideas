<?php
/**
 * Id4Ideas #equipo426
 * https://idforideas.com/
 */


 class util
{
	/**
	 * Converts any accent characters to their equivalent normal characters
	 * and converts any other non-alphanumeric characters to dashes, then
	 * converts any sequence of two or more dashes to a single dash. This
	 * function generates slugs safe for use as URLs, and if you pass true
	 * as the second parameter, it will create strings safe for use as CSS
	 * classes or IDs.
	 *
	 * @param   string  $string    A string to convert to a slug
	 * @param   string  $separator The string to separate words with
	 * @param   boolean $css_mode  Whether or not to generate strings safe for CSS classes/IDs (Default to false)
	 * @return  string
	 */
	public static function slugify($string, $separator = '-', $css_mode = false)
	{
		$slug = preg_replace('/([^a-z0-9]+)/', $separator, strtolower(self::remove_accents($string)));

		if ($css_mode) {
			$digits = array("zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine");

			if (is_numeric(substr($slug, 0, 1))) {
				$slug = $digits[substr($slug, 0, 1)] . substr($slug, 1);
			}
		}

		return $slug;
	}


	/**
	 * Converts all accent characters to ASCII characters.
	 * @see https://stackoverflow.com/a/25414406
	 *
	 * @param  string $string   Text that might have accent characters
	 * @return string Filtered string with replaced "nice" characters
	 */
	public static function remove_accents( $string )
	{
		return iconv("UTF-8", "ASCII//TRANSLIT", $string);
	}


	/**
	 * Prepara el texto normal para ser presentado en HTML
	 *
	 * @param  string $dirty    Normal text
	 * @return string HTML string
	 */
	public static function text2html( $dirty )
	{
		return htmlspecialchars($dirty, ENT_QUOTES, "UTF-8");
	}


	/**
	 * Determina si hay HTML almacenado en una variable
	 *
	 * @param  string $string
	 * @return boolean
	 */
    public static function isHtml( $string )
    {
        if ( $string != strip_tags($string) )
            return true;  // Contains HTML

        return false;     // Does not contain HTML
    }


	/**
	 * Determina cómo mostrar números "grandes"
	 *
	 * @param  integer $number    Number to format
	 * @return string HTML string
	 */
	public static function custom_number_format( $number, $presentation="short" )
	{
		if ( $presentation=="short" ) {
			if ($number > 9999)
				$format = number_format(round($number/1000, 0)) . "k" ;
			elseif ($number > 999)
				$format = number_format(round($number/1000, 1),1) . "k" ;
			else
				$format = $number  ;

		} else {
			$format = number_format($number,0) ;

		}

		return $format;
	}


	/**
	 * Determina cómo mostrar el tamaño de un archivo
	 * @see https://stackoverflow.com/a/2510459
	 *
	 * @param  integer $bytes      Number to format
	 * @param  integer $precision  Precision (decimals)
	 * @return string bytes formated
	 */
	public static function formatBytes($bytes, $precision = 2)
	{
		$units = array("B", "Kb", "Mb", "Gb", "Tb");

		$bytes = max($bytes, 0);
		$pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow   = min($pow, count($units) - 1);

		// Uncomment one of the following alternatives
		   $bytes /= pow(1024, $pow);
		// $bytes /= (1 << (10 * $pow));

		return round($bytes, $precision) . " " . $units[$pow];
	}


	/**
	 * Devuelve en "lenguage humano" la diferencia
	 *   de tiempo entre dos momentos
	 */
	public static function timeAgo($initialTime, $finishTime="")
	{
		$initialTime = strtotime($initialTime) ;
		$finishTime  = (empty($finishTime)?time():strtotime($finishTime)) ;

		$seconds = $finishTime - $initialTime ;
		$minutes = round($seconds / 60 );
		$hours   = round($seconds / 3600);
		$days    = round($seconds / 86400 );

		if ( $seconds <= 60 ) {
			// Seconds
			return "just now";
		} elseif ( $minutes <= 60 ) {
			//Minutes
			if ( $minutes==1 ) {
				return "1 minute ago";
			} else {
				return "$minutes minutes ago";
			}
		} elseif ( $hours <= 24 ) {
			//Hours
			if ( $hours==1 ) {
				return "an hour ago";
			} else {
				return "$hours hours ago";
			}
		} else {
			//Days
			if ( $days==1 ) {
				return "yesterday";
			} else {
				return "$days days ago";
			}
		}

	}


	/**
	 * Determina si un objeto está vacío
	 *
	 * @param  object $obj
	 * @return boolean
	 */
	public static function isEmptyObj( $obj )
	{
		foreach ( $obj AS $prop )
			return false;
		return true;
	}


	/**
	 * Operación de Módulo tal y como la hace (por ejemplo) Excel
	 * @see https://stackoverflow.com/a/23214321
	 *
	 * @param  integer $dividend
	 * @param  integer $divisor
	 * @return integer
	 */
	public static function mod(int $dividend, int $divisor)
	{
		return ( (($dividend %= $divisor) < 0) ? $dividend+$divisor : $dividend );
	}


	/**
	 * Crea en el objeto $object las propiedades de acuerdo al array asociativo $properties
	 * @see https://stackoverflow.com/a/29907450
	 *
	 * @param  object  $object      Objeto al cual hay que agregarle las propiedades
	 * @param  array   $properties  Array asocitivo con las propiedades a agregar
	 * @return string
	 */
	public static function createProperties($object, $properties)
	{
		foreach ($properties AS $key => $value) {
			$object->{$key} = $value;
		}
	}


	/**
	 * Encuentra la posición de la n ocurrencia de needle en haystack
	 *
	 * @return mixed  int con la posición, false si no lo encuentra
	 */
	public static function strpos_nth( $haystack, $needle, $occurrence )
	{
		if ( filter_var( $occurrence, FILTER_VALIDATE_INT, [ "options" => ["min_range"=>1, "max_range"=>strlen($haystack)] ] ) === false)
			return false ;

		$loop     = 0 ;
		$position = -1 ;
		while ( $position!==false && ++$loop<=$occurrence )
			$position = strpos( $haystack, $needle, ++$position ) ;

		return $position ;
	}


	/**
	 * Retorna un array con los valores validos de TimeZone para PHP.
	 *
	 * @return array  Valores válidos para la TimeZone
	 */
	public static function getTimeZones( )
	{
		$allTimeZones = DateTimeZone::listIdentifiers();

		return $allTimeZones;
	}


	/**
	 * Determina si una TimeZone tiene un valor válido para PHP.
	 *
	 * @param  string  $timeZone
	 * @return boolean
	 */
	public static function isValidTimeZone( $timeZone )
	{
		$allTimeZones = DateTimeZone::listIdentifiers();

		if ( !in_array($timeZone, $allTimeZones) ) {
			error_log("<START>\n= ".date("Y-m-d H:i:s")." [{$_SERVER["REMOTE_ADDR"]}]\n= File: ".__FILE__."\n= Method: ".__METHOD__."\n\n= Caught: Invalid TimeZone ({$timeZone})\n<END>\n\n", 3, PHP_WARNINGS_LOG );
			return false;
		}

		return true;
	}


	public static function deliverThis( $result, $modifiers )
	{
		isset($modifiers["casts"]) or $modifiers["casts"] = [];
		isset($modifiers["urls"])  or $modifiers["urls"]  = [];

		return self::addUrls( self::cast( $result, $modifiers["casts"] ), $modifiers["urls"] ) ;
	}

	
	public static function cast( $result, $cast )
	{
		if ( empty( $cast ) )
			return $result;


		if ( is_array($result)==false ) {
			/**
			 * Recibimos directamente propiedades de un objeto.
			 * Generalmente el resultado de un fetch() en lugar de fetchAll()
			 */
			$resultArray[0] = $result;
		} else {
			$resultArray = $result;
		}
	
		
		foreach ($resultArray as $eachResult) 
		{
			foreach ($cast as $key => $value) {
				if ( isset($eachResult->$key) ) {
					if ( $value=="bool" ) {
						$eachResult->$key = (bool) $eachResult->$key;
					} elseif ( $value=="decimal" ) {
						$eachResult->$key = +$eachResult->$key;
					}
				}
			}
		}


		if ( is_array($result)==false ) {
			return $resultArray[0] ;
		} else {
			return $resultArray ;
		}
	}


	public static function addUrls( $result, $urls )
	{
		if ( empty( $urls ) )
			return $result;


		if ( is_array($result)==false ) {
			/**
			 * Recibimos directamente propiedades de un objeto.
			 * Generalmente el resultado de un fetch() en lugar de fetchAll()
			 */
			$resultArray[0] = $result;
		} else {
			$resultArray = $result;
		}
	
		
		foreach ($resultArray as $eachResult) 
		{
			foreach ($urls as $key => $value) {
				if ( isset($eachResult->$key) && !empty($eachResult->$key) ) {
					$eachResult->$key = $value.$eachResult->$key;
				}
			}
		}


		if ( is_array($result)==false ) {
			return $resultArray[0] ;
		} else {
			return $resultArray ;
		}
	}


}
