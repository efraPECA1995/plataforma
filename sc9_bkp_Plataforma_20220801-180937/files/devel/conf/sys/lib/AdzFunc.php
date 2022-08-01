<?php
//__NM__General Functions__NM__FUNCTION__NM__//
	// Function to query the current time and compare it against when the login took place
	// If the time is greater than the passed idle time (minutes) then log out, forcing a
	// re-login - if not exceeded "reset" current time to now....
	function check_idle_time($idle_interval) {
		$time_diff = (time() - [last_active_time]) / 60;	// Idle time in minutes
		
		if ($time_diff > $idle_interval) {
			// Idle time exceeded limit, then logout
			sc_redir(sec_Login, logout_s = 1, "_parent");
		
		} else {
			// Still within idle time limit - reset start time
			[last_active_time] = time();			
		}

	}
		
	// Function to write debug messages to a file.
	function debug_msg($var_message) {
		if ([global_debug] == true) {
			if (is_array($var_message)) {
				file_put_contents('~/DebugSC.txt', print_r($var_message, true), FILE_APPEND);
			} else {
				file_put_contents('~/DebugSC.txt', $var_message.PHP_EOL, FILE_APPEND);
			}
		} 
	}

	// Function display object contents e.g. arrays
	function display_var($var_message) {
		echo '<pre>'; 							// This is for correct handling of newlines
		ob_start();
		var_dump($var_message);
		$a=ob_get_contents();
		ob_end_clean();
		echo htmlspecialchars($a,ENT_QUOTES); 	// Escape every HTML special chars (especially > and < )
		echo '</pre>'; 
	}

	// Function display Javascript pop-up message
	function display_js($var_message) {
		echo "<script type='text/javascript'>alert('" . $var_message . "');</script>";
	}

	// Get email settings based on friendly name (returns array)
	function get_smtp_details($smtp_name) {
		$ssql = "SELECT ".
					"server_email, ".
					"user_email, ".
					"pass_email, ".
					"from_email, ".
					"port_email, ".
					"ssl_email, ".
					"format_email ".
				"FROM ".
					"tbl_smtp ".
				"WHERE ".
					"friendly_name = '" . $smtp_name . "'";
		
		sc_lookup(rs, $ssql);
		
		if ({rs} === false) {
			echo "Access error. Message=". {rs_erro} ;
		
		} elseif (count({rs}) == 0) {
			sc_error_message("No SMTP for '" . $smtp_name . "' found.");
			sc_error_exit();
		
		} else {
			return {rs};
		}
	}				
?>