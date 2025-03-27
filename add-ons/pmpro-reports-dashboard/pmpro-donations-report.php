<?php
/**
 * PMPro Donations Reporting
 *
 * This snippet adds donation reporting functionality to Paid Memberships Pro.
 *  
 * title: Add donations reporting to PMPro for Admins and Member Managers
 * layout: snippet
 * collection: Admin-Pages
 * category: add-ons/pmpro-reports-dashboard
 *  
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 *
 * Features:
 * - Adds a widget to the PMPro Reports dashboard
 * - Displays total donations with filtering by month and year
 * - Lists individual donation transactions with member details
 * - Provides CSV export functionality
 * 
 * The report is accessible to Administrators and Membership Managers (if using the 
 * Membership Manager Role Add On). To make this accessible only to Administrators,
 * remove the pmpro_memberships_menu capability check.
 * 
 * Created by Graham Godfrey, gp54g@mac.com
 */

// Function to fetch total donation data
function MY_pmpro_get_total_donations($month = null, $year = null)
{
    global $wpdb;
    $query = "
        SELECT SUM(meta_value) 
        FROM {$wpdb->prefix}pmpro_membership_ordermeta 
        JOIN {$wpdb->prefix}pmpro_membership_orders 
        ON {$wpdb->prefix}pmpro_membership_ordermeta.pmpro_membership_order_id = {$wpdb->prefix}pmpro_membership_orders.id
        WHERE meta_key = 'donation_amount' AND meta_value > 0
    ";

    if ($month || $year) {
        if ($month) {
            $query .= $wpdb->prepare(" AND MONTH(timestamp) = %d", $month);
        }
        if ($year) {
            $query .= $wpdb->prepare(" AND YEAR(timestamp) = %d", $year);
        }
    }

    $total_donations = $wpdb->get_var($query);
    return $total_donations ? $total_donations : 0;
}

/**
 * Fetch all donations for the selected period including member details
 * 
 * Retrieves donation data along with member information for better reporting.
 * 
 * @param int|null $month Month number (1-12) or null for all months
 * @param int|null $year Year (e.g., 2025) or null for all years
 * @return array Array of donation objects with member details
 */
function MY_pmpro_get_donations($month = null, $year = null)
{
    global $wpdb;
    $query = "
        SELECT om.meta_value, o.timestamp, o.user_id, u.user_login, 
               um1.meta_value as first_name, um2.meta_value as last_name
        FROM {$wpdb->prefix}pmpro_membership_ordermeta om
        JOIN {$wpdb->prefix}pmpro_membership_orders o
            ON om.pmpro_membership_order_id = o.id
        LEFT JOIN {$wpdb->users} u 
            ON o.user_id = u.ID
        LEFT JOIN {$wpdb->usermeta} um1
            ON u.ID = um1.user_id AND um1.meta_key = 'first_name'
        LEFT JOIN {$wpdb->usermeta} um2
            ON u.ID = um2.user_id AND um2.meta_key = 'last_name'
        WHERE om.meta_key = 'donation_amount' AND om.meta_value > 0
    ";

    if ($month || $year) {
        if ($month) {
            $query .= $wpdb->prepare(" AND MONTH(o.timestamp) = %d", $month);
        }
        if ($year) {
            $query .= $wpdb->prepare(" AND YEAR(o.timestamp) = %d", $year);
        }
    }

    $query .= " ORDER BY o.timestamp DESC";

    return $wpdb->get_results($query);
}

// Add a Custom Report to the Memberships > Reports Screen in Paid Memberships Pro.
global $pmpro_reports;
$pmpro_reports['my_donations'] = __('Donations Received', 'pmpro');

/**
 * Handle CSV export of donation data
 * 
 * Creates a downloadable CSV file with donation information when the export button is clicked.
 */
function MY_pmpro_donations_csv_export()
{
    if (!isset($_GET['report']) || $_GET['report'] != 'my_donations' || !isset($_GET['export']) || $_GET['export'] != 'csv') {
        return;
    }

    // Check for proper user permissions (administrators or membership managers). 
    // If not using Member Manager Role Add On remove "&& !current_user_can('pmpro_memberships_menu')" from the if statement.
    if (!current_user_can('manage_options') && !current_user_can('pmpro_memberships_menu')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'pmpro'));
    }

    $month = isset($_GET['month']) ? intval($_GET['month']) : null;
    $year = isset($_GET['year']) ? intval($_GET['year']) : null;

    // Get donations data
    $donations = MY_pmpro_get_donations($month, $year);

    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="pmpro-donations-' . date('Y-m-d') . '.csv"');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add CSV headers
    fputcsv($output, array(
        __('Member ID', 'pmpro'),
        __('Username', 'pmpro'),
        __('First Name', 'pmpro'),
        __('Last Name', 'pmpro'),
        __('Amount', 'pmpro'),
        __('Date', 'pmpro')
    ));

    // Add data rows
    foreach ($donations as $donation) {
        fputcsv($output, array(
            $donation->user_id,
            $donation->user_login,
            $donation->first_name,
            $donation->last_name,
            $donation->meta_value,
            $donation->timestamp
        ));
    }

    fclose($output);
    exit();
}
add_action('admin_init', 'MY_pmpro_donations_csv_export');

