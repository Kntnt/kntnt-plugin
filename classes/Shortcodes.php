<?php


namespace Kntnt\Plugin;


trait Shortcodes {

	public static function boolean( $value ): ?bool {
		switch ( strtolower( $value ) ) {
			case 0:
			case 'no':
			case 'false':
				return false;
			case 1:
			case 'yes':
			case 'true':
				return true;
			default:
				return null;
		}
	}

	// Removes unwanted leading or trailing <br />, <p> and </p> which are
	// artifacts of wpautop() which runs before shortcodes.
	public static function remove_wpautop_artifacts( $content ) {

		if ( substr( $content, 0, 5 ) == "</p>\n" ) {
			$content = substr( $content, 5 );
		}
		else if ( substr( $content, 0, 7 ) == "<br />\n" ) {
			$content = substr( $content, 7 );
		}

		if ( substr( $content, - 4 ) == "\n<p>" ) {
			$content = substr( $content, 0, - 4 );
		}
		else if ( substr( $content, - 7 ) == "<br />\n" ) {
			$content = substr( $content, 0, - 7 );
		}

		return $content;

	}

	// A more forgiving version of WordPress' shortcode_atts().
	public static function shortcode_atts( $pairs, $atts, $shortcode = '' ): array {

		// $atts can be a string which is cast to an array. An empty string should
		// be an empty array (not an array with an empty element as by casting).
		$atts = $atts ? (array) $atts : [];

		$out = [];
		$pos = 0;

		while ( $name = key( $pairs ) ) {
			$default = array_shift( $pairs );
			if ( array_key_exists( $name, $atts ) ) {
				$out[ $name ] = $atts[ $name ];
			}
			else if ( array_key_exists( $pos, $atts ) ) {
				$out[ $name ] = $atts[ $pos ];
				++ $pos;
			}
			else {
				$out[ $name ] = $default;
			}
		}

		if ( $shortcode ) {
			$out = apply_filters( "shortcode_atts_{$shortcode}", $out, $pairs, $atts, $shortcode );
		}

		return $out;

	}

}