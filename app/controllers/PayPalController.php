<?php

class PayPalController extends AppController {


	function paypal_redirect(){
		global $db, $lang2, $lang3, $membership, $routes;
		if(isset($_SESSION['membership']['transaction_id'])){
			
			// Create paypal redirect
			include("app/helpers/paypal/pp-class.php");
			
			// Get transaction ID
			//$transaction = new Transaction();
			//$record_transaction = $transaction->get($_SESSION['transaction']['id']);
			
			// Initiate an instance of the class
			$p = new paypal_class();
			
			# Paypal informations are stored in config.php.
			
			// Paypal url
			$p->paypal_url = PAYPAL_URL;   
			$this->waiting_message = $membership["paypal.waiting_message"];
			$this->click_label = $membership["paypal.click_label"];
			
			// Add fields
			$p->add_field('business', PAYPAL_ACCOUNT);
			$p->add_field('return', 'http://' . $_SERVER['HTTP_HOST'] . URL_ROOT.$lang2.'/'.$routes['pp_back']);
			$p->add_field('cancel_return', 'http://' . $_SERVER['HTTP_HOST'] . URL_ROOT.$lang2.'/'.$routes['pp_cancel_complete'] . '/' . $_SESSION['transaction']['id']);
			$p->add_field('notify_url', 'http://' . $_SERVER['HTTP_HOST'] . URL_ROOT.$lang2.'/'.$routes['pp_paypal_ipn']);
			$p->add_field('upload', '1');
			$p->add_field('cmd', '_cart');
			$p->add_field('currency_code', 'CAD');
			$p->add_field('no_shipping', '1');
			$p->add_field('lc', 'en');
			$p->add_field('custom', $record_transaction['transactions.id']);
			$p->add_field('charset', 'utf-8');
			$p->add_field('item_name_1', $record_transaction['transactions.name']);
			$p->add_field('amount_1', $record_transaction['transactions.amount']);
			$paypal_info = $p->submit_paypal_post();
			
			unset($_SESSION['membership']);
			return array("paypal_info" => $paypal_info);
			
		}else $this->redirect('membership');
	}
	
	
	function back(){
		global $db, $lang3;
		if(isset($_POST['custom'])){
			$transaction = new Transaction();
			$fournisseur = new Fournisseur();
			
			$record_transaction = $transaction->get($_POST['custom']);
			$record_fournisseur = $fournisseur->get($record_transaction['transactionadhesions.fournisseur_id']);
			return array("name" => $record_fournisseur["fournisseurs.name"]);
		}else{
			return array("name" => '');
		}
		
	}
	
	
	function cancel(){
		global $db, $lang3;
		if(isset($_GET['param2'])){
			$transaction = new Transaction();
			$fournisseur = new Fournisseur();
			$record_transaction = $transaction->get($_GET['param2']);
			$record_fournisseur = $fournisseur->get($record_transaction['transactionadhesions.fournisseur_id']);
			//if the provider is new, we delete it. 
			if(empty($record_fournisseur['special.date_end'])){
				$fournisseur->delete($record_transaction['transactionadhesions.fournisseur_id']);
			}
			$this->redirect('membership_cancel_complete');
		}

	}
	
	
	function paypal_ipn(){
		
		global $db, $lang3;
		
		// Create paypal ipn
		include("app/helpers/paypal/pp-class.php");
		
		// Initiate an instance of the class
		$p = new paypal_class();
		
		# Paypal informations are stored in config.php.
		// Paypal url
		$p->paypal_url = PAYPAL_URL; 
		$transaction = new TransactionAdhesion();
		if ($this->is_test($p, PAYPAL_ACCOUNT) || $p->validate_ipn() ) {
			if($this->is_secure_transaction($p, PAYPAL_ACCOUNT, $transaction) === true){
				$data = array('txn_id' => $p->ipn_data['txn_id'], 'paid' => 1, 'status' => 1);
				$transaction->update($data, $p->ipn_data['custom']);
				
				$record_transaction = $transaction->get($p->ipn_data['custom']);
					
				//activate and set the right date 
				$fournisseur = new Fournisseur();
				if($record_transaction['rates.slug'] == 'renouvellement-fournisseur'){
					$date_range = explode(',', $record_transaction['fournisseurs.date_activation']);
					$date_start = new DateTime($date_range[1]);
					$date_end = new DateTime($date_start->format('Y-m-d') . " +1 year");
					$record_fournisseur['date_activation'] = $date_range[0].','.$date_end->format('Y-m-d');
					$record_fournisseur['active'] = 1;
				}else if($record_transaction['rates.slug'] == 'adhesion-fournisseur'){
					$date_start = new DateTime();
					$date_end = new DateTime($date_start->format('Y-m-d') . " +1 year");
					$record_fournisseur['date_activation'] = $date_start->format('Y-m-d').','.$date_end->format('Y-m-d');
					$record_fournisseur['active'] = 1;
				}
				
				$fournisseur->update($record_fournisseur, $record_transaction['fournisseurs.id']);
				$this->notify_admin($fournisseur->get($record_transaction['fournisseurs.id']), $fournisseur, $record_transaction['rates.slug']);
			}
		}else{
		
		}
	}	
	
	
	function is_secure_transaction($p, $paypal_account, $transaction){
		$rsTransaction = array('txn_id' => $p->ipn_data['txn_id'], 'updated' => date("Y-m-d H:i:s"));
		if($p->ipn_data['receiver_email'] == $paypal_account){
			
			# Transaction has not been already processed
			$transactionExists = $transaction->checkIfTransactionExist($p->ipn_data['txn_id']);
			if($transactionExists['txn_id_exists'] == 0){
			
			# Transaction has been completed
				if($p->ipn_data['payment_status'] == 'Completed'){
					
					# Transaction mc_gross is corresponding with the total we registered earlier in our database.
					$record_transaction = $transaction->get($p->ipn_data['custom']);
	
					if($p->ipn_data['mc_gross'] == $record_transaction['transactionadhesions.amount']){
						return true;
					} else $rsTransaction['message'] = "Transaction amount is not correct. ({$p->ipn_data['mc_gross']} <> {$record_transaction['transactionadhesions.amount']})";
				}else{
					$rsTransaction['message'] = "Transaction was not completed.";
					$rsTransaction['status'] = 3;
				}
			}else{
				$rsTransaction['message'] = "Transaction was already processed.";
			}
		}else{
			$rsTransaction['message'] = "Email doesn't match. ($paypal_account)";
		}
	
		$transaction->update($rsTransaction, $p->ipn_data['custom']);
		return $rsTransaction['message'];
	}
	
	
	function is_test(&$p, $paypal_account){
		if(isset($_GET['param2']) && isset($_GET['param3'])){
			$p->ipn_data['custom'] = $_GET['param2'];
			$p->ipn_data['txn_id'] = $_GET['param3'];
			$p->ipn_data['mc_gross'] = '500';
			$p->ipn_data['payment_status'] = 'Completed';
			$p->ipn_data['receiver_email'] = $paypal_account;
			return true;
		}
	}
	
}
