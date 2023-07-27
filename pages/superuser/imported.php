<?php
    //error_reporting(E_ERROR | E_PARSE);
    error_reporting(E_ALL);

    /*
     * Autoload Queue
     */
    spl_autoload_register(function ($class) {
        include '_includes/classes/' . $class . '_class.php';
    });

    bounce();
    bounce_support();

        $twig->addGlobal('page_title', 'Import Deals History');
        $twig->addGlobal('session', $_SESSION['FT']);


        $sql = "SELECT * FROM deal_import_history AS d order by date DESC, id DESC";
        $deals = db_select_assoc_array($sql);
        $twig->addGlobal('deal_result',$deals);

        /*
         * Render Templates
         */
        echo $twig->render('superuser/tpl.imported.twig');
    