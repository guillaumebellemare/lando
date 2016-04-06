<?php

class App extends SluggedRecord {
	
	private $from;
	private $where = array();
	private $order_by = array();
	private $group_by = array();
	private $joined_table = NULL;
	private $joined_table_code = NULL;
	private $joined_statement = NULL;
	private $joined_table_active = NULL;
	private $table_active;
	private $limit;
	private $table_rows;
	private $a_table_rows = array();

	public function __construct($table = NULL, $table_code = NULL) {
		
		global $lang2, $lang3;
		
		# Create ADO object & connect to the database
		$db = ADONewConnection(DB_TYPE);
		$db->Connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
		$db_temp = ADONewConnection(DB_TYPE);
		$db_temp->Connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
		
		# Allow MySQL to query in utf8 encoding
		$db->Execute("SET NAMES utf8");
		$db_temp->Execute("SET NAMES utf8");

		$this->db = $db;
		$this->db_temp = $db_temp;
		$this->lang2 = $lang2;
		$this->lang3 = $lang3;
		if($lang3=="fre") $this->lang3_trans = "eng"; else $this->lang3_trans = "fre";
		$this->table = $table;
		$this->table_code = $table_code;
		
		if(DEBUG==true)
		{
			if($_SERVER['REMOTE_ADDR']===IP_ADDRESS)
			{
				error_reporting(E_ERROR | E_WARNING | E_PARSE);
				ini_set("display_errors", 1);
				$this->db->debug = true;
			}
		}
		
		if(DEBUG_ALL==true)
		{
			if($_SERVER['REMOTE_ADDR']===IP_ADDRESS)
			{
				error_reporting(E_ERROR | E_WARNING | E_PARSE);
				ini_set("display_errors", 1);
				$this->db_temp->debug = true;
			}
		}

	}
	
	# get()
	# @access public
	# @param $id
	# @return current result
	public function get($id){
		$rs = $this->select($this->table)->where("{$this->table}.id = $id")->all(false);
		return current($rs);
	}

	# select()
	# @access public
	# @param string $table
	# @return $this
    function select($table=NULL)
    {
		if(!$table) $table = $this->table;
		if($table)
		{			
			$data = array();
			$this->db_temp->SetFetchMode(ADODB_FETCH_ASSOC);
			$this->table_rows = "";
			
			#$table_exist = $this->db_temp->Execute("SELECT * FROM information_schema.TABLES WHERE TABLE_NAME = '{$table}' AND TABLE_SCHEMA = '".DB_NAME."'");

			$q = "SHOW COLUMNS FROM {$table}";
			$rsColumns = $this->db_temp->Execute($q);
			
			# Select rows names
			$this->i = 1;
			while(!$rsColumns->EOF){
				
				# Put fields in an array
				$this->a_table_rows[$this->i] = $table.".".$rsColumns->fields["Field"]." AS ".$table."_".$rsColumns->fields["Field"];
				
				# Check if active field exist
				if($rsColumns->fields["Field"]=='active') $this->table_active = true;

				# Put fields in a string
				$this->table_rows .= $table.".".$rsColumns->fields["Field"]." AS ".$table."_".$rsColumns->fields["Field"];;
				if($rsColumns->RecordCount()!=$this->i) $this->table_rows .= ", ";
				
				$this->i++;
				
			$rsColumns->MoveNext();
			}
			$rsColumns->Close();

		}
        return $this;
    }
    
 	# append()
	# @access public
	# @param string $a_rows
	# @return $this
    function append($a_rows){
	    foreach($a_rows as $current_row){
		    $this->a_table_rows[] = $current_row;
		    $this->table_rows .= ', '. $current_row;
	    }
	    return $this;
    }
	
	# left_join()
	# @access public
	# @param string $joined_table
	# @return $this
	function left_join($joined_table, $force_active = true)
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
			$rsColumns = $this->db_temp->Execute($q);
			
