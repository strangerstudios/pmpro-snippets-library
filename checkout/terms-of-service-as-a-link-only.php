<?php
/**
 * Add a Terms & Conditions checkbox on checkout with a link to either a media file, e.g. PDF, or page.
 *
 * For a CSS example to enhance the appearance of the checkbox and link, 
 * see this companion gist to get started:
 * @link https://gist.github.com/ipokkel/47d227e4bb160b26f594c26f5ee0e83a#file-terms-and-conditions-css
 *
 * title: Terms Of Service As A Link Only
 * layout: snippet
 * collection: checkout
 * category: registration-check
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
// Declare function if it does not already exist.
if ( ! function_exists( 'my_pmpro_init_require_tos_link' ) ) {
	function my_pmpro_init_require_tos_link() {

		// Check if PMPro Core is active
		if ( ! function_exists( 'pmpro_add_user_field' ) ) {
			return;
		}

		// Check that display TOS Setting is disabled.
		if ( ! empty( pmpro_getOption( 'tospage' ) ) ) {
			return;
		}

		/**
		 * Get the link to the Terms & Conditions.
		 *
		 * To link to an existing page on your site, instead of a media file,
		 * you may replace this line:
		 *
		 * $tos_link = wp_get_attachment_url( 73 ); // Set your media file ID here.
		 *
		 * with this
		 *
		 * $tos_link = get_permalink( 177 ); // Set Terms & Conditions post ID here.
		 */
		$tos_link = wp_get_attachment_url( 73 ); // Set your media file ID here.

		// Create label that displays a link to the Terms & Conditions.
		$label = '&nbsp;&nbsp;&nbsp;&nbsp; I agree to the <a href="' . $tos_link . '" target="_blank" id="my-pmpro-require-tos-link" class="my-pmpro-require-tos-link">Terms & Conditions</a>.';

		// Create fields array.
		$fields = array();

		// Terms & Conditions Checkbox field.
		$fields[] = new PMPro_Field(
			'pmpro_tos_checked',
			'checkbox',
			array(
				'label'          => $label,
				'required'       => true, // make field required
				'memberslistcsv' => true, // include in memberslist csv
				'profile'        => false, // do not display on user profile page
			)
		);

		foreach ( $fields as $field ) {
			pmpro_add_user_field( 'before_submit_button', $field );
		}
	}
}

// Add action
if ( ! has_action( 'init', 'my_pmpro_init_require_tos_link' ) ) {
	add_action( 'init', 'my_pmpro_init_require_tos_link' );
}
