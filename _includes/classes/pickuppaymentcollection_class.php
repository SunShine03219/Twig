<?php
class pickuppaymentcollection implements IteratorAggregate{
	
	private $payments;
	
	private $loaded_ids;
	
	public function __construct(){
		$this->payments = new ArrayObject(array());
		$this->loaded_ids = array();
	}
	
	public function add(pickuppayment &$payment){
		$this->loaded_ids[] = $payment->get_id();
		$this->payments->append($payment);
	}
	
	public function count(){
		return count($this->payments);
	}
	
	public function is_empty(){
		return (count($this->payments) == 0);
	}
	
	public function getIterator(){
		return $this->payments->getIterator();
	}
	
	public function exists($id){
		return in_array($id, $this->loaded_ids);
	}
	
	public function retrieve($id){
		if($this->exists($id)){
			foreach($this->payments as $payment){
				if($payment->get_id() == $id){
					return $payment;
				}
			}
		}
		return false;
	}
	
	public function get_loaded_ids(){
		return $this->loaded_ids;
	}
	
	public function commit($deal_id){
		foreach($this->payments as $payment){
			$payment->commit($deal_id);
		}
	}
	
}