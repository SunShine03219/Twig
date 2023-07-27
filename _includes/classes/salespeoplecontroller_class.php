<?php
class salespeoplecontroller {
	private $projected;

	public function  __construct() {
		$this->projected = false;
	}

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
	public function setProjected(){
		$this->projected = true;
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
			case 'daterange':
				$start = new DateTime($datestart);
				$start = $start->format("Y-m-d");
				$end = new DateTime($dateend);
				$end = $end->format("Y-m-d");
				$str_where .= " WHERE d.date_sold >='".$start."' AND d.date_sold <= '".$end . "'";
			break;
			case "unlimited":
				$is_second_where = false;
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
		if(isset($lender_id) && $mode != "unlimited") {
			$str_where .= " ".$applyAnd." d.lender_id = '".$lender_id."'";
			// if($is_second_where) {
			// 	$str_where .= " AND d.lender_id = '".$lender_id."'";
			// } else {
			// 	$str_where = " WHERE d.lender_id = '".$lender_id."'";
			// }
			$is_second_where = true;
		}
		// $str_where1 = "";
		// $is_second_where1 = "";

		// if($is_second_where) {
		// 	if(isset($deleted)) {
		// 		$str_where .= " AND d.deleted = '". $deleted ."' AND d.company_id = '".$company_id."'";
		// 		$str_where1 .= " AND d.deleted = '". $deleted ."' AND company_id = '".$company_id."'";
		// 	} else  {
		// 		$str_where .= " AND d.deleted = '0' AND d.company_id = '".$company_id."'";
		// 		$str_where1 .= " AND d.deleted = '0' AND company_id = '".$company_id."'";
		// 	}
		// } else {
		// 	if(isset($deleted) && $deleted != 2) {
		// 		$str_where .= " WHERE d.deleted = '". $deleted ."' AND d.company_id = '".$company_id."'";
		// 		$str_where1 .= " WHERE d.deleted = '". $deleted ."' AND company_id = '".$company_id."'";
		// 	}else if($deleted == 2) {
		// 		$str_where .= " WHERE d.company_id = '".$company_id."'";
		// 		$str_where1 .= " WHERE company_id = '".$company_id."'";
		// 	} else {
		// 		$str_where .= " WHERE d.deleted = '0' AND d.company_id = '".$company_id."'";
		// 		$str_where1 .= " WHERE d.deleted = '0' AND company_id = '".$company_id."'";
		// 	}
		// }

		$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
		if(isset($search) && $search !== '') {
		    $str_where .= " ".$applyAnd." CONCAT(d.client_name) LIKE '%". $search ."%'";
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

		$salessummarysql = "SELECT m.sales_id sales_id, s.name NAME, 'Total' date_sold_f, SUM(d.payable_gross * dc.share) - SUM(d.discount * dc.share) payable_gross, 
		(SUM(d.payable_gross)-SUM(d.discount)) / COUNT(d.id) payable_gross_average, SUM(d.doc_fee * dc.share) doc_fee, SUM(d.pack * dc.share) pack, 
		SUM(d.discount * dc.share) discount, SUM(d.payable_gross * dc.share) + SUM(d.doc_fee * dc.share) + SUM(d.pack * dc.share) - SUM(d.discount * dc.share) frontend, 
		(SUM(d.payable_gross ) + SUM(d.doc_fee ) + SUM(d.pack ) - SUM(d.discount )) / COUNT(d.id) frontend_average, SUM(d.warranty_gross * dc.share) warranty_gross, 
		SUM(CASE WHEN warranty_id<>0 THEN 1 ELSE 0 END) warranty_count, SUM(d.warranty_gross )/SUM(CASE WHEN warranty_gross>0 THEN 1 ELSE 0 END) warranty_average, 
		SUM(CASE WHEN warranty_id <> 0 THEN 1 ELSE 0 END)/COUNT(*) warranty_perc, SUM(d.gap_gross * dc.share) gap_gross, SUM(CASE WHEN gap_id<>0 THEN 1 ELSE 0 END) gap_count, 
		SUM(d.gap_gross)/SUM(CASE WHEN gap_gross>0 THEN 1 ELSE 0 END) gap_average, SUM(CASE WHEN gap_id <> 0 THEN 1 ELSE 0 END)/SUM(financed_deal) gap_perc, 
		SUM(d.finance_gross * dc.share) miscfinance_gross, SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_count, 
		SUM(d.finance_gross )/SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_average, SUM(CASE WHEN finance_gross > 0 THEN 1 ELSE 0 END)/COUNT(*) miscfinance_perc, 
		SUM(warranty_gross* dc.share) + SUM(gap_gross* dc.share) + SUM(finance_gross* dc.share) + SUM(reserve* dc.share) backend, 
		(SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve)) / COUNT(d.id) backend_average, 
		SUM(payable_gross* dc.share) + SUM(pack* dc.share) + SUM(doc_fee* dc.share) + SUM(warranty_gross* dc.share) + SUM(gap_gross* dc.share) + SUM(finance_gross* dc.share) - 
		SUM(discount* dc.share) + SUM(reserve* dc.share) total_gross, COUNT(d.id) total_gross_average, 
		SUM(dc.share) deals FROM deals d INNER JOIN deals_to_sales m ON d.id=m.deal_id LEFT JOIN salespeople s ON m.sales_id=s.id INNER JOIN 
		( SELECT deal_id, 1/COUNT(*) SHARE FROM deals_to_sales GROUP BY deal_id ) dc ON d.id=dc.deal_id $str_where GROUP BY sales_id UNION 
		SELECT m.sales_id sales_id, s.name NAME, 'Total' date_sold_f, SUM(d.payable_gross * dc.share) - SUM(d.discount * dc.share) payable_gross, 
		(SUM(d.payable_gross)-SUM(d.discount)) / COUNT(d.id) payable_gross_average, SUM(d.doc_fee * dc.share) doc_fee, SUM(d.pack * dc.share) pack, 
		SUM(d.discount * dc.share) discount, SUM(d.payable_gross * dc.share) + SUM(d.doc_fee * dc.share) + SUM(d.pack * dc.share) - SUM(d.discount * dc.share) frontend, 
		(SUM(d.payable_gross ) + SUM(d.doc_fee ) + SUM(d.pack ) - SUM(d.discount )) / COUNT(d.id) frontend_average, SUM(d.warranty_gross * dc.share) warranty_gross, 
		SUM(CASE WHEN warranty_id<>0 THEN 1 ELSE 0 END) warranty_count, SUM(d.warranty_gross )/SUM(CASE WHEN warranty_gross>0 THEN 1 ELSE 0 END) warranty_average, 
		SUM(CASE WHEN warranty_id <> 0 THEN 1 ELSE 0 END)/COUNT(*) warranty_perc, SUM(d.gap_gross * dc.share) gap_gross, SUM(CASE WHEN gap_id<>0 THEN 1 ELSE 0 END) gap_count, 
		SUM(d.gap_gross)/SUM(CASE WHEN gap_gross>0 THEN 1 ELSE 0 END) gap_average, SUM(CASE WHEN gap_id <> 0 THEN 1 ELSE 0 END)/SUM(financed_deal) gap_perc, 
		SUM(d.finance_gross * dc.share) miscfinance_gross, SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_count, 
		SUM(d.finance_gross )/SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_average, SUM(CASE WHEN finance_gross > 0 THEN 1 ELSE 0 END)/COUNT(*) miscfinance_perc, 
		SUM(warranty_gross* dc.share) + SUM(gap_gross* dc.share) + SUM(finance_gross* dc.share) + SUM(reserve* dc.share) backend, 
		(SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve)) / COUNT(d.id) backend_average, SUM(payable_gross* dc.share) + SUM(pack* dc.share) + 
		SUM(doc_fee* dc.share) + SUM(warranty_gross* dc.share) + SUM(gap_gross* dc.share) + SUM(finance_gross* dc.share) - SUM(discount* dc.share) + SUM(reserve* dc.share) 
		total_gross, COUNT(d.id) total_gross_average, SUM(dc.share) deals FROM deals d JOIN (SELECT 0 sales_id) m JOIN 
		(SELECT 'Unassigned' NAME) s JOIN (SELECT 1 SHARE) dc $str_where AND d.id NOT IN (SELECT deal_id id FROM deals_to_sales) GROUP BY sales_id 
		UNION SELECT s.id sales_id, s.name NAME, 'Total' date_sold_f, 0 payable_gross, 0 payable_gross_average, 0 doc_fee, 0 pack, 0 discount, 0 frontend, 0 frontend_average, 
		0 warranty_gross, 0 warranty_count, 0 warranty_average, 0 warranty_perc, 0 gap_gross, 0 gap_count, 0 gap_average, 0 gap_perc, 0 miscfinance_gross, 0 miscfinance_count, 
		0 miscfinance_average, 0 miscfinance_perc, 0 backend, 0 backend_average, 0 total_gross, 0 total_gross_average, 0 deals FROM salespeople s where company_id='".$company_id."'  and active = 1  
		AND s.id NOT IN ( SELECT m.sales_id sales_id FROM deals d INNER JOIN deals_to_sales m ON d.id=m.deal_id LEFT JOIN salespeople s 
		ON m.sales_id=s.id $str_where) ORDER BY NAME ASC";

