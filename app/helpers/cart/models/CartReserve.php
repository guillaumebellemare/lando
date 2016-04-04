<?php

class CartReserve extends App {
	
	public function __construct() {
		$this->table = 'reserves';
		$this->table_code = 'reserve';
		parent::__construct($this->table, $this->table_code);
	}
	
	public function reserveQtys()
	{
		foreach($_SESSION['cart']['items'] as $item){
			$record["session_id"] = $_SESSION['customer']['customer_unique_id'];
			$record["product_id"] = $item['data']['products.id'];
			$record["qty"] = $item['qty']['qty'];
			$record["created"] = date("Y-m-d H:i:s");
			
			$rs = $this->select($this->table)->where("$this->table.product_id = '".$record["product_id"]."'")->where("$this->table.session_id = '".$record["session_id"]."'")->all();
			$rs_s = $this->select($this->table)->where("$this->table.product_id = '".$record["product_id"]."'")->where("$this->table.session_id = '".$record["session_id"]."'")->limit(1);
			
			$product = new CartProduct();
			$current_item_qty = $product->check_product_availability($record["product_id"], $record["qty"]);
			$current_item_reserved_qty = $this->select($this->table)->where("$this->table.session_id != '".$_SESSION['customer']['customer_unique_id']."'")->where("$this->table.product_id = ".$record["product_id"]."")->where("$this->table.created > DATE_SUB(NOW(), INTERVAL 15 MINUTE)")->limit(1);		
			$current_item_reserved_qty = $current_item_reserved_qty["$this->table.qty"];
			$available_qty = ($current_item_qty-$current_item_reserved_qty);

			if(!$rs && $available_qty>=$record["qty"]) $this->insert($record);
			if($rs_s) $this->update($record, $rs_s["$this->table.id"]);
			if($available_qty<$record["qty"])
			{
				$this->releaseReservation();
				$_SESSION[$session_name]['customer_unique_id'] = NULL;
				$this->redirect("cart");
			}
			
			$record = NULL;
		}
	}
	
	public function getCurrentProductQtysReserved($id)
	{
		return $this->select($this->table)->where("$this->table.product_id = '$id'")->where("$this->table.session_id != '".$_SESSION['customer']['customer_unique_id']."'")->where("$this->table.created > DATE_SUB(NOW(), INTERVAL 15 MINUTE)")->all();
	}

	public function confirmItemsAvailability($items)
	{
		
		$product = new CartProduct();
		foreach($items as $item){
			$current_item_qty = $product->check_product_availability($item["product_id"], $item["qty"]);
			$current_item_reserved_qty = $this->select($this->table)->where("$this->table.session_id != '".$_SESSION['customer']['customer_unique_id']."'")->where("$this->table.product_id = ".$item['product_id']."")->where("$this->table.created > DATE_SUB(NOW(), INTERVAL 15 MINUTE)")->limit(1);
			$current_item_reserved_qty = $current_item_reserved_qty["$this->table.qty"];

			$total_item_available = ($current_item_qty-$current_item_reserved_qty);
			if($total_item_available<=0)
			{
				$this->releaseReservation();
				$_SESSION[$session_name]['customer_unique_id'] = NULL;
				$this->redirect("cart");
			}
		}
		
		return true;
		
	}
	
	public function releaseReservation()
	{
		$this->delete("reserves", "session_id = '".$_SESSION['customer']['customer_unique_id']."'");
		$this->delete("reserves", "created < DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
	}

}
