<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$twig->addGlobal('page_title', 'Desk Manager');
$twig->addGlobal('session', $_SESSION['FT']);


$controller = new deskmanagementcontroller();
$output = $controller->invoke('deskmanager');
$twig->addGlobal('deskmanagers', $output);
// print_r($output['summarydata']);exit;
$salsedata = $output['summarydata'];

$total_sale = array();
$total_backend = array();
$average_backend = array();
$total_backend[] = array("name", "Warranty", "Gap", "Misc Fin", "Reserve");
$average_backend[] = array("name", "Warranty", "Gap", "Misc Fin", "Reserve");
$total_gross = array();
$total_gross[] = array("name", "Front End", "Back End");
$average_gross = array();
$average_gross[] = array("name", "Front End", "Back End");

if($_GET['mode'] == "mtd") {
    $total_sale[] = array("name", "Sale", "Projected");
} else {
    $total_sale[] = array("name", "Sale");
}
// print_r($salsedata);exit;
foreach ($salsedata as $summary => $value) {

    if($_GET['mode'] == "mtd") {
        $total_sale[] = array($value['name'], round($value["deals"]), $value["projected"]['stock'], number_format($value["projected"]['stock'], 0, ".", ","));
    } else {
        $total_sale[] = array($value['name'], round($value["deals"]), round($value["deals"]));
    }
    $total_gross[] = array($value["name"], round($value["frontend"]), round($value["backend"]), "$".(number_format($value["frontend"] + $value["backend"], 0, ".", ',')));
    $average_gross[] = array($value["name"], round($value["frontend_average"]), round($value["backend_average"]), "$".(number_format($value["frontend_average"] + $value["backend_average"], 0, '.', ',')));
    $total_backend[] = array($value['name'], round($value["warranty_gross"]), round($value["gap_gross"]), round($value["miscfinance_gross"]), round($value["reserve"]), "$".(number_format($value["reserve"] + $value["miscfinance_gross"] + $value["gap_gross"] + $value["warranty_gross"], 0, '.', ',')));
    $average_backend[] = array($value['name'], round($value["warranty_average"]), round($value["gap_average"]), round($value["miscfinance_average"]), round($value["reserve_average"]), "$".(number_format($value["reserve_average"] + $value["miscfinance_average"] + $value["gap_average"] + $value["warranty_average"], 0, '.', ',')));
}

$twig->addGlobal('total_sales_data', $total_sale);
$twig->addGlobal('total_backend_data', $total_backend);
$twig->addGlobal('average_backend_data', $average_backend);
$twig->addGlobal('total_gross_data', $total_gross);
$twig->addGlobal('average_gross_data', $average_gross);

echo $twig->render('reports/tpl.desk-manager.twig');