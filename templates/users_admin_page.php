<?php  if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
$query_string = $_SERVER['QUERY_STRING'];
?>

<div class="loading display-none"></div>
<h2 class="text-center">User's Jira Time</h2>
<div class="jira-admin-filter">
	<div class="filter-element">
		<form method="get" action="<?php echo admin_url('admin.php');?>">
			<div>
				<label class="screen-reader-text" for="usearch"><?php _x( 'Search for:', 'label' ); ?></label>
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'];?>" />
				<input type="text" value="<?php $_GET['usearch'];?>" name="usearch" placeholder="<?php if( $_GET['usearch']){ echo $_GET['usearch'];}else{echo "Type user's name";} ?>"/>
				<input type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button' ); ?>" />
			</div>
		</form>
	</div>

	<div >

		<form method="get" action="<?php echo admin_url('admin.php'); ?>">
			<div class="form-div">
				<div class="form">

					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'];?>" />
					<select name="time" id="time">
						<option value="">All</option>
						<option value="overbooked" <?php if( $_GET['time']){ echo 'selected';} ?>>Overbooked</option>
					</select>
				</div>
				<input type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button' ); ?>" />
			</div>


		</form>
	</div>
	</div>




<?php

$only_fields = array( 'user_login', 'user_nicename', 'user_email','ID' );
$count_args  = array(
	'meta_query' => array(
	),

);


if($_GET['usearch']){
	$count_args['meta_query'][] = array(
		'relation' => 'OR',
		array(
			'key'     => 'first_name',
			'value'   => $_GET['usearch'],
			'compare' => 'LIKE'
		),
		array(
			'key'     => 'last_name',
			'value'   => $_GET['usearch'],
			'compare' => 'LIKE'
		)
	);
}

if($_GET['time'] ){
	$count_args  = array(
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => 'total_time',
				'compare' => '<',
				'value' => 0
			)
		),

	);

}

$user_count_query = new WP_User_Query($count_args);
$user_count = $user_count_query->get_results();

// count the number of users found in the query
$total_users = $user_count ? count($user_count) : 1;

// grab the current page number and set to 1 if no page number is set
$page = isset($_GET['p']) ? $_GET['p'] : 1;

// how many users to show per page
$users_per_page = 20;

// calculate the total number of pages.
$total_pages = 1;
$offset      = $users_per_page * ($page - 1);
$total_pages = ceil($total_users / $users_per_page);

$user_fields =  array( 'user_login', 'user_nicename', 'user_email','ID' );


$args = array( // get all users where
	'number'    => $users_per_page,
	'offset'    => $offset
);

if($_GET['usearch']){
	$args['meta_query'][] = array(
		'relation' => 'OR',
		array(
			'key'     => 'first_name',
			'value'   => $_GET['usearch'],
			'compare' => 'LIKE'
		),
		array(
			'key'     => 'last_name',
			'value'   => $_GET['usearch'],
			'compare' => 'LIKE'
		)
	);
}

if($_GET['time'] ){
	$args['meta_query'][] = array(
		array(
			'key'     => 'total_time',
			'compare' => '<',
			'value' => 0
		)
	);
}


$base = admin_url('admin.php') . '?' . remove_query_arg('p', $query_string) . '%_%' ;


