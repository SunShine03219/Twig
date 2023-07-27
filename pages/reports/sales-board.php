<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$twig->addGlobal('page_title', 'Sales Board');
$twig->addGlobal('session', $_SESSION['FT']);

$company_id=$_SESSION['FT']['company_id'];
$mode = $_GET['mode'];
$month = $_GET['month'];
$year = $_GET['year'];
$datestart = $_GET['datestart'];
$dateend = $_GET['dateend'];
$lender_id = $_GET['lender_id'];
$deleted = $_GET['deleted'];
$newcar = $_GET['newcar'];
$locked = $_GET['locked'];
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
    $is_second_where = true;
}

$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';

if(isset($locked) && $locked != '2' && $locked != '') {
    $str_where .= " ".$applyAnd." d.locked =".$locked;
    $is_second_where = true;
}


$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
if(isset($deleted) && $deleted != 2) {
    $str_where .= " ".$applyAnd." d.deleted = '". $deleted ."' AND d.company_id = '".$company_id."'";
}elseif($deleted == 2) {
    $str_where .= " ".$applyAnd." d.company_id = '".$company_id."'";
} else {
    $str_where .= " ".$applyAnd." d.deleted = '0' AND d.company_id = '".$company_id."'";
}

// if(isset($_GET['year'])){
//     $date = date_create_from_format('Y-m-d', $_GET['year'].'-'.$_GET['month'].'-'.'01');
//     $now = $date->format('Y-m-d');
// }else{
//     $now = date('Y-m-1');
// }
// $lockStr = '';
// if($_GET['locked'] != ''){
//     $lockStr = ' AND locked='.$lockStr;
// }

$sql = "SELECT m.sales_id sales_id, s.name name, 'Total' date_sold_f, 
sum(d.payable_gross * dc.share) - sum(d.discount * dc.share) payable_gross, 
(sum(d.payable_gross)-sum(d.discount)) / count(d.id) payable_gross_average, 
sum(d.doc_fee * dc.share) doc_fee, sum(d.pack * dc.share) pack, 
sum(d.discount * dc.share) discount, sum(d.payable_gross * dc.share) + 
sum(d.doc_fee * dc.share) + sum(d.pack * dc.share) - sum(d.discount * dc.share) frontend, 
(sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount )) / count(d.id) frontend_average, 
sum(d.warranty_gross * dc.share) warranty_gross, 
sum(case when warranty_id<>0 then 1 else 0 end) warranty_count, 
sum(d.warranty_gross )/sum(case when warranty_gross>0 then 1 else 0 end) warranty_average, 
sum(CASE WHEN warranty_id <> 0 THEN 1 ELSE 0 END)/count(*) warranty_perc, sum(d.gap_gross * dc.share) gap_gross, 
sum(case when gap_id<>0 then 1 else 0 end) gap_count, 
sum(d.gap_gross)/sum(case when gap_gross>0 then 1 else 0 end) gap_average, 
sum(CASE WHEN gap_id <> 0 THEN 1 ELSE 0 END)/sum(financed_deal) gap_perc, 
sum(d.finance_gross * dc.share) miscfinance_gross, 
sum(case when finance_gross>0 then 1 else 0 end) miscfinance_count, 
sum(d.finance_gross )/sum(case when finance_gross>0 then 1 else 0 end) miscfinance_average, 
sum(CASE WHEN finance_gross > 0 THEN 1 ELSE 0 END)/count(*) miscfinance_perc, 
sum(warranty_gross* dc.share) + sum(gap_gross* dc.share) + sum(finance_gross* dc.share) + sum(reserve* dc.share) backend, 
(sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve)) / count(d.id) backend_average, 
sum(payable_gross* dc.share) + sum(pack* dc.share) + sum(doc_fee* dc.share) + sum(warranty_gross* dc.share) 
+ sum(gap_gross* dc.share) + sum(finance_gross* dc.share) - sum(discount* dc.share) + sum(reserve* dc.share) total_gross, 
count(d.id) total_gross_average, sum(dc.share) deals 
FROM deals d 
INNER JOIN deals_to_sales m ON d.id=m.deal_id 
LEFT JOIN salespeople s ON m.sales_id=s.id 
INNER JOIN ( SELECT deal_id, 1/count(*) share 
FROM deals_to_sales GROUP BY deal_id ) dc on d.id=dc.deal_id 
".$str_where." and s.active = 1 
GROUP BY sales_id UNION SELECT m.sales_id sales_id, s.name name, 
'Total' date_sold_f, sum(d.payable_gross * dc.share) - sum(d.discount * dc.share) payable_gross, 
(sum(d.payable_gross)-sum(d.discount)) / count(d.id) payable_gross_average, sum(d.doc_fee * dc.share) doc_fee, 
sum(d.pack * dc.share) pack, 
sum(d.discount * dc.share) discount, 
sum(d.payable_gross * dc.share) + sum(d.doc_fee * dc.share) + sum(d.pack * dc.share) - sum(d.discount * dc.share) frontend, 
(sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount )) / count(d.id) frontend_average, 
sum(d.warranty_gross * dc.share) warranty_gross, 
sum(case when warranty_id<>0 then 1 else 0 end) warranty_count, 
sum(d.warranty_gross )/sum(case when warranty_gross>0 then 1 else 0 end) warranty_average, 
sum(CASE WHEN warranty_id <> 0 THEN 1 ELSE 0 END)/count(*) warranty_perc, 
sum(d.gap_gross * dc.share) gap_gross, sum(case when gap_id<>0 then 1 else 0 end) gap_count, 
sum(d.gap_gross)/sum(case when gap_gross>0 then 1 else 0 end) gap_average, 
sum(CASE WHEN gap_id <> 0 THEN 1 ELSE 0 END)/sum(financed_deal) gap_perc, 
sum(d.finance_gross * dc.share) miscfinance_gross, 
sum(case when finance_gross>0 then 1 else 0 end) miscfinance_count, 
sum(d.finance_gross )/sum(case when finance_gross>0 then 1 else 0 end) miscfinance_average, 
sum(CASE WHEN finance_gross > 0 THEN 1 ELSE 0 END)/count(*) miscfinance_perc, 
sum(warranty_gross* dc.share) + sum(gap_gross* dc.share) + sum(finance_gross* dc.share) + 
sum(reserve* dc.share) backend, 
(sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve)) / count(d.id) backend_average, 
sum(payable_gross* dc.share) + sum(pack* dc.share) + sum(doc_fee* dc.share) + sum(warranty_gross* dc.share) 
+ sum(gap_gross* dc.share) + sum(finance_gross* dc.share) - sum(discount* dc.share) + sum(reserve* dc.share) total_gross, 
count(d.id) total_gross_average, sum(dc.share) deals 
FROM deals d 
JOIN (select 0 sales_id) m 
JOIN (select 'Unassigned' name) s 
JOIN (select 1 share) dc 
".$str_where." 
AND d.id NOT IN (select deal_id id from deals_to_sales) 
GROUP BY sales_id";

