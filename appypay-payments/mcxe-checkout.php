<?php
/**
 *
 * Template Name: JAJAMCXECheckout
 */
 ?>
 

 <head>
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <meta charset="utf-8">
     <title>Pagamento com Multicaixa Express</title>
  </head>
<body> 
<?php  if ($_GET['go'] == "0" ) {?>https://kambaexpress.com/wp-admin/plugin-editor.php?file=wc-ja-ja-pagamentos-multicaixa-express1%2Fmcxe-checkout.php&plugin=wc-ja-ja-pagamentos-multicaixa-express1%2Fmcxe-woocommerce.php
   <iframe src="https://cerpagamentonline.emis.co.ao/online-payment-gateway/portal/frame?token=<?php echo $_GET['token']; ?>" width="100%" height="100%" title="Pagamento Multicaixa Express" style="border:none;"></iframe>
<?php } else { ?>
    <iframe src="https://pagamentonline.emis.co.ao/online-payment-gateway/portal/frame?token=<?php echo $_GET['token']; ?>" width="100%" height="100%" title="Pagamento Multicaixa Express" style="border:none;"></iframe>
<?php } ?>

</body> 



<script>
    window.addEventListener('message', receiveMessage, false);
    function receiveMessage(event) {
        console.log('Received from GPO: ', event.data);
        console.log('Received from GPO: ', event.origin);
        var destin = "https://yellen-api.herokuapp.com/v1/mcxe_verify/".concat(event.data);
        console.log(destin);
        if (!event.origin.includes('emis.co.ao')) { return; }
        fetch(destin).then(function (response) {
                return response.json();
            }).then(function (myJson) {
                var userid = myJson;
                if (myJson.status == "ACCEPTED"){
                    redirect_to=window.location.origin+"/compra_express-sucesso/";
                }else{
                    redirect_to=window.location.origin+"/compra_express-inssucesso/";
                }
                
                window.location.replace(redirect_to);
         });
    }
</script>
