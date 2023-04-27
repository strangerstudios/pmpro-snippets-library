<?php
/**
 * Add a renew_url link to the membership_expiring email data.
 * Add renew_url as the redirect_to querystring parameter to login_url.
 *
 * Example of using these email variables in the body of the membership_expiring email template:
 *
 * <p>Thank you for your membership to !!sitename!!. This is just a reminder that your membership will end on !!enddate!!.</p>
 * <p>Account: !!display_name!! (!!user_email!!)</p>
 * <p>Membership Level: !!membership_level_name!!</p>
 * <p>Renew your membership here: !!renew_url!!</p>
 * <p>Log in to your membership account here: !!login_url!!</p>
 *
 * title: Add renew URL
 * layout: snippet
 * collection: email
 * category: email, email-data
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_membership_expiring_email_add_renewal_link_to_login_url( $data, $email ) {
	// set the renew_url to default to the levels page
	$data['renew_url'] = pmpro_url( 'levels', '', 'https' );

	if ( 'membership_expiring' === $email->template && ! empty( $data['membership_id'] ) ) {

		// Construct link to checkout page for the membership level that is expiring.
		$checkout_page = pmpro_url( 'checkout', '?level=' . $data['membership_id'], 'https' );

		// Set the renew_url to the checkout page.
		$data['renew_url'] = $checkout_page;

		// Add the checkout page as a redirect_to querystring parameter to the login_url.
		$data['login_url'] = add_query_arg( 'redirect_to', rawurlencode( $checkout_page ), $data['login_url'] );
	}

	return $data;
}
add_filter( 'pmpro_email_data', 'my_pmpro_membership_expiring_email_add_renewal_link_to_login_url', 20, 2 );
