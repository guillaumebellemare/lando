<?php

class CartCustomer extends App {
	
	public function __construct() {
		$this->table = 'customers';
		$this->table_code = 'customer';
		parent::__construct($this->table, $this->table_code);
	}

	public function validate_email($email, $customer_id = NULL){
		if(isset($email) && !empty($email)){
			if(!empty($customer_id))$cond_id = " AND $this->table.id <> $customer_id";
			else $cond_id = '';
			$rsCustomer = $this->select($this->table)->where("$this->table.email = '$email'$cond_id")->all(false);
			if(count($rsCustomer)) return 'customer';
			return count($rsFournisseur) == 0;
		}
		return true;
	}
	
	public function authenticate($email, $password){
		$customer = $this->select($this->table)->where("email = '$email' AND password = MD5(CONCAT('".UNIQUE_SALT."', MD5('$password')))")->all(false);
		if(!count($customer)) return false;
		else {
			$current_customer = current($customer);
			return $current_customer['customers.id'];
		}
	}

	function forgotPassword($username) {
	
		global $routes, $errors, $messages, $login, $meta;
		
		$q = "SELECT {$this->table}.id, {$this->table}.email FROM {$this->table} WHERE {$this->table}.email='{$username}'";
		$rsList = $this->db->Execute($q);

		if($rsList->RecordCount() && $username!='')
		{
			
			require_once("app/helpers/mail/phpmailer.class.php");
			require_once("app/helpers/mail/smtp.class.php");
	
			# Your code here to handle a successful verification
			$mail = new PHPMailer();
			$mail->CharSet = "utf-8"; 
			$mail->IsSMTP();
			$mail->IsHTML(true);
			
			# Token, 128 bits encrypted
			$bytes = openssl_random_pseudo_bytes(64, $cstrong);
    		$random_number = bin2hex($bytes);
			
			# Fields that you want to send	
			$email = $rsList->fields["email"];
			$record["recovery_token"] = $random_number;
			$record["customer_id"] = $rsList->fields["id"];
			$record["created"] = date('Y-m-d H:i:s');
			$record["updated"] = date('Y-m-d H:i:s');
			$this->db->AutoExecute("recoveries", $record, 'INSERT');

			# Email information
			$mail->Subject	= "Récupération de votre mot de passe"; 
			$mail->Sender	= "no-reply@ccrvc.ca";
			$mail->From		= "no-reply@ccrvc.ca";
			$mail->FromName	= "".$meta['site.title']."";
			$mail->AddAddress("$email");
			#$mail->AddEmbeddedImage("http://" . $_SERVER['HTTP_HOST'] . URL_ROOT . PUBLIC_FOLDER . "images/common/logo-bnc.png", 'logo_2u');

			# Message that will be sent to the user
			$message = '';
			$message .= '<body style="background:#FFF; width:100%; padding:20px; margin:0; font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px;">';
				#$message .= '<img alt="" src="cid:logo_2u">';
				$message .= "Bonjour, <br />";
				$message .= "Vous avez fait une demande de récupération de mot de passe sur le site ".$meta['site.title'].".<br /><br />";
				$message .= "Pour changer votre mot de passe, cliquez sur le lien ci-dessous.<br />";
				$message .= "<a href='http://" . $_SERVER['HTTP_HOST'] . URL_ROOT . $this->lang2 . "/" . $routes['recover'] . '/' . $random_number . '/' . $rsList->fields["email"] . "'>Cliquez ici</a><br /><br />";
				$message .= "Veuillez prendre note que le lien ci-dessus expirera dans 1h.";
				//$message .= "<br><br><img src='http://".$_SERVER['HTTP_HOST'].URL_ROOT.PUBLIC_FOLDER.'images/common/logo-bnc.png'."' alt='BNC'>";
			$message .= '</body>';
			
			$mail->Body = $message;
		
			if($mail->Send()) {
				$messages[] = $login['forgot.confim'];
			}else{
				$errors[] = $login['forgot.error'];
			}
			
			$rsList->Close();
			return true;
				
		}else{
			$errors[] = $login['does_not_exist'];
			$rsList->Close();
			return false;
		}
	}
	
	function verifyRecoveryToken($token, $user) {
				
		$q = "SELECT recoveries.id, recoveries.created FROM recoveries LEFT JOIN customers ON customers.id = recoveries.customer_id WHERE recoveries.recovery_token='{$token}' AND {$this->table}.email='{$user}'";
		$rsList = $this->db->Execute($q);

		$expired_time = date($rsList->fields["created"]);
		$expired_time = strtotime(date("Y-m-d H:i:s", strtotime($expired_time)) . " +1 hour");
		$expired_time = date("Y-m-d H:i:s",$expired_time);
		
		if($rsList->RecordCount() && $expired_time > date('Y-m-d H:i:s')){
			return true;	
		}else{
			return false;	
		}

	}
	
	function updateUserPassword($new_password, $email) {
			
		$q = "SELECT {$this->table}.id FROM {$this->table} WHERE {$this->table}.email='{$email}'";
		$rsList = $this->db->Execute($q);
				
		$record["password"] = md5(UNIQUE_SALT.md5($new_password));
		$this->db->AutoExecute($this->table, $record, 'UPDATE', 'id = '.$rsList->fields["id"].'');
		
		$q = "DELETE FROM recoveries";
		$q .= " WHERE recoveries.customer_id = '".$rsList->fields["id"]."'";
		$rsDelete = $this->db->Execute($q);

	}
	
	function is_valid_password($candidate) {
		
	   $rule_1 = '/[A-Z]/'; # Uppercase
	   $rule_2 = '/[a-z]/'; # Lowercase
	   $rule_3 = '/[0-9]/'; # Numbers
	
	   if(preg_match_all($rule_1, $candidate, $o) < 1) return false;
	   if(preg_match_all($rule_2, $candidate, $o) < 1) return false;
	   if(preg_match_all($rule_3, $candidate, $o) < 1) return false;
	   if(strlen($candidate) < 8) return false;
	
	   return true;
	}
	
	function exist($email) {
		
		$customers = $this->select($this->table)->where("$this->table.email = '{$email}'")->limit(1);
		if($customers) return true; else return false;
		
	}

}
