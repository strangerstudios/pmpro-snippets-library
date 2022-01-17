<?php
/**
 * Load custom CSS on checkout page for specific membership level in Paid Memberships Pro.
 *
 * title: Load custom CSS on checkout page
 * layout: snippet
 * collection: checkout
 * category: css
 *
 * Change the level value to the relevant level ID you want to apply this CSS code to.
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_load_css_for_level_checkout() {
	global $pmpro_pages;

	if ( is_page( $pmpro_pages['checkout'] ) && isset( $_REQUEST['level'] ) == '1' ) {
		?>
		<style type="text/css">
			#other_discount_code_p {display: none;}
			#other_discount_code_tr {display: table-row !important;}
		</style>
		<?php
	}

}
add_action( 'wp_footer', 'my_load_css_for_level_checkout', 10 );
