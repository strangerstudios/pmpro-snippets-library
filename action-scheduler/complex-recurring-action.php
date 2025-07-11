<?php
/**
 * Custom recurring action example for PMPro.
 * This code sets up a weekly action that loops over some example user IDs
 * and then schedules a custom task for each user.
 *
 * When the task is run, it will execute the 'my_custom_user_task_handler'
 * with the passed-in user ID and custom data.
 */
function my_custom_weekly_action() {
	// Let's loop over some items and setup actions for each one
	$users_ids = array( 1, 2, 3 ); // Example user IDs

	foreach ( $users_ids as $user_id ) {
		PMPro_Action_Scheduler::instance()->maybe_add_task(
			'my_custom_user_task', // Custom task to be executed
			array(
				'user_id'     => $user_id, // User ID to pass to the task handler
				'custom_data' => 'some_value', // Custom data to pass to the task handler
			),
			'my_custom_tasks' // Grouping tasks under 'my_custom_tasks'
		);
	}
}
add_action( 'pmpro_schedule_weekly', 'my_custom_weekly_action' );

/**
 * Custom task function that will be executed for each user ID scheduled.
 */
function my_custom_user_task_handler( $args ) {

	$user_id     = $args['user_id'];  // The user ID passed from the scheduled action
	$custom_data = $args['custom_data']; // Custom data passed from the scheduled action

	//  Do something with the user ID and custom data
	// For example, log the user ID and custom data
	error_log( "Running custom task for user ID: $user_id with data: $custom_data" );
}
add_action( 'my_custom_user_task', 'my_custom_user_task_handler' );
