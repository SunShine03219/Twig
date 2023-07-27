<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$_SESSION['FT']['current_page'] = 'floored';
$twig->addGlobal('session', $_SESSION['FT']);

if(isset($_GET['dropdown'])) {
    $_SESSION['FT']['topbar_filters'] = $_POST;
} else if(isset($_GET['table-list'])) {
    $controller = new dealcontroller();

    echo $controller->getFlooredTableList($_GET, $_SESSION['FT']['company_id']);
} else {
    $twig->addGlobal('page_title', 'Floored Deals');
    $controller = new dealcontroller();
    $output = $controller->invoke('floored');

    // if(isset($_GET['type'])){
    //     if($_GET['type'] == "pagination" || $_GET['type'] == "sort"){
    //         echo json_encode($output);
    //         exit();
    //     }
    // }

    if ($output != false) {

        //$twig->addGlobal('deals', $output);
        $twig->addGlobal('deal_count', $dealCount);
        $twig->addGlobal('deal_financed_total', $totalFinanced);
        $twig->addGlobal('deal_financed_average', $averageFinanced);
    }

    $_SESSION["current_page"] = "floored";
    $_SESSION['FT']['query_string'] = $_SERVER['QUERY_STRING'];
    $delete_permission = ($_SESSION['FT']['SEC_DELETE']) ? 'yes' : 'no';
    $twig->addGlobal('delete_permission', $delete_permission);
    $twig->addGlobal('deals_dropdown', $_SESSION['FT']['deals_dropdown']);

    echo $twig->render('deal-management/tpl.floored.twig');
}
