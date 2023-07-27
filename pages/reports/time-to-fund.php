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
$deleted = $_GET['deleted'];
$newcar = $_GET['newcar'];
$locked = $_GET['locked'];
$paid = $_GET['paid'];
$search = $_GET['search'];
$sortid = $_GET['sortid'];
$sortdir = $_GET['sortdir'];

$str_where = "";
$is_second_where = true;
switch( $mode ) {
    case "mtd": 
        $str_where = " WHERE d.date_sold >='".date('Y-m-01') . "' AND d.date_sold <='".date('Y-m-d')."' ";
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
        $str_where = " WHERE d.date_sold >='".date('Y-m-01') . "' AND d.date_sold <='".date('Y-m-31')."' ";
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

if($locked != '' && $locked != '2') {
    $str_where .= " ".$applyAnd." d.locked =".$locked;
    // if($is_second_where) {
    //     $str_where .= " AND d.locked =".$locked;
    // } else {
    //     $str_where = " WHERE d.locked =".$locked;;
    // }
    $is_second_where = true;
}

// $applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
// $str_where .= " ".$applyAnd." d.company_id = '".$company_id."'";


$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
if(trim($search) !== '') {
    //$str_where .= " ".$applyAnd." CONCAT(d.client_name,d.make,d.model) LIKE '%". $search ."%'";
    $str_where .= " ".$applyAnd." (d.client_name LIKE '%". $search ."%' OR CAST(DATE_FORMAT(d.date_sold,'%m/%d/%Y') as CHAR) LIKE '%". $search ."%' OR CAST(DATE_FORMAT(d.approved_date,'%m/%d/%Y') as CHAR) LIKE '%". $search ."%' OR CAST(d.stock as CHAR) LIKE '%". $search ."%' OR f.name LIKE '%". $search ."%' OR d.model LIKE '%". $search ."%' OR d.make LIKE '%". $search ."%') ";
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

//echo $str_where;exit;

// if($is_second_where) {
//     $str_where .= " AND d.company_id='".$company_id . "'";
// } else {
//     $str_where .= " WHERE d.company_id='".$company_id."'";
// }

function mtd_projected_modifier(){
    $day_of_month = date('j');
    $days_in_month = date('t');
    return (float)((float)$days_in_month)/(float)$day_of_month;
}

$companytotalsql = 'SELECT d.id deal_id, d.date_sold date_sold, DATE_FORMAT(d.date_sold,"%m/%d/%Y") date_sold_f, d.finance_payment finance_payment, d.stock stock, d.client_name client_name, dm.name deskmanager, d.model model, d.make make, f.name financeperson, "Total" date_sold_f, count(d.id) deals, "Total" name, sum(d.payable_gross ) - sum(d.discount) payable_gross, (sum(d.payable_gross)-sum(d.discount)) / count(d.id) payable_gross_average, sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount ) frontend, (sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount )) / count(d.id) frontend_average, sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve) backend, (sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve)) / count(d.id) backend_average, sum(payable_gross) + sum(pack) + sum(doc_fee) + sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) - sum(discount) + sum(reserve) total_gross, count(d.id) total_gross_average, sum(financed_deal) financed_deals, sum(dealer_check) financed_total, sum(case when financed_deal=1 AND funded=1 THEN dealer_check ELSE 0 END) funded_total, sum(case when financed_deal=1 AND funded=1 THEN 1 ELSE 0 END) funded_count, sum(case when financed_deal=1 AND funded=0 THEN dealer_check ELSE 0 END) unfunded_total, sum(case when financed_deal=1 AND funded=0 THEN 1 ELSE 0 END) unfunded_count FROM deals d LEFT JOIN deskmanagers dm on d.deskmanager=dm.id LEFT JOIN financepeople f ON d.finance_person=f.id '.$str_where.' AND d.approved = "1"';

$dealssql = 'SELECT d.id deal_id, d.date_sold date_sold, DATE_FORMAT(d.date_sold,"%m/%d/%Y") date_sold_f, d.finance_payment finance_payment, d.stock stock, d.client_name client_name, dm.name deskmanager, d.model model, d.make make, f.name financeperson, d.payable_gross - d.discount payable_gross, (d.payable_gross + d.doc_fee + d.pack - d.discount) frontend, (warranty_gross + gap_gross + finance_gross + reserve) backend, d.approved_date, DATE_FORMAT(d.approved_date,"%m/%d/%Y") approved_date_f, (payable_gross + pack + doc_fee + warranty_gross + gap_gross + finance_gross - discount + reserve) total_gross FROM deals d LEFT JOIN deskmanagers dm on d.deskmanager=dm.id LEFT JOIN financepeople f ON d.finance_person=f.id '.$str_where.' AND d.approved = "1"';

if(isset($sortid) && $sortid != 'approved_avarege') {
    $dealssql .= " ORDER BY ". $sortid . " " . $sortdir;
}else{
    $dealssql .= " ORDER BY date_sold DESC";
}

$companytotal = db_select_assoc($companytotalsql);
$deals = db_select_assoc_array($dealssql);

$days = Array();
$sumDays = 0;
// print_r($deals);exit;
for ($i = 0;$i < count($deals);$i++)
{
    $row = $deals[$i];
    $dateStart = strtotime($row['date_sold']);
    $dateEnd = strtotime($row['approved_date']);
    $dateBetween = $dateEnd - $dateStart;
    $days[$i] = floor($dateBetween / (60 * 60 * 24));
    $sumDays += $days[$i];
    $deals[$i]['approved_avarege'] = $days[$i];
    $deals[$i]['approved_percent'] = $days[$i];
}

$days = $sumDays / count($days);
$days = floor($days);

$projected_modifier = mtd_projected_modifier();
if($mode == "mtd" || !isset($mode)){
    $companytotal['projected'] = array(
        'date_sold_f' =>'Projected',
        'deals'=>number_format($companytotal['deals'] * $projected_modifier, 2),
        'stock'=>number_format($companytotal['deals'] * $projected_modifier, 2),
        'payable_gross'=> number_format($companytotal['payable_gross'] * $projected_modifier, 2),
        'frontend'=> number_format($companytotal['frontend'] * $projected_modifier, 2),
        'backend'=> number_format($companytotal['backend'] * $projected_modifier, 2),
        'total_gross'=> number_format($companytotal['total_gross'] * $projected_modifier, 2),
    );
}
// sort
if ($sortid == "approved_avarege") {
    for ($i=0; $i < count($deals) -1 ; $i++) { 
        for ($j = $i + 1; $j < count($deals); $j++) {
            if($sortdir == 'asc') {
                if($deals[$i][$sortid] < $deals[$j][$sortid]) {
                    $temp = $deals[$i];
                    $deals[$i] = $deals[$j];
                    $deals[$j] = $temp;
                }
            } else {
                if($deals[$i][$sortid] > $deals[$j][$sortid]) {
                    $temp = $deals[$i];
                    $deals[$i] = $deals[$j];
                    $deals[$j] = $temp;
                }
            }
        }
    }
}

$twig->addGlobal('companytotal', $companytotal);
$twig->addGlobal('deals', $deals);
$twig->addGlobal('days', $days);

$twig->addGlobal('page_title', 'Time To Fund');
$twig->addGlobal('session', $_SESSION['FT']);

if(isset($_GET['sortdir']))
{
    $twig->addGlobal('sortdir', $_GET['sortdir']);
    $twig->addGlobal('sortid', $_GET['sortid']);
}
else{
    $twig->addGlobal('sortdir', "desc");
    $twig->addGlobal('sortid', 'date_sold');
}
/*
$controller = new dealcontroller();
$output = $controller->invoke('unfunded');
$twig->addGlobal('deals', $output);
*/

//var_dump($output);
//var_dump($_SESSION['FT']);

/*
 * Render Templates
 */
echo $twig->render('reports/tpl.time-to-fund.twig');