$user_query = new WP_User_Query( $args ); ?>
<?php if ( !empty( $user_query->results ) ) {?>
<div class="demo-data">
	</div>
<table class="form-table jira-users-table" >
	<th scope="row">User</th>
	<th scope="row">Total time booked</th>
	<th scope="row">Time currently left</th>
	<th scope="row">Billable Booked</th>
	<th scope="row">Nonbillable Booked</th>
	<th scope="row">Billable Real</th>
	<th scope="row">Nonbillable Real</th>
	<th scope="row">Filter ID</th>
	<th scope="row">Overbooked</th>
	<th scope="row">Manual editing</th>
	<?php foreach ( $user_query->results as $user ) {?>
		<?php global $wpdb;
		$table_name = 'jira_users';
		$billable = 'billable';
		$nonbillable = 'nonbillable';
		$billable_rounded = 'billable_rounded';
		$nonbillable_rounded = 'nonbillable_rounded';
		$booked_time = 'booked_time';
		$get_booked = $wpdb->prepare( "SELECT {$booked_time} FROM {$table_name} WHERE  user_id = %d", $user->id );
		$get_billable = $wpdb->prepare( "SELECT {$billable} FROM {$table_name} WHERE  user_id = %d", $user->id );
		$get_nonbillable = $wpdb->prepare( "SELECT {$nonbillable} FROM {$table_name} WHERE  user_id = %d", $user->id );
		$get_billable_rounded = $wpdb->prepare( "SELECT {$billable_rounded} FROM {$table_name} WHERE  user_id = %d", $user->id );
		$get_nonbillable_rounded = $wpdb->prepare( "SELECT {$nonbillable_rounded} FROM {$table_name} WHERE  user_id = %d", $user->id );
		$overbooked = $wpdb->get_col($get_booked)[0] - $wpdb->get_col($get_billable)[0];
		$table_name = 'jira_manual_logs';
		$booked_time_added = 'booked_time_added';
		$get_manual_booked = $wpdb->prepare( "SELECT {$booked_time_added} FROM {$table_name} WHERE  user_id = %d", $user->id );
		?>
	<tr class="<?php echo $user->id;?>">

		<td>
			<span class="time-popup user-orders" data-user="<?php echo $user->id;?>"><?php echo get_user_meta( $user->id, 'first_name', true ).' '.get_user_meta( $user->id, 'last_name', true );?></span>
		</td>
		<td class="booked">
			<?php echo $wpdb->get_col($get_booked)[0];?>
		</td>
		<td>
			<?php echo $wpdb->get_col($get_booked)[0] - $wpdb->get_col($get_billable_rounded)[0];?>
		</td>
		<td class="billable-booked">
			<?php if($wpdb->get_col($get_billable_rounded)[0] > 0){
				echo '<span class="time-popup bill_rounded" data-user="'.$user->id.'">'.$wpdb->get_col($get_billable_rounded)[0].'</span>';
			}else{
				echo $wpdb->get_col($get_billable_rounded)[0];
			}?>
		</td>
		<td>
			<?php if($wpdb->get_col($get_nonbillable_rounded)[0] > 0){
				echo '<span class="time-popup nonbill_rounded" data-user="'.$user->id.'">'.$wpdb->get_col($get_nonbillable_rounded)[0].'</span>';
			}else{
				echo $wpdb->get_col($get_nonbillable_rounded)[0];
			}?>
		</td>
		<td class="billable">
			<?php if($wpdb->get_col($get_billable)[0] > 0){
				echo '<span class="time-popup bill" data-user="'.$user->id.'">'.$wpdb->get_col($get_billable)[0].'</span>';
			}else{
				echo $wpdb->get_col($get_billable)[0];
			}?>
		</td>
		<td>
			<?php if($wpdb->get_col($get_nonbillable)[0] > 0){
				echo '<span class="time-popup nonbill" data-user="'.$user->id.'">'.$wpdb->get_col($get_nonbillable)[0].'</span>';
			}else{
				echo $wpdb->get_col($get_nonbillable)[0];
			}?>
		</td>
		<td class="filter_id">
			<span class="time-popup user-filters" data-user="<?php echo $user->id;?>">

                <?php if(get_user_meta( $user->id, 'jira_filter', true )) {
                    echo get_user_meta($user->id, 'jira_filter', true);
                    } else {
                echo 'Add filter';
                }?></span>
		</td>
		<td class="overbooked">

			<?php if($overbooked < 0){
				echo 'Yes';
			}?>
		</td>
		<td>

			<button type="button" class="btn btn-info ajax-call" data-bs-id="<?php echo $user->id;?>" data-user-id="<?php echo $user->id;?>">Edit</button>
		</td>

	</tr>
<?php }?>
</table>
<?php }else{
	echo 'No users found';
}?>
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
<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="userModalLabel">Modal title</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form>
					<div class="mb-3">
						<label for="billable_plus" class="form-label">Add Time</label>
						<input type="number" class="form-control" id="billable_plus" aria-describedby="add_time">
						<div id="add_time" class="form-text">Add booked time to user</div>
					</div>
					<div class="mb-3">
						<label for="billable_minus" class="form-label">Subtract Time</label>
						<input type="number" class="form-control" id="billable_minus" aria-describedby="subtract_time">
						<div id="subtract_time" class="form-text">Subtract booked time</div>
					</div>
					<div class="mb-3">
						<label for="message_time" class="form-label">Notes</label>
						<textarea class="form-control" id="message_time" rows="3"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save changes</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="userTimeModal" tabindex="-1" aria-labelledby="userTimeModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="userTimeModalLabel">Modal title</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form>
					<div class="mb-3">
						<label for="billable_plus" class="form-label">Add Time In Minutes</label>
						<input type="number" class="form-control" id="billable_plus" aria-describedby="add_time">
						<div id="add_time" class="form-text">Add booked time to user</div>
					</div>
					<div class="mb-3">
						<label for="billable_minus" class="form-label">Subtract Time</label>
						<input type="number" class="form-control" id="billable_minus" aria-describedby="subtract_time">
						<div id="subtract_time" class="form-text">Subtract booked time</div>
					</div>
					<div class="mb-3">
						<label for="message_time" class="form-label">Notes</label>
						<textarea class="form-control" id="message_time" rows="3"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save changes</button>
			</div>
		</div>
	</div>
	</div>


