<?php
/*
 * Check User Status
 */
function bounce() {
    // Determine if user is logged in
	if (!$_SESSION['FT']['ds_is_logged_in']) {
		$_SESSION['FT']['redir'] = $_SERVER['REQUEST_URI'];
		set_postback_msg("Please log in to continue.");
		header("location: /login");
		exit();
	}
	
	// Verify that user and company are still active
	if (!(user::user_id_is_active($_SESSION['FT']['user_id']) && company::company_id_is_active($_SESSION['FT']['company_id']))){
		header('location: /logout');
		exit();
	}
}

/*
 * Check Admin User Status
 */
function bounce_admin() {
	if (!$_SESSION['FT']['SEC_ADMIN']) {
		header("location: /error/admin");
		exit();
	}
}

/*
 * Check Support User Status
 */
function bounce_support() {
	if (!$_SESSION['FT']['SEC_SUPPORT']) {
		header("location: /error/support");
		exit();
	}
}