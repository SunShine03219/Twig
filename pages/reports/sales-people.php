<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$twig->addGlobal('page_title', 'Sales People');
$twig->addGlobal('session', $_SESSION['FT']);


$controller = new salespeoplecontroller();
$mode = $_GET['mode'];

if($mode == "mtd") {
    $controller->setProjected();
}

$output = $controller->invoke('salsepeople');

$salsedata = $output['salessummary'];

$total_deals = array();
$total_backend = array();
$average_backend = array();
$total_backend[] = array("name", "Front End", "Back End");
$average_backend[] = array("name", "Front End", "Back End");
$total_deals = array();

if($mode == "mtd") { // projected
    $total_deals[] = array("name", "Sales", "Projected");
    $twig->addGlobal('isProjected', true);
    $twig->addGlobal('projected', $output['projected']);
} else {
    $total_deals[] = array("name", "Deal");
}

// print_r($salsedata);exit;
foreach ($salsedata as $summary => $value) {
    if ($mode == "mtd") { // is projected
        $total_deals[] = array($value['NAME'], round($value["deals"]), $value["projected"],$value["projected"]);
    } else {
        $total_deals[] = array($value['NAME'], round($value["deals"], 2), round($value["deals"], 2));
    }
    $total_backend[] = array($value["NAME"], round($value["frontend"]), round($value["backend"]), "$".(number_format($value["frontend"] + $value["backend"], 0, '.', ',')));
    $average_backend[] = array($value["NAME"], round($value["frontend_average"]), round($value["backend_average"]), "$".(number_format($value["frontend_average"] + abs($value["backend_average"]), 0, '.', ',')));
}

$twig->addGlobal('total_deals', $total_deals);
$twig->addGlobal('total_backend', $total_backend);
$twig->addGlobal('average_backend', $average_backend);
// print_r($total_deals);exit;
//var_dump($output);
//var_dump($_SESSION['FT']);

for ($i=0; $i < count($output['salessummary']) - 1; $i++) {
    for ($j=$i + 1; $j < count($output['salessummary']); $j++) { 
        if($output['salessummary'][$i]['sales_id'] == $output['salessummary'][$j]['sales_id']) {
            if($output['salessummary'][$i]['total_gross'] < $output['salessummary'][$j]['total_gross']) {
                $output['salessummary'][$i] = $output['salessummary'][$j];
            } else {
                $output['salessummary'][$j] = $output['salessummary'][$i];
            }
        }
    }
}

$twig->addGlobal('sales', $output['salessummary']);
$twig->addGlobal('totalcompanies', $output['totalcompany']);
$twig->addGlobal('deals', $output['deals']);
/*
 * Render Templates
 */
echo $twig->render('reports/tpl.sales-people.twig');