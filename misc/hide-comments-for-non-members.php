/**
 * This recipe hides comments for non-members 
 *
 * title: Hide comments for non-members.
 * layout: snippet
 * collection: misc
 * category: comments
 * author: Theuns Coetzee (@ipokkel) 
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 
 // hook into comments_array to filter comments
 function pmpro_hide_comments_for_non_members( $comments ) {
   
 	// Is PMPro active and does the user have an active membership level?
 	if ( function_exists( 'pmpro_hasMembershipLevel' ) && pmpro_hasMembershipLevel() ) {
 		// Show the comments.
 		return $comments;
 	} else {
 		// The non-member message would only show on posts where the comment form shows.
 		add_action( 'comment_form_before', 'pmpro_non_member_text_before_comment_form' );
 		// Remove the comments for non-members.
 		return array();
 	}
 	return $comments;
 }
 add_filter( 'comments_array', 'pmpro_hide_comments_for_non_members' );

 function pmpro_non_member_text_before_comment_form() {
 	$pmpro_comments_non_member_text = pmpro_get_no_access_message( null, null, null );
 	$pmpro_comments_non_member_text = str_replace( __( 'This content is', 'pmpro-comments' ), __( 'Comments are', 'pmpro-comments' ), $pmpro_comments_non_member_text );

 	echo wp_kses_post( $pmpro_comments_non_member_text );
 }