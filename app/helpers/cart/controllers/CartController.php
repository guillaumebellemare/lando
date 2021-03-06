<?php
require COMPLETE_URL_ROOT . 'app/helpers/paypal/bootstrap.php';

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\ExecutePayment;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\InputFields;

class CartController extends AppController {

	private $customer_unique_id = NULL;
	
	function index() {
		ob_start();
		//$this->_clean_sessions();
		$this->_init_cart();
		$this->_calculate_cart();
		
		$current_item_zero = 1;
		$shipping_rates = NULL;

		foreach($_SESSION['cart']['items'] as &$current_item)
		{
			# Validate that item quantity is not zero
			if($current_item["qty"]["qty"]<=0)
			{
				$current_item["qty"]["qty"] = 1;
				$current_item_zero = 0;
			}
			if(!$current_item_zero) $this->redirect('cart');
			
			# Validate that item quantity doesn't exceed possibilities
			$item = new CartProduct();
			//echo  $current_item["data"]["id"];
			$current_item_qty = $item->getCurrentProductQtys($current_item["data"]["products.id"], $current_item["qty"]["qty"], $current_item["option_id"]);

			if($current_item["qty"]["qty"] > $current_item_qty)
			{
				$current_item["qty"]["qty"] = $current_item_qty;
				$this->redirect('cart');
			}

		}

		if(isset($_POST['checkout_billing-address'])){
			$this->redirect('checkout_login');
		}else if(isset($_POST['empty_cart'])){
			$this->_empty_cart();
		}
		$item = new CartProduct();

		$item->createSlugField("name_$this->lang3");
		$cart_total_weight = $item->getCartTotalWeight($_SESSION['cart']['items']);

		$shipping_rates = NULL;
		$shipping_rate = new CartShipping();
		if(isset($_POST['action']) && $_POST['action']=='estimate' && isset($_POST['zip_code_estimate']))
		{
			$_SESSION['cart']["zip_code_estimate"] = str_replace(' ', '', strtoupper($_POST['zip_code_estimate']));
			$_SESSION['cart']["shipping_rates_estimate"] = strtoupper($_POST['shipping_rates_select']);
			$this->redirect("cart");
		}

		if($_SESSION['cart']["zip_code_estimate"]) $shipping_rates = $shipping_rate->getShippingRates($_SESSION['cart']["zip_code_estimate"], $cart_total_weight);
		
		return array('cart_session' => $_SESSION['cart'], 'lang3_trans' => $this->lang3_trans, 'shipping_rates' => $shipping_rates);
	}
	
	function estimate_shipping_rates(){
		$this->_init_cart();
		if($this->is_ajax()){
		
			$item = new CartProduct();
			$item->createSlugField("name_$this->lang3");
			$cart_total_weight = $item->getCartTotalWeight($_SESSION['cart']['items']);
			
			$shipping_rates = NULL;
			$shipping_rate = new CartShipping();
			
			if(isset($_POST['action']) && $_POST['action']=='estimate' && isset($_POST['zip_code_estimate']))
			{
				$_SESSION['cart']["zip_code_estimate"] = str_replace(' ', '', strtoupper($_POST['zip_code_estimate']));
				$_SESSION['cart']["shipping_rates_estimate"] = strtoupper($_POST['shipping_rates_select']);				
			}
	
			if($_SESSION['cart']["zip_code_estimate"]) $shipping_rates = $shipping_rate->getShippingRates($_SESSION['cart']["zip_code_estimate"], $cart_total_weight);
			return array('cart_session' => $_SESSION['cart'], 'lang3_trans' => $this->lang3_trans, 'shipping_rates' => $shipping_rates, 'estimate' => $_SESSION['cart']["shipping_rates_estimate"]);
		}else{
			$this->redirect("cart");	
		}
	}
	
	function add(){
		$this->_init_cart();
		if($this->is_ajax()){
			$item_session_key = null;
			$qty = isset($_POST['qty']) ? $_POST['qty'] : 1;
			if($this->_item_exists_in_cart($_POST['id'], $item_session_key, $qty, $_POST['option'])){
				$msg = $this->_update_quantity($item_session_key, array('qty' => $qty));
				
			}else{
				$msg = $this->_add_item();
			}
			
			$this->_calculate_cart();
			
			
			return array("msg" => $msg, "item_count" => count($_SESSION['cart']['items']));
		}else{
			$this->redirect("index");	
		}
	}
	
	function update(){
		$this->_init_cart();
		if($this->is_ajax()){
			$item_session_key = key($_POST['items']);
			$out = $this->_update_quantity($item_session_key, $_POST['items'][$item_session_key]);
			echo '<div id="content">' . $out . '</div>';
			exit;			
		}else{
			$this->redirect("index");	
		}
	}
	
	function remove(){
		$this->_init_cart();
		if($this->is_ajax()){
			$item_session_key = ($_POST['id']);
			unset($_SESSION['cart']['items'][$item_session_key]);
			echo '<div><div id="content">' . 'success' . '</div><div id="item_count">'.count($_SESSION['cart']['items']).'</div></div>';
			exit;			
		}else{
			$this->redirect("index");	
		}
	}
	
	function login() {
		global $cart;
		$this->_init_cart();
		#if(!$this->_check_cart()) $this->redirect('cart');
		
		if(isset($_SESSION['customer']['logged_in'])) $this->redirect('checkout_billing-address');
		if(isset($_POST['connection'])){
			$customer = new CartCustomer();
			$province = new CartProvince();
			
			# Try to connect
			$current_customer_id = $customer->authenticate($_POST['email'], $_POST['password']);
			if(!$current_customer_id){
				$_SESSION['errors'][] = $cart["invalid_login.error"];
			}else{
				$current_customer = $customer->get($current_customer_id);
				$_SESSION['customer'] = array();
				foreach($current_customer as $key => $value){
					list($table, $field) = explode('.', $key);
					$_SESSION['customer'][$field] = $value;
				}
				
				# Other fields 
				$_SESSION['customer']['complete_name'] = $current_customer['customers.first_name']." ".$current_customer['customers.last_name'];
				$_SESSION['customer']['shipping_email'] = $current_customer['customers.email'];
				$_SESSION['customer']['payment_email'] = $current_customer['customers.email'];
				$_SESSION['customer']['logged_in'] = 1;
				$_SESSION['customer']['user_id'] = $current_customer['customers.id'];
				$_SESSION['customer']['shipping_phone'] = $current_customer['customers.phone'];
				$_SESSION['customer']['payment_phone'] = $current_customer['customers.phone'];
				$_SESSION['customer']['payment_zip'] = $current_customer['customers.payment_postcode'];
				$_SESSION['customer']['shipping_zip'] = $current_customer['customers.shipping_postcode'];
				$_SESSION['customer']['shipping_province_id'] = $province->getIdFromName($current_customer['customers.shipping_province']);
				$_SESSION['customer']['payment_province_id'] = $province->getIdFromName($current_customer['customers.payment_province']);
				
				if(!$this->_check_cart())
				{
					$this->redirect('user_profile');
				}else{
					$_SESSION['messages'][] = $cart["thank_you_user"];
					$this->redirect('checkout_billing-address');
				}
			}
		}
	}
		
	function logout() {
		global $messages, $global;
		
		unset($_SESSION['customer']);
		$messages[] = $global['disconnected'];
		$this->redirect('checkout_login');
		
		return true;
	}	

	function billing_address() {
		
		$this->_init_cart();
		if(!$this->_check_cart()) $this->redirect('cart');

		# Required fields
		$required_list = array(
			"payment_first_name",
			"payment_last_name",
			"email",
			"phone",
			"payment_address_1",
			"payment_city",
			"payment_zip",
			"payment_province_id",
			"payment_mode_id",
		);
		
		# Fields to save
		$save_list = array(
			"payment_address_2",
			"payment_country",
			"payment_company",
		);
		
		$province = new CartProvince();
		$provinces = $province->select('provinces')->order_by("rank ASC")->group_by("name_$this->lang3")->all();
		
		$payment_mode = new CartPaymentMode();
		$payment_modes = $payment_mode->getAll();
		$this->_init_customer($required_list, $save_list);

		if(isset($_POST['continue'])){
			
			$completed = $this->_saveAndValidateSession($required_list, $save_list, 'customer');
			
			# Set province as a readable string
			if(isset($_SESSION['customer']['payment_province_id'])){
				foreach($provinces as $province){
					if($province['provinces.id'] == $_SESSION['customer']['payment_province_id']){
						$_SESSION['customer']['payment_province'] = $province['provinces.name_'.$this->lang3];
						break;
					}
				}
			}
			
			
			if($completed) $this->redirect('checkout_shipping-address');
		}
		return array('provinces' => $provinces, 'payment_modes' => $payment_modes);
	}
	
