<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$twig->addGlobal('page_title', 'Search Deals');
$twig->addGlobal('session', $_SESSION['FT']);

$controller = new dealcontroller();
$output = $controller->invoke('search');

if(isset($_GET['type'])){
    if($_GET['type'] == "pagination" || $_GET['type'] == "sort"){
        echo json_encode($output['data']);
        exit();
    }
}

$twig->addGlobal('search', $_GET['search']);

if ($output['data'] != false) {
    // $dealCount = count($output);
    // $totalFinanced = 0;

    // foreach ($output as $deal) {
    //     // Financed Amount Total
    //     $finVal = str_replace(',', '', $deal['amount_financed']);
    //     $finVal = str_replace('$', '', $finVal);
    //     $totalFinanced += $finVal;
    // }

    $twig->addGlobal('deals', $output['data']);
    $twig->addGlobal('deal_count', $output['count']);
    $twig->addGlobal('deal_financed_total', $output['info']['amount_financed']);
} else {
    $twig->addGlobal('no_results', 'No Results Found');
}

//var_dump($output);
//var_dump($_SESSION['FT']);

/*
 * Render Templates
 */
echo $twig->render('deal-management/tpl.search.twig');