<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$twig->addGlobal('page_title', 'Finance Penetration');
$twig->addGlobal('session', $_SESSION['FT']);


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
$paid = $_GET['paid'];
$search = $_GET['search'];

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
    //     $str_where = " WHERE d.newcar = 0 ";
    // }
    $is_second_where = true;
}

$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
if($locked != '' && $locked != 2) {
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

// if($is_second_where) {
//     $str_where .= " AND d.company_id = '".$company_id."'";
// } else {
//     $str_where .= " WHERE d.company_id = '".$company_id."'";
// }

$companytotalsql = "SELECT COUNT(d.id) deals, 'Total' NAME, SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve) backend, 
                    (SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve)) / COUNT(d.id) backend_average, SUM(financed_deal) financed_deals, 
                    SUM(CASE WHEN financed_deal=1 AND funded=1 THEN 1 ELSE 0 END) funded_count, SUM(CASE WHEN financed_deal=1 AND funded=0 THEN 1 ELSE 0 END) unfunded_count, 
                    SUM(CASE WHEN warranty_id<>0 THEN 1 ELSE 0 END) warranty_count, SUM(d.warranty_gross ) warranty_gross, SUM(CASE WHEN warranty_id <> 0 THEN 1 ELSE 0 END)/COUNT(*) warranty_perc, 
                    SUM(d.warranty_gross )/SUM(CASE WHEN warranty_gross>0 THEN 1 ELSE 0 END) warranty_average, SUM(CASE WHEN gap_id<>0 THEN 1 ELSE 0 END) gap_count, SUM(d.gap_gross ) gap_gross, 
                    SUM(CASE WHEN gap_id <> 0 THEN 1 ELSE 0 END)/SUM(financed_deal) gap_perc, SUM(d.gap_gross )/SUM(CASE WHEN gap_gross>0 THEN 1 ELSE 0 END) gap_average, 
                    SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_count, SUM(d.finance_gross ) miscfinance_gross, SUM(CASE WHEN finance_gross > 0 THEN 1 ELSE 0 END)/COUNT(*) miscfinance_perc, 
                    SUM(d.finance_gross )/SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_average, SUM(reserve ) reserve, SUM(dealer_check) financed_total, 
                    SUM(CASE WHEN financed_deal=1 AND funded=0 THEN 1 ELSE 0 END) unfunded_count, SUM(CASE WHEN financed_deal=1 AND funded=0 THEN dealer_check ELSE 0 END) unfunded_total, 
                    SUM(CASE WHEN financed_deal=1 AND funded=1 THEN 1 ELSE 0 END) funded_count, SUM(CASE WHEN financed_deal=1 AND funded=1 THEN dealer_check ELSE 0 END) funded_total 
                    FROM deals d ".$str_where;

$summarysql = "SELECT COALESCE(f.name, 'Unclaimed') NAME, COALESCE(f.id,0) id, 'Total' date_sold_f, COUNT(d.id) deals, SUM(financed_deal) financed_deals, d.finance_person finance_id, 
                SUM(CASE WHEN financed_deal=1 AND funded=1 THEN 1 ELSE 0 END) funded_count, SUM(CASE WHEN financed_deal=1 AND funded=1 THEN dealer_check ELSE 0 END) funded_total, 
                SUM(CASE WHEN financed_deal=1 AND funded=0 THEN 1 ELSE 0 END) unfunded_count, SUM(CASE WHEN financed_deal=1 AND funded=0 THEN dealer_check ELSE 0 END) unfunded_total, 
                SUM(CASE WHEN warranty_id<>0 THEN 1 ELSE 0 END) warranty_count, SUM(d.warranty_gross ) warranty_gross, SUM(CASE WHEN warranty_id <> 0 THEN 1 ELSE 0 END)/COUNT(*) warranty_perc, 
                SUM(d.warranty_gross )/SUM(CASE WHEN warranty_gross>0 THEN 1 ELSE 0 END) warranty_average, SUM(CASE WHEN gap_id<>0 THEN 1 ELSE 0 END) gap_count, SUM(d.gap_gross ) gap_gross, 
                SUM(CASE WHEN gap_id <> 0 THEN 1 ELSE 0 END)/SUM(financed_deal) gap_perc, SUM(d.gap_gross )/SUM(CASE WHEN gap_gross>0 THEN 1 ELSE 0 END) gap_average, 
                SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_count, SUM(d.finance_gross ) miscfinance_gross, SUM(CASE WHEN finance_gross > 0 THEN 1 ELSE 0 END)/COUNT(*) miscfinance_perc, 
                SUM(d.finance_gross )/SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_average, SUM(reserve ) reserve, SUM(reserve)/SUM(CASE WHEN reserve > 0 THEN 1 ELSE 0 END) reserve_average, 
                (SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve)) / COUNT(d.id) backend_average, d.approved, SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve) backend 
                FROM deals d 
                LEFT JOIN financepeople f ON d.finance_person=f.id 
                ".$str_where." GROUP BY finance_person";

