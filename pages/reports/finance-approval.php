<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$twig->addGlobal('page_title', 'Finance Approval');
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
if($locked != '' && $locked != '2') {
    $str_where .= " ".$applyAnd." d.locked =".$locked;
    // if($is_second_where) {
    //     $str_where .= " AND d.locked =".$locked;
    // } else {
    //     $str_where = " WHERE d.locked =".$locked;;
    // }
    $is_second_where = true;
}

// if(isset($lender_id) && $mode != "unlimited") {
//     if($is_second_where) {
//         $str_where .= " AND d.lender_id = '".$lender_id."'";
//     } else {
//         $str_where = " WHERE d.lender_id = '".$lender_id."'";
//     }
//     $is_second_where = true;
// }
// $applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
// $str_where .= " ".$applyAnd." d.company_id = '".$company_id."'";


$applyAnd = ($is_second_where) ? 'AND' : 'WHERE';
if(isset($search) && trim($search) != '') {
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
//     $str_where .= " ".$applyAnd." d.company_id = '".$company_id."'";
// } else {
//     $str_where .= " ".$applyAnd." d.company_id = '".$company_id."'";
// }

// echo $str_where;exit;
$companytotalsql = "SELECT 
            COUNT(d.id) deals, 'Total' NAME, 
            SUM(d.payable_gross ) - SUM(d.discount) payable_gross, 
            (SUM(d.payable_gross)-SUM(d.discount)) / COUNT(d.id) payable_gross_average, 
            SUM(d.pack ) pack, 
            SUM(d.doc_fee ) doc_fee, SUM(d.discount ) discount, 
            SUM(d.payable_gross ) + SUM(d.doc_fee ) + SUM(d.pack ) - SUM(d.discount ) frontend, 
            (SUM(d.payable_gross ) + SUM(d.doc_fee ) + SUM(d.pack ) - SUM(d.discount )) / COUNT(d.id) frontend_average, 
            SUM(d.warranty_gross ) warranty_gross, 
            SUM(CASE WHEN warranty_id<>0 THEN 1 ELSE 0 END) warranty_count, 
            SUM(d.warranty_gross )/SUM(CASE WHEN warranty_gross>0 THEN 1 ELSE 0 END) warranty_average, 
            SUM(d.gap_gross ) gap_gross, 
            SUM(CASE WHEN gap_id<>0 THEN 1 ELSE 0 END) gap_count, 
            SUM(d.gap_gross )/SUM(CASE WHEN gap_gross>0 THEN 1 ELSE 0 END) gap_average, 
            SUM(d.finance_gross ) miscfinance_gross, 
            SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_count, 
            SUM(d.finance_gross )/SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_average, 
            SUM(reserve ) reserve, 
            SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve) backend, 
            (SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve)) / COUNT(d.id) backend_average, 
            SUM(payable_gross) + SUM(pack) + SUM(doc_fee) + SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) - SUM(discount) + SUM(reserve) total_gross, 
            COUNT(d.id) total_gross_average 
            FROM deals d ".$str_where;

