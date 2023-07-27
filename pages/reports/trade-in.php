<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$session = $_SESSION["FT"];
$locked = $_GET['locked'];
$lockStr = '';
if($locked != ''){
    $lockStr = ' AND locked='.$locked ;
}


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
$str_where = "";
$is_second_where = true;
if(!isset($mode)) {
    $mode = "unlimited";
}

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
if(isset($newcar) && $newcar='newcar') {
    $str_where .= " ".$applyAnd." d.newcar = 1 ";
    $is_second_where = true;
}

$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
if($locked != '' && $locked != '2') {
    $str_where .= " ".$applyAnd." d.locked =".$locked;
    $is_second_where = true;
}

// $applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
// if(isset($lender_id) && $lender_id != 0) {
//     $str_where .= " ".$applyAnd." d.lender_id = '".$lender_id."'";
//     $is_second_where = true;
// }


$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
if(trim($search) != '') {
    // $str_where .= " ".$applyAnd." CONCAT(d.client_name,d.make,d.model) LIKE '%". $search ."%'";
    $str_where .= " ".$applyAnd." (d.client_name LIKE '%". $search ."%' OR CAST(DATE_FORMAT(d.date_sold,'%m/%d/%Y') as CHAR) LIKE '%". $search ."%' OR CAST(d.stock as CHAR) LIKE '%". $search ."%' OR d.make LIKE '%". $search ."%' OR d.model LIKE '%". $search ."%' OR d.year LIKE '%". $search ."%' OR d.finance_acv LIKE '%". $search ."%' OR d.finance_miles LIKE '%". $search ."%') ";
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
// echo $str_where;exit;
$sql = 'SELECT d.id dealid, d.closed_dms closed_dms, d.date_sold date_sold, d.deleted deleted, d.funded funded, DATE_FORMAT(d.date_sold,"%m/%d/%Y") date_sold_f, d.client_name client_name, d.stock stock, l.name lender, d.year year, d.make make, d.model model, d.finance_acv finance_acv, d.finance_miles finance_miles, d.finance_payoff finance_payoff, concat_ws(" ",`d`.`year`,`d`.`make`,`d`.`model`) `vehicle`, GROUP_CONCAT(`s`.`name` SEPARATOR "
") AS `sales_team`, concat_ws("
",`dm`.`name`,`f`.`name`) AS `deskteam`, CONCAT("$",FORMAT(d.amount_financed,2)) amount_financed FROM deals d LEFT JOIN deals_to_sales m ON d.id=m.deal_id LEFT JOIN salespeople s ON m.sales_id=s.id LEFT JOIN lenders l ON d.lender_id=l.id LEFT JOIN deskmanagers dm ON d.deskmanager = dm.id LEFT JOIN financepeople f ON d.finance_person = f.id '.$str_where.' AND funded = "1" GROUP BY d.id, m.deal_id ORDER BY date_sold ASC';

$totalsql = 'SELECT count(*) deals, d.date_sold date_sold, d.deleted deleted, d.funded funded, DATE_FORMAT(d.date_sold,"%m/%d/%Y") date_sold_f, d.client_name client_name, d.stock stock, l.name lender, d.year year, d.make make, d.model model, d.finance_acv finance_acv, d.finance_miles finance_miles, d.finance_payoff finance_payoff, CONCAT("$",FORMAT(sum(amount_financed),2)) financed_total, CONCAT("$",FORMAT(sum(amount_financed)/count(*),2)) financed_average FROM deals d '.$str_where.' AND funded = "1"';

// echo $sql;exit;
$result = db_select_assoc_array($sql);
$totalresult = db_select_assoc($totalsql);

if($result){
    $count = 0;
    $sprint_edit = "<a href=\"deals.php?id=%d\">%s</a>";
    foreach($result as $row){
        $result[$count]['finance_payoff'] = ($row['finance_payoff']?'Yes':'No');
        $result[$count]['funded'] = ($row['funded']?'Funded':'Unfunded');
        $count++;
    }
}
// print_r($totalresult);exit;

$twig->addGlobal('deals', $result);
$twig->addGlobal('totalresult', $totalresult);

$twig->addGlobal('page_title', 'Trade In');
$twig->addGlobal('session', $_SESSION['FT']);

echo $twig->render('reports/tpl.trade-in.twig');