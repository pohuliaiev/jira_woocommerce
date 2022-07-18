<?php

function jira_user_orders_data($user_id){
	$customer_orders = get_posts(
		apply_filters(
			'woocommerce_my_account_my_orders_query',
			array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => $user_id,
				'orderby'          => 'date',
				'order'            => 'DESC',
				'post_type'   => wc_get_order_types( 'view-orders' ),
				'post_status' => array_keys( wc_get_order_statuses() ),
			)
		)
	);
	$hourly_orders = [];
	$hourly_orders_objects = [];
	foreach ( $customer_orders as $customer_order ) {
		$order = wc_get_order( $customer_order );

		foreach ( $order->get_items() as $item_id => $item ) {
			$product = $item->get_product();
			if((get_post_meta($product->id, 'hours_field', true) != '')){
				if(get_user_meta($user_id,'jira_orders',true) != 1){
					update_user_meta($user_id,'jira_orders',1);
				}
				array_push($hourly_orders, $product);
				array_push($hourly_orders_objects, $order);
			}
		}

	}
	$hourly_orders_sum = [];
	foreach($hourly_orders as $hourly_order){
		$time = get_post_meta( $hourly_order->id, 'hours_field', true );
		$time_in_minutes = $time * 60;
		array_push($hourly_orders_sum, $time_in_minutes);
	}
	$project_key =  get_user_meta( $user_id, 'project_key', true );
	$first_order_num = count($hourly_orders) - 1;
	$jira_first_order_date = date("Y-m-d", strtotime($hourly_orders_objects[$first_order_num]->date_paid));
	if(empty($hourly_orders)){
		$jira_first_order_date = '';
	}
	return ['start_date' => $jira_first_order_date, 'booked_time' => array_sum($hourly_orders_sum),
	'orders' => $customer_orders];
}

function check_user_orders( $order_id ) {
	$order = wc_get_order( $order_id );
	$user_id = $order->get_user_id();
	foreach ( $order->get_items() as $item_id => $item ){
		$product = $item->get_product();
		if((get_post_meta($product->id, 'hours_field', true) != '')){
			if(get_user_meta($user_id,'jira_orders',true) != 1){
				update_user_meta($user_id,'jira_orders',1);
			}
		}
	}
}
add_action( 'woocommerce_order_status_completed', 'check_user_orders', 10, 1 );

