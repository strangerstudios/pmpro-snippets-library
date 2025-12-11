<?php
/**
 * Filter the LearnDash Course List to show courses the viewer can enroll in and 
 * courses they are already enrolled in.
 *
 * title: Filter the LearnDash Course List to Only Show a Member’s Enrolled Courses
 * layout: snippet
 * collection: integration-compatibility
 * category: learndash
 * link: https://www.paidmembershipspro.com/filter-the-learndash-course-list-to-only-show-a-members-enrolled-courses/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_learndash_course_list_visibility( $shortcode_atts ) {
	// Include free and open courses in course list for all visitors.
	$course_cpt_slug            = learndash_get_post_type_slug( 'course' );
	$free_courses               = learndash_get_posts_by_price_type( $course_cpt_slug, 'free' );
	$open_courses               = learndash_get_posts_by_price_type( $course_cpt_slug, 'open' );
	$pmpro_learndash_my_courses = array_merge( $free_courses, $open_courses );

	// Add enrolled courses to course list for logged in users.
	if ( is_user_logged_in() ) {
		global $current_user;
		$enrolled_courses           = learndash_user_get_enrolled_courses( $current_user->ID );
		$pmpro_learndash_my_courses = array_merge( $pmpro_learndash_my_courses, $enrolled_courses );
	} 

	// Update ld_course_list shortcode attributes.
	$shortcode_atts['post__in'] = $pmpro_learndash_my_courses;

	return $shortcode_atts;
}
add_filter( 'ld_course_list_shortcode_attr_defaults', 'my_learndash_course_list_visibility' );
