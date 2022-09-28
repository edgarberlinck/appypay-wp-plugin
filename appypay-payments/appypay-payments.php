<?php

	/**
	 * Plugin Name: Appypay Payments
	 * Plugin URI: https://github.com/edgarberlinck
	 * Description: Plugin de integração ao GPO por WebFrame para WooCommerce by Appypay
	 * Version: 1.0.0
	 * Author E-mail: edgarberlinck@icloud.com
	 * Developer: Edgar Muniz Berlinck
	 * Text Domain: appypay-pagamentos
	 *
	 * WC requires at least: 2.2
	 * WC tested up to: 2.3
	 *
	 * License: GNU General Public License v3.0
	 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
	 */

	 
	/**
	 * Exit if accessed directly.
	 */

	if (!defined('ABSPATH')) {
		exit;
	}

	// Add settings link on plugin page
	function plugin_settings_link($links) { 
		$settings_link = '<a href="admin.php?page=wc-settings&tab=checkout&section=appypay-pagamentos">Settings</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	}

	$plugin = plugin_basename(__FILE__); 
	add_filter("plugin_action_links_$plugin", 'plugin_settings_link' );

	function woocommerce_required_admin_notice() {
		echo   '<div class="updated error notice"><p>';
			echo    _e( '<b>MCXE Woocommerce</b> É necessário instalar o WooCommerce primeiro!', 'my-text-domain' ); 
		echo  '</p></div>';
	}
	

	if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
	{
		add_action( 'admin_notices', 'woocommerce_required_admin_notice' );
	} 
	else
	{
		# initialize your Gateway Class
		add_action( 'plugins_loaded', 'init_gateway' );
		function init_gateway()
		{
			include "payment_gateway.php";
		}


		# add paymetnt method to payment gateway list
		add_filter("woocommerce_payment_gateways","add_payment_gateway");
		function add_payment_gateway($methods){
			$methods[] = 'Payment_Gateway';
			return $methods;
		}

		// add_action( 'rest_api_init', 'register_custom_endpoints' );

		// function register_custom_endpoints(){
		// 	// Registering the payment api callback. It's executed after the payment
		// 	register_rest_route(
		// 			'callback/', // Namespace
		// 			'payment', // Endpoint
		// 			array(
		// 					'methods'  => 'POST',
		// 					'callback' => 'process_payment_response'
		// 			)
		// 	);
			// Legacy from other plugin. Not sure if it's necessary.
			// register_rest_route(
			// 		'verify/', // Namespace
			// 		'payment', // Endpoint
			// 		array(
			// 				'methods'  => 'GET',
			// 				'callback' => 'verify_payment'
			// 		)
			// );
		}
        
		// function verify_payment( $request_data ){
		// 		$parameters = $request_data->get_params();
		// 		$pedido   = $parameters['id'];
		// 		$data = array();
		// 		$data["status"] = $pedido;
		// 		$args = array(
		// 				'return' => 'ids',
		// 				'limit' => 10,
		// 		);
		// 		$orders = wc_get_orders( $args );
		// 		foreach ($orders as $order) {
		// 				$meta = get_post_meta( $order, 'request_id_gpo', true );
		// 				if ($meta == $pedido){
		// 						$data["status"] = get_post_meta( $order, 'request_status_gpo', true );
		// 						return $data;
		// 				}
		// 		}  
		// 		return -1;
				
		// }

		// function process_payment_response( $request_data ) {
		// 		$data = array();
		// 		// TODO: I must process the API return from AppyPay.
		// 		$parameters = $request_data->get_params();
				
		// 		$pedido   = $parameters['merchantReferenceNumber'];
		// 		$estado = $parameters['status'];
		// 		$request_id_gpo = $parameters['id'];
				
		// 		if ( isset($pedido) && isset($estado) ) {
						
		// 			$order_id = $pedido;
		// 			$order = new WC_Order( $order_id );
	
		// 		if($order)
		// 		{
		// 			if($estado == "ACCEPTED"){
		// 				$order->payment_complete($order_id);
		// 			}
		// 			else{
		// 				$order->update_status('failed', 'Pagamento não completado!');
		// 				$order->save();
		// 			}
		// 			delete_post_meta($order_id, 'request_id_gpo');
		// 			delete_post_meta($order_id, 'request_status_gpo');
					
		// 			add_post_meta( $order_id, 'request_id_gpo', $request_id_gpo);
		// 			add_post_meta( $order_id, 'request_status_gpo', $estado);
						
		// 		}else
		// 			logd("Order not found with order id $order_id");
								
		// 		}
						
		// 		return $data;
		// }
	
	// }
?>
