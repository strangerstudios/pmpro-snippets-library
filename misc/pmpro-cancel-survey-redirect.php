<?php
/**
 * Change the default PMPro Cancel link to the cancellation survey page.
 *
 * title: PMPro Cancellation Survey - Redirect Cancel Link to Survey Page
 * layout: snippet
 * collection: misc
 * category: cancellation
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function pmpro_cancel_survey_replace_cancel_link( $url ) {
	$page = get_page_by_path( 'cancel-my-membership' );
	if ( $page ) {
		return get_permalink( $page );
	}
	return $url;
}
add_filter( 'pmpro_cancel_link', 'pmpro_cancel_survey_replace_cancel_link' );
