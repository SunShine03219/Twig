<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$twig->addGlobal('page_title', 'Oops');
$twig->addGlobal('session', $_SESSION['FT']);

/*
 * Render Templates
 */
if ($_GET['q'] == 'error/admin') {
    echo $twig->render('errors/tpl.admin.twig');
} elseif ($_GET['q'] == 'error/support') {
    echo $twig->render('errors/tpl.support.twig');
} else {
    echo $twig->render('errors/tpl.unknown.twig');
}