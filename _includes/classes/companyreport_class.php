<?php

class companyreport{

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
<div class="summary_title">No companies found for this report.</div><div class="gap"></div>
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
		
		$sql = 'SELECT
					t.name name,
					t.username,
					t.last_login,
					t.active active,
					t.id id,
					t.phone_main phone_main,
					t.phone_contact phone_contact,
					t.contact contact
					'.$fromclause . ' ' . $whereclause;

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
		    /*
			$count = 0;
			$sprint_edit = "<a href=\"company.php?id=%d\"><img src=\"images/edit.png\" width=\"25px\" height=\"25px\" border=\"0\" /></a>";
			$sprint_name = "<a href=\"company.php?id=%d\">%s</a>";
			foreach($result as $row){
				$result[$count]['name'] = sprintf($sprint_name, $row['id'], $row['name']);
				$result[$count]['edit'] = sprintf($sprint_edit, $row['id']);
				$result[$count]['last_login'] = date_format(date_create($row['last_login']), 'F j, Y h:i:s');
				$result[$count]['status'] = ($row['active']?"Active":"Inactive");
				$count++;
			}
			*/
		    $count = 0;
		    foreach($result as $row){
		        $result[$count]['last_login'] = date_format(date_create($row['last_login']), 'F j, Y h:i:s');
		        $result[$count]['status'] = ($row['active']?"Active":"Inactive");
		        $count++;
		    }
		    return $result;
		} else {
		    return false;
		}
		
		$cl = new reportcolumnlist();
		$cl->add('edit', 'Edit', '5%', '', 'tcen');
		$cl->add('name', 'Company Name', '25%', 'name', 'ttxt');
		$cl->add('contact', 'Contact', '20%', 'contact', 'ttxt');
		$cl->add('phone_main', 'Main Phone', '10%', '', 'ttxt');
		$cl->add('phone_contact', 'Contact', '10%', '', 'ttxt');
		$cl->add('status', 'Status', '5%', 'active', 'ttxt');
		$cl->add('username', 'Username', '10%', '', 'ttxt');
		$cl->add('last_login', 'Last access', '15%', '', 'ttxt');
		
		$table = new reporttable('reporttable');
		$table->set_columnlist($cl);
		$table->set_active_sort_array($orderbuilder->get_active_sort());
		$table->add_get_vars($this->other_vars);
		$this->table = $table->build($result);
		
		return true;
	}
}