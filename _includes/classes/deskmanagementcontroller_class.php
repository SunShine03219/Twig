<?php
class deskmanagementcontroller {

	public function invoke($action){
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		
		switch($method){
			case 'post':
				return $this->run_validate();
				break;
				
			case 'get':
			    return $this->run_view($action);
				break;
				
			default:
				return $this->build_error('Unsupported request method');
		}
	}
	
	private function run_view($action){
        $request = new request();
		$request->add_array($_GET);
        $action = trim($request->action);
        $id = intval($request->id);
		if(empty($action)){
			if(empty($id)){
				$action = 'view';
			} else {
				$action = 'edit';
			}
        }
        
		if(empty($id) && in_array($action, array('view'))){
			return $this->view_deals($action, $request);
		} elseif(in_array($action,  array('edit'))){
			return $this->deal_form($action, $request);
		} else {
			return $this->build_error('Unsupported action.');
		} 
	}

	function mtd_projected_modifier(){
		//returns the modifier that needs to be entered against totals to get projected values
		$day_of_month = date('j');
		$days_in_month = date('t');
		return (float)((float)$days_in_month)/(float)$day_of_month;
	}

	function two_decimal($val){
		if(is_numeric($val)){
			return round($val, 2);
		} else {
			return $val;
		}
	}
	
