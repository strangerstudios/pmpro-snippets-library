/**
 * Database Script to Remove End Dates From All Members
 *
 * title: Database Script to Remove End Dates From All Members
 * layout: snippet
 * collection: misc
 * category:  membership-levels, bulk update, sql
 * link: https://www.paidmembershipspro.com/membership-level-recurring-billing-and-expiration-date/
 * 
 * This is a SQL query and should not be added to your theme or site as PHP code.
 * Instead, you can run this query using a database management tool like phpMyAdmin 
 * or through your hosting control panel's database access (e.g. cPanel > phpMyAdmin).
 * Always make a full database backup before running SQL queries on your live site.
 */

/**
 * Note: Your database table prefix may be something other than wp_.
 * Update the query below to match your site's table prefix.
 */
UPDATE wp_pmpro_memberships_users SET enddate = NULL WHERE status = 'active';

/**
 * If you have multiple membership levels and only want to update specific levels,
 * use a query like the one below instead.
 *
 * Ignore the query above, replace membership_id with the appropriate level ID,
 * and ensure the table prefix matches your site's database prefix.
 */
UPDATE wp_pmpro_memberships_users SET enddate = NULL WHERE status = 'active' AND membership_id = 1;
