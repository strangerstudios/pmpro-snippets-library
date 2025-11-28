<?php
/**
 * Display custom content in the marker info window.
 *
 * title: Add Information to All Map Markers
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
function mypmpromd_single_marker_content( $content, $member ) {

	$graduation_year = get_user_meta( $member->ID, 'graduation_year', true );

	$content .= '<p><strong>Graduation Year</strong> ' . $graduation_year . '</p>';

	return $content;
}
add_filter( 'pmpromd_single_marker_content', 'mypmpromd_single_marker_content', 10, 2 );
