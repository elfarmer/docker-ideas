<?php
/**
 * Id4Ideas #equipo426
 * https://idforideas.com/
 */


/* Server */
   define ( 'BASE_PATH',          $_SERVER['DOCUMENT_ROOT']                           ) ;

   define ( 'PHP_ERRORS_LOG',     BASE_PATH.'/_phpLogs/'.date('Y-m-d').'.errors.log'   ) ;
   define ( 'PHP_WARNINGS_LOG',   BASE_PATH.'/_phpLogs/'.date('Y-m-d').'.warnings.log' ) ;
   define ( 'PHP_DEBUG_LOG',      BASE_PATH.'/_phpLogs/'.date('Y-m-d').'.debug.log'    ) ;


/* MySql configuration */
   define ( 'DB_HOST', 'db'        ) ;
   define ( 'DB_NAME', 'id4ideas3' ) ;
   define ( 'DB_USER', 'ideasUser' ) ;
   define ( 'DB_PASS', 'ideasPass' ) ;
   define ( 'DB_CHAR', 'utf8mb4'   ) ;

   define ( 'SERVER_TIME_ZONE',  'America/Argentina/Buenos_Aires' ) ;
   define ( 'TIME_NAMES',        'es_AR'                          ) ;

/* Site */
   define ( 'SITE_URL',   'http://localhost:8080/' ) ;
   define ( 'IMAGES_URL', [
      'productos'  => SITE_URL.'img/',
      'categorias' => SITE_URL.'img/',
   ]) ;


/* eof */