	function shipping_address() {
		
		if(!$_SESSION['customer']['payment_first_name'] || !$_SESSION['customer']['payment_last_name'] || !$_SESSION['customer']['payment_address_1'] || !$_SESSION['customer']['payment_city'] || !$_SESSION['customer']['payment_zip'] || !$_SESSION['customer']['payment_province']) $this->redirect("checkout_billing-address");

		global $routes, $cart;

		$this->_init_cart();
		if(!$this->_check_cart()) $this->redirect('cart');
		
		# Required fields
		$required_list = array(
			"shipping_first_name",
			"shipping_last_name",
			"shipping_address",
			"shipping_city",
			"shipping_zip",
			"shipping_province_id",
			"shipping_address_1",
		);
		# Fields to save
		$save_list = array(
			"shipping_address_2",
			"shipping_country",
			"sameAsBillingInfo",
			"shipping_company",
		);


		$this->_init_customer($required_list, $save_list);
		$customer = new CartCustomer();
		$province = new CartProvince();
		$provinces = $province->select('provinces')->order_by("rank ASC")->group_by("name_$this->lang3")->all();

		if(isset($_POST['continue'])) {
			
			if(isset($_POST['sameAsBillingInfo'])){
				$_POST['sameAsBillingInfo'] = true;
				//no validation is really required, just fill in.
				$tofill = array_merge($required_list, $save_list);
				foreach($tofill as $key){
					if(strstr($key, 'shipping_') && isset($_SESSION['customer'][str_replace('shipping_', 'payment_', $key)])) $_POST[$key] = $_SESSION['customer'][str_replace('shipping_', 'payment_', $key)];
				}
				$completed = $this->_saveAndValidateSession($required_list, $save_list, 'customer');
			}else {
				$_POST['sameAsBillingInfo'] = false;
				$completed = $this->_saveAndValidateSession($required_list, $save_list, 'customer');
			}

			# Validate
			$completed = $this->_saveAndValidateSession($required_list, $save_list, 'customer');
			
			# Set province as a readable string
			if(isset($_SESSION['customer']['shipping_province_id'])){
				foreach($provinces as $province){
					if($province['provinces.id'] == $_SESSION['customer']['shipping_province_id']){
						$_SESSION['customer']['shipping_province'] = $province['provinces.name_'.$this->lang3];
						break;
					}
				}
			}
			
			if($completed){
				$valid_email = $customer->validate_email($_POST['shipping_email'],  $_SESSION['customer']['id']);
				if( $valid_email === true){
					$this->redirect('checkout_order-confirmation');
				}else{
					$_SESSION['errors'][] = sprintf($cart["email_already_used.error"], (URL_ROOT . $this->lang2 . "/" . $routes["checkout_login"]));
				}
			} 
					
		}
		return array('provinces' => $provinces);
	}
	
	function guest() {
		
	}
	
	# C'est ici que ça devient pas mal différent
	function order_confirmation() {
		
		global $cart, $login;
		
		/*$_SESSION['cart']["calculated_shipping_rate"] = NULL;
		$_SESSION['cart']["calculated_shipping_name"] = NULL;
		$_SESSION['cart']["calculated_shipping_expected_delivery_date"] = NULL;*/
					
		$this->_init_cart();
		$this->_calculate_cart();
		if(!$this->_check_cart()) $this->redirect('cart');

		# Required fields
		$required_list = array(
			"password",
			"password_confirm"
		);
		
		# Fields to save
		$save_list = array(
			"create_account"
		);
		
		$exclude_fields_customer = array("errors", "password_confirm", "shipping_province_id", "payment_province_id", "sameAsBillingInfo", "create_account");
		$exclude_fields_cart = array();
		
		$order = new CartOrder();
		$order_item = new CartOrderItem();
		$customer = new CartCustomer();
		$reserve = new CartReserve();
		$item = new CartProduct();
		$shipping_rate = new CartShipping();
		
		$item->createSlugField("name_$this->lang3");
		$cart_total_weight = $item->getCartTotalWeight($_SESSION['cart']['items']);
		
		$_SESSION['customer']['shipping_zip'] = str_replace(' ', '', strtoupper($_SESSION['customer']['shipping_zip']));
		$_SESSION['customer']['shipping_zip'] = strtoupper($_SESSION['customer']['shipping_zip']);
		$final_shipping_rates = $shipping_rate->getShippingRates($_SESSION['customer']['shipping_zip'], $cart_total_weight);

		if(isset($_POST['shipping_rates_select']) && $_POST['shipping_rates_select'] || isset($_SESSION['cart']["calculated_shipping_rates"]))
		{
			if(!isset($_POST["shipping_rates_select"])) $_POST["shipping_rates_select"] = $_SESSION['cart']["calculated_shipping_rates"];
			$_SESSION['cart']['calculated_shipping_rates'] = $_POST["shipping_rates_select"];
		}else{
			# First time in the page, select the first shipping option
			$rate = current($final_shipping_rates);
			$_SESSION['cart']["calculated_shipping_rates"] = NULL;
			$_SESSION['cart']["calculated_shipping_rates"] = (string)$rate["service_code"];
			$_POST["shipping_rates_select"] = $_SESSION['cart']["calculated_shipping_rates"];
		}

		if(isset($_POST['shipping_rates_select'])){
			foreach($final_shipping_rates as $rate)
			{
				if($rate["service_code"]==$_SESSION['cart']["calculated_shipping_rates"]) {
					$_SESSION['cart']['total'] += (float)$rate["shipping_cost"];
					$_SESSION['cart']["calculated_shipping_rate"] = (float)$rate["shipping_cost"];
					$_SESSION['cart']["calculated_shipping_name"] = (string)$rate["service_name"];
					$_SESSION['cart']["calculated_shipping_expected_delivery_date"] = (string)$rate["expected_delivery_date"];
				}
			}
		}

		# If Canada Post return nothing, go back to the shadow
		#echo $final_shipping_rates;
		#echo $_SESSION['customer']['shipping_zip'];
		if(!is_array($final_shipping_rates))
		{
			$_SESSION['errors'][] = $cart["zip_code_error"];
			$this->redirect("checkout_shipping-address");
		}
		
		if(isset($_POST['continue']) && $_SESSION['cart']["calculated_shipping_rates"]){
		
			$record = array();
			if(isset($_POST['create_account'])){
				
				# Validate password and create new customer
				$completed = $this->_saveAndValidateSession($required_list, $save_list, 'customer');
				if($completed){
					if($_POST['password'] == $_POST['password_confirm']){
						if($customer->is_valid_password($_POST['password']))
						{
							# Ready to create the customer
							$record_customer = array();
							foreach($_SESSION['customer'] as $key => $value){
								if(!in_array($key, $exclude_fields_customer) && !is_int($key)) $record_customer[$key] = $value;
							}
							
							$record_customer['first_name'] = $record_customer['payment_first_name'];
							$record_customer['last_name'] = $record_customer['payment_last_name'];
							$record_customer['payment_postcode'] = $_SESSION['customer']['payment_zip'];
							$record_customer['shipping_postcode'] = $_SESSION['customer']['shipping_zip'];
							$record_customer['email'] = $record_customer['email'];
							$record_customer['phone'] = $record_customer['phone'];
							$record_customer['payment_state'] = $_SESSION['customer']['payment_province'];
							$record_customer['shipping_state'] = $_SESSION['customer']['shipping_province'];
							
							$record_customer['password'] = md5(UNIQUE_SALT.md5($record_customer['password']));
							if(!$customer->exist($record_customer['email']))
							{
								$customer->insert($record_customer);
							}else{
								$_SESSION['errors'][] = $cart["user_already_exist.error"];
								$this->redirect("checkout_billing-address");
							}
						}else{
							$_SESSION['errors'][] = $login["recover.not_secure"];
							$this->redirect("checkout_order-confirmation");
						}
						
					}else{
						$_SESSION['errors'][] = $cart["password_mismatch.error"];
						$this->redirect("checkout_order-confirmation");
					}
				}
				$record['customer_id'] = $record_customer['id'];
			}else if(isset($_SESSION['customer']['id'])){
				$record['customer_id'] = $_SESSION['customer']['id'];
			}
			
			# Create new order for each product separately
			$record['payment'] = 1;
			$record['lang'] = $this->lang2;
			
			# General
			$record['email'] = $_SESSION['customer']['email'];
			$record['phone'] = $_SESSION['customer']['phone'];
			
			# Shipping
			$record['shipping_first_name'] = $_SESSION['customer']['shipping_first_name'];
			$record['shipping_last_name'] = $_SESSION['customer']['shipping_last_name'];
			$record['shipping_company'] = $_SESSION['customer']['shipping_company'];
			$record['shipping_address_1'] = $_SESSION['customer']['shipping_address_1'];
			$record['shipping_address_2'] = $_SESSION['customer']['shipping_address_2'];
			$record['shipping_city'] = $_SESSION['customer']['shipping_city'];
			$record['shipping_postcode'] = $_SESSION['customer']['shipping_zip'];
			$record['shipping_state'] = $_SESSION['customer']['shipping_province'];
			$record['shipping_country'] = $_SESSION['customer']['shipping_country'];
			$record['shipping_fees'] = $_SESSION['cart']['calculated_shipping_rate'];
			$record['expected_delivery_date'] = $_SESSION['cart']['calculated_shipping_expected_delivery_date'];
			$record['shipping_type'] = $_SESSION['cart']['calculated_shipping_name'];
			
			$record['currency_id'] = 1;

			//TODO Ajuster ça pour que ce soit plus spécifique.
			$record['shippingmethod_id'] = 2;
			
			# Payment
			$record['payment_first_name'] = $_SESSION['customer']['payment_first_name'];
			$record['payment_last_name'] = $_SESSION['customer']['payment_last_name'];
			$record['payment_company'] = $_SESSION['customer']['payment_company'];
			$record['payment_address_1'] = $_SESSION['customer']['payment_address_1'];
			$record['payment_address_2'] = $_SESSION['customer']['payment_address_2'];
			$record['payment_city'] = $_SESSION['customer']['payment_city'];
			$record['payment_postcode'] = $_SESSION['customer']['payment_zip'];
			$record['payment_state'] = $_SESSION['customer']['payment_province'];
			$record['payment_country'] = $_SESSION['customer']['payment_country'];
			
			$record['payment_mode'] = $_SESSION['customer']['payment_mode_id'];
			$record['order_date'] = date("Y-m-d");
			
			$sub_total = 0;
			$tps = 0;
			$tvq = 0;
			$tvh = 0;
			$taxes = 0;
			$total = 0;
			unset($record['id']);
			$count++;
			$record_items = array();
			
			foreach($_SESSION['cart']['items'] as $item){
				$sub_total += $item['total_price'];
				
				/*$tps += isset($item['taxes']['TPS']) ? $item['taxes']['TPS']['amount'] : 0;
				$tvq += isset($item['taxes']['TVQ']) ? $item['taxes']['TVQ']['amount'] : 0;
				$tvh += isset($item['taxes']['TVH']) ? $item['taxes']['TVH']['amount'] : 0;*/
				$price = $item['price'];
				$option = $item['data_option']["product_opts.option_$this->lang3"];
				$option_id = $item['data_option']["product_opts.id"];
				$record_items[] = array('product_id' => $item['data']['products.id'],
										'qty' => $this->_qty_to_string($item['qty']['qty']), 
										'price' => $price,
										'option' => $option,
										'product_opt_id' => $option_id);
			}
			$tvh = isset($item['taxes']['TVH']) ? $item['taxes']['TVH']['amount'] : 0;
			$record['tps'] .= number_format($_SESSION['cart']['taxes']["TPS"]["amount"], 2, ".", "");
			$record['tvq'] .= number_format($_SESSION['cart']['taxes']["TVQ"]["amount"], 2, ".", "");
			$record['tvh'] .= number_format($_SESSION['cart']['taxes']["TVH"]["amount"], 2, ".", "");
			$record['subtotal'] .= number_format($sub_total, 2, ".", "");
			$record['taxes'] .= number_format($tps + $tvq + $tvh, 2, ".", "");
			$record['total'] .= number_format($record['subtotal'] + $record['taxes'] + $record['shipping_fees'], 2, ".", "");
			$record['invoice_no'] = $order->getOrderNumber($record, $count, count($orders));
			$record['unique_transaction_id'] = $this->getToken(32);
			$record['active'] = 1;
			$record['statut_id'] = 1;

			$_SESSION['cart']['unique_transaction_id'] = $record['unique_transaction_id'];
			$confirm_item_availability = $reserve->confirmItemsAvailability($record_items);

			if($confirm_item_availability)
			{
				$order->insert($record, $record_items);

				$this->redirect('pp_redirect');
			}
		}else{
			# Reserve item quantities
			$reserve = new CartReserve();
			$reserve->reserveQtys();
		}
		
		
		return array('cart_session' => $_SESSION['cart'], 'final_shipping_rates' => $final_shipping_rates);
	}	
	
