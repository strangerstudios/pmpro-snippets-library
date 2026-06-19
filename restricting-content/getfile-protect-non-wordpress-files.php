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
	if ( empty( $_REQUEST['pmpro_getfile'] ) ) {
		return;
	}

	// Members only. No args allows any active member; pass level IDs to restrict,
	// e.g. pmpro_hasMembershipLevel( array( 3, 7, 10 ) ).
	if ( ! pmpro_hasMembershipLevel() ) {
		wp_die( esc_html__( 'This file is available to members only.', 'your-text-domain' ), '', array( 'response' => 403 ) );
	}

	// Real files live here (anywhere readable, even outside the web root). Only let
	// trusted admins write here — a planted file (e.g. a <script> SVG) runs in members' browsers.
	$base_dir  = trailingslashit( wp_upload_dir()['basedir'] ) . 'protected-tools';
	$real_base = realpath( $base_dir );

	// Strip "../" style sequences so visitors can't climb out of the folder.
	$requested = preg_replace( '#[./\\\\]{2,}#', '', wp_unslash( $_REQUEST['pmpro_getfile'] ) );
	$file_path = realpath( $base_dir . '/' . ltrim( $requested, '/' ) );

	// Confirm the path is a real file still inside the folder. The DIRECTORY_SEPARATOR
	// stops a sibling like "protected-tools-evil" from matching the prefix.
	if ( false === $real_base || false === $file_path
		|| strpos( $file_path, $real_base . DIRECTORY_SEPARATOR ) !== 0
		|| ! is_file( $file_path ) ) {
		wp_die( esc_html__( 'File not found.', 'your-text-domain' ), '', array( 'response' => 404 ) );
	}

	// finfo reports .css/.js as "text/plain", which browsers won't apply, so map
	// common web types by extension first and fall back to finfo for the rest.
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
		// finfo_open() returns false if the fileinfo extension is missing.
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		if ( false === $finfo ) {
			$content_type = 'application/octet-stream';
		} else {
			$content_type = finfo_file( $finfo, $file_path );
			finfo_close( $finfo );
		}
	}

	// Serve inline so files render in the browser; use "attachment" to force a download.
	nocache_headers();
	header( 'Content-Type: ' . $content_type );
	header( 'Content-Disposition: inline; filename="' . basename( $file_path ) . '"' );
	header( 'Content-Length: ' . filesize( $file_path ) );

	readfile( $file_path );
	exit;
}
add_action( 'init', 'my_pmpro_getfile' );
