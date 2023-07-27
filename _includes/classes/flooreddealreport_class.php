<?php

class flooreddealreport {
	
	private $table;
	
	private $total;
	private $average;
	private $projected;
	
	private $other_vars;
	
	public function __construct(){
		$this->table = '';
		$this->total = true;
		$this->average = false;
		$this->projected = false;
		
		$this->other_vars = array();
	}
	
	public function get_table(){
		return $this->table;
	}
	
	public function add_other_vars($array){
		$this->other_vars = array_merge($this->other_vars, $array);
	}
	
	public function execute(reportwherebuilder &$wherebuilder, reportfrombuilder &$frombuilder, reportorderbuilder &$orderbuilder){
		
		$whereclause = $wherebuilder->build_whereclause();
		$fromclause = $frombuilder->build_fromclause();
		$orderclause = $orderbuilder->build_orderclause();
		
		$anyresults = 'SELECT count(*) count ' . $fromclause . ' ' . $whereclause;
		$result = db_select_assoc($anyresults);
		if(!$result){
			return false;
		} elseif ($result['count']==0){
			return false;
		}
		
		$sql = 'SELECT
			d.id dealid,
			d.date_sold date_sold,
			DATE_FORMAT(d.date_sold,"%m/%d/%Y") date_sold_f,
			d.client_name client_name,
			d.stock stock,
			d.locked locked,
			concat_ws(" ",`d`.`year`,`d`.`make`,`d`.`model`) `vehicle`,
			fl.name flooring_provider,
			d.flooring_id flooring_id,
			d.flooring_paid flooring_paid,
			d.flooring_date flooring_date,
			DATE_FORMAT(d.flooring_date,"%m/%d/%Y") flooring_date_f
			'. $fromclause .'
			INNER JOIN flooring fl on d.flooring_id = fl.id
			' . $whereclause;

			if(isset($_GET['field'])){
				$sql .= 'ORDER BY LOWER('.$_GET['field'].') '.$_GET['direction'];
			}else{
				$sql .= $orderclause;
			}
			
			if(isset($_GET['type'])){
				if($_GET['type'] == 'pagination'){
					$sql .= " limit ". $_GET['row_count'].", 50";
				}else{
					$sql .= " limit 0, 100";
				}
			}else{
				$sql .= " limit 0, 100";
			}
			
		$result = db_select_assoc_array($sql);
		
		if($result){
			$count = 0;
			$sprint_edit = "<a href=\"deals.php?id=%d\">%s</a>";
			$sprint_notes = '<a href="#" onclick="popnotes(%d);">Notes</a>';
			/*
			foreach($result as $row){
				$result[$count]['stock'] = sprintf($sprint_edit, $row['dealid'], $row['stock']);
				$result[$count]['paid'] = ($row['flooring_paid']?'Paid':'Unpaid');
				$result[$count]['flooring_date_f'] = ($row['flooring_paid']?$row['flooring_date_f']:'');
				$count++;
			}
			*/
			return $result;
		} else {
		    return false;
		}
		/*
		$totaldata = array('stock'=>count($result), 'flooring_provider'=>'Total');
		
		$cl = new reportcolumnlist();
		$cl->add('date_sold_f', 'Date Sold', '15%', 'date_sold', 'tcen');
		$cl->add('flooring_provider', 'Floored By', '20%', 'flooring_id', 'tcen');
		$cl->add('stock', 'Stock No', '10%', 'stock', 'tcen');
		$cl->add('vehicle', 'Vehicle', '25%', 'vehicle', 'tcen');
		$cl->add('paid', 'Status', '15%', 'flooring_paid', 'tcen');
		$cl->add('flooring_date_f', 'Date Paid', '15%', 'flooring_date', 'tcen');
		
		$table = new reporttable('reporttable');
		$table->set_columnlist($cl);
		$table->set_active_sort_array($orderbuilder->get_active_sort());
		$table->add_get_var('action', 'floored');
		$table->add_get_vars($this->other_vars);
		$this->table = $table->build($result, $totaldata);
		
		return true;
		*/
	}
	
}