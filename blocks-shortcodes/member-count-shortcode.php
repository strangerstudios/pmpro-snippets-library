<?php

/**
 * This recipe will create a [pmpro_member_count] shortcode, that will display the number of members in a specific level with a specific status.

 * title: Add a shortcode that counts members. Support passing different statuses
 * layout: snippet
 * collection: block-shortcodes
 * category: shortcodes
 * link: https://www.paidmembershipspro.com/display-count-members-level-andor-status-via-shortcode/
 *
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 /**
 *
 *  @param $attrs An array of attributes ( status, level, justnumber)
 */
function pmpro_member_count_shortcode( $attrs = null ) {
	global $wpdb;
	$attrs = shortcode_atts(
		array(
			'status' => 'active',
			'level' => null,
			'justnumber' => false,
		),
		$attrs
	);

	$statuses = array_map('trim', explode(',', $attrs['status']));

	//avoid pass null
	if (isset($attrs['level'])) {
		$levels = array_map('trim', explode(',', $attrs['level']));
	}
	if (! is_array($statuses) && ! empty($attrs['status']) ) {

		$statuses = array($attrs['status']);
	}

	$sql = "SELECT COUNT(*)
			FROM {$wpdb->pmpro_memberships_users}
			WHERE `status` IN ('" . implode("', '", $statuses) . "')";

	//Ensure isn't empty and values are int
	if (! empty( $levels ) && array_reduce($levels, function ($result, $item) { return $result && is_int(intval($item)); }, true )
	) {
		$sql .= "
			AND `membership_id` IN ('" . implode("', '", $levels) . "')";
	}

	$member_count = $wpdb->get_var($sql);
	//Bail if there's an error
	if ( is_wp_error($member_count)) {
		return sprintf( __( "Error while processing request: %s", "pmpromsc" ), esc_html ( $wpdb->print_error()) );
	}
	//If it's just number return it
	if( !empty($attrs['justnumber'] ) ) {
		return $member_count;
	} else {
		//
		if (! empty($levels ) ) {
			$level_names="";
			if (count($levels) === 1) {
				// If there's only one element, directly assign its name
				$level_names = pmpro_getLevel($levels[0])->name;
			} else {
				foreach ($levels as $level) {
					$level_names .= pmpro_getLevel($level)->name . ', ';
				}
			}
			return sprintf( __( "This site has %d %s members", "pmpromsc" ), esc_html( $member_count ) ,  esc_html ( $level_names ) ) ;
		} else {
			return sprintf( __( "This site has %d members", "pmpromsc" ), esc_html( $member_count ) );
		}
	}
}
add_shortcode('pmpro_member_count', 'pmpro_member_count_shortcode');