// WHERE d.company_id = '$company_id' and s.active = 1 
// AND MONTH(d.date_sold) = MONTH('$now') 
// AND YEAR(d.date_sold) = YEAR('$now') 
// WHERE d.company_id='$company_id'".$lockStr." 
// AND MONTH(d.date_sold) = MONTH('$now') 
// AND YEAR(d.date_sold) = YEAR('$now') 
// AND d.id NOT IN (select deal_id id from deals_to_sales) 


$salessql2 = 'SELECT m.sales_id sales_id, s.name name, "Total" date_sold_f, 
sum(d.payable_gross * dc.share) - sum(d.discount * dc.share) payable_gross, 
(sum(d.payable_gross)-sum(d.discount)) / count(d.id) payable_gross_average, 
sum(d.doc_fee * dc.share) doc_fee, sum(d.pack * dc.share) pack, 
sum(d.discount * dc.share) discount, 
sum(d.payable_gross * dc.share) + sum(d.doc_fee * dc.share) + sum(d.pack * dc.share) - sum(d.discount * dc.share) frontend, 
(sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount )) / count(d.id) frontend_average, 
sum(d.warranty_gross * dc.share) warranty_gross, 
sum(case when warranty_id<>0 then 1 else 0 end) warranty_count, 
sum(d.warranty_gross )/sum(case when warranty_gross>0 then 1 else 0 end) warranty_average, 
sum(CASE WHEN warranty_id <> 0 THEN 1 ELSE 0 END)/count(*) warranty_perc, 
sum(d.gap_gross * dc.share) gap_gross, 
sum(case when gap_id<>0 then 1 else 0 end) gap_count, 
sum(d.gap_gross)/sum(case when gap_gross>0 then 1 else 0 end) gap_average, 
sum(CASE WHEN gap_id <> 0 THEN 1 ELSE 0 END)/sum(financed_deal) gap_perc, 
sum(d.finance_gross * dc.share) miscfinance_gross, 
sum(case when finance_gross>0 then 1 else 0 end) miscfinance_count, 
sum(d.finance_gross )/sum(case when finance_gross>0 then 1 else 0 end) miscfinance_average, 
sum(CASE WHEN finance_gross > 0 THEN 1 ELSE 0 END)/count(*) miscfinance_perc, 
sum(warranty_gross* dc.share) + sum(gap_gross* dc.share) + sum(finance_gross* dc.share) + sum(reserve* dc.share) backend, 
(sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve)) / count(d.id) backend_average, 
sum(payable_gross* dc.share) + sum(pack* dc.share) + sum(doc_fee* dc.share) + sum(warranty_gross* dc.share) 
+ sum(gap_gross* dc.share) + sum(finance_gross* dc.share) - sum(discount* dc.share) + sum(reserve* dc.share) total_gross, count(d.id) total_gross_average, 
sum(dc.share) deals 
FROM deals d 
INNER JOIN deals_to_sales m ON d.id=m.deal_id 
LEFT JOIN salespeople s ON m.sales_id=s.id 
INNER JOIN ( SELECT deal_id, 1/count(*) share FROM deals_to_sales GROUP BY deal_id ) dc on d.id=dc.deal_id 
'.$str_where.'
GROUP BY sales_id';

