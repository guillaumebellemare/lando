<?php
require_once("classes/sluggedrecord.class.php");

class Login extends SluggedRecord {
	
	var $table = 'users';	
	var $sortBy = ' ORDER BY rank';
	var $db = null;	
	
	var $name;

	
	function __construct($db, $lang3) {
		$this->name = 'user';
		parent::__construct($db, $lang3, $this->table);
	}
	
	function getCurrent() {
		$this->rsCurrent = $this->db->Execute("SELECT id, name_{$this->lang3} AS name, slug_{$this->lang3} AS slug FROM {$this->table} WHERE active = 1 AND id = {$this->current_id} ORDER BY rank LIMIT 1");
	}
	
	function login(&$errors) {
		
		global $db;
		global $lang;
		$data = NULL;
		
		if(isset($_POST['action']) && $_POST['action'] == 'login')
		{
			/////////////////////
			// Initialize data //
			/////////////////////
			$username = $_POST['tUsername'];
			$password = $_POST['tPassword'];
			
			if(isset($username) && isset($password)){
				
				$qUsers = sprintf("SELECT {$this->table}.id, {$this->table}.password, {$this->table}.username, {$this->table}.level FROM {$this->table} WHERE {$this->table}.username = '%s' AND {$this->table}.password = MD5('%s') ", trim($username), trim($password));
				$rsUsers = $db->Execute($qUsers);
				
				//////////////////////////////
				// Verify if the user exist //
				//////////////////////////////
				if(!$rsUsers->EOF){
					$_SESSION['id'] = $rsUsers->fields["id"];
					$_SESSION['access'] = $rsUsers->fields["level"];
					return true;
				}else{
					$errors[] = "Nom d'utilisateur ou mot de passe incorrect";
				}
				$rsUsers->Close();
				
			}
			
			return $data;
		}
		
	}
	
	function isLogged() {
		if($_SESSION['id']!=NULL){
			return $_SESSION['id'];
		}else{
			return false;
		}
	}
	
	function logout(&$messages) {
		global $lang2;
		
		if(isset($_GET['page']) && $_GET['page'] == 'logout'){
			unset($_SESSION['id']);
			header('Location: '.URL_ROOT.$lang2.'/');
			$messages[] = htmlentities('Vous avez été déconnecté');
			
			return true;
		}
	}	
	
	function getUserFullName() {
		global $db;
		$qUsers = "SELECT {$this->table}.id, {$this->table}.firstname, {$this->table}.lastname FROM {$this->table} WHERE {$this->table}.id = '".$_SESSION['id']."'";
		$rsUsers = $db->Execute($qUsers);
		
		$username = $rsUsers->fields["firstname"].' '.$rsUsers->fields["lastname"];
		
		return $username;
	}
	
	function createUser(&$messages, &$errors) {
		
		if(isset($_POST['action']) && $_POST['action']=='create')
		{
			global $lang2;
			global $db;
	
			$out = '';
			 
			$q = "SELECT {$this->table}.id, {$this->table}.username FROM {$this->table} WHERE {$this->table}.username='".$_POST['tUsername']."'";
			$rsList = $this->db->Execute($q);
			
			if(isset($_POST['action']) && $_POST['action']=='create' && $rsList->RecordCount()==0) {
				
				if($_POST['tName']!=NULL)		$record["firstname"] = $_POST['tName'];
				if($_POST['tSurname']!=NULL)	$record["lastname"] = $_POST['tSurname'];
				if($_POST['tUsername']!=NULL)	$record["username"] = $_POST['tUsername'];
				if($_POST['tPassword']!=NULL)	$record["password"] =  MD5($_POST['tPassword']);
				if($_POST['sLevel']!=NULL)		$record["level"] = $_POST['sLevel'];
				if($_POST['sRegion']!=NULL)		$record["region_id"] = $_POST['sRegion'];
							
				$db->AutoExecute('users', $record,'INSERT');
	
				$messages[] .= 'L\'utilisateur <strong>'.$_POST['tName'].' '.$_POST['tSurname'].'</strong> a été créé.';
			}
			$rsList->Close();
			
			if($out == '') $out = 'Aucun client';
			return $out;
		}else{
			return false;	
		}
	}
	
	function getRegions($region_id){
		
		global $lang2;
		global $db;

		$out = '';
		 
		$q = "SELECT regions.id, regions.name FROM regions";
		$rsList = $this->db->Execute($q);
		
		while(!$rsList->EOF){
			
			if($region_id==$rsList->fields["id"]) $selected = ' selected'; else $selected = '';
			$out .= '<option value="'.$rsList->fields["id"].'"'.$selected.'>'.$rsList->fields["name"].'</option>';
		
		$rsList->MoveNext();
		}
		$rsList->Close();
		
		return $out;
	}
	
