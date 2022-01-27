<?php
/**
 * Change the wording from 'Membership' to 'Subscription' in Paid Memberships Pro
 *
 * This filter will search your codebase for translatable strings and replace when an exact match is found.
 *
 * title: Change the wording from 'Membership' to 'Subscription' in Paid Memberships Pro
 * layout: snippet
 * collection: misc
 * category: localization
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Here we're changing 'Membership' to 'Subscription' for Paid Memberships Pro.
 *
 * @param  string $output_text     this represents the end result
 * @param  string $input_text      what is written in the code that we want to change
 * @param  string $domain          text-domain of the plugin/theme that contains the code
 *
 * @return string                  the result of the text transformation
 */
function my_gettext_membership( $output_text, $input_text, $domain ) {

	if ( ! is_admin() && 'paid-memberships-pro' === $domain ) {

		$output_text = str_replace( 'Membership Level', 'Subscription', $output_text );
		$output_text = str_replace( 'membership level', 'subscription', $output_text );
		$output_text = str_replace( 'membership', 'subscription', $output_text );
		$output_text = str_replace( 'Membership', 'Subscription', $output_text );

	}

	return $output_text;

}
add_filter( 'gettext', 'my_gettext_membership', 10, 3 );

// Support _n calls.
function my_ngettext_membership( $output_text, $single, $plural, $number, $domain ) {

	if ( $number == 1 ) {
		return my_gettext_membership( $output_text, $single, $domain );
	} else {
		return my_gettext_membership( $output_text, $plural, $domain );
	}

}
add_filter( 'ngettext', 'my_ngettext_membership', 10, 5 );