			# Select rows names
			while(!$rsColumns->EOF){
				
				# Put fields of the joined table in the array
				$current_field = $rsColumns->fields["Field"];
				$this->a_table_rows[$this->i] = $joined_table.".".$current_field." AS ".$joined_table."_".$rsColumns->fields["Field"];;
	
				# Put fields in a string
				$this->table_rows .= ", ";
				$this->table_rows .= $joined_table.".".$current_field." AS ".$joined_table."_".$rsColumns->fields["Field"];;
				if($current_field=='active' && $force_active) $this->joined_table_active .= " AND {$joined_table}.active = 1";
				
				$this->i++;
			$rsColumns->MoveNext();
			}
			$rsColumns->Close();
	    }

		return $this;
	}
	
	# oneToMany()
	# @access public
	# @param string $foreign_key, $primary_key
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
       if($where) $this->where[] = "($where)";
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
	
	# group_by()
	# @access public
	# @param string $group_by
	# @return $this
    function group_by($group_by=NULL)
    {
       if($group_by) $this->group_by[] = $group_by;
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
	function all($force_active = true)
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
		
		if($this->table_active && $force_active) $q .= " {$this->table}.active = 1"; else $q .= " 1"; 
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
		/*include(COMPLETE_URL_ROOT .'zap/lib/php/adodb5/tohtml.inc.php'); # load code common to ADOdb 
		rs2html($rsList,'border=2 cellpadding=3');*/

		# Put all data in an array
		$i = 0;

		while(!$rsList->EOF){
			
			$data[$i] = array();
			
			foreach($this->a_table_rows as $table_row)
			{
				if(explode(' AS ', $table_row)) list($complete_field, $as_field) = explode(' AS ', $table_row);
				if(explode('.', $table_row)) list($table_name, $table_field) = explode('.', $table_row);
				if(strstr($as_field, 'special_')){
					$table_row = (explode('special_', $as_field));
					$complete_field = 'special.'.$table_row[1];
				}else{
					$table_row = explode(".", $table_row);
					$table_row_complete = $table_row[0].'.'.$table_row[1];
				}

				$data[$i][$complete_field] = $rsList->fields[$as_field];
			}
			$i++;
			
		$rsList->MoveNext();
		}
		$rsList->Close();

		//if($_SERVER['REMOTE_ADDR']===IP_ADDRESS) $app_messages[] = "<hr class='app-hr'><span class='app-query'>$q</span><br>";
		
		if($this->from) unset($this->from);
		if($this->where) unset($this->where);
		if($this->order_by) unset($this->order_by);
		if($this->joined_table) unset($this->joined_table);
		if($this->joined_statement) unset($this->joined_statement);
		if($this->joined_table_active) unset($this->joined_table_active);
		if($this->joined_table_code) unset($this->joined_table_code);
		if($this->table_rows) unset($this->table_rows);
		if($this->a_table_rows) unset($this->a_table_rows);

		if($this->limit && $this->limit === 1)
		{
			if($this->limit) unset($this->limit);
			return current($data);
		}else{
			if($this->limit) unset($this->limit);
        	return $data;
		}
    }
	
	# raw_query()
	# @access public
	# @param $raw_query
	# @return array()
	function raw_query($raw_query){
		
		$data = array();
		$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$rsList = $this->db->Execute($raw_query);
		
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
	
	# insert()
	# @access public
	# @param $record
	# @return @void
	public function insert(&$record){
		$this->db->AutoExecute($this->table, $record, 'INSERT'); 
		$record['id'] = $this->db->insert_id();
		return true;
	}
	
	# update()
	# @access public
	# @param $record, $id
	# @return @void
	public function update(&$record, $id){
		return $this->db->AutoExecute($this->table, $record, 'UPDATE', 'id = ' . $id); 
	}
	
	# raw_update()
	# @access public
	# @param $raw_update
	function raw_update($raw_query){
		
		$this->db->Execute($raw_query);
	}

	# delete()
	# @access public
	# @param $table, $clause
	# @return @void
	function delete($table, $clause) {
	
		global $db;
		global $messages, $errors;
		if(!isset($table))$table = $this->table;
		$q = "SELECT * FROM {$table} WHERE {$clause}";
		$rsList = $this->db->Execute($q);
		if($rsList->RecordCount()!=0)
		{
			$q = "DELETE FROM {$table}";
			$q .= " WHERE {$clause}";
			$rsList = $this->db->Execute($q);
		
			$_SESSION['errors'] = 'Le champs a bien été supprimé.';
		}else{
			$_SESSION['errors'] = 'Aucun champs avec cet id. Le champs n\'a pas été supprimé.';
		}

	}


}
