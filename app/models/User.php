<?php

class User extends App {
	
	public function __construct() {
		$this->table = 'users';
		$this->table_code = 'user';
		parent::__construct($this->table, $this->table_code);
	}
	
	function authenticate($username, $password) {
		
		global $global, $errors;
		
		if(session_id() == '') session_start();
		
		if(isset($_POST['action']) && $_POST['action'] == 'login')
		{
			# Initialize data
			$username = $_POST["$username"];
			$password = $_POST["$password"];
			
			if(isset($username) && isset($password)){
				
				$username = trim($username);
				$password = trim($password);
				
				$qUsers = "SELECT {$this->table}.id, {$this->table}.password, {$this->table}.username FROM {$this->table} WHERE ({$this->table}.username = '$username' OR {$this->table}.email = '$username') AND {$this->table}.password = MD5(CONCAT('".UNIQUE_SALT."', MD5('$password')))";
				$rsUsers = $this->db->Execute($qUsers);
				
				# Verify if the user exist
				if(!$rsUsers->EOF){
					$_SESSION['id'] = $rsUsers->fields["id"];
					return true;
				}else{
					$errors[] = $global['incorrect_credentials'];
				}
				$rsUsers->Close();
				
			}
			
			return true;
		}
		
	}
	
	function logout() {
		
		global $lang2, $routes, $messages, $global;
		
		if(isset($_GET['page']) && $_GET['page'] == 'logout'){
			unset($_SESSION['id']);
			header('Location: '.URL_ROOT.$lang2.'/'.$routes['login']);
			$messages[] = $global['disconnected'];
			
			return true;
		}
	}	
	
	function check() {
		
		// To get time, multiply minutes and 60 (30min*60 = 1800)
		if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
			session_unset();
			session_destroy();
		}
		$_SESSION['LAST_ACTIVITY'] = time();
		
