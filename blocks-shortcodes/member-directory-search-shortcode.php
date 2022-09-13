<?php
/**
 * This recipe will create the [pmpro_member_search] shortcode, allowing to you to search
 * your member directory.

 * title: Create search member directory shortcode
 * layout: snippet
 * collection: block-shortcodes
 * category: member-directory, search
 *
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function mypmpro_member_search() {

	$url = 'https://mysite.com/member-directory'; // Change this line to the member directory URL

	$ret = '';

	if ( isset( $_REQUEST['limit'] ) ) {
		$limit = intval( $_REQUEST['limit'] );
	} elseif ( empty( $limit ) ) {
		$limit = 15;
	}

	if ( ! empty( $_REQUEST['ps'] ) ) {
		$ps = stripslashes( esc_attr( $_REQUEST['ps'] ) );
	} else {
		$ps = '';
	}

	$ret      .= '<form role="search" method="post" class="pmpro_member_directory_search search-form" action="' . $url . '">';
	 $ret     .= '<label>';
		 $ret .= '<span class="screen-reader-text">' . __( 'Search for:', 'pmpromd' ) . '</span>';
		 $ret .= '<input type="search" class="search-field" placeholder="' . __( 'Search Members', 'pmpromd' ) . '" name="ps" value="' . $ps . '" title="' . __( 'Search Members', 'pmpromd' ) . '" />';
		 $ret .= '<input type="hidden" name="limit" value="' . esc_attr( $limit ) . '" />';
	 $ret     .= '</label>';
	 $ret     .= '<input type="submit" class="search-submit" value="' . __( 'Search Members', 'pmpromd' ) . '">';
	$ret      .= '</form>';

	return $ret;

}
add_shortcode( 'pmpro_member_search', 'mypmpro_member_search' );
