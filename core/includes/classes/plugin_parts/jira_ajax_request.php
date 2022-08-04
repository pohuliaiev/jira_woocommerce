<?php
function jira_user_popup(){
    global $post;
    $user_id = $_POST['user_id'] ? $_POST['user_id'] : ''; ?>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel"><?php echo get_user_meta( $user_id, 'first_name', true ).' '.get_user_meta( $user_id, 'last_name', true );?></h5>
                <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="booked_plus" class="form-label">Change Time In Minutes</label>
                        <input type="number" class="form-control" id="booked_plus" aria-describedby="add_time">
                        <div id="add_time" class="form-text">Change user's booked time </div>
                    </div>
                    <div class="mb-3">
                        <label for="message_time" class="form-label">Notes</label>
                        <textarea class="form-control" id="message_time" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary update-user-time" data-user-id="<?php echo $user_id;?>">Save changes</button>
            </div>
        </div>
    </div>
    <script>
        jQuery(".close").on('click', function () {

            jQuery('.modal-backdrop').remove();

        });
        jQuery("#booked_plus").on('change',function(e){
            jQuery("#billable_plus").prop('disabled',jQuery(this).val().length>0);
        });
        jQuery("#billable_plus").on('change',function(e){
            jQuery("#booked_plus").prop('disabled',jQuery(this).val().length>0);
        });
    </script>
   <?php wp_die();
}
add_action('wp_ajax_jira_user_popup', 'jira_user_popup');
add_action('wp_ajax_nopriv_jira_user_popup', 'jira_user_popup');

