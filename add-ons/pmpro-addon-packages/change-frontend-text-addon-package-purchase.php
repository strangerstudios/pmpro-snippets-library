<?php
/**
 * Change Front End Text for Addon Packages.
 *
 * title: Change Frontend Text for Add On Packages
 * layout: snippet
 * collection: addons
 * category: pmpro-addon-packages
 * url: https://www.paidmembershipspro.com/change-frontend-text-addon-package-purchase-using-wp-gettext-filter/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 // Update Your custom text your preferred messages., romove elseif 
 // condition if you don't want to change that text.
function my_gettext_pmproap_changes( $translated_text, $text, $domain ) {
	if ( $domain == 'pmpro-addon-packages' ) {
		if ( $text == 'This content requires that you purchase additional access. The price is %s.' ) {
			$translated_text = 'Your custom text. The price is %s.';
		} elseif ( $text == 'This content requires that you purchase additional access. The price is %1$s or free for our %2$s members.' ) {
			$translated_text = 'Your custom text. The price is %1$s for %2$s members.';
		} elseif ( $text == 'Purchase this Content (%s)' ) {
			$translated_text = 'Your custom text. %s';
		} elseif ( $text == 'Click here to checkout' ) {
			$translated_text = 'Your custom text. ';
		} elseif ( $text == 'Click here to choose a membership level.' ) {
			$translated_text = 'Your custom text.';
		} elseif ( $text == 'Choose a Membership Level' ) {
			$translated_text = 'Your custom text.';
		} elseif ( $text == 'Choose a Level' ) {
			$translated_text = 'Your custom text.';
		} elseif ( $text == 'Buy Now' ) {
			$translated_text = 'Your custom text.';
		}
	}

	return $translated_text;
}
add_filter( 'gettext', 'my_gettext_pmproap_changes', 10, 3 );
