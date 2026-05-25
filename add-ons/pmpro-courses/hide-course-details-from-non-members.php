<?php
/**
 * This recipe hides the full course details from non-members, honoring the PMPro excerpt setting.
 *
 * title: Hide Course Details from Non-Members Using the Default Courses Add On Module
 * layout: snippet
 * collection: add-ons
 * category: restricting-content, pmpro-courses
 * link: https://www.paidmembershipspro.com/hide-course-details-lesson-list-non-members-default-module/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

add_filter( 'pmpro_courses_show_course_content_to_nonmembers', '__return_false' );
