<?php


namespace Kntnt\Plugin;


trait Fields {

	// Returns the value of the custom field `$field`. If a `get_field()`-
	// function is provided by a plugin, e.g. ACF, it is called with `$field`
	// as the only argument. If a `get_field()`-function doesn't exist,
	// WordPress `get_metadata()`-function is called with all provided
	// arguments.
	public static final function get_field( $field, $post_id, $single = false, $type = 'post' ) {
		if ( function_exists( 'get_field' ) ) {
			// If ACF is installed, let it get the field.
			return get_field( $field, $post_id );
		}
		else {
			// If ACF not installed, let's do it ourselves.
			return get_metadata( $type, $post_id, $field, $single );
		}
	}

}
