<?php
/*
 * Autoload Queue
 */

spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

/*
 * Login attempt
 */
if (isset($_POST['username']) || isset($_POST['password'])) {
    if (empty($_POST['username'])) {

        /* throw missing username error */
        $twig->addGlobal('error', 'Username is required.');
    } elseif (empty($_POST['password'])) {
        
        /* throw missing password error */
        $twig->addGlobal('username', $_POST['username']);
        $twig->addGlobal('error', 'Password is required.');
    } else {
        /* attempt login */
        $controller = new logincontroller();
        $output = $controller->invoke();
        if ($output) {
            $twig->addGlobal('username', $_POST['username']);
            $twig->addGlobal('error', $output);
        }
    }
}

$nonce = new nonce();
$nonce->add('type', 'login');
$nonce->save();
$nonceArray = $nonce->get_nonce_list();
$twig->addGlobal('nonce', $nonceArray['nonce']);


/*
 * Render Templates
 */
echo $twig->render('tpl.login.twig');