	function completed_transaction(){
		if(!$_SESSION["messages"]) $this->redirect();
	}
	
	function _notify_admin($record){
		global $cart;


		require_once("app/helpers/mail/phpmailer.class.php");
		require_once("app/helpers/mail/smtp.class.php");
		
		# Your code here to handle a successful verification
		$order = new CartOrder();
		
		$record_complete = $order->get($record["cartorders.id"]);
		$record_items = $order->getItemsOrderByID($record["cartorders.id"]);

		if(strrpos($record_complete['cartorders.invoice_no'], '-') > 3) $main_id = (substr($record_complete['cartorders.invoice_no'], 0, strrpos($record_complete['cartorders.invoice_no'], '-')));
		else $main_id = (($record_complete['cartorders.invoice_no']));
		
		$mail = new PHPMailer();
		$mail->CharSet = "utf-8"; 
		$mail->IsSMTP();
		$mail->IsHTML(true);
		
		# Email information
		$mail->Subject	= "Nouvelle commande - (#$main_id)"; 
		$mail->From = (EMAIL_MANAGER);
		$mail->FromName = (EMAIL_MANAGER_NAME);
		$mail->AddReplyTo(EMAIL_MANAGER, EMAIL_MANAGER_NAME);

		# Client's address
		$mail->AddAddress(EMAIL_MANAGER, EMAIL_MANAGER_NAME);

		# Message that will be sent to the admin
		$message = '<style>td{border-bottom:1px solid #ddd; padding:10px;background:#f4f4f4;}</style>';
		$message .= '<body style="background:#FFF; width:100%; padding:20px; margin:0; font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px;">';
			# Insert logo in email
			//$message .= "<br><br><img src='http://".$_SERVER['HTTP_HOST'].URL_ROOT.PUBLIC_FOLDER.'images/common/*.png'."' alt=''>";
			$message .= "<p style='font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px;'>";
			$message .= "Le client <strong>{$record_complete['cartorders.payment_first_name']} {$record_complete['cartorders.payment_last_name']}</strong> a passé une commande. Voici les informations qu'il a saisi: ";
			$message .= "</p>";
			$message .= "<p style='font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px;'>";
			$message .= $record_complete['cartorders.shipping_type']." - ";
			$message .= $this->writePrettyDate($record_complete['cartorders.expected_delivery_date']);
			$message .= "</p>";
			$message .= '<table width="600" cellpadding="0" cellspacing="0" style="width:600px; font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px; margin:0 0 20px 0;">';
				$message .= "<tr><td colspan='2'><strong>{$cart['order_mail_billing']}</strong></td><td style='border:none;background:#FFF;'></td><td colspan='2'><strong>{$cart['order_mail_expedition']}</strong></td></tr>";
				$message .= "<tr><td width='50'><strong>{$cart['form_first_name']}:</strong></td><td width='205'>{$record_complete['cartorders.payment_first_name']}</td><td width='30' style='border:none;background:#FFF;'></td><td width='50'><strong>{$cart['form_first_name']}:</strong></td><td width='205'>{$record_complete['cartorders.shipping_first_name']}</td></tr>";
				$message .= "<tr><td><strong>{$cart['form_last_name']}:</strong></td><td>{$record_complete['cartorders.payment_last_name']}</td><td style='border:none;background:#FFF;'></td><td><strong>{$cart['form_last_name']}:</strong></td><td>{$record_complete['cartorders.shipping_last_name']}</td></tr>";
				$message .= "<tr><td><strong>{$cart['form_company']}:</strong></td><td>{$record_complete['cartorders.payment_company']}</td><td style='border:none;background:#FFF;'></td><td><strong>{$cart['form_company']}:</strong></td><td>{$record_complete['cartorders.shipping_company']}</td></tr>";
				$message .= "<tr><td><strong>{$cart['form_address']}:</strong></td><td>{$record_complete['cartorders.payment_address_1']}</td><td style='border:none;background:#FFF;'></td><td><strong>{$cart['form_address']}:</strong></td><td>{$record_complete['cartorders.shipping_address_1']}</td></tr>";
				$message .= "<tr><td></td><td>{$record_complete['cartorders.shipping_address_2']}</td><td></td><td>{$record_complete['cartorders.shipping_address_2']}</td></tr>";
				$message .= "<tr><td><strong>{$cart['form_city']}:</strong></td><td>{$record_complete['cartorders.payment_city']}</td><td style='border:none;background:#FFF;'></td><td><strong>{$cart['form_city']}:</strong></td><td>{$record_complete['cartorders.shipping_city']}</td></tr>";
				$message .= "<tr><td><strong>{$cart['form_zip']}:</strong></td><td>{$record_complete['cartorders.payment_postcode']}</td><td style='border:none;background:#FFF;'></td><td><strong>{$cart['form_zip']}:</strong></td><td>{$record_complete['cartorders.shipping_postcode']}</td></tr>";
				$message .= "<tr><td><strong>{$cart['form_province']}:</strong></td><td>{$record_complete['cartorders.payment_state']}</td><td style='border:none;background:#FFF;'></td><td><strong>{$cart['form_province']}:</strong></td><td>{$record_complete['cartorders.shipping_state']}</td></tr>";
				$message .= "<tr><td><strong>{$cart['form_country']}:</strong></td><td>{$record_complete['cartorders.payment_country']}</td><td style='border:none;background:#FFF;'></td><td><strong>{$cart['form_country']}:</strong></td><td>{$record_complete['cartorders.shipping_country']}</td></tr>";
			$message .= '</table>';
			$message .= '<table width="600" cellpadding="0" cellspacing="0" style="width:600px; font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px; margin:0 0 20px 0;">';
				$message .= "<tr><td><strong>{$cart['form_email']}:</strong></td><td><a href='mailto:{$record_complete['cartorders.email']}'>{$record_complete['cartorders.email']}</a></td></tr>";
				$message .= "<tr><td><strong>{$cart['form_phone']}:</strong></td><td>{$record_complete['cartorders.phone']}</td></tr>";
			$message .= '</table>';

			
			$message .= "<p style='font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px;'><strong>{$cart['order_mail_order']}:</strong></p>";
			$message .= '<table width="600" cellpadding="0" cellspacing="0" style="width:600px; font-family:Arial, sans-serif; color:#6d6d6d; background:#FFF; font-size:12px; margin:0 0 20px 0;">';
			
			foreach($record_items as $item){
				$message .= "<tr>";
				$message .= "<td style='background:#FFF;'><img src='http://".$_SERVER['HTTP_HOST']. URL_ROOT . PUBLIC_FOLDER . WBR_FOLDER . $this->getPicturePath($item["product_pic_ts"]["product_pic_ts.file"]) . "' width='100' /></td>";
				$message .= "<td style='background:#FFF;'>".$item["products.name_$this->lang3"]. " (".$item["product_opts.option_$this->lang3"].")</td>";
				if($item["cartorder_products.qty"]) $message .= "<td style='background:#FFF;'><strong>{$cart['form_qty']}</strong><br>".$item["cartorder_products.qty"] . "</td>";
				if($item["cartorder_products.unit_cost"]) $message .= "<td style='background:#FFF;'><strong>{$cart['form_unit_cost']}</strong><br>".number_format($item["cartorder_products.unit_cost"],2)." $</td>";
				if($item["cartorder_products.qty"] && $item["cartorder_products.unit_cost"]) $message .= "<td style='background:#FFF;'><strong>{$cart['order_mail_subtotal']}</strong><br>".number_format($item["cartorder_products.unit_cost"]*$item["cartorder_products.qty"],2)." $</td>";

				$message .= "</tr>";
			}
			$message .= '</table>';
			$message .= '<table width="600" cellpadding="0" cellspacing="0" style="width:600px; font-family:Arial, sans-serif; color:#6d6d6d; background:#f4f4f4; font-size:12px; margin:0 0 20px 0;">';
				$message .= "<tr><td>{$cart['order_mail_subtotal']}:</td><td align='right'>".number_format($record_complete['cartorders.subtotal'],2)." $</td></tr>";
				if($record_complete['cartorders.tps'] && $record_complete['cartorders.tps']!="0.00")$message .= "<tr><td>{$cart['order_mail_tps']}:</td><td align='right'>".number_format($record_complete['cartorders.tps'],2)." $</td></tr>";
				if($record_complete['cartorders.tvq'] && $record_complete['cartorders.tvq']!="0.00")$message .= "<tr><td>{$cart['order_mail_tvq']}:</td><td align='right'>".number_format($record_complete['cartorders.tvq'],2)." $</td></tr>";
				if($record_complete['cartorders.tvh'] && $record_complete['cartorders.tvh']!="0.00")$message .= "<tr><td>{$cart['order_mail_tvh']}:</td><td align='right'>".number_format($record_complete['cartorders.tvh'],2)." $</td></tr>";
				if($record_complete['cartorders.shipping_fees'] && $record_complete['cartorders.shipping_fees']!="0.00")$message .= "<tr><td>{$cart['order_mail_shipping']}:</td><td align='right'>".number_format($record_complete['cartorders.shipping_fees'],2)." $</td></tr>";
				$message .= "<tr><td><strong>Total:</strong></td><td align='right'><strong>".number_format($record_complete['cartorders.total'],2)." $</strong></td></tr>";

			$message .= '</table>';
			
		$message .= '</body>';
		$mail->Body = $message;
	
		if ($mail->Send()) {
			//$_SESSION['messages'][] = "L'administrateur a été avisé de la commande.";
		}else{
			$_SESSION['errors'][] = "L'administrateur n'a pu être contacté: " . $mail->ErrorInfo;

		}

	}
	
