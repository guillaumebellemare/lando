<?php
require_once("classes/record.class.php");
/**
 * SluggedRecord class.
 */
class SluggedRecord extends Record {

	var $source_field;
	var $slug_field;

	var $current_slug;
	
	/**
	 * slug function.
	 * Converti une chaine en slug (en éliminant les espaces, caractères spéciaux ou accentués, etc.)
	 * @access public
	 * @param mixed $str La chaine à convertir.
	 * @return La chaîne convertie.
	 */
	function slug($str) {
	    # special accents
	    $a = array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','Ð','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','?','?','J','j','K','k','L','l','L','l','L','l','?','?','L','l','N','n','N','n','N','n','?','O','o','O','o','O','o','Œ','œ','R','r','R','r','R','r','S','s','S','s','S','s','Š','š','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Ÿ','Z','z','Z','z','Ž','ž','?','ƒ','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','?','?','?','?','?','?');
	    $b = array('A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','O','U','U','U','U','Y','s','a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','l','l','N','n','N','n','N','n','n','O','o','O','o','O','o','OE','oe','R','r','R','r','R','r','S','s','S','s','S','s','S','s','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Y','Z','z','Z','z','Z','z','s','f','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','A','a','AE','ae','O','o');
	    return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/','/[ -]+/','/^-|-$/'),array('','-',''),str_replace($a,$b,$str)));
	}
	
	/**
	 * check_slug function.
	 * 
	 * @access public
	 * @param string $source_field Le nom du champ source (ex: name_fre, name, etc.)
	 * @param string $slug_field Le nom du champ slug (ex: slug_fre, slug, etc.)
	 * @return void
	 */
	function check_slug ($source_field,  $slug_field) {
		//create slug for  
		$rs = $this->db->Execute("SELECT id, $source_field, $slug_field FROM {$this->table} WHERE $slug_field = '' AND active = 1");
		while(!$rs->EOF){
			$this->db->Execute("UPDATE {$this->table} SET $slug_field = '" . $this->slug(strip_tags($rs->fields($source_field)), '-') . "' WHERE id = ".$rs->fields('id').""); 
			$rs->MoveNext();
		}
		$rs->Close();
	}
	
	/**
	 * create_slug_field function.
	 * Cette fonction permet de créer le champ slug dans la table de la base de données mais aussi dans l'interface d'administration ZA. 
	 * Le champ sera automatiquement rempli à partir du champ source (tout en convertissant la chaine du champ source en une chaine URL valide (slug)
	 * 
	 * @access public
	 * @param string $code Le nom de code la page (tel que nommé dans l'admin)
	 * @param string $source_field Le nom du champ source 
	 * @param string $slug_field Le nom du champ slug à créer
	 * @param string $slug_label Le label du champ slug (tel qu'il s'affichera dans la zone admin. 
	 * @return void
	 */
	function create_slug_field ($code,  $source_field,  $slug_field,  $slug_label) {
		$rsField = $this->db->Execute('SELECT id FROM fields WHERE type = "form/slug"');
		$field_id = $rsField->fields['id'];
		$rsField->Close();
		$rs = $this->db->Execute('SELECT pages.id FROM pages WHERE code_name = "' . $code . '"');
		$page_id = $rs->fields('id'); $rs->Close();
		$rs = $this->db->Execute('SELECT pages_fields.id FROM pages_fields WHERE name = "' . $slug_field . '" AND page_id = '.$page_id.'');
		if(!$rs->RecordCount()){
			$this->db->Execute("INSERT INTO `pages_fields` ( `page_id`, `field_id`, `classdef`, `list_display_style`, `display_in_list`, `active`, `rank`, `name`, `label_fre`, `label_eng`, `lang_specific`, `specs`, `rules_fre`, `rules_eng`) VALUES
($page_id, $field_id, '', '', 0, 1, 60, '$slug_field', '$slug_label', '$slug_label', 1, '', '', '')");
			$this->db->Execute("ALTER TABLE {$this->table} ADD $slug_field varchar(255) NOT NULL");
		}
		$this->check_slug($source_field, $slug_field);
	}
	
	/**
	 * get_from_slug function.
	 * Cette fonction renvoie l'ID à partir d'un slug donné. 
	 * @access public
	 * @param string $slug_field Le nom du champ slug.
	 * @param string $slug_value La valeur du champ slug (provient généralement de l'URL)
	 * @param string $source_field (default: null) Le nom du champ source à partir duquel est construit le slug (en cas de slug non défini)
	 * @param int $trans (default: 1) Défini si on doit aller chercher la valeur du champ slug traduit (généralement utile pour les boutons "english/français" pour passer d'une langue à l'autre tout en restant sur la même page. 
	 * @return L'ID de l'enregistrement associé au slug.
	 */
	// "name_$lang3", $_GET['type'], "slug_$lang3", 0
	function get_from_slug ($slug_field, $slug_value, $source_field = null, $trans = 1) {
		$this->setComplexWhere();
		$this->getLangTrans($lang2, $lang3);
		if($source_field) {
			$this->check_slug($source_field, $slug_field);
			if($trans == 1)$this->check_slug(str_replace($this->lang3, $lang3, $source_field), str_replace($this->lang3, $lang3, $slug_field));
		}
		if($trans == 1)$slug_trang = "slug_$lang3";
		else $slug_trans = $slug_field;
		$rs = $this->db->Execute("SELECT id, $slug_field AS slug_trans FROM {$this->table} WHERE $slug_field = '$slug_value' AND active = 1{$this->complex_where}" );
		if($rs && $rs->RecordCount()){
			$this->current_id = $rs->fields('id');
			$this->current_slug = $slug_value;
			$this->current_slug_trans = $rs->fields('slug_trans');
		} else $this->current_id =false;
		return $this->current_id;
	}
	
	/**
	 * setComplexWhere function.
	 * À ce niveau, la fonction est vide, mais elle doit être définie dans les classes enfants si nécessaire.
	 * @access public
	 * @return void
	 */
	function setComplexWhere(){
		// nothing to do here ...
	}

	/**
	 * getLangURL function.
	 * 
	 * @access public
	 * @return La langue sous sa forme utilisée dans l'URL (c'est-à-dire, 2 lettres).
	 */
	function getLangURL() {
		if($this->lang3 == 'fre') return 'fr';
		else  return 'en';
	}
	
	/**
	 * getLangTrans function.
	 * Permet d'obtenir la langue de traduction (valide pour un site bilingue). Si c'est français, ça retourne les code en anglais (en, eng), si c'est anglais, ça retourne les codes en français (fr, fre).
	 * @access public
	 * @param string &$lang2
	 * @param string &$lang3
	 * @return void
	 */
	function getLangTrans(&$lang2,  &$lang3) {
		if($this->lang3 == 'fre'){
			$lang2 = 'en';
			$lang3 = 'eng';
		}else{
			$lang2 = 'fr';
			$lang3 = 'fre';
		}
	}


}
?>