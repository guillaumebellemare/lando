<?php

class CartPaymentMode extends AppModel {
	
	public function __construct() {
		
	}
	
	public function getAll(){
		$payment_modes = array();
		$payment_modes[] = array('payment_modes.id' => 'transit', 		'payment_modes.name_fre' => 'Numéro de transit pour la facturation et autres informations', 			'payment_modes.name_eng' => 'Transit number and other information');
		$payment_modes[] = array('payment_modes.id' => 'entrepreneur', 	'payment_modes.name_fre' => 'Numéro de compte entrepeneur', 'payment_modes.name_eng' => 'Entrepreneur account number');
		$payment_modes[] = array('payment_modes.id' => 'project', 		'payment_modes.name_fre' => 'Numéro de projet', 			'payment_modes.name_eng' => 'Project number');
		$payment_modes[] = array('payment_modes.id' => 'check', 		'payment_modes.name_fre' => 'Chèque', 						'payment_modes.name_eng' => 'Check');
		return $payment_modes;
	}
}