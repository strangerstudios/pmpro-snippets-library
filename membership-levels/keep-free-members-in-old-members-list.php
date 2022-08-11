<?php
/**
 * When PMPro users cancel or expire, they are shown on the
 * cancelled, expired, and "old" members lists.
 * However, if an old user is given a default/free level later,
 * then they are not considered old, cancelled, or expired anymore.
 *
 * You may still want to see those users on the old users lists
 * so you can follow up to get them back into paid accounts.
 * This code does that.
  *
 * title: Cancelled or expired memmbers show on cancelled, expired and "old" membership list
 * layout: snippet
 * collection: membership-levels
 * category: membership list
 *
 * IMPORTANT: Change the $free_level_ids value below. Use a comma-separated list of ids.
* After activating this code, members who previously had a paid level,
* but now have a free level, will still be included in the old members lists.
* Note also that free users who cancelled or expired are also still included.
*
* Install this as a Code Snippet or into a custom plugin.
* https://www.paidmembershipspro.com/how-to-add-code-to-wordpress/
*/
function my_keep_free_members_in_old_members_list( $sql ) {
 $free_level_ids = '1';	// comma-separated list

 if ( $_REQUEST['l'] == 'expired' || $_REQUEST['l'] == 'cancelled' || $_REQUEST['l'] == 'old' ) {
	 $sql = str_replace( 'mu2.status IS NULL', '(mu2.status IS NULL OR mu2.membership_id IN(' . $free_level_ids . '))', $sql );
 }

 return $sql;
}
add_filter( 'pmpro_members_list_sql', 'my_keep_free_members_in_old_members_list' );
