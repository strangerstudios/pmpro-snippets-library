<?php
/**
 * Check to require level 1 by default.
 *
 * title: Check to require level 1 by default.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_default_new_posts_to_level1() {
	$screen = get_current_screen();
	if ( ! empty( $screen ) && $screen->action == 'add' && $screen->base == 'post' && in_array( $screen->post_type, array( 'post', 'page' ) ) ) {
		?>
	 <script>
		 //change the -1 there to -2 or -3 etc to check a different membership level
		 jQuery('#in-membership-level-1').prop('checked', true);
	 </script>
		<?php
	}
}
 add_action( 'admin_footer', 'my_pmpro_default_new_posts_to_level1' );
