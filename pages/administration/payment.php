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

$twig->addGlobal('page_title', 'Payment Settings');
$twig->addGlobal('session', $_SESSION['FT']);

include_once '_includes/classes/companysubscription_class.php';
include_once '_includes/classes/company_class.php';
include_once '_includes/classes/subscription_class.php';
include_once '_includes/classes/company_class.php';

$comp = new company();
$comp->make_me_into($_SESSION['FT']['company_id']);

if(isset($_POST['update'])) {
	$comp->set_subscription($_POST['update']);
	$comp->update_one();
	
}else if(isset($_POST['action'])){
	if($_POST['action'] == "subscription"){
		
		 \Stripe\Stripe::setApiKey(STRIPE_SECRET); 

		try {
			
			$customer = \Stripe\Customer::update(
				$_SESSION['FT']['stripe_customer_id'],
				array( 
					'source'  => $_POST['stripeToken']
				)
			);
			
			$company = new company();
			$company->update_card_info($_SESSION['FT']['company_id'], $_POST['cardtype'], $_POST['last4']);
			echo "success";
			// Use Stripe's library to make requests...
		  } catch(\Stripe\Exception\CardException $e) {
			// Since it's a decline, \Stripe\Exception\CardException will be caught
			echo $e->getError()->message;
		  } catch (\Stripe\Exception\RateLimitException $e) {
			  
			echo "1 error";
			// Too many requests made to the API too quickly
		  } catch (\Stripe\Exception\InvalidRequestException $e) {
			
		  	echo "2 error";
			// Invalid parameters were supplied to Stripe's API
		  } catch (\Stripe\Exception\AuthenticationException $e) {
			
		  	echo "3 error";
			// Authentication with Stripe's API failed
			// (maybe you changed API keys recently)
		  } catch (\Stripe\Exception\ApiConnectionException $e) {
			
		  	echo "4 error";
			// Network communication with Stripe failed
		  } catch (\Stripe\Exception\ApiErrorException $e) {
			
		  	echo "5 error";
			// Display a very generic error to the user, and maybe send
			// yourself an email
		  } catch (Exception $e) {
			// Something else happened, completely unrelated to Stripe
			
		  	echo "6 error";
		  }
		exit;
	}
}
$subs = new subscription();

$company_subs = new company_subscription();
$billing = $company_subs->get_subscritions_by_company($_SESSION['FT']['company_id']);

$start = date('Y-m-d');
if(isset($billing[0]['billing_start_date'])) {
	$subs->make_me_into($billing[0]['defined_subscription_id']);
	$s = (date_add(date_create($billing[0]['billing_start_date']), date_interval_create_from_date_string($subs->get_trial_days().' days')))->format('Y-m-d');
	$s = (date_add(date_create($s), date_interval_create_from_date_string($subs->get_payment_interval_length().' '.$subs->get_payment_interval_unit())))->format('Y-m-d');
	if(strtotime($s) < time())
	{
		$s = date('Y-m-d');
	}
	$twig->addGlobal('start', $s);
	if($comp->get_subscription()) {
		$subs->make_me_into($comp->get_subscription());
	}
	$twig->addGlobal('selected_subs', $subs->to_array());
}


$twig->addGlobal('company', $comp->to_array());
$twig->addGlobal('billings', $billing);
$twig->addGlobal('subscription', $subs->get_active_subscritions());
$twig->addGlobal('stripe_key', STRIPE_KEY);
echo $twig->render('administration/tpl.payment.twig');
