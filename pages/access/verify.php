<?php
/*
 * Autoload Queue
 */

spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

if(isset($_POST['verify_code']))
{
    if(empty($_POST['verify_code'])){
       $twig->addGlobal('error', 'Verify Code is required.');
    } else {
        $controller = new verify();
        $output = $controller->confirm();
		if($output !== false) {
			$twig->addGlobal('action', 'reset');
			$twig->addGlobal('id', $output);
			echo $twig->render('tpl.reset-password.twig');
		} else {
			$twig->addGlobal('error', $controller->error);
			echo $twig->render('tpl.verify-code.twig');
		}
    }
} else {
	$twig->addGlobal('error', 'Verify Code is required.');
	echo $twig->render('tpl.verify-code.twig');
}


?>