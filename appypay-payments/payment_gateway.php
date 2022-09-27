<?php

require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
require_once(ABSPATH . "wp-admin/includes/class-wp-filesystem-direct.php");

Class Payment_Gateway extends WC_Payment_Gateway
{   
	private $testmode;
	private $merchant_id;
	private $secret_key;
	private $application_id;
	private $client_id;
	private $client_secret;
	private $resource;

	public function __construct()
	{
		$this->id = "mcxe";
		$this->icon = "https://pagamentonline.emis.co.ao/online-payment-gateway/portal/gfx/mcexpress.svg";
		$this->has_fields = false;
		$this->method_title = "Multicaixa Express";
		$this->method_description = "Pagamento Online com Multicaixa Express";
		$this->init_form_fields();
		$this->init_settings();
		$this->title = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->application_id = $this->get_option('application_id');
		$this->client_secret = $this->get_option( 'client_secret' );
		$this->client_id = $this->get_option( 'client_id' );
		$this->resource	= $this->get_option( 'resource' );
		$this->terminal	= $this->get_option( 'terminal' );
		$this->mobile	= $this->get_option( 'mobile' );
		$this->testmode	= $this->get_option( 'testmode' );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
  }
	
	public function init_form_fields()
	{
		$this->form_fields = include("appypay-settings.php");
	}

	public function process_payment($orderId)
	{
		$this->add_checkout_page();
		
		include_once "lib/AppyPayApi.php";
		$order = new WC_Order( $orderId );
		$product_list = "";
        $order_item = $order->get_items();

		try {
			$request = new AppyPayApi($this->client_id, $this->client_secret, $this->resource, $this->testmode);

			$request_data['capture'] = true;
			$request_data['merchantTransactionId'] = $orderId;
			$request_data['amount'] = "". $this->get_order_total();
			$request_data['orderOrigin'] = 0;
			$request_data['paymentMethod'] = $this->application_id;
			$request_data['description'] = 'I need to check with Carles what should be this description';
			$request_data['paymentInfo'] = array(
				'phoneNumber' => '111 473 100' // TODO: Need to find out where is the user phone number. 
			);
			
			$request_data['callback_url'] = site_url() .'/wp-json/callback/payment';
			$redirect_url_success	= site_url()."/appypay-checkout";
        
			$response = $request->createCheckout(stripcslashes(json_encode($request_data)));
			
			if (isset($response)) {	
			
				if ($response['success']) {
					if ($this->testmode == "yes"){
						// TODO: What the
        	  $url = $redirect_url_success . "?token=" . $response['response']->id . "&go=0";
					}else{
						$url = $redirect_url_success . "?token=" . $response['response']->id . "&go=1";
					}
					return array(
						'result'    => 'success', 
						'redirect'  => $url
					);
				}else{
					$json = array(
					"result"    =>  "failure",
					"messages"  =>  "<ul class=\"woocommerce-error\">\n\t\t\t<li>Houve uma problemas para avançar com a compra via Multicaixa Express. Contacte o suporte da site.</li>\n\t</ul>\n",
					"refresh"   =>  "false",
					"reload"    =>  "false"
					);
					die(json_encode($json));		
				}				
			}
		} catch (Exception $e) {
			$json = array(
				"result"    =>  "failure",
				"messages"  =>  "<ul class=\"woocommerce-error\">\n\t\t\t<li>".$e->getMessage()."</li>\n\t</ul>\n",
				"refresh"   =>  "false",
				"reload"    =>  "false"
				);
			die(json_encode($json));
		}
	}

	public static function log( $message, $level = 'info' ) {
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ) {self::$log = wc_get_logger(); }
			self::$log->log( $level, $message, array( 'source' => 'Appypay Pagamentos' ) );
		}
	}

	public function add_checkout_page()
	{
		$page_title = 'appypay-checkout';
		$checkout_page = get_page_by_title($page_title, 'OBJECT', 'page');

		if (empty($checkout_page)) {
			wp_insert_post(
				array(
					'comment_status' => 'close',
					'post_author'    => 1,
					'post_title'     => $page_title,
					'post_name'      => strtolower(str_replace(' ', '-', trim($page_title))),
					'post_status'    => 'publish',
					'post_content'   => '',
					'post_type'      => 'page',
					'page_template'  => 'appypay-checkout.php'
				)
			);
			$this->move_checkout_file_to_themes_dir();
		}
	}

	public function move_checkout_file_to_themes_dir()
	{
		$checkout_file_path = __DIR__  . "/appypay-checkout.php";
		$current_themes_path = get_template_directory();

		$filesystem = new WP_Filesystem_Direct(false);

		if ($filesystem->exists($checkout_file_path)) {
			if ($filesystem->copy($checkout_file_path, $current_themes_path . "/appypay-checkout.php", true)) {
			} else {
					error_log("failed to copy file from " . $checkout_file_path . " to " . $current_themes_path);
			}
		} else {
			error_log("mcxe-checkout.php was not found in plugin directory");
		}
	}

}