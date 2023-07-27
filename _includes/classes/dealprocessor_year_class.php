<?php
class dealprocessor_year extends dealprocessor_integerfield{
	
	protected function get_label(){
		return 'Year';
	}
	
	public function calculate(){
		
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->required = true;
		return $setting;
	}
	
	public function validate(request &$request){
		$result = parent::validate($request);
		if($result){
			$year = $this->deal->get_year();
			$YEAR_CUTOFF = date('y') + 2;
			$YEAR_MAX = 2000 + $YEAR_CUTOFF;
			$YEAR_MIN = 1900 + $YEAR_CUTOFF;
			
			//if year is 2 digits, make it 4
			if($year < 100 && $year >= $YEAR_CUTOFF){
				$year = 1900 + $year;
			} elseif ($year >= 0 && $year < $YEAR_CUTOFF){
				$year = 2000 + $year;
			}
			
			if($year<$YEAR_MIN || $year>$YEAR_MAX){
				$request->add_error($this->name, $this->get_label() . ' must be between '.$YEAR_MIN.' and '.$YEAR_MAX);
				return false;
			}
			
			if($year != $this->deal->get_year()){
				$this->deal->set_year($year);
			}
			
		}
		return $result;		
	}
}