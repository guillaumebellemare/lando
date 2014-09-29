<?php
require_once("classes/sluggedrecord.class.php");

class Search extends SluggedRecord {
	
	var $table = 'searchs';	
	var $sortBy = ' ORDER BY rank';
	var $db = null;	
	
	var $name;

	
	function __construct($db, $lang3){
		$this->name = 'search';
		parent::__construct($db, $lang3, $this->table);
	}
	
	function getCurrent(){
		$this->rsCurrent = $this->db->Execute("SELECT id, name_{$this->lang3} AS name, slug_{$this->lang3} AS slug FROM {$this->table} WHERE active = 1 AND id = {$this->current_id} ORDER BY rank LIMIT 1");
	}
	
	function getResults($aFields=NULL, $terms=NULL) {
		
		$table = 'matable';
		$aFields = array(array("autos","name"),array("autos","couleur"),array("camions","name"));
		$terms = 'mon champs de recherche';
		$out = '';
		
		$i = 0;		
		$normalized_terms = NULL;
		if($terms) $normalized_terms = $this->normalize($terms);
		$q = "SELECT $table.id FROM $table";
		$q .= " WHERE active = 1";
		/*foreach ($aFields as &$value) {
			if($i==0) $q .= " AND"; else $q .= " OR";
    		$q .= " $table.$value LIKE '%$normalized_terms%'";
			$i++;
		}*/
		for ($row = 0; $row < 3; $row++)
		{
			for ($col = 1; $col < 2; $col++)
			{
				if($i==0) $q .= " AND"; else $q .= " OR";
				$q .= " ".$aFields[$row][0].".".$aFields[$row][$col]." LIKE  '%$normalized_terms%'";
				$i++;
			}
		}
		$q .= " ORDER BY rank";
		$out .= $q;
		/*$rsList = $this->db->Execute($q);

		if($rsList->RecordCount())
		{
			while(!$rsList->EOF){
			
				$out .= $rsList->fields["id"];
			
			$rsList->MoveNext();
			}
			$rsList->Close();
		}else{
			$out = '<p>Désolé mais aucun résultat ne correspond à vos critères de recherche.</p>';	
		}*/
		
		return $out;
	}
	
	function normalize($str){
		$normalizeChars = array( 
            'Á'=>'A', 'À'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Å'=>'A', 'Ä'=>'A', 'Æ'=>'AE', 'Ç'=>'C', 
            'É'=>'E', 'È'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Í'=>'I', 'Ì'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ð'=>'Eth', 
            'Ñ'=>'N', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 
            'Ú'=>'U', 'Ù'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 
    
            'á'=>'a', 'à'=>'a', 'â'=>'a', 'ã'=>'a', 'å'=>'a', 'ä'=>'a', 'æ'=>'ae', 'ç'=>'c', 
            'é'=>'e', 'è'=>'e', 'ê'=>'e', 'ë'=>'e', 'í'=>'i', 'ì'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'eth', 
            'ñ'=>'n', 'ó'=>'o', 'ò'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 
            'ú'=>'u', 'ù'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 
            
            'ß'=>'sz', 'þ'=>'thorn', 'ÿ'=>'y' 
        ); 
        return strtr($str, $normalizeChars);
	}
		
	function highlightMatches($stack, $needle, $uc = false){
		$normalized_stack = $this->normalize($stack);
		$normalized_needle = $this->normalize($needle);

		if(mb_strripos($normalized_stack, $normalized_needle) !== FALSE){
			$results++;
			$sPos = mb_strripos($normalized_stack, $normalized_needle);
			$sLength = strlen($normalized_needle);
			if($uc){
				if($sPos == 0) return '<span class="is-highlighted">' . ucfirst(mb_substr($stack, $sPos, $sLength)).  '</span>' . mb_substr($stack, $sPos+$sLength);
				else return ucfirst(mb_substr($stack, 0, $sPos) . '<span class="is-highlighted">' . mb_substr($stack, $sPos, $sLength).  '</span>' . mb_substr($stack, $sPos+$sLength));
			}else {
				return (mb_substr($stack, 0, $sPos) . '<span class="is-highlighted">' . mb_substr($stack, $sPos, $sLength).  '</span>' . mb_substr($stack, $sPos+$sLength));
			}
		}else return FALSE;
	}

	function setComplexWhere(){
		if(!$this->complex_where && $this->current_id) $this->complex_where = ' AND id = ' .$this->current_id;
	}

}
?>