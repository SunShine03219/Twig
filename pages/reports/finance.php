<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();
function mtd_projected_modifier(){
    //returns the modifier that needs to be entered against totals to get projected values
    $day_of_month = date('j');
    $days_in_month = date('t');
    return (float)((float)$days_in_month)/(float)$day_of_month;
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
$paid = $_GET['paid'];

$str_where = "";
$is_second_where = true;
switch( $mode ) {
    case "mtd": 
        $str_where = " WHERE d.date_sold >='".date('Y-m-01') . "' AND d.date_sold <='".date('Y-m-31')."' ";
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
if(isset($newcar)) {
    if($is_second_where) {
        $str_where .= " AND d.newcar = 1 ";
    } else {
        $str_where = " WHERE d.newcar = 1 ";
    }
    $is_second_where = true;
}

if($locked != '') {
    if($is_second_where) {
        $str_where .= " AND d.locked =".$locked;
    } else {
        $str_where = " WHERE d.locked =".$locked;;
    }
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

$companytotalsql = 'SELECT count(d.id) deals, "Total" name, sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve) backend, (sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve)) / count(d.id) backend_average, sum(financed_deal) financed_deals, sum(case when financed_deal=1 AND funded=1 THEN 1 ELSE 0 END) funded_count, sum(case when financed_deal=1 AND funded=0 THEN 1 ELSE 0 END) unfunded_count, sum(case when warranty_id<>0 then 1 else 0 end) warranty_count, sum(d.warranty_gross ) warranty_gross, sum(CASE WHEN warranty_id <> 0 THEN 1 ELSE 0 END)/count(*) warranty_perc, sum(d.warranty_gross )/sum(case when warranty_gross>0 then 1 else 0 end) warranty_average, sum(case when gap_id<>0 then 1 else 0 end) gap_count, sum(d.gap_gross ) gap_gross, sum(CASE WHEN gap_id <> 0 THEN 1 ELSE 0 END)/sum(financed_deal) gap_perc, sum(d.gap_gross )/sum(case when gap_gross>0 then 1 else 0 end) gap_average, sum(case when finance_gross>0 then 1 else 0 end) miscfinance_count, sum(d.finance_gross ) miscfinance_gross, sum(CASE WHEN finance_gross > 0 THEN 1 ELSE 0 END)/count(*) miscfinance_perc, sum(d.finance_gross )/sum(case when finance_gross>0 then 1 else 0 end) miscfinance_average, sum(reserve ) reserve, sum(dealer_check) financed_total, sum(case when financed_deal=1 AND funded=0 THEN 1 ELSE 0 END) unfunded_count, sum(case when financed_deal=1 AND funded=0 THEN dealer_check ELSE 0 END) unfunded_total, sum(case when financed_deal=1 AND funded=1 THEN 1 ELSE 0 END) funded_count, sum(case when financed_deal=1 AND funded=1 THEN dealer_check ELSE 0 END) funded_total FROM deals d'. $str_where;

$summarysql = 'SELECT COALESCE(f.name, "Unclaimed") name, COALESCE(f.id,0) id, "Total" date_sold_f, count(d.id) deals, sum(financed_deal) financed_deals, sum(case when financed_deal=1 AND funded=1 THEN 1 ELSE 0 END) funded_count, sum(case when financed_deal=1 AND funded=1 THEN dealer_check ELSE 0 END) funded_total, sum(case when financed_deal=1 AND funded=0 THEN 1 ELSE 0 END) unfunded_count, sum(case when financed_deal=1 AND funded=0 THEN dealer_check ELSE 0 END) unfunded_total, sum(case when warranty_id<>0 then 1 else 0 end) warranty_count, sum(d.warranty_gross ) warranty_gross, sum(CASE WHEN warranty_id <> 0 THEN 1 ELSE 0 END)/count(*) warranty_perc, sum(d.warranty_gross )/sum(case when warranty_gross>0 then 1 else 0 end) warranty_average, sum(case when gap_id<>0 then 1 else 0 end) gap_count, sum(d.gap_gross ) gap_gross, sum(CASE WHEN gap_id <> 0 THEN 1 ELSE 0 END)/sum(financed_deal) gap_perc, sum(d.gap_gross )/sum(case when gap_gross>0 then 1 else 0 end) gap_average, sum(case when finance_gross>0 then 1 else 0 end) miscfinance_count, sum(d.finance_gross ) miscfinance_gross, sum(CASE WHEN finance_gross > 0 THEN 1 ELSE 0 END)/count(*) miscfinance_perc, sum(d.finance_gross )/sum(case when finance_gross>0 then 1 else 0 end) miscfinance_average, sum(reserve ) reserve, sum(reserve)/sum(CASE WHEN reserve > 0 THEN 1 ELSE 0 END) reserve_average, (sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve)) / count(d.id) backend_average, d.approved, sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve) backend FROM deals d LEFT JOIN financepeople f ON d.finance_person=f.id '.$str_where.' GROUP BY finance_person';

$dealssql = 'SELECT COALESCE(d.finance_person,0) finance_id, d.id deal_id, d.stock stock, d.client_name client_name, l.name lender, w.name warranty_provider, d.warranty_gross warranty_gross, g.name gap_provider, d.gap_gross gap_gross, d.finance_gross miscfinance_gross, d.reserve reserve, (warranty_gross + gap_gross + finance_gross + reserve) backend FROM deals d LEFT JOIN lenders l ON d.lender_id=l.id LEFT JOIN warrantyproviders w on d.warranty_id = w.id LEFT JOIN gapproviders g on d.gap_id = g.id '.$str_where.' ORDER BY finance_person ASC, date_sold ASC';
// echo $companytotalsql.'<br>';
// echo $summarysql.'<br>';
// echo $dealssql.'<br>';exit;
$companytotal = db_select_assoc($companytotalsql);
$salessummary = db_select_assoc_array($summarysql);
$deals = db_select_assoc_array($dealssql);

// is projected
$projected_modifier = mtd_projected_modifier();
if ($mode == "mtd") {
    # code...
    $companytotal['projected'] = array(
        'name'=>'Projected',
        'deals'=>number_format($companytotal['deals']*$projected_modifier, 2),
        'warranty_gross'=>number_format($companytotal['warranty_gross']*$projected_modifier, 2),
        'gap_gross'=>number_format($companytotal['gap_gross']*$projected_modifier, 2),
        'miscfinance_gross'=>number_format($companytotal['miscfinance_gross']*$projected_modifier, 2),
        'backend'=>number_format($companytotal['backend']*$projected_modifier, 2),
        'reserve'=>number_format($companytotal['reserve']*$projected_modifier, 2)
    );
    // print_r($companytotal["projected"]);exit;
}

for($i = 0; $i<count($deals); $i++){
    $row = $deals[$i];

    if($deals[$i]['lender'] === "Cash"){
        for($j = 0; $j < count($salessummary); $j++){
            if($deals[$i]['finance_id'] == $salessummary[$j]['id']){
                $salessummary[$j]['total_cash'] += 1;
                $companytotal['total_cash'] += 1;
                break;
            }
        }
    }
}

for($i = 0;$i < count($salessummary); $i++){
    $row = $salessummary[$i];

    $reserve = 0;
    if(($row['deals'] - $row['total_cash']) > 0){
        $reserve = round($row['reserve'] / ($row['deals'] - $row['total_cash']), 2);
    }
    $salessummary[$i]["av_reserve"] = $reserve;

    if($mode == "mtd") { // is projected
        $salessummary[$i]["projected"] = array(
            'stock'=>number_format($row['deals']*$projected_modifier, 2),
            'lender'=>'Projected',
            'warranty_provider'=>'&nbsp;',
            'warranty_gross'=>number_format($row['warranty_gross']*$projected_modifier, 2),
            'gap_provider'=>'&nbsp;',
            'gap_gross'=>number_format($row['gap_gross']*$projected_modifier, 2),
            'miscfinance_gross'=>number_format($row['miscfinance_gross']*$projected_modifier, 2),
            'reserve'=>number_format($row['reserve']*$projected_modifier, 2),
            'backend'=>number_format($row['backend']*$projected_modifier, 2)
        );
    }
}

// total average
$average_reserve = 0;
if(($companytotal['deals'] - $companytotal['total_cash']) > 0){
    $average_reserve = round($companytotal['reserve'] / ($companytotal['deals'] - $companytotal['total_cash']), 2);
}
$companytotal["av_reserve"] = $average_reserve;

$companytotal['backend_average'] = round($companytotal['backend_average'], 2);
$companytotal['warranty_average'] = round($companytotal['warranty_average'], 2);
$companytotal['gap_average'] = round($companytotal['gap_average'], 2);
$companytotal['miscfinance_average'] = round($companytotal['miscfinance_average'], 2);

$total_deals = array();
$total_backend = array();
$average_backend = array();
$total_backend[] = array("name", "Warrenty", "Gap", "Misc Fin", "Reserve");
$average_backend[] = array("name", "Warrenty", "Gap", "Misc Fin", "Reserve");
$total_deals[] = array("name", "deals");

foreach ($salessummary as $summary => $value) {
    $total_deals[] = array($value['name'], round($value["deals"]), round($value["deals"]));
    $total_backend[] = array($value["name"], $value['warranty_gross'], $value["gap_gross"], $value["miscfinance_gross"], $value["reserve"], "$".number_format($value["backend"], 0));
    $comment = $value["warranty_average"] + $value["gap_average"] + $value["miscfinance_average"] + $value["reserve_average"];
    $comment = round($comment, 2);
    $average_backend[] = array($value["name"], $value["warranty_average"], $value["gap_average"], $value["miscfinance_average"], $value["reserve_average"], "$".number_format($comment, -0));
}

$twig->addGlobal('companytotal', $companytotal);
$twig->addGlobal('salessummary', $salessummary);
$twig->addGlobal('deals', $deals);
$twig->addGlobal('total_deals', $total_deals);
$twig->addGlobal('total_backend', $total_backend);
$twig->addGlobal('average_backend', $average_backend);

$twig->addGlobal('page_title', 'Finance');
$twig->addGlobal('session', $_SESSION['FT']);
/*
 * Render Templates
 */
echo $twig->render('reports/tpl.finance.twig');