$dealssql = "SELECT COALESCE(d.finance_person,0) finance_id, d.id deal_id, d.stock stock, d.client_name client_name, l.name lender, w.name warranty_provider, d.warranty_gross warranty_gross, g.name gap_provider, 
            d.gap_gross gap_gross, d.finance_gross miscfinance_gross, d.reserve reserve, d.approved, (warranty_gross + gap_gross + finance_gross + reserve) backend 
            FROM deals d 
            LEFT JOIN lenders l ON d.lender_id=l.id 
            LEFT JOIN warrantyproviders w ON d.warranty_id = w.id 
            LEFT JOIN gapproviders g ON d.gap_id = g.id 
            ".$str_where." ORDER BY finance_person ASC, date_sold ASC";

$companytotal = db_select_assoc($companytotalsql);
$salessummary = db_select_assoc_array($summarysql);
$deals = db_select_assoc_array($dealssql);

function mtd_projected_modifier(){
    //returns the modifier that needs to be entered against totals to get projected values
    $day_of_month = date('j');
    $days_in_month = date('t');
    return (float)((float)$days_in_month)/(float)$day_of_month;
}
// is projected
$projected_modifier = mtd_projected_modifier();
if($mode == "mtd") {
    $companytotal['projected'] =array(
        'name'=>'Projected',
        'deals'=>two_decimal($companytotal['deals']*$projected_modifier),
        'warranty_gross'=>cashmoney($companytotal['warranty_gross']*$projected_modifier),
        'gap_gross'=>cashmoney($companytotal['gap_gross']*$projected_modifier),
        'miscfinance_gross'=>cashmoney($companytotal['miscfinance_gross']*$projected_modifier),
        'backend'=>cashmoney($companytotal['backend']*$projected_modifier),
        'reserve'=>cashmoney($companytotal['reserve']*$projected_modifier)
    );
}

$outTotalDeals = db_select_assoc_array('SELECT finance_person, financepeople.name, count(deals.id) as total_deals '
                        . 'FROM deals inner join financepeople on financepeople.id=deals.finance_person '
                        . 'WHERE deals.company_id = ' . $_SESSION['FT']['company_id']
                        . ' group by finance_person,financepeople.name');
$totalDeals = Array();
for($v=0;$v<count($outTotalDeals); $v++){
    $outTotalDeals[$v]['total_approved'] = 0;
    $totalDeals[$outTotalDeals[$v]['finance_person']]=$outTotalDeals[$v];
}

$totalApproved = 0;

for($i = 0; $i<count($deals); $i++){
    $row = $deals[$i];
    if($row['approved']==1){
        $totalDeals[$row['finance_id']]['total_approved'] += 1;
        $totalApproved++;
    }
}

for($i = 0;$i < count($salessummary); $i++){
    $row = $salessummary[$i];
    $salessummary[$i]['name'] = '&nbsp;';
    $salessummary[$i]['lender'] = 'Total';
    $salessummary[$i]['deals'] = two_decimal($row['deals']);
    $salessummary[$i]['client_name'] = two_decimal($row['deals']);
    $salessummary[$i]['backend'] = cashmoney($row['backend']);
    $salessummary[$i]['warranty_perc'] = $row['warranty_count'] . ' ' . build_pretty_percent($row['warranty_perc']);
    $salessummary[$i]['warranty_provider'] = $salessummary[$i]['warranty_perc'];
    $salessummary[$i]['warranty_gross'] = cashmoney($row['warranty_gross']);
    $salessummary[$i]['gap_perc'] = $row['gap_count'] . ' ' . build_pretty_percent($row['gap_perc']);
    $salessummary[$i]['gap_provider'] = $salessummary[$i]['gap_perc'];
    $salessummary[$i]['gap_gross'] = cashmoney($row['gap_gross']);
    $salessummary[$i]['miscfinance_perc'] = $row['miscfinance_count'] . ' ' . build_pretty_percent($row['miscfinance_perc']);
    $salessummary[$i]['miscfinance_provider'] =$salessummary[$i]['miscfinance_perc'];
    $salessummary[$i]['miscfinance_gross'] = cashmoney($row['miscfinance_gross']);
    $salessummary[$i]['reserve'] = cashmoney($row['reserve'] / $row['deals']);
    $salessummary[$i]['approved_percent'] = build_pretty_percent(0);

    $row = $salessummary[$i];
    if(!empty($row['finance_id']) && !empty($totalDeals[$row['finance_id']]['total_approved']))
                $salessummary[$i]['approved_percent'] = floor(($totalDeals[$row['finance_id']]['total_approved'] / $row['deals']) * 100) . '%';

    // projected
    if($mode == "mtd") {
        $salessummary[$i]['projected'] = array(
            'stock'=>two_decimal($row['deals']*$projected_modifier),
            'warranty_gross'=>cashmoney($row['warranty_gross']*$projected_modifier),
            'gap_gross'=>cashmoney($row['gap_gross']*$projected_modifier),
            'miscfinance_gross'=>cashmoney($row['miscfinance_gross']*$projected_modifier),
            'reserve'=>cashmoney($row['reserve']*$projected_modifier),
            'backend'=>cashmoney($row['backend']*$projected_modifier)
        );
    }
}


