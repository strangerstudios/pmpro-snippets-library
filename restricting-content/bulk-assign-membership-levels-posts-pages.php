<?php
/**
 * Bulk assign membership levels to posts and pages.
 *
 * title: Bulk assign membership levels to posts and pages.
 * layout: snippet
 * collection: restricting-content
 * category:  restricting-content, bulk update
 * link: https://www.paidmembershipspro.com/restrict-access-bulk-methods/
 * 
 * You will need to adjust the $levels to your desired levels as well as 
 *  select your desired $post_type.
 *  Onced added to your site add the following to the end of yout URL 
 *  in order ro run the script `/wp-admin/?assign_levels_posts=true`
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function mypmpro_bulk_assign_levels_to_posts(){

	if( isset( $_REQUEST['assign_levels_posts'] ) ){

		global $wpdb;

		$levels = array( 1, 2 ); //Assign these levels to each post. Will override exsising restrictions.

		$post_type = 'post'; //Change to your preference

		$args = array(
			'post_type' => $post_type,
			'posts_per_page' => -1
		);

		$the_query = new WP_Query( $args );

		if( $the_query->have_posts() ){
			while( $the_query->have_posts() ){
				$the_query->the_post();

				$post_id = get_the_ID();

				//remove all memberships for this page
				$wpdb->query("DELETE FROM {$wpdb->pmpro_memberships_pages} WHERE page_id = '$post_id'");

				//add new memberships for this page
				if(is_array($levels))
				{
					foreach($levels as $level){
						$sql = "INSERT INTO {$wpdb->pmpro_memberships_pages} (membership_id, page_id) VALUES('" . intval($level) . "', '" . intval($post_id) . "')";
						echo $sql."<br/>";
						$wpdb->query($sql);
					}
				}

			}
		} else {
			echo "Nothing Found";
		}
		exit();

	}

}
add_action( 'admin_init', 'mypmpro_bulk_assign_levels_to_posts' );