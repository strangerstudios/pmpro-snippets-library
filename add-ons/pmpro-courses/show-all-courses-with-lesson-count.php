<?php
/**
 * This recipe builds a new shortcode for all pmpro courses list and shows the lesson count for each course in a table layout
 * Use the shortcode [pmpro_courses_with_lessons] to display your course with lessons on your site
 *
 * title: show all courses with lesson count
 * layout: snippet
 * collection: add-ons
 * category: pmpro-courses
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
// Add the shortcode
function pmpro_courses_with_lessons_shortcode( $atts ) {

	// PMPro courses not installed.
	if ( ! function_exists( 'pmpro_courses_get_courses' ) ) {
		return;
	}

	$courses = pmpro_courses_get_courses();
	if ( empty( $courses ) ) {
		return '<p>No courses available.</p>';
	}

	// Output styles inline to keep everything in one file
	ob_start();
	?>
	<style>
	.pmpro-courses-table {
		border-top: 1px solid #ddd;
		margin-top: 1em;
		max-width: 600px;
	}

	.pmpro-courses-header {
		font-weight: bold;
		padding: 0.5em 0;
		border-bottom: 1px solid #ddd;
		text-transform: uppercase;
	}

	.pmpro-course-row {
		display: flex;
		justify-content: space-between;
		padding: 0.5em 0;
		border-bottom: 1px solid #eee;
	}

	.pmpro-course-title a {
		text-decoration: none;
		color: #333;
	}

	.pmpro-course-lessons {
		font-weight: bold;
	}
	</style>
	<?php

	echo '<div class="pmpro-courses-table">';
	echo '<div class="pmpro-courses-header"><strong>Courses</strong></div>';

	foreach ( $courses as $course ) {
		$lesson_count = pmpro_courses_get_lesson_count( $course->ID );

		echo '<div class="pmpro-course-row">';
		echo '<div class="pmpro-course-title"><a href="' . esc_url( get_permalink( $course->ID ) ) . '">' . esc_html( get_the_title( $course->ID ) ) . '</div>';
		echo '<div class="pmpro-course-lessons">' . esc_html( $lesson_count ) . ' lesson' . ( $lesson_count !== 1 ? 's' : '' ) . '</div></a>';
		echo '</div>';
	}

	echo '</div>';

	return ob_get_clean();
}
add_shortcode( 'pmpro_courses_with_lessons', 'pmpro_courses_with_lessons_shortcode' );

