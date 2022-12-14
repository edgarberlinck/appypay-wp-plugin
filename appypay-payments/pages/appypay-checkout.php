<?php
/**
 *
 * Template Name: AppyPayCheckout
 */
 ?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title>Pagamento com Multicaixa Express</title>
</head>
<body> 
  <?php 
    $b64 = $_GET['data'];
    $data = unserialize(base64_decode($b64));
    
    if ($data['testmode'] == 'yes') {
      $widgetUri = 'https://app-appypay-web-dev.azurewebsites.net/assets/chargesWidgetV1_2/main.js';
    } else {
      $widgetUri = 'https://appypay.co.ao/assets/chargesWidgetV1_2/main.js';
    }
  ?>
  <div id="appyPay-charges-v2"></div>
</body> 

<script async 
  id="appyPay-charges-widget-v2" 
  amount="<?=$data['amount']?>" 
  paymentDescription="<?=$data['description']?>" 
  referenceNumber="<?=$data['referenceNumber']?>" 
  paymentMethod="<?=$data['paymentMethod']?>" 
  requestType="sync"
  lang="<?=$data['lang']?>"
  src="<?=$widgetUri?>" 
  redirectURI="<?=$data['redirectURI']?>"
></script>