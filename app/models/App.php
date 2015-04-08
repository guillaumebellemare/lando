<?php

/*
|--------------------------------------------------------------------------
| App Model
|--------------------------------------------------------------------------
|
|
|
*/

class App extends SluggedRecord {
	
	private $from;
	private $where = array();
	private $group_by = array();
	private $order_by = array();
	private $joined_table;
	private $joined_table_code;
	private $joined_statement;
	private $joined_table_active;
	private $limit;
	private $table_rows;
	private $a_table_rows = array();

	public function __construct($table=NULL) {
		
		global $lang3;
		
		# Create ADO object & connect to the database
		$db = ADONewConnection(DB_TYPE);
		$db->Connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
		
		# Allow MySQL to query in utf8 encoding
		$db->Execute("SET NAMES utf8");

		$this->db = $db;
		$this->lang3 = $lang3;
		$this->table = $table;
		
		if(DEBUG==true)
		{
			if($_SERVER['REMOTE_ADDR']===IP_ADDRESS)
			{
				error_reporting(E_ERROR | E_WARNING | E_PARSE);
				ini_set("display_errors", 1);
				//$db->debug = true;
			}
		}

	}

	# select()
	# @access public
	# @param string $table
	# @return $this
    function select($table)
    {

		if($table)
		{			
			$data = array();
			$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
	
			$q = "SHOW COLUMNS FROM {$table}";
			$rsColumns = $this->db->Execute($q);
			
			# Select rows names
			$this->i = 1;
			while(!$rsColumns->EOF){
				
				# Put fields in an array
				$this->a_table_rows[$this->i] = $table.".".$rsColumns->fields["Field"]." AS {$table}_{$rsColumns->fields['Field']}";
				
				# Put fields in a string
				if($this->i==1) $this->table_rows = $table.".".$rsColumns->fields["Field"]." AS {$table}_{$rsColumns->fields['Field']}"; else $this->table_rows .= $table.".".$rsColumns->fields["Field"]." AS {$table}_{$rsColumns->fields['Field']}";
				if($rsColumns->RecordCount()!=$this->i) $this->table_rows .= ", ";
				
				$this->i++;
			$rsColumns->MoveNext();
			}
			$rsColumns->Close();
		}
		
        return $this;
    }
	
	# left_join()
	# @access public
	# @param string $joined_table
	# @return $this
	function left_join($joined_table)
	{
		
       if($joined_table)
	   {
			$this->joined_table = $joined_table;
			$this->joined_table_code = rtrim($joined_table, "s");
			
			$called_class = get_called_class();
			$called_class_init = new $called_class();
		    if(method_exists($called_class_init, $joined_table))
			{
				$this->joined_statement .= " LEFT JOIN {$this->joined_table} ".$called_class_init->{$joined_table}();
			}else{
				$this->joined_statement .= " LEFT JOIN {$this->joined_table} ON {$this->table}.{$this->joined_table_code}_id = {$this->joined_table}.id";
			}
			
			$q = "SHOW COLUMNS FROM {$joined_table}";
			$rsColumns = $this->db->Execute($q);
			
			# Select rows names
			while(!$rsColumns->EOF){
				
				# Put fields of the joined table in the array
				$current_field = $rsColumns->fields["Field"];
				$this->a_table_rows[$this->i] = $joined_table.".".$current_field." AS {$joined_table}_{$current_field}";

				# Put fields in a string
				if($rsColumns->RecordCount()!=$this->i) $this->table_rows .= ", ";
				$this->table_rows .= $joined_table.".".$current_field." AS {$joined_table}_{$current_field}";
				if($current_field=='active') $this->joined_table_active .= " AND {$joined_table}.active = 1";
				
				$this->i++;
			$rsColumns->MoveNext();
			}
			$rsColumns->Close();
	    }

		return $this;
	}
	
	# oneToMany()
	# @access public
	# @param string $called_class, $foreign_key, $primary_key
	# @return $this
	function oneToMany($foreign_key, $primary_key)
	{
		return "ON {$foreign_key} = {$primary_key}";
	}
	
	# where()
	# @access public
	# @param string $where
	# @return $this
    function where($where)
    {
       if($where) $this->where[] = $where;
       return $this;
    }
	
	# group_by()
	# @access public
	# @param string $where
	# @return $this
    function group_by($group)
    {
       if($group) $this->group_by[] = $group;
       return $this;
    }
	
	# order_by()
	# @access public
	# @param string $order_by
	# @return $this
    function order_by($order_by=NULL)
    {
       if($order_by) $this->order_by[] = $order_by;
       return $this;
    }
	
	# limit()
	# @access public
	# @param string $limit
	# @return $this->all()
	function limit($limit)
	{
		if($limit) $this->limit = $limit;
		return $this->all();
	}
	
