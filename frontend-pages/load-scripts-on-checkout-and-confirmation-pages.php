<?php
/**
 * Load scripts or styles on PMPro checkout and confirmation pages.
 *
 * title: Load Scripts or Styles to the PMPro Pages of your Membership Site
 * layout: snippet
 * collection: frontend-pages
 * category: content
 * link: https://www.paidmembershipspro.com/load-javascript-scripts-pmpro-pages/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_load_scripts_example() {
	global $pmpro_pages;

	// No PMPro pages found, bail
	if ( empty( $pmpro_pages ) ) {
		return;
	}

	// No PMPro function found, bail.
	if ( ! function_exists( 'pmpro_is_checkout' ) ) {
		return;
	}

	if ( pmpro_is_checkout() || is_page( $pmpro_pages['confirmation'] ) ) {
		?>
			<!-- scripts or styles go here -->
		<?php
	}
}
add_action( 'wp_head', 'my_pmpro_load_scripts_example' );
