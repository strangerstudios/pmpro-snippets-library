<?php
/**
 * Display restricted PDFs inline in the browser instead of forcing a download.
 *
 * For more information about restricting files with PMPro visit: https://www.paidmembershipspro.com/locking-down-protecting-files-with-pmpro/
 *
 * title: Display Restricted PDFs in the Browser
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_inline_restricted_pdfs( $content_disposition, $file, $file_dir, $file_path ) {
	$filetype = wp_check_filetype( $file_path );

	if ( 'application/pdf' === $filetype['type'] ) {
		return 'inline';
	}

	return $content_disposition;
}
add_filter( 'pmpro_restricted_file_content_disposition', 'my_pmpro_inline_restricted_pdfs', 10, 4 );
