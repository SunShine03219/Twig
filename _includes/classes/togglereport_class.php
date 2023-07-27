<?php

class togglereport{
	
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
<div class="summary_title">No results found for this report.</div><div class="gap"></div>
</div><!-- end  -->
';
	}
	
	public function execute(reportwherebuilder &$wherebuilder, reportfrombuilder &$frombuilder, reportorderbuilder &$orderbuilder){
		$whereclause = $wherebuilder->build_whereclause();
		$fromclause = $frombuilder->build_fromclause();
		$orderclause = $orderbuilder->build_orderclause();
		
		$sql = 'SELECT count(*) count ' . $fromclause . ' ' . $whereclause;
		$result = db_select_assoc($sql);
		if(!$result){
			return false;
		} elseif ($result['count'] == 0){
			return false;
		}
		
		$sql = 'SELECT t.name name, t.active active, t.id id ' . $fromclause . $whereclause . $orderclause;
		
		$result = db_select_assoc_array($sql);
		
		$script = $_SERVER['SCRIPT_NAME'];
		if($result){
		    /*
			$count = 0;
			$sprint_edit = '<a href="'.$script.'?id=%d"><img src="images/edit.png" width="25px" height="25px" border="0" /></a>';
			$sprint_status = '<a href="'.$script.'?id=%d&action=toggle">%s</a>';
			foreach($result as $row){
				$result[$count]['edit'] = sprintf($sprint_edit, $row['id']);
				$result[$count]['status'] = sprintf($sprint_status, $row['id'], ($row['active']?"Active":"Inactive"));
				$count++;
			}
			*/
		    $count = 0;
		    foreach($result as $row){
		        $result[$count]['status'] = ($row['active']?"Active":"Inactive");
		        $count++;
		    }
		    return $result;
		} else {
		    return false;
		}
		/*
		$cl = new reportcolumnlist();
		$cl->add('edit', 'Edit', '25%', '', 'tcen');
		$cl->add('name', 'Name', '50%', 'name', 'ttxt');
		$cl->add('status', 'Status', '25%', 'active', 'ttxt');
		
		$table = new reporttable('reporttable');
		$table->set_columnlist($cl);
		$table->set_active_sort_array($orderbuilder->get_active_sort());
		$table->add_get_vars($this->other_vars);
		$this->table = $table->build($result);
		
		return true;
		*/
	}
	
}