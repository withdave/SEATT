<?php
function seatt_form($event_id) {
	global $wpdb;
	global $current_user;
	get_currentuserinfo();
	
	// Get event id if set
	if (isset($_POST['seatt_event_id'])) {
		$form_event_id = (int)$_POST['seatt_event_id'];
	} else {
		$form_event_id = '';
	}
	
	// Cast as integer to strip out unwanted
	$event_id = (int)$event_id;

	// Continue if field isn't NULL
	if ($event_id != '') {
	
		// If submitted, remove registration
		if ((isset($_POST['seatt_unregister'])) && ($form_event_id == $event_id)) {
			$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."seatt_attendees WHERE user_id = %d AND event_id = %d", $current_user->ID,  $event_id));
		
		}
		
		// If submitted, add registration
		if ((isset($_POST['seatt_register'])) && ($form_event_id == $event_id)) {
			
			$registered = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM ".$wpdb->prefix."seatt_attendees WHERE event_id = %d AND user_id = %d", $event_id, $current_user->ID));
			
			if ($registered == "") {
				$user_comment = stripslashes_deep($_POST['seatt_comment']);
				//$user_comment = $wpdb->escape($user_comment);
				
				$wpdb->insert($wpdb->prefix.'seatt_attendees', array( 'event_id' => $event_id, 'user_id' => $current_user->ID, 'user_comment' => $user_comment ), array('%d', '%d', '%s'));
			}
		 
		}
		
		// Get event details
		$event = $wpdb->get_results($wpdb->prepare("SELECT id, event_name, event_desc, event_limit, event_start, event_expire, event_status FROM ".$wpdb->prefix."seatt_events WHERE id = %d LIMIT 1", $event_id));
		
		// Make sure that results were returned
		if (count($event) > 0) {
			
			if ($event[0]->id != "") {
				$attendees = $wpdb->get_var($wpdb->prepare("SELECT COUNT(user_id) FROM ".$wpdb->prefix."seatt_attendees WHERE event_id = %d", $event_id));
				
				if ($attendees == '') {
					$attendees = 0;
				}
				if (($event[0]->event_limit == 0) OR ($event[0]->event_limit == "")) { 
					$event[0]->event_limit = 100000; 
				}
				
				$seatt_output = '<div style="background:#E8E8E8;padding:10px;"><h2>' . $event[0]->event_name . '</h2>
				<p><strong>Description:</strong> ' . $event[0]->event_desc . '<br />
				Registration opens at ' . date("d-m-Y H:i", $event[0]->event_start) . '<br />
				Registration closes at ' . date("d-m-Y H:i", $event[0]->event_expire) . '</p>';
				
				if ($event[0]->event_limit != 100000) {
					$seatt_output .= '<p><br>
					<strong>Max Participants:</strong> ' . $event[0]->event_limit . '</p>';
			}
			
			$seatt_output .= '<p><strong>Registered Users</strong></p>
			<p><ol style="margin-left:1em;">';
			
			$users = $wpdb->get_results($wpdb->prepare("SELECT id, user_id FROM ".$wpdb->prefix."seatt_attendees WHERE event_id = %d ORDER BY id ASC", $event_id));
			
			$num = 1;
			foreach ($users as $user) {
				$user_info = get_userdata($user->user_id);
				$seatt_output .= '<li>' . $user_info->user_login . '</li>';
				$num++;
			}
			
			if ($num == 1) {
				$seatt_output .= 'None found.';
			}
			
			$seatt_output .= '</ol></p>';
			
			// Check if user has already registered
			$attending = $wpdb->get_results($wpdb->prepare("SELECT user_id, user_comment FROM ".$wpdb->prefix."seatt_attendees WHERE event_id = %d AND user_id = %d", $event_id, $current_user->ID));
			
			$current_time = current_time('timestamp');
			if (($event[0]->event_status != 1) OR ($current_time >= $event[0]->event_expire) OR ($current_time < $event[0]->event_start)) {
				$seatt_output .= '
				<p><strong>Registration is currently closed.</strong></p>';
			}
			// Check if user logged in
			elseif (!is_user_logged_in()) {
				$seatt_output .= '
			<p><strong>Please <a href="' . site_url('wp-login.php') . '">login</a> (or <a href="' . site_url('wp-login.php?action=register') . '">create an account</a>) to sign up for this event. </strong></p>'; 
				} elseif ($attending) {
					// if they have already signed up
					$seatt_output .= '
					<p><strong>You\'re currently registered. Unregister?</strong></p>
			<form name="seatt_unregister" method="post" action="http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '">
					<input name="seatt_event_id" type="hidden" id="seatt_event_id" value="' . $event_id . '" size="40">
			  <p>Username:
					<input name="seatt_username" type="text" disabled id="seatt_username" value="' . $current_user->user_login . '" size="40" readonly="readonly">
				  <br />Comment: 
					<input name="seatt_comment" type="text" disabled id="seatt_comment" value="' . htmlspecialchars($attending[0]->user_comment) . '" size="40" maxlength="40" readonly="readonly">
			  </p>
				  <p>
					<input type="submit" name="seatt_unregister" id="seatt_unregister" value="Unregister">
				  </p>
				</form>';
			
				} elseif ($attendees >= $event[0]->event_limit) { 
				$seatt_output .= '<p><strong>Unfortunately all places are already reserved. </strong></p>';
				} else {
				$seatt_output .= '
				<strong>Register for this event:</strong>
					</p>
			
				<form name="seatt_register" method="post" action="http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '">
					<input name="seatt_event_id" type="hidden" id="seatt_event_id" value="' . $event_id . '" size="40">
				  <p>Username: 
					<label for="seatt_username"></label>
					<input name="seatt_username" type="text" disabled id="seatt_username" value="' . $current_user->user_login . '" size="40" readonly="readonly">
			<br />Comment: 
					<input name="seatt_comment" type="text" id="seatt_comment" size="40" maxlength="40">
				  </p>
				  <p>
					<input type="submit" name="seatt_register" id="seatt_register" value="Register">
				  </p>
				</form>';
				
				}
			} //End if no results
			$seatt_output .= '</div>';
			return $seatt_output;
		}
	}
}

?>