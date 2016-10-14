<?php

class AppModel {
	
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
		$lang = new Lang();
		$this->lang2 = $lang->lang2;
		$this->lang3 = $lang->lang3;
		$this->lang2_trans = $lang->lang2_trans;
		$this->lang3_trans = $lang->lang3_trans;
		$this->lang_trans_complete = $lang->lang_trans_complete;
		$this->possible_languages = $lang->possible_languages;
		
		$this->db = AppConnection::getInstance();
		$this->table = $table;
		$this->table_code = $table_code;
		
		if(DEBUG && $_SERVER['REMOTE_ADDR']===IP_ADDRESS) $this->db->debug = true;
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
			$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
			$this->table_rows = "";
			
			#$table_exist = $this->db->Execute("SELECT * FROM information_schema.TABLES WHERE TABLE_NAME = '{$table}' AND TABLE_SCHEMA = '".DB_NAME."'");

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
	# slug()
	# Convert a string to a slug (by removing spaces, special characters, etc.)
	# @access public
	# @param $str
	# @return converted string
	function slug($str) {
	    
		# Special accents
	    $a = array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','Ð','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','?','?','J','j','K','k','L','l','L','l','L','l','?','?','L','l','N','n','N','n','N','n','?','O','o','O','o','O','o','Œ','œ','R','r','R','r','R','r','S','s','S','s','S','s','Š','š','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Ÿ','Z','z','Z','z','Ž','ž','?','ƒ','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','?','?','?','?','?','?');
	    $b = array('A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','O','U','U','U','U','Y','s','a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','l','l','N','n','N','n','N','n','n','O','o','O','o','O','o','OE','oe','R','r','R','r','R','r','S','s','S','s','S','s','S','s','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Y','Z','z','Z','z','Z','z','s','f','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','A','a','AE','ae','O','o');
	    
		return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/','/[ -]+/','/^-|-$/'),array('','-',''),str_replace($a,$b,$str)));
	}
	
	# check_slug()
	# @access public
	# @param $source_field, $slug_field
	# @return void
	function check_slug($source_field,  $slug_field) {
		
		# Create slug for  
		$rs = $this->db->Execute("SELECT id, $source_field, $slug_field FROM {$this->table} WHERE $slug_field = '' AND active = 1");
		
		while(!$rs->EOF){
			$this->db->Execute("UPDATE {$this->table} SET $slug_field = '" . $this->slug(strip_tags($rs->fields($source_field)), '-') . "' WHERE id = ".$rs->fields('id').""); 
			$rs->MoveNext();
		}
		$rs->Close();
	}
	
	# createSlugField()
	# Create slug field in the db table and in the ZAP interface
	# This field will be automatically filled with the source field (all that while converting the source field in a valid URL - slug)
	# @access public
	# @param $code, $source_field, $slug_field, $slug_label
	# @return void
	function createSlugField($source_field,  $slug_field = NULL,  $slug_label = NULL) {

		if(!$slug_field) $slug_field = "slug_$this->lang3";
		if(!$slug_label) $slug_label = "URL Slug - $this->lang2";
		
		$rsField = $this->db->Execute('SELECT id FROM fields WHERE type = "form/slug"');
		$field_id = $rsField->fields['id'];
		$rsField->Close();
		$rs = $this->db->Execute('SELECT pages.id FROM pages WHERE code_name = "' . $this->table_code . '"');
		$page_id = $rs->fields('id');
		$rs->Close();
		$rs = $this->db->Execute('SELECT pages_fields.id FROM pages_fields WHERE name = "' . $slug_field . '" AND page_id = ' . $page_id);

		if(!$rs->RecordCount()){
			$this->db->Execute("INSERT INTO `pages_fields` ( `page_id`, `field_id`, `classdef`, `list_display_style`, `display_in_list`, `active`, `rank`, `name`, `label_fre`, `label_eng`, `lang_specific`, `specs`, `rules_fre`, `rules_eng`) VALUES
($page_id, $field_id, '', '', 0, 1, 60, '$slug_field', '$slug_label', '$slug_label', 1, '', '', '')");

			$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
			$q = "SHOW COLUMNS FROM {$this->table}";
			$rsColumns = $this->db->Execute($q);
			$is_already_setted = false;
			while(!$rsColumns->EOF){
				$current_field = $rsColumns->fields["Field"];
				if("$current_field"=="$slug_field") $is_already_setted = true;
				//echo $current_field." ".$slug_field."<br>";
			$rsColumns->MoveNext();
			}
			$rsColumns->Close();

			if(!$is_already_setted) $this->db->Execute("ALTER TABLE {$this->table} ADD $slug_field varchar(255) NOT NULL");
		}
		
		$this->check_slug($source_field, $slug_field);
	}
	
	# get_from_slug()
	# Cette fonction renvoie l'ID à partir d'un slug donné. 
	# @access public
	# @param string $slug_field Le nom du champ slug.
	# @param string $slug_value La valeur du champ slug (provient généralement de l'URL)
	# @param string $source_field (default: null) Le nom du champ source à partir duquel est construit le slug (en cas de slug non défini)
	# @param int $trans (default: 1) Défini si on doit aller chercher la valeur du champ slug traduit
	#                               (généralement utile pour les boutons "english/français" pour passer d'une langue à l'autre tout en restant sur la même page. 
	# @return L'ID de l'enregistrement associé au slug.
	function get_from_slug ($slug_field, $slug_value, $source_field = null, $trans = 1) {
		$this->setComplexWhere();
		$this->getLangTrans($lang2, $lang3);
		if($source_field) {
			$this->check_slug($source_field, $slug_field);
			if($trans == 1)$this->check_slug(str_replace($this->lang3, $lang3, $source_field), str_replace($this->lang3, $lang3, $slug_field));
		}
		if($trans == 1)$slug_trang = "slug_$lang3";
		else $slug_trans = $slug_field;
		$rs = $this->db_temp->Execute("SELECT id, $slug_field AS slug_trans FROM {$this->table} WHERE $slug_field = '$slug_value' AND active = 1{$this->complex_where}" );
		if($rs && $rs->RecordCount()){
			$this->current_id = $rs->fields('id');
			$this->current_slug = $slug_value;
			$this->current_slug_trans = $rs->fields('slug_trans');
		} else $this->current_id =false;
		return $this->current_id;
	}
	
	# getLangTrans()
	# Permet d'obtenir la langue de traduction (valide pour un site bilingue).
	# Si c'est français, ça retourne les code en anglais (en, eng), si c'est anglais, ça retourne les codes en français (fr, fre).
	# @access public
	# @param string &$lang2
	# @param string &$lang3
	# @return void
	function getLangTrans(&$lang2,  &$lang3) {
		if($this->lang3 == 'fre'){
			$lang2 = 'en';
			$lang3 = 'eng';
		}else{
			$lang2 = 'fr';
			$lang3 = 'fre';
		}
	}
	
	
	function getCodeFromTable(){
		
	}

}
