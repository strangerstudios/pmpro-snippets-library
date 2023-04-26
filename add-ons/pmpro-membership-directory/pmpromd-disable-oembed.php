<?php
/**
 * Remove embeds on the PMPro Member Directory pages.
 *
 * title: Disable oEmbed on Member Directory
 * layout: snippet
 * collection: pmpro-member-directory
 * category: directory, embeds, oembed, clickable, links
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Remove the oembed functionality.
add_filter( 'pmpromd_try_oembed_url', '__return_false' );

// Add a clickable link to field names defined in the $clickable_fields array.
function my_pmpromd_make_clickable( $value, $original_value, $field_name ) {

	/**
	 * Set fields that should have a clickable link here,
	 * e.g. 'user_url', 'website_url', 'user_facebook', etc.
	 */
	$clickable_fields = array( 'user_url' );

	if ( ! empty( $clickable_fields ) && in_array( $field_name, $clickable_fields, true ) ) {
		$value = make_clickable( $value );
	}

	return $value;
}
add_filter( 'pmpromd_format_profile_field', 'my_pmpromd_make_clickable', 10, 3 );