	function getUsers() {
			 
		global $lang2;
		global $db;

		$out = '';
		
		$q = "SELECT {$this->table}.id, {$this->table}.username, {$this->table}.firstname, {$this->table}.lastname, {$this->table}.level, {$this->table}.region_id, regions.name AS region_name FROM {$this->table}";
		$q .= " LEFT JOIN regions ON regions.id = {$this->table}.region_id";
		$q .= " ORDER BY regions.name ASC, {$this->table}.level ASC, {$this->table}.firstname ASC";
		$rsList = $this->db->Execute($q);
		
		
		while(!$rsList->EOF){
			
			if($currentCenter!=$rsList->fields["region_id"])
			{
			$out .= '</table>';
			$out .= '<h2>Centre de justice de proximité '.$rsList->fields["region_name"].'</h2>';
			$out .= '<table>';
				$out .= '<tr>';
					$out .= '<th style="width:35%;">Nom de l\'utilisateur</th>';
					$out .= '<th>Niveau</th>';
					$out .= '<th style="width:15%;">Actions</th>';
				$out .= '</tr>';
			}
			
			if($rsList->fields["level"]!=0)
			{
				$out .= '<tr>';
					$out .= '<td>';
						if($rsList->fields["level"]==1) $out .= '<i class="fi-torso has-right-margin" title="Administrateur"></i>';
						$out .= $rsList->fields["firstname"].' '.$rsList->fields["lastname"];
					$out .= '</td>';
					$out .= '<td>';
						if($rsList->fields["level"]==1) $level = 'Admnistrateur'; else $level = 'Utilisateur';
						$out .= $level;
					$out .= '</td>';
					$out .= '<td>';
						$out .= '<form action="'.URL_ROOT.$lang2.'/gestion/" name="fDelete" method="POST" autocomplete="off">';
							$out .= '<a href="'.URL_ROOT.$lang2.'/gestion/update/'.$rsList->fields["id"].'/" class="is-link" title="Modifier"><i class="fi-pencil"></i></a>';
							$out .= '<input name="action" type="hidden" value="delete">';
							$out .= '<input name="tUserId" type="hidden" value="'.$rsList->fields["id"].'">';
							$out .= '<button type="submit" name="btSubmit" class="is-link" title="Supprimer" onclick="return confirm(\'Voulez-vous vraiment supprimer '.$rsList->fields["firstname"].' '.$rsList->fields["lastname"].'?\')" value="Supprimer"/>';
							$out .= '<i class="fi-x"></i>';
							$out .= '</button>';
						$out .= '</form>';
					$out .= '</td>';
				$out .= '</tr>';
			}
			
			$currentCenter = $rsList->fields["region_id"]; 
		
		$rsList->MoveNext();
		}
		$rsList->Close();
      	
		
		return $out;
	}
	
	function updateUser(&$message, &$error, $userID=false) {
		
		global $lang2;
		global $db;

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
						
			$db->AutoExecute('users' ,$record,'UPDATE', 'id ='.$_POST['tUserId']);

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
	
	function getUserName() {
			 
		global $lang2;
		global $db;

		$out = '';
		
		$q = "SELECT {$this->table}.id, {$this->table}.username FROM {$this->table} WHERE {$this->table}.id='".$_SESSION['id']."'";
		$rsList = $this->db->Execute($q);
		
		$out .= $rsList->fields["username"];
		
		return $out;
	}
	
	function deleteUser(&$message, &$error) {

		global $lang2;
		global $db;
		$userID = $_POST['tUserId'];
		
		$q = "SELECT {$this->table}.id, {$this->table}.username, {$this->table}.firstname, {$this->table}.lastname FROM {$this->table} WHERE {$this->table}.id='".$userID."'";
		$rsList = $this->db->Execute($q);
		
		if($rsList->RecordCount()!=0)
		{
			$name = $rsList->fields["firstname"].' '.$rsList->fields["lastname"];
			
			$q = "DELETE FROM {$this->table}";
			$q .= " WHERE {$this->table}.id = '".$userID."'";
			$rsList = $db->Execute($q);
		
			$error[] .= 'L\'utilisateur <strong>'.$name.'</strong> a été supprimé.';
		}
	}
	
	function getUserLevel() {
			 
		global $lang2;
		global $db;
		
		$q = "SELECT {$this->table}.id, {$this->table}.level FROM {$this->table} WHERE {$this->table}.id='".$_SESSION['id']."'";
		$rsList = $this->db->Execute($q);
		
		$this->level = $rsList->fields["level"];
		
		$rsList->Close();
		return true;
	}
	
	function setComplexWhere() {
		if(!$this->complex_where && $this->current_id) $this->complex_where = ' AND id = ' .$this->current_id;
	}

}
?>