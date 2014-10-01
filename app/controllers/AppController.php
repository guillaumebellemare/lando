<?php
require_once("classes/sluggedrecord.class.php");

class AppController extends SluggedRecord {
	
	/*
	/ Arguments = Table name, Column to order, Has slug 1/0?
	*/
	function get($table=NULL, $sortBy=NULL, $hasSlug=NULL){
		
		$numb_arga = func_num_args();
		$numb_join_tables  = $numb_arga - 3;

		$data = array();
		$this->db->SetFetchMode(ADODB_FETCH_ASSOC);

		$q = "SHOW COLUMNS FROM {$table}";
		$rsColumns = $this->db->Execute($q);
		
		// Select rows names
		$a_table_rows = array();
		$table_rows = "";
		$i = 1;
		while(!$rsColumns->EOF){
			
			// Put fields in an array
			$a_table_rows[$i] = $table.".".$rsColumns->fields["Field"];
			
			// Put fields in a string
			if($i==1) $table_rows = $table.".".$rsColumns->fields["Field"]; else $table_rows .= $table.".".$rsColumns->fields["Field"];
			if($rsColumns->RecordCount()!=$i) $table_rows .= ", ";
			
			$i++;
		$rsColumns->MoveNext();
		}
		$rsColumns->Close();


		// Build the query
		
			// Join tables
			$join = NULL;
			
			if($numb_join_tables>0)
			{
				$join_tables = array();
				$v = 0;
				for ($j = 3; $j < $numb_arga; $j++) {
					$join_tables[$v] = func_get_arg($j);
					$v++;
				}

				foreach($join_tables as $k => $jtable)
				{
					$join .= " ".$jtable["type"]." ".$jtable["table"]." ON ".$jtable["on"];

					$joined_table = $jtable["table"];
					$q = "SHOW COLUMNS FROM {$joined_table}";
					$rsColumns = $this->db->Execute($q);
					// Select rows names
					while(!$rsColumns->EOF){
						
						// Put fields of the joined table in the array
						$current_field = $rsColumns->fields["Field"];
						//$a_table_rows[$i] = $joined_table.".".$current_field." AS {$joined_table}_{$current_field}";
						$a_table_rows[$i] = $joined_table.".".$joined_table."_".$current_field;
			
						// Put fields in a string
						if($rsColumns->RecordCount()!=$i) $table_rows .= ", ";
						$table_rows .= $joined_table.".".$current_field." AS {$joined_table}_{$current_field}";
						
						$i++;
					$rsColumns->MoveNext();
					}
					$rsColumns->Close();
					
				}
			}

			$q = "SELECT $table_rows FROM {$table}";
			if($join) $q .= $join;
			$q .= " WHERE {$table}.active = 1";
			
			// Add slugs
			if($this->getArgs($table) && $hasSlug) $q .= $this->getArgs($table);
			if($sortBy)
			{
				$sortBy = str_replace(' ', '', $sortBy);
				$sortBy = explode(",", $sortBy);
				
				$q .= " ORDER BY ";
				$i = 0;
				foreach($sortBy as $sort_field)
				{
					if($i>0) $q .= ", ";
					$q .= "{$table}.$sort_field";
					$i++;
				}
			}
		$rsList = $this->db->Execute($q);
		
		// Put all data in an array
		$i = 0;
				

		while(!$rsList->EOF){
			$data[$i] = array();
			foreach($a_table_rows as $table_row)
			{
				if(explode('.', $table_row)) list($table_name, $table_field) = explode('.', $table_row);
				$data[$i][$table_row] = $rsList->fields[$table_field];
			}
			$i++;
			
		$rsList->MoveNext();
		}
		$rsList->Close();


		return $data;
	}
	
	/*
	/ Arguments = Custom Query
	*/
	function custom_get($customQuery){
		
		$data = array();
		$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
		
		$q = $customQuery;
		$rsList = $this->db->Execute($q);
		
		$columns = array();
		$columns = $rsList->fields;
		$i = 0;
		while(!$rsList->EOF){
			
			foreach($columns AS $column)
			{
				$data[$i] = $rsList->fields;
			}
			$i++;

		$rsList->MoveNext();
		}
		$rsList->Close();


		return $data;
	}


