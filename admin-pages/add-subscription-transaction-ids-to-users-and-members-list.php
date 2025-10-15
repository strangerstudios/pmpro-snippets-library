<?php
/*
	Payment/Subscription Transaction IDs Code
	
	Add to your active theme's functions.php or a custom plugin.
*/
//Add Transaction IDs to Members List
function tids_pmpro_memberslist_extra_cols_header($theusers)
{
?>
<th><?php _e('Transaction IDs', 'pmpro');?></th>
<?php
}
add_action('pmpro_memberslist_extra_cols_header', 'tids_pmpro_memberslist_extra_cols_header');

function tids_pmpro_memberslist_extra_cols_body($theuser)
{
	$order = new MemberOrder();
	$order->getLastMemberOrder($theuser->ID, "");	
?>
<td>
	<?php _e('Payment', 'pmpro');?>: <?php if(!empty($order->payment_transaction_id)) echo '<a href="' . admin_url('admin.php?page=pmpro-orders&order=' . $order->id) . '">' . $order->payment_transaction_id . '</a>'; else echo "N/A";?>
	<br />
	<?php _e('Subscription', 'pmpro');?>: <?php if(!empty($order->subscription_transaction_id)) echo '<a href="' . admin_url('admin.php?page=pmpro-orders&order=' . $order->id) . '">' .$order->subscription_transaction_id . '</a>'; else echo "N/A";?>	
</td>
<?php
}
add_action('pmpro_memberslist_extra_cols_body', 'tids_pmpro_memberslist_extra_cols_body');

//add Transaction IDs to members list CSV
function tids_pmpro_members_list_csv_extra_columns($columns)
{
	$columns["subscription_transaction_id"] = "tids_csv_columns_get_order_property";
	$columns["payment_transaction_id"] = "tids_csv_columns_get_order_property";
	
	return $columns;
}
add_filter('pmpro_members_list_csv_extra_columns', 'tids_pmpro_members_list_csv_extra_columns');

function tids_csv_columns_get_order_property($theuser, $heading)
{
	global $tids_csv_columns_orders;
	
	if(!isset($tids_csv_columns_orders))
		$tids_csv_columns_orders = array();
	
	if(!isset($tids_csv_columns_orders[$theuser->ID]))
	{
		$tids_csv_columns_orders[$theuser->ID] = new MemberOrder;
		$tids_csv_columns_orders[$theuser->ID]->getLastMemberOrder($theuser->ID);
	}
	
	if(!empty($tids_csv_columns_orders[$theuser->ID]))
		return $tids_csv_columns_orders[$theuser->ID]->$heading;
	else
		return "";
}

//Search Sub IDs on Members List Page
function tids_pmpro_members_list_sql($sqlQuery)
{
	global $wpdb;
	
	$s = $_REQUEST['s'];	
	
	if(!empty($s) && strlen($s) > 8)
	{
		//look for orders with this sub id
		$user_ids = $wpdb->get_col("SELECT user_id FROM $wpdb->pmpro_membership_orders WHERE subscription_transaction_id lIKE '%" . esc_sql($s) . "%' OR payment_transaction_id LIKE '%" . esc_sql($s) . "%' GROUP BY user_id");
		
		if(!empty($user_ids))
		{			
			//some vars for the search
			$l = $_REQUEST['l'];
			
			if(isset($_REQUEST['pn']))
				$pn = $_REQUEST['pn'];
			else
				$pn = 1;
				
			if(isset($_REQUEST['limit']))
				$limit = $_REQUEST['limit'];
			else
				$limit = 15;
			
			$end = $pn * $limit;
			$start = $end - $limit;	
			
			//filter results to only include these user ids
			$sqlQuery = "SELECT SQL_CALC_FOUND_ROWS u.ID, u.user_login, u.user_email, UNIX_TIMESTAMP(u.user_registered) as joindate, mu.membership_id, mu.initial_payment, mu.billing_amount, mu.cycle_period, mu.cycle_number, mu.billing_limit, mu.trial_amount, mu.trial_limit, UNIX_TIMESTAMP(mu.startdate) as startdate, UNIX_TIMESTAMP(mu.enddate) as enddate, m.name as membership FROM $wpdb->users u LEFT JOIN $wpdb->usermeta um ON u.ID = um.user_id LEFT JOIN $wpdb->pmpro_memberships_users mu ON u.ID = mu.user_id LEFT JOIN $wpdb->pmpro_membership_levels m ON mu.membership_id = m.id ";
			
			if($l == "oldmembers")
				$sqlQuery .= " LEFT JOIN $wpdb->pmpro_memberships_users mu2 ON u.ID = mu2.user_id AND mu2.status = 'active' ";
			
			//this is the line changed
			$sqlQuery .= " WHERE u.ID IN(" . implode(",", $user_ids) . ") ";				
			//---
		
			if($l == "oldmembers")
				$sqlQuery .= " AND mu.status = 'inactive' AND mu2.status IS NULL ";
			elseif($l)
				$sqlQuery .= " AND mu.status = 'active' AND mu.membership_id = '" . $l . "' ";					
			else
				$sqlQuery .= " AND mu.status = 'active' ";			
			
			$sqlQuery .= "GROUP BY u.ID ";
			
			if($l == "oldmembers")
				$sqlQuery .= "ORDER BY enddate DESC ";
			else
				$sqlQuery .= "ORDER BY u.user_registered DESC ";
			
			$sqlQuery .= "LIMIT $start, $limit";			
		}
	}
	
	return $sqlQuery;
}
add_filter('pmpro_members_list_sql', 'tids_pmpro_members_list_sql');

