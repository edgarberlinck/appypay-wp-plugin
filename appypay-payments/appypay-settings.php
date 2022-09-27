<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
			'enabled' => array(
				'title' => __( 'Enable/Disable', 'woocommerce' ),
				'type' => 'checkbox',
				'label' => __( 'Habilitar Pagamento Online com Multicaixa Express', 'Express' ),
				'default' => 'yes'
			),
			'title' => array(
				'title' => __( 'Title*', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Descrição que o usuário vê durante o checkout.', 'woocommerce' ),
				'default' => __( 'Pagamento com Multixaixa Express', 'woocommerce' ),
				'desc_tip'      => true
			),
			'description' => array(
				'title'       => __( 'Description', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'Descrição que o usuário vê durante o checkout.', 'woocommerce' ),
				'default'     => __( 'Ao escolher pagar com Multicaixa Express, você finalizará o seu pagamento com 
				o aplicativo Multicaixa Express presente no seu telefone.', 'appypay-pagamentos' )
			),
			'application_id' => array(
				'title' => __( 'Application Id*', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Disponibilizado pela Appypay', 'woocommerce' ),
				'desc_tip' => true
			),
			'client_secret' => array(
				'title' => __( 'Appy pay client secret*', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Disponibilizado pela Appypay', 'woocommerce' ),
				'desc_tip'      => true
			),
			'client_id' => array(
				'title' => __('client_id', 'woocomemrce'),
				'type' => 'text',
				'desc_tip' => true,
				'description' => __('Disponibilizado pela Appypay', 'woocommerce'),
				'default' => __('', 'appypay-pagamentos')
			),
			'resource'=> array(
				'title' => __('resource', 'woocomemrce'),
				'type' => 'text',
				'desc_tip' => true,
				'description' => __('Disponibilizado pela Appypay', 'woocommerce'),
				'default' => __('', 'appypay-pagamentos')
			),
			'terminal' => array(
				'title' => __( 'Terminal de pagamento', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Está disponível no portal do GPO', 'woocommerce' ),
				'desc_tip'      => true
			),
			'testmode' => array(
				'title' => __( 'Enable/Disable', 'woocommerce' ),
				'type' => 'checkbox',
				'label' => __( 'Selecionar caso esteja a usar o ambiente de teste', 'Express' ),
				'default' => 'yes'
			),
			'mobile'   => array(
        'title'       => __( 'Mobile', 'woocommerce' ),
        'type'        => 'select',
				'description' => __( 'Autorização ou Compra a um tempo', 'woocommerce' ),
        'options'     => array(
           'AUTHORIZATION' => 'Autorização',
           'PAYMENT'  => 'Pagamento a um tempo'
          )
        )
		);