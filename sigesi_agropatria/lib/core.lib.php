<?php
    error_reporting(E_ALL & ~E_NOTICE);
    
    //CONFIGURACION DE LA ZONA HORARIA PARA VENEZUELA
    setlocale(LC_TIME, 'es_VE', 'es_VE.utf-8', 'es_VE.utf8'); # Asi es mucho mas seguro que funciones, ya que no todos los sistemas llaman igual al locale ;)
    date_default_timezone_set('America/Caracas');
    
    //PREVENT ATTACK FROM OTHER SITES
    if (preg_match( "/core.lib.php/", $_SERVER ["PHP_SELF"] ) || preg_match( "/core.lib.php/", $HTTP_SERVER_VARS ["PHP_SELF"] ))
        die("Access denied!");
    $ini_request=microtime(true);
    //config var goes in the same place than core.lib
    require_once("config_var.php");
    $conn_string = "host=".SERVER." port=5432 password=".PASS." user=".USER." dbname=".DB;
    //$conn_string = "host=localhost port=5432 password=123456 user=admin dbname=sigesi";
    $link = pg_connect($conn_string);
    if (pg_ErrorMessage($link)) { echo "<p><b>Ocurrio un error conectando a la base de datos: .</b></p>"; exit; }
    //$link=mysql_connect(SERVER,USER,PASS) or die("Could not connect to Database server");
    //mysql_select_db(DB,$link) or die( "Could not open database");

    require_once(APPROOT."lib/class/logs.class.php");
    require_once(APPROOT."lib/class/dbtools.class.php");
    require_once(APPROOT."lib/class/debug.class.php");
    require_once(APPROOT."lib/rewrite_globals.php"); //REWRITE GLOBALSS
    require_once(APPROOT."lib/class/general.class.php");
    require_once(APPROOT."lib/class/parameter.class.php");
    require_once(APPROOT."lib/class/html.class.php");
    require_once(APPROOT."lib/class/javascript.class.php");
    require_once(APPROOT."lib/class/externals/cake/inflector.php");
    
    require(APPROOT."lib/class/autoloader.class.php");
    Autoloader::Register();

    session_start();

    // Global Objects
    $general = new General;
    $parametros = new Parameter();
    $javascript = new Javascript();
    $html = new Html();

    if (file_exists(APPROOT.'lib/common/verificar_login.php')){
        $archivo = explode("/", $_SERVER['PHP_SELF']);
        if(!in_array($archivo[count($archivo)-1],array('index.php', 'app_error.php', 'verificar_acceso.php', 'cerrar_sesion.php'))){
            require_once(APPROOT.'lib/common/verificar_login.php');
        }
    }
    //habilitamos el error handler
    if ((DEBUG) and (Debug::$config['Handler'])){
    /* replace PHP's errorhandler */
        $errorhandler = array (
        Debug,
            'errorHandlerCallback'
        );
        set_error_handler($errorhandler);
    }
?>