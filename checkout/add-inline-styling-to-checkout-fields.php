<?php
/**
 * Add inline label styling to Paid Memberships Pro checkout page.
 *
 * title: Add inline styling to Paid Memberships Pro checkout page.
 * layout: snippet
 * collection: checkout
 * category: css, styling
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_load_inline_labels_checkout() {

	global $pmpro_pages;

	if ( ! is_page( $pmpro_pages['checkout'] ) ) {
		return;
	}
	?>
<style>
	@media only screen and (min-width: 1200px) {
		#pmpro_form h3 {
			display: grid;
			grid-template-columns: 2fr 1fr;
		}
		.pmpro_checkout .pmpro_checkout-fields .pmpro_checkout-field { clear: left; } 
		.pmpro_checkout .pmpro_checkout-fields .pmpro_checkout-field label {
			float: left;
			margin: 0 1em 0 0;
			text-align: right;
			width: 200px;
		}
		.pmpro_checkout .pmpro_checkout-field.pmpro_captcha, .pmpro_checkout .pmpro_checkout-field-text p {
			margin-left: 200px;
			padding-left: 1em;
		}
		.pmpro_checkout .pmpro_checkout-field-text p { margin-top: 0; }
		form.pmpro_form .pmpro_submit { text-align: right; }
		.pmpro_btn.pmpro_btn-submit-checkout { width: auto; }

		/* The CSS below, is only intended to style the "First Name & Last Name" 
		fields when using the Paid Memberships Pro "Add Name to Checkout Add On". Do not include unless using these fields. */
		label[for="first_name"], label[for="last_name"] {
			clear: left !important;
			float: left !important;
			margin: 0 1em 0 0 !important;
			text-align: right !important;
			width: 200px !important;
		} 	
	}
</style>
	<?php
}
add_action( 'wp_footer', 'my_load_inline_labels_checkout', 20 );
