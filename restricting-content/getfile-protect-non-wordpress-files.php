<?php
/**
 * Protect non-WordPress files in a directory and only serve them to members.
 * Loads a file from a protected directory (e.g. /protected-directory/) and
 * redirects non-members to the login page before the file is served.
 *
 * title: Protect Non-WordPress Files in a Directory for Members Only
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, member-access
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 *
 * Setup instructions:
 * (!) Update the PROTECTED_DIR constant below to the name of the folder to protect.
 * (!) Update the pmpro_hasMembershipLevel() call below with the level ID or array
 *     of level IDs you want to require for access.
 * (!) Add a corresponding rule to your Apache .htaccess file to redirect files to
 *     this script. e.g.
 *
 *     ###
 *     # BEGIN protected folder lock down
 *     <IfModule mod_rewrite.c>
 *     RewriteBase /
 *     RewriteRule ^protected-directory/(.*)$ /?pmpro_getfile=$1 [L]
 *     </IfModule>
 *     # END protected folder lock down
 *     ###
 */
define( 'PROTECTED_DIR', 'protected-directory' ); // Change this to the name of the folder to protect.

function my_pmpro_getfile() {
	// Only act on protected file requests.
	if ( ! isset( $_GET['pmpro_getfile'] ) ) {
		return;
	}

	// Prevent loops when redirecting to .php files.
	if ( ! empty( $_GET['noloop'] ) ) {
		status_header( 500 );
		die( 'This file cannot be loaded through the get file script.' );
	}

	$uri = sanitize_text_field( wp_unslash( $_GET['pmpro_getfile'] ) );
	if ( ! empty( $uri ) && '/' === $uri[0] ) {
		$uri = substr( $uri, 1, strlen( $uri ) - 1 );
	}

	/*
	 * Remove ../-like strings from the URI.
	 * Actually removes any combination of two or more ., /, and \.
	 * This will prevent traversal attacks and loading hidden files.
	 */
	$uri = preg_replace( '/[\.\/\\\\]{2,}/', '', $uri );

	// Point at your protected directory.
	$new_uri = PROTECTED_DIR . '/' . $uri;

	$filename = ABSPATH . $new_uri;

	// Remove params from the end.
	if ( false !== strpos( $filename, '?' ) ) {
		$parts    = explode( '?', $filename );
		$filename = $parts[0];
	}

	// Add index.html if this is a directory.
	if ( is_dir( $filename ) ) {
		$filename .= 'index.html';
	}

	// Only check membership for non-admins.
	if ( ! current_user_can( 'manage_options' ) ) {
		// Non-members don't have access. Edit the level ID or array of level IDs below.
		if ( ! pmpro_hasMembershipLevel( array( 1, 2 ) ) ) {
			wp_redirect( wp_login_url( home_url( $new_uri ) ) );
			exit;
		}
	}

	// Get the mimetype.
	require_once PMPRO_DIR . '/classes/class.mimetype.php';
	$mimetype      = new pmpro_mimetype();
	$file_mimetype = $mimetype->getType( $filename );

	// In case we want to do something else with the file.
	do_action( 'pmpro_getfile_before_readfile', $filename, $file_mimetype );

	// If the file is not found, die.
	if ( ! file_exists( $filename ) ) {
		status_header( 404 );
		nocache_headers();
		die( 'File not found.' );
	}

	// If this is a blocklisted file type, redirect to it instead.
	$basename = basename( $filename );
	$parts    = explode( '.', $basename );
	$ext      = strtolower( $parts[ count( $parts ) - 1 ] );

	// Build the blocklist and allow for filtering.
	$blocklist = array( 'inc', 'php', 'php3', 'php4', 'php5', 'phps', 'phtml' );
	$blocklist = apply_filters( 'pmpro_getfile_extension_blocklist', $blocklist );

	// Check the extension against the blocklist.
	if ( in_array( $ext, $blocklist, true ) ) {
		// Add a noloop param to avoid infinite loops.
		$uri = add_query_arg( 'noloop', 1, $uri );

		// Redirect to the file directly using the site URL.
		wp_redirect( home_url( $uri ) );
		exit;
	}

	// Okay, show the file.
	header( 'Content-type: ' . $file_mimetype );
	readfile( $filename );
	exit;
}
add_action( 'init', 'my_pmpro_getfile' );
