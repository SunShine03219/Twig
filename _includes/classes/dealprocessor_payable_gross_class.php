<?php

class dealprocessor_payable_gross extends dealprocessor_floatfield{
	
	protected function get_label(){
		return 'Payable Gross';
	}
	
	public function calculate(){
		if($this->setting->setting == dealsetting::CALC){
			switch ($this->setting->value){
				case 'iminusdp':
					$pg = $this->deal->get_initial_gross() - $this->deal->get_discount() - $this->deal->get_pack();
					$this->deal->set_payable_gross($pg);
					break;
				case 'equalinitial':
					$pg = $this->deal->get_initial_gross();
					$this->deal->set_payable_gross($pg);
					break;
			}
		}
	}
	
}