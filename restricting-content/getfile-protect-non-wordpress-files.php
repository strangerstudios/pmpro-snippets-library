<?php
/**
 * Serve a folder of non-WordPress files (e.g. an AI-generated artifact:
 * index.html + JSON/CSS/JS/images) only to logged-in members.
 *
 * title: Protect Non-WordPress Files and Folders and Serve Them Inline to Members
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, member-access
 *
 * link: https://www.paidmembershipspro.com/locking-non-wordpress-files-folders-paid-memberships-pro/
 *
 * Pairs with a rewrite rule that routes /protected-tools/ requests to
 * index.php?pmpro_getfile={path}. For Apache, add this to the TOP of .htaccess,
 * before the WordPress rules:
 *
 *   # BEGIN protected folder lock down
 *   RewriteBase /
 *   RewriteRule ^protected-tools/(.*)$ /index.php?pmpro_getfile=$1 [L]
 *   # END protected folder lock down
 *
 * For NGINX:
 *
 *   rewrite ^/protected-tools/(.*)$ /index.php?pmpro_getfile=$1 last;
 *
 * Files are served INLINE so the artifact renders in the browser (and inside an
 * iframe) instead of downloading. Requires the PHP `fileinfo` extension.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_getfile() {
	// Only act on protected-folder requests.
	if ( empty( $_REQUEST['pmpro_getfile'] ) ) {
		return;
	}

	// Membership check first, before any filesystem work. Checking here (rather
	// than after resolving the path) means non-members always get a 403 whether or
	// not the requested file exists, so the response can't be used to probe for it.
	// With no arguments this allows any active member. To limit to specific levels,
	// pass their IDs, e.g.: if ( ! pmpro_hasMembershipLevel( array( 3, 7, 10 ) ) ).
	if ( ! pmpro_hasMembershipLevel() ) {
		wp_die( esc_html__( 'This file is available to members only.', 'your-text-domain' ), '', array( 'response' => 403 ) );
	}

	// Where the real files live. This can be anywhere your server can read,
	// including outside the web root. Here we use a folder in wp-content/uploads.
	// Only let trusted admins write to this folder: a malicious file placed here
	// (e.g. an SVG containing <script>) would be served to members and could run
	// JavaScript in their browser.
	$base_dir  = trailingslashit( wp_upload_dir()['basedir'] ) . 'protected-tools';
	$real_base = realpath( $base_dir );

	// Strip any "../" style sequences so visitors can't climb out of the folder.
	$requested = preg_replace( '#[./\\\\]{2,}#', '', wp_unslash( $_REQUEST['pmpro_getfile'] ) );
	$file_path = realpath( $base_dir . '/' . ltrim( $requested, '/' ) );

	// Confirm the resolved path is a real file still inside the protected folder.
	// The trailing slash on $real_base matters: without it, a sibling folder whose
	// name merely starts with "protected-tools" would pass this check.
	if ( false === $real_base || false === $file_path
		|| strpos( $file_path, $real_base . DIRECTORY_SEPARATOR ) !== 0
		|| ! is_file( $file_path ) ) {
		wp_die( esc_html__( 'File not found.', 'your-text-domain' ), '', array( 'response' => 404 ) );
	}

	// Figure out the MIME type so the browser handles the file correctly.
	// Map the common web types by extension first: finfo detects .css and .js as
	// "text/plain", and browsers won't apply a stylesheet or run a script served
	// that way. Fall back to finfo for everything else (images, PDFs, fonts, etc.).
	$ext   = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
	$known = array(
		'html' => 'text/html',
		'css'  => 'text/css',
		'js'   => 'application/javascript',
		'json' => 'application/json',
		'svg'  => 'image/svg+xml',
	);

	if ( isset( $known[ $ext ] ) ) {
		$content_type = $known[ $ext ];
	} else {
		// finfo_open() returns false if the fileinfo extension is unavailable;
		// guard against it so we don't fatal by passing false to finfo_file().
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		if ( false === $finfo ) {
			$content_type = 'application/octet-stream';
		} else {
			$content_type = finfo_file( $finfo, $file_path );
			finfo_close( $finfo );
		}
	}

	// Serve INLINE so HTML/CSS/JS/JSON render in the browser (and inside an
	// iframe) instead of downloading. Change to "attachment" to force a download.
	nocache_headers();
	header( 'Content-Type: ' . $content_type );
	header( 'Content-Disposition: inline; filename="' . basename( $file_path ) . '"' );
	header( 'Content-Length: ' . filesize( $file_path ) );

	readfile( $file_path );
	exit;
}
add_action( 'init', 'my_pmpro_getfile' );
