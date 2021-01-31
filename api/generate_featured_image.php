<?php
	namespace Gamux;
	
	require_once ABSPATH . '/wp-includes/post.php';
	require_once ABSPATH . '/wp-admin/includes/file.php';
	require_once ABSPATH . '/wp-admin/includes/media.php';
	require_once ABSPATH . '/wp-admin/includes/image.php';
	
	/**
	* Downloads an image from the specified URL and attaches it to a post as a post thumbnail.
	*
	* @param string $file    The URL of the image to download.
	* @param int    $post_id The post ID the post thumbnail is to be associated with.
	* @param string $desc    Optional. Description of the image.
	* @return string|WP_Error Attachment ID, WP_Error object otherwise.
	*/
	function generate_featured_image( $file, $post_id, $desc = "" ){
		// Set variables for storage, fix file filename for query strings.
		preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
		if ( ! $matches ) {
			return new WP_Error( 'image_sideload_failed', __( 'Invalid image URL' ) );
		}

		$file_array = array();
		$file_array['name'] = basename( $matches[0] );

		// Download file to temp location.
		$file_array['tmp_name'] = download_url( $file );

		// If error storing temporarily, return the error.
		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			return $file_array['tmp_name'];
		}

		// Do the validation and storage stuff.
		$id = media_handle_sideload( $file_array, $post_id, $desc );

		// If error storing permanently, unlink.
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );
			return $id;
		}
		return set_post_thumbnail( $post_id, $id );

	}
	
?>