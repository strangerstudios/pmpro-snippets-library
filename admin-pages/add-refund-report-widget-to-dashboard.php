<?php
//Add the new "Refund Rate" report widget and page
global $pmpro_reports;
$pmpro_reports['refunds'] = __('Refund Rate', 'pmpro-reports-refunds');

global $wpdb;

//$refundsThisMonth = "SELECT COUNT(*) FROM wp_pmpro_membership_orders WHERE membership_id IN(6,20) AND total > 0 AND timestamp > '2016-01-01' AND status = 'refunded' ";

//$refundRate = $refunds / $sales;

//Show the report widget on the Memberships > Reports dashboard
function pmpro_report_refunds_widget()
{	
	global $wpdb;
	?>
	<table class="wp-list-table widefat fixed striped">
	<thead>
		<tr>
			<th scope="col">&nbsp;</th>
			<th scope="col"><?php _e('Sales', 'pmpro'); ?></th>
			<th scope="col"><?php _e('Refunds', 'pmpro'); ?></th>
			<th scope="col"><?php _e('Refund Rate', 'pmpro'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th><?php _e('This Month', 'pmpro'); ?></th>
			<td><?php echo number_format_i18n(pmpro_getSales("this month")); ?></td>
			<td><?php echo number_format_i18n(pmpro_getRefunds("this month")); ?></td>
			<td>
			<?php 
				if(pmpro_getSales('this month') > 0)
					echo sprintf("%.2f%%", (pmpro_getRefunds("this month") / pmpro_getSales("this month")) * 100); 
				else
					echo __('N/A', 'pmpro');
			?>
			</td>
		</tr>
		<tr>
			<th><?php _e('This Year', 'pmpro'); ?></th>
			<td><?php echo number_format_i18n(pmpro_getSales("this year")); ?></td>
			<td><?php echo number_format_i18n(pmpro_getRefunds("this year")); ?></td>
			<td>
			<?php 
				if(pmpro_getSales('this year') > 0)
					echo sprintf("%.2f%%", (pmpro_getRefunds("this year") / pmpro_getSales("this year")) * 100);
				else
					echo __('N/A', 'pmpro');
			?>	
			</td>
		</tr>
			<th><?php _e('All Time', 'pmpro'); ?></th>
			<td><?php echo number_format_i18n(pmpro_getSales("all time")); ?></td>
			<td><?php echo number_format_i18n(pmpro_getRefunds("all time")); ?></td>
			<td>
			<?php 
				if(pmpro_getSales('all time') > 0)
					echo sprintf("%.2f%%", (pmpro_getRefunds("all time") / pmpro_getSales("all time")) * 100);
				else
					echo __('N/A', 'pmpro');
			?>
			</td>
		</tr>
	</tbody>
	</table>
	<?php
}

//Show the report on the Memberships > Reports > Refund Rate page
function pmpro_report_refunds_page()
{
	global $wpdb;
	?>
	<div class="metabox-holder">
		<h1><?php _e('Refund Rate', 'pmpro'); ?></h1>
		<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th scope="col">&nbsp;</th>
				<th scope="col"><?php _e('Sales', 'pmpro'); ?></th>
				<th scope="col"><?php _e('Refunds', 'pmpro'); ?></th>
				<th scope="col"><?php _e('Refund Rate', 'pmpro'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th><?php _e('This Month', 'pmpro'); ?></th>
				<td><?php echo number_format_i18n(pmpro_getSales("this month")); ?></td>
				<td><?php echo number_format_i18n(pmpro_getRefunds("this month")); ?></td>
				<td><?php echo sprintf("%.2f%%", (pmpro_getRefunds("this month") / pmpro_getSales("this month")) * 100); ?></td>
			</tr>
			<tr>
				<th><?php _e('This Year', 'pmpro'); ?></th>
				<td><?php echo number_format_i18n(pmpro_getSales("this year")); ?></td>
				<td><?php echo number_format_i18n(pmpro_getRefunds("this year")); ?></td>
				<td><?php echo sprintf("%.2f%%", (pmpro_getRefunds("this year") / pmpro_getSales("this year")) * 100); ?></td>
			</tr>
				<th><?php _e('All Time', 'pmpro'); ?></th>
				<td><?php echo number_format_i18n(pmpro_getSales("all time")); ?></td>
				<td><?php echo number_format_i18n(pmpro_getRefunds("all time")); ?></td>
				<td><?php echo sprintf("%.2f%%", (pmpro_getRefunds("all time") / pmpro_getSales("all time")) * 100); ?></td>
			</tr>
		</tbody>
		</table>	
	</div>
	<?php
}

//get refunds
if(!function_exists('pmpro_getRefunds')) {
	function pmpro_getRefunds($period, $levels = NULL)
	{
		//check for a transient
		$cache = get_transient("pmpro_report_refunds");
		if(!empty($cache) && !empty($cache[$period]) && !empty($cache[$period][$levels]))
			return $cache[$period][$levels];
			
		//a refund is an order with status = 'refunded' with a total > 0
		if($period == "today")
			$startdate = date("Y-m-d", current_time('timestamp'));
		elseif($period == "this month")
			$startdate = date("Y-m", current_time('timestamp')) . "-01";
		elseif($period == "this year")
			$startdate = date("Y", current_time('timestamp')) . "-01-01";
		else
			$startdate = "";
		
		$gateway_environment = pmpro_getOption("gateway_environment");
		
		//build query
		global $wpdb;
		$sqlQuery = "SELECT COUNT(*) FROM $wpdb->pmpro_membership_orders WHERE total > 0 AND status = 'refunded' AND timestamp >= '" . $startdate . "' AND gateway_environment = '" . esc_sql($gateway_environment) . "' ";
		
		//restrict by level
		if(!empty($levels))
			$sqlQuery .= "AND membership_id IN(" . $levels . ") ";
		
		$refunds = $wpdb->get_var($sqlQuery);
		
		//save in cache
		if(!empty($cache) && !empty($cache[$period]))
			$cache[$period][$levels] = $refunds;
		elseif(!empty($cache))
			$cache[$period] = array($levels => $refunds);
		else
			$cache = array($period => array($levels => $refunds));
		
		set_transient("pmpro_report_refunds", $cache, 3600*24);
		
		return $refunds;
	}
}

//delete transients when an order is updated
function pmpro_report_refunds_delete_transient()
{
	delete_transient("pmpro_report_refunds");
}
add_action("pmpro_updated_order", "pmpro_report_refunds_delete_transient");