	private function view_deals($action, request &$request){
		$company_id = $_SESSION['FT']['company_id'];
		$mode = $_GET['mode'];
		$month = $_GET['month'];
		$year = $_GET['year'];
		$datestart = $_GET['datestart'];
		$dateend = $_GET['dateend'];
		$lender_id = $_GET['lender_id'];
		$deleted = $_GET['deleted'];
		$locked = $_GET['locked'];
		$newcar = $_GET['newcar'];
		$search = $_GET['search'];
		$paid = $_GET['paid'];
		
		$str_where = "";
		$is_second_where = true;
		switch( $mode ) {
			case "mtd": 
				$str_where = " WHERE d.date_sold >='".date('Y-m-01') . "' AND d.date_sold <='".date("Y-m-31")."' ";
			break;
			case 'today':
				$str_where = " WHERE d.date_sold ='".date("Y-m-d") . "' ";
			break;
			case 'prevmon':
				$str_where = " WHERE d.date_sold >='".date('Y-m-01', strtotime('last month')) . "' AND " . " d.date_sold <= '" . date('Y-m-31', strtotime('last month')) . "' ";
			break;
			case 'yesterday':
				$str_where = " WHERE d.date_sold ='".date('Y-m-d', strtotime("-1 days")) ."' ";
			break;
			case 'custommonth':
				$str_where = " WHERE d.date_sold >='".$year."-".$month."-01'" . " AND d.date_sold <='".$year."-".$month."-31' ";
			break;
			case "unlimited":
				$is_second_where = false;
			break;
			case 'daterange':
				$start = new DateTime($datestart);
				$start = $start->format("Y-m-d");
				$end = new DateTime($dateend);
				$end = $end->format("Y-m-d");
				$str_where .= " WHERE d.date_sold >='".$start."' AND d.date_sold <= '".$end . "'";
			break;
			default:
				$is_second_where = false;
			break;
		}

		$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
		if(isset($newcar) && $newcar='newcar') {
			$str_where .= " ".$applyAnd." d.newcar = 1 ";
			// if($is_second_where) {
			// 	$str_where .= " AND d.newcar = 1 ";
			// } else {
			// 	$str_where = " WHERE d.newcar = 0 ";
			// }
			$is_second_where = true;
		}

		$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
		if(isset($locked) && $locked != '') {
			$str_where .= " ".$applyAnd." d.locked =".$locked;
		    // if($is_second_where) {
		    //     $str_where .= " AND d.locked =".$locked;
		    // } else {
		    //     $str_where = " WHERE d.locked =".$locked;;
		    // }
		    $is_second_where = true;
		}
		
		$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
		if(isset($lender_id)) {
			$str_where .= " ".$applyAnd." d.lender_id = '".$lender_id."'";
			// if($is_second_where) {
			// 	$str_where .= " AND d.lender_id = '".$lender_id."'";
			// } else {
			// 	$str_where = " WHERE d.lender_id = '".$lender_id."'";
			// }
			$is_second_where = true;
		}
		// if($is_second_where) {
		// 	if(isset($deleted)) {
		// 		$str_where .= " AND d.deleted = '". $deleted ."' AND d.company_id = '".$company_id."'";
		// 	} else  {
		// 		$str_where .= " AND d.deleted = '0' AND d.company_id = '".$company_id."'";
		// 	}
		// } else {
		// 	if(isset($deleted) && $deleted != 2) {
		// 		$str_where .= " WHERE d.deleted = '". $deleted ."' AND d.company_id = '".$company_id."'";
		// 	}elseif($deleted == 2) {
		// 		$str_where .= " WHERE d.company_id = '".$company_id."'";
		// 	} else {
		// 		$str_where .= " WHERE d.deleted = '0' AND d.company_id = '".$company_id."'";
		// 	}
		// }

		$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
		if(isset($search) && $search !== '') {
		    $str_where .= " ".$applyAnd." CONCAT(dm.name) LIKE '%". $search ."%'";
		    // $str_where .= " ".$applyAnd." (d.client_name LIKE '%". $search ."%' OR CAST(DATE_FORMAT(d.date_sold,'%m/%d/%Y') as CHAR) LIKE '%". $search ."%' OR CAST(d.stock as CHAR) LIKE '%". $search ."%' OR dm.name LIKE '%". $search ."%' OR f.name LIKE '%". $search ."%') ";
		    $is_second_where = true;
		}

		$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
		if(isset($paid) && $paid != 2) {
		    $str_where .= " ".$applyAnd." d.flooring_paid = '". $paid ."'";
		    $is_second_where = true;
		}

		$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
		if(isset($deleted) && $deleted == 1) {
		    $str_where .= " ".$applyAnd." d.deleted = '". $deleted ."' AND d.company_id = '".$company_id."'";
		}elseif($deleted == 2) {
		    $str_where .= " ".$applyAnd." d.company_id = '".$company_id."'";
		} else {
		    $str_where .= " ".$applyAnd." d.deleted = '0' AND d.company_id = '".$company_id."'";
		}

		$dealssql = "SELECT d.id deal_id, COALESCE(d.deskmanager, 0) deskmanager_id, dm.name deskmanager_name, d.stock stock, d.date_sold date_sold, DATE_FORMAT(d.date_sold,'%m/%d/%Y') date_sold_f, d.payable_gross - d.discount payable_gross, d.client_name client_name, (d.payable_gross + d.doc_fee + d.pack - d.discount) frontend, w.name warranty_provider, d.warranty_gross warranty_gross, g.name gap_provider, d.gap_gross gap_gross, d.finance_gross miscfinance_gross, d.reserve reserve, (warranty_gross + gap_gross + finance_gross + reserve) backend, (payable_gross + pack + doc_fee + warranty_gross + gap_gross + finance_gross - discount + reserve) total_gross FROM deals d LEFT JOIN warrantyproviders w ON d.warranty_id = w.id LEFT JOIN gapproviders g ON d.gap_id = g.id LEFT JOIN deskmanagers dm ON COALESCE(d.deskmanager, 0)=dm.id $str_where ORDER BY date_sold";
		
		$summarysql = "SELECT COALESCE(dm.id,0) id, COALESCE(dm.name, 'Unclaimed') name, count(d.id) deals, 'Total' date_sold_f, sum(d.payable_gross ) - sum(d.discount) payable_gross, (sum(d.payable_gross)-sum(d.discount)) / count(d.id) payable_gross_average, sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount ) frontend, (sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount )) / count(d.id) frontend_average, sum(d.warranty_gross ) warranty_gross, sum(CASE WHEN warranty_id <> 0 THEN 1 ELSE 0 END)/count(*) warranty_perc, sum(d.warranty_gross )/sum(case when warranty_gross>0 then 1 else 0 end) warranty_average, sum(d.gap_gross ) gap_gross, sum(CASE WHEN gap_id <> 0 THEN 1 ELSE 0 END)/sum(financed_deal) gap_perc, sum(d.gap_gross )/sum(case when gap_gross>0 then 1 else 0 end) gap_average, sum(d.finance_gross ) miscfinance_gross, sum(CASE WHEN finance_gross > 0 THEN 1 ELSE 0 END)/count(*) miscfinance_perc, sum(d.finance_gross )/sum(case when finance_gross>0 then 1 else 0 end) miscfinance_average, sum(reserve ) reserve, sum(reserve)/sum(CASE WHEN reserve > 0 THEN 1 ELSE 0 END) reserve_average, sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve) backend, (sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve)) / count(d.id) backend_average, sum(payable_gross) + sum(pack) + sum(doc_fee) + sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) - sum(discount) + sum(reserve) total_gross, count(d.id) total_gross_average FROM deals d LEFT JOIN deskmanagers dm ON d.deskmanager = dm.id $str_where GROUP BY deskmanager ORDER BY deskmanager ASC";

		$totalcompanysql = "SELECT dm.name deskmanager_name, COUNT(d.id) deals, 'Total' NAME, SUM(d.payable_gross ) - SUM(d.discount) payable_gross, (SUM(d.payable_gross)-SUM(d.discount)) / COUNT(d.id) payable_gross_average, SUM(d.pack ) pack, SUM(d.doc_fee ) doc_fee, SUM(d.discount ) discount, SUM(d.payable_gross ) + SUM(d.doc_fee ) + SUM(d.pack ) - SUM(d.discount ) frontend, (SUM(d.payable_gross ) + SUM(d.doc_fee ) + SUM(d.pack ) - SUM(d.discount )) / COUNT(d.id) frontend_average, SUM(d.warranty_gross ) warranty_gross, (SUM(CASE WHEN warranty_id <> 0 THEN 1 ELSE 0 END)/COUNT(*))*100 warranty_perc, SUM(d.warranty_gross )/SUM(CASE WHEN warranty_gross>0 THEN 1 ELSE 0 END) warranty_average, SUM(d.gap_gross ) gap_gross, (SUM(CASE WHEN gap_id <> 0 THEN 1 ELSE 0 END)/SUM(financed_deal))*100 gap_perc, SUM(d.gap_gross )/SUM(CASE WHEN gap_gross>0 THEN 1 ELSE 0 END) gap_average, SUM(d.finance_gross ) miscfinance_gross, (SUM(CASE WHEN finance_gross > 0 THEN 1 ELSE 0 END)/COUNT(*))*100 miscfinance_perc, SUM(d.finance_gross )/SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_average, SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve) backend, (SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve)) / COUNT(d.id) backend_average, SUM(payable_gross) + SUM(pack) + SUM(doc_fee) + SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) - SUM(discount) + SUM(reserve) total_gross, COUNT(d.id) total_gross_average FROM deals d LEFT JOIN deskmanagers dm ON d.deskmanager = dm.id $str_where";

		// print_r($summarysql);exit;
		$summarydata = db_select_assoc_array($summarysql);
		$totalcompanydata = db_select_assoc_array($totalcompanysql);
		$dealsdata = db_select_assoc_array($dealssql);

		// projected
		$result;
		$projected_modifier = $this->mtd_projected_modifier();
		if($mode == 'mtd')  {
			$result["projected"] = array(
					'name'=>'Projected',
					'deals'=>round($totalcompanydata[0]['deals'] * $projected_modifier),
					'payable_gross'=>number_format($totalcompanydata[0]['payable_gross'] * $projected_modifier, 2),
					'frontend' => number_format($totalcompanydata[0]['frontend'] * $projected_modifier, 2),
					'warranty_gross'=>number_format($totalcompanydata[0]['warranty_gross'] * $projected_modifier, 2),
					'gap_gross'=>number_format($totalcompanydata[0]['gap_gross'] * $projected_modifier, 2),
					'miscfinance_gross'=>number_format($totalcompanydata[0]['miscfinance_gross'] * $projected_modifier, 2),
					'backend' => number_format($totalcompanydata[0]['backend'] * $projected_modifier, 2),
					'total_gross' => number_format($totalcompanydata[0]['total_gross'] * $projected_modifier, 2)
				);
		}

		if($mode == 'mtd')  {
			for($i = 0;$i < count($summarydata); $i++){
				$row = $summarydata[$i];
				$summarydata[$i]["projected"] = array(
						'date_sold_f'=>'Projected',
						'stock'=>number_format($row['deals']*$projected_modifier, 2),
						'payable_gross'=>number_format($row['payable_gross']*$projected_modifier, 2),
						'frontend'=>number_format($row['frontend']*$projected_modifier, 2),
						'warranty_provider'=>'&nbsp;',
						'warranty_gross'=>number_format($row['warranty_gross']*$projected_modifier, 2),
						'gap_provider'=>'&nbsp;',
						'gap_gross'=>number_format($row['gap_gross']*$projected_modifier, 2),
						'miscfinance_gross'=>number_format($row['miscfinance_gross']*$projected_modifier, 2),
						'backend'=>number_format($row['backend']*$projected_modifier, 2),
						'total_gross'=>number_format($row['total_gross']*$projected_modifier, 2)
				);
			}
		}

		for($i = 0;$i < count($dealsdata); $i++){
			$row = $dealsdata[$i];
			
			if($row['warranty_provider'] != ''){
				for($j = 0; $j < count($summarydata); $j++){
					if($summarydata[$j]['id'] == $row['deskmanager_id']){
						$totalcompanydata[0]['total_warranty'] += 1;
						$summarydata[$j]['total_warranty'] += 1;
						break;
					}
				}
			}

			if($row['gap_provider'] != ''){
				for($j = 0; $j < count($summarydata); $j++){
					if($summarydata[$j]['id'] == $row['deskmanager_id']){
						$totalcompanydata[0]['total_gap'] += 1;
						$summarydata[$j]['total_gap'] += 1;
						break;
					}
				}
			}
		}
		
		for($i = 0;$i < count($summarydata); $i++){
			$row = $summarydata[$i];
			if($row['total_warranty'] > 0){
				$summarydata[$i]['warranty_gross_average'] = $row['warranty_gross'] / $row['total_warranty'];
			}
		}

		$totalcompanydata[0]["av_warranty_gross"] = 0;
		if($totalcompanydata[0]["total_warranty"] > 0) {
			$totalcompanydata[0]["av_warranty_gross"] = $totalcompanydata[0]["warranty_gross"] / $totalcompanydata[0]["total_warranty"];
		}

		$result['dealsdata'] = $dealsdata;
		$result['summarydata'] = $summarydata;
		$result['totalcompanydata'] = $totalcompanydata;
		
		// print_r($totalcompanydata[0]);exit;
		return $result;
	}
}