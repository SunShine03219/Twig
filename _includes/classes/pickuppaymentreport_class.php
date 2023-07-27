<?php
class pickuppaymentreport {

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
<div class="summary_title">No pickup payments found for this report.</div><div class="gap"></div>
</div><!-- end  -->
';
	}
	
	public function execute(reportwherebuilder &$wherebuilder, reportfrombuilder &$frombuilder, reportorderbuilder &$orderbuilder){
		
		$whereclause = $wherebuilder->build_whereclause();
		$fromclause = $frombuilder->build_fromclause();
		$orderclause = $orderbuilder->build_orderclause();
		$joinclause = ' INNER JOIN (select id, company_id, stock, locked, deleted, closed_dms, funded, client_name from deals) d on pp.deal_id=d.id ';
		
		
		$sql = 'SELECT count(*) count ' . $fromclause . $joinclause . $whereclause;
		$result = db_select_assoc($sql);
		if(!$result){
			return false;
		} elseif ($result['count'] == 0){
			return false;
		}
		
		$joinclause .= ' LEFT JOIN paymentmethods pm ON pp.payment_method = pm.id ';
		$joinclause .= ' LEFT JOIN '.deal::MAP_DEAL_TO_SALES.' m ON d.id=m.deal_id ';
		$joinclause .= ' LEFT JOIN salespeople s ON m.sales_id=s.id ';
		
		$sql = 'SELECT
		pp.deal_id deal_id,
		if(DATE_FORMAT(pp.date_due,"%Y-%m-%d") < DATE_FORMAT(now(),"%Y-%m-%d"), "Over Due", if(DATE_FORMAT(pp.date_due,"%Y-%m-%d") > DATE_FORMAT(now(),"%Y-%m-%d"), "Waiting", "Due Today")) `status`,
		pp.id id,
		pp.date_due date_due,
		DATE_FORMAT(pp.date_due,"%m/%d/%Y") date_due_f,
		pp.amount amount,
		pp.coupon_value coupon_value,
		pp.coupon_id coupon_id,
		pm.caption method,
		pp.paid paid,
		pp.deleted deleted,
		d.deleted deleted,
		d.closed_dms closed_dms,
		d.funded funded,
		d.stock stock,
		d.locked locked,
		GROUP_CONCAT(`s`.`name` SEPARATOR " & ") AS `sales_team`,
		d.client_name customer ' . $fromclause . $joinclause . $whereclause . 
		' GROUP BY pp.id,m.deal_id'; 

		// if(isset($_GET['field'])){
		// 	$sql .= ' ORDER BY '.$_GET['field'].' '.$_GET['direction'];
		// }else{
		// 	$sql .= $orderclause;
		// }

		$sql .= ' ORDER BY date_due DESC';
		
		$totalSql = $sql;
		if(isset($_GET['type'])){
			if($_GET['type'] == 'pagination'){
				$sql .= " limit ". $_GET['row_count'].", 50";
			}else{
				$sql .= " limit 0, 100";
			}
		}else{
			$sql .= " limit 0, 100";
		}
		
		$custom_result = $result;

		//echo $sql; exit;
		// $result = db_select_assoc_array($sql);
		$result = db_select_assoc_array($sql);
		$custom_result['data'] = $result;
		

		$result = db_select_assoc_array($totalSql);

		$total = array();
		$totalToday = 0;
		$totalOverDue = 0;
		$totalWaiting = 0;
		if($result){
			$count = 0;
			$total['amount'] = 0.0;
			$total['deal'] = 'Total: ' . intval(count($result));
			$sprint_deal_id = '<a href="deals.php?id=%d">%s - %s</a>';
			$sprint_action_pay = '<a href="deferred_payments.php?id=%d&action=%s">%s</a>';
			$sprint_action_undelete = '<a href="deferred_payments.php?id=%s&action=undelete">Recover</a>';
			$sprint_view_log = '<a href="#" onclick="poppickuplog(%d);">Log</a>';
			$totalOverDue = 0;
			foreach($result as $row){
                                  
				$total['amount'] += $row['amount'];
				$result[$count]['customer'] = $row['customer'];
				$result[$count]['deal'] = sprintf($sprint_deal_id, $row['deal_id'],$row['customer'], $row['stock']);
				if($row['deleted']){
					$result[$count]['paid'] = sprintf($sprint_action_undelete, $row['id']);
				} else {
					$result[$count]['paid'] = sprintf($sprint_action_pay,$row['id'], ($row['paid']==1?'unpay':'pay'), ($row['paid']==1?'Mark Unpaid':'Mark Paid'));
				}
				//$result[$count]['amount'] = cashmoney($row['amount']);
				$result[$count]['log'] = sprintf($sprint_view_log, $row['deal_id']);
				$result[$count]['status'] = $this->get_payment_status($row);
				unset($row['deleted']);
				if($this->get_payment_status($row) == 'Over Due')
					$totalOverDue += $row['amount'];
				if($this->get_payment_status($row) == 'Due Today')
					$totalToday += $row['amount'];
				if($this->get_payment_status($row) == 'Waiting')
					$totalWaiting += $row['amount'];

				$count++;
			}

			$custom_result['info']['totalDue'] = $total['amount'];
			$custom_result['info']['totalDueToday'] = $totalToday;
			$custom_result['info']['totalPastDue'] = $totalWaiting;
			$custom_result['info']['totalOverDue'] = $totalOverDue;

			return $custom_result;
		} else {
		    return false;
		}
		/*
		$total['amount'] = 'Over Due: ' . cashmoney($totalOverDue) . '&nbsp;&nbsp;'.'Total: ' .  cashmoney($total['amount']);
		$total['status'] = 'Due Today: ' . cashmoney($totalToday) . '&nbsp;&nbsp;'.'Past Due: ' . cashmoney($totalWaiting);
		*/
		/*
		$total['over'] = '';
		$total['today'] = 'Due Today: ' . cashmoney($totalOverDue);
		$total['waiting'] = 'Waiting: ' . cashmoney($totalOverDue);
		*/
        /*
		$cl = new reportcolumnlist();
		$cl->add('deal', 'Deal', '', 'stock', 'tcen');
		$cl->add('sales_team', 'Sales', '', '', 'tcen');
		$cl->add('status', 'Status', '', '', 'tcen');
		$cl->add('log', 'Log', '', '', 'tcen');
		$cl->add('date_due_f', 'Due Date', '', 'date_due', 'tcen');
		$cl->add('amount', 'Amount', '', 'amount', 'tnum');
		$cl->add('method', 'Payment Method', '', 'method', 'tcen');
		$cl->add('paid','Action', '', 'paid', 'tcen');
				
		$table = new reporttable('reporttable');
		$table->set_columnlist($cl);
		$table->set_active_sort_array($orderbuilder->get_active_sort());
		$table->add_get_vars($this->other_vars);
		$this->table = $table->build($result, $total);
		
		return true;
		*/
	}
	
	public function get_payment_status($row){
		if($row['deleted'] == pickuppayment::DELETED){
			return 'Deleted';
		}
		if($row['paid']==pickuppayment::PICKUP_PAID){
			return 'Paid';
		}
		$date_due = strtotime($row['date_due']);
		$today = strtotime('today');
		if($date_due < $today){
			return 'Over Due';
		} elseif ($date_due == $today){
			return 'Due Today';
		} elseif ($date_due > $today){
			return 'Waiting';
		}
		return 'Unknown';
	}
	
}