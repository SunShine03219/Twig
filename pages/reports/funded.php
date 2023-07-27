<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();
$twig->addGlobal('page_title', 'Funded');
$twig->addGlobal('session', $_SESSION['FT']);

$newdate = date("j");
$makedate;
$yesterday;
$firstdate;
$lastdate;
$prevmonfirst;
$prevmonlast;
$make;
$leadsource = [];
$year = date("Y");
$month = date('m');
$sortid = $_GET['sortid'];
$sortdir=$_GET['sortdir'];

        $prevmonfirst = $year . "/" . $month-1 . "/" . "01";
        $prevmonlast = $year . "/" . $month . "/" . "31";
        $firstdate = $year . "/" . $month . "/" . "01";
        $lastdate = $year . "/" . $month . "/" . "31";
        if($newdate<10){
            $makedate = $year . "/" . $month . "/" . $newdate;
            $yesterday = $year . "/" . $month . "/" . $newdate-1;
        }
        else{
            $makedate = $year . "/" . $month . "/" . $newdate;
            $yesterday = $year . "/" . $month . "/" . $newdate-1;
        }
    // else{
    //     $prevmonfirst = $year .  "/" . $month-1 . "/" . "01";
    //     $prevmonlast = $year .  "/" . $month . "/" . "31";
    //     $firstdate = $year . "/" . "0" . $month . "/" . "01";
    //     $lastdate = $year . "/" . "0" . $month . "/" . "31";
    //     if($newdate<10){
    //         $makedate = $year .  "/" . $month . "/" . $newdate;
    //         $yesterday = $year .  "/" . $month . "/" . $newdate-1;
    //     }
    //     else{
    //         $makedate = $year . "/" . $month . "/" . $newdate;
    //         $yesterday = $year . "/" . $month . "/" . $newdate-1;
    //     }
    // }

$date1='2013/04/05';
$datestart=$_GET['datestart'];
$dateend=$_GET['dateend'];
$deleted= $_GET['deleted'];
$locked= $_GET['locked'];
$search= $_GET['search'];
$paid= $_GET['paid'];
$company_id=$_SESSION['FT']['company_id'];