	function _notify_user($record){
		
		global $cart, $meta;
		require_once("app/helpers/mail/phpmailer.class.php");
		require_once("app/helpers/mail/smtp.class.php");
		
		# Your code here to handle a successful verification
		$order = new CartOrder();
		$record_complete = $order->get($record["cartorders.id"]);
		$record_items = $order->getItemsOrderByID($record["cartorders.id"]);

		$mail = new PHPMailer();
		$mail->CharSet = "utf-8"; 
		$mail->IsSMTP();
		$mail->IsHTML(true);
		
		if(strrpos($record_complete['cartorders.invoice_no'], '-') > 3) $main_id = (substr($record_complete['cartorders.invoice_no'], 0, strrpos($record_complete['cartorders.invoice_no'], '-')));
		else $main_id = (($record_complete['cartorders.invoice_no']));
		
		# Email information
		$mail->Subject	= "{$meta['site.title']} - {$cart['order_mail_info']} #$main_id"; 
		$mail->From = (EMAIL_MANAGER);
		$mail->FromName = (EMAIL_MANAGER_NAME);
		$mail->AddReplyTo(EMAIL_MANAGER, EMAIL_MANAGER_NAME);

		//Client's address
		$mail->AddAddress($record_complete['cartorders.email'], "{$record_complete['cartorders.payment_first_name']} {$record_complete['cartorders.payment_last_name']}");

		# Message that will be sent to the user
		$message = '<style>td{border-bottom:1px solid #ddd; padding:10px;background:#f4f4f4;}</style>';
		$message .= '<body style="background:#FFF; width:100%; padding:20px; margin:0; font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px;">';
			$message .= "<p style='font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px;'>";
			$message .= " {$cart['order_mail_info']}: ";
			$message .= "</p>";
			$message .= "<p style='font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px;'>";
			$message .= $record_complete['cartorders.shipping_type']." - ";
			$message .= $this->writePrettyDate($record_complete['cartorders.expected_delivery_date']);
			$message .= "</p>";
			
			$message .= '<table width="600" cellpadding="0" cellspacing="0" style="width:600px; font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px; margin:0 0 20px 0;">';
				
				# Shipping Information
				$message .= "<tr><td colspan='2'><strong>{$cart['order_mail_billing']}</strong></td><td style='border:none;background:#FFF;'></td><td colspan='2'><strong>{$cart['order_mail_expedition']}</strong></td></tr>";
				
				$message .= "<tr><td width='50'><strong>{$cart['form_first_name']}:</strong></td><td width='205'>{$record_complete['cartorders.payment_first_name']}</td><td width='30' style='border:none;background:#FFF;'></td><td width='50'><strong>{$cart['form_first_name']}:</strong></td><td width='205'>{$record_complete['cartorders.shipping_first_name']}</td></tr>";
				$message .= "<tr><td><strong>{$cart['form_last_name']}:</strong></td><td>{$record_complete['cartorders.payment_last_name']}</td><td width='30' style='border:none;background:#FFF;'></td><td><strong>{$cart['form_last_name']}:</strong></td><td>{$record_complete['cartorders.shipping_last_name']}</td></tr>";
				$message .= "<tr><td><strong>{$cart['form_company']}:</strong></td><td>{$record_complete['cartorders.payment_company']}</td><td width='30' style='border:none;background:#FFF;'></td><td><strong>{$cart['form_company']}:</strong></td><td>{$record_complete['cartorders.shipping_company']}</td></tr>";
				$message .= "<tr><td><strong>{$cart['form_address']}:</strong></td><td>{$record_complete['cartorders.payment_address_1']}</td><td width='30' style='border:none;background:#FFF;'><td><strong>{$cart['form_address']}:</strong></td><td>{$record_complete['cartorders.shipping_address_1']}</td></td></tr>";
				$message .= "<tr><td></td><td>{$record_complete['cartorders.payment_address_2']}</td><td width='30' style='border:none;background:#FFF;'></td><td></td><td>{$record_complete['cartorders.shipping_address_2']}</td></tr>";
				$message .= "<tr><td><strong>{$cart['form_city']}:</strong></td><td>{$record_complete['cartorders.payment_city']}</td><td width='30' style='border:none;background:#FFF;'></td><td><strong>{$cart['form_city']}:</strong></td><td>{$record_complete['cartorders.shipping_city']}</td></tr>";
				$message .= "<tr><td><strong>{$cart['form_zip']}:</strong></td><td>{$record_complete['cartorders.payment_postcode']}</td><td width='30' style='border:none;background:#FFF;'></td><td><strong>{$cart['form_zip']}:</strong></td><td>{$record_complete['cartorders.shipping_postcode']}</td></tr>";
				$message .= "<tr><td><strong>{$cart['form_province']}:</strong></td><td>{$record_complete['cartorders.payment_state']}</td><td width='30' style='border:none;background:#FFF;'></td><td><strong>{$cart['form_province']}:</strong></td><td>{$record_complete['cartorders.shipping_state']}</td></tr>";
				$message .= "<tr><td><strong>{$cart['form_country']}:</strong></td><td>{$record_complete['cartorders.payment_country']}</td><td width='30' style='border:none;background:#FFF;'></td><td><strong>{$cart['form_country']}:</strong></td><td>{$record_complete['cartorders.shipping_country']}</td></tr>";
				$message .= '</table>';
				
				$message .= '<table width="600" cellpadding="0" cellspacing="0" style="width:600px; font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px; margin:0 0 20px 0;">';
					$message .= "<tr><td><strong>{$cart['form_email']}:</strong></td><td><a href='mailto:{$record_complete['cartorders.email']}'>{$record_complete['cartorders.email']}</a></td></tr>";
					$message .= "<tr><td><strong>{$cart['form_phone']}:</strong></td><td>{$record_complete['cartorders.phone']}</td></tr>";
				$message .= '</table>';

				
				$message .= "<p style='font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px;'><strong>{$cart['order_mail_order']}:</strong></p>";
				$message .= '<table width="600" cellpadding="0" cellspacing="0" style="width:600px; font-family:Arial, sans-serif; color:#6d6d6d; background:#FFF; font-size:12px; margin:0 0 20px 0;">';
				foreach($record_items as $item){
					$message .= "<tr>";
					$message .= "<td style='background:#FFF;'><img src='http://".$_SERVER['HTTP_HOST']. URL_ROOT . PUBLIC_FOLDER . WBR_FOLDER . $this->getPicturePath($item["product_pic_ts"]["product_pic_ts.file"]) . "' width='100' /></td>";
					$message .= "<td style='background:#FFF;'>".$item["products.name_$this->lang3"]. " (".$item["product_opts.option_$this->lang3"].")</td>";
					if($item["cartorder_products.qty"]) $message .= "<td style='background:#FFF;'><strong>{$cart['form_qty']}</strong><br>".$item["cartorder_products.qty"] . "</td>";
					if($item["cartorder_products.unit_cost"]) $message .= "<td style='background:#FFF;'><strong>{$cart['form_unit_cost']}</strong><br>".number_format($item["cartorder_products.unit_cost"],2)." $</td>";
					if($item["cartorder_products.qty"] && $item["cartorder_products.unit_cost"]) $message .= "<td style='background:#FFF;'><strong>{$cart['order_mail_subtotal']}</strong><br>".number_format($item["cartorder_products.unit_cost"]*$item["cartorder_products.qty"],2)." $</td>";
	
					$message .= "</tr>";
				}
				$message .= '</table>';
				$message .= '<table width="600" cellpadding="0" cellspacing="0" style="width:600px; font-family:Arial, sans-serif; color:#6d6d6d; background:#f4f4f4; font-size:12px; margin:0 0 20px 0;">';

				$message .= "<tr><td>{$cart['order_mail_subtotal']}:</td><td align='right'>".number_format($record_complete['cartorders.subtotal'],2)." $</td></tr>";
				if($record_complete['cartorders.tps'] && $record_complete['cartorders.tps']!="0.00")$message .= "<tr><td>{$cart['order_mail_tps']}:</td><td align='right'>".number_format($record_complete['cartorders.tps'],2)." $</td></tr>";
				if($record_complete['cartorders.tvq'] && $record_complete['cartorders.tvq']!="0.00")$message .= "<tr><td>{$cart['order_mail_tvq']}:</td><td align='right'>".number_format($record_complete['cartorders.tvq'],2)." $</td></tr>";
				if($record_complete['cartorders.tvh'] && $record_complete['cartorders.tvh']!="0.00")$message .= "<tr><td>{$cart['order_mail_tvh']}:</td><td align='right'>".number_format($record_complete['cartorders.tvh'],2)." $</td></tr>";
				if($record_complete['cartorders.shipping_fees'] && $record_complete['cartorders.shipping_fees']!="0.00")$message .= "<tr><td>{$cart['order_mail_shipping']}:</td><td align='right'>".number_format($record_complete['cartorders.shipping_fees'],2)." $</td></tr>";
				$message .= "<tr><td><strong>Total:</strong></td><td align='right'><strong>".number_format($record_complete['cartorders.total'],2)." $</strong></td></tr>";

			$message .= '</table>';
			$message .= "<p style='font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px;'>{$cart['order_mail_representant']}.</p>";
			$message .= "{$cart['order_mail_yours_truly']},";
			
			# Insert logo in email
			//$message .= "<br><br><img src='http://".$_SERVER['HTTP_HOST'].URL_ROOT.PUBLIC_FOLDER.'images/common/*.png'."' alt=''>";
		$message .= '</body>';
		$mail->Body = $message;
	
		if ($mail->Send()) {
			$_SESSION['messages'][] = "{$cart['order_mail_email_details']}";
		}else{
			$_SESSION['errors'][] = "{$cart['order_mail_email_error']}: " . $mail->ErrorInfo;

		}		
	}
	
