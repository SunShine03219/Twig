<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$twig->addGlobal('page_title', 'Unapproved Deals');
$twig->addGlobal('session', $_SESSION['FT']);

$_GET['approved'] = 0;

$controller = new dealcontroller();
$output = $controller->invoke('approved');

if(isset($_GET['type'])){
    if($_GET['type'] == "pagination"){
        echo json_encode($output);
        exit();
    }
}

if (count($output) > 0) {
    $dealCount = count($output);
    $totalFinanced = 0;
    $totalDiscount = 0;
    $averageFinanced = 0;
    $averageDiscount = 0;
    
    foreach ($output as $deal) {
        // Financed Amount Total
        $finVal = str_replace(',', '', $deal['amount_financed']);
        $finVal = str_replace('$', '', $finVal);
        $totalFinanced += $finVal;
        
        // Discount Amount Total
        $disVal = str_replace(',', '', $deal['discount']);
        $disVal = str_replace('$', '', $disVal);
        $totalDiscount += $disVal;
    }
    $averageFinanced = $totalFinanced / $dealCount;
    $averageDiscount = $totalDiscount / $dealCount;
    
    $twig->addGlobal('deals', $output);
    $twig->addGlobal('deal_count', $dealCount);
    $twig->addGlobal('deal_financed_total', $totalFinanced);
    $twig->addGlobal('deal_discount_total', $totalDiscount);
    $twig->addGlobal('deal_financed_average', $averageFinanced);
    $twig->addGlobal('deal_discount_average', $averageDiscount);
}

//var_dump($output);
//var_dump($_SESSION['FT']);

/*
 * Render Templates
 */
 
$_SESSION["current_page"] = "unapprobed";
$_SESSION['FT']['current_page'] = 'unapproved';

echo $twig->render('deal-management/tpl.unapproved.twig');