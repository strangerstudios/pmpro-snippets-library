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
// Check if a user ever paid
function my_hasPaid( $user_id = null, $level_id = null ) {
	global $current_user, $wpdb;
	
	// Make sure PMPro is active
	if ( ! function_exists( 'pmpro_getOption' ) ) {
		return false;
	}
	
	// No user passed? Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = $current_user->ID;
	}
	
	// No user?
	if ( empty( $user_id ) ) {
		return false;
	}
	
	// Figure out if we are in a live or test gateway environment
	$environment = pmpro_getOption( 'gateway_environment' );
	
	// Query to check
	$sqlQuery = "SELECT COUNT(*) FROM $wpdb->pmpro_membership_orders WHERE user_id = '" . esc_sql( $user_id ) . "' AND gateway_environment = '" . esc_sql( $environment ) . "' AND total > 0 AND status NOT IN('error', 'refund', 'token', 'review') ";
	if ( ! empty( $level_id ) ) {
		$sqlQuery .= "AND membership_id = '" . esc_sql( $level_id ) . "' LIMIT 1";
	}
		
	// Get val
	$paid = $wpdb->get_var( $sqlQuery );
	
	// Force true/false
	return (bool) $paid;
}

/*
	Shortcode attributes using the my_hasPaid function.
	[haspaid]This will show up if the user has paid for any level.[/haspaid]
	[haspaid paid='0']This will show up if the user has NOT paid for any level.[/haspaid]
	[haspaid paid='1' level='1']This will show up if the user has paid for level 1 specifically.[/haspaid]
	[haspaid paid='0' level='1']This will show up if the user has not paid for level 1 specifically.[/haspaid]
*/
function my_haspaid_shortcode( $atts, $content = null, $code = "" ) {
	// $atts    ::= array of attributes
	// $content ::= text within enclosing form of shortcode element
	// $code    ::= the shortcode found, when == callback name
	// examples: [haspaid level="3"]...[/haspaid]
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
