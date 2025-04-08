<?php
/**
 * Move a custom user field from its default location to below the
 * email address fields in the account information section with JavaScript.
 *
 * Set the field name you want to move in the `fieldName` variable on line 25.
 *
 * title: Move custom user field to account information section.
 * layout: snippet-example
 * collection: user-fields
 * category: custom-fields
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_move_user_field_to_account_information() {
	// Only run on the checkout page.
	if ( ! function_exists( 'pmpro_is_checkout' ) || ! pmpro_is_checkout() ) {
		return;
	}
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		var fieldName = 'field_name_here'; // The name of the user field you want to move
		var userField = $('#' + fieldName + '_div');

		// Check if the userField exists
		if (userField.length) {
			// Look for the parent div containing email fields
			var emailContainer = $('.pmpro_form_field-bemail').closest('.pmpro_cols-2');

			// Check if the confirm email field exists
			var confirmEmailField = $('.pmpro_form_field-bconfirmemail');

			// If the email container exists, append website field after it
			if (emailContainer.length) {
				emailContainer.after(userField);
			}
		}
	});
	</script>
	<?php
}
add_action( 'wp_footer', 'my_pmpro_move_user_field_to_account_information' );