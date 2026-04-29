<?php
/**
 * Enable async Stripe webhook processing only on the production environment.
 * Requires PMPro version 3.7+
 *
 * title: Enable Async Stripe Webhook Processing Conditionally by Environment
 * layout: snippet
 * collection: payment-gateways, stripe
 * category: action-scheduler
 * link: https://www.paidmembershipspro.com/async-processing-stripe-webhooks/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_enable_async_stripe_webhooks() {
    return wp_get_environment_type() === 'production';
}

add_filter( 'pmpro_stripe_webhook_enable_async_processing', 'my_pmpro_enable_async_stripe_webhooks' );
