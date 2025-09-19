/**
 * Give all non-member WP users level 1 with a specific start and end date set
 * change the level (1) below and start (2017-01-01) and end (2017-12-31) dates below to per your needs.
 *
 * title: Database Script: Apply a Membership Level to All Users Without a Level
 * layout: snippet
 * collection: membership-levels
 * category: levels, sql
 * link: https://www.paidmembershipspro.com/database-script-apply-membership-level-users-without-level/
 *
 * This is a database query.
 * Back up your database before running this query so you can restore if needed.
 * Change the membership_id (currently 1) to the ID of the level you want to assign, and adjust the start and end dates as needed. Use NULL for enddate if there is no expiration.
 * Run the query in phpMyAdmin, Adminer, or the MySQL command line. You can run the SELECT portion by itself first to preview which users will be affected before inserting.
**/
INSERT INTO wp_pmpro_memberships_users (user_id, membership_id, code_id, initial_payment, billing_amount, cycle_number, cycle_period, billing_limit, trial_amount, trial_limit, status, startdate, enddate)
SELECT
  u.ID,           -- ID from wp_users table
  1,              -- id of the level to give users
  0,              -- code_id: 0 means no discount code
  0,              -- initial_payment: 0 for no payment
  0,              -- billing_amount: 0 for no recurring billing
  0,              -- cycle_number: 0 for no cycle
  'Month',        -- cycle_period: default to 'Month'
  0,              -- billing_limit: 0 for no limit
  0,              -- trial_amount: 0 for no trial
  0,              -- trial_limit: 0 for no trial period
  'active',       -- status
  '2025-01-01',   -- start date
  '2025-12-31'    -- end date (or NULL for no expiration)
FROM wp_users u
LEFT JOIN wp_pmpro_memberships_users mu
  ON u.ID = mu.user_id
  AND status = 'active'
WHERE mu.id IS NULL;
