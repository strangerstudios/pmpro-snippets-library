<?php
/**
 * Add custom columns to PMPro Courses and Lessons in the PMPro Courses Add On counting users having completed lessons and courses
 *
 * title: Add custom columns to PMPro Courses and Lessons in the PMPro Courses Add On.
 * layout: snippet
 * collection: add-ons
 * category: wp-list-table
 * link: http://www.paidmembershipspro.com/user-course-progress
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 
 
/**
 * Add custom column to the lessons list table.
 *
 * @param array $columns The WP_List_Table columns array.
 * @return array The updated columns array.
 */
function lessons_custom_columns( $columns ) {
    $columns['completed_users'] = 'Users have completed this lesson';
    return $columns;
}
add_filter('manage_pmpro_lesson_posts_columns', 'lessons_custom_columns');

/**
 * Hook into manage posts custom column to display the quantity of users completed this lesson. Iterates over table columns
 * 
 * @param $column The name of the table column being iterated over.
 * @param $post_id The ID of the current post.
 */
function lessons_custom_column_content( $column, $post_id ) {
    if ($column == 'completed_users') {
        // Get and display the quantity of users completed this lesson
		$lesson = get_post($post_id);
		$parent_id = $lesson->post_parent;
        echo count_users_completed_lesson( $post_id, $parent_id );
    }
}
add_action('manage_pmpro_lesson_posts_custom_column', 'lessons_custom_column_content', 10, 2);

/**
 * Add custom column to the courses list table.
 *
 * @param array $columns The WP_List_Table columns array.
 * @return array The updated columns array.
 */
function courses_custom_columns( $columns ) {
    $columns['completed_users_all_lessons'] = 'Users have completed this course (all lessons)';
    return $columns;
}
add_filter('manage_pmpro_course_posts_columns', 'courses_custom_columns');


/**
 * Hook into manage posts custom column to display the quantity of users completed this course. Iterates over table columns
 * 
 * @param $column The name of the table column being iterated over.
 * @param $post_id The ID of the current post.
 */
function courses_custom_column_content($column, $post_id) {
    if ($column == 'completed_users_all_lessons') {
        echo count_users_completed_course( $post_id );
    }
}
add_action('manage_pmpro_course_posts_custom_column', 'courses_custom_column_content', 10, 2);


/**
 * Count users completed a lesson.
 * 
 * @param $lesson_id The ID of the lesson.
 * @param $course_id The ID of the course.
 * @return int The quantity of users completed the lesson.
 */
function count_users_completed_lesson( $lesson_id, $course_id ) {
    global $wpdb;

    $query = $wpdb->prepare(
        "SELECT COUNT(user_id) FROM {$wpdb->usermeta} WHERE meta_key = 'pmpro_courses_progress_" . esc_sql( $course_id ). "'" .
        " AND meta_value LIKE %s",
        '%i:' . $lesson_id . ';b:1%'
    );

    $ret =  $wpdb->get_var($query);
	return $ret != null ? $ret : 0;
}

/**
 * Count users completed a course.
 *
 * @param $course_id The ID of the course.
 * @return int The quantity of users completed the lesson.
 */
function count_users_completed_course( $course_id ) {
    global $wpdb;

    $query = $wpdb->prepare(
        "SELECT COUNT(user_id)  FROM {$wpdb->usermeta} WHERE meta_key = 'pmpro_courses_progress_" .  esc_sql( $course_id ) . "'" .
        " AND meta_value NOT LIKE %s",
		 '%b:0%'
    );

	$ret =  $wpdb->get_var( $query);
    return $ret != null ? $ret : 0;
}
