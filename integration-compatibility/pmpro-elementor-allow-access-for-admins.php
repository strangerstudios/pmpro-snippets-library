<?php
/**
 * Allow admins without any PMPro membership level to any Elementor restricted content.
 *
 * title: Allow admins access to any Elementor restricted content.
 * layout: snippet
 * collection: integration-compatibility
 * category: content, restriction, elementor
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_elementor_allow_access_for_admins( $access, $element, $restricted_levels ) {
	// If user is an admin allow access.
	if ( current_user_can( 'manage_options' ) ) {
		$access = true;
	}

	return $access;
}
add_filter( 'pmpro_elementor_has_access', 'my_pmpro_elementor_allow_access_for_admins', 10, 3 );