if($deleted == null){
    $sql = "SELECT d.id dealid,d.closed_dms closed_dms,d.date_sold date_sold,DATE_FORMAT(d.date_sold,'%m/%d/%Y') date_sold_f,d.client_name client_name,d.stock stock,l.name lender,
    d.deleted deleted,d.funded funded,d.amount_financed financeamount,
    CONCAT(CONVERT(d.`year`, CHAR(10)),' ',d.`make`, ' ',d.`model`) `vehicle`,
    GROUP_CONCAT(`s`.`name` SEPARATOR ' & ') AS `sales_team`,
    CONCAT_WS(' & ',`dm`.`name`,`f`.`name`) AS `deskteam`,
    CONCAT('$',FORMAT(d.amount_financed,2)) amount_financed
    FROM deals AS d
    LEFT JOIN deals_to_sales AS m ON d.id=m.deal_id
    LEFT JOIN salespeople AS s ON m.sales_id=s.id
    LEFT JOIN lenders AS l ON d.lender_id=l.id
    LEFT JOIN deskmanagers AS dm ON d.deskmanager=dm.id LEFT JOIN financepeople AS f ON d.finance_person=f.id WHERE d.company_id=$company_id AND date_sold between '$firstdate' and '$makedate' GROUP BY d.id, m.deal_id";
}
if($deleted == 2){
    // print_r("stop");exit();
    $sql = "SELECT d.id dealid,d.closed_dms closed_dms,d.date_sold date_sold,DATE_FORMAT(d.date_sold,'%m/%d/%Y') date_sold_f,d.client_name client_name,d.stock stock,l.name lender,
            d.deleted deleted,d.funded funded,d.amount_financed financeamount,
            CONCAT(CONVERT(d.`year`, CHAR(10)),' ',d.`make`, ' ',d.`model`) `vehicle`,
            GROUP_CONCAT(`s`.`name` SEPARATOR ' & ') AS `sales_team`,
            CONCAT_WS(' & ',`dm`.`name`,`f`.`name`) AS `deskteam`,
            CONCAT('$',FORMAT(d.amount_financed,2)) amount_financed
            FROM deals AS d
            LEFT JOIN deals_to_sales AS m ON d.id=m.deal_id
            LEFT JOIN salespeople AS s ON m.sales_id=s.id
            LEFT JOIN lenders AS l ON d.lender_id=l.id
            LEFT JOIN deskmanagers AS dm ON d.deskmanager=dm.id LEFT JOIN financepeople AS f ON d.finance_person=f.id WHERE d.company_id=$company_id";
    if($_GET['newcar'] == 'newcar'){
        $sql .=" AND newcar=1";
    }
    if($_GET['locked'] != ''){
        $sql .=" AND locked=".$_GET['locked'];
    }
    if(trim($search) != '') {
        // $sql .= " AND CONCAT(d.client_name) LIKE '%". $search ."%'";
        $sql .= " AND (d.client_name LIKE '%". $search ."%' OR CAST(DATE_FORMAT(d.date_sold,'%m/%d/%Y') as CHAR) LIKE '%". $search ."%' OR CAST(d.stock as CHAR) LIKE '%". $search ."%' OR CONCAT(CONVERT(d.year, CHAR(10)),' ',d.`make`, ' ',d.`model`) LIKE '%". $search ."%' OR s.name LIKE '%". $search ."%' OR CONCAT_WS(' & ',dm.name,f.name) LIKE '%". $search ."%' OR l.name LIKE '%". $search ."%') ";

    }
    if($paid != '' && $paid != 2) {
        $sql .= " AND d.flooring_paid = '". $paid ."'";
    }
    if($_GET['mode'] == 'unlimited'){
        $sql .=" GROUP BY d.id, m.deal_id";
    }
    if($_GET['mode'] == 'mtd'){
        $sql .=" AND date_sold between '$firstdate' and '$makedate'";
        $sql .=" GROUP BY d.id, m.deal_id";
    }
    if($_GET['mode'] == 'today'){
        $sql .=" AND date_sold = '$makedate'";
        $sql .=" GROUP BY d.id, m.deal_id";
    }
    if($_GET['mode'] == 'prevmon'){
        $sql .=" AND d.date_sold >='".date('Y-m-01', strtotime('first day of previous month')). "' AND " . " d.date_sold <= '".date('Y-m-31', strtotime('last day of previous month')). "'" ;
        // $sql .=" AND date_sold between '$prevmonfirst' and '$prevmonlast'";
        $sql .=" GROUP BY d.id, m.deal_id";
    }
    if($_GET['mode'] == 'yesterday'){
        $sql .=" AND date_sold = '$yesterday'";
        $sql .=" GROUP BY d.id, m.deal_id";
    }
    if($_GET['mode'] == 'daterange'){
        $sql .=" AND date_sold between '$datestart' and '$dateend'";
        $sql .=" GROUP BY d.id, m.deal_id";
    }
    if($_GET['mode'] == 'custommonth'){
        $year = $_GET["year"];
        $month = $_GET["month"];
        $sql .= " AND d.date_sold >='".$year."-".$month."-01'" . " AND d.date_sold <='".$year."-".$month."-31' ";
        // $sql .=" AND date_sold between '$firstdate' and '$lastdate'";
        $sql .=" GROUP BY d.id, m.deal_id";
    }
}
if($deleted != null && $deleted != 2){
    $sql = "SELECT d.id dealid,d.closed_dms closed_dms,d.date_sold date_sold,DATE_FORMAT(d.date_sold,'%m/%d/%Y') date_sold_f,d.client_name client_name,d.stock stock,l.name lender,
            d.deleted deleted,d.funded funded,d.amount_financed financeamount,
            CONCAT(CONVERT(d.`year`, CHAR(10)),' ',d.`make`, ' ',d.`model`) `vehicle`,
            GROUP_CONCAT(`s`.`name` SEPARATOR ' & ') AS `sales_team`,
            CONCAT_WS(' & ',`dm`.`name`,`f`.`name`) AS `deskteam`,
            CONCAT('$',FORMAT(d.amount_financed,2)) amount_financed
            FROM deals AS d
            LEFT JOIN deals_to_sales AS m ON d.id=m.deal_id
            LEFT JOIN salespeople AS s ON m.sales_id=s.id
            LEFT JOIN lenders AS l ON d.lender_id=l.id
            LEFT JOIN deskmanagers AS dm ON d.deskmanager=dm.id LEFT JOIN financepeople AS f ON d.finance_person=f.id WHERE d.company_id=$company_id";
    if($_GET['newcar'] == 'newcar'){
        $sql .=" AND newcar=1";
    }
    if($_GET['locked'] != ''){
        $sql .=" AND locked=".$_GET['locked'];
    }
    if($search != '') {
        //$sql .= " AND CONCAT(d.client_name) LIKE '%". $search ."%'";
        $sql .= " AND (d.client_name LIKE '%". $search ."%' OR CAST(DATE_FORMAT(d.date_sold,'%m/%d/%Y') as CHAR) LIKE '%". $search ."%' OR CAST(d.stock as CHAR) LIKE '%". $search ."%' OR CONCAT(CONVERT(d.year, CHAR(10)),' ',d.`make`, ' ',d.`model`) LIKE '%". $search ."%' OR s.name LIKE '%". $search ."%' OR CONCAT_WS(' & ',dm.name,f.name) LIKE '%". $search ."%' OR l.name LIKE '%". $search ."%') ";

    }
    if($paid != '' && $paid != 2) {
        $sql .= " AND d.flooring_paid = '". $paid ."'";
    }
    if($_GET['mode'] == 'unlimited'){
        $sql .=" AND deleted=$deleted";
        $sql .=" GROUP BY d.id, m.deal_id";
    }
    if($_GET['mode'] == 'mtd'){
        $sql .=" AND deleted=$deleted";
        $sql .=" AND date_sold between '$firstdate' and '$makedate'";
        $sql .=" GROUP BY d.id, m.deal_id";
    }
    if($_GET['mode'] == 'today'){
        $sql .=" AND deleted=$deleted";
        $sql .=" AND date_sold = '$makedate'";
        $sql .=" GROUP BY d.id, m.deal_id";
    }
    if($_GET['mode'] == 'prevmon'){
        $sql .=" AND deleted=$deleted";
        $sql .= " AND d.date_sold >='".date('Y-m-01', strtotime('last month')) . "' AND " . " d.date_sold <= '" . date('Y-m-31', strtotime('last month')) . "'";
        $sql .=" GROUP BY d.id, m.deal_id";
    }
    if($_GET['mode'] == 'yesterday'){
        $sql .=" AND deleted=$deleted";
        $sql .=" AND date_sold = '$yesterday'";
        $sql .=" GROUP BY d.id, m.deal_id";
    }
    if($_GET['mode'] == 'daterange'){
        $sql .=" AND deleted=$deleted";
        $sql .=" AND date_sold between '$datestart' and '$dateend'";
        $sql .=" GROUP BY d.id, m.deal_id";
    }
    if($_GET['mode'] == 'custommonth'){
        $sql .=" AND deleted=$deleted";
        // $sql .=" AND date_sold between '$firstdate' and '$lastdate'";
        $year = $_GET["year"];
        $month = $_GET["month"];
        $sql .= " AND d.date_sold >='".$year."-".$month."-01'" . " AND d.date_sold <='".$year."-".$month."-31' ";
        $sql .=" GROUP BY d.id, m.deal_id";
    }
}

