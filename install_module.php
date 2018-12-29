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

if( ! defined( 'E_DEPRECATED' ) ) {
	@error_reporting ( E_ALL ^ E_NOTICE );
	@ini_set ( 'error_reporting', E_ALL ^ E_NOTICE );
} else {
	@error_reporting ( E_ALL ^ E_DEPRECATED ^ E_NOTICE );
	@ini_set ( 'error_reporting', E_ALL ^ E_DEPRECATED ^ E_NOTICE );
}

define ( 'DATALIFEENGINE', true );
define ( 'ROOT_DIR', dirname ( __FILE__ ) );
define ( 'ENGINE_DIR', ROOT_DIR . '/engine' );
define ( 'LANG_DIR', ROOT_DIR . '/language/' );

require_once(ENGINE_DIR."/inc/include/functions.inc.php");
require_once(ENGINE_DIR."/data/config.php");
require_once(ROOT_DIR."/language/".$config['langs']."/adminpanel.lng");
require_once(ENGINE_DIR."/classes/mysql.php");
require_once(ENGINE_DIR."/data/dbconfig.php");
require_once(ENGINE_DIR."/modules/sitelogin.php");
require_once ENGINE_DIR . "/classes/install.class.php";
require_once ENGINE_DIR . "/api/api.class.php";
if ( $config['version_id'] < "10.2" ) require_once ENGINE_DIR . "/skins/default.skin.php";

@header( "Content-type: text/html; charset=" . $config['charset'] );
require_once(ROOT_DIR."/language/".$config['langs']."/adminpanel.lng");

$Turkish = array ( 'm01' => "Kuruluma Başla", 'm02' => "Yükle", 'm03' => "Kaldır", 'm04' => "Yapımcı", 'm05' => "Çıkış Tarihi", 'm08' => "Kurulum Tamamlandı", 'm10' => "dosyasını silerek kurulumu bitirebilirsiniz", 'm11' => "Modül Kaldırıldı", 'm21' => "Kuruluma başlamadan önce olası hatalara karşı veritabanınızı yedekleyin", 'm22' => "Eğer herşeyin tamam olduğuna eminseniz", 'm23' => "butonuna basabilirsiniz.", 'm24' => "Güncelle", 'm25' => "Site", 'm26' => "Çeviri", 'm27' => "Hata", 'm28' => "Bu modül DLE sürümünüz ile uyumlu değil.", 'm29' => "Buradan sürümünüze uygun modülü isteyebilirsiniz" );
$English = array ( 'm01' => "Start Installation", 'm02' => "Install", 'm03' => "Uninstall", 'm04' => "Author", 'm05' => "Release Date", 'm06' => "Module Page", 'm07' => "Support Forum", 'm08' => "Installation Finished", 'm10' => "delete this file to finish installation", 'm11' => "Module Uninstalled", 'm21' => "Back up your database before starting the installation for possible errors", 'm22' => "If you are sure that everything is okay, ", 'm23' => "click button.", 'm24' => "Upgrade", 'm25' => "Site", 'm26' => "Translation", 'm27' => "Error", 'm28' => "This module not compatible with your DLE.", 'm29' => "You can ask for compatible version from here" );
$Russian = array ( 'm01' => "Начало установки", 'm02' => "Установить", 'm03' => "Удалить", 'm04' => "Автор", 'm05' => "Дата выпуска", 'm06' => "Страница модуля", 'm07' => "Форум поддержки", 'm08' => "Установка завершена", 'm10' => "удалите этот фаля для окончания установки", 'm11' => "Модуль удален", 'm21' => "Сделайте резервное копирование базы данных для избежания возможных ошибок", 'm22' => "Если вы уверены что всё впорядке, ", 'm23' => "нажмите кнопку.", 'm24' => "обновлять", 'm25' => "сайт", 'm26' => "перевод" );
$lang = array_merge( $lang, $$config['langs'] );

if ( $config['version_id'] >= "10.2" ) {

function mainTable_head( $title ) {
	echo <<< HTML
	<div class="box">
		<div class="box-header">
			<div class="title"><div class="box-nav"><font size="2">{$title}</font></div></div>
		</div>
		<div class="box-content">
			<table class="table table-normal">
HTML;
}

function mainTable_foot() {
	echo <<< HTML
			</table>
		</div>
	</div>
HTML;
}
} else {
function mainTable_head( $title ) {
	echo <<< HTML
	<div style="padding-top:5px;padding-bottom:2px;">
	<table width="100%">
		<tr>
			<td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
			<td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
			<td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
		</tr>
		<tr>
			<td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
			<td style="padding:5px;" bgcolor="#FFFFFF">
				<table width="100%">
					<tr>
						<td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$title}</div></td>
					</tr>
				</table>
				<div class="unterline"></div>
				<table width="100%">
					<tr>
						<td>
HTML;
}

function mainTable_foot() {
	echo <<< HTML
						</td>
					</tr>
				</table>
			</td>
			<td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
		</tr>
		<tr>
			<td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
			<td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
			<td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
		</tr>
	</table>
HTML;
}
}