$companytotal['warranty_perc'] = $companytotal['warranty_count'] . ' ' . build_pretty_percent($companytotal['warranty_perc']);
$companytotal['warranty_gross'] = cashmoney($companytotal['warranty_gross']);
$companytotal['gap_perc'] = $companytotal['gap_count'] . ' ' . build_pretty_percent($companytotal['gap_perc']);
$companytotal['gap_gross'] = cashmoney($companytotal['gap_gross']);
$companytotal['miscfinance_perc'] = $companytotal['miscfinance_count'] . ' ' . build_pretty_percent($companytotal['miscfinance_perc']);
$companytotal['miscfinance_gross'] = cashmoney($companytotal['miscfinance_gross']);
$companytotal['reserve'] = cashmoney($companytotal['reserve']);
$companytotal['backend'] = cashmoney($companytotal['backend']);
$companytotal['unfunded_total'] = cashmoney($companytotal['unfunded_total']);

// print_r($salessummary);exit();


function build_pretty_percent($val){
	if(is_numeric($val)){
        return number_format($val*100, 1) . '%';
        
	} else {
		return $val;
	}
}

function cashmoney($number){
    if(is_numeric($number)){
        if($number){
            if($number>=0){
                return '$' . number_format($number, 2, '.', ',');
            } else {
                return '($' . number_format(abs($number), 2, '.', ',') . ')';
            }
        } else {
            return '$' . number_format(0.00, 2);
        }
    } else {
        return $number;
    }
}
function two_decimal($val){
    if(is_numeric($val)){
        return round($val, 2);
    } else {
        return $val;
    }
}

// print_r($salessummary);exit();
$total = array();
$total[] = ['NAME', 'deals'];
$total1 = array();
$total1[] = ['NAME', 'Warranty', 'Gap', 'Misc Fin', 'Reserve'];
$total2 = array();
$total2[] = ['NAME', 'Warranty', 'Gap', 'Misc Fin', 'Reserve'];
// print_r($salessummary );exit;

foreach($salessummary as $key => $sum) {
    $total[] = array($sum['NAME'], $sum['deals'], $sum['deals']);
    $temp1 = array($sum['NAME'], $sum['warranty_gross'], $sum['gap_gross'], $sum['miscfinance_gross'], $sum['reserve'], $sum['backend']);
    
    foreach ($temp1 as $key => $value) {
        if($key != count($temp1) -1) {
            $temp1[$key] = str_replace(',', '', trim($value, "$"));
        }
        if($key == count($temp1) -1) {
            $temp1[$key] = substr($temp1[$key], strpos($temp1[$key], '$'), strpos($temp1[$key], '.'));
        }
    }
    $total1[] =$temp1;
    $temp2 = array(
        $sum['NAME'], 
        $sum['warranty_average'], 
        $sum['gap_average'], 
        $sum['miscfinance_average'], 
        $sum['reserve_average'], 
            '$'.number_format(
                floatval($sum['warranty_average']) + 
                floatval($sum['gap_average'])+  
                floatval($sum['miscfinance_average']) +
                floatval($sum['reserve_average']), 0, ".", ","
            )
        );
    $total2[] = $temp2;
}

$twig->addGlobal('companyTotals', $companytotal);
$twig->addGlobal('salessummaries', $salessummary);
$twig->addGlobal('totals', $total);
$twig->addGlobal('totals1', $total1);
$twig->addGlobal('totals2', $total2);
$twig->addGlobal('deals', $deals);

// print_r ($salessummary);exit;    

echo $twig->render('reports/tpl.finance-penetration.twig');