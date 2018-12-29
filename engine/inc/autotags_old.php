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

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	die( "Hacking attempt!" );
}

if( $member_id['user_group'] != 1 ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

require_once ENGINE_DIR . "/data/autotags.conf.php";
require_once ROOT_DIR . "/language/" . $config['langs'] . "/autotags.lng";

if ( ! is_writable(ENGINE_DIR . '/data/autotags.conf.php' ) ) {
	$lang['stat_system'] = str_replace( "{file}", "engine/data/autotags.conf.php", $lang['stat_system'] );
	$fail = "<div class=\"alert alert-error\">{$lang['stat_system']}</div>";
} else $fail = "";

if ( $action == "save" ) {
	if ( $member_id['user_group'] != 1 ) { msg( "error", $lang['opt_denied'], $lang['opt_denied'] ); }
	if ( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) { die( "Hacking attempt! User not found" ); }
	
	$save_con = $_POST['save_con'];
	$save_con['tag_count'] = intval($save_con['tag_count']);
	$save_con['tag_minchr'] = intval($save_con['tag_minchr']);
	$save_con['tag_maxchr'] = intval($save_con['tag_maxchr']);
	$save_con['use_random'] = intval($save_con['use_random']);
	$save_con['use_translit'] = intval($save_con['use_translit']);

	$find = array(); $replace = array();
	$find[] = "'\r'"; $replace[] = "";
	$find[] = "'\n'"; $replace[] = "";
	
	$save_con = $save_con + $sett;

	if ( count( $save_con['__new___cat'] ) > 0 && count( $save_con['__new___source'] ) > 0 && ! empty( $save_con['__new___rules'] ) ) {
		$new_name = md5( $db->safesql( $save_con['__new___rules'] . implode( "", $save_con['__new___cat'] ) . implode( "", $save_con['__new___source'] ) ) );
		$save_con[ $new_name . "_cats" ] = $save_con['__new___cat'];
		$save_con[ $new_name . "_rules" ] = $save_con['__new___rules'];
		$save_con[ $new_name . "_source" ] = $save_con['__new___source'];
		$save_con[ $new_name . "_type" ] = intval($save_con['__new___type']);

	}
	unset( $save_con['__new___rules'], $save_con['__new___cat'], $save_con['__new___source'], $save_con['__new___type'] );

	$handler = fopen( ENGINE_DIR . '/data/autotags.conf.php', "w" );
	
	fwrite( $handler, "<?PHP \n\n//MWS Auto Tags Settings\n\n\$sett = array (\n" );
	foreach ( $save_con as $name => $value ) {
		$value = ( is_array( $value ) ) ? implode(",", $value ) : $value;
		$value = trim(strip_tags(stripslashes( $value )));
		$value = htmlspecialchars( $value, ENT_QUOTES, $config['charset']);
		$value = preg_replace( $find, $replace, $value );
		$name = trim(strip_tags(stripslashes( $name )));
		$name = htmlspecialchars( $name, ENT_QUOTES, $config['charset'] );
		$name = preg_replace( $find, $replace, $name );
		$value = str_replace( "$", "&#036;", $value );
		$value = str_replace( "{", "&#123;", $value );
		$value = str_replace( "}", "&#125;", $value );
		$value = str_replace( ".", "", $value );
		$value = str_replace( '/', "", $value );
		$value = str_replace( chr(92), "", $value );
		$value = str_replace( chr(0), "", $value );
		$value = str_replace( '(', "", $value );
		$value = str_replace( ')', "", $value );
		$value = str_ireplace( "base64_decode", "base64_dec&#111;de", $value );
		$name = str_replace( "$", "&#036;", $name );
		$name = str_replace( "{", "&#123;", $name );
		$name = str_replace( "}", "&#125;", $name );
		$name = str_replace( ".", "", $name );
		$name = str_replace( '/', "", $name );
		$name = str_replace( chr(92), "", $name );
		$name = str_replace( chr(0), "", $name );
		$name = str_replace( '(', "", $name );
		$name = str_replace( ')', "", $name );
		$name = str_ireplace( "base64_decode", "base64_dec&#111;de", $name );
		fwrite( $handler, "'{$name}' => '{$value}',\n" );
	}
	fwrite( $handler, ");\n\n?>" );
	fclose( $handler );
	
	msg( "info", $lang['opt_sysok'], $lang['opt_sysok_1'], "{$PHP_SELF}?mod=autotags" );

} else if ( $action == "delete" ) {

	if ( $member_id['user_group'] != 1 ) { msg( "error", $lang['opt_denied'], $lang['opt_denied'] ); }
	if ( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) { die( "Hacking attempt! User not found" ); }
	if ( isset( $_REQUEST['page'] ) ) {

		unset( $sett[ $_REQUEST['page'] . '_cats'], $sett[ $_REQUEST['page'] . '_rules'], $sett[ $_REQUEST['page'] . '_source'], $sett[ $_REQUEST['page'] . '_type'] );

		$find = array(); $replace = array();
		$find[] = "'\r'"; $replace[] = "";
		$find[] = "'\n'"; $replace[] = "";

		$handler = fopen( ENGINE_DIR . '/data/autotags.conf.php', "w" );
		fwrite( $handler, "<?PHP \n\n//MWS Auto Tags Settings\n\n\$sett = array (\n" );
		foreach ( $sett as $name => $value ) {
			fwrite( $handler, "'{$name}' => '{$value}',\n" );
		}
		fwrite( $handler, ");\n\n?>" );
		fclose( $handler );
	
		msg( "info", $lang['opt_sysok'], $lang['opt_sysok_1'], "{$PHP_SELF}?mod=autotags" );

	} else {
		msg( "info", $lang['opt_sysok'], $lang['opt_sysok_1'], "{$PHP_SELF}?mod=autotags" );
	}

}


$js_array[] = "engine/skins/chosen/chosen.js";
echoheader( "home", $lang['autotags_0'] );

echo <<< HTML
	<script type="text/javascript">
	$(document).ready( function() {
		$('.categoryselect').chosen({allow_single_deselect:true, no_results_text: '{$lang['addnews_cat_fault']}'});
	});
	var is_open = false;
	function ShowOrHidePanel( id ) {
		if ( is_open == false ) {
			$("#"+id).slideDown();
			$('.chzn-container').css({'width': '350px'});
			is_open = true;
		} else {
			$("#"+id).slideUp();
			is_open = false;
		}
	}
	function onCategoryChange(obj) {
		var value = $(obj).val();
		if ($.isArray(value)) {} else {}
	}
	function DeleteAllTags() {
	    $("#dialog-confirm").text("{$lang['autotags_40']}").attr('title', "{$lang['autotags_41']}");
	    $("#dialog-confirm").dialog({
	        resizable: false,
	        height: 150,
	        modal: true,
	        buttons: {
	            "{$lang['autotags_47']}": function() {
	                $(this).dialog("close");
	                $.post("engine/ajax/autotags.ajax.php", { action: 'admin:tags:delete' }, function(data) {
	                    if ( data.result == "ok" ) {
	                    	DLEalert( data.text, "{$lang['autotags_42']}" );
	                    } else {
	                        DLEalert( data.error, "{$lang['autotags_43']}" );
	                    }
	                }, 'json');
	            },
	            "{$lang['autotags_48']}": function() {
	                $(this).dialog("close");
	            }
	        }
	    });
	}
    </script>
HTML;

function showRow($title = "", $description = "", $field = "") {
	   echo "<tr>
       <td style=\"padding:4px\" class=\"option\">
        <b>{$title}</b><br /><span class=small>{$description}</span>
        <td width=\"50%\" align=\"left\" >
        {$field}
        </tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr>";
		$bg = "";
		$i ++;
}
	
function makeDropDown($options, $name, $selected) {
		$output = "<select name=\"{$name}\">\r\n";
		foreach ( $options as $value => $description ) {
			$output .= "<option value=\"{$value}\"";
			if( $selected == $value ) {
				$output .= " selected ";
			}
			$output .= ">{$description}</option>\n";
		}
		$output .= "</select>";
		return $output;
}

function showSep( ) {
	echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";
}

function makeCheckBox($name, $selected) {
	$selected = $selected ? "checked" : "";
	return "<input class=\"iButton-icons-tab\" type=\"checkbox\" name=\"{$name}\" value=\"1\" {$selected}>";
}

function set_selected( $arr, $sel ) {
	if ( ! is_array( $sel ) ) { $sel = explode( ",", $sel ); }
	$html = "";
	foreach( $arr as $key => $val ) {
		$selected = ( in_array( $key, array_values( $sel ) ) ) ? " selected" : "";
		$html .= "<option style=\"color: black\"" . $selected . " value=\"" . $key . "\" >" . $val . "</option>";
	}
	return $html;
}

$source_list = array(
	"1" => $lang['autotags_1'],
	"2" => $lang['autotags_2'],
	"3" => $lang['autotags_3'],
);

$type_list = array(
	"1" => $lang['autotags_4'],
	"2" => $lang['autotags_5'],
);


echo <<<HTML
{$fail}
<div id="dialog-confirm" style="display:none" title=""></div>
<link rel="stylesheet" type="text/css" href="engine/skins/chosen/chosen.css"/>
<style>.help-button { padding: 2px 7px; margin: 0 5px; background: #555; border: 1px solid #333; border-radius: 3px; color: #fff; } .help-button:hover { cursor: pointer; background: #777; }</style>
<form action="{$PHP_SELF}?mod=autotags&action=save" name="conf" id="conf" method="post">
<div style="padding-top:5px;padding-bottom:2px;display:none" id="addtag">
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
				<td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['autotags_6']} <div style="float:right;display:inline"><a href="javascript:ShowOrHidePanel('addtag');"><span class="btn btn-mini btn-info">{$lang['autotags_6']}</span></a></div></div></td>
			</tr>
		</table>
		<div class="unterline"></div>
		<table width="100%">
HTML;


		$categories_list = CategoryNewsSelection( 0, 0 );
		$sources_list = set_selected( $source_list, "" );
		$types_list = makeDropDown( $type_list, "save_con[__new___type]" );

		showRow( $lang['autotags_7'], $lang['autotags_8'], "<select data-placeholder=\"{$lang['addnews_cat_sel']}\" name=\"save_con[__new___cat][]\" onchange=\"onCategoryChange(this)\" class=\"categoryselect\" multiple style=\"width:100%;max-width:310px;\">{$categories_list}</select>" );
		showRow( $lang['autotags_9'], $lang['autotags_10'], "<input type=\"text\" class=\"edit bk\" style=\"text-align: center;\" name=\"save_con[__new___rules]\" value=\"\" size=\"50\">&nbsp;&nbsp;<span class=\"help-button\" title=\"{$lang['autotags_11']}\" >?</span>" );
		showRow( $lang['autotags_12'], $lang['autotags_13'], "<select data-placeholder=\"{$lang['autotags_14']}\" name=\"save_con[__new___source][]\" onchange=\"onCategoryChange(this)\" class=\"categoryselect\" multiple style=\"width:100%;max-width:310px;\">{$sources_list}</select>" );
		showRow( $lang['autotags_15'], $lang['autotags_16'], $types_list );


echo <<<HTML
			<tr>
				<td colspan="2" style="margin:10px;"><br />
					<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
					<input type="submit" class="btn btn-success" value="{$lang['autotags_45']}">
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
</div>
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
				<td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['autotags_17']} <div style="float:right;display:inline"><a href="javascript:DeleteAllTags();"><span class="btn btn-mini btn-danger">{$lang['autotags_44']}</span></a>&nbsp;&nbsp;<a href="javascript:ShowOrHidePanel('addtag');"><span class="btn btn-mini btn-info">{$lang['autotags_6']}</span></a></div></div></td>
			</tr>
		</table>
		<div class="unterline"></div>
		<table width="100%">
