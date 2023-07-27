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
$newcar = $_GET['newcar'];
$sortid = $_GET['sortid'];
$sortdir = $_GET['sortdir'];
$deleted = $_GET['deleted'];
$locked = $_GET['locked'];
$search = $_GET['search'];
$paid = $_GET['paid'];

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
// if(isset($newcar)) {
//     if($is_second_where) {
//         $str_where .= " AND d.newcar = 1 ";
//     } else {
//         $str_where = " WHERE d.newcar = 1 ";
//     }
//     $is_second_where = true;
// }

if($locked != '' && $locked != '2') {
    $str_where .= " ".$applyAnd." d.locked =".$locked;
    // if($is_second_where) {
    //     $str_where .= " AND d.locked =".$locked;
    // } else {
    //     $str_where = " WHERE d.locked =".$locked;;
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
// if(isset($search) && $search !== '') {
//     $str_where .= " ".$applyAnd." CONCAT(d.client_name) LIKE '%". $search ."%'";
//     $is_second_where = true;
// }
if(trim($search) != '') {
    //$str_where .= " ".$applyAnd." CONCAT(d.client_name) LIKE '%". $search ."%'";
    $str_where .= " ".$applyAnd." (d.client_name LIKE '%". $search ."%' OR CAST(DATE_FORMAT(d.date_sold,'%m/%d/%Y') as CHAR) LIKE '%". $search ."%' OR CAST(d.stock as CHAR) LIKE '%". $search ."%' OR dm.name LIKE '%". $search ."%' OR f.name LIKE '%". $search ."%') ";
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

$sql = 'SELECT d.id deal_id, d.date_sold date_sold, DATE_FORMAT(d.date_sold,"%m/%d/%Y") date_sold_f, d.stock stock, d.client_name client_name, dm.name deskmanager, f.name financeperson, d.payable_gross - d.discount payable_gross, (d.payable_gross + d.doc_fee + d.pack - d.discount) frontend, (warranty_gross + gap_gross + finance_gross + reserve) backend, d.holdback, (payable_gross + pack + doc_fee + warranty_gross + gap_gross + finance_gross - discount + reserve) total_gross FROM deals d LEFT JOIN deskmanagers dm on d.deskmanager=dm.id LEFT JOIN financepeople f ON d.finance_person=f.id ' . $str_where . ' AND d.newcar = 1 ORDER BY date_sold DESC';

$company_total = 'SELECT d.id deal_id, d.date_sold date_sold, DATE_FORMAT(d.date_sold,"%m/%d/%Y") date_sold_f, d.stock stock, d.client_name client_name, dm.name deskmanager, f.name financeperson, "Total" date_sold_f, count(d.id) deals, "Total" name, sum(d.payable_gross ) - sum(d.discount) payable_gross, (sum(d.payable_gross)-sum(d.discount)) / count(d.id) payable_gross_average, sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount ) frontend, (sum(d.payable_gross ) + sum(d.doc_fee ) + sum(d.pack ) - sum(d.discount )) / count(d.id) frontend_average, sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve) backend, (sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) + sum(reserve)) / count(d.id) backend_average, sum(payable_gross) + sum(pack) + sum(doc_fee) + sum(warranty_gross) + sum(gap_gross) + sum(finance_gross) - sum(discount) + sum(reserve) total_gross, count(d.id) total_gross_average, sum(financed_deal) financed_deals, sum(dealer_check) financed_total, sum(case when financed_deal=1 AND funded=1 THEN dealer_check ELSE 0 END) funded_total, sum(case when financed_deal=1 AND funded=1 THEN 1 ELSE 0 END) funded_count, sum(case when financed_deal=1 AND funded=0 THEN dealer_check ELSE 0 END) unfunded_total, sum(case when financed_deal=1 AND funded=0 THEN 1 ELSE 0 END) unfunded_count FROM deals d LEFT JOIN deskmanagers dm on d.deskmanager=dm.id LEFT JOIN financepeople f ON d.finance_person=f.id '. $str_where .' AND d.newcar = 1';

//  sort datas
if(isset($sortid) && $sortid != 'status' && $sortid != 'frontend' && $sortid != 'backend' && $sortid != 'total_gross') {
    $sql .= " ORDER BY ". $sortid . " " . $sortdir;
}
//print_r($sql);exit;
$result = db_select_assoc_array($sql);
$companytotal = db_select_assoc($company_total);
// print_r($result);exit;

if($result) {
    foreach($result as $count => $row){
        $result[$count]['status'] = ($row['deleted']?'Deleted':($row['closed_dms']?'Closed':($row['funded']?'Funded':'Unfunded')));
    }

    // sort datas 
    if($sortid == 'status' || $sortid == 'frontend' || $sortid == 'backend' || $sortid == 'total_gross') {
        for ($i=0; $i < count($result) -1 ; $i++) { 
            for ($j = $i + 1; $j < count($result); $j++) {
                if($sortdir == 'asc') {
                    if($result[$i][$sortid] < $result[$j][$sortid]) {
                        $temp = $result[$i];
                        $result[$i] = $result[$j];
                        $result[$j] = $temp;
                    }
                } else {
                    if($result[$i][$sortid] > $result[$j][$sortid]) {
                        $temp = $result[$i];
                        $result[$i] = $result[$j];
                        $result[$j] = $temp;
                    }
                }
            }
        }
    }
    foreach($result as $count => $row){
        $result[$count]['payable_gross'] = cashmoney($row['payable_gross']);
        $result[$count]['frontend'] = cashmoney($result[$count]['frontend']);
        $result[$count]['backend'] = cashmoney($result[$count]['backend']);
        $result[$count]['holdback'] = cashmoney($row['holdback']);
        $result[$count]['total_gross'] = cashmoney($result[$count]['total_gross']);
    }
}

$projected_modifier = date('t') / date('d');

$companyprojected;
if($_GET['mode'] == 'mtd') {
    $companyprojected = array(
        'date_sold_f' =>'Projected',
        'deals'=>two_decimal($companytotal['deals'] * $projected_modifier),
        'stock'=>two_decimal($companytotal['deals'] * $projected_modifier),
        'payable_gross'=> cashmoney($companytotal['payable_gross'] * $projected_modifier),
        'frontend'=> cashmoney($companytotal['frontend'] * $projected_modifier),
        'backend'=> cashmoney($companytotal['backend'] * $projected_modifier),
        'total_gross'=> cashmoney($companytotal['total_gross'] * $projected_modifier),
    );
}

if($companytotal) {
    if(!empty($companytotal['total_gross_average'])) {
        $companytotal['total_gross_average'] = cashmoney($companytotal['total_gross'] / $companytotal['total_gross_average']);
    }
    $companytotal['deals'] = two_decimal($companytotal['deals']);
    $companytotal['stock'] = $companytotal['deals'];
    $companytotal['payable_gross'] = cashmoney($companytotal['payable_gross']);
    $companytotal['payable_gross_average'] = cashmoney($companytotal['payable_gross_average']);
    $companytotal['frontend'] = cashmoney($companytotal['frontend']);
    $companytotal['frontend_average'] = cashmoney($companytotal['frontend_average']);
    $companytotal['backend'] = cashmoney($companytotal['backend']);
    $companytotal['backend_average'] = cashmoney($companytotal['backend_average']);
    $companytotal['total_gross'] = cashmoney($companytotal['total_gross']);
    $companytotal['financed_total'] = cashmoney($companytotal['financed_total']);
    $companytotal['funded_total'] = cashmoney($companytotal['funded_total']);
    $companytotal['unfunded_total'] = cashmoney($companytotal['unfunded_total']);
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

$twig->addGlobal('page_title', 'Gross New Cars');
$twig->addGlobal('session', $_SESSION['FT']);

$twig->addGlobal('projects', $companyprojected);
$twig->addGlobal('companytotal', $companytotal);
$twig->addGlobal('vehicles', $result);
$twig->addGlobal('sortdir', $_GET['sortdir']);
$twig->addGlobal('sortid', $_GET['sortid']);

echo $twig->render('reports/tpl.gross-new.twig');