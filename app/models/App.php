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
		
		# Allow MySQL to query in utf8 encoding
		$db->Execute("SET NAMES utf8");

		$this->db = $db;
		$this->lang2 = $lang2;
		$this->lang3 = $lang3;
		$this->table = $table;
		$this->table_code = $table_code;
		
		if(DEBUG==true)
		{
			if($_SERVER['REMOTE_ADDR']===IP_ADDRESS)
			{
				error_reporting(E_ERROR | E_WARNING | E_PARSE);
				ini_set("display_errors", 1);
				$db->debug = true;
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
    function select($table)
    {

		if($table)
		{			
			$data = array();
			$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
			$this->table_rows = "";
			
			$q = "SHOW COLUMNS FROM {$table}";
			$rsColumns = $this->db->Execute($q);
			
			# Select rows names
			$this->i = 1;
			while(!$rsColumns->EOF){
				
				# Put fields in an array
				$this->a_table_rows[$this->i] = $table.".".$rsColumns->fields["Field"]." AS ".$table."_".$rsColumns->fields["Field"];
				
				# Check if active field exist
				if($rsColumns->fields["Field"]=='active') $this->table_active = true;

				# Put fields in a string
				$this->table_rows .= $table.".".$rsColumns->fields["Field"]." AS ".$table."_".$rsColumns->fields["Field"];;
				#if($this->i==1) $this->table_rows = $table.".".$rsColumns->fields["Field"]; else $this->table_rows .= $table.".".$rsColumns->fields["Field"];
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
			$rsColumns = $this->db->Execute($q);
			
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
		if($this->limit) unset($this->limit);
		if($this->table_rows) unset($this->table_rows);
		if($this->a_table_rows) unset($this->a_table_rows);

        return $data;
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
			$rsList = $db->Execute($q);
		
			$_SESSION['errors'] = 'Le champs a bien été supprimé.';
		}else{
			$_SESSION['errors'] = 'Aucun champs avec cet id. Le champs n\'a pas été supprimé.';
		}

	}

	# writePrettyDate()
	# @access public
	# @param string $date
	# @return readable date
	public function writePrettyDate($date){
		
		if($this->lang3=="fre") $word_link = "au"; else $word_link = "to";
		$return_date = NULL;
		
		$date = explode(',', $date);
		
		$first_date = explode('-', $date[0]);
		$first_date_day = (int)$first_date[2];
		$first_date_month = $this->writePrettyMonth($first_date[1]);
		$first_date_year = $first_date[0];
		
		if($this->lang3=="fre" && $first_date_day==1) $first_date_day = $first_date_day."<sup>er</sup>";
		
		$first_date_send = $first_date_day." ".$first_date_month." ".$first_date_year;
		
		if(count($date) == 1){
			$return_date = $first_date_send;
		}else {
			$second_date = explode('-', $date[1]);
			$second_date_day = (int)$second_date[2];
			$second_date_month = $this->writePrettyMonth($second_date[1]);
			$second_date_year = $second_date[0];
			$second_dateSend = $second_date_day." ".$second_date_month." ".$second_date_year;
			
			if($this->lang3=="fre" && $second_date_day==1) $second_date_day = $second_date_day."<sup>er</sup>";
			
			# Only one date
			if($first_date==$second_date)
			{
				$return_date = $first_date_send;
			}else {
				if($this->lang3=="fre")
				{
					# French formatting
					
					# Two dates of the same year
					if($first_date_year == $second_date_year) $return_date = $first_date_day." ".$first_date_month." $word_link ".$second_date_day." ".$second_date_month." ".$second_date_year;
					# Two dates of the same month
					if($first_date_month === $second_date_month) $return_date = $first_date_day." $word_link ".$second_date_day." ".$second_date_month." ".$second_date_year;
					
					# Two dates of different year
					if($first_date_year != $second_date_year) $return_date = $first_date_day." ".$first_date_month." ".$first_date_year." $word_link ".$second_date_day." ".$second_date_month." ".$second_date_year;
					
					# Default
					if($return_date==NULL) $first_date_send." $word_link ".$second_dateSend;
					
				}elseif($this->lang3=="eng"){
					# English formatting
					
					# Two dates of the same year
					if($first_date_year == $second_date_year) $return_date = $first_date_month." ".$first_date_day." $word_link ".$second_date_month." ".$second_date_day.", ".$second_date_year;
					# Two dates of the same month
					if($first_date_month === $second_date_month) $return_date = $second_date_month." ".$first_date_day." $word_link ".$second_date_day.", ".$second_date_year;
					
					# Two dates of different year
					if($first_date_year != $second_date_year) $return_date = $first_date_month." ".$first_date_day.", ".$first_date_year." $word_link ".$second_date_month." ".$second_date_day.", ".$second_date_year;
					
					# Default 
					if($return_date==NULL) $first_date_send." $word_link ".$second_dateSend;	 
				}
			}
		}
		
		return $return_date;
	}

	# writePrettyMonth()
	# @access public
	# @param string $month
	# @return readable montb
	public function writePrettyMonth($month)
	{
		global $lang3;
		
		if($this->lang3=="fre")
		{
			switch ($month) {
				case "01":
					$month = "janvier";
				break;
				case "02":
					$month = "février";
				break;
				case "03":
					$month = "mars";
				break;
				case "04":
					$month = "avril";
				break;
				case "05":
					$month = "mai";
				break;
				case "06":
					$month = "juin";
				break;
				case "07":
					$month = "juillet";
				break;
				case "08":
					$month = "août";
				break;
				case "09":
					$month = "septembre";
				break;
				case "10":
					$month = "octobre";
				break;
				case "11":
					$month = "novembre";
				break;
				case "12":
					$month = "décembre";
				break;
			}
		}elseif($this->lang3=="eng"){
			switch ($month) {
				case "01":
					$month = "January";
				break;
				case "02":
					$month = "February";
				break;
				case "03":
					$month = "March";
				break;
				case "04":
					$month = "April";
				break;
				case "05":
					$month = "May";
				break;
				case "06":
					$month = "June";
				break;
				case "07":
					$month = "July";
				break;
				case "08":
					$month = "August";
				break;
				case "09":
					$month = "September";
				break;
				case "10":
					$month = "October";
				break;
				case "11":
					$month = "November";
				break;
				case "12":
					$month = "December";
				break;
			}
		}
		
		return $month;	
	}
	
	# limitStringSize()
	# @access public
	# @param string $string, $size
	# @return cropped string
	public function limitStringSize($string, $size=200)
	{
		$pos = strpos($string, ' ', $size);
		$cropped_string = substr($string,0,$pos);
		if(strlen($cropped_string) >= $size) $string = $cropped_string;
		return $string;
	}
	
	# format_money()
	# @access public
	# @param string $number
	# @return formatted number
	public function format_money($number)
	{
		setlocale(LC_MONETARY, "fr_CA");
		if($this->lang3=="fre") $number = money_format('%!.0n', $number)." $";
		else $number = money_format('$ %!.0n', $number);
		return $number;
	}
	
	# getPicturePath()
	# @access public
	# @param string $string
	# @return picture path
	public function getPicturePath($string){
		$a = explode("::", $string);
		return $a[0];
	}
	
	# getPictureInfo()
	# @access public
	# @param string $string
	# @return array() of picture infos
	function getPictureInfo($string){
		$a = explode('::', $string);
		return array('file' => $a[0], 'cropdata' => $a[1]);
	}
	
	# nl2p()
	# @access public
	# @param string $string, $line_breaks, $xml
	# @return trimmed string
	function nl2p($string, $line_breaks = true, $xml = true) {

		$string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);
		
		# It is conceivable that you might still want single line-breaks without breaking into a new paragraph.
		if ($line_breaks == true)
		    return '<p>'.preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'), trim($string)).'</p>';
		else 
		    return '<p>'.preg_replace(
		    array("/([\n]{2,})/i", "/([\r\n]{3,})/i","/([^>])\n([^<])/i"),
		    array("</p>\n<p>", "</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'),
		
		    trim($string)).'</p>'; 
	}
	
	# compact_list()
	# @access public
	# @param string $a, $field
	# @return $a_compact
	function compact_list($a, $field){
		$a_compact = array();
		foreach($a as $row){
			$a_compact[] = $row[$field];
		}
		return $a_compact;
	}
}
