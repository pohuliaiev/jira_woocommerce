<?php  if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$query_string = $_SERVER['QUERY_STRING'];?>
<h2 class="text-center">Jira Log</h2>

<?php

global $wpdb;
$demo_arr = $wpdb->get_results("SELECT * FROM jira_api_logs;");
$demo_arr2 = $wpdb->get_results("SELECT * FROM jira_manual_logs;");
$final_arr = array_merge($demo_arr,$demo_arr2);
$logs_arr = [];

foreach($final_arr as $item){
    array_push($logs_arr,(array)$item);
}
$date_sorted = array_column($logs_arr, 'modify_date');
array_multisort($date_sorted, SORT_DESC, $logs_arr);
$logs_total = $logs_arr ? count($logs_arr) : 1;
// grab the current page number and set to 1 if no page number is set
$page = isset($_GET['p']) ? $_GET['p'] : 1;
// how many users to show per page
$logs_per_page = 10;
$total_pages = 1;
$offset      = $logs_per_page * ($page - 1);
$total_pages = ceil($logs_total / $logs_per_page);
// print_r($logs_arr);
$base = admin_url('admin.php') . '?' . remove_query_arg('p', $query_string) . '%_%' ;
$logs = array_slice($logs_arr, $offset, $logs_per_page);
?>
<?php if($logs_arr):?>
    <table class="form-table jira-users-table">

        <th scope="row">Update source</th>
        <th scope="row">Updated By</th>
        <th scope="row">User Affected</th>
        <th scope="row">Date and Time</th>
        <th scope="row">Fields affected</th>
        <th scope="row">Update notice</th>
       <?php foreach($logs as $item){
           $comment = '';
           if($item['text_message']){
               $comment = $item['text_message'];
           }
           if($item['comment']){
               $comment = $item['comment'];
           }
           $updated_by = '';
           if($item['added_by']){
               $source = 'Manual';
               $updated_by = $item['added_by'];
           }else{
               $source = 'API/System';
           }?>
            <tr>

                <td>
                <?php echo $source;?>
                </td>

                <td>
                <?php echo $updated_by;?>
                </td>

                <td>
                    <?php echo $item['user_name'];?>
                </td>

                <td>
                    <?php echo $item['modify_date'];?>
                </td>
                <td>
                <?php if($item['billable_old_value'] != $item['billable_new_value']){
                    echo 'Billable old value:'.$item['billable_old_value'].'<br>';
                    echo 'Billable new value:'.$item['billable_new_value'].'<br>';
                }
                if($item['nonbillable_old_value'] != $item['nonbillable_new_value']){
                    echo 'Nonbillable old value: '.$item['nonbillable_old_value'].'<br>';
                    echo 'Nonbillable new value: '.$item['nonbillable_new_value'].'<br>';
                }
                if($item['booked_time_old_value'] != $item['booked_time_new_value']){
                    echo 'Booked time old value: '.$item['booked_time_old_value'].'<br>';
                    echo 'Booked time new value: '.$item['booked_time_new_value'].'<br>';
                }?>
                </td>
                <td>
                    <?php echo $comment?>
                </td>

            </tr>
        <?php }?>

    </table>
<?php endif;?>
<?php // if on the front end, your base is the current page
//$base = get_permalink( get_the_ID() ) . '?' . remove_query_arg('p', $query_string) . '%_%';
echo '<div id="pagination" class="tablenav-pages">';
echo paginate_links( array(
    'base'      => $base, // the base URL, including query arg
    'format'    => '&p=%#%', // this defines the query parameter that will be used, in this case "p"
    'prev_text' => __('&laquo; Previous'), // text for previous page
    'next_text' => __('Next &raquo;'), // text for next page
    'total'     => $total_pages, // the total number of pages we have
    'current'   => $page, // the current page
    'end_size'  => 1,
    'mid_size'  => 5,
));
echo '</div>';

?>
