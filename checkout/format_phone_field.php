<?php
/**
 * Format the billing phone field to look like (###) ###-####.
 * You may adjust this code to target custom phone fields if necessary.
 *
 *
 * title: Format the billing phone number to be (###) ###-####
 * layout: snippet
 * collection: checkout
 * category: phone, user-fields
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 function my_pmpro_format_phone() {
	?>
	<script>
	    jQuery(document).ready(function () {
	        // Change the id below accordingly.
	        jQuery('#bphone').on('input', function () {
	            let input = jQuery(this).val();
	
	            // Remove all non-numeric characters
	            input = input.replace(/\D/g, '');
	
	            // Limit input to 10 characters (standard US phone number format)
	            if (input.length > 10) {
	                input = input.substring(0, 10);
	            }
	
	            // Format input as (XXX) XXX-XXXX
	            let formatted = '';
	
	            if (input.length > 0) {
	                formatted = '(' + input.substring(0, Math.min(3, input.length));
	            }
	            if (input.length >= 4) {
	                formatted += ') ' + input.substring(3, Math.min(6, input.length));
	            }
	            if (input.length >= 7) {
	                formatted += '-' + input.substring(6, Math.min(10, input.length));
	            }
	
	            // Set the formatted value back to the input field
	            jQuery(this).val(formatted);
	        });
	    });
	</script>
	<?php
}
add_action( 'pmpro_checkout_after_form', 'my_pmpro_format_phone' );
