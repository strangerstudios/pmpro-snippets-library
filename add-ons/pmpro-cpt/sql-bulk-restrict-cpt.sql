/**
 * The recipe will restrict CPTs for specific levels
 * If you are using the Custom Post Type Membership Access Add On to restrict CPT access, 
 * this recipe will help you speed up the initial set up but 
 * replicating membership requirements across all CPTs of a selected post_type for a specific level.

 * title: Restricting Custom Post Types
 * layout: snippet
 * collection: restricting-content
 * category:  restricting-content, CPTs, bulk update, sql
 * link: https://www.paidmembershipspro.com/restrict-access-bulk-methods/
 * 
 * This is a SQL query and should not be added to your theme or site as PHP code.
 * Instead, you can run this query using a database management tool like phpMyAdmin 
 * or through your hosting control panel's database access (e.g. cPanel > phpMyAdmin).
 * Always make a full database backup before running SQL queries on your live site.
 */

/** Example SQL to add restriction on CPT 'gallery' for Level ID 3. 
    Change CPT name and ID for your needs.*/
INSERT IGNORE INTO wp_pmpro_memberships_pages (membership_id, page_id) 
	SELECT '3', ID FROM wp_posts WHERE post_type = 'gallery'; 