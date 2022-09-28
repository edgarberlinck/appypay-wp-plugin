<?php

require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
require_once(ABSPATH . "wp-admin/includes/class-wp-filesystem-direct.php");

Class Payment_Gateway extends WC_Payment_Gateway
{   
	private $testmode;
	private $application_id;
	private $redirectTo;

	public function __construct()
	{
		$this->id = "appypay";
		$this->icon = "https://pagamentonline.emis.co.ao/online-payment-gateway/portal/gfx/mcexpress.svg";
		$this->has_fields = false;
		$this->method_title = "Multicaixa Express";
		$this->method_description = "Pagamento Online com Multicaixa Express";
		$this->init_form_fields();
		$this->init_settings();
		$this->redirectTo = $this->get_option('redirect_to');
		$this->application_id = 'GPO_5D7A07BC-24EB-4CC7-8218-109E61C62093'; //$this->get_option('application_id');
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
		$order = new WC_Order( $orderId );
		// Appypay widgets variables
		$amoumt = $this->get_order_total();
		$description = "Order id ".$orderId;
		$referenceNumber = $orderId;
		$paymentMethod = $this->application_id;
		$lang = 'pt';
		$redirectURI = $this->redirectTo;

		$product_list = "";
    $order_item = $order->get_items();

		$qs = array(
			'amount' => $amoumt,
			'description' => $description,
			'referenceNumber' => $referenceNumber,
			'paymentMethod' => $paymentMethod,
			'lang' => $lang,
			'redirectURI' => $redirectTo
		);

		return array(
			'result'    => 'success', 
			'redirect'  => site_url() .'/appypay-checkout.php?data='.base64_encode(serialize($qs))
		);

	}

	public static function log( $message, $level = 'info' ) {
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ) {self::$log = wc_get_logger(); }
			self::$log->log( $level, $message, array( 'source' => 'Appypay Pagamentos' ) );
		}
	}

	// This is not working properly.
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
		$checkout_file_path = __DIR__  . "/pages/appypay-checkout.php";
		$current_themes_path = get_template_directory();

		$filesystem = new WP_Filesystem_Direct(false);

		if ($filesystem->exists($checkout_file_path)) {
			if ($filesystem->copy($checkout_file_path, $current_themes_path . "/appypay-checkout.php", true)) {
			} else {
					error_log("failed to copy file from " . $checkout_file_path . " to " . $current_themes_path);
			}
		} else {
			error_log("appypay-checkout.php was not found in plugin directory");
		}
	}

}