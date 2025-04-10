/**
 * The recipe below will replicate parent page membership requirements across all child pages in your site. 
 *
 * title: Copy page restrictions from parent pages to child pages.
 * layout: snippet
 * collection: restricting-content
 * category:  restricting-content, pages, bulk update, sql
 * link: https://www.paidmembershipspro.com/restrict-access-bulk-methods/
 * 
 * This is a SQL query and should not be added to your theme or site as PHP code.
 * Instead, you can run this query using a database management tool like phpMyAdmin 
 * or through your hosting control panel's database access (e.g. cPanel > phpMyAdmin).
 * Always make a full database backup before running SQL queries on your live site.
 */

 /** For this recipe to work, you must first add level requirements via 
 the Pages > Edit Page > “Require Membership” metabox to the appropriate parent pages. 
 
 Copy page restrictions from parent pages to child pages.
 NOTE that this doesn't delete any existing restrictions for the child pages. */
  
   INSERT IGNORE INTO wp_pmpro_memberships_pages (membership_id, page_id)
        SELECT mp.membership_id, p.ID
	    FROM wp_posts p
		    LEFT JOIN wp_pmpro_memberships_pages mp ON p.post_parent = mp.page_id
	    WHERE mp.membership_id IS NOT NULL 
		    AND p.post_type IN ( 'page' )
		    AND p.post_parent <> p.ID;