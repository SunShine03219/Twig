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

/*
 * register attempt
 */ 
if(isset($_GET['captcha']) && $_GET['captcha'] == '1') {
	if(isset($_SESSION['captcha'])) {
		unset($_SESSION['captcha']);
	}
	$captcha_code = '';
	$captcha_image_height = 30;
	$captcha_image_width = 130;
	$total_characters_on_image = 6;

	function hextorgb ($hexstring){
		$integar = hexdec($hexstring);
		return array("red" => 0xFF & ($integar >> 0x10),
               "green" => 0xFF & ($integar >> 0x8),
               "blue" => 0xFF & $integar);
	}
	//The characters that can be used in the CAPTCHA code.
	//avoid all confusing characters and numbers (For example: l, 1 and i)
	$possible_captcha_letters = 'bcdfghjkmnpqrstvwxyz23456789';
	$captcha_font = getcwd().'/arial.ttf';

	$random_captcha_dots = 70;
	$random_captcha_lines = 10;
	$captcha_text_color = "0x142864";
	$captcha_noise_color = "0x142864";


	$count = 0;
	while ($count < $total_characters_on_image) { 
		$captcha_code .= substr(
			$possible_captcha_letters,
			mt_rand(0, strlen($possible_captcha_letters)-1),
			1);
		$count++;
	}

	$captcha_font_size = $captcha_image_height * 0.65;
	$captcha_image = @imagecreate(
		$captcha_image_width,
		$captcha_image_height
	);

	/* setting the background, text and noise colours here */
	$background_color = imagecolorallocate(
		$captcha_image,
		255,
		255,
		255
	);

	$array_text_color = hextorgb($captcha_text_color);
	$captcha_text_color = imagecolorallocate(
		$captcha_image,
		$array_text_color['red'],
		$array_text_color['green'],
		$array_text_color['blue']
	);

	$array_noise_color = hextorgb($captcha_noise_color);
	$image_noise_color = imagecolorallocate(
		$captcha_image,
		$array_noise_color['red'],
		$array_noise_color['green'],
		$array_noise_color['blue']
	);

	/* Generate random dots in background of the captcha image */
	for( $count=0; $count<$random_captcha_dots; $count++ ) {
		imagefilledellipse(
			$captcha_image,
			mt_rand(0,$captcha_image_width),
			mt_rand(0,$captcha_image_height),
			2,
			3,
			$image_noise_color
		);
	}

	/* Generate random lines in background of the captcha image */
	for( $count=0; $count<$random_captcha_lines; $count++ ) {
		imageline(
			$captcha_image,
			mt_rand(0,$captcha_image_width),
			mt_rand(0,$captcha_image_height),
			mt_rand(0,$captcha_image_width),
			mt_rand(0,$captcha_image_height),
			$image_noise_color
			);
	}

	/* Create a text box and add 6 captcha letters code in it */
	$text_box = imagettfbbox(
		$captcha_font_size,
		0,
		$captcha_font,
		$captcha_code
	); 
	$x = ($captcha_image_width - $text_box[4])/2;
	$y = ($captcha_image_height - $text_box[5])/2;
	imagettftext(
		$captcha_image,
		$captcha_font_size,
		0,
		$x,
		$y,
		$captcha_text_color,
		$captcha_font,
		$captcha_code
	);

	/* Show captcha image in the html page */
	// defining the image type to be shown in browser widow
	header('Content-Type: image/png'); 
	imagepng($captcha_image); //showing the image
	imagedestroy($captcha_image); //destroying the image instance
	$_SESSION['captcha'] = $captcha_code;

} else {

	/* create user for company */
	include '_includes/classes/user_class.php';
	$user = new user();
	
	
	if(isset($_SESSION['open_reg'])) {
		if(isset($_POST['action'])) {		
			if($_SESSION['open_reg'] == 'company' && $_POST['action'] == 'company') {
				$_SESSION['post_data'] = $_POST;
				$_SESSION['open_reg'] = 'captcha';
				
			} else if($_SESSION['open_reg'] == 'captcha' && $_POST['action'] == 'captcha') {
				if($_POST['captcha'] == $_SESSION['captcha']) {
					
					/* create company after confirm captcha code */
					include '_includes/classes/company_class.php';
					$company = new company();
					
					if(!$company->validate_new($_SESSION['post_data'])) {
						$twig->addGlobal('error', 'Unable to validate customer data.');
						$_SESSION['open_reg'] = 'company';
						unset($_SESSION['post_data']);
					} else {
						if(!$company->insert_new()){
							$twig->addGlobal('error', 'Unable to create customer.');
							unset($_SESSION['open_reg']);
						} else {
							$_SESSION['company_id'] = $company->get_id();
							$_SESSION['open_reg'] = 'user';
							unset($_SESSION['post_data']);
						}
					}
				} else {
					header('Location: /register');
				}
			} else if($_SESSION['open_reg'] == 'user' && $_POST['action'] == 'user') {
				$_POST['company_id'] = $_SESSION['company_id'];


				$_POST['sec_admin'] = 1;
				if (!$user->validate_new($_POST)){
					$twig->addGlobal('error', 'Unable to validate form data.');
					unset($_SESSION['open_reg']);
				} else {
					if (!$user->insert_new()) {
						$twig->addGlobal('error', 'Unable to create new user.');
						unset($_SESSION['open_reg']);
					} else {
						$_SESSION['user_email'] = $user->get_email();
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

							//Recipients
							$mail->setFrom('administrator@fundingtracker.net', 'FundingTracker');
							$mail->addAddress($_SESSION['user_email'], '');     //Add a recipient
							
							//Content
							$mail->isHTML(true);                                  //Set email format to HTML
							$mail->Subject = 'Welcome';
							$mail->Body    = 'Welcome to the Funding Tracker.';
				
							$mail->send();
						} catch (Exception $e) {
						}	
						$_SESSION['user_id'] = $user->get_id();
						$_SESSION['open_reg'] = 'subscription';
					}
				}
			} else if($_SESSION['open_reg'] == 'subscription' && $_POST['action'] == 'subscription') {
				
				\Stripe\Stripe::setApiKey(STRIPE_SECRET); 
				
				/* update subscription of company */
				if(!isset($_SESSION['stripe'])) {
					$_SESSION['stripe'] = array();
				}
				// Add customer to stripe 
				try {
					if(isset($_SESSION['stripe'][$_SESSION['user_email']]['customer'])) {
						$customer = \Stripe\Customer::retrieve($_SESSION['stripe'][$_SESSION['user_email']]['customer'], []);
					} else {
						$customer = \Stripe\Customer::create(array( 
							'email' => $_SESSION['user_email'], 
							'source'  => $_POST['stripeToken'] 
						));
						$_SESSION['stripe'][$_SESSION['user_email']]['customer'] = $customer->id;
					}
					$user->set_customer_key($_SESSION['stripe'][$_SESSION['user_email']]['customer']);
					$user->update_customer_key($_SESSION['user_id'],$_SESSION['stripe'][$_SESSION['user_email']]['customer']);
				}catch(Exception $e) {  
					$api_error = $e->getMessage();  
				} 
				 
				if(empty($api_error) && $customer){  
					$sub = new subscription();
					$sub->make_me_into($_POST['subscription']);
					$subs = $sub->to_array();
					// Convert price to cents 
					$priceCents = round($subs['payment_amount']*100); 
					
					// Create a plan 
					try { 
						if(isset($_SESSION['stripe'][$_SESSION['user_email']]['plan'])) {
							$plan = \Stripe\Plan::retrieve($_SESSION['stripe'][$_SESSION['user_email']]['plan'], []); 
						} else {	
							$plan = \Stripe\Plan::create(array( 
								"product" => [ 
									"name" => $subs['name'] 
								], 
								"amount" => $priceCents, 
								"currency" => 'usd', 
								"trial_period_days" => $subs['trial_days'], 
								"interval" => $subs['payment_interval_unit'], 
								"interval_count" => $subs['payment_interval_length']
							)); 
							$_SESSION['stripe'][$_SESSION['user_email']]['plan'] = $plan->id;
						}
					}catch(Exception $e) { 
						$api_error = $e->getMessage(); 
					} 
					 
					if(empty($api_error) && $plan){ 
						// Creates a new subscription 
						try { 
							if(isset($_SESSION['stripe'][$_SESSION['user_email']]['subscription'])) {
								$subscription = \Stripe\Subscription::retrieve($_SESSION['stripe'][$_SESSION['user_email']]['subscription'],[]); 								
							} else {								
								$subscription = \Stripe\Subscription::create(array( 
									"customer" => $customer->id, 
									"items" => array( 
										array( 
											"plan" => $plan->id, 
										), 
									), 
								)); 
								$_SESSION['stripe'][$_SESSION['user_email']]['subscription'] = $subscription->id;
							}
						}catch(Exception $e) { 
							$api_error = $e->getMessage(); 
						} 
						 
						if(empty($api_error) && $subscription){
							include '_includes/classes/company_class.php';
							$company = new company();
							$company->make_me_into($_SESSION['company_id']);
							$company->set_card_digit($_POST['last4']);
							$company->set_card_type($_POST['cardtype']);
							$company->set_subscription($_POST['subscription']);
							
							$company->update_one();
							
							$data = array();
							$data['company_id'] = $_SESSION['company_id'];
							$data['defined_subscription_id'] = $_POST['subscription'];
							$data['subscription_name'] = $subs['name'];
							$data['subscription_interval_length'] = $subs['payment_interval_length'];
							$data['subscription_interval_unit'] = $subs['payment_interval_unit'];
							$data['subscription_payment_amount'] = $subs['payment_amount'];
							$data['subscription_occurances'] = $subs['total_occurances'];
							$data['subscription_description'] = $subs['description'];
							$data['ctype'] = $_POST['cardtype']; 
							$data['ccexp'] = $_POST['exp_date']; 
							$data['cclast'] = substr($_POST['last4'], -2); 
							// Retrieve subscription data 
							$subsData = $subscription->jsonSerialize(); 
							// Check whether the subscription activation is successful 
							if($subsData['status'] == 'active'){ 
								// Subscription info 
								$data['status'] = $subsData['status'];
								$data['payment_processor_subscription_id'] = $subsData['id']; 
								$data['payment_processor_name'] = $subsData['customer']; 
								$data['billing_start_date'] = date("Y-m-d H:i:s", $subsData['current_period_start']); 
								include '_includes/classes/companysubscription_class.php';
								$company_subscription = new company_subscription();
								if($company_subscription->validate($data)) {
									if($company_subscription->insert_new()) {										
										unset($_SESSION['open_reg']);
										unset($_SESSION['company_id']);
										header('Location: /login');
									}
									
								}
							}else{ 
								$statusMsg = "Subscription activation failed!"; 
							} 
						}else{ 
							$statusMsg = "Subscription creation failed! ".$api_error; 
						} 
					}else{ 
						$statusMsg = "Plan creation failed! ".$api_error; 
					} 
				}else{  
					$statusMsg = "Invalid card details! $api_error";  
				} 
				if(isset($statusMsg))$twig->addGlobal('error', $statusMsg);
			}
		}
	} else {
		$_SESSION['open_reg'] = 'company';
	}
	if(isset($_SESSION['open_reg'])) {
		if($_SESSION['open_reg'] == 'company') {
			$states = array(
					'AL' => 'Alabama',
					'AK' => 'Alaska',
					'AS' => 'American Samoa',
					'AZ' => 'Arizona',
					'AR' => 'Arkansas',
					'CA' => 'California',
					'CO' => 'Colorado',
					'CT' => 'Connecticut',
					'DE' => 'Delaware',
					'DC' => 'District of Columbia',
					'FL' => 'Florida',
					'GA' => 'Georgia',
					'GU' => 'Guam',
					'HI' => 'Hawaii',
					'ID' => 'Idaho',
					'IL' => 'Illinois',
					'IN' => 'Indiana',
					'IA' => 'Iowa',
					'KS' => 'Kansas',
					'KY' => 'Kentucky',
					'LA' => 'Louisiana',
					'ME' => 'Maine',
					'MD' => 'Maryland',
					'MA' => 'Massachusetts',
					'MI' => 'Michigan',
					'MN' => 'Minnesota',
					'MS' => 'Mississippi',
					'MO' => 'Missouri',
					'MT' => 'Montana',
					'NE' => 'Nebraska',
					'NV' => 'Nevada',
					'NH' => 'New Hampshire',
					'NJ' => 'New Jersey',
					'NM' => 'New Mexico',
					'NY' => 'New York',
					'NC' => 'North Carolina',
					'ND' => 'North Dakota',
					'MP' => 'Northern Mariana Islands',
					'OH' => 'Ohio',
					'OK' => 'Oklahoma',
					'OR' => 'Oregon',
					'PA' => 'Pennsylvania',
					'PR' => 'Puerto Rico',
					'RI' => 'Rhode Island',
					'SC' => 'South Carolina',
					'SD' => 'South Dakota',
					'TN' => 'Tennessee',
					'TX' => 'Texas',
					'VI' => 'U.S. Virgin Islands',
					'UT' => 'Utah',
					'VT' => 'Vermont',
					'VA' => 'Virginia',
					'WA' => 'Washington',
					'WV' => 'West Virginia',
					'WI' => 'Wisconsin',
					'WY' => 'Wyoming'
				);
			$twig->addGlobal('page_title', 'New Company');
			
			$twig->addGlobal('states', $states);
		} else if($_SESSION['open_reg'] == 'subscription') {
			include_once '_includes/classes/subscription_class.php';
			$_SERVER['REQUEST_METHOD'] = "GET";
			$controller = new subscription();
			$output = $controller->invoke();
			$twig->addGlobal('subscriptions', $output);
		}

		$twig->addGlobal('action', $_SESSION['open_reg']);
	}
	/*
	 * Render Templates
	 */
	echo $twig->render('tpl.register.twig');
}
