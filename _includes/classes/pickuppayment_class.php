<?php
class pickuppayment{
	
	private $id;
	private $deal_id;
	private $amount;
	private $coupon_value;
	private $coupon_id;
	private $amount_payable;
	private $payment_method;
	private $date_due;
	private $paid;
	private $date_paid;
	private $deleted;
	private $note;
	
	private $created_by;
	private $created_timestamp;
	private $modified_by;
	private $modified_timestamp;
	
	private $actions;
	private $log;
	
	const TABLE = 'pickuppayments';
	const LOGTABLE = 'pickuppaymentlog';
	
	const PICKUP_PAID = 1;
	const PICKUP_UNPAID = 0;
	
	const NOT_DELETED = 0;
	const DELETED = 1;
	
	public function __contruct(){
		$this->zero_it();
	}
	
	private function zero_it(){
		$this->id = 0;
		$this->deal_id = 0;
		$this->amount = 0.0;
		$this->coupon_value = 0;
		$this->coupon_id = 0;
		$this->amount_payable = 0.0;
		$this->payment_method = 0;
		$this->date_due = '';
		$this->paid = self::PICKUP_UNPAID;
		$this->date_paid = '';
		$this->deleted = self::NOT_DELETED;
		$this->note = '';
		
		$this->created_by = 0;
		$this->created_timestamp = 0;
		$this->modified_by = 0;
		$this->modified_timestamp = 0;
		
		$this->actions = array();
		$this->log = array();
	}
	
	public function make_me_into($id){
		$this->set_id($id);
		return $this->populate();
	}
	
	private function populate(){
		if(empty($this->id)) {$this->zero_it(); return false;}
		
		$where = array('id' => $this->id);
		$data = db_select_one(self::TABLE, $where);
		
		if($data === false || empty($data)){ $this->zero_it(); return false; }
		
		$this->set_id($data['id']);
		$this->set_deal_id($data['deal_id']);
		$this->set_amount($data['amount']);
		$this->set_coupon_value($data['coupon_value']);
		$this->set_coupon_id($data['coupon_id']);
		$this->set_amount_payable($data['amount_payable']);
		$this->set_payment_method($data['payment_method']);
		$this->set_date_due(date('Y-m-d', strtotime($data['date_due'])));
		$this->set_paid($data['paid']);
		$this->set_date_paid($data['date_paid']);
		$this->set_deleted($data['deleted']);
		$this->set_note($data['note']);
		$this->set_created_by($data['created_by']);
		$this->set_created_timestamp($data['created_timestamp']);
		$this->set_modified_by($data['modified_by']);
		$this->set_modified_timestamp($data['modified_timestamp']);
		
		
		return true;
	}
	
	public function get_id(){return $this->id;}
	public function get_deal_id(){return $this->deal_id;}
	public function get_amount(){return $this->amount;}
	public function get_coupon_value(){return $this->coupon_value;}
	public function get_coupon_id(){return $this->coupon_id;}
	public function get_amount_payable(){return $this->amount_payable;}
	public function get_payment_method(){return $this->payment_method;}
	public function get_date_due(){return $this->date_due;}
	public function get_paid(){return $this->paid;}
	public function get_date_paid(){return $this->date_paid;}
	public function get_deleted(){return $this->deleted;}
	public function get_note(){return $this->note;}
	public function get_created_by(){return $this->created_by;}
	public function get_created_timestamp(){return $this->created_timestamp;}
	public function get_modified_by(){return $this->modified_by;}
	public function get_modified_timestamp(){return $this->modified_timestamp;}
	public function is_paid(){return $this->paid==self::PICKUP_PAID;}
	public function get_actions(){return $this->actions;}
	
	private function set_id($v){
		$v = intval($v);
		if($v>0){
			$this->id = $v;
		} else {
			$this->id = 0;
		}
	}
	
	public function set_deal_id($v){
		$v = intval($v);
		$this->deal_id = $v;
	}
	
	public function set_amount($v){
		$v = floatval($v);
		if($v>=0.00){
			$this->amount = $v;
		}
	}

	public function set_coupon_value($v){
		$this->coupon_value = $v;
	}

	public function set_coupon_id($v){
		$this->coupon_id = $v;
	}

	public function set_amount_payable($v){
		$v = floatval($v);
		if($v>=0.00){
			$this->amount_payable = $v;
		}
	}
	
	public function set_payment_method($v){
		$this->payment_method = $v;
	}
	
	public function set_date_due($v){
		$this->date_due = date('Y-m-d', strtotime($v));
	}
	