$salessql = "SELECT 
             d.id dealid, m.sales_id sales_id, s.name NAME, 'Total' date_sold_f, 
            SUM(d.payable_gross * dc.share) - SUM(d.discount * dc.share) payable_gross, 
            (SUM(d.payable_gross)-SUM(d.discount)) / COUNT(d.id) payable_gross_average, 
            SUM(d.doc_fee * dc.share) doc_fee, 
            SUM(d.pack * dc.share) pack, 
            SUM(d.discount * dc.share) discount, 
            SUM(d.payable_gross * dc.share) + SUM(d.doc_fee * dc.share) + SUM(d.pack * dc.share) - SUM(d.discount * dc.share) frontend, 
            (SUM(d.payable_gross ) + SUM(d.doc_fee ) + SUM(d.pack ) - SUM(d.discount )) / COUNT(d.id) frontend_average, 
            SUM(d.warranty_gross * dc.share) warranty_gross, 
            SUM(CASE WHEN warranty_id<>0 THEN 1 ELSE 0 END) warranty_count, 
            SUM(d.warranty_gross )/SUM(CASE WHEN warranty_gross>0 THEN 1 ELSE 0 END) warranty_average, 
            SUM(CASE WHEN warranty_id <> 0 THEN 1 ELSE 0 END)/COUNT(*) warranty_perc, SUM(d.gap_gross * dc.share) gap_gross, 
            SUM(CASE WHEN gap_id<>0 THEN 1 ELSE 0 END) gap_count, 
            SUM(d.gap_gross)/SUM(CASE WHEN gap_gross>0 THEN 1 ELSE 0 END) gap_average, SUM(CASE WHEN gap_id <> 0 THEN 1 ELSE 0 END)/SUM(financed_deal) gap_perc, 
            SUM(d.finance_gross * dc.share) miscfinance_gross, 
            SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_count, 
            SUM(d.finance_gross )/SUM(CASE WHEN finance_gross>0 THEN 1 ELSE 0 END) miscfinance_average, 
            SUM(CASE WHEN finance_gross > 0 THEN 1 ELSE 0 END)/COUNT(*) miscfinance_perc, 
            SUM(warranty_gross* dc.share) + SUM(gap_gross* dc.share) + SUM(finance_gross* dc.share) + SUM(reserve* dc.share) backend, 
            (SUM(warranty_gross) + SUM(gap_gross) + SUM(finance_gross) + SUM(reserve)) / COUNT(d.id) backend_average, 
            SUM(payable_gross* dc.share) + SUM(pack* dc.share) + SUM(doc_fee* dc.share) + SUM(warranty_gross* dc.share) + SUM(gap_gross* dc.share) + SUM(finance_gross* dc.share) - SUM(discount* dc.share) + SUM(reserve* dc.share) total_gross, 
            COUNT(d.id) total_gross_average, 
            SUM(dc.share) deals 
            FROM deals d 
            INNER JOIN deals_to_sales m ON d.id=m.deal_id 
            LEFT JOIN salespeople s ON m.sales_id=s.id 
            INNER JOIN ( SELECT deal_id, 1/COUNT(*) SHARE FROM deals_to_sales GROUP BY deal_id ) dc ON d.id=dc.deal_id 
            ".$str_where." GROUP BY sales_id UNION 
SELECT
    d.id dealid,
   m.sales_id sales_id,
   s.name name,
   'Total' date_sold_f,
   sum(d.payable_gross * dc.share) - sum(d.discount * dc.share) payable_gross,
   (
      sum(d.payable_gross) - sum(d.discount)
   )
   / count(d.id) payable_gross_average,
   sum(d.doc_fee * dc.share) doc_fee,
   sum(d.pack * dc.share) pack,
   sum(d.discount * dc.share) discount,
   sum(d.payable_gross * dc.share) + sum(d.doc_fee * dc.share) + sum(d.pack * dc.share) - sum(d.discount * dc.share) frontend,
   (
      sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount )
   )
   / count(d.id) frontend_average,
   sum(d.warranty_gross * dc.share) warranty_gross,
   sum( case when warranty_id <> 0 then 1 else 0  end
) warranty_count, sum(d.warranty_gross ) / sum(
   case
      when warranty_gross > 0  then 1  else 0  end
) warranty_average, sum( CASE WHEN warranty_id <> 0  THEN 1 ELSE   0 
   END
) / count(*) warranty_perc, sum(d.gap_gross * dc.share) gap_gross, sum(
   case
      when gap_id <> 0  then 1  else 0  end
) gap_count, sum(d.gap_gross) / sum(
   case when gap_gross > 0  then 1  else 0  end
) gap_average, sum( CASE WHEN gap_id <> 0  THEN 1  ELSE 0  END
) / sum(financed_deal) gap_perc, sum(d.finance_gross * dc.share) miscfinance_gross, sum(
   case when finance_gross > 0 
      then 1 else 0  end ) miscfinance_count, sum(d.finance_gross ) / sum( case when finance_gross > 0  then 1  else 0  end
) miscfinance_average, sum( CASE WHEN finance_gross > 0 THEN 1  ELSE 0 
   END
) / count(*) miscfinance_perc, sum(warranty_gross* dc.share) + sum(gap_gross* dc.share) + sum(finance_gross* dc.share) + sum(reserve* dc.share) backend, 
   (
      sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve)
   )
   / count(d.id) backend_average, sum(payable_gross* dc.share) + sum(pack* dc.share) + sum(doc_fee* dc.share) + sum(warranty_gross* dc.share) + sum(gap_gross* dc.share) + sum(finance_gross* dc.share) - sum(discount* dc.share) + sum(reserve* dc.share) total_gross, count(d.id) total_gross_average, sum(dc.share) deals 
FROM deals d  JOIN ( select 0 sales_id ) m  JOIN ( select 'Unassigned' name ) s  JOIN ( select 1 share ) dc  $str_where  AND d.id NOT IN  ( select deal_id id  from deals_to_sales ) GROUP BY sales_id ";