		$totalcompanysql = "SELECT COUNT(d.id) deals, 'Total' NAME, SUM(d.payable_gross ) - SUM(d.discount) payable_gross, 
		(SUM(d.payable_gross)-SUM(d.discount)) / COUNT(d.id) payable_gross_average, SUM(d.pack ) pack, SUM(d.doc_fee ) doc_fee, SUM(d.discount ) discount, 
		SUM(d.payable_gross ) + SUM(d.doc_fee ) + SUM(d.pack ) - SUM(d.discount ) frontend, 
		(SUM(d.payable_gross ) + SUM(d.doc_fee ) + SUM(d.pack ) - SUM(d.discount )) / COUNT(d.id) frontend_average, SUM(d.warranty_gross ) warranty_gross, 
		SUM(CASE WHEN warranty_id<>0 THEN 1 ELSE 0 END) warranty_count, SUM(d.warranty_gross )/SUM(CASE WHEN warranty_gross>0 THEN 1 ELSE 0 END) warranty_average, 
		SUM(d.gap_gross ) gap_gross, SUM(CASE WHEN gap_id<>0 THEN 1 ELSE 0 END) gap_count, SUM(d.gap_gross )/SUM(CASE WHEN gap_gross>0 THEN 1 ELSE 0 END) gap_average, 
		SUM(d.finance_gross ) miscfinance_gross, SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_count, 
		SUM(d.finance_gross )/SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_average, SUM(reserve ) reserve, 
		SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve) backend, 
		(SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve)) / COUNT(d.id) backend_average, 
		SUM(payable_gross) + SUM(pack) + SUM(doc_fee) + SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) - SUM(discount) + SUM(reserve) total_gross, 
		(SUM(payable_gross) + SUM(pack) + SUM(doc_fee) + SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) - SUM(discount) + SUM(reserve)) / COUNT(d.id) 
		total_gross_average FROM deals d $str_where";

		$dealssql = "SELECT d.id deal_id, d.stock stock, COALESCE(m.sales_id,0) sales_id, dc.salescount salesperson_count, d.date_sold date_sold, 
		DATE_FORMAT(d.date_sold,'%m/%d/%Y') date_sold_f, d.payable_gross - d.discount payable_gross, d.client_name client_name, d.pack pack, d.doc_fee doc_fee, 
		d.discount discount, (d.payable_gross + d.doc_fee + d.pack - d.discount) frontend, d.warranty_gross warranty_gross, d.gap_gross gap_gross, 
		d.finance_gross miscfinance_gross, d.reserve reserve, (warranty_gross + gap_gross + finance_gross + reserve) backend, (payable_gross + pack + doc_fee + 
		warranty_gross + gap_gross + finance_gross - discount + reserve) total_gross FROM deals d LEFT JOIN deals_to_sales m ON d.id=m.deal_id LEFT JOIN 
		( SELECT deal_id, 1/count(*) share, count(*) salescount FROM deals_to_sales GROUP BY deal_id ) dc on d.id=dc.deal_id $str_where ORDER BY d.date_sold ASC";

		$totalcompany = db_select_assoc_array($totalcompanysql);
		$deals = db_select_assoc_array($dealssql);
		$salsesummary = db_select_assoc_array($salessummarysql);

		// print_r($salessummarysql);exit;

		$projected_modifier = $this->mtd_projected_modifier();

		$companyprojected;

		foreach ($salsesummary as $key => $row) {
			# code...
			if($this->projected) { // is projected
				$salsesummary[$key]['projected'] = number_format($row['deals'] * $projected_modifier, 2);
				$salsesummary[$key]['payable_gross_projected'] = number_format($row['payable_gross'] * $projected_modifier, 2);
				$salsesummary[$key]['projectedData'] = array(
					'date_sold_f' => 'Projected',
					'deals' => number_format($row['deals'] * $projected_modifier, 2),
					'stock' => number_format($row['deals'] * $projected_modifier, 2), //this is odd, but used for displaying inside the deal table
					'payable_gross'=>number_format($row['payable_gross'] * $projected_modifier, 2),
					'frontend'=>number_format($row['frontend'] * $projected_modifier, 2),
					'backend'=>number_format($row['backend'] * $projected_modifier, 2),
					'total_gross'=>number_format($row['total_gross'] * $projected_modifier, 2),
					$salessummary[$i]['projected'] = number_format($row['deals'] * $projected_modifier, 2)
			);
			}
		}

		if($this->projected){
			$tot_com = $totalcompany[0];
			$companyprojected = array(
					'name'=>'Projected',
					//'deals'=>two_decimal($companytotal['deals'] * $projected_modifier),
					'projected'=>two_decimal($tot_com['deals'] * $projected_modifier),
					'payable_gross' => number_format($tot_com['payable_gross'] * $projected_modifier, 2),
					'frontend' => number_format($tot_com['frontend'] * $projected_modifier, 2),
					'backend' => number_format($tot_com['backend'] * $projected_modifier, 2),
					'total_gross' => number_format($tot_com['total_gross'] * $projected_modifier, 2)
			);
		}
		
		$result['totalcompany'] = $totalcompany;
		$result['deals'] = $deals;
		$result['salessummary'] = $salsesummary;
		if($this->projected) {
			$result['projected'] = $companyprojected;
		}
		// print_r($result);die();
		return $result;
	}
	public function mtd_projected_modifier(){
		//returns the modifier that needs to be entered against totals to get projected values
		$day_of_month = date('j');
		$days_in_month = date('t');
		return (float)((float)$days_in_month)/(float)$day_of_month;
	}
}
	function two_decimal($val){
	if(is_numeric($val)){
		return round($val, 2);
	} else {
		return $val;
	}
}