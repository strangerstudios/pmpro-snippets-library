<?php
/**
 * Dynamically track UTM parameters on PMPro checkout and use them to redirect to unique confirmation pages.
 * To test, add parameters like &utm_source=google&utm_medium=cpc&utm_campaign=campaign-slug-1 to the end of your checkout page URL.
 * Update the $campaign_redirects to match campaign parameters to real pages on your site.
 *
 * title: Redirect Members to a Unique Confirmation Page Based on UTM Campaign
 * layout: snippet
 * collection: checkout
 * category: confirmation, checkout
 * link: https://www.paidmembershipspro.com/track-utm-parameters-redirect-confirmation-page/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Allowlist of accepted UTM parameter keys.
define( 'MY_PMPRO_ALLOWED_UTM_KEYS', [ 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content' ] );

/**
 * Register hidden UTM fields on the PMPro checkout page only.
 */
function my_pmpro_add_utm_user_fields() {
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return;
	}

	$utm_fields = array();

	foreach ( MY_PMPRO_ALLOWED_UTM_KEYS as $key ) {
		$value = isset( $_REQUEST[ $key ] ) ? sanitize_text_field( $_REQUEST[ $key ] ) : '';

		$utm_fields[] = new PMPro_Field(
			$key,
			'hidden',
			array(
				'label'		  => '',
				'profile'		=> false,
				'required'	   => false,
				'memberslistcsv' => true,
				'value'		  => $value,
			)
		);
	}

	foreach ( $utm_fields as $utm_field ) {
		pmpro_add_user_field( 'before_submit_button', $utm_field );
	}
}
add_action( 'init', 'my_pmpro_add_utm_user_fields' );

/**
 * Redirect to a specific confirmation URL based on utm_campaign stored at checkout.
 * Falls back to the default PMPro confirmation URL if no match is found.
 */
function my_pmpro_redirect_confirmation_url( $rurl, $user_id, $pmpro_level ) {
	$campaign = get_user_meta( $user_id, 'utm_campaign', true ); // or change to your utm variable of choice (utm_source, utm_medium, etc.)

	if ( empty( $campaign ) ) {
		return $rurl;
	}
/**
 * Change the key "your-campaign-slug" and the url "https://example.com/thank-you-page" to match 
 * your campaigns and the pages you want to redirect to.
*/
	$campaign_redirects = array(
		'your-campaign-slug' => 'https://example.com/thank-you-page',
		'your-campaign-slug-2' => 'https://example.com/thank-you-page-2',
		'your-campaign-slug-3' => 'https://example.com/thank-you-page-3',
	);

	if ( isset( $campaign_redirects[ $campaign ] ) ) {
		$rurl = $campaign_redirects[ $campaign ];
	}

	return $rurl;
}
add_filter( 'pmpro_confirmation_url', 'my_pmpro_redirect_confirmation_url', 10, 3 );