	function _init_customer($required_list, $save_list){
		$session_name = 'customer';
		if(!isset($_SESSION[$session_name])){
			$_SESSION[$session_name] = array();
			//Default to Canada
			
			$_SESSION[$session_name]['shipping_country'] = 'Canada';
			$_SESSION[$session_name]['payment_country'] = 'Canada';
			
		}
		if(!$_SESSION[$session_name]['customer_unique_id'])
		{
			$_SESSION[$session_name]['customer_unique_id'] = $this->getToken(32);
			$this->customer_unique_id = $_SESSION['customer']['customer_unique_id'];
		}
				

		//fill session
		$list = array_merge($required_list, $save_list);
		foreach($list as $key => $value){
			if(!isset($_SESSION[$session_name][$key]) && !isset($_POST[$session_name][$key])) $_SESSION[$session_name][$key] = '';
		}
	}
	
	function _saveAndValidateSession($required_list, $save_list, $session_name){
		global $cart;

		$completed = true;
		foreach($_POST as $key => $value){
			if(array_search($key, $required_list) || array_search($key, $required_list)===0){
				if($key=="phone") $value = $this->format_phone_number($value);
				$_SESSION[$session_name]["$key"] = $value;
				if(!$value && $completed) $completed = false;
				if(!$value) $_SESSION['errors'][] = $cart["$key.error"];
			}else if(array_search($key, $save_list) || array_search($key, $save_list)===0){
				$_SESSION[$session_name]["$key"] = $value;
			}
		}
		return $completed;
	}
	
