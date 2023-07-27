<?php
class dealprocessor_pickuppayments extends dealprocessor_base{
	
	private $pickups;
	
	public function __construct(dealprocessor_factory &$factory, deal &$deal, dealsetting &$setting = null){
		parent::__construct($factory, $deal, $setting);
		$this->pickups = new pickuppaymentcollection();
		
		$pickup_payment_ids = $deal->get_pickuppayments();
		if(!empty($pickup_payment_ids) && is_array($pickup_payment_ids)){
			$this->load_existing_pickups($pickup_payment_ids);
		}
	}
	
	private function load_existing_pickups($id_array){
		foreach($id_array as $id){
			$pp = new pickuppayment();
			if($pp->make_me_into($id)){
				$this->pickups->add($pp);
			}
		}
	}
	
	public function set_deal_default(){
		
	}
	
	public function build_form(formbuilder &$form, $mode = ''){
		if($this->setting->setting != dealsetting::SKIP){
			if($this->deal->get_id()==0 && $this->pickups->is_empty()){
				$this->pickups->add(new pickuppayment());
				$this->pickups->add(new pickuppayment());
				$this->pickups->add(new pickuppayment());
			}
			$pickupformbuilder = new pickuppaymentform();
			$pickupformbuilder->create_form($form, $this->pickups, $this->deal->get_id());
		}
	}
	
	public function calculate(){
		if($this->setting->setting != dealsetting::SKIP){
			
		}
	}
	
	public function commit(){
		if($this->setting->setting != dealsetting::SKIP){
			$this->pickups->commit($this->deal->get_id());
		}
	}
	
	public function validate(request &$request){
		if($this->setting->setting == dealsetting::SKIP){return true;}
		
		$data = $request->get_fields(array('pickup_id', 'pickup_date_due', 'pickup_amount', 'pickup_coupon_value', 'pickup_amount_payable', 'pickup_coupon_id', 'pickup_payment_method', 'pickup_delete', 'pickup_paid', 'pickup_note'));
		
		if(empty($data) || empty($data['pickup_id'])){
			return true;
		}
		
		$payments = array();
		for($i = 0; $i<count($data['pickup_id']); $i++){
			$payments[] = array(
					'id'=>$data['pickup_id'][$i],
					'date_due'=>$data['pickup_date_due'][$i],
					'amount'=>$data['pickup_amount'][$i],
					'coupon_value'=>$data['pickup_coupon_value'][$i],
					'coupon_id'=>$data['pickup_coupon_id'][$i],
					'amount_payable'=>$data['pickup_amount_payable'][$i],
					'payment_method'=>$data['pickup_payment_method'][$i],
					'delete'=>$data['pickup_delete'][$i],
					'paid'=>$data['pickup_paid'][$i],
					'note'=>$data['pickup_note'][$i]
					//''=>$data[''][$i],
					);
		}

		$error_flag = false;
		
		foreach($payments as $payment){
			if($payment['id'] == 'new'){
				$pp = new pickuppayment();
				$result = $pp->validate_new($payment);
				if($result){
					$this->pickups->add($pp);
				}
			} else {
				if($pp = $this->pickups->retrieve($payment['id'])){
					$result = $pp->validate_edit($payment);
					if(!$result){$error_flag = true;}
				}
			}
		}
		
		return !($error_flag);
	}
	
}