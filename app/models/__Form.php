<?php
require_once("app/helpers/sluggedrecord.class.php");

class Form extends SluggedRecord {
	
	var $table = 'forms';	
	var $sortBy = ' ORDER BY rank';
	var $db = null;	
	
	var $name;

	
	function __construct($db, $lang3){
		$this->name = 'form';
		parent::__construct($db, $lang3, $this->table);
	}
	
	function getCurrent(){
		$this->rsCurrent = $this->db->Execute("SELECT id, name_{$this->lang3} AS name, slug_{$this->lang3} AS slug FROM {$this->table} WHERE active = 1 AND id = {$this->current_id} ORDER BY rank LIMIT 1");
	}
	
    function createForm($name, $method='POST', $action='') {
		$out = '';
		
        $out .= "<form action='$action' method='$method' name='$name' id='$name'>";
		$out .= "\n";
		
		return $out;
    }
	
    function closeForm($name, $label, $btName='btSubmit') {
		$out = '';
		
		$out .= "<input name='".$name."Sent' type='hidden' value=''>";
 		$out .= "\n";
		$out .= "<input name='btSubmit' value='$btName' type='submit' id='btSubmit'>";
 		$out .= "\n";
        $out .=	"</form>";
		$out .= "\n";
		
		return $out;
    }

    function input($label, $name , $placeholder=NULL) {
		$out = '';
		$label = ucfirst($label);
		$name = strtolower($name);
		
		$out .= "<label for='$name'>";
			$out .= $label;
			$out .= "<input type='text'";
			$out .= " name='$name'";
			if($placeholder) $out .= " placeholder='$placeholder'";
			$out .= " />";
		$out .=	"</label>";
		$out .= "\n";
		
		return $out;
    }
			
    function textarea($label, $name, $placeholder=NULL) {
		$out = '';
        $label = ucfirst($label);
		$name = strtolower($name);

        $out .= "<label for='$name'>";
			$out .= $label;
			$out .= "<textarea name='$name'>";
				if($placeholder) $out .= "$placeholder";
			$out .=	"</textarea>";
		$out .=	"</label>";
		$out .= "\n";
		
		return $out;
    }

    function radio($label, $name, $options) {
		$out = '';
		$i = 0;
        $label = ucfirst($label);
		$name = strtolower($name);

		$out .= "<label for='$name'>";
			$out .= $label;
		$out .= "</label>";
		$out .= "\n";
		
		foreach ($options as $key => $value) {
			$out .= "<input type='radio' name='$name' value='$value'";
			if($i==0) $out .= ' checked';
			$out .= " id='".$name."_$i'>";
			$out .= $key;
			$i++;
		}
		$out .= "\n";
		
		return $out;
    }

    function select($label, $name, $options) {
		$out = '';
        $label = ucfirst($label);
		$name = strtolower($name);

		$out .= "<label for='$name'>";
			$out .= $label;
		
			$out .= "<select name='$name'>";
			foreach ($options as $key => $value) {
				$out .= "<option value='$value'>";
					$out .= $key;
				$out .= "</option>";
			}
			$out .= "</select>";
		$out .= "</label>";
		$out .= "\n";
		
		return $out;
    }
          
    function checkbox($label, $name, $options) {
		$out = '';
		$i = 0;
        $label = ucfirst($label);
		$name = strtolower($name);

		$out .= "<label for='$name'>";
			$out .= $label;
		$out .= "</label>";
		$out .= "\n";
		
		foreach ($options as $key => $value) {
			$out .= "<input type='checkbox' name='$name' value='$value'";
			$out .= " id='".$name."_$i'>";
			$out .= $key;
			$i++;
		}
		$out .= "\n";
		
		return $out;
    }

	function setComplexWhere(){
		if(!$this->complex_where && $this->current_id) $this->complex_where = ' AND id = ' .$this->current_id;
	}

}
?>