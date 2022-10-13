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
				'default' => __( 'Pagamento com Multicaixa Express', 'woocommerce' ),
				'desc_tip' => true
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
			'redirect_to' => array(
				'title'       => __( 'Redirect to', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => 'URL que o Widget vai nos redirecionar apoós o pagamento',
				'default' => '/'
			),
			'testmode' => array(
				'title' => __( 'Enable/Disable', 'woocommerce' ),
				'type' => 'checkbox',
				'label' => __( 'Selecionar caso esteja a usar o ambiente de teste', 'Express' ),
				'default' => 'yes'
			),
		);