<?php
function pretty_localtime(){
	$mylocaltime = time() - $_SESSION['FT']['time'];
	return pretty_time($mylocaltime); 
}

function pretty_time($time){
	return gmdate('m/d/Y g:i:s a', $time);
}

function pretty_date($date){
	if(!empty($date)){
		if($time = strtotime($date)){
			return date('m/d/Y', $time);
		}
	}
	return '';
}

function gmt_to_local_pretty_time($time){
	$time = $time - $_SESSION['FT']['time'];
	return pretty_time($time);
}

function js_timezone_set(){
	return '
    $(document).ready(function() {
        var visitortime = new Date();
        var visitortimezone = visitortime.getTimezoneOffset()*60;
        $.ajax({
            type: "GET",
            url: "timezone_set.php",
            data: \'time=\'+ visitortimezone,
            success: function(){}
        });
    });';
}

function db_timestamp(){
	return date('Y-m-d H:i:s', time());
}
