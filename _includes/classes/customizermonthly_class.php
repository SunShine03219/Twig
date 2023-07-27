<?php

class customizermonthly implements icustomizer{
	
	private $active;
	private $mode;
	
	private $custom_month;
	private $custom_year;
	
	private $from;
	private $to;
	
	const MODE_MTD = 'mtd';
	const MODE_PREV = 'prevmon';
	const MODE_CUSTOM = 'custommonth';
	const CUSTOM_MONTH = 'month';
	const CUSTOM_YEAR = 'year';
	const DATE_SOLD_COLUMN = 'date_sold';
	
	
	public function __construct(){
		$this->active = false;
		$this->mode = '';
		
		$this->custom_month = date('m');
		$this->custom_year = date('y');
	}
	
	public function handle(&$array){
		switch($array['mode']){
			case self::MODE_MTD:
			case self::MODE_CUSTOM:
			case self::MODE_PREV:
				$this->handlecontroller($array['mode'], $array);
				$this->active = true;
				$this->mode = $array['mode'];
		}
	}
	
	private function handlecontroller($mode, $array){
		switch($mode){
			case self::MODE_MTD:
				$this->handle_mtd();
				break;
			case self::MODE_PREV:
				$this->handle_prev();
				break;
			case self::MODE_CUSTOM:
				$this->handle_custom($array[self::CUSTOM_MONTH], $array[self::CUSTOM_YEAR]);
				break;
		}
	}
	
	private function handle_mtd(){
		$this->from = first_of_month();
		$this->to = last_of_month();
	}
	
	private function handle_prev(){
		$this->from = first_of_last_month();
		$this->to = last_of_last_month();
	}
	
	private function handle_custom($month, $year){
		if($month < 1 || $month > 12){
			$month = date('n');
		}
		if($year < 2000 | $year > date('Y')){
			$year = date('Y');
		}
		$this->custom_month = $month;
		$this->custom_year = $year;
		
		$this->from = first_of($month, $year);
		$this->to = last_of($month, $year);
	}
	
	public function build(formbuilder &$form){
		$form->start_section('form_row');
		$this->build_mtd($form);
		$this->build_prevmon($form);
		$this->build_custommonth($form);
		$form->end_section();
	}
	
	private function build_mtd(formbuilder &$form){
		$form->add_radio('mode', self::MODE_MTD, 'Month to Date', ($this->mode == self::MODE_MTD) );
	}
	
	private function build_prevmon(formbuilder &$form){
		$form->add_radio('mode', self::MODE_PREV, 'Previous Month', ($this->mode == self::MODE_PREV) );
	}
	
	private function build_custommonth(formbuilder &$form){
		$extra = $this->build_monthselect() . $this->build_yearselect();
		$form->add_radio('mode', self::MODE_CUSTOM, 'Select Month', ($this->mode == self::MODE_CUSTOM), $extra );
	}
	
	private function build_monthselect(){
		$months = array('1'=>'01 Jan', '2'=>'02 Feb', '3'=>'03 Mar', '4'=>'04 Apr', '5'=>'05 May', '6'=>'06 Jun', '7'=>'07 Jul', '8'=>'08 Aug', '9'=>'09 Sep', '10'=>'10 Oct', '11'=>'11 Nov', '12'=>'12 Dec');
		foreach($months as $id=>$val){
			$monthopts[] = '<option value="' . $id . '"'.(($this->custom_month==$id?' selected="selected"':'')).'>' . $val . '</option>';
		}
		return '<select name="'.self::CUSTOM_MONTH.'">' . join('', $monthopts) . '</select>';
	}
	
	private function build_yearselect(){
		$year = intval(date('Y'));
		for($i = $year; $i > $year - 9; $i--){
			$yearopts[] = '<option value="' . $i . '"'.(($this->custom_year==$i?' selected="selected"':'')).'>' . $i . '</option>';
		}
		return '<select name="'.self::CUSTOM_YEAR.'">' . join('', $yearopts) . '</select>';
	}
	
	public function wheres(reportwherebuilder &$wherebuilder){
		if($this->active){
			$wherebuilder->add_where_lessequal(self::DATE_SOLD_COLUMN, $this->to);
			$wherebuilder->add_where_greatequal(self::DATE_SOLD_COLUMN, $this->from);
		}
	}
	
	public function headline($type){
		if($this->active){
			return $this->headlinecontroller($type);
		} else {
			return false;
		}
	}
	
	private function headlinecontroller($type){
		switch($this->mode){
			case self::MODE_MTD:
				return $this->build_headline_mtd($type);
				break;
			case self::MODE_PREV:
				return $this->build_headline_prev($type);
				break;
			case self::MODE_CUSTOM:
				return $this->build_headline_custom($type);
				break;
			default:
				return 'No Title';
		}
	}
	
	private function build_headline_mtd($type){
		return sprintf('Month to Date %s Report - %s', $type, display_today_date());
	}
	
	private function build_headline_prev($type){
		return sprintf('%s Report - %s', $type, display_previous_month());
	}
	
	private function build_headline_custom($type){
		return sprintf('%s Report - %s', $type, display_month_year($this->custom_month, $this->custom_year));
	}
	
	public function get_active_vars(){
		if($this->active){
			if($this->mode == self::MODE_CUSTOM){
				return array('mode'=>$this->mode,
						self::CUSTOM_MONTH=>$this->custom_month,
						self::CUSTOM_YEAR=>$this->custom_year);
			} else {
				return array('mode'=>$this->mode);
			}
		}
	}
}