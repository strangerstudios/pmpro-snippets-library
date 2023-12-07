<?php
/**
 * This recipe removes the list of Lessons from the pmpro_course single CPT frontend content.
 *
 * title: Remove lesson list from single course.
 * layout: snippet
 * collection: add-ons
 * category: pmpro-courses
 * link: https://www.paidmembershipspro.com/remove-lesson-list/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Remove lesson list from single course.
 */
function my_pmpro_courses_remove_lessons_on_course_single() {
	// Return early if the Courses Add On is not active. 
	if ( ! defined( 'PMPRO_COURSES_VERSION' ) ) {
		return;
	}

	// Return if not on a single pmpro_course.
	if ( get_post_type( get_queried_object_id() ) != 'pmpro_course' ) {
		return;
	}

	// Remove the filter to add lesson list to single course.
	remove_filter( 'the_content', 'pmpro_courses_add_lessons_to_course', 10 );
}
add_action( 'wp', 'my_pmpro_courses_remove_lessons_on_course_single' );
