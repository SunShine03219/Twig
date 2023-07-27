<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

// print_r("exit");exit;

$twig->addGlobal('page_title', 'Company Gross');
$twig->addGlobal('session', $_SESSION['FT']);


$controller = new companygrosscontroller();
$output = $controller->invoke('company-gross');

// print_r($output);exit;

$twig->addGlobal('companygrosses', $output);
if(isset($_GET['sortdir']))
{
    $twig->addGlobal('sortdir', $_GET['sortdir']);
    $twig->addGlobal('sortid', $_GET['sortid']);
}
else{
    $twig->addGlobal('sortdir', "desc");
    $twig->addGlobal('sortid', 'date_sold');
}


//var_dump($output);
//var_dump($_SESSION['FT']);

/*
 * Render Templates
 */
echo $twig->render('reports/tpl.company-gross.twig');