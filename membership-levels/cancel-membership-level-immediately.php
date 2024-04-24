<?php
/**
 * Cancel an automatically recurring payment membership level immediately.
 *
 * title: Cancel Recurring Level Immediately
 * layout: snippet
 * collection: membership-levels
 * category: cancellation
 * link: https://www.paidmembershipspro.com/documentation/membership-levels/canceling-a-user-membership/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
add_filter( 'pmpro_cancel_on_next_payment_date', '__return_false' );
