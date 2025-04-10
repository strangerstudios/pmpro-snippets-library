/**
 * Example SQL to add restriction to all 'posts' for Level ID 2. Change level ID for your needs.
 *
 * title: Add restriction to all 'posts' for Level ID 2. 
 * layout: snippet
 * collection: restricting-content
 * category:  restricting-content, bulk update, sql
 * link: https://www.paidmembershipspro.com/restrict-access-bulk-methods/
 * 
 * Change level ID for your needs.
 *
 * This is a SQL query and should not be added to your theme or site as PHP code.
 * Instead, you can run this query using a database management tool like phpMyAdmin 
 * or through your hosting control panel's database access (e.g. cPanel > phpMyAdmin).
 * Always make a full database backup before running SQL queries on your live site.
 */

/* Example SQL to add restriction to all 'posts' for Level ID 2. Change level ID for your needs. */

INSERT IGNORE INTO wp_pmpro_memberships_pages (membership_id, page_id) 
SELECT 2, ID FROM wp_posts WHERE post_type = 'post';