// WHERE d.company_id = "' . $company_id . '" 
// && MONTH(d.date_sold) = MONTH(DATE_SUB("' . $now . '", INTERVAL 1 MONTH)) 
// && YEAR(d.date_sold) = YEAR(DATE_SUB("' . $now . '", INTERVAL 1 MONTH)) '.$lockStr.' 

$unclaimedsummarysql2 = 'SELECT m.sales_id sales_id, s.name name, "Total" date_sold_f, 
sum(d.payable_gross * dc.share) - sum(d.discount * dc.share) payable_gross, 
(sum(d.payable_gross)-sum(d.discount)) / count(d.id) payable_gross_average, 
sum(d.doc_fee * dc.share) doc_fee, sum(d.pack * dc.share) pack, 
sum(d.discount * dc.share) discount, sum(d.payable_gross * dc.share) + sum(d.doc_fee * dc.share) 
+ sum(d.pack * dc.share) - sum(d.discount * dc.share) frontend, (sum(d.payable_gross ) + sum(d.doc_fee ) 
+ sum(d.pack ) - sum(d.discount )) / count(d.id) frontend_average, 
sum(d.warranty_gross * dc.share) warranty_gross, 
sum(case when warranty_id<>0 then 1 else 0 end) warranty_count, 
sum(d.warranty_gross )/sum(case when warranty_gross>0 then 1 else 0 end) warranty_average, 
sum(CASE WHEN warranty_id <> 0 THEN 1 ELSE 0 END)/count(*) warranty_perc, 
sum(d.gap_gross * dc.share) gap_gross, 
sum(case when gap_id<>0 then 1 else 0 end) gap_count, 
sum(d.gap_gross)/sum(case when gap_gross>0 then 1 else 0 end) gap_average, 
sum(CASE WHEN gap_id <> 0 THEN 1 ELSE 0 END)/sum(financed_deal) gap_perc, 
sum(d.finance_gross * dc.share) miscfinance_gross, 
sum(case when finance_gross>0 then 1 else 0 end) miscfinance_count, 
sum(d.finance_gross )/sum(case when finance_gross>0 then 1 else 0 end) miscfinance_average, 
sum(CASE WHEN finance_gross > 0 THEN 1 ELSE 0 END)/count(*) miscfinance_perc, 
sum(warranty_gross* dc.share) + sum(gap_gross* dc.share) + sum(finance_gross* dc.share) + sum(reserve* dc.share) backend, 
(sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve)) / count(d.id) backend_average, 
sum(payable_gross* dc.share) + sum(pack* dc.share) + sum(doc_fee* dc.share) + sum(warranty_gross* dc.share) 
+ sum(gap_gross* dc.share) + sum(finance_gross* dc.share) - sum(discount* dc.share) + sum(reserve* dc.share) total_gross, 
count(d.id) total_gross_average, 
sum(dc.share) deals 
FROM deals d 
JOIN (select 0 sales_id) m 
JOIN (select "Unassigned" name) s 
JOIN (select 1 share) dc 
'.$str_where.'
AND d.id NOT IN (select deal_id id from deals_to_sales)
GROUP BY sales_id';
// WHERE d.company_id = "'. $company_id .'" AND 
// MONTH(d.date_sold) = MONTH(DATE_SUB("' . $now . '", INTERVAL 1 MONTH)) 
// AND YEAR(d.date_sold) = YEAR(DATE_SUB("' . $now . '", INTERVAL 1 MONTH)) 

$salessummary = db_select_assoc_array($sql);


$salessummary2 = db_select_assoc_array($salessql2 . ' UNION ' . $unclaimedsummarysql2);

for($i = 0;$i < count($salessummary); $i++){
    $row = $salessummary[$i];
    $salessummary[$i]['lastmonth'] = 0;
    $project = two_decimal(($row['deals']/date('d') * date('t')));
    $salessummary[$i]['projectcao'] = $project;
}

// print_r($salessummary);exit;
$totalMesPassado = 0;
for($i = 0;$i < count($salessummary); $i++){
    $row = $salessummary[$i];

    for($x = 0;$x < count($salessummary2); $x++) {
        $r = $salessummary2[$x];
        
        if($r['name'] == $row['name']) {
            $salessummary[$i]['lastmonth'] = two_decimal($r['deals']);
            $totalMesPassado += two_decimal($r['deals']);
            break;
        }
    }
}

$twig->addGlobal('salesboards', $salessummary);
$twig->addGlobal('totalMesPassado', $totalMesPassado);


function two_decimal($val){
	if(is_numeric($val)){
		return round($val, 2);
	} else {
		return $val;
	}
}
/*
 * Render Templates
 */
echo $twig->render('reports/tpl.sales-board.twig');