function jira_user_manual_update(){
    global $wpdb;
    $user_id = $_POST['user_id'] ? $_POST['user_id'] : '';
    $booked_time = $_POST['booked_time'] ? $_POST['booked_time'] : '';
    $message = $_POST['message'] ? $_POST['message'] : '';

    $table_name = 'jira_users';
    $booked_time_field = 'booked_time';
    $get_booked = $wpdb->prepare( "SELECT {$booked_time_field} FROM {$table_name} WHERE  user_id = %d", $user_id );
    $old_booked = $wpdb->get_col($get_booked)[0];
    $new_booked = $old_booked;
    $user_name = get_user_meta( $user_id, 'first_name', true ).' '.get_user_meta( $user_id, 'last_name', true );
    $project_key = get_user_meta($user_id, 'project_key', true);
    $filter_id = get_user_meta( $user_id, 'jira_filter', true );
    $date = date('Y-m-j G:i:s');
    $current_user = wp_get_current_user();
    if($booked_time){
        $new_booked = $old_booked + $booked_time;
    }

    $table_name = "jira_manual_logs";

    $charset_collate = $wpdb->get_charset_collate();
if($booked_time){
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      log_number bigint(20) AUTO_INCREMENT,
      user_id bigint(20) UNSIGNED NOT NULL,
      user_name VARCHAR(100) NOT NULL,
      booked_time_added bigint(20) SIGNED NOT NULL,
      booked_time_old_value bigint(20) SIGNED NOT NULL,
      booked_time_new_value bigint(20) SIGNED NOT NULL,
      text_message VARCHAR(100) NOT NULL,
      filter_id VARCHAR(100) NOT NULL,
      modify_date DATETIME NOT NULL,
      added_by VARCHAR(100) NOT NULL,
      PRIMARY KEY(log_number)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $wpdb->query("INSERT INTO jira_manual_logs ( user_id,user_name,booked_time_added,booked_time_old_value,booked_time_new_value,text_message, filter_id, modify_date,added_by)
VALUES ('$user_id','$user_name','$booked_time','$old_booked','$new_booked','$message','$filter_id','$date','$current_user->user_login');");


    $wpdb->query("UPDATE jira_users
SET booked_time = '$new_booked'
WHERE user_id = '$user_id';");
}

    wp_die();
}
add_action('wp_ajax_jira_user_manual_update', 'jira_user_manual_update');
add_action('wp_ajax_nopriv_jira_user_manual_update', 'jira_user_manual_update');

/*
function jira_issues_pagination(){
    global $wpdb;
    $page = $_POST['page'] ? $_POST['page'] : '';
    $user_id = $_POST['user'] ? $_POST['user'] : '';

    $billable_issues_db = $wpdb->get_results("SELECT * FROM jira_issues
WHERE user_id = '$user_id'
AND category = 0;");
    $billable_issues_arr = [];

    foreach($billable_issues_db as $item){
        array_push($billable_issues_arr,(array)$item);
    }
    $date_sorted = array_column($billable_issues_arr, 'issue_date');
    array_multisort($date_sorted, SORT_DESC, $billable_issues_arr);
    $billable_issues = $billable_issues_arr;
    $issues_total = $billable_issues ? count($billable_issues) : 1;
    $issues_per_page = 1;
    $total_pages = 1;
    $offset      = $issues_per_page * ($page - 1);
    $total_pages = ceil($issues_total / $issues_per_page);
    $issues = array_slice($billable_issues, $offset, $issues_per_page);
    foreach ($issues as $item){?>
        <tr>
            <td><?php echo $item['issue_key'];?></td>
            <td><?php echo $item['issue_date'];;?></td>
            <td><?php echo $item['time_spent'];?></td>
            <td><?php echo $item['comment'];?></td>
        </tr>
    <? } wp_die();
}
add_action('wp_ajax_jira_issues_pagination', 'jira_issues_pagination');
add_action('wp_ajax_nopriv_jira_issues_pagination', 'jira_issues_pagination');
*/


function jira_billable_admin(){
    global $wpdb;
    $page = 1;
    $user_id = $_POST['user'] ? $_POST['user'] : '';

    $billable_issues_db = $wpdb->get_results("SELECT * FROM jira_issues
WHERE user_id = '$user_id'
AND category = 0;");
    $billable_issues_arr = [];

    foreach($billable_issues_db as $item){
        array_push($billable_issues_arr,(array)$item);
    }
    $date_sorted = array_column($billable_issues_arr, 'issue_date');
    array_multisort($date_sorted, SORT_DESC, $billable_issues_arr);
    $billable_issues = $billable_issues_arr;
    ?>
        <!-- Modal -->

            <div class="modal-dialog user-time-popup">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userTimeModalLabel"><?php echo get_user_meta( $user_id, 'first_name', true ).' '.get_user_meta( $user_id, 'last_name', true );?> billable time</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table billable-popup-table">
                            <thead>
                            <tr>
                                <th scope="col">Issue name</th>
                                <th scope="col">Issue ID</th>
                                <th scope="col">Date</th>
                                <th scope="col">Time spent</th>
                                <th scope="col">Comment</th>
                            </tr>
                            </thead>

                            <tbody>
    <?php foreach ($billable_issues as $item){?>
                            <tr>
                                <td><?php echo $item['issue_key'];?></td>
                                <td><?php echo $item['issue_id'];?></td>
                                <td><?php echo $item['issue_date'];;?></td>
                                <td><?php echo $item['time_spent'];?></td>
                                <td><?php echo $item['comment'];?></td>
                            </tr>
        <?php }?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    <script>
        jQuery(document).ready(function () {
            jQuery('.billable-popup-table').DataTable({
                paging: false
            });
        });
    </script>


    <?  wp_die();
}
add_action('wp_ajax_jira_billable_admin', 'jira_billable_admin');
add_action('wp_ajax_nopriv_jira_billable_admin', 'jira_billable_admin');

function jira_billable_pagination_admin(){
    global $wpdb;
    $page = $_POST['page'] ? $_POST['page'] : '';
    $user_id = $_POST['user'] ? $_POST['user'] : '';

    $billable_issues_db = $wpdb->get_results("SELECT * FROM jira_issues
WHERE user_id = '$user_id'
AND category = 0;");
    $billable_issues_arr = [];

    foreach($billable_issues_db as $item){
        array_push($billable_issues_arr,(array)$item);
    }
    $date_sorted = array_column($billable_issues_arr, 'issue_date');
    array_multisort($date_sorted, SORT_DESC, $billable_issues_arr);
    $billable_issues = $billable_issues_arr;
    $issues_total = $billable_issues ? count($billable_issues) : 1;
    $issues_per_page = 1;
    $total_pages = 1;
    $offset      = $issues_per_page * ($page - 1);
    $total_pages = ceil($issues_total / $issues_per_page);
    $issues = array_slice($billable_issues, $offset, $issues_per_page);
    $next = $page + 1;?>
    <!-- Modal -->


                    <?php foreach ($issues as $item){?>
                        <tr>
                            <td><?php echo $item['issue_key'];?></td>
                            <td><?php echo $item['issue_date'];;?></td>
                            <td><?php echo $item['time_spent'];?></td>
                            <td><?php echo $item['comment'];?></td>
                        </tr>
                    <?php }?>


                <div class="modal-footer">

                </div>




    <?  wp_die();
}
add_action('wp_ajax_jira_billable_pagination_admin', 'jira_billable_pagination_admin');
add_action('wp_ajax_nopriv_jira_billable_pagination_admin', 'jira_billable_pagination_admin');


function jira_nonbillable_admin(){
    global $wpdb;
    $page = $_POST['page'] ? $_POST['page'] : '';
    $user_id = $_POST['user'] ? $_POST['user'] : '';

    $billable_issues_db = $wpdb->get_results("SELECT * FROM jira_issues
WHERE user_id = '$user_id'
AND category = 1;");
    $billable_issues_arr = [];

    foreach($billable_issues_db as $item){
        array_push($billable_issues_arr,(array)$item);
    }
    $date_sorted = array_column($billable_issues_arr, 'issue_date');
    array_multisort($date_sorted, SORT_DESC, $billable_issues_arr);
    $billable_issues = $billable_issues_arr;?>
    <!-- Modal -->

    <div class="modal-dialog user-time-popup">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userTimeModalLabel"><?php echo get_user_meta( $user_id, 'first_name', true ).' '.get_user_meta( $user_id, 'last_name', true );?> nonbillable time</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table billable-popup-table">
                    <thead>
                    <tr>
                        <th scope="col">Issue name</th>
                        <th scope="col">Issue ID</th>
                        <th scope="col">Date</th>
                        <th scope="col">Time spent</th>
                        <th scope="col">Comment</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($billable_issues as $item){?>
                        <tr>
                            <td><?php echo $item['issue_key'];?></td>
                            <td><?php echo $item['issue_id'];;?></td>
                            <td><?php echo $item['issue_date'];;?></td>
                            <td><?php echo $item['time_spent'];?></td>
                            <td><?php echo $item['comment'];?></td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery('.billable-popup-table').DataTable({
                paging: false
            });
        });
    </script>

    <?  wp_die();
}
add_action('wp_ajax_jira_nonbillable_admin', 'jira_nonbillable_admin');
add_action('wp_ajax_nopriv_jira_nonbillable_admin', 'jira_nonbillable_admin');

function jira_nonbillable_pagination_admin(){
    global $wpdb;
    $page = $_POST['page'] ? $_POST['page'] : '';
    $user_id = $_POST['user'] ? $_POST['user'] : '';

    $billable_issues_db = $wpdb->get_results("SELECT * FROM jira_issues
WHERE user_id = '$user_id'
AND category = 1;");
    $billable_issues_arr = [];

    foreach($billable_issues_db as $item){
        array_push($billable_issues_arr,(array)$item);
    }
    $date_sorted = array_column($billable_issues_arr, 'issue_date');
    array_multisort($date_sorted, SORT_DESC, $billable_issues_arr);
    $billable_issues = $billable_issues_arr;
    $issues_total = $billable_issues ? count($billable_issues) : 1;
    $issues_per_page = 1;
    $total_pages = 1;
    $offset      = $issues_per_page * ($page - 1);
    $total_pages = ceil($issues_total / $issues_per_page);
    $issues = array_slice($billable_issues, $offset, $issues_per_page);
    $next = $page + 1;?>
    <!-- Modal -->


    <?php foreach ($issues as $item){?>
        <tr>
            <td><?php echo $item['issue_key'];?></td>
            <td><?php echo $item['issue_date'];;?></td>
            <td><?php echo $item['time_spent'];?></td>
            <td><?php echo $item['comment'];?></td>
        </tr>
    <?php }?>


    <div class="modal-footer">

    </div>




    <?  wp_die();
}
add_action('wp_ajax_jira_nonbillable_pagination_admin', 'jira_nonbillable_pagination_admin');
add_action('wp_ajax_nopriv_jira_nonbillable_pagination_admin', 'jira_nonbillable_pagination_admin');


function jira_user_orders_admin(){
    global $wpdb;
    $user_id = $_POST['user'] ? $_POST['user'] : '';

    $customer_orders = get_posts(
        apply_filters(
            'woocommerce_my_account_my_orders_query',
            array(
                'numberposts' => -1,
                'meta_key'    => '_customer_user',
                'meta_value'  => $user_id,
                'orderby'          => 'date',
                'order'            => 'ASC',
                'post_type'   => wc_get_order_types( 'view-orders' ),
                'post_status' => array_keys( wc_get_order_statuses() ),
            )
        )
    );
    $hourly_orders_objects = [];
    foreach ( $customer_orders as $customer_order ) {
        $order = wc_get_order( $customer_order );
        foreach ( $order->get_items() as $item_id => $item ) {
            $product = $item->get_product();
            if((get_post_meta($product->id, 'hours_field', true))){
                array_push($hourly_orders_objects, $order);
            }
        }
    } ?>
    <!-- Modal -->

    <div class="modal-dialog user-time-popup">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userTimeModalLabel"><?php echo get_user_meta( $user_id, 'first_name', true ).' '.get_user_meta( $user_id, 'last_name', true );?> ordered time</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table billable-popup-table">
                    <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Order ID</th>
                        <th scope="col">Ordered Time</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ( $hourly_orders_objects as $customer_order ) {
                        $order = wc_get_order( $customer_order );
                        $date1 = str_replace('T',' ',$order->get_date_completed());
                        $date = substr($date1, 0, strpos($date1, '+'));
                        $hourly_orders = [];
                    foreach ( $order->get_items() as $item_id => $item ) {
                        $product = $item->get_product();
                        $time = get_post_meta( $product->id, 'hours_field', true );
                        $time_in_minutes = $time * 60;
                        array_push($hourly_orders, $time_in_minutes);
                    }?>
                        <tr>
                            <td><?php echo $date;?></td>
                            <td><?php echo $order->get_id();?></td>
                            <td><?php echo array_sum($hourly_orders).' minutes';?></td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery('.billable-popup-table').DataTable({
                paging: false
            });
        });
    </script>

    <?  wp_die();
}
add_action('wp_ajax_jira_user_orders_admin', 'jira_user_orders_admin');
add_action('wp_ajax_nopriv_user_orders_admin', 'jira_user_orders_admin');

function jira_user_filter_popup(){
    global $post;
    $user_id = $_POST['user_id'] ? $_POST['user_id'] : ''; ?>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel"><?php echo get_user_meta( $user_id, 'first_name', true ).' '.get_user_meta( $user_id, 'last_name', true );?></h5>
                <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="filter_id" class="form-label">Filter ID</label>
                        <input type="text" class="form-control" id="filter_id" aria-describedby="change_filter" value="<?php echo get_user_meta($user_id, 'jira_filter', true);?>">
                        <div id="change_filter" class="form-text">Change user's filter(s) ID</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary update-user-filter" data-user-id="<?php echo $user_id;?>">Save changes</button>
            </div>
        </div>
    </div>
    <script>
        jQuery(".close").on('click', function () {

            jQuery('.modal-backdrop').remove();

        });
    </script>
    <?php wp_die();
}
add_action('wp_ajax_jira_user_filter_popup', 'jira_user_filter_popup');
add_action('wp_ajax_nopriv_jira_user_filter_popup', 'jira_user_filter_popup');


function jira_user_filter_update(){

    $user_id = $_POST['user_id'] ? $_POST['user_id'] : '';
    $filter_id = $_POST['filter_id'] ? $_POST['filter_id'] : '';

    update_user_meta($user_id, 'jira_filter', sanitize_text_field($filter_id));

    wp_die();
}
add_action('wp_ajax_jira_user_filter_update', 'jira_user_filter_update');
add_action('wp_ajax_nopriv_jira_user_filter_update', 'jira_user_filter_update');


function jira_billable_rounded_admin(){
    global $wpdb;
    $page = 1;
    $user_id = $_POST['user'] ? $_POST['user'] : '';

    $billable_issues_db = $wpdb->get_results("SELECT * FROM jira_issues
WHERE user_id = '$user_id'
AND category = 0;");
    $billable_issues_arr = [];

    foreach($billable_issues_db as $item){
        array_push($billable_issues_arr,(array)$item);
    }
    $date_sorted = array_column($billable_issues_arr, 'issue_date');
    array_multisort($date_sorted, SORT_DESC, $billable_issues_arr);
    $billable_issues = $billable_issues_arr;
    ?>
    <!-- Modal -->

    <div class="modal-dialog user-time-popup">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userTimeModalLabel"><?php echo get_user_meta( $user_id, 'first_name', true ).' '.get_user_meta( $user_id, 'last_name', true );?> billable time</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table billable-popup-table">
                    <thead>
                    <tr>
                        <th scope="col">Issue name</th>
                        <th scope="col">Issue ID</th>
                        <th scope="col">Date</th>
                        <th scope="col">Time spent</th>
                        <th scope="col">Comment</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($billable_issues as $item){?>
                        <tr>
                            <td><?php echo $item['issue_key'];?></td>
                            <td><?php echo $item['issue_id'];?></td>
                            <td><?php echo $item['issue_date'];;?></td>
                            <td><?php echo $item['time_spent_rounded'];?></td>
                            <td><?php echo $item['comment'];?></td>
                        </tr>
                    <?php }?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery('.billable-popup-table').DataTable({
                paging: false
            });
        });
    </script>


    <?  wp_die();
}
add_action('wp_ajax_jira_billable_rounded_admin', 'jira_billable_rounded_admin');
add_action('wp_ajax_nopriv_jira_billable_rounded_admin', 'jira_billable_rounded_admin');

function jira_nonbillable_rounded_admin(){
    global $wpdb;
    $page = $_POST['page'] ? $_POST['page'] : '';
    $user_id = $_POST['user'] ? $_POST['user'] : '';

    $billable_issues_db = $wpdb->get_results("SELECT * FROM jira_issues
WHERE user_id = '$user_id'
AND category = 1;");
    $billable_issues_arr = [];

    foreach($billable_issues_db as $item){
        array_push($billable_issues_arr,(array)$item);
    }
    $date_sorted = array_column($billable_issues_arr, 'issue_date');
    array_multisort($date_sorted, SORT_DESC, $billable_issues_arr);
    $billable_issues = $billable_issues_arr;?>
    <!-- Modal -->

    <div class="modal-dialog user-time-popup">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userTimeModalLabel"><?php echo get_user_meta( $user_id, 'first_name', true ).' '.get_user_meta( $user_id, 'last_name', true );?> nonbillable time</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table billable-popup-table">
                    <thead>
                    <tr>
                        <th scope="col">Issue name</th>
                        <th scope="col">Issue ID</th>
                        <th scope="col">Date</th>
                        <th scope="col">Time spent</th>
                        <th scope="col">Comment</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($billable_issues as $item){?>
                        <tr>
                            <td><?php echo $item['issue_key'];?></td>
                            <td><?php echo $item['issue_id'];;?></td>
                            <td><?php echo $item['issue_date'];;?></td>
                            <td><?php echo $item['time_spent_rounded'];?></td>
                            <td><?php echo $item['comment'];?></td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery('.billable-popup-table').DataTable({
                paging: false
            });
        });
    </script>

    <?  wp_die();
}
add_action('wp_ajax_jira_nonbillable_rounded_admin', 'jira_nonbillable_rounded_admin');
add_action('wp_ajax_nopriv_jira_nonbillable_rounded_admin', 'jira_nonbillable_rounded_admin');

function jira_billable_rounded_admin_frontend(){
    global $wpdb;
    $page = 1;
    $user_id = $_POST['user'] ? $_POST['user'] : '';

    $billable_issues_db = $wpdb->get_results("SELECT * FROM jira_issues
WHERE user_id = '$user_id'
AND category = 0;");
    $billable_issues_arr = [];

    foreach($billable_issues_db as $item){
        array_push($billable_issues_arr,(array)$item);
    }
    $date_sorted = array_column($billable_issues_arr, 'issue_date');
    array_multisort($date_sorted, SORT_DESC, $billable_issues_arr);
    $billable_issues = $billable_issues_arr;
    ?>
    <!-- Modal -->

    <div class="modal-dialog user-time-popup">
        <div class="modal-content">
            <div class="modal-header-frontend">
                <h5 id="userTimeModalLabel"><?php echo get_user_meta( $user_id, 'first_name', true ).' '.get_user_meta( $user_id, 'last_name', true );?> billable time</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table billable-popup-table">
                    <thead>
                    <tr>
                        <th scope="col">Issue name</th>
                        <th scope="col">Issue ID</th>
                        <th scope="col">Date</th>
                        <th scope="col">Time spent</th>
                        <th scope="col">Comment</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($billable_issues as $item){?>
                        <tr>
                            <td><?php echo $item['issue_key'];?></td>
                            <td><?php echo $item['issue_id'];?></td>
                            <td><?php echo $item['issue_date'];;?></td>
                            <td><?php echo $item['time_spent_rounded'];?></td>
                            <td><?php echo $item['comment'];?></td>
                        </tr>
                    <?php }?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>



    <?  wp_die();
}
add_action('wp_ajax_jira_billable_rounded_admin_frontend', 'jira_billable_rounded_admin_frontend');
add_action('wp_ajax_nopriv_jira_billable_rounded_admin_frontend', 'jira_billable_rounded_admin_frontend');

