<?php
/*
 * Autoload Queue
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});
if(isset($_POST['username']))
{
    if(empty($_POST['username'])){
       /* throw missing username error */
       $twig->addGlobal('error', 'Username is required.');
    } else {
        try {
			$mail = new PHPMailer(true);
			$smtp = new SMTP;
			//Server settings
			// $mail->SMTPDebug = $smtp::DEBUG_SERVER;                      
			// $mail->isSMTP();
			$mail->Mailer = 'SMTP';
			$mail->Host       = 'ssl://shared40.accountservergroup.com';      
			$mail->SMTPAuth   = true;                                   
			$mail->Username   = 'administrator@fundingtracker.net';
			$mail->Password   = 'QWER!@#$qwer1234';
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
			$mail->Port       = 465;                                    

			$code = strrev(rand(100000, 999999));
			$_SESSION['reset_pwd'] = array(
				'code'=>$code,
				'time'=>time(),
				'email'=>$_POST['username']	
			);
			
			//Recipients
			$mail->setFrom('administrator@fundingtracker.net', 'FundingTracker');
			$mail->addAddress($_POST['username'], '');     //Add a recipient
			
			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = 'Password Recover Verification Code';
			$mail->Body    = $code;

			$mail->send();
			echo $twig->render('tpl.verify-code.twig');
		} catch (Exception $e) {
			$twig->addGlobal('error', "Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
			echo $twig->render('tpl.forgot.twig');
		}
    }
} else {
	echo $twig->render('tpl.forgot.twig');
}


?>