<?php

class tradeindealreport {
	
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
			d.closed_dms closed_dms,
			d.date_sold date_sold,
			d.deleted deleted,
			d.funded funded,
			DATE_FORMAT(d.date_sold,"%m/%d/%Y") date_sold_f,
			d.client_name client_name,
			d.stock stock,
			l.name lender,
			d.year year,
			d.make make,
			d.model model,
			d.finance_acv finance_acv,
			d.finance_miles finance_miles,
			d.finance_payoff finance_payoff,
			concat_ws(" ",`d`.`year`,`d`.`make`,`d`.`model`) `vehicle`,
			GROUP_CONCAT(`s`.`name` SEPARATOR "<br/>") AS `sales_team`,
			concat_ws("<br/>",`dm`.`name`,`f`.`name`) AS `deskteam`,
			CONCAT("$",FORMAT(d.amount_financed,2)) amount_financed
			' . $fromclause . '
			LEFT JOIN '.deal::MAP_DEAL_TO_SALES.' m ON d.id=m.deal_id
			LEFT JOIN salespeople s ON m.sales_id=s.id
			LEFT JOIN lenders l ON d.lender_id=l.id
			LEFT JOIN deskmanagers dm ON d.deskmanager = dm.id
			LEFT JOIN financepeople f ON d.finance_person = f.id
			' . $whereclause . '
			GROUP BY d.id, m.deal_id 
			' . $orderclause;
		
			if(isset($_GET['type'])){
				if($_GET['type'] == 'pagination'){
					$sql .= " limit ". $_GET['row_count'] .", 50";
				}
			}else{
				$sql .= " limit 0, 100";
			}
			
		/*
		$salessql = 'SELECT
		m.deal_id deal_id,
		m.sales_id sales_id,
		s.name name
		FROM `'.deal::MAP_DEAL_TO_SALES.'` m
		LEFT JOIN `'.sales::TABLE.'` s ON m.sales_id = s.id
		WHERE m.deal_id
		IN (
		SELECT id deal_id
		' . $fromclause . '
		' . $whereclause . ' )';
		*/
		
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
			foreach($result as $row){
				//if(isset($deal_to_salespeople[$row['dealid']])){$result[$count]['sales_team'] = join('<br/>', $deal_to_salespeople[$row['dealid']]);}
				$result[$count]['client_name'] = sprintf($sprint_edit, $row['dealid'], $row['client_name']);
				$result[$count]['finance_payoff'] = ($row['finance_payoff']?'Yes':'No');
				$result[$count]['funded'] = ($row['funded']?'Funded':'Unfunded');
				$count++;
			}
		}
		
		$cl = new reportcolumnlist();
		$cl->add('stock', 'Stock', '7%', 'stock', 'tcen');
		$cl->add('client_name', 'Customer name', '9%', 'client_name', 'tcen');
		$cl->add('year', 'Year', '10%', 'year', 'tcen');
		$cl->add('make', 'Make', '10%', 'make', 'tcen');
		$cl->add('model', 'Model', '10%', 'model', 'tcen');
		$cl->add('finance_acv', 'ACV', '10%', 'finance_acv', 'tcen');
		$cl->add('finance_miles', 'Miles', '10%', 'finance_miles', 'tcen');
		$cl->add('finance_payoff', 'Payoff', '10%', 'finance_payoff', 'tcen');
		$cl->add('funded', 'Funded', '10%', 'funded', 'tcen');
		
		$table = new reporttable('reporttable');
		$table->set_columnlist($cl);
		$table->set_active_sort_array($orderbuilder->get_active_sort());
		$table->add_get_var('action', 'view');
		$table->add_get_vars($this->other_vars);
		// $this->table = $table->build($result, $totaldata, $avgdata);
		$this->table = $table->build($result);
		
		return true;
	}
}