<?php

	/**
	 * Plugin Name: Já-Já Pagamentos - Multicaixa Express 
	 * Plugin URI: https://jaja.yellen-corp.com/
	 * Description: Plugin de integração ao GPO por WebFrame para WooCommerce by Já Já Pagamentos
	 * Version: 1.0.0
	 * Author E-mail: suporte@jaja.yellen-corp.com
	 * Developer: Yellen
	 * Text Domain: jaja-pagamentos
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
	function J2PME_plugin_settings_link($links) { 
		$settings_link = '<a href="admin.php?page=wc-settings&tab=checkout&section=mcxe">Settings</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	}

	$plugin = plugin_basename(__FILE__); 
	add_filter("plugin_action_links_$plugin", 'J2PME_plugin_settings_link' );

	function J2PME_woocommerce_required_admin_notice() {
		echo   '<div class="updated error notice"><p>';
			echo    _e( '<b>MCXE Woocommerce</b> É necessário instalar o WooCommerce primeiro!', 'my-text-domain' ); 
		echo  '</p></div>';
	}
	

	if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	add_action( 'admin_notices', 'J2PME_woocommerce_required_admin_notice' ); } else
	{
		# initialize your Gateway Class
		add_action( 'plugins_loaded', 'J2PME_init' );
		function J2PME_init()
		{
			include "payment_gateway.php";
		}


		# add paymetnt method to payment gateway list
		add_filter("woocommerce_payment_gateways","J2PME_add_mcxe");
		function J2PME_add_mcxe($methods){
			$methods[] = 'J2PME_Gateway_Mcxe';
			return $methods;
		}

		add_action( 'rest_api_init', 'J2PME_callback_url_endpoint' );

        function J2PME_callback_url_endpoint(){
            register_rest_route(
                'callback/', // Namespace
                'mcxe', // Endpoint
                array(
                    'methods'  => 'POST',
                    'callback' => 'J2PME_receive_callback'
                )
            );
            
            register_rest_route(
                'verify/', // Namespace
                'mcxe', // Endpoint
                array(
                    'methods'  => 'GET',
                    'callback' => 'J2PME_verify'
                )
            );
            
            
        }
        
        function J2PME_verify( $request_data ){
            $parameters = $request_data->get_params();
            $pedido   = $parameters['id'];
            $data = array();
            $data["status"] = $pedido;
            $args = array(
                'return' => 'ids',
                'limit' => 10,
            );
            $orders = wc_get_orders( $args );
            foreach ($orders as $order) {
                $meta = get_post_meta( $order, 'request_id_gpo', true );
                if ($meta == $pedido){
                   $data["status"] = get_post_meta( $order, 'request_status_gpo', true );
                   return $data;
                }
            }  
            return -1;
            
        }


        function J2PME_receive_callback( $request_data ) {
            $data = array();
            
            $parameters = $request_data->get_params();
            
            $pedido   = $parameters['merchantReferenceNumber'];
            $estado = $parameters['status'];
            $request_id_gpo = $parameters['id'];
            
            if ( isset($pedido) && isset($estado) ) {
                
               $order_id = $pedido;
        	   $order = new WC_Order( $order_id );
			
        		if($order)
        		{
        			if($estado == "ACCEPTED"){
        			  $order->payment_complete($order_id);
        			}
        			else{
        			  $order->update_status('failed', 'Pagamento não completado!');
        			  $order->save();
        			}
        			delete_post_meta($order_id, 'request_id_gpo');
        			delete_post_meta($order_id, 'request_status_gpo');
        			
        			add_post_meta( $order_id, 'request_id_gpo', $request_id_gpo);
        			add_post_meta( $order_id, 'request_status_gpo', $estado);
        				
        		}else
        			logd("Order not found with order id $order_id");
                    
            }
                
            return $data;
        }
	
	}
?>