/**
 * Create Donations Report widget for the PMPro Reports dashboard
 * 
 * Adds a widget to the main Reports dashboard that links to the detailed donations report.
 */
function MY_pmpro_report_donations_widget()
{
?>
    <span id="pmpro_report_donations" class="pmpro_report-holder">
        <h2><?php _e('Donations Received', 'pmpro'); ?></h2>
        <p><?php _e('View the total donations received and individual donation entries.', 'pmpro'); ?></p>
        <p class="pmpro_report-button">
            <a class="button button-primary" href="<?php echo admin_url('admin.php?page=pmpro-reports&report=my_donations'); ?>"><?php _e('Details', 'pmpro'); ?></a>
        </p>
    </span>
<?php
}

// This is the function PMPro is looking for based on the report ID
function pmpro_report_my_donations_widget()
{
    MY_pmpro_report_donations_widget();
}

add_action('pmpro_reports_widget', 'MY_pmpro_report_donations_widget');

/**
 * Donations Report page content
 * 
 * Generates the main report page with filtering options, total amount display,
 * export functionality, and a detailed list of all donations.
 */
function MY_pmpro_report_donations_page()
{
    // Check for proper user permissions (administrators or membership managers).  
    // If not using Member Manager Role Add On remove "&& !current_user_can('pmpro_memberships_menu')" from the if statement.
    if (!current_user_can('manage_options') && !current_user_can('pmpro_memberships_menu')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'pmpro'));
    }

    $month = isset($_GET['month']) ? intval($_GET['month']) : null;
    $year = isset($_GET['year']) ? intval($_GET['year']) : null;
    $total_donations = MY_pmpro_get_total_donations($month, $year);
    $donations = MY_pmpro_get_donations($month, $year);

    // Get current year
    $current_year = date('Y');
?>
    <h2><?php _e('Total Donations Received', 'pmpro'); ?></h2>
    <form method="get" action="">
        <input type="hidden" name="page" value="pmpro-reports" />
        <input type="hidden" name="report" value="my_donations" />
        <label for="month"><?php _e('Month:', 'pmpro'); ?></label>
        <select name="month" id="month">
            <option value=""><?php _e('All', 'pmpro'); ?></option>
            <?php for ($m = 1; $m <= 12; $m++) { ?>
                <option value="<?php echo $m; ?>" <?php selected($month, $m); ?>><?php echo date_i18n('F', mktime(0, 0, 0, $m, 10)); ?></option>
            <?php } ?>
        </select>
        <label for="year"><?php _e('Year:', 'pmpro'); ?></label>
        <select name="year" id="year">
            <option value=""><?php _e('All', 'pmpro'); ?></option>
            <?php
            // Show years from current year down to 2022 or earlier if needed
            for ($y = $current_year; $y >= 2022; $y--) {
            ?>
                <option value="<?php echo $y; ?>" <?php selected($year, $y); ?>><?php echo $y; ?></option>
            <?php } ?>
        </select>
        <input type="submit" value="<?php _e('Filter', 'pmpro'); ?>" class="button" />
    </form>

    <p>
        <?php
        echo sprintf(
            __('Total Donations: %s', 'pmpro'),
            '<strong>$' . number_format_i18n($total_donations, 2) . '</strong>'
        );
        ?>
    </p>

    <!-- Add Export Button -->
    <p>
        <a href="<?php echo add_query_arg(array('export' => 'csv', 'month' => $month, 'year' => $year)); ?>" class="button">
            <?php _e('Export to CSV', 'pmpro'); ?>
        </a>
    </p>

    <h3><?php _e('Donation Entries (Descending Order):', 'pmpro'); ?></h3>
    <table class="widefat striped">
        <thead>
            <tr>
                <th><?php _e('Member ID', 'pmpro'); ?></th>
                <th><?php _e('Username', 'pmpro'); ?></th>
                <th><?php _e('Name', 'pmpro'); ?></th>
                <th><?php _e('Amount', 'pmpro'); ?></th>
                <th><?php _e('Date', 'pmpro'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($donations)) {
                foreach ($donations as $donation) {
                    $name = trim($donation->first_name . ' ' . $donation->last_name);
                    if (empty($name)) {
                        $name = __('(not set)', 'pmpro');
                    }
            ?>
                    <tr>
                        <td><?php echo intval($donation->user_id); ?></td>
                        <td><?php echo esc_html($donation->user_login); ?></td>
                        <td><?php echo esc_html($name); ?></td>
                        <td><?php echo '$' . number_format_i18n($donation->meta_value, 2); ?></td>
                        <td><?php echo date_i18n(get_option('date_format'), strtotime($donation->timestamp)); ?></td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="5"><?php _e('No donations found for the selected period.', 'pmpro'); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php
}

/**
 * This function is needed for compatibility with PMPro's function naming convention
 * PMPro will look for a function with this exact name based on the report ID
 */
function pmpro_report_my_donations_page()
{
    MY_pmpro_report_donations_page();
}

/**
 * Register the new report with PMPro
 * 
 * Adds our custom donations report to the list of available PMPro reports.
 * 
 * @param array $reports Existing PMPro reports
 * @return array Modified array with our custom report
 */
function MY_pmpro_register_donations_report($reports)
{
    $reports['my_donations'] = __('Donations Received', 'pmpro');
    return $reports;
}
add_filter('pmpro_reports', 'MY_pmpro_register_donations_report');
