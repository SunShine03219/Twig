<?php



class reportwherebuilder{
	
	private $wheres;
	
	public function __construct(){
		$this->wheres = array();
	}
	
	public function empty_me(){
		$this->wheres = array();
	}
	
	public function build_whereclause(){
		$whereclause = '';
		if(!empty($this->wheres)){
		//echo ' WHERE ' . join(' AND ', $this->wheres);
			$whereclause = ' WHERE ' . join(' AND ', $this->wheres);
		}
		return $whereclause;
	}
	
	public function add_where_equal($column, $value){
		array_push($this->wheres, sprintf('%s = "%s"', $column, $value));
	}
	
	public function add_where_less($column, $value){
		array_push($this->wheres, sprintf('%s < "%s"', $column, $value));
	}
	
	public function add_where_lessequal($column, $value){
		array_push($this->wheres, sprintf('%s <= "%s"', $column, $value));
	}
	
	public function add_where_greater($column, $value){
		array_push($this->wheres, sprintf('%s > "%s"', $column, $value));
	}
	
	public function add_where_greatequal($column, $value){
		array_push($this->wheres, sprintf('%s >= "%s"', $column, $value));
	}
	
	public function add_custom($clause){
		array_push($this->wheres, $clause);
	}
	
}

class unfundedwherebuilder extends reportwherebuilder{
	public function __construct(){
		parent::__construct();
		$this->add_where_equal('funded', 0);
	}
}

class fundedwherebuilder extends reportwherebuilder{
	public function __construct(){
		parent::__construct();
		$this->add_where_equal('funded', 1);
	}
}
class approvedwherebuilder extends reportwherebuilder{
	public function __construct(){
		parent::__construct();
		$this->add_where_equal('approved', 1);
	}
}

class daterangewherebuilder extends reportwherebuilder{
	public function __construct($start, $end){
		parent::__construct();
		$this->add_custom(sprintf('date_sold>="%s"', $start));
		$this->add_custom(sprintf('date_sold<="%s"', $end));
	}
}

class mtdwherebuilder extends daterangewherebuilder{
	public function __construct(){
		parent::__construct(first_of_month(), last_of_month() );
	}
}

class prevmonthwherebuilder extends daterangewherebuilder{
	public function __construct(){
		parent::__construct(first_of_last_month(), last_of_last_month());
	}
}

class customdatewherebuilder extends daterangewherebuilder{
	public function __construct($start, $end){
		parent::__construct($start, $end);
	}
}





class singledaywherebuilder extends reportwherebuilder{
	public function __construct($date){
		parent::__construct();
		$this->add_where_equal('date_sold', $date);
	}
}

class dailywherebuilder extends singledaywherebuilder{
	public function __construct(){
		parent::__construct(just_today());
	}
}

class yesterdaywherebuilder extends singledaywherebuilder{
	public function __construct(){
		parent::__construct(just_yesterday());
	}
}

class customonedaywherebuilder extends singledaywherebuilder{
	public function __construct($date){
		parent::__construct($date);
	}
}
