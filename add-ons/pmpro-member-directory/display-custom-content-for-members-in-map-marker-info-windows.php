<?php
/**
 * Display custom content in the marker info window if the viewer is a member of level 1 or 2.
 *
 * title: Add Information If the Viewing User Has Specific Membership Levels.
 * layout: snippet
 * collection: pmpro-member-directory
 * category: maps, markers
 * link: https://www.paidmembershipspro.com/adding-custom-content-to-your-marker-info-window/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function mypmpromd_single_marker_content_protected( $content, $member ) {

	if ( pmpro_hasMembershipLevel( 1, 2 ) ) { // Change levels to your preference.

		$graduation_year = get_user_meta( $member->ID, 'graduation_year', true );

		$content .= '<p><strong>Graduation Year</strong> ' . $graduation_year . '</p>';

	}

	return $content;
}
add_filter( 'pmpromd_single_marker_content', 'mypmpromd_single_marker_content_protected', 10, 2 );
