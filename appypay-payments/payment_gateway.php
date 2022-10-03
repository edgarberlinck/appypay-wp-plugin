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
		$this->title = "Multicaixa Express";
		$this->description = "Pagamento Online com Multicaixa Express";
		$this->redirectTo =  get_site_url() . '/appypay-verify.php'; //$this->get_option('redirect_to');
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
		// Creating basic resources at WP dir.
		$this->move_checkout_file_to_themes_dir();
		$this->move_verify_file_to_themes_dir();

		$order = new WC_Order( $orderId );
		// Appypay widgets variables
		$amoumt = $this->get_order_total();
		$description = "Order id ".$orderId;
		$referenceNumber = $orderId;
		$paymentMethod = $this->application_id;
		$lang = 'pt-PT';
		$redirectURI = $this->redirectTo;

		$product_list = "";
    $order_item = $order->get_items();

		$qs = array(
			'amount' => $amoumt,
			'description' => $description,
			'referenceNumber' => $referenceNumber,
			'paymentMethod' => $paymentMethod,
			'lang' => $lang,
			'redirectURI' => $redirectURI
		);

		return array(
			'result'    => 'success', 
			'redirect'  => site_url() .'/appypay-checkout.php?data='.base64_encode(serialize($qs))
		);

	}

	public function move_checkout_file_to_themes_dir()
	{
		$checkout_file_path = __DIR__  . "/pages/appypay-checkout.php";
		$destination_file =  ABSPATH . "/appypay-checkout.php";
		
		$filesystem = new WP_Filesystem_Direct(false);

		if ($filesystem->exists($checkout_file_path) && !$filesystem->exists($destination_file)) {
			if (!$filesystem->copy($checkout_file_path, ABSPATH. "/appypay-checkout.php", true)) {
				error_log("failed to copy file from " . $checkout_file_path . " to " . $current_themes_path);
			}
		} else {
			error_log("appypay-checkout.php was not found in plugin directory");
		}
	}

	public function move_verify_file_to_themes_dir()
	{
		$verify_file_path = __DIR__  . "/pages/appypay-verify.php";
		$destination_file = ABSPATH . "appypay-verify.php";

		$filesystem = new WP_Filesystem_Direct(false);

		if ($filesystem->exists($verify_file_path) && !$filesystem->exists($destination_file)) {
			if (!$filesystem->copy($verify_file_path, ABSPATH. "/appypay-verify.php", true)) {
				error_log("failed to copy file from " . $verify_file_path . " to " . $current_themes_path);
			}
		} else {
			error_log("appypay-verify.php was not found in plugin directory");
		}
	}

}