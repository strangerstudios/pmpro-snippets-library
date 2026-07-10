<?php
/**
 * Restrict the "premium-downloads" directory to levels 1, 2, and 3.
 *
 * Files should be added to:
 * wp-content/uploads/pmpro-<random_string>/premium-downloads/
 *
 * Files can be accessed via a URL like:
 * https://example.com/?pmpro_restricted_file_dir=premium-downloads&pmpro_restricted_file=<file_name>
 *
 * title: Restricting files
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction
 * link: https://www.paidmembershipspro.com/locking-down-protecting-files-with-pmpro/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_can_access_premium_downloads( $can_access, $file_dir ) {
	if ( 'premium-downloads' === $file_dir ) {
		return pmpro_hasMembershipLevel( array( 1, 2, 3 ) );
	}

	return $can_access;
}
add_filter( 'pmpro_can_access_restricted_file', 'my_pmpro_can_access_premium_downloads', 10, 2 );
