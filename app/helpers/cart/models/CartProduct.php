<?php

class CartProduct extends App {
	
	public function __construct() {
		$this->table = 'products';
		$this->table_code = 'product';
		parent::__construct($this->table, $this->table_code);
	}
	
	public function getAllProducts(){
		return $this->select()->all();
	}
	
	public function getCurrentProduct($id = NULL, $slug = NULL){
		$where = NULL;
		if($id) $where .= "$this->table.id = '$id'";
		if($slug) $where .= "$this->table.slug_fre = '$slug' || $this->table.slug_eng = '$slug'";
		return $this->select($this->table)->where($where)->limit(1);
	}
	
	public function getCurrentProductQtys($id, $qty){
		$reserve = new CartReserve();
		
		$rs = $this->select($this->table)->where("$this->table.id = '$id'")->where("$this->table.qty >= $qty")->limit(1);
		$current_item_qty = $rs["products.qty"];
	
		$current_item_reserved_qtys = $reserve->getCurrentProductQtysReserved($id);
		$reserved_qty = 0;
		foreach($current_item_reserved_qtys as $current_item_reserved_qty) 
		{
			$reserved_qty += $current_item_reserved_qty["reserves.qty"];
		}
		
		$total_qty = ($current_item_qty-$reserved_qty);
		#if($total_qty<=0) $total_qty = 1;
		return $total_qty;

	}
	
	public function updateQuantity($qty_to_substract, $id){
		$raw_update = "UPDATE $this->table SET qty = (qty - {$qty_to_substract}) WHERE id = {$id}";
		$this->raw_update($raw_update);
	}
	
	public function check_product_availability($id, $qty){
		$rs = $this->select($this->table)->where("$this->table.id = '$id'")->where("$this->table.qty >= $qty")->limit(1);
		return $rs["products.qty"];
	}
	
	public function getCurrentProductMaxQty($id){
		$rs = $this->select($this->table)->where("$this->table.id = '$id'")->limit(1);
		return $rs["products.qty"];
	}
	
	public function getCartTotalWeight($items){
		
		$total_weight = 0;
		foreach($items as $item)
		{
			$total_weight += $item["weight"]*$item["qty"]["qty"];
		}
		
		return $total_weight/1000;
	}
	
	public function cartorder_products() {
		return $this->oneToMany("$this->table.id", "cartorder_products.product_id");
	}


}
