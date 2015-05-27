<?php

class LoginController extends AppController {

	function index() {

		global $routes;
		unset($_SESSION['id']);
		unset($_SESSION['type']);

		if(isset($_POST['action']) && $_POST['action']=='login') {
			
			# Model declaration
			$user = new User();
			
			# Authenticate
			$user->authenticate("email", "password");

			# Redirect the user to the profile page
			if($user->check($_SESSION['type'])){
				if(isset($_SESSION['redirect'])){
					$url = $_SESSION['redirect'];
					unset($_SESSION['redirect']);
					header('Location: '.$url);
				}else{
					switch($_SESSION['type']){
						case 'protected':
							header('Location: '.URL_ROOT.$this->lang2.'/'.$routes['profile']);
						break;
					}
					
				} 
			} 
		}
	}
	
	function logout() {

		# Model declaration
		$user = new User();
		
		#Logout
		$user->logout();
	}
	
	function forgot() {

		# Model declaration
		$user = new User();
		
		if(isset($_POST['action']) && $_POST['action']=='send' && isset($_POST['email'])) {
			$user->forgotPassword($_POST['email']);
		}

	}
	
	function recover() {

		global $routes, $errors, $login;
		
		# Model declaration
		$user = new User();
				
		if($user->verifyRecoveryToken($_GET['param1'], $_GET['param2']))
		{
			if(isset($_POST['action']) && $_POST['action']=='reset' && isset($_POST['password']) && isset($_POST['password_confirm'])) {
				
				if($_POST['password']===$_POST['password_confirm'])
				{
					if($user->is_valid_password($_POST['password']))
					{
						$user->updateUserPassword($_POST['password'], $_GET['param2']);
						if(session_id() == '') session_start();
						$_SESSION['messages'][] = $login["recover.success"];
						header('Location: '.URL_ROOT.$this->lang2.'/'.$routes['login']);
					}else{
						$errors[] = $login["recover.not_secure"];
					}
				}else{
					$errors[] = $login["recover.not_same"];
				}
				
			}
			
		}else{
			header('Location: '.URL_ROOT.$this->lang2.'/'.$routes['login']);	
		}

	}
	
}
