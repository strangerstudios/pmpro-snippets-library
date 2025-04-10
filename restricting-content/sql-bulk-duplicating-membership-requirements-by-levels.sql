/**
 * Duplicating Membership Requirements Across Levels
 *
 * title: Duplicating Membership Requirements Across Levels
 * layout: snippet
 * collection: restricting-content
 * category:  restricting-content, bulk update, sql
 * link: https://www.paidmembershipspro.com/restrict-access-bulk-methods/
 * 
 * This is a SQL query and should not be added to your theme or site as PHP code.
 * Instead, you can run this query using a database management tool like phpMyAdmin 
 * or through your hosting control panel's database access (e.g. cPanel > phpMyAdmin).
 * Always make a full database backup before running SQL queries on your live site.
 */

/** Example SQL to duplicate restrictions for level ID 1 to also restrict for level ID 2 */
INSERT IGNORE INTO wp_pmpro_memberships_pages (membership_id, page_id)
	SELECT '2', page_id FROM wp_pmpro_memberships_pages WHERE membership_id = 1;