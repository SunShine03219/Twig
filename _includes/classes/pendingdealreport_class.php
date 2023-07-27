<?php

class pendingdealreport {
	
	private $table;
	
	private $total;
	private $average;
	private $projected;
	
	private $other_vars;
	
	public function __construct(){
		$this->table = '';
		
		$this->total = true;
		$this->average = true;
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
		} elseif ($result['count'] == 0){
			return false;
		}
		
		$sql = 'SELECT
		d.id dealid,
		if(d.deleted = 1, "Deleted", if(d.closed_dms = 1, "Closed", if(d.funded = 1, "Funded", "Unfunded"))) `status`,
		DATE_FORMAT(d.date_sold,"%m/%d/%Y") date_sold_f,
		d.client_name client_name,
		d.locked locked,
		d.stock stock,
		l.name lender,
		concat_ws(" ",`d`.`year`,`d`.`make`,`d`.`model`) `vehicle`,
		GROUP_CONCAT(`s`.`name` SEPARATOR " & ") AS `sales_team`,
		concat_ws(" & ",`dm`.`name`,`f`.`name`) AS `deskteam`,
		CONCAT("$",FORMAT(d.amount_financed,2)) amount_financed
		' . $fromclause . '
		LEFT JOIN '.deal::MAP_DEAL_TO_SALES.' m ON d.id=m.deal_id
		LEFT JOIN salespeople s ON m.sales_id=s.id
		LEFT JOIN lenders l ON d.lender_id=l.id
		LEFT JOIN deskmanagers dm ON d.deskmanager = dm.id
		LEFT JOIN financepeople f ON d.finance_person = f.id
		' . $whereclause . '
		GROUP BY d.id, m.deal_id ';

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
		
		$totalsql = 'SELECT
			count(*) deals,
			CONCAT("$",FORMAT(sum(amount_financed),2)) financed_total,
			CONCAT("$",FORMAT(sum(amount_financed)/count(*),2)) financed_average
		' . $fromclause . '
		' . $whereclause . '
		';
		
		$result = db_select_assoc_array($sql);
		//$salesresult = db_select_assoc_array($salessql);
		$totalresult = db_select_assoc($totalsql);
		
		$totaldata = array('amount_financed'=>$totalresult['financed_total'], 'lender'=>'TOTAL', 'stock'=>$totalresult['deals']);
		$avgdata = array('amount_financed'=>$totalresult['financed_average'], 'lender'=>'Average');
		
		/*
		$deal_to_salespeople = array();
		foreach($salesresult as $row){
			if(!is_array($deal_to_salespeople[$row['deal_id']])){
				$deal_to_salespeople[$row['deal_id']] = array();
			}
			array_push($deal_to_salespeople[$row['deal_id']], $row['name']);
		}
		*/
		if($result){
			//play with data
			$count = 0;
			$sprint_edit = "<a href=\"deals.php?id=%d\">%s</a>";
			$sprint_notes = '<a href="#" onclick="popnotes(%d);">Notes</a>';
			//$sprint_notes = "<a href=\"javascript:popnotes(%d);\">Notes</a>";
			/*
			foreach($result as $row){
				//if(isset($deal_to_salespeople[$row['dealid']])){$result[$count]['sales_team'] = join('<br/>', $deal_to_salespeople[$row['dealid']]);}
				$result[$count]['client_name'] = sprintf($sprint_edit, $row['dealid'], $row['client_name']);
				$result[$count]['notes'] = sprintf($sprint_notes, $row['dealid']);
				$result[$count]['status'] = ($row['deleted']?'Deleted':($row['closed_dms']?'Closed':($row['funded']?'Funded':'Unfunded')));
				$count++;
			}
			*/

			return $result;
		} else {
		    return false;
		}
		/*
		$cl = new reportcolumnlist();
		$cl->add('status', 'Status', '8%', 'funded', 'tcen');
		$cl->add('date_sold_f', 'Date Sold', '8%', 'date_sold', 'tcen');
		$cl->add('lender', 'Lender', '16%', 'lender', 'tcen');
		$cl->add('client_name', 'Client', '9%', 'client_name', 'tcen');
		$cl->add('stock', 'Stock', '7%', 'stock', 'tcen');
		$cl->add('notes', 'Notes', '5%', '', 'tcen');
		$cl->add('vehicle', 'Vehicle', '14%', 'vehicle', 'tcen');
		$cl->add('amount_financed', 'Amount Financed', '12%', 'amount_financed', 'tnum');
		//$cl->add('discount', 'Discount', '8%', 'discount', 'tnum');
		$cl->add('sales_team', 'Sales', '12%', '', 'tcen');
		$cl->add('deskteam', 'Desk &amp; Fin', '9%', '', 'tcen');
		
		$table = new reporttable('reporttable');
		$table->set_columnlist($cl);
		$table->set_active_sort_array($orderbuilder->get_active_sort());
		$table->add_get_var('action', 'view');
		$table->add_get_vars($this->other_vars);
		$this->table = $table->build($result, $totaldata, $avgdata);
		
		return true;
		*/
	}
}