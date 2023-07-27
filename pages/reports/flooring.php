<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$twig->addGlobal('page_title', 'Flooring');
$twig->addGlobal('session', $_SESSION['FT']);

$company_id = $_SESSION['FT']['company_id'];
$mode = $_GET['mode'];
$month = $_GET['month'];
$year = $_GET['year'];
$datestart = $_GET['datestart'];
$dateend = $_GET['dateend'];
$lender_id = $_GET['lender_id'];
$deleted = $_GET['deleted'];
$newcar = $_GET['newcar'];
$locked = $_GET['locked'];
$paid = $_GET['paid'];
$search = $_GET['search'];
$str_where = "";
$is_second_where = true;
if(!isset($mode)) {
    $mode = "mtd";
}

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
    //     $str_where .= " AND d.newcar = 1 ";
    // } else {
    //     $str_where = " WHERE d.newcar = 1 ";
    // }
    $is_second_where = true;
}

$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
if($locked != '' && $locked != '2') {
    $str_where .= " ".$applyAnd." d.locked =".$locked;
    // if($is_second_where) {
    //     $str_where .= " AND d.locked =".$locked;
    // } else {
    //     $str_where = " WHERE d.locked =".$locked;;
    // }
    $is_second_where = true;
}

$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
//if(isset($lender_id) && $mode != "unlimited") {
if(isset($lender_id) && $lender_id != 0) {
    $str_where .= " ".$applyAnd." d.lender_id = '".$lender_id."'";
    // if($is_second_where) {
    //     $str_where .= " AND d.lender_id = '".$lender_id."'";
    // } else {
    //     $str_where = " WHERE d.lender_id = '".$lender_id."'";
    // }
    $is_second_where = true;
}
// if($is_second_where) {
//     if(isset($deleted)) {
//         $str_where .= " AND d.deleted = '". $deleted ."' AND d.company_id = '".$company_id."'";
//     } else  {
//         $str_where .= " AND d.deleted = '0' AND d.company_id = '".$company_id."'";
//     }
// } else {
//     if(isset($deleted) && $deleted != 2) {
//         $str_where .= " WHERE d.deleted = '". $deleted ."' AND d.company_id = '".$company_id."'";
//     }elseif($deleted == 2) {
//         $str_where .= " WHERE d.company_id = '".$company_id."'";
//     } else {
//         $str_where .= " WHERE d.deleted = '0' AND d.company_id = '".$company_id."'";
//     }
// }

$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
if(trim($search) != '') {
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

$companytotalsql = 'SELECT d.client_name client_name, count(d.id) deals, "Total" name, sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount ) frontend, (sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount )) / count(d.id) frontend_average, sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve) backend, (sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve)) / count(d.id) backend_average, sum(payable_gross) + sum(pack) + sum(doc_fee) + sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) - sum(discount) + sum(reserve) total_gross, count(d.id) total_gross_average FROM deals d '.$str_where.' AND d.flooring_id > "0"';

$summarysql = 'SELECT d.client_name client_name, fl.id id, fl.name name, count(d.id) deals, "Total" date_sold_f, sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount ) frontend, (sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount )) / count(d.id) frontend_average, sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve) backend, (sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve)) / count(d.id) backend_average, sum(payable_gross) + sum(pack) + sum(doc_fee) + sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) - sum(discount) + sum(reserve) total_gross, count(d.id) total_gross_average FROM deals d LEFT JOIN flooring fl ON d.flooring_id = fl.id '.$str_where.' AND d.flooring_id > "0" GROUP BY flooring_id ORDER BY date_sold DESC';

$sql = 'SELECT d.client_name client_name, d.date_sold date_sold, DATE_FORMAT(d.date_sold,"%m/%d/%Y") date_sold_f, d.id deal_id, d.flooring_id flooring_id, d.stock stock, concat_ws(" ",d.year,d.make,d.model) vehicle, (d.payable_gross + d.doc_fee + d.pack - d.discount) frontend, (warranty_gross + gap_gross + finance_gross + reserve) backend, (payable_gross + pack + doc_fee + warranty_gross + gap_gross + finance_gross - discount + reserve) total_gross FROM deals d ' . $str_where . ' AND d.flooring_id > "0" ORDER BY date_sold DESC';

$companytotal = db_select_assoc($companytotalsql);
$summary = db_select_assoc_array($summarysql);
$result = db_select_assoc_array($sql);

//echo $sql;exit;

if($result){
    $count = 0;
    foreach($result as $row){
        $result[$count]['status'] = ($row['deleted']?'Deleted':($row['closed_dms']?'Closed':($row['funded']?'Funded':'Unfunded')));
        $count++;
    }
} 

$total = array();
$total[] = ['flooring','used'];
$total1 = array();
$total1[] = ['flooring','Front End','Back End'];
$total2 = array();
$total2[] = ['flooring','Front End','Back End'];

foreach($summary as $sum){
    $total[] = array($sum["name"], $sum['deals']);
    $total1[] = array($sum["name"], $sum['frontend'], $sum['backend'], '$'.number_format($sum['frontend'] + $sum['backend'], 0, '.', ','));
    $total2[] = array($sum["name"], $sum['frontend_average'], $sum['backend_average']);
}

$twig->addGlobal('totals', $total);
$twig->addGlobal('total1s', $total1);
$twig->addGlobal('total2s', $total2);
$twig->addGlobal('floorings', $result);
$twig->addGlobal('summaries', $summary);
$twig->addGlobal('companytotal', $companytotal);


/*
 * Render Templates
 */
echo $twig->render('reports/tpl.flooring.twig');