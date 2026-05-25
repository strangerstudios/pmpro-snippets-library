<?php
/**
 * Create a function and a shortcode to detect users with or without paid invoices.
 * Show Content to Paying or Not Paying Members Only
 *
 * title: Show Content To Paying or Not Paying Members
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction
 * link: https://www.paidmembershipspro.com/haspaid-shortcode-show-content-paying-members
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Check whether a user has any successful paid PMPro order.
 *
 * @param  int|null $user_id  WordPress user ID. Defaults to the current user when null/empty.
 * @param  int|null $level_id Optional PMPro membership level ID to restrict the check to.
 * @return bool     True if the user has at least one matching paid order, false otherwise.
 */
function my_hasPaid( $user_id = null, $level_id = null ) {
	global $wpdb;

	// Make sure PMPro is active
	if ( ! function_exists( 'pmpro_getOption' ) ) {
		return false;
	}

	// No user passed? Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}
	
	// No user?
	if ( empty( $user_id ) ) {
		return false;
	}
	
	// Figure out if we are in a live or test gateway environment
	$environment = pmpro_getOption( 'gateway_environment' );
	
	// Query to check
	$sql  = "SELECT COUNT(*) FROM $wpdb->pmpro_membership_orders WHERE user_id = %d AND gateway_environment = %s AND total > 0 AND status NOT IN('error', 'refunded', 'token', 'review')";
	$args = array( $user_id, $environment );

	if ( ! empty( $level_id ) ) {
		$sql   .= " AND membership_id = %d";
		$args[] = $level_id;
	}

	// Get val
	$paid = $wpdb->get_var( $wpdb->prepare( $sql, $args ) );
	
	// Force true/false
	return (bool) $paid;
}

/**
 * Add [haspaid] shortcode to conditionally render content based on the user's payment history.
 *
 * Examples:
 * [haspaid]This will show up if the user has paid for any level.[/haspaid]
 * [haspaid paid='0']This will show up if the user has NOT paid for any level.[/haspaid]
 * [haspaid paid='1' level='1']This will show up if the user has paid for level 1 specifically.[/haspaid]
 * [haspaid paid='0' level='1']This will show up if the user has not paid for level 1 specifically.[/haspaid]
 *
 * @param  array       $atts    Shortcode attributes: 'paid' (true by default; '0'/'false' inverts) and 'level' (PMPro level ID, optional).
 * @param  string|null $content Enclosed shortcode content to render conditionally.
 * @param  string      $code    The shortcode tag. Unused.
 * @return string      Rendered content, or an empty string when the condition is not met.
 */
function my_haspaid_shortcode( $atts, $content = null, $code = "" ) {
	$atts = shortcode_atts( array(
		'paid'  => true,
		'level' => null,
	), $atts );
	$paid  = $atts['paid'];
	$level = $atts['level'];
		
	// Convert paid attribute to bool
	if ( $paid === '0' || $paid === 'false' ) {
		$paid = false;
	} else {
		$paid = true;
	}
	
	// To show or not to show
	if ( my_hasPaid( null, $level ) ) {
		// Return content if paid
		if ( $paid ) {
			return do_shortcode( $content );	// show content
		} else {
			return '';
		}
	} else {
		// Return content if NOT paid
		if ( ! $paid ) {
			return do_shortcode( $content );	// show content
		} else {
			return "";	// just hide it
		}
	}
}
add_shortcode( 'haspaid', 'my_haspaid_shortcode' );
