<?php
/**
 * Hide everything but the last 4 digits of the phone number field for non-members.
 * Update line 22 for your unique user field name.
 *
 * title: Hide Phone Number Field for Non-Members
 * layout: snippet
 * collection: pmpro-member-directory
 * category: directory, profile, privacy
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpromd_get_display_value_mask_phone_field( $value, $element, $pu, $displayed_levels ) {
	if ( empty( $value ) ) {
		return $value;
	}

	if ( ! pmpro_hasMembershipLevel() && $element === 'phone' ) {
		// Remove any non-digit characters to count only numeric digits.
		$numeric_value = preg_replace('/\D/', '', $value);
		$length = strlen($numeric_value);

		// If the number is longer than 4 digits, mask all but the last 4.
		if ( $length > 4 ) {
			$maskLength = $length - 4;
			$masked_number = str_repeat('*', $maskLength) . substr($numeric_value, -4);
		} else {
			// For short numbers, mask all digits.
			$masked_number = str_repeat('*', $length);
		}

		return $masked_number;
	}

	return $value;
}
add_filter( 'pmpromd_get_display_value', 'my_pmpromd_get_display_value_mask_phone_field', 10, 4 );
