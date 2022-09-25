<?php
/**
 * Id4Ideas
 *
 */


/**
 * Configuración para debug (en desarrollo todos los valores pueden ser 1, pero en producción los dos últimos deben ser 0)
 */
error_reporting(E_ALL ^ E_NOTICE );
ini_set( "log_errors",             1 );
ini_set( "display_errors",         0 );
ini_set( "display_startup_errors", 0 );


/**
 * Huevadas varias utf-8
 * https://phptherightway.com/#i18n_l10n
 */
mb_internal_encoding("UTF-8");     // Tell PHP that we're using UTF-8 strings until the end of the script
mb_http_output("UTF-8");           // Tell PHP that we'll be outputting UTF-8 to the browser
setlocale(LC_ALL, "en_US.utf8");   // Sin especificar esto la función iconv no trabaja adecuadamente


/**
 * Constantes de configuración
 */
require_once "__constants.inc.php" ;


/**
 * PHP Time Zone
 */
date_default_timezone_set( SERVER_TIME_ZONE );


/**
 * Class Autoloader
 */
spl_autoload_register(function($className) {
	$classLocations = [ BASE_PATH . "/classes/{$className}.php" ];
	foreach ($classLocations as $classFile)
	{
		if (file_exists($classFile)) {
			require_once $classFile ;
			break;
		}
	}
});