	function format_phone_number($phone_number) {
		
		$numbers_only = preg_replace("/[^\d]/", "", $phone_number);
  		return preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "$1 $2-$3", $numbers_only);
	}
		
	function _init_cart(){
		
		if(!isset($_SESSION['cart'])){
			$_SESSION['cart'] = array();
			$_SESSION['cart']['items'] = array();
			$_SESSION['cart']['sub_total'] = 0;
			//var_dump(session_id());
		}
	}
	
	function _check_cart(){
		
		global $cart;
		
		$count = 0;
		foreach($_SESSION['cart']['items'] as $item){
			if(isset($item['invalid'])) {
				$_SESSION['errors'][] = sprintf($cart["invalid_item.error"], $item['data']["products.name_$this->lang3"]);
				return false;
			}else $count++;
		}
		return $count;
	}
	
	function _calculate_cart(){

		$province = new CartProvince();
		$citem = new CartProduct();
		if(!isset($_SESSION['customer']['shipping_province'])){
			if($this->lang3 == 'fre') $shipping_province = 'Québec';
			else $shipping_province = 'Quebec';
		}else $shipping_province = $_SESSION['customer']['shipping_province'];
		$taxes = $province->select('provinces')->where("name_$this->lang3 = '$shipping_province'")->order_by("FIELD(tax_name, 'TPS', 'TVQ', 'TVH')")->all();
		$_SESSION['cart']['sub_total'] = 0;
		$_SESSION['cart']['total'] = 0;
		$current_shipping_rate = 0;
		$cart_total_weight = $citem->getCartTotalWeight($_SESSION['cart']['items']);
		if($_SESSION['cart']["zip_code_estimate"] || $_SESSION['customer']['shipping_zip'])
		{
			if($_SESSION['customer']['shipping_zip']) $current_zip_code = $_SESSION['customer']['shipping_zip']; else $current_zip_code = $_SESSION['cart']["zip_code_estimate"];

			$shipping_rate = new CartShipping();
			$shipping_rates = $shipping_rate->getShippingRates($current_zip_code, $cart_total_weight);
			
			foreach($shipping_rates as $rate)
			{
				if($_SESSION['customer']['shipping_zip']) if($rate["service_code"]==$_SESSION['cart']["calculated_shipping_rates"]) $current_shipping_rate = $rate["shipping_cost"];	
				if($_SESSION['cart']["zip_code_estimate"]) if($rate["service_code"]==$_SESSION['cart']["shipping_rates_estimate"]) $current_shipping_rate = $rate["shipping_cost"];	
			}
		}

		foreach($_SESSION['cart']['items'] as &$item){
			$_SESSION['cart']['sub_total'] += $item['total_price'];
			
			# Calculate tax individually
			$item['taxes'] = array();
			$item['taxes_total'] = 0;
			/*foreach($taxes as $tax){
				$tax_amount = (floatval($item['total_price']) + floatval($current_shipping_rate)) * floatval($tax['provinces.tax_rate']);
				$item['taxes']["{$tax['provinces.tax_name']}"] = array('amount' => $tax_amount, 'rate' => $tax['provinces.tax_rate']); 
				$item['taxes_total'] += $tax_amount;
			}
			$item['total_with_taxes'] = $item['total_price'] + $item['taxes_total'];*/
		}

		# And now calculate tax globally
		$_SESSION['cart']['total'] += $_SESSION['cart']['sub_total'];
		$_SESSION['cart']['taxes'] = array();
		/*foreach($taxes as $tax){
			$tax_amount = (floatval($_SESSION['cart']['sub_total']) + floatval($current_shipping_rate)) * floatval($tax['provinces.tax_rate']);
			$_SESSION['cart']['taxes']["{$tax['provinces.tax_name']}"] = array('amount' => $tax_amount, 'rate' => $tax['provinces.tax_rate']); 
			$_SESSION['cart']['total'] += $tax_amount;
		}*/
		
	}
	
	function _add_item(){
		
		global $cart, $routes, $errors;
		
		$item = new CartProduct();
		$item_option = new CartProductOption();
		$reserve = new CartReserve();
		$productPicture = new CartProductPicture();
		$productCategory = new CartProductCategory();
		
		$current_item = $item->getCurrentProductCart($_POST['id'], NULL, $_POST['option']);
		$current_item_option = $item_option->getCurrentProductOption($_POST['option']);
		$current_item_qty = $item->getCurrentProductQtys($_POST['id'], $_POST['qty'], $_POST['option']);
		$current_item_pic_t = $productPicture->getProductFirstPicture($_POST['id']);
		$current_item_pic_t = $current_item_pic_t["product_pic_ts.file"];
		$current_item_category = $productCategory->getCurrentProductCategoryFromID($_POST['id']);

		# Error with the item, return an error message.
		if(!$current_item_option) return "<div class='msg is-failure'>{$cart['not_in_inventory']}</div>";
		if(!$current_item_qty || $_POST['qty'] > $current_item_qty) return "<div class='msg is-failure'>{$cart['not_enough_in_inventory']}</div>";

		# Create item in the cart
		$qty_total = $_POST['qty'];

		# Handle price
		$price = $current_item_option["product_opts.price"];
		$weight = $current_item_option["product_opts.weight"];
		
		if(!is_numeric($qty_total) || intval($qty_total) != $qty_total) return "<div class='msg is-failure'>{$cart['invalid_qty']}</div>";

		# If quantity is too low, we return an error message
		if($qty_total && !$price) return "<div class='msg is-failure'>".$price."{$cart['not_enough_qty']}</div>";
		if(!$qty_total) return "<div class='msg is-failure'>{$cart['no_qty']}</div>";
		$total_price = $price * $qty_total;
		
		# Set Session variables
		$_SESSION['cart']['items'][] = array('qty' => array('qty' => $_POST['qty'], 'total' => $qty_total, 'id' => $_POST['id']), 'price' => $price, 'pic_t' => $current_item_pic_t, 'category_slug' => $current_item_category, 'weight' => $weight, 'total_price' => $total_price, 'option_id' => $_POST['option'], 'data_option' => $current_item_option, 'data' => $current_item);
		$_SESSION['cart']['sub_total'] += $total_price;
		
		# If everything is ok, return the success message
		return "<div class='msg is-success'>".sprintf($cart['added_item'], "<strong>".$current_item["products.name_$this->lang3"]."</strong> (".$current_item_option["product_opts.option_$this->lang3"].")", "<a href='". URL_ROOT . $this->lang2 . "/" . $routes["cart"] . "'>", "</a>")."</div>";
	}
	
	function _item_exists_in_cart($id, &$session_key, &$qty, &$option){
		
		foreach($_SESSION['cart']['items'] as $session_key => $item){
			if($item['option_id'] == $option){
				$qty += $item['qty']['qty'];
				return true;	
			}
		}
		return false;
	}
	
	function _update_quantity($session_key, $qties){
		
		global $cart, $routes;
		$item = new CartProduct();
		$reserve = new CartReserve();
		$shipping = new CartShipping();
		
		# Retrive item data
		$current_item = &$_SESSION['cart']['items'][$session_key];

		# Substract old price from the cart
		$_SESSION['cart']['sub_total'] -= floatval($current_item['total_price']);
		
		$current_item['qty']['qty'] = $qties['qty'];
		$qty_total = $qties['qty'];

		$current_item['qty']['total'] = $qty_total;
		
		$current_item_price = $current_item['price'];
		$total_qty = $qty_total;
		
		$current_item_qty = $item->getCurrentProductQtys($current_item['id'], $total_qty, $current_item['option_id']);
		
		# If quantity is too low, we return an error message
		if($total_qty<=0)
		{
			//$current_item['qty']['qty'] = 1;	
			return "<div class='error msg is-failure'>{$cart['not_enough_qty']}</div>";
		}

		# If quantity is too high, we return an error message
		if($current_item_qty < $current_item['qty']['qty'])
		{
			//$current_item['qty']['qty'] = 1;	
			return "<div class='error msg is-failure'>{$cart['not_enough_in_inventory']}</div>";
		}
		
		# If quantity doesn't exist, we return an error message
		if($current_item_qty<=0)
		{
			//$current_item['qty']['qty'] = 1;	
			return "<div class='error msg is-failure'>".$current_item_qty."{$cart['not_enough_in_inventory']}</div>";
		}

		if(!$total_qty || $total_qty < 1) return "<div class='msg is-failure'><p>{$cart['no_qty']}</p></div>";
		
		if($invalid_msg) {
			$current_item['invalid'] = true;
			$current_item['price'] = 0;
			$current_item['total_price'] = 0;
			return $invalid_msg;
		}else{
			unset($current_item['invalid']);
			$current_item['price'] = $current_item_price;
			$current_item['total_price'] = floatval($current_item['price'])*intval($qty_total);
			return "<div id='unit_price'>{$current_item['price']}</div>";
		}

		$cart_total_weight = $item->getCartTotalWeight($_SESSION['cart']['items']);
		$shipping_rates = $shipping->getShippingRates($_SESSION['cart']["zip_code_estimate"], $cart_total_weight);
		
		
		# Add new price to the cart
		$_SESSION['cart']['sub_total'] += floatval($current_item['total_price']);

		# If everything is ok, return the success message
		$ret =  "<div class='msg is-success'>".sprintf($cart['updated_item'], "<strong>".$current_item["products.name_$this->lang3"]."</strong>", "<a href='". URL_ROOT . $this->lang2 . "/" . $routes["cart"] . "'>", "</a>")."</div>";
		return $ret . "<div id='new_shipping_rates'>".json_encode($shipping_rates)."</div>";
		
	
	}
	
	function _get_price_range($price_ranges, $qty){
		foreach($price_ranges as $current_price_range){
			if((empty($current_price_range['item_prices.minimum']) && empty($current_price_range['item_prices.maximum'])) || 
				($qty >= $current_price_range['item_prices.minimum'] && ($qty <= $current_price_range['item_prices.maximum'] || empty($current_price_range['item_prices.maximum']))) ){
				
				return $current_price_range;
			}
		}
		
	}
	
	function _empty_cart(){
		unset($_SESSION['cart']);
	}
	
	function _clean_sessions(){
		unset($_SESSION['cart']);
		unset($_SESSION['customer']);
	}
	
	function _qty_to_string($qty){
		$str = '';
		if(is_array($qty)){
			$qty_total = array_sum($qty);
			foreach($qty as $key => $value){
				if($value) $str .= $key.':'.$value.',';
			}
			$str .= 'TOTAL:'.$qty_total;
		} else $str = $qty;
		return $str;
	}
	
	function _qty_total($qty){
		if(is_array($qty)) return $qty_total = array_sum($qty);
		else return $qty;
	}
	
	function forgot() {

		# Model declaration
		$customer = new CartCustomer();
		
		if(isset($_POST['action']) && $_POST['action']=='send' && isset($_POST['email'])) {
			$customer->forgotPassword($_POST['email']);
		}

	}
	
	function recover() {

		global $routes, $errors, $login;
		
		# Model declaration
		$customer = new CartCustomer();

		if($customer->verifyRecoveryToken($_GET['param1'], $_GET['param2']))
		{
			if(isset($_POST['action']) && $_POST['action']=='reset' && isset($_POST['password']) && isset($_POST['password_confirm'])) {
				
				if($_POST['password']===$_POST['password_confirm'])
				{
					if($customer->is_valid_password($_POST['password']))
					{
						$customer->updateUserPassword($_POST['password'], $_GET['param2']);
						if(session_id() == '') session_start();
						$_SESSION['messages'][] = $login["recover.success"];
						header('Location: '.URL_ROOT.$this->lang2.'/'.$routes['checkout_login']);
					}else{
						$errors[] = $login["recover.not_secure"];
					}
				}else{
					$errors[] = $login["recover.not_same"];
				}
				
			}
			
		}else{
			header('Location: '.URL_ROOT.$this->lang2.'/'.$routes['checkout_login']);	
		}

	}

	function product() {

	}
	
	function product_category_list() {
		
		global $routes;
		
		# Model declaration
		$product = new CartProduct();
		$productCategory = new CartProductCategory();
		
		# Slug creation
		$product->createSlugField("name_$this->lang3");

		# Data
		$product = $product->getFirstProductFromCategory($_GET["param1"]);
		$product_category = $productCategory->getCurrentProductCategory($_GET["param1"]);

		if(!$product_category || !$product) $this->redirect("product");
		elseif($product["products.slug_$this->lang3"]) header('Location: '.URL_ROOT.$this->lang2."/".$routes["product"]."/".$_GET["param1"]."/".$product["products.slug_$this->lang3"]);
		//else header('Location: '.URL_ROOT.$this->lang2."/".$routes["product"]."/".$_GET["param1"]);

	}

	function product_show() {
		
		global $routes;
		
		# Model declaration
		$product = new CartProduct();
		$productCategory = new CartProductCategory();
		$productPicture = new CartProductPicture();
		$productCategoryDetail = new CartProductCategoryDetail();
		$season = new Season();

		# Slug creation
		$product->createSlugField("name_$this->lang3");

		# Data
		$products = $product->getAllProductsFromCategory($_GET["param1"]);
		$product = $product->getCurrentProduct($_GET["param1"], $_GET["param2"]);
		$product_pictures = $productPicture->getProductPictures($_GET["param2"]);
		$product_category = $productCategory->getCurrentProductCategory($_GET["param1"]);
		$product_details = $productCategoryDetail->getAllProductDetails($_GET["param2"]);
		$season_tags = $season->getAllSeasonTagsFromProduct($_GET["param2"]);

		# Translation
		$this->translateSlug($product_category["productcats.slug_$this->lang3"], $product["products.slug_$this->lang3"]);
		
		if(!$product && !$product_category) $this->redirect("product");
		elseif(!$product) header('Location: '.URL_ROOT.$this->lang2."/".$routes["product"]."/".$_GET["param1"]);
		
		# Metas
		$this->setTitle($product["products.name_$this->lang3"]." | ".$product_category["productcats.name_$this->lang3"]);
		$this->setDescription($product_category["productcats.description_$this->lang3"]);
		$product_picture = current($product_pictures);
		$this->setImage($product_picture["product_pic_ts.file"]);
		
		return array("product" => $product, "products" => $products, "product_pictures" => $product_pictures, "product_category" => $product_category, "product_details" => $product_details, "season_tags" => $season_tags);
	}
	
	function profile() {

		$customer = new CartCustomer();
		
		# If user is logged in
		if($_SESSION['customer']['logged_in']) {
			$order = new CartOrder();
			$orders = $order->getCurrentUserOrders($_SESSION['customer']['user_id']);
		}else{
			$this->redirect("checkout_login");
		}
		
		return array('orders' => $orders);
	}
	
	
	function crypto_rand_secure($min, $max)
	{
		$range = $max - $min;
		if ($range < 1) return $min; // not so random...
		$log = ceil(log($range, 2));
		$bytes = (int) ($log / 8) + 1; // length in bytes
		$bits = (int) $log + 1; // length in bits
		$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
		do {
			$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rnd = $rnd & $filter; // discard irrelevant bits
		} while ($rnd >= $range);
		return $min + $rnd;
	}
	
	function getToken($length)
	{
		$token = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet.= "0123456789";
		$max = strlen($codeAlphabet) - 1;
		for ($i=0; $i < $length; $i++) {
			$token .= $codeAlphabet[$this->crypto_rand_secure(0, $max)];
		}
		return $token;
	}

	# PAYPAL
	public function paypal_redirect(){
		
		global $routes;
		session_start();
		if(!$_SESSION['cart']['unique_transaction_id']) $this->redirect("cart");

		$apiContext = new \PayPal\Rest\ApiContext(
			new \PayPal\Auth\OAuthTokenCredential(
				PAYPAL_CLIENT_ID,     // ClientID
				PAYPAL_SECRET      // ClientSecret
			)
		);
		
		# Lets create an instance of FlowConfig and add landing page type information
		$flowConfig = new \PayPal\Api\FlowConfig();
		
		# Type of PayPal page to be displayed when a user lands on the PayPal site for checkout.
		### Allowed values: Billing or Login. When set to Billing, the Non-PayPal account landing page is used. When set to Login, the PayPal account login landing page is used.
		$flowConfig->setLandingPageType("Billing");
		
		# The URL on the merchant site for transferring to after a bank transfer payment.
		$flowConfig->setBankTxnPendingUrl("http://www.lrdi.ca");
		
		# Parameters for style and presentation.
		$presentation = new \PayPal\Api\Presentation();
		
		# A URL to logo image. Allowed vaues: .gif, .jpg, or .png.
		$presentation->setLogoImage("http://www.lrdi.ca/public/images/common/logo-black.png")
					 ->setBrandName("LRDI")
				     ->setLocaleCode("fr_CA");
		
		# Parameters for input fields customization.
		$inputFields = new \PayPal\Api\InputFields();
		
		# Enables the buyer to enter a note to the merchant on the PayPal page during checkout.
		$inputFields->setAllowNote(true)
					->setNoShipping(1)
					->setAddressOverride(0);
		
		# Payment Web experience profile resource
		$webProfile = new \PayPal\Api\WebProfile();
		
		# Name of the web experience profile. Required. Must be unique
		$webProfile->setName("LRDI" . uniqid())
				   ->setFlowConfig($flowConfig)
				   ->setPresentation($presentation)
			       ->setInputFields($inputFields);
		
		# For Sample Purposes Only.
		$request = clone $webProfile;
		
		try {
			# Use this call to create a profile.
			$createProfileResponse = $webProfile->create($apiContext);
		} catch (\PayPal\Exception\PayPalConnectionException $ex) {
			# NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
			$this->redirect("pp_error");
			//ResultPrinter::printError("Created Web Profile", "Web Profile", null, $request, $ex);
			exit(1);
		}
		
		# NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
		//ResultPrinter::printResult("Created Web Profile", "Web Profile", $createProfileResponse->getId(), $request, $createProfileResponse);
				
		$profile = json_decode($createProfileResponse, true);
		
		$order = new CartOrder();
		$current_order = $order->getOrderByUniqueID($_SESSION['cart']['unique_transaction_id']);
		$items = $order->getItemsOrderByUniqueID($_SESSION['cart']['unique_transaction_id']);

		if(!$current_order) $this->redirect("cart");
		
		$payer = new Payer();
		$payer->setPaymentMethod("paypal");
		
		#echo $current_order["cartorder_products.qty"];
		#echo intval($current_order["product_opts.price"]);
		#exit();
		$i = 0;
		foreach($items as $item)
		{
			$i++;
			${"item".$i} = new Item();
			${"item".$i}->setName($item["products.name_$this->lang3"]." (".$item["product_opts.option_$this->lang3"].")")
			  ->setCurrency('CAD')
			  ->setQuantity($item["cartorder_products.qty"])
			  // ->setSku("123123") // Similar to `item_number` in Classic API
			  ->setPrice((float)($item["product_opts.price"]));

		}
		
		$itemList = new ItemList();
		$itemList->setItems(array($item1));
		
		# Additional payment details
		# Use this optional field to set additional payment information such as tax, shipping charges etc.
		$item = new CartProduct();
		$item->createSlugField("name_$this->lang3");
		$cart_total_weight = $item->getCartTotalWeight($_SESSION['cart']['items']);

		$shipping_rate = new CartShipping();
		$final_shipping_rates = $shipping_rate->getShippingRates($_SESSION['customer']['shipping_zip'], $cart_total_weight);

		$shipping_cost = 0;
		if(isset($_SESSION['cart']["calculated_shipping_rates"])){
			foreach($final_shipping_rates as $rate)
			{
				echo $rate["service_code"];
				if($rate["service_code"]==$_SESSION['cart']["calculated_shipping_rates"]) {
							
					$shipping_cost += (float)$rate["shipping_cost"];

				}
			}
		}

		$details = NULL;
		$details = new Details();
		$details->setShipping(((float)$shipping_cost))
				->setTax(round($record_order["cartorders.tps"]+$record_order["cartorders.tvq"]+$record_order["cartorders.tvh"], 2))
				->setSubtotal((float)($current_order["cartorders.subtotal"]));
		
		# Amount
		# Lets you specify a payment amount. You can also specify additional details such as shipping, tax.
		$amount = new Amount();
		$amount->setCurrency("CAD")
				->setTotal((float)($current_order["cartorders.total"]))
				->setDetails($details);
			
			
		# Transaction
		# A transaction defines the contract of a payment - what is the payment for and who is fulfilling it.
		$transaction = new Transaction();
		$transaction->setAmount($amount)
					->setItemList($itemList)
					->setDescription("Payment description")
					->setInvoiceNumber(uniqid());
		
		# Redirect urls
		# Set the urls that the buyer must be redirected to after payment approval / cancellation.
		#$baseUrl = getBaseUrl($_SERVER['HTTP_HOST']."/PAYPAL_EXPRESS_CHECKOUT");
		$baseUrl = "http://".$_SERVER['HTTP_HOST'].URL_ROOT.$this->lang2."/".$routes["pp_execute_payment"];
		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl("$baseUrl?success=true")
					 ->setCancelUrl("$baseUrl?success=false");
		
		
		# Payment
		# A Payment Resource; create one using the above types and intent set to 'sale'
		$payment = new Payment();
		$payment->setIntent("sale")
				->setExperienceProfileId($profile["id"])
				->setPayer($payer)
				->setRedirectUrls($redirectUrls)
				->setTransactions(array($transaction));
		# For Sample Purposes Only.
		$request = clone $payment;
		
		
		# Create Payment
		# Create a payment by calling the 'create' method passing it a valid apiContext. (See bootstrap.php for more on ApiContext) The return object contains the state and the url to which the buyer must be redirected to for payment approval
		try {
			$payment->create($apiContext);
		} catch (Exception $ex) {
			# NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
			$this->redirect("pp_error");
			#ResultPrinter::printError("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", null, $request, $ex);
			exit(1);
		}
		
		
		# Get redirect url
		# The API response provides the url that you must redirect the buyer to. Retrieve the url from the $payment->getApprovalLink() method
		$approvalUrl = $payment->getApprovalLink();
		
		//ResultPrinter::printResult("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", "<a href='$approvalUrl' >$approvalUrl</a>", $request, $payment);
		header('Location: '.$approvalUrl.'');

		#return $payment;
	}

	public function paypal_execute_payment(){
		
		session_start();
		$url = $_SERVER['REQUEST_URI'];
		$gets = strstr($url, 'success');

		$gets = explode("&", $gets);
		$g_success = explode("=",$gets["0"]);
		$g_success = $g_success[1];
		
		$g_paymentId = explode("=",$gets["1"]);
		$g_paymentId = $g_paymentId[1];
		
		$g_token = explode("=",$gets["2"]);
		$g_token = $g_token[1];
		
		$g_payerID = explode("=",$gets["3"]);
		$g_payerID = $g_payerID[1];

		if ($g_success && $g_success == 'true') {

			$order = new CartOrder();
			$current_order = $order->getOrderByUniqueID($_SESSION['cart']['unique_transaction_id']);

			$apiContext = new \PayPal\Rest\ApiContext(
				new \PayPal\Auth\OAuthTokenCredential(
					PAYPAL_CLIENT_ID,     // ClientID
					PAYPAL_SECRET      // ClientSecret
				)
			);
			
			// Get the payment Object by passing paymentId
			// payment id was previously stored in session in
			// CreatePaymentUsingPayPal.php
			$paymentId = $g_paymentId;
			try {
				$payment = Payment::get($paymentId, $apiContext);
			} catch (Exception $ex) {
				$this->redirect("pp_error");
				exit(1);
			}
			
			if($payment->transactions[0]->amount->total != $current_order["cartorders.total"]) $this->redirect("pp_error");

			// ### Payment Execute
			// PaymentExecution object includes information necessary
			// to execute a PayPal account payment.
			// The payer_id is added to the request query parameters
			// when the user is redirected from paypal back to your site
			$execution = new PaymentExecution();
				try {
			$execution->setPayerId($g_payerID);
			} catch (Exception $ex) {
				$this->redirect("pp_error");
				exit(1);
			}
		
			try {
				// Execute the payment
				// (See bootstrap.php for more on `ApiContext`)
				$result = $payment->execute($execution, $apiContext);
				$transaction_id = json_decode($result, true);
				$transactionID = $transaction_id["transactions"][0]["related_resources"][0]["sale"]["id"];
				$record["statut_id"] = 2;
				$record["token"] = $g_token;
				$record["paymentid"] = $g_paymentId;
				$record["payerid"] = $g_payerID;
				$record["transaction_id"] = $transactionID;
				$record['active'] = 1;
				$record['paymentmethod_id'] = 1;
				$order->update($record, $current_order["cartorders.id"]);
				$this->_notify_user($current_order);
				$this->_notify_admin($current_order);
				$this->redirect("pp_back");

				// NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
				#ResultPrinter::printResult("Executed Payment", "Payment", $payment->getId(), $execution, $result);
		
				try {
					$payment = Payment::get($paymentId, $apiContext);
				} catch (Exception $ex) {
					// NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
					$this->redirect("pp_error");
					//ResultPrinter::printError("Get Payment", "Payment", null, null, $ex);
					exit(1);
				}
			} catch (Exception $ex) {
				// NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
				$this->redirect("pp_error");
				//ResultPrinter::printError("Executed Payment", "Payment", null, null, $ex);
				exit(1);
			}
		
			// NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
			#ResultPrinter::printResult("Get Payment", "Payment", $payment->getId(), null, $payment);
	
			//return $payment;
		}else{
			$this->redirect("pp_cancel");
		}
	}
	
	
	function paypal_back(){
		$reserve = new CartReserve();
		$reserve->releaseReservation();
		$this->_clean_sessions();
	}
	
	
	function paypal_cancel(){
		$order = new CartOrder();
		$current_order = $order->getOrderByUniqueID($_SESSION['cart']['unique_transaction_id']);
		$record["statut_id"] = 3;
		$order->update($record, $current_order["cartorders.id"]);
		$this->_clean_sessions();
		
		if(isset($_GET['param1'])){
			$order = new CartOrder();
			$order_item = new CartOrderItem();
			$reserve = new CartReserve();
			
			$current_order = $order->getOrderByUniqueID($_GET['param1']);
			$reserve->releaseReservation();
			
			# If order exist, delete it
			if($current_order['cartorders.id'])
			{
				$order->delete("cartorders", "id = ".$current_order['cartorders.id']);
				$order_item->delete("cartorder_products", "cartorder_id = ".$current_order['cartorders.id']);
			}
		}

	}
	
	public function paypal_error(){
		$order = new CartOrder();
		$current_order = $order->getOrderByUniqueID($_SESSION['cart']['unique_transaction_id']);
		$record["statut_id"] = 4;
		$order->update($record, $current_order["cartorders.id"]);
		$this->_clean_sessions();
	}

	function paypal_ipn(){
		
		# Create paypal ipn
		include("app/helpers/paypal/pp-class.php");
		
		# Initiate an instance of the class
		$p = new paypal_class();
		
		# Paypal informations are stored in /config.php
		$p->paypal_url = PAYPAL_URL;
		$transaction = new CartOrder();
		$reserve = new CartReserve();
		$product = new CartProduct();
		
		if($this->is_test($p, PAYPAL_ACCOUNT) || $p->validate_ipn() ) {
			if($this->is_secure_transaction($p, PAYPAL_ACCOUNT, $transaction) === true){
				$data = array('txn_id' => $p->ipn_data['txn_id'], 'paid' => 1, 'orderstatus_id' => 1);
				$transaction->update($data, $p->ipn_data['custom']);
				
				$record["id"] = $p->ipn_data['custom'];
				$items = $transaction->getItemsOrderByID($p->ipn_data['custom']);
				foreach($items as $item)
				{
					$product->updateQuantity($item["cartorder_products.qty"], $item['products.id']);
				}
				
				$reserve->releaseReservation();
				$this->_clean_sessions();
				
				$this->_notify_user($record);
				$this->_notify_admin($record);
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
	
					if($p->ipn_data['mc_gross'] == $record_transaction['cartorders.total']){
						return true;
					} else $rsTransaction['message'] = "Transaction amount is not correct. ({$p->ipn_data['mc_gross']} <> {$record_transaction['cartorders.total']})";
				}else{
					$rsTransaction['message'] = "Transaction was not completed.";
					$rsTransaction['orderstatus_id'] = 4;
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
		if(isset($_GET['param1']) && isset($_GET['param2'])){
			$p->ipn_data['custom'] = $_GET['param1'];
			$p->ipn_data['txn_id'] = $_GET['param2'];
			$p->ipn_data['mc_gross'] = '500';
			$p->ipn_data['payment_status'] = 'Completed';
			$p->ipn_data['receiver_email'] = $paypal_account;
			return true;
		}
	}

	
}
