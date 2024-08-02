<?php
/**
 * Format a custom field to collect the phone field to look like (###) ###-####
 *
 * title: How to Collect a Member Telephone Number at Checkout
 * layout: snippet
 * collection: checkout
 * category: user fields
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function my_pmpro_format_phone() {
	?>
	<script>
		jQuery( document ).ready( function( $ ) {
			//Change the id below accordingly
			$( '#phone' ).on( 'input', function() {
				 let input = $( this ).val();

				// Remove all non-numeric characters
				input = input.replace( /\D/g, '' );

				// Initialize formatted output
				let formatted = '';

				// Add area code part
				if ( input.length > 0 ) {
					formatted += '(' + input.substring( 0, 3 );
				}
				// Add first 3 digits after area code
				if ( input.length > 3 ) {
					formatted += ') ' + input.substring( 3, 6 );
				}
				// Add last 4 digits
				if ( input.length > 6 ) {
					formatted += '-' + input.substring( 6, 10 );
				}

				// Set the formatted value back to the input field
				$( this ).val( formatted );
			});
		});
	</script>
	<?php
}
add_action( 'pmpro_checkout_after_form', 'my_pmpro_format_phone' );