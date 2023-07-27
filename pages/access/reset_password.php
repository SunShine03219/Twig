<?php
/*
 * Autoload Queue
 */

spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

if(isset($_POST['password']))
{
    if(empty($_POST['password'])){
       $twig->addGlobal('error', 'password is required.');
	   echo $twig->render('tpl.reset-password.twig');
	} else if($_POST['password'] != $_POST['confirm_password']) {
		$twig->addGlobal('error', 'Confirm password is matched with password.');
		echo $twig->render('tpl.reset-password.twig');
    } else {
        $controller = new reset_password();
        $output = $controller->reset_pwd();
		if($output) {
			echo $twig->render('tpl.login.twig');
		} else {
			$twig->addGlobal('error', $controller->error);
			echo $twig->render('tpl.reset-password.twig');
		}
    }
} else {
	$twig->addGlobal('error', 'Password is required.');
	echo $twig->render('tpl.reset-password.twig');
}


?>