if(isset($sortid) && $sortid != "status" && $sortid != 'vehicle') {
    $sql .= " ORDER BY ". $sortid . " " . $sortdir;
} else if ($sortid == 'vehicle') {
    $sql .= " ORDER BY SUBSTR(vehicle,1,4)" . $sortdir;
}else{
    $sql .= " ORDER BY date_sold DESC";
}

// print_r($sql);exit;
$result = db_select_assoc_array($sql);
if($result) {
    $count = 0;
    foreach($result as $row) {
        $result[$count]['status'] = ($row['deleted']?'Deleted':($row['closed_dms']?'Closed':($row['funded']?'Funded':'Unfunded')));
        $count++;
    }
}

// sort result for status column

function cmp($a, $b) {
    if($a['status'] == $b['status']) {
        return 0;
    }
    return ($a['status'] > $b['status']) ? 1 : -1;
}
function cmp_objection($a, $b) {
    if($a['status'] == $b['status']) {
        return 0;
    }
    return ($a['status'] > $b['status']) ? -1 : 1;
}

if($sortid == "status") {
    if($sortdir == "asc") {
        uasort($result, 'cmp');
    } else {
        uasort($result, 'cmp_objection');
    }
}

$twig->addGlobal('fundeds', $result);
$twig->addGlobal('sortid', $sortid);
$twig->addGlobal('sortdir', $sortdir);

//var_dump($output);
//var_dump($_SESSION['FT']);

/*
 * Render Templates
 */
echo $twig->render('reports/tpl.funded.twig');