HTML;

	$writed = array( 'use_random', 'use_translit', 'tag_count', 'tag_minchr', 'tag_maxchr', 'tag_seperator', 'tag_blacklist' );
	showRow( $lang['autotags_18'], $lang['autotags_19'], "<input type=\"text\" class=\"edit bk\" style=\"text-align: center;\" name=\"save_con[tag_count]\" value=\"{$sett['tag_count']}\" size=\"10\">&nbsp;&nbsp;<span class=\"help-button\" title=\"{$lang['autotags_37']}\" >?</span>" );
	showRow( $lang['autotags_20'], $lang['autotags_21'], "<input type=\"text\" class=\"edit bk\" style=\"text-align: center;\" name=\"save_con[tag_minchr]\" value=\"{$sett['tag_minchr']}\" size=\"10\">&nbsp;&nbsp;<span class=\"help-button\" title=\"{$lang['autotags_36']}\" >?</span>" );
	showRow( $lang['autotags_22'], $lang['autotags_23'], "<input type=\"text\" class=\"edit bk\" style=\"text-align: center;\" name=\"save_con[tag_maxchr]\" value=\"{$sett['tag_maxchr']}\" size=\"12\">&nbsp;&nbsp;<span class=\"help-button\" title=\"{$lang['autotags_35']}\" >?</span>" );
	showRow( $lang['autotags_24'], $lang['autotags_25'], "<input type=\"text\" class=\"edit bk\" style=\"text-align: center;\" name=\"save_con[tag_seperator]\" value=\"{$sett['tag_seperator']}\" size=\"10\">&nbsp;&nbsp;<span class=\"help-button\" title=\"{$lang['autotags_32']}\" >?</span>" );
	showRow( $lang['autotags_26'], $lang['autotags_27'], "<textarea style=\"width: 350px; height: 100px;\" name=\"save_con[tag_blacklist]\">{$sett['tag_blacklist']}</textarea>&nbsp;&nbsp;<span class=\"help-button\" title=\"{$lang['autotags_33']}\" >?</span>" );
	showRow( $lang['autotags_28'], $lang['autotags_29'], makeCheckBox( "save_con[use_random]", "{$sett['use_random']}" ) );
	showRow( $lang['autotags_30'], $lang['autotags_31'], makeCheckBox( "save_con[use_translit]", "{$sett['use_translit']}" ) );

	$pages = array();
	foreach( $sett as $k_sett => $v_sett ) { if ( ! in_array( $k_sett, $writed ) ) { if ( strpos( $k_sett, "_rules" ) ) { $pages[] = substr( $k_sett, 0, -6 ); } } }

	foreach( $pages as $page ) {
		showSep( );
		$categories_list = CategoryNewsSelection( explode(",", $sett[$page . "_cats"] ), 0 );
		$sources_list = set_selected( $source_list, $sett[$page . "_source"] );
		$page_link = ( $config['allow_alt_url'] ) ? $config['http_home_url']. $page . ".html" : $config['http_home_url']. "index.php?do=autotags&name=" . $page;
		$delete_link = $PHP_SELF . "?mod=autotags&action=delete&page=" . $page . "&user_hash=" . $dle_login_hash;
		$types_list = makeDropDown( $type_list, "save_con[" . $page . "_type]", $sett[ $page . "_type" ] );
		showRow( $lang['autotags_7'], $lang['autotags_8'], "<select data-placeholder=\"{$lang['addnews_cat_sel']}\" name=\"save_con[" . $page . "_cats][]\" onchange=\"onCategoryChange(this)\" class=\"categoryselect\" multiple style=\"width:100%;max-width:310px;\">{$categories_list}</select>" );
		showRow( $lang['autotags_9'], $lang['autotags_10'], "<input type=\"text\" class=\"edit bk\" style=\"text-align: left;\" name=\"save_con[" . $page . "_rules]\" value=\"" . $sett["{$page}_rules"] . "\" size=\"45\">&nbsp;&nbsp;<span class=\"help-button\" title=\"{$lang['autotags_11']}\" >?</span>&nbsp;&nbsp;<a href=\"" . $delete_link . "\" class=\"tip\" title=\"{$lang['autotags_34']}\"><span class=\"btn btn-mini btn-danger\">x</span></a>" );
		showRow( $lang['autotags_12'], $lang['autotags_13'], "<select data-placeholder=\"" . $lang['autotags_46'] . "\" name=\"save_con[" . $page . "_source][]\" onchange=\"onCategoryChange(this)\" class=\"categoryselect\" multiple style=\"width:100%;max-width:310px;\">{$sources_list}</select>" );
		showRow( $lang['autotags_15'], $lang['autotags_16'], $types_list );
	}

echo <<<HTML
			<tr>
				<td colspan="2" style="margin:10px;"><br />
					<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
					<input type="submit" class="btn btn-success" value="{$lang['user_save']}">
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
</div>
</form>
HTML;

echofooter();
?>