<?php
class Record{

	var $table;
	var $lang3;
	var $complex_where = '';
	var $current_id;

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $db La référence à la base de donnée (adodbphp).
	 * @param mixed $lang3 Le code à trois lettres de la langue selectionnée.
	 * @param mixed $table Le nom de la table associé à l'instance de cette classe.
	 * @return void
	 */
	function __construct($db, $lang3, $table){
		$this->db = $db;
		$this->lang3 = $lang3;
		$this->table = $table;
		$this->sortBy = " ORDER BY {$this->table}.rank";
	}
	
	/**
	 * getCurrent function.
	 * Exécute la requête pour aller chercher l'enregistrement selectionné (à partir du ID, qui à son tour provenait du slug).
	 * @access public
	 * @return void
	 */
	function getCurrent($current_id = null){
		if($current_id) $this->current_id = $current_id;
		if(!isset($this->current_id)){
			echo 'FATAL ERROR : undefined $this->current_id in getCurrent() method';
			exit;
		}
		$this->rsCurrent = $this->db->Execute("SELECT * FROM {$this->table} WHERE active = 1 AND id = {$this->current_id}{$this->complex_where} LIMIT 1");
	}
		
	
	/**
	 * get function.
	 * Avant de renvoyer le contenu du champ, on s'assure que la requête de sélection de l'enregistrement en cours a bien été fait auparavant.
	 * @access public
	 * @param mixed $field Nom du champ à retourner.
	 * @return Le contenu d'un champ de l'enregistrement selectionné.
	 */
	function get($field){
		if(!$this->rsCurrent) $this->getCurrent();
		return $this->rsCurrent->fields($field);
	}

}
?>