	public function set_paid($v){
		$this->paid = ($v?self::PICKUP_PAID:self::PICKUP_UNPAID);
	}
	
	public function set_date_paid($v){
		$this->date_paid = $v;
	}
	
	public function set_deleted($v){
		$this->deleted = ($v?self::DELETED:self::NOT_DELETED);
	}

	public function set_note($v){
		$this->note = $v;
	}
	
	private function set_created_by($v){
		$this->created_by = intval($v);
	}
	
	private function set_created_timestamp($v){
		$this->created_timestamp = $v;
	}
	
	private function set_modified_by($v){
		$this->modified_by = intval($v);
	}
	
	private function set_modified_timestamp($v){
		$this->modified_timestamp = $v;
	}
	
	public function to_array(){
		$data = array();
		$data['id'] = $this->get_id();
		$data['deal_id'] = $this->get_deal_id();
		$data['amount'] = $this->get_amount();
		$data['coupon_value'] = $this->get_coupon_value();
		$data['coupon_id'] = $this->get_coupon_id();
		$data['amount_payable'] = $this->get_amount_payable();
		$data['payment_method'] = $this->get_payment_method();
		$data['date_due'] = $this->get_date_due();
		$data['paid'] = $this->get_paid();
		$data['date_paid'] = $this->get_date_paid();
		$data['deleted'] = $this->get_deleted();
		$data['note'] = $this->get_note();
		$data['created_by'] = $this->created_by;
		$data['created_timestamp'] = $this->created_timestamp;
		$data['modified_by'] = $this->modified_by;
		$data['modified_timestamp'] = $this->modified_timestamp;
		return $data;
	}
	
	private function payment_array(){
		$data = array();
		$data['amount'] = $this->get_amount();
		$data['coupon_value'] = $this->get_coupon_value();
		$data['coupon_id'] = $this->get_coupon_id();
		$data['amount_payable'] = $this->get_amount_payable();
		$data['payment_method'] = $this->get_payment_method();
		$data['date_due'] = $this->get_date_due();
		$data['note'] = $this->get_note();
		return $data;
	}
	
	public function from_array($data){
		$this->set_id($data['id']);
		$this->set_deal_id($data['deal_id']);
		$this->set_amount($data['amount']);
		$this->set_coupon_value($data['coupon_value']);
		$this->set_coupon_id($data['coupon_id']);
		$this->set_amount_payable($data['amount_payable']);
		$this->set_payment_method($data['payment_method']);
		$this->set_date_due($data['date_due']);
		$this->set_paid($data['paid']);
		$this->set_date_paid($data['date_paid']);
		$this->set_deleted($data['deleted']);
		$this->set_note($data['note']);
		$this->set_created_by($data['created_by']);
		$this->set_created_timestamp($data['created_timestamp']);
		$this->set_modified_by($data['modified_by']);
		$this->set_modified_timestamp($data['modified_timestamp']);
	}
	
	private function poke_created(){
		$this->set_created_by($_SESSION['FT']['user_id']);
		$this->set_created_timestamp(db_timestamp());
		$this->set_modified_by($this->get_created_by());
		$this->set_modified_timestamp($this->get_created_timestamp());
	}
	
	private function poke_modified(){
		$this->set_modified_by($_SESSION['FT']['user_id']);
		$this->set_modified_timestamp(db_timestamp());
	}
	
	public function insert_new(){
		$this->poke_created();
		$data = $this->to_array();
		unset($data['id']);
		$result = db_insert_assoc_one(self::TABLE, $data);
		
		if($result){
			$this->set_id(db_get_insert_id());
		}
		
		return $result;
	}
	
	public function update_one(){
		$this->poke_modified();
		
		$where = array('id' => $this->id);
		$data = $this->to_array();
		unset($data['id']);
		$result = db_update(self::TABLE, $data, $where);
		
		return $result;
	}
	
	public function validate_new($data){
		$error = false;
		
		if(!$this->validate_payment_data($data)){
			$error = true;
		}
		
		if(empty($this->date_due) || empty($this->amount)){
			$error = true;
		}
		
		if($data['paid'] ==	'paid'){
			$this->add_action('paid','paid');
		}
		
		if(!$error){
			$this->add_action('insert', 'insert');
		}
		
		return !$error;
	}
	