	function save($table) {
		
		global $lang2;
		global $db;
		global $messages, $errors;
		
		$q = "SELECT * FROM {$table}";
		
		// Check for data that already exist in the database
		$numb_arga = func_num_args();
		$numb_verified_fields  = $numb_arga;
		if($numb_verified_fields > 1 && $_POST)
		{
			$q .= " WHERE";
			for ($i = 1; $i < $numb_arga; $i++) {
				$verified_value = $_POST[func_get_arg($i)];
				$verified_field = func_get_arg($i);
				if($i!=1) $q .= " OR";
				$q .= " {$table}.$verified_field = '$verified_value'";
			}
		}
		$rsList = $this->db->Execute($q);
		
		if(isset($_POST['action']) && $_POST['action']=='save' && $_POST && $rsList->RecordCount()==0)
		{
			foreach($_POST as $field => $val)
			{
				if($val) if($_POST[$field]!=NULL) $record["$field"] = $val;
			}
			
			$record["$field"] = $val;
			$db->AutoExecute($table, $record, 'INSERT');
			unset($_POST);
			$messages[] = "L'ajout a bien été effectué.";
		}else{
			if($_POST) $errors[] = "Valeur déjà entrée.";
		}
		
		return true;
	}
	
	function remove($table, $row_id) {
	
		global $lang2;
		global $db;
		global $messages, $errors;
		
		$q = "SELECT * FROM {$table} WHERE {$table}.id={$row_id}";
		$rsList = $this->db->Execute($q);
		if($rsList->RecordCount()!=0)
		{
			$q = "DELETE FROM {$table}";
			$q .= " WHERE {$table}.id = '".$row_id."'";
			$rsList = $db->Execute($q);
		
			$_SESSION['errors'] = 'Le champs a bien été supprimé.';
		}else{
			$_SESSION['errors'] = 'Aucun champs avec cet id. Le champs n\'a pas été supprimé.';
		}
		
		unset($_POST);
		header("Location: ".URL_ROOT.$lang2."/".$_GET['page'].".html");
	}
	
	function getArgs($table) {
		if(isset($_GET['argc'])) return " AND {$table}.slug_{$this->lang3} = '".$_GET['argc']."'";
		elseif(isset($_GET['argb'])) return " AND {$table}.slug_{$this->lang3} = '".$_GET['argb']."'";
		elseif(isset($_GET['arga'])) return " AND {$table}.slug_{$this->lang3} = '".$_GET['arga']."'";
		else return '';
	}
	
	function writePrettyDate($date){
		
		$returnDate = NULL;
		
		$date = explode(',', $date);
		
		$firstDate = explode('-', $date[0]);
		$firstDateMonth = $this->writePrettyMonth($firstDate[1]);
		$firstDateSend = $firstDate[2].' '.$firstDateMonth.' '.$firstDate[0];
		if(count($date) == 1){
			$returnDate = '' . $firstDateSend;
		}else {
			$secondDate = explode('-', $date[1]);
			$secondDateMonth = $this->writePrettyMonth($secondDate[1]);
			$secondDateSend = $secondDate[2].' '.$secondDateMonth.' '.$secondDate[0];
						
			// Only one date
			if($firstDate==$secondDate) $returnDate = '' . $firstDateSend;
			else {
				// Two dates of the same year
				if($firstDate[0]==$secondDate[0]) $returnDate = $firstDate[2].' '.$firstDateMonth.' au '.$secondDate[2].' '.$secondDateMonth.' '.$secondDate[0];
				// Two dates of the same month
				if($firstDate[1]==$secondDate[1]) $returnDate = $firstDate[2].' au '.$secondDate[2].' '.$secondDateMonth.' '.$secondDate[0];
				// DEFAULT 
				if($returnDate==NULL) $firstDateSend.' au '.$secondDateSend;	 
			}
			
		}
		
		return $returnDate;
	}
	
	function writePrettyMonth($month)
	{
		switch ($month) {
			case '01':
				$month = 'janvier';
			break;
			case '02':
				$month = 'février';
			break;
			case '03':
				$month = 'mars';
			break;
			case '04':
				$month = 'avril';
			break;
			case '05':
				$month = 'mai';
			break;
			case '06':
				$month = 'juin';
			break;
			case '07':
				$month = 'juillet';
			break;
			case '08':
				$month = 'août';
			break;
			case '09':
				$month = 'septembre';
			break;
			case '10':
				$month = 'octobre';
			break;
			case '11':
				$month = 'novembre';
			break;
			case '12':
				$month = 'décembre';
			break;
		}
		
		return $month;	
	}
	
}
?>