$module = array(
	'name'		=> "MWS Auto Tags v1.2",
	'desc'		=> "Specify tags for each category, create automatically",
	'id'		=> "autotags",
	'icon'		=> "autotags.png",
	'ticon'		=> "tags",
	'date'		=> "08.11.2016",
	'ifile'		=> "install_module.php",
	'link'		=> "http://dle.net.tr",
	'image'		=> "http://img.dle.net.tr/mws/autotags.png",
	'author_n'	=> "Mehmet Hanoğlu (MaRZoCHi)",
	'author_s'	=> "http://mehmethanoglu.com.tr",
	'tran_n'	=> "",
	'tran_s'	=> "",
);



if ( $config['version_id'] >= "10.2" ) {
	echoheader("<i class=\"icon-{$module['ticon']}\"></i>" . $module['name'], $lang['m01'] );
} else {
	echoheader("home", "" );
}

if ( $_REQUEST['action'] == "install" ) {

	$mod = new VQEdit();
	$mod->backup = True;
	$mod->bootup( $path = ROOT_DIR, $logging = True );
	$dle_api->install_admin_module($module['id'], $module['name'], $module['desc'], $module['icon'] , "1");
	$mod->file( ROOT_DIR. "/install/xml/autotags.xml" );
	$mod->close();

	mainTable_head( $lang['m08'] );
	echo <<< HTML
	<table width="100%">
		<tr>
			<td width="210" align="center" valign="middle" style="padding:4px;">
				<img src="{$module['image']}" alt="" />
			</td>
			<td style="padding-left:20px;padding-top: 4px;" valign="top">
				<b><a href="{$module['link']}">{$module['name']}</a></b><br /><br />
				<b>{$lang['m04']}</b> : <a href="{$module['author_s']}">{$module['author_n']}</a><br />{$translation}
				<b>{$lang['m05']}</b> : <font color="#555555">{$module['date']}</font><br />
				<b>{$lang['m25']}</b> : <a href="{$module['link']}">{$module['link']}</a><br />
				<br /><br />
				<b><font color="#BF0000">{$module['ifile']}</font> {$lang['m10']}</b><br />
			</td>
		</tr>
	</table>
HTML;
	mainTable_foot();

} else if ( $_REQUEST['action'] == "uninstall" ) {

	mainTable_head( $lang['m11'] );
	echo <<< HTML
	<table width="100%">
		<tr>
			<td width="210" align="center" valign="middle" style="padding:4px;">
				<img src="{$module['image']}" alt="" />
			</td>
			<td style="padding-left:20px;padding-top: 4px;" valign="top">
				<b><a href="{$module['link']}">{$module['name']}</a></b><br /><br />
				<b>{$lang['m04']}</b> : <a href="{$module['author_s']}">{$module['author_n']}</a><br />{$translation}
				<b>{$lang['m05']}</b> : <font color="#555555">{$module['date']}</font><br />
				<b>{$lang['m25']}</b> : <a href="{$module['link']}">{$module['link']}</a><br />
				<br /><br />
				<b><font color="#BF0000">{$module['ifile']}</font> {$lang['m10']}</b><br />
			</td>
		</tr>
	</table>
HTML;
	mainTable_foot();
	$db->free();

} else {

	mainTable_head( $lang['m01'] );
	$translation = ( ! empty( $module['tran_n'] ) ) ? "<b>{$lang['m26']}</b> : <a href=\"{$module['tran_s']}\">{$module['tran_n']}</a><br />" : "";
	echo <<< HTML
	<table width="100%">
		<tr>
			<td width="210" align="center" valign="middle" style="padding:4px;">
				<img src="{$module['image']}" alt="" /><br /><br />
			</td>
			<td style="padding-left:20px;padding-top: 4px;" valign="top">
				<b><a href="{$module['link']}">{$module['name']}</a></b><br /><br />
				<b>{$lang['m04']}</b> : <a href="{$module['author_s']}">{$module['author_n']}</a><br />{$translation}
				<b>{$lang['m05']}</b> : <font color="#555555">{$module['date']}</font><br />
				<b>{$lang['m25']}</b> : <a href="{$module['link']}">{$module['link']}</a><br />
				<br /><br />
				<b><font color="#BF0000">{$lang['m01']} ...</font></b><br /><br />
				<b>*</b> {$lang['m21']}<br />
				<b>*</b> {$lang['m22']} <font color="#51A351"><b>{$lang['m02']}</b></font> {$lang['m23']}<br />
			</td>
		</tr>
		<tr>
			<td width="150" align="left" style="padding:4px;"></td>
			<td colspan="2" style="padding:4px;" align="right">
HTML;
		echo "<input type=\"button\" value=\"{$lang['m02']}\" class=\"btn btn-green btn-success\" onclick=\"location.href='{$PHP_SELF}?action=install'\" />";
		echo <<< HTML
			</td>
		</tr>
	</table>
HTML;
	mainTable_foot();
	$db->free();
}

echofooter();
?>