	public function validate_edit($data){
		$error = false;
		$pre = $this->payment_array();
		
		if(!$this->validate_payment_data($data)){
			$error = true;
		}
		
		if(empty($this->date_due) || empty($this->amount)){
			$error = true;
		}
		
		$post = $this->payment_array();
		
		if(!$error && $pre!=$post){
			$this->add_action('update', 'update');
		}
		
		if($data['paid'] ==	'paid' || $data['paid']=='unpaid'){
			$this->add_action('paid', $data['paid']);
		}
		
		if($data['delete'] == 'delete' || $data['delete'] == 'undelete'){
			$this->add_action('delete', $data['delete']);
		}
		
		return !$error;
	}
	
	private function validate_payment_data($data){
		$this->validate_note($data['note']);
		$this->validate_date_due($data['date_due']);
		$this->validate_amount($data['amount']);
		$this->validate_payment_method($data['payment_method']);
		return true;
	}
	
	private function validate_date_due($date_due){
		$date_due_timestamp = strtotime($date_due);
		if($date_due_timestamp !== false){
			$this->set_date_due(date('Y-m-d',$date_due_timestamp));
		}
	}

	private function validate_note($note){
		$this->set_note($note);
	}
	
	private function validate_amount($amount){
		$this->set_amount(floatval($amount));
	}
	
	private function validate_payment_method($payment_method){
		$this->set_payment_method(intval($payment_method));
	}
	
	public function add_action($key, $value){
		$this->actions[$key] = $value;
	}
	
	public function commit($deal_id=0){
		if(!empty($this->actions)){
			$data = $this->to_array();
			$update_flag = false;
			if(isset($this->actions['delete']) && $this->actions['delete']=='delete'){
				$this->log[] = 'Payment was deleted.';
				$this->set_deleted(self::DELETED);
				$update_flag = true;
			}
			if(isset($this->actions['delete']) && $this->actions['delete']=='undelete'){
				$this->log[] = 'Payment was Recovered.';
				$this->set_deleted(self::NOT_DELETED);
				$update_flag = true;
			}
			if(isset($this->actions['paid']) && $this->actions['paid']=='paid'){
				$this->log[] = 'Marked as paid.';
				$this->set_paid(self::PICKUP_PAID);
				$this->set_date_paid(date('Y-m-d', strtotime('today')));
				$update_flag = true;
			}
			if(isset($this->actions['paid']) && $this->actions['paid']=='unpaid'){
				$this->log[] = 'Marked as unpaid.';
				$this->set_paid(self::PICKUP_UNPAID);
				$this->set_date_paid('0');
				$update_flag = true;
			}
			
			if(isset($this->actions['insert']) && $this->actions['insert']=='insert'){
				$this->set_deal_id($deal_id);
				$this->log[] = 'Payment for ' . cashmoney($this->amount) . ' was scheduled for ' . pretty_date($this->get_date_due()) . '.';
				$this->insert_new();
			}elseif(isset($this->actions['update']) && $this->actions['update']=='update'){
				$this->log[] = 'Payment was updated to ' . cashmoney($this->amount) . ' scheduled for ' . pretty_date($this->get_date_due()) . '.';
				$this->update_one();
			} elseif($update_flag){
				$this->update_one();
			}
			
			if(!empty($this->log)){
				$this->save_log();
			}
		}
	}
	
	private function save_log(){
		$data = array(
			'pickup_id' => $this->get_id(),
			'deal_id' => $this->get_deal_id(),
			'user_id' => $_SESSION['FT']['user_id'],
			'changed' => db_timestamp(),
			'comment' => join(' ', $this->log)
				);
		db_insert_assoc_one(self::LOGTABLE, $data);
	}
	
	public static function build_log($deal_id){
		$deal_id = intval($deal_id);
		$where = array('pl.deal_id' => $deal_id);
		$whereclause = db_build_where($where);
		
		$sql = 'SELECT
				CONCAT_WS(\' \', u.name_first, u.name_last) person,
				changed,
				comment
				FROM '. self::LOGTABLE .' pl
				INNER JOIN '. user::TABLE .' u ON u.id=pl.user_id
				'.$whereclause.'
				ORDER BY changed asc';
		
		$data = db_select_assoc_array($sql);
		
		if(!$data){return false;}
		
		$sprint_log = "<div class='notecontainer'>\n\t<div class='dealnote'>%s</div><br/>\n\t<div class='noteuser'>%s</div> <div class='noteposted'>%s</div><br/>\n</div>";
		
		$a = array();
		
		foreach($data as $row){
			$a[] = sprintf($sprint_log, $row['comment'], $row['person'], gmt_to_local_pretty_time(strtotime($row['changed'])));
		}
		
		return join(PHP_EOL, $a);
	}
	
	
	
	
	
	
	
	
	
	
}