$dealssql = "SELECT d.id deal_id, d.stock stock, COALESCE(m.sales_id,0) sales_id, dc.salescount salesperson_count, d.date_sold date_sold, DATE_FORMAT(d.date_sold,'%m/%d/%Y') date_sold_f, 
            d.payable_gross - d.discount payable_gross, d.client_name client_name, d.pack pack, d.doc_fee doc_fee, d.discount discount, (d.payable_gross + d.doc_fee + d.pack - d.discount) frontend, 
            d.warranty_gross warranty_gross, d.gap_gross gap_gross, d.finance_gross miscfinance_gross, d.reserve reserve, (warranty_gross + gap_gross + finance_gross + reserve) backend, d.approved, 
            (payable_gross + pack + doc_fee + warranty_gross + gap_gross + finance_gross - discount + reserve) total_gross 
            FROM deals d 
            LEFT JOIN deals_to_sales m ON d.id=m.deal_id 
            LEFT JOIN ( SELECT deal_id, 1/COUNT(*) SHARE, COUNT(*) salescount FROM deals_to_sales GROUP BY deal_id ) dc ON d.id=dc.deal_id 
            ".$str_where." ORDER BY d.date_sold ASC";

$companytotal = db_select_assoc($companytotalsql);
$sales = db_select_assoc_array($salessql);
$deals = db_select_assoc_array($dealssql);

function mtd_projected_modifier(){
    //returns the modifier that needs to be entered against totals to get projected values
    $day_of_month = date('j');
    $days_in_month = date('t');
    return (float)((float)$days_in_month)/(float)$day_of_month;
}

$projected_modifier = mtd_projected_modifier();
// is projected
if($mode == "mtd")  {
    $companytotal['projected'] = array(
        'name'=>'Projected',
        'projected'=>number_format($companytotal['deals'] * $projected_modifier, 2),
        'payable_gross' => number_format($companytotal['payable_gross'] * $projected_modifier, 2),
        'frontend' => number_format($companytotal['frontend'] * $projected_modifier, 2),
        'backend' => number_format($companytotal['backend'] * $projected_modifier, 2),
        'total_gross' => number_format($companytotal['total_gross'] * $projected_modifier,2)
    );
    foreach ($sales as $key => $row) {
        # code...
        $sales[$key]['projected'] = array(
            'date_sold_f' => 'Projected',
            'deals' => number_format($row['deals'] * $projected_modifier, 2),
            'stock' => number_format($row['deals'] * $projected_modifier, 2), //this is odd, but used for displaying inside the deal table
            'payable_gross'=>number_format($row['payable_gross'] * $projected_modifier, 2),
            'frontend'=>number_format($row['frontend'] * $projected_modifier, 2),
            'backend'=>number_format($row['backend'] * $projected_modifier, 2),
            'total_gross'=>number_format($row['total_gross'] * $projected_modifier, 2),
        );
    }
}

$total = array();
if ($mode == "mtd") {
    # code...
    $total[] = ['NAME', 'sales', 'projected'];
} else {
    $total[] = ['NAME', 'sales'];
}
$total1 = array();
$total1[] = ['NAME', 'Front End', 'Back End'];
$total2 = array();
$total2[] = ['NAME', 'Front End', 'Back End'];

// print_r($sales);exit;

foreach($sales as $sum) {
    // is projected
    if ($mode == "mtd") {
        # code...
        $total[] = array($sum['NAME'], $sum['deals'], $sum['projected']['deals'], $sum['projected']['deals']);
    } else {
        $total[] = array($sum['NAME'], $sum['deals'], $sum['deals']);
    }
    $total1[] = array($sum['NAME'], abs($sum['frontend']), abs($sum['backend']), "$".number_format(abs($sum['backend'] + $sum['frontend']), 0, '.', ','));
    $total2[] = array(
        $sum['NAME'], 
        round(abs($sum['frontend_average']), 2), 
        round(abs($sum['backend_average']), 2), 
        '$'.number_format(abs($sum['backend_average'] + $sum['frontend_average']), 0, '.', ','));
}

$twig->addGlobal('companytotal', $companytotal);
$twig->addGlobal('sales', $sales);
$twig->addGlobal('totals', $total);
$twig->addGlobal('totals1', $total1);
$twig->addGlobal('totals2', $total2);
$twig->addGlobal('deals', $deals);

// print_r($deals);exit;

echo $twig->render('reports/tpl.finance-approval.twig');