//add transaction ids to WP users list
function tids_manage_users_columns($columns) {
    $columns['tids_transaction_ids'] = __('Transaction IDs', 'pmpro');
    return $columns;
}
function tids_manage_users_custom_column($column_data, $column_name, $user_id) {
    //make sure PMPro is installed
	if(!class_exists("MemberOrder"))
		return $column_data;
	
	if($column_name == 'tids_transaction_ids') {
        $order = new MemberOrder();
		$order->getLastMemberOrder($user_id, "");	
        
		$column_data = __('Pay', 'pmpro') . ": ";
		if(!empty($order) && !empty($order->payment_transaction_id))
			$column_data .= '<a href="' . admin_url('admin.php?page=pmpro-orders&order=' . $order->id) . '">' . $order->payment_transaction_id . '</a>';
		else
			$column_data .= "N/A";		
		$column_data .= "<br />" . __('Sub', 'pmpro') . ": ";
		if(!empty($order) && !empty($order->subscription_transaction_id))
			$column_data .= '<a href="' . admin_url('admin.php?page=pmpro-orders&order=' . $order->id) . '">' . $order->subscription_transaction_id . '</a>';
		else
			$column_data .= "N/A";
    }
    return $column_data;
}
add_filter('manage_users_columns', 'tids_manage_users_columns');
add_filter('manage_users_custom_column', 'tids_manage_users_custom_column', 10, 3);

//add some fields to search in users list
function tids_pre_user_query( $user_query )
{
	// Make sure this is only applied to user search
	if ( $user_query->query_vars['search'] ){
		$search = trim( $user_query->query_vars['search'], '*' );
		if ( $_REQUEST['s'] == $search ){
			global $wpdb;
 
			//look for transaction ids
			if(strlen($search) > 8)
			{
				$user_ids = $wpdb->get_col("SELECT user_id FROM $wpdb->pmpro_membership_orders WHERE subscription_transaction_id lIKE '%" . esc_sql($search) . "%' OR payment_transaction_id LIKE '%" . esc_sql($search) . "%' GROUP BY user_id");
			}
 
			if(!empty($user_ids))
			{
				$user_query->query_where = "WHERE 1=1 AND ID IN(" . implode(",", $user_ids) . ") ";
			}
			else
			{ 
				$user_query->query_from .= " LEFT JOIN $wpdb->usermeta MF ON MF.user_id = {$wpdb->users}.ID AND MF.meta_key = 'first_name'";
				$user_query->query_from .= " LEFT JOIN $wpdb->usermeta ML ON ML.user_id = {$wpdb->users}.ID AND ML.meta_key = 'last_name'";
				$user_query->query_from .= " LEFT JOIN $wpdb->usermeta AE ON AE.user_id = {$wpdb->users}.ID AND AE.meta_key = 'AdditionalEmail'";
	 
				$user_query->query_where = 'WHERE 1=1' . $user_query->get_search_sql( $search, array( 'user_login', 'user_email', 'user_nicename', 'MF.meta_value', 'ML.meta_value', 'AE.meta_value' ), 'both' );
			}			
		}
	}
}
add_action( 'pre_user_query', 'tids_pre_user_query' );

//add transaction ids to edit user page
function tids_profile_fields($user)
{
	$order = new MemberOrder();
	$order->getLastMemberOrder($user->ID, "");				
	?>
	<h3><?php _e('Transaction IDs', 'pmpro');?></h3>
	<p>
		<?php _e('Payment', 'pmpro');?>: <?php if(!empty($order->payment_transaction_id)) echo '<a href="' . admin_url('admin.php?page=pmpro-orders&order=' . $order->id) . '">' . $order->payment_transaction_id . '</a>'; else echo "N/A";?>
		<br />
		<?php _e('Subscription', 'pmpro');?>: <?php if(!empty($order->subscription_transaction_id)) echo '<a href="' . admin_url('admin.php?page=pmpro-orders&order=' . $order->id) . '">' . $order->subscription_transaction_id . '</a>'; else echo "N/A";?>	
	</p>
	<?php
}
add_action( 'show_user_profile', 'tids_profile_fields' );
add_action( 'edit_user_profile', 'tids_profile_fields' );
?>
