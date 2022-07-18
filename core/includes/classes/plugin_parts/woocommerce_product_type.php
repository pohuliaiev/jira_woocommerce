<?php
function cfwc_create_custom_field() {
	$args = array(
		'id' => 'hours_field',
		'label' => __( 'Hours Field', 'cfwc' ),
		'class' => 'hours_field',
		'desc_tip' => true,
		'type' => 'number',
		'placeholder' => 'Hours Field',
		'description' => __( 'Enter the hours quantity. If it is not hourly order - live this field empty', 'ctwc' ),
	);
	woocommerce_wp_text_input( $args );
}
add_action( 'woocommerce_product_options_general_product_data', 'cfwc_create_custom_field' );

function cfwc_save_custom_field( $post_id ) {
	$product = wc_get_product( $post_id );
	$hours_field = isset( $_POST['hours_field'] ) ? $_POST['hours_field'] : '';
	$product->update_meta_data( 'hours_field', esc_attr( $hours_field ) );
	$product->save();
}
add_action( 'woocommerce_process_product_meta', 'cfwc_save_custom_field' );

