<?php
/**
 * 
 * Restful MCXE Checkout API client
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

Class AppyPayApi {
	
	private $api_endpoint;
	private $test_mode;
	private $client_id;
	private $client_secret;
	private $resource;
	private $expected_http_code;
	private $expected_auth_http_code;
	private $api_version;

	function __construct($client_id, $client_secret, $resource, $test_mode)
	{
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
		$this->resource = $resource;
		$this->test_mode =  $test_mode;
		$this->expected_http_code = 201;
		$this->expected_auth_http_code = 200;
		$this->api_version = '1.2';
		
		$this->auth_endpoint = 'https://login.microsoftonline.com/appypaydev.onmicrosoft.com/oauth2/token';

		if ($this->test_mode == "yes")
			$this->api_endpoint  = "https://app-appypay-api-tst.azurewebsites.net";
		else
			$this->api_endpoint  = "https://appypay.co.ao";
		
	}
	
	public function get_api_token() 
	{
		$request_data['client_id'] = $this->client_id;
		$request_data['client_secret'] = $this->client_secret;
		$request_data['resource'] = $this->resource;
		$request_data['grant_type'] = 'client_credentials';

		$post_args = array(
			'body' => json_encode($request_data),
			'timeout'     => '10',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(
				"Content-type" => "application/json"
			)
		);

		$response = wp_remote_post($this->auth_endpoint, $post_args);

		if (is_wp_error($response)) {
			return array(
				'success' => false,
				'http_code' => null,
				'access_token' => null,
				'error' => $response->get_error_messages(),
				'args' => serialize( $args ),
			);	
		} else {
			$http_code = wp_remote_retrieve_response_code($response);

			if ($http_code == $this->expected_auth_http_code) {
				return array(
					'success' => true,
					'status_code' => $http_code,
					'access_token' => $response['access_token'],
					'error' => null,
					'args' => serialize($request_data)
				);
			} else {
				return array(
					'success' => false,
					'http_code' => $http_code,
					'access_token'  => null,
					'error' => null,
					'args' => serialize($request_data),
				);
			} 
		}
	}

	public function createCheckout($args)
	{
		$auth_response = this->get_api_token();

		if ($auth_response['success']) {
			// TODO: enviar request para pagamento
			
		} else {
			return array(
				'success' => false,
				'http_code' => $auth_response['status_code'],
				'response' => null,
				'error' => $auth_response['error'],
				'args' => serialize($args),
			);
		}

		// $post_args = array(
		// 	'body'        => $args,
		// 	'timeout'     => '10',
		// 	'redirection' => '5',
		// 	'httpversion' => '1.0',
		// 	'blocking'    => true,
		// 	'headers'     => array(
		// 		"Content-type" => "application/json",
		// 		"ENV" => ($this->test_mode == "yes" ? "CER":"PRD"),
		// 		"Authorization" => "Token ". $this->acess_token
		// 		),
		// );
		
		// $response = wp_remote_post($this->api_endpoint, $post_args );
		
		// if ( is_wp_error( $response ) ) {
		// 	return array(
		// 		'success'   => false,
		// 		'http_code' => null,
		// 		'response'  => $response,
		// 		'error'     => $response->get_error_messages(),
		// 		'args'      => serialize( $args ),
		// 	);
		// } else {
			
		// 	$http_code = wp_remote_retrieve_response_code( $response );
			
		// 	if ( $http_code == $this->expected_http_code ) {
		// 		return array(
		// 			'success'   => true,
		// 			'http_code' => $http_code,
		// 			'response'  => json_decode($response['body']),
		// 			'error'     => null,
		// 			'args'      => serialize( $args ),
		// 		);
		// 	} else {
		// 		return array(
		// 			'success'   => false,
		// 			'http_code' => $http_code,
		// 			'response'  => json_decode($response['body']),
		// 			'error'     => null,
		// 			'args'      => serialize( $args ),
		// 		);
		// 	}
		// }
		
	}
}