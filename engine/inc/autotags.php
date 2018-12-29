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

if ( !defined( 'DATALIFEENGINE' ) OR ! defined( 'LOGGED_IN' ) ) {
	die( "Hacking attempt!" );
}

require_once ENGINE_DIR . "/data/config.php";

if ( $config['version_id'] >= "10.2" ) {
	include ENGINE_DIR . "/inc/autotags_new.php";
} else {
	include ENGINE_DIR . "/inc/autotags_old.php";
}

?>