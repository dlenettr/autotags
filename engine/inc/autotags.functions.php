<?php
/*
=====================================================
 MWS Auto Tags v1.0 - by MaRZoCHi (MWS)
-----------------------------------------------------
 http://www.dle.net.tr/
-----------------------------------------------------
 Copyright (c) 2014 - DLE.NET.TR
-----------------------------------------------------
 License : GPL License
=====================================================
*/

include ENGINE_DIR . '/data/autotags.conf.php';

if ( ! function_exists( 'array_random_assoc' ) ) {

	function array_random_assoc( $arr, $num = 1 ) {
		$keys = array_keys($arr);
		shuffle($keys);
		$r = array();
		for ($i = 0; $i < $num; $i++) {
			$r[$keys[$i]] = $arr[$keys[$i]];
		}
		return $r;
	}
}

if ( ! function_exists( 'parse_story' ) ) {

	function parse_story( $story ) {

		$quotes = array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", ".", "/", "¬", "#", ";", ":", "@", "~", "[", "]", "{", "}", "=", "-", "+", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"');
		$fastquotes = array ("\x22", "\x60", "\t", "\n", "\r", '"', '\r', '\n', "$", "{", "}", "[", "]", "<", ">");
		$story = preg_replace( "#\[hide\](.+?)\[/hide\]#is", "", $story );
		$story = preg_replace( "'\[attachment=(.*?)\]'si", "", $story );
		$story = preg_replace( "'\[page=(.*?)\](.*?)\[/page\]'si", "", $story );
		$story = str_replace( "{PAGEBREAK}", "", $story );
		$story = str_replace( "&nbsp;", " ", $story );
		$story = str_replace( '<br />', ' ', $story );
		$story = strip_tags( $story );
		$story = preg_replace( "#&(.+?);#", "", $story );
		$story = trim(str_replace( " ,", "", $story ));
		$story = str_replace( $quotes, ' ', $story );

		return $story;
	}
}

$sett['tag_blacklist'] = explode( ",", $sett['tag_blacklist'] );

if ( ! function_exists( 'correct_tag' ) ) {

	function correct_tag( $key, $tag ) {
		global $sett, $db, $config;

		$sett['tag_minchr'] = ( $sett['tag_minchr'] == "0" ) ? 0 : intval( $sett['tag_minchr'] );
		$sett['tag_maxchr'] = ( $sett['tag_maxchr'] == "0" ) ? 9999 : intval( $sett['tag_maxchr'] );
		$sett['tag_seperator'] = ( $sett['tag_seperator'] == "space" ) ? " " : $sett['tag_seperator'];

		$tag = $db->safesql( $tag );

		if ( $sett['use_translit'] ) {
			$tag = totranslit( $tag, false, false );
		}

		if ( ! empty( $key ) && in_array( trim( $key ), $sett['tag_blacklist'] ) ) {

			return false;

		} else {

			$tag = trim( $tag );

			$tag = str_replace( "-", $sett['tag_seperator'], $tag );

			if ( ( dle_strlen( $key, $config['charset'] ) > $sett['tag_minchr'] ) && ( dle_strlen( $key, $config['charset'] ) <= $sett['tag_maxchr'] ) ) {

				return $tag;

			} else {

				return false;

			}
		}
	}
}

if ( isset( $sett ) ) {

	$TAGS = array();

	$settings = array();
	foreach( $sett as $key => $val ) {
		if ( strpos( $key, "tag_" ) === false && strpos( $key, "use_" ) === false ) {
			$_tmp = explode( "_", $key ); $name = $_tmp[0];
			if ( ! in_array( $name, array_keys( $settings ) ) ) {
				$settings[ $name ] = array(
					'source' 	=> explode( ",", $sett[ $name . "_source" ] ),
					'type' 		=> $sett[ $name . "_type" ],
					'cats' 		=> explode( ",", $sett[ $name . "_cats" ] ),
					'rules' 	=> explode( ",", str_replace( array("&#123;","&#125;"), array("{","}"), $sett[ $name . "_rules" ] ) )
				);
			}
		}
	}

	$source_list = array(
		"1" => "title",
		"2" => "short_story",
		"3" => "full_story"
	);

	foreach( $settings as $key => $val ) {

		if ( strpos( $key, "tag_" ) === false || strpos( $key, "use_" ) === false ) {
			$_tmp = explode( "_", $key ); $name = $_tmp[0];
			$SOURCE = $val["source"]; $CATS = $val["cats"]; $TYPE = $val["type"]; $RULES = $val["rules"];
			$_cat = explode( ",", $category_list );
			$_incat = false;
			foreach( $CATS as $CAT ) { if ( in_array( $CAT, $_cat ) ) { $_incat = true; break; } }

			if ( $TYPE == "1" ) {

				$_text = "";
				if ( $_incat ) {
					foreach( $SOURCE as $_source ) {
						$_text .= " ". $$source_list[ $_source ];
					}

				}
				foreach( $RULES as $_rule ) {
					$_tag = correct_tag( $_text, str_replace( array("{word}"), array($_text), $_rule ) );
					if ( $_tag ) $TAGS[] = $_tag;
				}

			} else if ( $TYPE == "2" ) {

				$_text = "";
				if ( $_incat ) {
					foreach( $SOURCE as $_source ) {
						$_text .= " ". $$source_list[ $_source ];
					}

					$_text_arr = explode( " ", $_text );
				}
				foreach( $_text_arr as $_text ) {
					foreach( $RULES as $_rule ) {
						$_tag = correct_tag( $_text, str_replace( array("{word}"), array($_text), $_rule ) );
						if ( $_tag ) $TAGS[] = $_tag;
					}
				}
			}
		}
	}

	if ( count( $TAGS ) > 0 ) {
		if ( $sett['use_random'] ) { $TAGS = array_random_assoc( $TAGS, count( $TAGS ) ); }

			if ( $sett['tag_count'] != "0" ) {
				$TAGS = array_slice( $TAGS, 0, intval( $sett['tag_count'] ) );
			}

		$_POST['tags'] = implode( ",", $TAGS );
	}

}

?>