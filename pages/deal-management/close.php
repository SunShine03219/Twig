<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$_SESSION['FT']['current_page'] = 'close';
$twig->addGlobal('session', $_SESSION['FT']);

if(isset($_GET['dropdown'])) {
    $_SESSION['FT']['topbar_filters'] = $_POST;
} else if(isset($_GET['table-list'])) {
    $controller = new dealcontroller();

    echo $controller->getClosedTableList($_GET, $_SESSION['FT']['company_id']);
} else {
    $twig->addGlobal('page_title', 'Closed Deals');
    $controller = new dealcontroller();
    //$output = $controller->invoke('close');

    $company_id = $_SESSION['FT']['company_id'];
    $str_where = "company_id = $company_id AND funded = 1 AND closed_dms = 1 ";
    $whereclause = $controller->deals_filter($_SESSION['FT']['topbar_filters'], $company_id, $str_where);

    $totalsql = 'SELECT
                    count(*) deals,
                    CONCAT("$",FORMAT(sum(amount_financed),2)) financed_total,
                    CONCAT("$",FORMAT(sum(amount_financed)/count(*),2)) financed_average
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
        // $averageFinanced = 0;

        // foreach ($output as $deal) {
        //     // Financed Amount Total
        //     $finVal = str_replace(',', '', $deal['amount_financed']);
        //     $finVal = str_replace('$', '', $finVal);
        //     $totalFinanced += $finVal;
        // }
        // $averageFinanced = $totalFinanced / $dealCount;

        // $twig->addGlobal('deals', $output['data']);
        $twig->addGlobal('deal_count', $output['info']['deals']);
        $twig->addGlobal('deal_financed_total', $output['info']['financed_total']);
        $twig->addGlobal('deal_financed_average', $output['info']['financed_average']);
    }

    //var_dump($output);
    //var_dump($_SESSION['FT']);

    /*
     * Render Templates
     */
    $_SESSION["current_page"] = "close";
    $_SESSION['FT']['query_string'] = $_SERVER['QUERY_STRING'];
    $delete_permission = ($_SESSION['FT']['SEC_DELETE']) ? 'yes' : 'no';
    $twig->addGlobal('delete_permission', $delete_permission);
    $twig->addGlobal('deals_dropdown', $_SESSION['FT']['deals_dropdown']);

    echo $twig->render('deal-management/tpl.close.twig');
}