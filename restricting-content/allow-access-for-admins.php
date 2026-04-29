<?php
/**
 * Allow admins without any PMPro membership level to any restricted content.
 *
 * title: Allow admins without any PMPro membership level to any restricted content.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, admin-access
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function pmpro_allow_access_for_admins( $hasaccess, $mypost, $myuser, $post_membership_levels ) {
	if ( ! empty( $myuser ) && user_can( $myuser, 'manage_options' ) ) {
		$hasaccess = true;
	}

	return array( $hasaccess, $post_membership_levels );
}
add_filter( 'pmpro_has_membership_access_filter', 'pmpro_allow_access_for_admins', 30, 4 );
