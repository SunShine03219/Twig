<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$_SESSION['FT']['current_page'] = 'approved';
$twig->addGlobal('session', $_SESSION['FT']);

if(isset($_GET['dropdown'])) {
    $_SESSION['FT']['topbar_filters'] = $_POST;
} else if(isset($_GET['table-list'])) {
    $controller = new dealcontroller();

    echo $controller->getApprovedTableList($_GET, $_SESSION['FT']['company_id']);
} else {
    $twig->addGlobal('page_title', 'Approved Deals');

    $_GET['approved'] = 1;

    $controller = new dealcontroller();
    //$output = $controller->invoke('approved');

    $company_id = $_SESSION['FT']['company_id'];
    $str_where = "company_id = $company_id AND approved = 1 ";
    $whereclause = $controller->deals_filter($_SESSION['FT']['topbar_filters'], $company_id, $str_where);

    $totalsql = 'SELECT
                count(*) deals,
                CONCAT("$",FORMAT(sum(amount_financed),2)) financed_total,
                CONCAT("$",FORMAT(sum(discount),2)) discount_total,
                CONCAT("$",FORMAT(sum(amount_financed)/count(*),2)) financed_average,
                CONCAT("$",FORMAT(sum(discount)/count(*),2)) discount_average
                 FROM deals WHERE 
                ' . $whereclause . '
                ';
    
    $totalresult = db_select_assoc($totalsql);
    $output['info'] = $totalresult;

    // if(isset($_GET['type'])){
    //     if($_GET['type'] == "pagination" || $_GET['type'] == "sort"){
    //         echo json_encode($output['data']);
    //         exit();
    //     }
    // }

    if ($output['info'] != false) {
        // $dealCount = count($output);
        // $totalFinanced = 0;
        // $totalDiscount = 0;
        // $averageFinanced = 0;
        // $averageDiscount = 0;

        // foreach ($output as $deal) {
        //     // Financed Amount Total
        //     $finVal = str_replace(',', '', $deal['amount_financed']);
        //     $finVal = str_replace('$', '', $finVal);
        //     $totalFinanced += $finVal;

        //     // Discount Amount Total
        //     $disVal = str_replace(',', '', $deal['discount']);
        //     $disVal = str_replace('$', '', $disVal);
        //     $totalDiscount += $disVal;
        // }
        // $averageFinanced = $totalFinanced / $dealCount;
        // $averageDiscount = $totalDiscount / $dealCount;

        // $twig->addGlobal('deals', $output['data']);
        $twig->addGlobal('deal_count', $output['info']['deals']);
        $twig->addGlobal('deal_financed_total', $output['info']['financed_total']);
        $twig->addGlobal('deal_discount_total', $output['info']['discount_total']);
        $twig->addGlobal('deal_financed_average', $output['info']['financed_average']);
        $twig->addGlobal('deal_discount_average', $output['info']['discount_average']);
    }

//var_dump($output);
//var_dump($_SESSION['FT']);

/*
 * Render Templates
 */

$_SESSION["current_page"] = "approved";
$_SESSION['FT']['query_string'] = $_SERVER['QUERY_STRING'];
$delete_permission = ($_SESSION['FT']['SEC_DELETE']) ? 'yes' : 'no';
$twig->addGlobal('delete_permission', $delete_permission);
$twig->addGlobal('deals_dropdown', $_SESSION['FT']['deals_dropdown']);

echo $twig->render('deal-management/tpl.approved.twig');
}
