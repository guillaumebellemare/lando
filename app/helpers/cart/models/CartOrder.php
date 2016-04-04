<?php

class CartOrder extends App {
	
	public function __construct() {
		$this->table = 'cartorders';
		$this->table_code = 'cartorder';
		parent::__construct($this->table, $this->table_code);
	}


	public function insert(&$data, &$subdata){
		
		# Create order
		parent::insert($data);
		
		# Append items
		$order_item = new CartOrderItem();
		$product = new CartProduct();

		foreach($subdata as &$current_subdata){
			$current_subdata["{$this->table_code}_id"] = $data['id'];
			$order_item->insert($current_subdata);
		}
	}
	
	function getOrderNumber($record = NULL, $stack = 1, $of = 1){
		$alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

		$sql = "SELECT MAX(SUBSTRING_INDEX( invoice_no,  '-',1 )) AS highest_order_number FROM $this->table ";
		$rs = $this->db->Execute($sql);
		if($rs->EOF) $count = 1;
		else {
			$count = intval($rs->fields['highest_order_number']);
		}
		if($stack == 1)  $count++;
		return substr('0000' . $count, -5);
		#else return substr('0000' . $count, -5) . '-' . $alphabet[$stack-1];
		
	}
	
	function getCurrentUserOrders($id){
		$rs = $this->select($this->table)->where("$this->table.customer_id = $id")->left_join("cartorder_products")->group_by("$this->table.id")->order_by("$this->table.invoice_no DESC")->all();
		$product = new CartProduct();
		foreach($rs as &$record)
		{
			$record["products"] = $product->select($product->table)->left_join("cartorder_products")->where("cartorder_products.cartorder_id = ".$record['cartorder_products.cartorder_id']."")->all();
		}
		
		return $rs;

	}
	
	public function getOrderByID($id){
		return $this->select($this->table)->where("$this->table.id = '$id'")->limit(1);
	}
	
	
	public function getItemsOrderByID($id){
		return $this->select($this->table)->left_join("cartorder_products")->left_join("products")->where("$this->table.id = '$id'")->order_by("$this->table.id DESC")->all();
	}

	public function getOrderByUniqueID($unique_id){
		return $this->select($this->table)->where("$this->table.unique_transaction_id = '$unique_id'")->order_by("$this->table.id DESC")->limit(1);
	}
	
	public function getItemsOrderByUniqueID($unique_id){
		return $this->select($this->table)->left_join("cartorder_products")->left_join("products")->where("$this->table.unique_transaction_id = '$unique_id'")->order_by("$this->table.id DESC")->all();
	}

	
	public function checkIfTransactionExist($id){
		$q = "SELECT COUNT(*) AS txn_id_exists FROM {$this->table} WHERE txn_id = '".$id."'";
		return current($this->raw_query($q));	
	}

	public function cartorder_products() {
		return $this->oneToMany("$this->table.id", "cartorder_products.cartorder_id");
	}
	
	public function products() {
		return $this->oneToMany("products.id", "cartorder_products.product_id");
	}
}
