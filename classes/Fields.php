<?php


namespace Kntnt\Plugin;


trait Fields {

	public static function get_field( $field, $type = 'post', $object = null, $sep = '/' ) {
		if ( ! in_array( $type, [ 'post', 'user', 'acf', 'option' ] ) ) {
			return null;
		}
		$data = self::$type( explode( $sep, $field ), $object );
		return $data;
	}

	private static function post( $fields, $object ) {
		if ( in_array( $fields[0], [ 'ID', 'post_author', 'post_content', 'post_date', 'post_excerpt', 'post_modified', 'post_name', 'post_parent', 'post_status', 'post_title', 'comment_count', 'comment_status' ] ) ) {
			if ( $post = get_post( $object, 'OBJECT', 'display' ) ) {
				$field = array_shift( $fields );
				$data = $post->$field;
				if ( 'post_parent' == $field && count( $fields ) && $data ) {
					$data = self::post( $fields, $data );
				}
				if ( 'post_author' == $field && count( $fields ) ) {
					$data = self::user( $fields, $data );
				}
				return $data;
			}
		}
		else if ( 'featured_image' == $fields[0] ) {
			$id = get_metadata( 'post', $object, '_thumbnail_id', true );
			switch ( $fields[1] ?? null ) {
				case 'id':
					return $id;
				case 'alt':
					return get_post_meta( $id, '_wp_attachment_image_alt', true );
				case 'caption':
					return get_post( $id )->post_excerpt;
				case 'title':
					return get_post( $id )->post_title;
				case 'description':
					return get_post( $id )->post_content;
				case 'url':
					return get_permalink( $id );
				case 'html':
					return wp_get_attachment_image( $id, $fields[2] ?? 'thumbnail' );
				default:
					return null;
			}
		}
		else {
			$metadata = get_metadata( 'post', $object, array_shift( $fields ), true );
			return self::subfield( $fields, $metadata );
		}
	}

	private static function user( $fields, $object ) {
		return get_the_author_meta( $fields[0], $object );
	}

	private static function acf( $fields, $object ) {
		return function_exists( 'get_field' ) ? get_field( $fields[0], $object ) : null;
	}

	private static function option( $fields, $object ) {
		$option = get_option( array_shift( $fields ), null );
		return self::subfield( $fields, $option );
	}

	private static function subfield( $fields, $data ) {
		if ( $field = array_shift( $fields ) ) {
			if ( $data = $data[ $field ] ?? null ) {
				$data = self::subfield( $fields, $data );
			}
		}
		return $data;
	}

}