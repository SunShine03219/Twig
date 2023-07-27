<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
    spl_autoload_register(function ($class) {
        include '_includes/classes/' . $class . '_class.php';
    });

    bounce();
    bounce_admin();
    $twig->addGlobal('session', $_SESSION['FT']);

    $sql = "SELECT * FROM deal_port_history AS d ";
    $deals = db_select_assoc_array($sql);
    // print_r($deals) ; exit;

    $twig->addGlobal('deal_result',$deals);
    
        /*
        * Render Templates
        */
    echo $twig->render('administration/tpl.portdata.twig');

