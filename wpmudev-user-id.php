<?php
/** 
* Plugin Name:       	WPMU DEV GENERATE CUSTOMER ID
* Text Domain:         wpmudev-user-id
* 
**/


//Generate customer_id when a user is registered.
function generate_customer_id($user_id)
{
	$customer_id = get_user_meta($user_id, "customer_id", true);
	
	if(empty($customer_id))
	{
		global $wpdb;
		
		//this code generates a string 10 characters long of numbers and letters
		while(empty($customer_id))
		{
			$scramble = md5(AUTH_KEY . current_time('timestamp') . $user_id . SECURE_AUTH_KEY);
			$customer_id = substr($scramble, 0, 10);
			$check = $wpdb->get_var("SELECT meta_value FROM $wpdb->usermeta WHERE meta_value = '" . esc_sql($customer_id) . "' LIMIT 1");
			if($check || is_numeric($customer_id))
				$customer_id = NULL;
		}
		
		//save to user meta
		update_user_meta($user_id, "customer_id", $customer_id);
		
		return $customer_id;
	}
}
add_action('user_register', 'generate_customer_id');



// Shortcode that displays the Customer ID
function customer_id_shortcode() {
    add_shortcode( 'customer-id', 'generate_customer_id' );
}

add_action( 'init', 'customer_id_shortcode' );
