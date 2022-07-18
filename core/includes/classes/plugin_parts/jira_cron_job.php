<?php
if( get_option( 'jira_example_plugin_settings' )['cron_checkbox'] == '1' ) {

    add_filter('cron_schedules', 'jira_add_every_five_minutes');
    function jira_add_every_five_minutes($schedules)
    {
        $interval = 5;
        if(get_option( 'jira_example_plugin_settings' )['cron_checkbox'] > 0 &&  get_option( 'jira_example_plugin_settings' )['cron_interval'] > 0){
            $interval = get_option( 'jira_example_plugin_settings' )['cron_interval'];
        }
        $schedules['every_five_minutes'] = array(
            'interval' => 60 * $interval,
            'display' => __('Every 5 Minutes', 'textdomain')
        );
        return $schedules;
    }

// Schedule an action if it's not already scheduled
    if (!wp_next_scheduled('jira_add_every_five_minutes')) {
        wp_schedule_event(time(), 'every_five_minutes', 'jira_add_every_five_minutes');
    }

// Hook into that action that'll fire every five minutes
    add_action('jira_add_every_five_minutes', 'every_five_minutes_event_func');
    function every_five_minutes_event_func()
    {
        jira_update_user_tables();
    }

}
