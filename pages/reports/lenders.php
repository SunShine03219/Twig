<?php
error_reporting(E_ERROR | E_PARSE);
/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

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
$search = $_GET['search'];
$paid = $_GET['paid'];
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
//     }else if($deleted == 2) {
//         $str_where .= " WHERE d.company_id = '".$company_id."'";
//     } else {
//         $str_where .= " WHERE d.deleted = '0' AND d.company_id = '".$company_id."'";
//     }
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

$companytotalsql = 'SELECT d.client_name client_name, count(d.id) deals, "Total" name, sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount ) frontend, (sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount )) / count(d.id) frontend_average, sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve) backend, (sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve)) / count(d.id) backend_average, sum(reserve ) reserve, sum(reserve)/sum(CASE WHEN reserve > 0 THEN 1 ELSE 0 END) reserve_average, sum(d.discount ) discount, sum(d.discount ) / sum(CASE WHEN d.discount > 0 THEN 1 ELSE 0 END) discount_average, sum(payable_gross) + sum(pack) + sum(doc_fee) + sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) - sum(discount) + sum(reserve) total_gross, count(d.id) total_gross_average, DATEDIFF(d.funded_date, d.date_sent) / count(d.id) days_to_fund FROM deals d '.$str_where;

$summarysql = 'SELECT d.client_name client_name, COALESCE(l.id,0) id, COALESCE(l.name, "Unclaimed") name, count(d.id) deals, "Total" date_sold_f, DATEDIFF(d.funded_date, d.date_sent) / count(d.id) days_to_fund, sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount ) frontend, (sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount )) / count(d.id) frontend_average, sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve) backend, (sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve)) / count(d.id) backend_average, sum(reserve ) reserve, sum(reserve)/sum(CASE WHEN reserve > 0 THEN 1 ELSE 0 END) reserve_average, sum(d.discount ) discount, sum(d.discount ) / sum(CASE WHEN d.discount > 0 THEN 1 ELSE 0 END) discount_average, sum(payable_gross) + sum(pack) + sum(doc_fee) + sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) - sum(discount) + sum(reserve) total_gross, count(d.id) total_gross_average, sum(dealer_check) financed_total, sum(case when financed_deal=1 AND funded=0 THEN dealer_check ELSE 0 END) unfunded_total, sum(case when financed_deal=1 AND funded=1 THEN dealer_check ELSE 0 END) funded_total, sum(case when financed_deal=1 AND funded=0 THEN 1 ELSE 0 END) unfunded_count FROM deals d LEFT JOIN lenders l ON d.lender_id = l.id '.$str_where.' GROUP BY lender_id ORDER BY name ASC';

$dealssql = 'SELECT d.id deal_id, d.client_name client_name, d.lender_id lender_id, d.stock stock, d.date_sold date_sold, DATE_FORMAT(d.date_sold,"%m/%d/%Y") date_sold_f, (d.payable_gross + d.doc_fee + d.pack - d.discount) frontend, (warranty_gross + gap_gross + finance_gross + reserve) backend, d.reserve reserve, d.discount discount, (payable_gross + pack + doc_fee + warranty_gross + gap_gross + finance_gross - discount + reserve) total_gross, DATEDIFF(d.funded_date, d.date_sent) days_to_fund, d.dealer_check dealer_check FROM deals d '.$str_where.' ORDER BY lender_id ASC, date_sold ASC';

$companytotal = db_select_assoc($companytotalsql);
$salessummary = db_select_assoc_array($summarysql);
$deals = db_select_assoc_array($dealssql);

$total_financed = 0;
for($i = 0;$i < count($salessummary); $i++){
    $row = $salessummary[$i];
    $total_financed += $row['financed_total'];
}
$companytotal['total_financed'] = $total_financed;


$twig->addGlobal('salessummary', $salessummary);
$twig->addGlobal('companytotal', $companytotal);
$twig->addGlobal('deals', $deals);

$twig->addGlobal('page_title', 'Lenders');
$twig->addGlobal('session', $_SESSION['FT']);

$company_id = $_SESSION['FT']['company_id'];
$deal = new dealcontroller();
$lendingSource = $deal->getLendingSource($company_id);
$twig->addGlobal("lendingSource", $lendingSource);

/*
 * Render Templates
 */
echo $twig->render('reports/tpl.lenders.twig');
