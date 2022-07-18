<?php

    add_filter('cron_schedules', 'jira_update_users_orders');
    function jira_update_users_orders($schedules)
    {
        $interval = 1;
        $schedules['every_one_minute'] = array(
            'interval' => 60 * $interval,
            'display' => __('Every 1 Minute users update', 'textdomain')
        );
        return $schedules;
    }

// Schedule an action if it's not already scheduled
    if (!wp_next_scheduled('jira_update_users_orders')) {
        wp_schedule_event(time(), 'every_one_minute', 'jira_update_users_orders');
    }

// Hook into that action that'll fire every five minutes
    add_action('jira_update_users_orders', 'every_five_minutes_event_func');
    function every_one_minute_event_func()
    {
        jira_update_user_tables();
    }


