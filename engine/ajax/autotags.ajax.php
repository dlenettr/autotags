<?php
/*
=====================================================
 MWS Auto Tags v1.1 - by MaRZoCHi (MWS) 
-----------------------------------------------------
 http://www.dle.net.tr/
-----------------------------------------------------
 Copyright (c) 2014 - DLE.NET.TR
-----------------------------------------------------
 License : GPL License
=====================================================
*/

@error_reporting ( E_ALL ^ E_WARNING ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
@ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE );

define( 'DATALIFEENGINE', true );
define( 'ROOT_DIR', substr( dirname(  __FILE__ ), 0, -12 ) );
define( 'ENGINE_DIR', ROOT_DIR . '/engine' );

include ENGINE_DIR . '/data/config.php';
require_once ENGINE_DIR . "/data/autotags.conf.php";
require_once ROOT_DIR . "/language/" . $config['langs'] . "/autotags.lng";

if ( $config['version_id'] >= "10.3" ) date_default_timezone_set ( $config['date_adjust'] );

if ( $config['http_home_url'] == "" ) {
	$config['http_home_url'] = explode( "engine/ajax/autotags.ajax.php", $_SERVER['PHP_SELF'] );
	$config['http_home_url'] = reset( $config['http_home_url'] );
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];
}

require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . '/data/dbconfig.php';
require_once ENGINE_DIR . '/modules/functions.php';

if ( $config['version_id'] >= "10.0" ) dle_session();


@header( "Content-type: text/html; charset=" . $config['charset'] );

if ( isset( $_POST['action'] ) && $_POST['action'] = "admin:tags:delete" ) {

	$db->query("TRUNCATE TABLE " . PREFIX . "_tags");
	$db->query("UPDATE " . PREFIX . "_post SET tags = ''");

	$res = array( "text" => $lang['autotags_38'], "result" => "ok" );

} else {

	$res = array( "result" => "no", "error" => $lang['autotags_39'] );

}


echo json_encode( $res );

?>