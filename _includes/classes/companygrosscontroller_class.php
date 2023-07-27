<?php
class companygrosscontroller {

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
	
	private function view_deals($action, request &$request){
		$company_id = $_SESSION['FT']['company_id'];
		$mode = $_GET['mode'];
		$month = $_GET['month'];
		$year = $_GET['year'];
		$datestart = $_GET['datestart'];
		$dateend = $_GET['dateend'];
		$lender_id = $_GET['lender_id'];
		$deleted = $_GET['deleted'];
		$sortid = $_GET['sortid'];
		$sortdir=$_GET['sortdir'];
		$locked=$_GET['locked'];
		$newcar=$_GET['newcar'];
		$search=$_GET['search'];
		$paid=$_GET['paid'];

		$str_where = "";
		$is_second_where = true;
		$sort_query = "";
		
		switch( $mode ) {
			case "mtd": 
				$str_where = " WHERE d.date_sold >='".date('Y-m-01') . "' AND d.date_sold <='".date("Y-m-d")."' ";
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
		if(isset($newcar) && $newcar == 'newcar') {
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
		if(isset($search) && trim($search) != '') {
		    $str_where .= " ".$applyAnd." (d.client_name LIKE '%". $search ."%' OR CAST(DATE_FORMAT(d.date_sold,'%m/%d/%Y') as CHAR) LIKE '%". $search ."%' OR CAST(d.stock as CHAR) LIKE '%". $search ."%' OR dm.name LIKE '%". $search ."%' OR f.name LIKE '%". $search ."%') ";
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

		if(isset($sortid)) {
			$sort_query = " ORDER BY ". $sortid . " " . $sortdir;
		}else{
			$sort_query = " ORDER BY date_sold DESC";
		}
	// echo $str_where;exit;
		$companytotalsql = "SELECT 'Total' date_sold_f, COUNT(d.id) deals, dm.name deskmanager, f.name financeperson, 'Total' NAME, SUM(d.payable_gross ) - SUM(d.discount) payable_gross, (SUM(d.payable_gross)-SUM(d.discount)) / COUNT(d.id) payable_gross_average, SUM(d.payable_gross ) + SUM(d.doc_fee ) + SUM(d.pack ) - SUM(d.discount ) frontend, (SUM(d.payable_gross ) + SUM(d.doc_fee ) + SUM(d.pack ) - SUM(d.discount )) / COUNT(d.id) frontend_average, SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve) backend, (SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve)) / COUNT(d.id) backend_average, SUM(payable_gross) + SUM(pack) + SUM(doc_fee) + SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) - SUM(discount) + SUM(reserve) total_gross, COUNT(d.id) total_gross_average, SUM(financed_deal) financed_deals, SUM(dealer_check) financed_total, SUM(CASE WHEN financed_deal=1 AND funded=1 THEN dealer_check ELSE 0 END) funded_total, SUM(CASE WHEN financed_deal=1 AND funded=1 THEN 1 ELSE 0 END) funded_count, SUM(CASE WHEN financed_deal=1 AND funded=0 THEN dealer_check ELSE 0 END) unfunded_total, SUM(CASE WHEN financed_deal=1 AND funded=0 THEN 1 ELSE 0 END) unfunded_count FROM deals d LEFT JOIN deskmanagers dm ON d.deskmanager=dm.id LEFT JOIN financepeople f ON d.finance_person=f.id ".$str_where;
		
		$sql="SELECT d.id deal_id, d.date_sold date_sold, DATE_FORMAT(d.date_sold,'%m/%d/%Y') date_sold_f, d.stock stock, d.client_name client_name, dm.name deskmanager, f.name financeperson, d.payable_gross - d.discount payable_gross, (d.payable_gross + d.doc_fee + d.pack - d.discount) frontend, (warranty_gross + gap_gross + finance_gross + reserve) backend, d.holdback, (payable_gross + pack + doc_fee + warranty_gross + gap_gross + finance_gross - discount + reserve) total_gross FROM deals d LEFT JOIN deskmanagers dm ON d.deskmanager=dm.id LEFT JOIN financepeople f ON d.finance_person=f.id ".$str_where  . $sort_query;
		
		// print_r($sql);exit;
		$companytotaldata = db_select_assoc_array($companytotalsql);
		$result = db_select_assoc_array($sql);

		// projected
		$companyprojected = array();
		$projected_modifier = $this->mtd_projected_modifier();
		if($mode == 'mtd')  {
			$tot_com = $companytotaldata[0];
			$companyprojected = array(
				'date_sold_f' =>'Projected',
				'deals'=>number_format($tot_com['deals'] * $projected_modifier, 2),
				'stock'=>number_format($tot_com['deals'] * $projected_modifier,2 ),
				'payable_gross'=> number_format($tot_com['payable_gross'] * $projected_modifier, 2),
				'frontend'=> number_format($tot_com['frontend'] * $projected_modifier, 2),
				'backend'=> number_format($tot_com['backend'] * $projected_modifier, 2),
				'total_gross'=> number_format($tot_com['total_gross'] * $projected_modifier, 2),
				);
			$totaldata["projected"] = $companyprojected;
		}
		$totaldata['companytotal'] = $companytotaldata;
		$totaldata['tabledata'] = $result;
		
		return $totaldata;
	}
	public function mtd_projected_modifier(){
		//returns the modifier that needs to be entered against totals to get projected values
		$day_of_month = date('j');
		$days_in_month = date('t');
		return (float)((float)$days_in_month)/(float)$day_of_month;
	}
}