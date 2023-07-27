<?php
class dealcontroller{
	
	private $deal;
	private $factory;
	private $processor;

	public function getTableList($request, $company_id) {
		$primaryKey = 'id';
		
		$columns = array(
			array( 'db' => 'funded', 'dt' => 0,
					'formatter' => function($d, $row) {
						return $d == 0 ? "Unfunded" : "Funded";
					}),
			array( 'db' => 'date_sold', 'dt' => 1 , 
					'formatter' => function($d, $row){
						if(!empty($d) && $d != '0000-00-00'){
							if($time = strtotime($d)){
								return date('m/d/Y', $time);
							}
						}
						return '';
					}),
			array( 'db' => 'lender_id', 'dt' => 2 ,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : lender::get_lender_name($d);
					}),
			array( 'db' => 'client_name', 'dt' => 3 ),
			array( 'db' => 'stock', 'dt' => 4 ),
			array( 'db' => 'id',  'dt' => 5,
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						return $dealClass->get_vehicle();
					}),
			array( 'db' => 'amount_financed', 'dt' => 6, 
					'formatter' => function($d, $row) {
						return $d == 0 ? "$0.00" : "$" . number_format($d, 2);
					}),
			array( 'db' => 'id', 'dt' => 7, 
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						$sales_people_id = $dealClass->get_salespeople();
						$sales_people_id = explode(",", $sales_people_id);
						$salesClass = new sales;
						$salesPeopleName = [];
						foreach($dealClass->get_salespeople() as $salespeople) {
							array_push($salesPeopleName, $salesClass->get_salesperson_name($salespeople));
						}
						return implode(" & ", $salesPeopleName);
					}),
			array( 'db' => 'deskmanager', 'dt' => 8,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : deskmanager::get_deskmanager_name($d);
					}),
			// array( 'db' => 'pending_documents', 'dt' => 9,
			// 		'formatter' => function($d, $row) {
			// 			return $d == 0 ? "none" : "pending";
			// 		}),
			array( 'db' => 'locked', 'dt' => 9,
					'formatter' => function($d, $row) {
						return $d == 0 ? "unlocked" : "locked";
					}),
			array( 'db' => 'locked', 'dt' => 10 ),
			array( 'db' => 'id', 'dt' => 11 ),
			array( 'db' => 'finance_person', 'dt' => 12,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : finance::get_financeperson_name($d);
					}),
		);

		$whereStatement = "company_id = $company_id ";
		$whereStatement = $this->deals_filter($_SESSION['FT']['topbar_filters'], $company_id, $whereStatement);
		
		$data = SSP::complex( $request, 'deals', $primaryKey, $columns, null, $whereStatement);
		$data['delete_permission'] = ($_SESSION['FT']['SEC_DELETE']) ? 'yes' : 'no';
		return json_encode($data);
	}
	
	public function getPendingTableList($request, $company_id) {
		$primaryKey = 'id';

		$columns = array(
			array( 'db' => 'funded', 'dt' => 0,
					'formatter' => function($d, $row) {
						return $d == 0 ? "Unfunded" : "Funded";
					}),
			array( 'db' => 'date_sold', 'dt' => 1, 
					'formatter' => function($d, $row){
						if(!empty($d) && $d != '0000-00-00'){
							if($time = strtotime($d)){
								return date('m/d/Y', $time);
							}
						}
						return '';
					} ),
			array( 'db' => 'lender_id', 'dt' => 2 ,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : lender::get_lender_name($d);
					}),
			array( 'db' => 'client_name', 'dt' => 3 ),
			array( 'db' => 'stock', 'dt' => 4 ),
			array( 'db' => 'id',  'dt' => 5,
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						return $dealClass->get_vehicle();
					}),
			array( 'db' => 'amount_financed', 'dt' => 6, 
					'formatter' => function($d, $row) {
						return $d == 0 ? "$0.00" : "$" . number_format($d, 2);
					}),
			array( 'db' => 'id', 'dt' => 7, 
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						$sales_people_id = $dealClass->get_salespeople();
						$sales_people_id = explode(",", $sales_people_id);
						$salesClass = new sales;
						$salesPeopleName = [];
						foreach($dealClass->get_salespeople() as $salespeople) {
							array_push($salesPeopleName, $salesClass->get_salesperson_name($salespeople));
						}
						return implode(" & ", $salesPeopleName);
					}),
			array( 'db' => 'deskmanager', 'dt' => 8,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : deskmanager::get_deskmanager_name($d);
					}),
			// array( 'db' => 'pending_documents', 'dt' => 9,
			// 		'formatter' => function($d, $row) {
			// 			return $d == 0 ? "none" : "pending";
			// 		}),
			array( 'db' => 'locked', 'dt' => 9,
					'formatter' => function($d, $row) {
						return $d == 0 ? "unlocked" : "locked";
					}),
			array( 'db' => 'locked', 'dt' => 10 ),
			array( 'db' => 'id', 'dt' => 11 ),
			array( 'db' => 'finance_person', 'dt' => 12,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : finance::get_financeperson_name($d);
					}),
		);
		$whereStatement = "pending_documents = 1 AND company_id = $company_id ";
		$whereStatement = $this->deals_filter($_SESSION['FT']['topbar_filters'], $company_id, $whereStatement);	

		$data = SSP::complex( $request, 'deals', $primaryKey, $columns, null, $whereStatement);
		$data['delete_permission'] = ($_SESSION['FT']['SEC_DELETE']) ? 'yes' : 'no';
		return json_encode($data);
	}

	public function getUnfundedTableList($request, $company_id) {
		$primaryKey = 'id';

		$columns = array(
			array( 'db' => 'date_sold', 'dt' => 0, 
				'formatter' => function($d, $row){
					if(!empty($d) && $d != '0000-00-00'){
						if($time = strtotime($d)){
							return date('m/d/Y', $time);
						}
					}
					return '';
				}),
			array( 'db' => 'lender_id', 'dt' => 1,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : lender::get_lender_name($d);
					}),
			array( 'db' => 'client_name', 'dt' => 2 ),
			array( 'db' => 'stock', 'dt' => 3 ),
			array( 'db' => 'id',  'dt' => 4,
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						return $dealClass->get_vehicle();
					}),
			array( 'db' => 'amount_financed', 'dt' => 5, 
					'formatter' => function($d, $row) {
						return $d == 0 ? "$0.00" : "$" . number_format($d, 2);
					}),
			array( 'db' => 'discount', 'dt' => 6, 
					'formatter' => function($d, $row) {
						return $d == 0 ? "$0.00" : "$" . number_format($d, 2);
					}),
			array( 'db' => 'id', 'dt' => 7, 
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						$sales_people_id = $dealClass->get_salespeople();
						$sales_people_id = explode(",", $sales_people_id);
						$salesClass = new sales;
						$salesPeopleName = [];
						foreach($dealClass->get_salespeople() as $salespeople) {
							array_push($salesPeopleName, $salesClass->get_salesperson_name($salespeople));
						}
						return implode(" & ", $salesPeopleName);
					}),
			array( 'db' => 'deskmanager', 'dt' => 8,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : deskmanager::get_deskmanager_name($d);
					}),
			
			// array( 'db' => 'pending_documents', 'dt' => 9,
			// 		'formatter' => function($d, $row) {
			// 			return $d == 0 ? "none" : "pending";
			// 		}),
			array( 'db' => 'locked', 'dt' => 9,
					'formatter' => function($d, $row) {
						return $d == 0 ? "unlocked" : "locked";
					}),
			array( 'db' => 'locked', 'dt' => 10 ),
			array( 'db' => 'id', 'dt' => 11 ),
			array( 'db' => 'finance_person', 'dt' => 12,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : finance::get_financeperson_name($d);
					}),
		);
		$whereStatement = "company_id = $company_id AND funded = 0 ";
		$whereStatement = $this->deals_filter($_SESSION['FT']['topbar_filters'], $company_id, $whereStatement);
		
		$data = SSP::complex( $request, 'deals', $primaryKey, $columns, null, $whereStatement);
		$data['delete_permission'] = ($_SESSION['FT']['SEC_DELETE']) ? 'yes' : 'no';
		return json_encode($data);
	}

	public function getFundedTableList($request, $company_id) {
		$primaryKey = 'id';

		$columns = array(
			array( 'db' => 'date_sold', 'dt' => 0, 
					'formatter' => function($d, $row){
						
						if(!empty($d) && $d != '0000-00-00'){
							if($time = strtotime($d)){
								return date('m/d/Y', $time);
							}
						}
						return '';
					}),
			array( 'db' => 'lender_id', 'dt' => 1,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : lender::get_lender_name($d);
					}),
			array( 'db' => 'client_name', 'dt' => 2 ),
			array( 'db' => 'stock', 'dt' => 3 ),
			array( 'db' => 'id',  'dt' => 4,
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						return $dealClass->get_vehicle();
					}),
			array( 'db' => 'amount_financed', 'dt' => 5, 
					'formatter' => function($d, $row) {
						return $d == 0 ? "$0.00" : "$" . number_format($d, 2);
					}),
			array( 'db' => 'discount', 'dt' => 6, 
					'formatter' => function($d, $row) {
						return $d == 0 ? "$0.00" : "$" . number_format($d, 2);
					}),
			array( 'db' => 'id', 'dt' => 7, 
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						$sales_people_id = $dealClass->get_salespeople();
						$sales_people_id = explode(",", $sales_people_id);
						$salesClass = new sales;
						$salesPeopleName = [];
						foreach($dealClass->get_salespeople() as $salespeople) {
							array_push($salesPeopleName, $salesClass->get_salesperson_name($salespeople));
						}
						return implode(" & ", $salesPeopleName);
					}),
			array( 'db' => 'deskmanager', 'dt' => 8,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : deskmanager::get_deskmanager_name($d);
					}),

			array( 'db' => 'funded_date', 'dt' => 9, 
					'formatter' => function($d, $row){
						if(!empty($d) && $d != '0000-00-00'){
							if($time = strtotime($d)){
								return date('m/d/Y', $time);
							}
						}
						return '';
					}),
			
			// array( 'db' => 'pending_documents', 'dt' => 9,
			// 		'formatter' => function($d, $row) {
			// 			return $d == 0 ? "none" : "pending";
			// 		}),
			array( 'db' => 'locked', 'dt' => 10,
					'formatter' => function($d, $row) {
						return $d == 0 ? "unlocked" : "locked";
					}),
			array( 'db' => 'locked', 'dt' => 11),
			array( 'db' => 'id', 'dt' => 12),
			array( 'db' => 'finance_person', 'dt' => 13,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : finance::get_financeperson_name($d);
					}),
		);
		$whereStatement = "company_id = $company_id AND funded = 1 AND closed_dms = 0 ";
		$whereStatement = $this->deals_filter($_SESSION['FT']['topbar_filters'], $company_id, $whereStatement);

		$data = SSP::complex( $request, 'deals', $primaryKey, $columns, null, $whereStatement);
		$data['delete_permission'] = ($_SESSION['FT']['SEC_DELETE']) ? 'yes' : 'no';
		return json_encode($data);
	}

	public function getClosedTableList($request, $company_id) {
		$primaryKey = 'id';

		$columns = array(
			array( 'db' => 'closed_dms', 'dt' => 0),
			array( 'db' => 'date_sold', 'dt' => 1, 
					'formatter' => function($d, $row){
						if(!empty($d) && $d != '0000-00-00'){
							if($time = strtotime($d)){
								return date('m/d/Y', $time);
							}
						}
						return '';
					}),
			array( 'db' => 'lender_id', 'dt' => 2,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : lender::get_lender_name($d);
					}),
			array( 'db' => 'client_name', 'dt' => 3 ),
			array( 'db' => 'stock', 'dt' => 4 ),
			array( 'db' => 'id',  'dt' => 5,
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						return $dealClass->get_vehicle();
					}),
			array( 'db' => 'amount_financed', 'dt' => 6, 
					'formatter' => function($d, $row) {
						return $d == 0 ? "$0.00" : "$" . number_format($d, 2);
					}),
			array( 'db' => 'id', 'dt' => 7, 
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						$sales_people_id = $dealClass->get_salespeople();
						$sales_people_id = explode(",", $sales_people_id);
						$salesClass = new sales;
						$salesPeopleName = [];
						foreach($dealClass->get_salespeople() as $salespeople) {
							array_push($salesPeopleName, $salesClass->get_salesperson_name($salespeople));
						}
						return implode(" & ", $salesPeopleName);
					}),
			array( 'db' => 'deskmanager', 'dt' => 8,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : deskmanager::get_deskmanager_name($d);
					}),

			
			// array( 'db' => 'pending_documents', 'dt' => 9,
			// 		'formatter' => function($d, $row) {
			// 			return $d == 0 ? "none" : "pending";
			// 		}),
			// array( 'db' => 'locked', 'dt' => 9,
			// 		'formatter' => function($d, $row) {
			// 			return $d == 0 ? "unlocked" : "locked";
			// 		}),
			array( 'db' => 'locked', 'dt' => 9),
			array( 'db' => 'id', 'dt' => 10),
			array( 'db' => 'finance_person', 'dt' => 11,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : finance::get_financeperson_name($d);
					}),
		);
		$whereStatement = "company_id = $company_id AND funded = 1 AND closed_dms = 1 ";
		$whereStatement = $this->deals_filter($_SESSION['FT']['topbar_filters'], $company_id, $whereStatement);
		
		$data = SSP::complex( $request, 'deals', $primaryKey, $columns, null, $whereStatement);
		$data['delete_permission'] = ($_SESSION['FT']['SEC_DELETE']) ? 'yes' : 'no';
		return json_encode($data);
	}

	public function getDeletedTableList($request, $company_id) {
		$primaryKey = 'id';

		$columns = array(
			array( 'db' => 'closed_dms', 'dt' => 0),
			array( 'db' => 'date_sold', 'dt' => 1, 
					'formatter' => function($d, $row){
						if(!empty($d) && $d != '0000-00-00'){
							if($time = strtotime($d)){
								return date('m/d/Y', $time);
							}
						}
						return '';
					}),
			array( 'db' => 'lender_id', 'dt' => 2,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : lender::get_lender_name($d);
					}),
			array( 'db' => 'client_name', 'dt' => 3 ),
			array( 'db' => 'stock', 'dt' => 4 ),
			array( 'db' => 'id',  'dt' => 5,
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						return $dealClass->get_vehicle();
					}),
			array( 'db' => 'amount_financed', 'dt' => 6, 
					'formatter' => function($d, $row) {
						return $d == 0 ? "$0.00" : "$" . number_format($d, 2);
					}),
			array( 'db' => 'id', 'dt' => 7, 
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						$sales_people_id = $dealClass->get_salespeople();
						$sales_people_id = explode(",", $sales_people_id);
						$salesClass = new sales;
						$salesPeopleName = [];
						foreach($dealClass->get_salespeople() as $salespeople) {
							array_push($salesPeopleName, $salesClass->get_salesperson_name($salespeople));
						}
						return implode(" & ", $salesPeopleName);
					}),
			array( 'db' => 'deskmanager', 'dt' => 8,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : deskmanager::get_deskmanager_name($d);
					}),

			
			// array( 'db' => 'pending_documents', 'dt' => 9,
			// 		'formatter' => function($d, $row) {
			// 			return $d == 0 ? "none" : "pending";
			// 		}),
			// array( 'db' => 'locked', 'dt' => 9,
			// 		'formatter' => function($d, $row) {
			// 			return $d == 0 ? "unlocked" : "locked";
			// 		}),
			array( 'db' => 'locked', 'dt' => 9),
			array( 'db' => 'id', 'dt' => 10),
			array( 'db' => 'finance_person', 'dt' => 11,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : finance::get_financeperson_name($d);
					}),
		);
		$whereStatement = "company_id = $company_id AND closed_dms = 1 AND deleted = 1 ";
		$whereStatement = $this->deals_filter($_SESSION['FT']['topbar_filters'], $company_id, $whereStatement, 'deleted');
		$data = SSP::complex( $request, 'deals', $primaryKey, $columns, null, $whereStatement);
		$data['delete_permission'] = ($_SESSION['FT']['SEC_DELETE']) ? 'yes' : 'no';
		return json_encode($data);
	}

	public function getFlooredTableList($request, $company_id) {
		$primaryKey = 'id';
		$columns = array(
			array( 'db' => 'flooring_id', 'dt' => 0,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : flooring::get_flooring_name($d);
					}),
			array( 'db' => 'date_sold', 'dt' => 1, 
					'formatter' => function($d, $row){
						if(!empty($d) && $d != '0000-00-00'){
							if($time = strtotime($d)){
								return date('m/d/Y', $time);
							}
						}
						return '';
					}),
			array( 'db' => 'client_name', 'dt' => 2),
			array( 'db' => 'stock', 'dt' => 3),
			array( 'db' => 'id',  'dt' => 4,
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						return $dealClass->get_vehicle();
					}),
			array( 'db' => 'locked', 'dt' => 5),
			array( 'db' => 'id', 'dt' => 6),
			array( 'db' => 'locked', 'dt' => 7,
					'formatter' => function($d, $row) {
						return $d == 0 ? "unlocked" : "locked";
					}),
			array( 'db' => 'deskmanager', 'dt' => 8,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : deskmanager::get_deskmanager_name($d);
					}),
			array( 'db' => 'deskmanager', 'dt' => 9,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : deskmanager::get_deskmanager_name($d);
					}),

		);

		$whereStatement = "company_id = $company_id AND closed_dms = 0 AND flooring_id > 0 ";
		$whereStatement = $this->deals_filter($_SESSION['FT']['topbar_filters'], $company_id, $whereStatement);

		$data = SSP::complex( $request, 'deals', $primaryKey, $columns, null, $whereStatement);
		$data['delete_permission'] = ($_SESSION['FT']['SEC_DELETE']) ? 'yes' : 'no';
		return json_encode($data);
	}


	public function getApprovedTableList($request, $company_id) {
		$primaryKey = 'id';

		$columns = array(
			array( 'db' => 'date_sold', 'dt' => 0, 
					'formatter' => function($d, $row){
						if(!empty($d) && $d != '0000-00-00'){
							if($time = strtotime($d)){
								return date('m/d/Y', $time);
							}
						}
						return '';
					}),
			array( 'db' => 'lender_id', 'dt' => 1,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : lender::get_lender_name($d);
					}),
			array( 'db' => 'client_name', 'dt' => 2 ),
			array( 'db' => 'stock', 'dt' => 3 ),
			array( 'db' => 'id',  'dt' => 4,
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						return $dealClass->get_vehicle();
					}),
			array( 'db' => 'amount_financed', 'dt' => 5, 
					'formatter' => function($d, $row) {
						return $d == 0 ? "$0.00" : "$" . number_format($d, 2);
					}),
			array( 'db' => 'discount', 'dt' => 6, 
					'formatter' => function($d, $row) {
						return $d == 0 ? "$0.00" : "$" . number_format($d, 2);
					}),
			array( 'db' => 'id', 'dt' => 7, 
					'formatter' => function($d, $row) {
						$dealClass = new deal;
						$dealClass->make_me_into($d);
						$sales_people_id = $dealClass->get_salespeople();
						$sales_people_id = explode(",", $sales_people_id);
						$salesClass = new sales;
						$salesPeopleName = [];
						foreach($dealClass->get_salespeople() as $salespeople) {
							array_push($salesPeopleName, $salesClass->get_salesperson_name($salespeople));
						}
						return implode(" & ", $salesPeopleName);
					}),
			array( 'db' => 'deskmanager', 'dt' => 8,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : deskmanager::get_deskmanager_name($d);
					}),
			array( 'db' => 'locked', 'dt' => 9,
					'formatter' => function($d, $row) {
						return $d == 0 ? "unlocked" : "locked";
					}),
			array( 'db' => 'locked', 'dt' => 10),
			array( 'db' => 'id', 'dt' => 11),
			array( 'db' => 'finance_person', 'dt' => 12,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : finance::get_financeperson_name($d);
					}),
		);
		$whereStatement = "company_id = $company_id AND approved = 1 ";
		$whereStatement = $this->deals_filter($_SESSION['FT']['topbar_filters'], $company_id, $whereStatement);

		$data = SSP::complex( $request, 'deals', $primaryKey, $columns, null, $whereStatement);
		$data['delete_permission'] = ($_SESSION['FT']['SEC_DELETE']) ? 'yes' : 'no';
		return json_encode($data);
	}

	public function getImportedTableList($request, $company_id) {
		// $sql = "SELECT id, client_name, make, model, stock, date_sold, client_phone, lender_id, `year` FROM `deals_import` WHERE company_id = " . $_SESSION['FT']['company_id'] . " group by client_name, make, model, stock, date_sold ";
		$primaryKey = 'id';

		$columns = array(
			array( 'db' => 'client_name', 'dt' => 0),
			array( 'db' => 'client_phone', 'dt' => 1),
			array( 'db' => 'year', 'dt' => 2),
			array( 'db' => 'stock', 'dt' => 3),
			array( 'db' => 'model', 'dt' => 4),
			array( 'db' => 'make', 'dt' => 5),
			array( 'db' => 'lender_id', 'dt' => 6,
					'formatter' => function($d, $row) {
						return $d == 0 ? "" : lender::get_lender_name($d);
					}),
			array( 'db' => 'date_sold', 'dt' => 7 , 
					'formatter' => function($d, $row){
						if(!empty($d) && $d != '0000-00-00'){
							if($time = strtotime($d)){
								return date('m/d/Y', $time);
							}
						}
						return '';
					}),
			array( 'db' => 'id', 'dt' => 8), 
			array('db' => 'id', 'dt' => 9)
		);
		$whereStatement = "company_id = $company_id ";
		$whereStatement = $this->deals_filter($_SESSION['FT']['topbar_filters'], $company_id, $whereStatement);

		$data = SSP::complex( $request, 'deals_import', $primaryKey, $columns, null, $whereStatement);
		$data['delete_permission'] = ($_SESSION['FT']['SEC_DELETE']) ? 'yes' : 'no';
		return json_encode($data);
	}
	
	public function getDeferredTableList($request, $company_id) {
		$primaryKey = 'id';

		$columns = array( 
			0 =>'customer', 
			1 => 'sales_team',
			2=> 'status',
		  	3=>'date_due',
		  	4=>'amount',
		  	5=>'method'
		);
		$select = 'SELECT
		pp.deal_id deal_id,
		if(DATE_FORMAT(pp.date_due, "%m/%d/%Y") < DATE_FORMAT(now(), "%m/%d/%Y"), "Over Due", if(DATE_FORMAT(pp.date_due, "%m/%d/%Y") > DATE_FORMAT(now(), "%m/%d/%Y"), "Waiting", "Due Today")) `status`,
		pp.id id,
		pp.date_due date_due,
		DATE_FORMAT(pp.date_due, "%m/%d/%Y") date_due_f,
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
		d.make make,
		d.model model,
		d.locked LOCKED,
		d.date_sold date_sold,
		d.flooring_paid flooring_paid,
		d.newcar as newcar,
		GROUP_CONCAT(`s`.`name` SEPARATOR " & ") AS `sales_team`,
		s.name sales_person,
		d.client_name customer';
		
		$whereStatement = "";
		$whereStatement = $this->deferred_deals_filter($_SESSION['FT']['topbar_filters'], $company_id, $whereStatement);
		$joins = " INNER JOIN (
		SELECT
			id,
			company_id,
			stock,
			make,
			model,
			LOCKED,
			deleted,
			closed_dms,
			newcar,
			funded,
			client_name,
			date_sold,
			flooring_paid
		FROM
			deals) d ON pp.deal_id = d.id
		LEFT JOIN paymentmethods pm ON pp.payment_method = pm.id
		LEFT JOIN deals_to_sales m ON d.id = m.deal_id
		LEFT JOIN salespeople s ON m.sales_id = s.id";
		$groupBY = " GROUP BY pp.id, m.deal_id";
		$orderBy = " ORDER BY ". $columns[$_REQUEST['order'][0]['column']]." ".$_REQUEST['order'][0]['dir'];

		$data = SSP::joinGroupComplex( $request, 'pickuppayments pp', $primaryKey, $columns, $select, $whereStatement, $joins, $groupBY, $orderBy);
		$data['delete_permission'] = ($_SESSION['FT']['SEC_DELETE']) ? 'yes' : 'no';
		return json_encode($data);
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
	
	private function run_view($action){
		$request = new request();
		$request->add_array($_GET);
		if(!empty(trim($request->action))) {
		    $action = trim($request->action);
		}
		$id = intval($request->id);
		if(empty($action)) {
			if(empty($id)) {
				$action = 'view';
			} else {
				$action = 'edit';
			}
		}
		
		if(empty($id) && in_array($action, array('view', 'unfunded', 'funded', 'delete', 'close', 'floored', 'search', 'approved', 'lenders', 'pending'))){
			return $this->view_deals($action, $request);
		} elseif(in_array($action,  array('edit', 'delete', 'close', 'new'))){
			return $this->deal_form($action, $request);
		} else {
			return $this->build_error('Unsupported action.');
		}
	}
	
	private function view_deals($action, request &$request){
		switch($action) {
			case 'unfunded':
				$page = new deal_view_unfunded();
			break;
			case 'funded':
				$page = new deal_view_funded();
			break;
			case 'approved':
				$page = new deal_view_approved();
			break;
			case 'delete':
				$page = new deal_view_delete();
			break;
			case 'close':
				$page = new deal_view_close();
			break;
			case 'floored':
				$page = new deal_view_floored();
			break;
			case 'search':
				$page = new deal_view_search();
			break;
			case 'lenders':
				$page = new deal_view_lenders();
			break;
			case 'pending':
				$page = new deal_view_pending();
			break;
			case 'view':
			default:
				$page = new deal_view_table();
			break;
		}
		return $page->invoke();
	}
	
	private function deal_form($action, request &$request){
		switch($action){
			case 'edit':
			case 'close':
			case 'delete':
				$deal = $this->load_deal(intval($request->id));
				if(!$deal){
					return $this->build_error('Unable to load deal.');
				}
		
				$result = $this->company_check($deal, $_SESSION['FT']['company_id']);
				if(!$result){
					return $this->build_error('Requested deal is not owned by your company.');
				}
		
				if($deal->get_deleted()){
					$action = 'delete';
				}elseif($deal->get_closed_dms()){
					$action = 'close';
				}
					
				break;
		
			case 'new':
				$deal = new deal();
				$deal->set_company_id($_SESSION['FT']['company_id']);
				break;
				
			default:
				return $this->build_error('Unsupported action in request.');
		}
		
		$deskmanagers = new deskmanager();
		$financemanagers = new finance();
		$flooring_companies = new flooring();
		$gap_companies = new gap();
		$misc_product = new misc();
		$lead_sources = new lead();
		$lenders = new lender();
		$salespeople = new sales();
		$warrany_companies = new warranty();
		$notes = new dealnote();
		
		$form_information = [
		    'deal' => $deal->to_array(),
		    'desk_managers' => $deskmanagers->get_active_deskmanagers($deal->get_company_id()),
		    'finance' => $financemanagers->get_active_financepeople($deal->get_company_id()),
		    'flooring' => $flooring_companies->get_active_flooring_companies($deal->get_company_id()),
		    'gap' => $gap_companies->get_active_gap($deal->get_company_id()),
		    'misc' => $misc_product->get_active_misc($deal->get_company_id()),
		    'leads' => $lead_sources->get_active_lead_companies($deal->get_company_id()),
		    'lenders' => $lenders->get_active_lenders($deal->get_company_id()),
		    'sales' => $salespeople->get_active_salespeople($deal->get_company_id()),
		    'warranty' => $warrany_companies->get_active_warranty($deal->get_company_id()),
		    'notes' => $notes->retrieve_notes(intval($request->id))
		];
		
		return $form_information;
		//return $deal->to_array();
		
		/*
		$settinglist = $this->load_settingslist($deal->get_company_id());
		$factory = $this->get_dealprocessorfactory();
		$factory->set_settinglist($settinglist);
		
		
		switch($action){
			case 'edit':
				$processor = $factory->make('edituseddeal', $deal);
				break;
			case 'close':
				$processor = $factory->make('closeuseddeal', $deal);
				break;
			case 'delete':
				$processor = $factory->make('deleteuseddeal', $deal);
				break;
			case 'new':
				$processor = $factory->make('newuseddeal', $deal);
				$processor->set_deal_default();
				break;
			default:
				return $this->build_error('Unsupported action in request.');
		}
		
		$form = $processor->get_form();
                
		$processor->build_form($form);
                
		return $form;
		*/
	}

	public function deals_filter($filters, $company_id, $str_where, $page_type = NULL){
		$mode = $filters['mode'];
		$month = $filters['month'];
		$year = $filters['year'];
		$datestart = $filters['datestart'];
		$dateend = $filters['dateend'];
		$lender_id = $filters['lender_id'];
		$deleted = $filters['deleted'];
		$newcar = $filters['newcar'];
		$paid = $filters['paid'];
		$locked = $filters['locked'];
		$search = trim($filters['search']);

		switch( $mode ) {
		    case "mtd": 
		        $str_where .= "AND date_sold >='".date('Y-m-01') . "' AND date_sold <='".date('Y-m-31')."' ";
		    break;
		    case 'today':
		        $str_where .= "AND date_sold ='".date("Y-m-d") . "' ";
		    break;
		    case 'prevmon':
		        $str_where .= "AND date_sold >='".date('Y-m-01', strtotime('last month')) . "' AND " . " date_sold <= '" . date('Y-m-31', strtotime('last month')) . "' ";
		    break;
		    case 'yesterday':
		        $str_where .= "AND date_sold ='".date('Y-m-d', strtotime("-1 days")) ."' ";
		    break;
		    case 'custommonth':
		        $str_where .= "AND date_sold >='".$year."-".$month."-01'" . " AND date_sold <='".$year."-".$month."-31' ";
		    break;
		    case "unlimited":
		    break;
		    case 'daterange':
					$start = new DateTime($datestart);
					$start = $start->format("Y-m-d");
					$end = new DateTime($dateend);
					$end = $end->format("Y-m-d");
					$str_where .= "AND date_sold >='".$start."' AND date_sold <= '".$end. "'";
		    break;
		    default:
		    	//$str_where .= "AND date_sold >='".date('Y-m-01') . "' AND date_sold <='".date('Y-m-31')."' ";
		        //$is_second_where = false;
		}
		if($newcar != '') {
	        $str_where .= " AND newcar = 1 ";
		}

		if($locked != '') {
	        $str_where .= " AND locked =".$locked;
		}
		if($paid != '' && $paid != 2) {
	        $str_where .= " AND flooring_paid =".$paid;
		}
		if($search != '') {
	        $str_where .= " AND CONCAT(client_name,make,model) LIKE '%".$search."%'";
		}

		if(is_null($page_type)){
			if($deleted != '' && $deleted == 1) {
        		$str_where .= " AND deleted = '". $deleted ."' ";
		    }else if($deleted == '' || $deleted == 0) {
		        $str_where .= " AND deleted = '0' ";
		    }
		}
		return $str_where;
	}

	public function deferred_deals_filter($filters, $company_id, $str_where){
		$mode = $filters['mode'];
		$month = $filters['month'];
		$year = $filters['year'];
		$datestart = $filters['datestart'];
		$dateend = $filters['dateend'];
		$lender_id = $filters['lender_id'];
		$deleted = $filters['deleted'];
		$include_deleted = $filters['include_deleted'];
		$newcar = $filters['newcar'];
		$locked = $filters['locked'];
		$paid = $filters['paid'];
		$search = trim($filters['search']);
		$str_where = " WHERE d.company_id = $company_id ";
		switch( $mode ) {
		    case "mtd": 
		        $str_where .= "AND d.date_sold >='".date('Y-m-01') . "' AND d.date_sold <='".date('Y-m-31')."'";
		    break;
		    case 'today':
		        $str_where .= "AND d.date_sold ='".date("Y-m-d") . "' ";
		    break;
		    case 'prevmon':
		        $str_where .= "AND d.date_sold >='".date('Y-m-01', strtotime('last month')) . "' AND " . " d.date_sold <= '" . date('Y-m-31', strtotime('last month')) . "'";
		    break;
		    case 'yesterday':
		        $str_where .= "AND d.date_sold ='".date('Y-m-d', strtotime("-1 days")) ."'";
		    break;
		    case 'custommonth':
		        $str_where .= "AND d.date_sold >='".$year."-".$month."-01'" . " AND d.date_sold <='".$year."-".$month."-31'";
		    break;
		    case "unlimited":
		    break;
		    case 'daterange':
					$start = new DateTime($datestart);
					$start = $start->format("Y-m-d");
					$end = new DateTime($dateend);
					$end = $end->format("Y-m-d");
					$str_where .= "AND d.date_sold >='".$start."' AND d.date_sold <= '".$end . "'";
		    break;
		    default:
		    	//$str_where .= "AND d.date_sold >='".date('Y-m-01') . "' AND d.date_sold <='".date('Y-m-31')."' ";
		        //$is_second_where = false;
		}
		if($newcar != '') {
	        $str_where .= " AND d.newcar = 1";
		}

		if($locked != '') {
	        $str_where .= " AND d.locked =".$locked;
		}

		// if($paid != '' && $paid != 2) {
	    //     $str_where .= " AND paid =".$paid;
		// }

		switch($paid) {
		    case "1":
		        $str_where .= " AND paid = '1'";
		        break;
		    case "2":
		    	break;
	    	default:
	    		$str_where .= " AND paid = '0'";
		}

		if($search != '') {
	        $str_where .= " AND LOWER(CONCAT(d.client_name,d.make,d.model,s.name)) LIKE '%".strtolower($search)."%'";
		}

	    // if($deleted != '' && $deleted == 1) {
        // 	$str_where .= " AND d.deleted = '". $deleted ."' ";
	    // }else if($deleted == '' || $deleted != 2) {
	    //     $str_where .= " AND d.deleted = '0' ";
	    // }

	    switch($deleted) {
		    case "1":
		        $str_where .= " AND d.deleted = '1' ";
		    case "0":
		        $str_where .= " AND d.deleted = '0' ";
		    break;
		    case "2":
		    break;
		}


	    switch($include_deleted) {
		    case "1":
		        $str_where .= " AND pp.deleted = 1";
		    break;
		    case "2":
		    break;
		    default:
		    	$str_where .= " AND pp.deleted = '0'";
		}

	    // if($include_deleted != '' && $include_deleted == 1) {
        // 	$str_where .= " AND pp.deleted = '". $include_deleted ."' ";
	    // }else if($include_deleted != '' || $include_deleted == 0) {
	    //     $str_where .= " AND pp.deleted = '0' ";
	    // }
//PrintVar($str_where);exit;
		return $str_where;
	}
		
	private function run_validate(){
		$request = new request();
		$request->add_array($_POST);
		
		if(!($this->test_nonce($request->get_data()))){
			return $this->build_error('Invalid secuirty data in request.');
		}
		$action = trim($request->action);
		
		if(empty($action)){
			return $this->build_error('No action defined in request');
		}
		
		switch($action){
			case 'edit':
			case 'close':
			case 'delete':
				$deal = $this->load_deal(intval($request->id));
				if(!$deal){
					return $this->build_error('Unable to load deal.');
				}
				
				$result = $this->company_check($deal, $_SESSION['FT']['company_id']);
				if(!$result){
					return $this->build_error('Requested deal is not owned by your company.');
				}
				
				if($deal->get_deleted()){
					$action = 'delete';
				}
				
				if($deal->get_closed_dms()){
					$action = 'close';
				}
					
				break;
				
			case 'new':
				$deal = new deal();
				$deal->set_company_id($_SESSION['FT']['company_id']);
				break;
				
			default:
				return $this->build_error('Unsupported action in request.');
		}

		$settinglist = $this->load_settingslist($deal->get_company_id());
		$factory = $this->get_dealprocessorfactory();
		$factory->set_settinglist($settinglist);
		
		switch($action){
			case 'edit':
				$processor = $factory->make('edituseddeal', $deal);
				break;
			case 'close':
				$processor = $factory->make('closeuseddeal', $deal);
				break;
			case 'delete':
				$processor = $factory->make('deleteuseddeal', $deal);
				break;
			case 'new':
				$processor = $factory->make('newuseddeal', $deal);
				break;
			default:
				return $this->build_error('Unsupported action in request.');
		}
		
 
		
 
	 
		//Getting Request data
		$_SESSION["finance_sale"] = $_REQUEST['finance_sale'];
        $_SESSION["finance_cost"] = $_REQUEST['finance_cost'];
		$_SESSION["misc_id"] = $_REQUEST['misc_id'];
  
 
		$result = $processor->validate($request);
	 
		// if (!$result) {
	     // var_dump($request->get_errors());
		 	// return $this->build_error('Validate Error!');
		    // return $request->get_errors();
		// }
		 

		if(!$result){
			$errors = $request->get_errors();
			$msg = "";
			foreach($errors as $key => $value){
				$msg = $key . " : " . $value;
			}
			$_SESSION['errors'] = $msg;
			header('Location: /deals/new');
			return;
		}
	 
		$processor->calculate();
		$deal->from_array($_POST);

		switch($action){
			case 'delet':
			case 'close':
			case 'edit':
				$deal->update_one();
				set_success_msg($deal->get_client_name() . '\'s deal edited successfully.');
				break;
			case 'new':
				$deal->insert_new();
				set_success_msg($deal->get_client_name() . '\'s deal created successfully.');
				break;
		}

		$pickup_id = $_POST['pickup_id'];
		$pickup_paid = $_POST['pickup_paid'];
		$pickup_delete = $_POST['pickup_delete'];
		$pickup_date_due = $_POST['pickup_date_due'];
		$pickup_amount = $_POST['pickup_amount'];
		$pickup_coupon_value = $_POST['pickup_coupon_value'];
		$pickup_amount_payable = $_POST['pickup_amount_payable'];
		$pickup_coupon_id = $_POST['pickup_coupon_id'];
		$pickup_payment_method = $_POST['pickup_payment_method'];
		$pickup_note = $_POST['pickup_note'];
		$new_coupon_applied = $_POST['new_coupon_applied'];
		if($new_coupon_applied == 1){
			
			$couponId = end($pickup_coupon_id);
			$sql = "Select * from `coupons`
				where `id` = $couponId";
				$result = db_select_assoc_array($sql);
			if($result){
				$couponUsed = $result[0]['user_used'];
				$couponUsedVal = $couponUsed + 1;
				$sql = "Update coupons SET 
				`user_used` = $couponUsedVal
				where `id` = $couponId";
				$result = db_update_bare($sql);
			}
		}
		

		// $processor->commit();
		for($i = 0; $i < sizeof($pickup_id); $i++){
			if($pickup_id[$i] != "0"){
				$sql = "Update pickuppayments SET 
				`paid` = '$pickup_paid[$i]', 
				`deleted` = '$pickup_delete[$i]', 
				`amount` = '$pickup_amount[$i]', 
				`coupon_value` = '$pickup_coupon_value[$i]', 
				`coupon_id` = '$pickup_coupon_id[$i]', 
				`amount_payable` = '$pickup_amount_payable[$i]', 
				`note` = '$pickup_note[$i]', 
				`modified_timestamp` = now(), 
				`modified_by` = ".$_SESSION['FT']['user_id'].", ". 
				"`date_due` = '$pickup_date_due[$i]', 
				`payment_method` = '$pickup_payment_method[$i]' 
				where `id` = $pickup_id[$i]";
				$result = db_update_bare($sql);
			}else{
				$deal_id = $deal->get_id();
				$sql = "insert pickuppayments SET 
				`deal_id` = '$deal_id', 
				`paid` = '$pickup_paid[$i]', 
				`deleted` = '$pickup_delete[$i]', 
				`date_due` = '$pickup_date_due[$i]', 
				`amount` = '$pickup_amount[$i]',
				`coupon_value` = '$pickup_coupon_value[$i]', 
				`coupon_id` = '$pickup_coupon_id[$i]', 
				`amount_payable` = '$pickup_amount_payable[$i]', 
				`created_timestamp` = now(), 
				`modified_timestamp` = now(), 
				`created_by` = ".$_SESSION['FT']['user_id'].", ". 
				"`modified_by` = ".$_SESSION['FT']['user_id'].", ". 
				"`note` = '$pickup_note[$i]', 
				`payment_method` = '$pickup_payment_method[$i]'";
				$result = db_insert_bare($sql);
			}

		}

		$note = $deal->get_note();
		if($note->have_note()){
			$note->prepare_new();
			$note->set_deal_id($deal->get_id());
			$note->insert_new();
		}

		if($action == 'new'){
			unset($_SESSION['new_deal_info']);

			$action = ($deal->get_funded()?'funded':'unfunded');
			$success_msg = $deal->get_client_name() . '\'s deal created successfully.';
			$encoded_msg = urlencode($success_msg);
			if ($action == 'funded') {
			    header('Location: /deals/funded?msg=' . $encoded_msg);
			} else {
			    header('Location: /deals/unfunded?msg=' . $encoded_msg);
			}
		}else{
			if(!isset($_SESSION['current_page']))
			header('Location: /deals/funded');

			switch($_SESSION['current_page']){
				case 'close':
			    header('Location: /deals/close');
				break;
				case 'deleted':
				header('Location: /deals/delete');
				break;
				case 'floored':
			    header('Location: /deals/floored');
				break;
				case 'approved':
				header('Location: /deals/approved');
				break;
				case 'unapproved':
				header('Location: /deals/unapproved');
				break;
				case 'deferred':
				header('Location: /deals/deferred');
				break;
				case 'funded':
				header('Location: /deals/funded');
				break;
				case 'unfunded':
				header('Location: /deals/unfunded');
				break;
				default:
				header('Location: /deals/funded');
			}
		}


		return $this->view_deals($action, $request);
	}
	
	private function load_settingslist($company_id){
		$settinglist = new dealsettinglist();
		$settinglist->load_company_id($company_id);
		return $settinglist;
	}
	
	private function get_dealprocessorfactory(){
		$factory = new dealprocessor_factory();
		return $factory;
	}
	
	private function load_deal($id){
		$deal = new deal();
		$result = $deal->make_me_into($id);
		if(!$result){
			return false;
		}
		return $deal;
	}
	
	private function company_check(deal $deal, $company_id){
		return ($deal->get_company_id() == $company_id);
	}
	
	private function test_nonce($request){
		if(empty($request['nonce'])){
			return false;
		}
		
		$nonce = new nonce($this->$request['nonce']);
		return $nonce->test_request($request);		
	}
	
	private function build_error($msg){
		// $error = new basicbuildable();
		// $error->add_content($msg);
		return $msg;
	}
	
	public function getLendingSource($company_id) {
		$result = db_select_assoc_array("select * from `lenders` where company_id=" . $company_id . " ORDER BY NAME ASC");
		return $result;
	}
}