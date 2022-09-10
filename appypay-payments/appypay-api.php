<?php
class Appypay_Api
{
  private $client_id;
  private $client_secret;

  public function __construct($client_secret, $client_id)
  {
    $this->client_secret = $client_secret;
    $this->client_id = $client_id;
  }

  public function get_access_token()
  {
    $result["access_token"] = 'fake';

    return $result;
  }
}