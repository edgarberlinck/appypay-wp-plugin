<?php

require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
require_once(ABSPATH . "wp-admin/includes/class-wp-filesystem-direct.php");

Class J2PME_Gateway_Mcxe extends WC_Payment_Gateway
{   
	private $testmode;
	private $merchant_id;
	private $secret_key;
	
	public function __construct()
	{
		$this->id                   = "mcxe";
		$this->icon                 = "https://pagamentonline.emis.co.ao/online-payment-gateway/portal/gfx/mcexpress.svg";
		$this->has_fields           = false;
		$this->method_title         = "Multicaixa Express";
		$this->method_description   = "Pagamento Online com Multicaixa Express";
		$this->init_form_fields();
		$this->init_settings();
		$this->title            = $this->get_option( 'title' );
		$this->description      = "Ao escolher pagar com Multicaixa Express, você finalizará o seu pagamento com 
		o aplicativo Multicaixa Express presente no seu telefone.";
		$this->acess_token		= $this->get_option( 'acess_token' );
		$this->terminal		= $this->get_option( 'terminal' );
		$this->mobile		= $this->get_option( 'mobile' );
		$this->testmode		= $this->get_option( 'testmode' );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
  }
	
	public function init_form_fields()
	{
		$this->form_fields = include("mcxe-settings.php");		
	}

	public function process_payment($orderId)
	{
		$this->add_checkout_page();
		
		include_once "lib/J2PME_McxeCheckout.php";
		$order = new WC_Order( $orderId );
		$product_list = "";
        $order_item = $order->get_items();

		try {
			$request = new J2PME_McxeCheckout($this->acess_token,$this->testmode);
			$request_data['reference'] = $orderId;
			$request_data['amount'] = "". $this->get_order_total();
			$request_data['pos_id'] = $this->terminal;
			$request_data['card'] = "DISABLED";
			$request_data['mobile'] = $this->get_option( 'mobile' );
			$request_data['callback_url'] = site_url() .'/wp-json/callback/mcxe';
			$redirect_url_success	= site_url()."/mcxe-checkout";
        
			$response = $request->createCheckout(stripcslashes(json_encode($request_data)));
			
			if (isset($response)) {	
			
				if ($response['success']) {
					if ($this->testmode == "yes"){
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
        self::$log->log( $level, $message, array( 'source' => 'MCXE Checkout' ) );
      }
    }

	public function add_checkout_page()
    {
        $page_title = 'mcxe-checkout';
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
                'page_template'  => 'mcxe-checkout.php'
                )
            );
			  $this->move_checkout_file_to_themes_dir();
      }
    }

	public function move_checkout_file_to_themes_dir()
    {
        $checkout_file_path = __DIR__  . "/mcxe-checkout.php";
        $current_themes_path = get_template_directory();

        $filesystem = new WP_Filesystem_Direct(false);

        if ($filesystem->exists($checkout_file_path)) {
            if ($filesystem->copy($checkout_file_path, $current_themes_path . "/mcxe-checkout.php", true)) {
            } else {
                error_log("failed to copy file from " . $checkout_file_path . " to " . $current_themes_path);
            }
        } else {
            error_log("mcxe-checkout.php was not found in plugin directory");
        }
    }

}