	# all()
	# @access public
	# @return array()
	function all()
    {		
		global $app_messages;
		
		$q = "SELECT {$this->table_rows} FROM {$this->table}";
		if($this->joined_statement) $q .= $this->joined_statement;
		$q .= " WHERE ";
		if($this->where) 
		{
			foreach($this->where as $where_statement)
			{
				$q .= "{$where_statement} AND ";
			}
		}
		$q .= " {$this->table}.active = 1";
		$q .= $this->joined_table_active;
		if($this->group_by) 
		{
			$q .= " GROUP BY ";
			$i = 0;
			foreach($this->group_by as $group_statement)
			{
				if($i!=0) $q .= ", ";
				$q .= "{$group_statement}";
				$i++;
			}
		}
		if($this->order_by) 
		{
			$q .= " ORDER BY ";
			$i = 0;
			foreach($this->order_by as $order_statement)
			{
				if($i!=0) $q .= ", ";
				$q .= "{$order_statement}";
				$i++;
			}
		}
		if($this->limit) $q .= " LIMIT {$this->limit}";

		$rsList = $this->db->Execute($q);
		
		# Put all data in an array
		$i = 0;
		
		while(!$rsList->EOF){
			$data[$i] = array();

			foreach($this->a_table_rows as $table_row)
			{
				if(explode('AS', $table_row)) $table_rows = explode('AS', $table_row);
				$raw_field_name = str_replace(' ', '', $table_rows[0]);
				$as_field_name = str_replace(' ', '', $table_rows[1]);
				$data[$i][$raw_field_name] = $rsList->fields[$as_field_name];
			}
			$i++;
			
		$rsList->MoveNext();
		}
		$rsList->Close();

		if($_SERVER['REMOTE_ADDR']===IP_ADDRESS && DEBUG==true) $app_messages[] = "<hr class='app-hr'><span class='app-query'>$q</span><br>";
		
		if($this->from) unset($this->from);
		if($this->where) unset($this->where);
		if($this->group_by) unset($this->group_by);
		if($this->order_by) unset($this->order_by);
		if($this->joined_table) unset($this->joined_table);
		if($this->joined_table_code) unset($this->joined_table_code);
		if($this->limit) unset($this->limit);
		if($this->table_rows) unset($this->table_rows);
		if($this->a_table_rows) unset($this->a_table_rows);

        return $data;
    }
	
	# Arguments = Raw Query
	function raw_select($rawQuery){
		
		$data = array();
		$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
		
		$rsList = $this->db->Execute($rawQuery);
		
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

	# left_join_old()
	# @access public
	# @param string $joined_table
	# @return $this
	function left_join_old($joined_table)
	{
		
       if($joined_table)
	   {
		   $this->joined_table = $joined_table;
		   $this->joined_table_code = rtrim($joined_table, "s");
		   
			$q = "SHOW COLUMNS FROM {$joined_table}";
			$rsColumns = $this->db->Execute($q);
			
			# Select rows names
			while(!$rsColumns->EOF){
				
				# Put fields of the joined table in the array
				$current_field = $rsColumns->fields["Field"];
				$this->a_table_rows[$this->i] = $joined_table.".".$current_field;
	
				# Put fields in a string
				if($rsColumns->RecordCount()!=$this->i) $this->table_rows .= ", ";
				$this->table_rows .= $joined_table.".".$current_field;
				
				$this->i++;
			$rsColumns->MoveNext();
			}
			$rsColumns->Close();
	    }

		return $this;
	}
	
	function add($table) {
		
		global $lang2;
		global $db;
		global $messages, $errors;
		
		$q = "SELECT * FROM {$table}";
		
		# Check for data that already exist in the database
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
			$_SESSION['messages'] = "L'ajout a bien été effectué.";
		}/*elseif($_POST) {
			$_SESSION['errors'] = "Valeur déjà entrée.";
		}*/
		if(isset($_POST)) unset($_POST);
		header("Location: ".URL_ROOT.$lang2."/".$_GET['page']."");

		return true;
	}
	
	function delete($table, $row_id) {
	
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
		
		if(isset($_POST)) unset($_POST);
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
		$firstDateSend = ltrim($firstDate[2], '0').' '.$firstDateMonth.' '.$firstDate[0];
		if(count($date) == 1){
			$returnDate = '' . $firstDateSend;
		}else {
			$secondDate = explode('-', $date[1]);
			$secondDateMonth = $this->writePrettyMonth($secondDate[1]);
			$secondDateSend = ltrim($secondDate[2], '0').' '.$secondDateMonth.' '.$secondDate[0];
						
			# Only one date
			if($firstDate==$secondDate) $returnDate = '' . $firstDateSend;
			else {
				# Two dates of the same year
				if($firstDate[0]==$secondDate[0]) $returnDate = ltrim($firstDate[2], '0').' '.$firstDateMonth.' au '.ltrim($secondDate[2], '0').' '.$secondDateMonth.' '.$secondDate[0];
				# Two dates of the same month
				if($firstDate[1]==$secondDate[1]) $returnDate = ltrim($firstDate[2], '0').' au '.ltrim($secondDate[2], '0').' '.$secondDateMonth.' '.$secondDate[0];
				# DEFAULT 
				if($returnDate==NULL) ltrim($firstDateSend, '0').' au '.ltrim($secondDateSend, '0');	 
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
