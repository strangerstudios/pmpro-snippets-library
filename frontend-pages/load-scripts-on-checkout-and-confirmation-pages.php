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
function load_my_script_for_pmpro_pages() {
	global $pmpro_pages;

	if ( empty( $pmpro_pages ) ) {
		return;
	}

	if ( is_page( $pmpro_pages['checkout'] ) || is_page( $pmpro_pages['confirmation'] ) ) {
		?>
			<!-- scripts or styles go here -->
		<?php
	}
}
add_action( 'wp_head', 'load_my_script_for_pmpro_pages' );