		if(isset($_SESSION['id']) && $_SESSION['id']!=NULL){
			return $_SESSION['id'];
		}else{
			return false;
		}
		
	}
	
	function getUserType() {
		
		$q = "SELECT {$this->table}.id, {$this->table}.type_id FROM {$this->table} WHERE {$this->table}.id='".$_SESSION['id']."'";
		$rsList = $this->db->Execute($q);
		return $rsList->fields["type_id"];
		
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
	
	function forgotPassword($username) {
	
		global $lang2, $routes, $errors, $messages, $login;
		
		$q = "SELECT {$this->table}.id, {$this->table}.email FROM {$this->table} WHERE {$this->table}.username='{$username}' OR {$this->table}.email='{$username}'";
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
			$bytes = openssl_random_pseudo_bytes(128, $cstrong);
    		$random_number = bin2hex($bytes);
			
			# Fields that you want to send	
			$email = $rsList->fields["email"];
			$record["recovery_token"] = $random_number;
			$record["user_id"] = $rsList->fields["id"];
			$record["created"] = date('Y-m-d H:i:s');
			$record["updated"] = date('Y-m-d H:i:s');
			$this->db->AutoExecute("recoveries", $record, 'INSERT');

			# Email information
			$mail->Subject	= "Récupération de votre mot de passe"; 
			$mail->Sender	= "no-reply@ccrvc.ca";
			$mail->From		= "no-reply@ccrvc.ca";
			$mail->FromName	= "Conseil canadien du camping et du VR";
			$mail->AddAddress("$email");
			#$mail->AddEmbeddedImage("http://" . $_SERVER['HTTP_HOST'] . URL_ROOT . PUBLIC_FOLDER . "images/common/logo.png", 'logo_2u');

			# Message that will be sent to the user
			$message = '';
			$message .= '<body style="background:#FFF; width:100%; padding:20px; margin:0; font-family:Arial, sans-serif; color:#6d6d6d; font-size:12px;">';
				#$message .= '<img alt="" src="cid:logo_2u">';
				$message .= "Bonjour, <br />";
				$message .= "Vous avez fait une demande de récupération de mot de passe sur le site du Conseil canadien du camping et du VR.<br /><br />";
				$message .= "Pour changer votre mot de passe, cliquez sur le lien ci-dessous.<br />";
				$message .= "<a href='http://" . $_SERVER['HTTP_HOST'] . URL_ROOT . $lang2 . "/" . $routes['recover'] . '/' . $random_number . '/' . $rsList->fields["email"] . "'>Cliquez ici</a><br /><br />";
				$message .= "Veuillez prendre note que le lien ci-dessus expirera dans 1h.";
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
				
		$q = "SELECT recoveries.id, recoveries.created FROM recoveries LEFT JOIN users ON users.id = recoveries.user_id WHERE recoveries.recovery_token='{$token}' AND {$this->table}.email='{$user}'";
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
		$q .= " WHERE recoveries.user_id = '".$rsList->fields["id"]."'";
		$rsDelete = $this->db->Execute($q);

	}
	
	function register(&$messages, &$errors) {
		
		if(isset($_POST['action']) && $_POST['action']=='create')
		{
			global $lang2;
	
			$out = '';
			 
			$q = "SELECT {$this->table}.id, {$this->table}.username FROM {$this->table} WHERE {$this->table}.username='".$_POST['tUsername']."'";
			$rsList = $this->db->Execute($q);
			
			if(isset($_POST['action']) && $_POST['action']=='create' && $rsList->RecordCount()==0) {
				
				if($_POST['tName']!=NULL)		$record["firstname"] = $_POST['tName'];
				if($_POST['tSurname']!=NULL)	$record["lastname"] = $_POST['tSurname'];
				if($_POST['tUsername']!=NULL)	$record["username"] = $_POST['tUsername'];
				if($_POST['tPassword']!=NULL)	$record["password"] =  MD5($_POST['tPassword']);
							
				$this->db->AutoExecute('users', $record,'INSERT');
	
				$messages[] .= 'L\'utilisateur <strong>'.$_POST['tName'].' '.$_POST['tSurname'].'</strong> a été créé.';
			}
			$rsList->Close();
			
			if($out == '') $out = 'Aucun client';
			return $out;
		}else{
			return false;	
		}
		
	}
	
	function update(&$messages, &$errors, $userID=false) {
		
		global $lang2;
		$out = '';
		
		if(isset($_POST['action']) && $_POST['action']=='update')
		{
			$q = "SELECT {$this->table}.id, {$this->table}.username FROM {$this->table} WHERE {$this->table}.id='".$_POST['tUserId']."'";
			$rsList = $this->db->Execute($q);
			
			if($_POST['tName']!=NULL)		$record["firstname"] = $_POST['tName'];
			if($_POST['tSurname']!=NULL)	$record["lastname"] = $_POST['tSurname'];
			if($_POST['tUsername']!=NULL)	$record["username"] = $_POST['tUsername'];
			if($_POST['tPassword']!=NULL)	$record["password"] =  MD5($_POST['tPassword']);
			if($_POST['sLevel']!=NULL)		$record["level"] = $_POST['sLevel'];
			if($_POST['sRegion']!=NULL)		$record["region_id"] = $_POST['sRegion'];
						
			$this->db->AutoExecute('users' ,$record,'UPDATE', 'id ='.$_POST['tUserId']);

			$message[] .= 'L\'utilisateur <strong>'.$_POST['tName'].' '.$_POST['tSurname'].'</strong> a été modifié.';
			
			$rsList->Close();
		}else{
			$q = "SELECT {$this->table}.id, {$this->table}.username, {$this->table}.level, {$this->table}.firstname, {$this->table}.lastname, {$this->table}.region_id FROM {$this->table} WHERE {$this->table}.id='".$userID."'";
			$rsList = $this->db->Execute($q);
			
			$this->firstname = $rsList->fields["firstname"];
			$this->lastname = $rsList->fields["lastname"];
			$this->username = $rsList->fields["username"];
			$this->level = $rsList->fields["level"];
			$this->id = $rsList->fields["id"];
			$this->region_id = $rsList->fields["region_id"];
			
			$rsList->Close();
		}
		
		return $out;
		
	}
	
}
