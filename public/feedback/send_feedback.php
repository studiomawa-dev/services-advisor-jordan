<?php
	$GLOBALS['feedback_me_db'] = TRUE;
	$GLOBALS['feedback_me_website_name'] = 'Services Advisor';

	$GLOBALS['db_host'] = 'localhost';
	$GLOBALS['db_user'] = 'root';
	$GLOBALS['db_pass'] = 'j3moc83otlwafrl0LprazacL78kog0ziw0triwoj6thuchedrldroz1thljezaze';
	$GLOBALS['db_dtbs'] = 'feedback';

	ini_set ('output_buffering', 'Off');
	while (@ob_end_flush());

	date_default_timezone_set ('UTC');

	ini_set ('php.internal_encoding', 'UTF-8');
	mb_internal_encoding ('UTF-8');


	function Result ($sStatus)
	{
		switch ($sStatus)
		{
			case 'success':
				http_response_code (200); /*** 200 = OK ***/
				break;
			case 'error':
			default:
				http_response_code (404); /*** 404 = Not Found ***/
				break;
		}
	}

	function FeedbackDB ($arFeedback)
	{
		$GLOBALS['link'] = mysqli_init();
		
		if ($GLOBALS['link'] == FALSE)
			return (FALSE);
		
		if (!@mysqli_real_connect($GLOBALS['link'], $GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'], $GLOBALS['db_dtbs']))
			return (FALSE);

		mysqli_set_charset ($GLOBALS['link'], 'utf8');

		$query_feedback = "INSERT INTO feedback VALUES (
			NULL, 
			'".mysqli_real_escape_string ($GLOBALS['link'], $arFeedback['lang'])."', 
			'".(int)mysqli_real_escape_string ($GLOBALS['link'], $arFeedback['radio_list_value'])."', 
			'".(int)mysqli_real_escape_string ($GLOBALS['link'], $arFeedback['radio_list_value_2'])."', 
			'".(int)mysqli_real_escape_string ($GLOBALS['link'], $arFeedback['radio_list_value_3'])."', 
			'".$arFeedback['ip']."', 
			'".$arFeedback['datetime']."');";

		$result_feedback = mysqli_query ($GLOBALS['link'], $query_feedback);

		if (mysqli_affected_rows ($GLOBALS['link']) == 1)
		{
			$bResult = TRUE;
		} else {
			$bResult = FALSE;
		}
		mysqli_close ($GLOBALS['link']);

		return ($bResult);
	}

	function GetIP ()
	{
		$arServer = array (
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR'
		);
		foreach ($arServer as $sServer)
		{
			if (array_key_exists ($sServer, $_SERVER) === TRUE)
			{
				foreach (explode (',', $_SERVER[$sServer]) as $sIP)
				{
					if (filter_var ($sIP, FILTER_VALIDATE_IP) !== FALSE)
						{ return ($sIP); }
				}
			}
		}
		return ('unknown');
	}

	if (strtoupper ($_SERVER['REQUEST_METHOD']) === 'POST')
	{
		if ((isset ($_POST['radio_list_value'])) && (isset ($_POST['radio_list_value_2'])) &&	(!empty ($_POST['radio_list_value_3'])))
		{
			$arFeedback = array();
			$arFeedback['lang'] = htmlspecialchars ($_POST['lang'], ENT_QUOTES);
			$arFeedback['radio_list_value'] = htmlspecialchars ($_POST['radio_list_value'], ENT_QUOTES);
			$arFeedback['radio_list_value_2'] = htmlspecialchars ($_POST['radio_list_value_2'], ENT_QUOTES);
			$arFeedback['radio_list_value_3'] = htmlspecialchars ($_POST['radio_list_value_3'], ENT_QUOTES);
			$arFeedback['website'] = $GLOBALS['feedback_me_website_name'];
			$arFeedback['ip'] = GetIP();
			$arFeedback['datetime'] = date ('Y-m-d H:i:s');

			$bResultDB = TRUE;
			if ($GLOBALS['feedback_me_db'] == TRUE)
				$bResultDB = FeedbackDB($arFeedback);

			$bResultMail = TRUE;
			if ($GLOBALS['feedback_me_mail'] == TRUE)
				{ $bResultMail = FeedbackMail ($arFeedback); }

			if (($bResultDB != FALSE) && ($bResultMail != FALSE))
				Result ('success');
			else
				Result ('error');
		} 
		else 
		{ 
			Result ('error'); 
		}
	} 
	else 
	{ 
		Result ('error'); 
	}
?>
