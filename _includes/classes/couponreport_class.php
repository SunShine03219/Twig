<?php

class couponreport{
	
	private $table;
	
	private $other_vars;
	
	public function __construct(){
		$this->table = '';
	
		$this->other_vars = array();
	}
	
	public function get_table(){
		return $this->table;
	}
	
	public function add_other_vars($array){
		$this->other_vars = array_merge($this->other_vars, $array);
	}
	
	public function build_error_no_result(){
		return '
<div id="form_row">
<div class="summary_title">No Coupons found.</div><div class="gap"></div>
</div><!-- end  -->
';
	}
	
	public function execute(reportwherebuilder &$wherebuilder, reportfrombuilder &$frombuilder, reportorderbuilder &$orderbuilder){
		$whereclause = $wherebuilder->build_whereclause();
		$fromclause = $frombuilder->build_fromclause();
		$orderclause = $orderbuilder->build_orderclause();

		$sql = 'SELECT count(*) count ' . $fromclause . ' ' . $whereclause;

		$result = db_select_assoc($sql);
//		if(!$result){
//			return false;
//		} elseif ($result['count'] == 0){
//			return false;
//		}

		$sql = '
SELECT
    c.id id,
	c.title title,
	c.coupon coupon,
	c.start_date startDate,
	c.end_date endDate,
	c.user_allowed userAllowed,
	c.user_used userUsed,
	c.coupon_type couponType,
	c.coupon_value couponValue,
	c.no_limit noLimit,
	c.status status
FROM
		`' . coupon::TABLE . '` c ' . $orderclause;

		$result = db_select_assoc_array($sql);

		if($result){
		    $count = 0;
		    foreach($result as $row){
		        $count++;
		    }
		    return $result;
		} else {
		    return false;
		}
		

	}
	
}