<?php

	/**
	 * Plugin Name: Appypay Payments
	 * Plugin URI: https://github.com/edgarberlinck
	 * Description: Plugin de integração ao GPO por WebFrame para WooCommerce by Appypay
	 * Version: 1.0.2
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
		$settings_link = '<a href="admin.php?page=wc-settings&tab=checkout&section=appypay">Settings</a>'; 
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

		# registering the webhook actions 
		function add_appypay_response_handler ($topic_hooks) {
			$new_hooks = array(
				'order.appypay_response' => array(
					'appypay_response_handler',
				),
			);
			
			return array_merge( $topic_hooks, $new_hooks );
		}
		add_filter( 'woocommerce_webhook_topic_hooks', 'add_appypay_response_handler' );

		function add_appypay_response_events( $topic_events ) {
			$new_events = array('appypay_response');
		
			return array_merge( $topic_events, $new_events );
		}
		add_filter( 'woocommerce_valid_webhook_events', 'add_appypay_response_events' );

		function add_new_appypay_webhook_topics( $topics ) {
			$new_topics = array( 
				'order.appypay_response' => __( 'Appypay Response', 'woocommerce' ),
				);
		
			return array_merge( $topics, $new_topics );
		}
		add_filter( 'woocommerce_webhook_topics', 'add_new_appypay_webhook_topics' );

		# Register a route to your webhook
		# TODO: Protect the route https://medium.com/@GemmaBlack/authenticating-your-wordpress-rest-apis-84d8775a6f87
		# The route authentication could be 'basic' or 'API Key'
		add_action('rest_api_init', function () {
			register_rest_route( 'webhook/v1', 'handle-response',array(
				'methods'  => 'POST',
				'callback' => 'handle_appypay_request'
			));
		});

		function handle_appypay_request($request) {
			$orderId = $request['merchantTransactionId'];
			$status = $request['responseStatus']['successful'];
			$message = $request['responseStatus']['message'];
			
			$order_factory = new WC_Order_Factory();
			$order = $order_factory->get_order($orderId);
			# TODO: Check if the orders actualy existis. Don't trust appypay inputs
			if ($status) {
				$order->payment_complete();
			} else {
				$order->set_status('failed', $message);
			}
			$order->save();
			printf($order->get_status());
		}

		# add paymetnt method to payment gateway list
		add_filter("woocommerce_payment_gateways","add_payment_gateway");
		function add_payment_gateway($methods){
			$methods[] = 'Payment_Gateway';
			return $methods;
		}
	}
?>
