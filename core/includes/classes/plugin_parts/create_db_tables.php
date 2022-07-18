<?php
function create_db_table(){
    global $wpdb;

    $table_name = "jira_users";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      user_id bigint(20) UNSIGNED NOT NULL,
      user_name VARCHAR(100) NOT NULL,
      booked_time bigint(20) UNSIGNED NOT NULL,
      billable bigint(20) UNSIGNED NOT NULL,
      nonbillable bigint(20) UNSIGNED NOT NULL,
      billable_rounded bigint(20) UNSIGNED NOT NULL,
      nonbillable_rounded bigint(20) UNSIGNED NOT NULL,
      filter_id VARCHAR(100) NOT NULL,
      PRIMARY KEY user_id (user_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
function jira_return_user_db_values($user_id){
    global $wpdb;
    $table_name = 'jira_users';
    $billable = 'billable';
    $billable_rounded = 'billable_rounded';
    $nonbillable = 'nonbillable';
    $booked_time = 'booked_time';
    $get_booked = $wpdb->prepare( "SELECT {$booked_time} FROM {$table_name} WHERE  user_id = %d", $user_id );
    $get_billable = $wpdb->prepare( "SELECT {$billable} FROM {$table_name} WHERE  user_id = %d", $user_id );
    $get_billable_rounded = $wpdb->prepare( "SELECT {$billable_rounded} FROM {$table_name} WHERE  user_id = %d", $user_id );
    $get_nonbillable = $wpdb->prepare( "SELECT {$nonbillable} FROM {$table_name} WHERE  user_id = %d", $user_id );
    return ['billable' =>  $wpdb->get_col($get_billable)[0], 'nonbillable' => $wpdb->get_col($get_nonbillable)[0],
    'booked' => $wpdb->get_col($get_booked)[0], 'billable_rounded' => $wpdb->get_col($get_billable_rounded)[0]];

}
function create_jira_log_table(){
    global $wpdb;
    $table_name = "jira_api_logs";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      log_number bigint(20) AUTO_INCREMENT,
      user_id bigint(20) UNSIGNED NOT NULL,
      user_name VARCHAR(100) NOT NULL,
      booked_time_old_value bigint(20) UNSIGNED NOT NULL,
      booked_time_new_value bigint(20) UNSIGNED NOT NULL,
      billable_old_value bigint(20) UNSIGNED NOT NULL,
      billable_new_value bigint(20) UNSIGNED NOT NULL,
      nonbillable_old_value bigint(20) UNSIGNED NOT NULL,
      nonbillable_new_value bigint(20) UNSIGNED NOT NULL,
      filter_id VARCHAR(100) NOT NULL,
      comment VARCHAR(100),
      modify_date DATETIME NOT NULL,
      PRIMARY KEY(log_number)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function update_jira_log($user_id,
                         $billable,
                         $nonbillable,
                         $old_billabe,
                         $old_nonbillable,
                         $booked_time,
                         $old_booked){
    global $wpdb;
    $user_name = get_user_meta( $user_id, 'first_name', true ).' '.get_user_meta( $user_id, 'last_name', true );
    $filter_id = get_user_meta( $user_id, 'jira_filter', true );
    $date = date('Y-m-j G:i:s');
    $comment = 'Updated via cron';
    $wpdb->query("INSERT INTO jira_api_logs ( user_id, user_name, booked_time_old_value, booked_time_new_value, billable_old_value, billable_new_value, nonbillable_old_value, nonbillable_new_value, filter_id, modify_date, comment)
VALUES ('$user_id','$user_name','$old_booked','$booked_time','$old_billabe','$billable','$old_nonbillable','$nonbillable','$filter_id','$date','$comment');");
}

function create_jira_issues_tables(){
    global $wpdb;

    $table_name = "jira_issues";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      user_id bigint(20) UNSIGNED NOT NULL,
      user_name VARCHAR(100) NOT NULL,
      issue_id bigint(20) UNSIGNED NOT NULL,
      issue_key VARCHAR(100) NOT NULL,
      time_spent bigint(20) UNSIGNED NOT NULL,
      time_spent_rounded bigint(20) UNSIGNED NOT NULL,
      filter_id VARCHAR(100) NOT NULL,
      workog_id bigint(20) UNSIGNED NOT NULL,
      category bigint(20) UNSIGNED NOT NULL,
      comment VARCHAR(100) NOT NULL,
      issue_date DATETIME NOT NULL,
      PRIMARY KEY(workog_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

}

function update_jira_issues($start, $filter_id, $user_id){
    global $wpdb;
    $issues = get_filter_sum($filter_id,$start)['issues'];
    $user_name = get_user_meta( $user_id, 'first_name', true ).' '.get_user_meta( $user_id, 'last_name', true );

    foreach($issues as $issue){
        foreach($issue as $item_prev) {
            foreach ($item_prev as $item) {
                $time1 = str_replace("T", " ", $item['date']);
                $time = str_replace(".000Z", "", $time1);
                $issueKey = $item['issueKey'];
                $issueId = $item['issueId'];
                $timeSpent = $item['timeSpent'];
                $timeSpentRounded = $item['timeSpentRounded'];
                $comment = $item['comment'];
                $workLogId = $item['worklogId'];
                $category = $item['category'];

                $wpdb->query("INSERT INTO jira_issues ( user_id, user_name, issue_id, issue_key, time_spent, time_spent_rounded, filter_id, workog_id, category, comment,issue_date)
VALUES ('$user_id','$user_name','$issueId','$issueKey','$timeSpent','$timeSpentRounded','$filter_id','$workLogId','$category','$comment','$time');");
            }
        }
    }

}




function jira_update_user_tables(){
    create_db_table();
    create_jira_log_table();
    create_jira_issues_tables();
    global $wpdb;
    $args = array( // get all users where
        'meta_query' => array(
            array(
                'key' => 'jira_filter', // the key 'specialkey'
                'compare' => '!=', // has a value that is equal to
                'value' => '' // hello world
            )
        ),
    );
    $user_query = new WP_User_Query( $args );

    if ( !empty( $user_query->results ) ) {
        foreach ( $user_query->results as $user ) {
            jira_user_orders_data($user->id);
            $user_name = get_user_meta( $user->id, 'first_name', true ).' '.get_user_meta( $user->id, 'last_name', true );
            $project_key = get_user_meta($user->id, 'project_key', true);
            $filter_id = get_user_meta( $user->id, 'jira_filter', true );
            $start_date = jira_user_orders_data($user->id)['start_date'];
            $booked_time_woocommerce = jira_user_orders_data($user->id)['booked_time'];
            $billable = get_filter_sum($filter_id,$start_date)['billable'];
            $nonbillable = get_filter_sum($filter_id,$start_date)['nonbillable'];
            $billable_rounded = get_filter_sum($filter_id,$start_date)['billable_rounded'];
            $nonbillable_rounded = get_filter_sum($filter_id,$start_date)['nonbillable_rounded'];
            $old_billable = jira_return_user_db_values($user->id)['billable'];
            $old_nonbillable = jira_return_user_db_values($user->id)['nonbillable'];
            $old_booked = jira_return_user_db_values($user->id)['booked'];
            $table_name = 'jira_manual_logs';
            $booked_time_added = 'booked_time_added';
            $get_manual_booked = $wpdb->prepare( "SELECT {$booked_time_added} FROM {$table_name} WHERE  user_id = %d", $user->id );
            $booked_time = array_sum($wpdb->get_col($get_manual_booked)) + $booked_time_woocommerce;

            update_jira_issues(jira_user_orders_data($user->id)['start_date'],$filter_id,$user->id);

            if($billable != $old_billable || $nonbillable != $old_nonbillable || $old_booked != $booked_time){
                update_jira_log($user->id,$billable,$nonbillable,$old_billable,$old_nonbillable,$booked_time,$old_booked);
            }
            $wpdb->query("INSERT INTO jira_users ( user_id)
VALUES ('$user->id');");
            $wpdb->query("UPDATE jira_users
SET user_name = '$user_name', booked_time = '$booked_time', billable = '$billable',nonbillable = '$nonbillable',billable_rounded = '$billable_rounded',nonbillable_rounded = '$nonbillable_rounded',filter_id = '$filter_id'
WHERE user_id = '$user->id';");

        }
    }
}

add_action( 'woocommerce_thankyou', 'jira_update_after_order', 20, 1 );
function jira_update_after_order(){
    $user_id = get_current_user_id();
    global $wpdb;
    $table_name = 'jira_manual_logs';
    $user_name = get_user_meta( $user_id, 'first_name', true ).' '.get_user_meta( $user_id, 'last_name', true );
    $filter_id = get_user_meta( $user_id, 'jira_filter', true );
    $project_key = get_user_meta($user_id, 'project_key', true);
    $date = date('Y-m-j G:i:s');
    $booked_time_woocommerce = jira_user_orders_data($user_id)['booked_time'];
    $old_booked = jira_return_user_db_values($user_id)['booked'];
    $booked_time_added = 'booked_time_added';
    $get_manual_booked = $wpdb->prepare( "SELECT {$booked_time_added} FROM {$table_name} WHERE  user_id = %d", $user_id );
    $booked_time = array_sum($wpdb->get_col($get_manual_booked)) + $booked_time_woocommerce;
    $comment = 'User bought a time in Woocommerce';
    if($old_booked != $booked_time){
        $wpdb->query("INSERT INTO jira_api_logs ( user_id, user_name, booked_time_old_value, booked_time_new_value, filter_id, modify_date, comment)
VALUES ('$user_id','$user_name','$old_booked','$booked_time','$filter_id','$date', '$comment');");
        $wpdb->query("INSERT INTO jira_users ( user_id)
VALUES ('$user_id');");
        $wpdb->query("UPDATE jira_users
SET booked_time = '$booked_time'
WHERE user_id = '$user_id';");
    }

}

function jira_delete_tables(){
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS jira_users, jira_issues, jira_api_logs, jira_manual_logs;");
}