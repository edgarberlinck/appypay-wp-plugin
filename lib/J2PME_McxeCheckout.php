<?php
/**
 * 
 * Restful MCXE Checkout API client
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

Class J2PME_McxeCheckout {
	
	private $api_endpoint;
	private $test_mode;
	private $acess_token;
	private $expected_http_code;
	
	function __construct($acess_token,$test_mode)
	{
		$this->acess_token      =   $acess_token;
		$this->test_mode        =   $test_mode;
		$this->expected_http_code = 201;
	
		if ($this->test_mode == "yes")
			$this->api_endpoint  = "https://app-appypay-web-dev.azurewebsites.net";
		else
			$this->api_endpoint  = "https://appypay.co.ao";
		
	}
	
	public function createCheckout($args)
	{
		$post_args = array(
			'body'        => $args,
			'timeout'     => '10',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(
				"Content-type" => "application/json",
				"ENV" => ($this->test_mode == "yes" ? "CER":"PRD"),
				"Authorization" => "Token ". $this->acess_token
				),
		);
		
		$response = wp_remote_post($this->api_endpoint, $post_args );
		
		if ( is_wp_error( $response ) ) {
			return array(
				'success'   => false,
				'http_code' => null,
				'response'  => $response,
				'error'     => $response->get_error_messages(),
				'args'      => serialize( $args ),
			);
		} else {
			
			$http_code = wp_remote_retrieve_response_code( $response );
			
			if ( $http_code == $this->expected_http_code ) {
				return array(
					'success'   => true,
					'http_code' => $http_code,
					'response'  => json_decode($response['body']),
					'error'     => null,
					'args'      => serialize( $args ),
				);
			} else {
				return array(
					'success'   => false,
					'http_code' => $http_code,
					'response'  => json_decode($response['body']),
					'error'     => null,
					'args'      => serialize( $args ),
				);
			}
		}
		
	}
}