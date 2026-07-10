<?php
/**
 * This recipe will keep the member's original start date at checkout when switching levels.
 *
 * title: Keep member's start dates at checkout, even if switching levels.
 * layout: snippet
 * collection: checkout
 * category: checkout
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * If the member is not renewing, return the startdate from their active membership level.
 *
 * @param  string $startdate The current datetime formatted for MySQL.
 * @param  int    $user_id   The ID of the user checking out.
 * @param  object $level     The object of the level being checked out for.
 * @return string $startdate The member's start datetime.
 */
function my_pmpro_checkout_start_date( $startdate, $user_id, $level ) {
	global $wpdb;

	if ( ! pmpro_hasMembershipLevel( $level->id, $user_id ) ) {
		$sql_query = "SELECT UNIX_TIMESTAMP(startdate) FROM $wpdb->pmpro_memberships_users WHERE user_id = '" . esc_sql( $user_id ) . "' AND status = 'active' ORDER BY id LIMIT 1";
		$old_timestamp = $wpdb->get_var( $sql_query );

		if ( ! empty( $old_timestamp ) ) {
			$startdate = gmdate( 'Y-m-d H:i:s', $old_timestamp );
		}
	}

	return $startdate;
}
add_action( 'pmpro_checkout_start_date', 'my_pmpro_checkout_start_date', 15, 3 );
